<?php

$mrConfig = require __DIR__ . '/mr_config.php';
date_default_timezone_set($mrConfig['app']['timezone']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function mr_db()
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    $config = require __DIR__ . '/mr_config.php';
    $dbConfig = $config['db'];

    $conn = @mysqli_connect(
        $dbConfig['host'],
        $dbConfig['user'],
        $dbConfig['pass'],
        $dbConfig['name'],
        $dbConfig['port']
    );

    if (!$conn) {
        mr_json_response([
            'ok' => false,
            'error' => 'No se pudo conectar con la base de datos del módulo MR.',
        ], 500);
    }

    mysqli_set_charset($conn, 'utf8mb4');

    return $conn;
}

function mr_json_response($payload, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function mr_get_json_input()
{
    $rawInput = file_get_contents('php://input');
    if (!$rawInput) {
        return [];
    }

    $parsed = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        mr_json_response([
            'ok' => false,
            'error' => 'JSON inválido en el body.',
        ], 400);
    }

    return is_array($parsed) ? $parsed : [];
}

function mr_require_method($method)
{
    if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
        mr_json_response([
            'ok' => false,
            'error' => 'Método HTTP no permitido.',
        ], 405);
    }
}

function mr_request_param($name, $default = null)
{
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function mr_get_restaurante($slug = null, $restauranteId = null)
{
    $conn = mr_db();

    if ($restauranteId) {
        $stmt = mysqli_prepare($conn, 'SELECT id, nombre, slug, telefono, email, direccion, logo, activo FROM restaurantes WHERE id = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'i', $restauranteId);
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, nombre, slug, telefono, email, direccion, logo, activo FROM restaurantes WHERE slug = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $slug);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $restaurant = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $restaurant ?: null;
}

function mr_now()
{
    return date('Y-m-d H:i:s');
}

function mr_geo_distance_meters($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6371000;

    $dLat = deg2rad((float) $lat2 - (float) $lat1);
    $dLng = deg2rad((float) $lng2 - (float) $lng1);

    $a = sin($dLat / 2) * sin($dLat / 2)
        + cos(deg2rad((float) $lat1)) * cos(deg2rad((float) $lat2))
        * sin($dLng / 2) * sin($dLng / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
}

function mr_point_in_polygon($lat, $lng, array $polygonPoints)
{
    $inside = false;
    $pointsCount = count($polygonPoints);

    if ($pointsCount < 3) {
        return false;
    }

    for ($i = 0, $j = $pointsCount - 1; $i < $pointsCount; $j = $i++) {
        $xi = (float) ($polygonPoints[$i]['lng'] ?? 0);
        $yi = (float) ($polygonPoints[$i]['lat'] ?? 0);
        $xj = (float) ($polygonPoints[$j]['lng'] ?? 0);
        $yj = (float) ($polygonPoints[$j]['lat'] ?? 0);

        $intersect = (($yi > $lat) !== ($yj > $lat))
            && ($lng < (($xj - $xi) * ($lat - $yi) / (($yj - $yi) ?: 0.0000001) + $xi));

        if ($intersect) {
            $inside = !$inside;
        }
    }

    return $inside;
}

function mr_get_active_zonas($restauranteId)
{
    $conn = mr_db();

    $sql = 'SELECT id, restaurante_id, nombre, costo_envio, pedido_minimo, activo,
                   COALESCE(tipo_area, "manual") AS tipo_area,
                   centro_lat, centro_lng, radio_metros, poligono_json
            FROM zonas_envio
            WHERE restaurante_id = ? AND activo = 1
            ORDER BY id ASC';

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $restauranteId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $zonas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $zonas[] = $row;
    }

    mysqli_stmt_close($stmt);
    return $zonas;
}

function mr_resolve_delivery_zone($restauranteId, $zonaId = 0, $lat = null, $lng = null)
{
    $zonas = mr_get_active_zonas($restauranteId);

    if ($zonaId > 0) {
        foreach ($zonas as $zona) {
            if ((int) $zona['id'] === (int) $zonaId) {
                return $zona;
            }
        }
        return null;
    }

    if ($lat === null || $lng === null) {
        return null;
    }

    foreach ($zonas as $zona) {
        $tipo = $zona['tipo_area'] ?: 'manual';

        if ($tipo === 'radio') {
            if ($zona['centro_lat'] === null || $zona['centro_lng'] === null || $zona['radio_metros'] === null) {
                continue;
            }

            $dist = mr_geo_distance_meters(
                (float) $lat,
                (float) $lng,
                (float) $zona['centro_lat'],
                (float) $zona['centro_lng']
            );

            if ($dist <= (float) $zona['radio_metros']) {
                return $zona;
            }
        }

        if ($tipo === 'poligono') {
            $polygon = json_decode((string) $zona['poligono_json'], true);
            if (is_array($polygon) && mr_point_in_polygon((float) $lat, (float) $lng, $polygon)) {
                return $zona;
            }
        }
    }

    return null;
}

function mr_mp_config()
{
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }

    $config = require __DIR__ . '/mr_config.php';
    $cfg = $config['mercadopago'] ?? [];

    return $cfg;
}

function mr_mp_is_enabled()
{
    $cfg = mr_mp_config();
    return !empty($cfg['access_token']);
}

function mr_http_json_request($method, $url, $headers = [], $payload = null)
{
    $ch = curl_init();

    $httpHeaders = ['Content-Type: application/json'];
    foreach ($headers as $k => $v) {
        if (is_int($k)) {
            $httpHeaders[] = $v;
        } else {
            $httpHeaders[] = $k . ': ' . $v;
        }
    }

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_HTTPHEADER => $httpHeaders,
        CURLOPT_TIMEOUT => 20,
    ];

    if ($payload !== null) {
        $options[CURLOPT_POSTFIELDS] = json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    curl_setopt_array($ch, $options);
    $responseBody = curl_exec($ch);
    $curlError = curl_error($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlError) {
        return [
            'ok' => false,
            'status' => 0,
            'error' => $curlError,
            'data' => null,
        ];
    }

    $decoded = null;
    if (is_string($responseBody) && $responseBody !== '') {
        $decoded = json_decode($responseBody, true);
    }

    return [
        'ok' => $statusCode >= 200 && $statusCode < 300,
        'status' => $statusCode,
        'error' => null,
        'data' => $decoded,
        'raw' => $responseBody,
    ];
}

function mr_mp_status_to_internal($mpStatus)
{
    $status = strtolower((string) $mpStatus);

    if (in_array($status, ['approved', 'accredited', 'authorized'], true)) {
        return 'aprobado';
    }

    if (in_array($status, ['rejected', 'cancelled', 'charged_back'], true)) {
        return 'rechazado';
    }

    if (in_array($status, ['refunded'], true)) {
        return 'reembolsado';
    }

    return 'pendiente';
}

function mr_resolve_restaurante_id($user, $requestedId = 0)
{
    $requestedId = (int) $requestedId;

    if (!is_array($user)) {
        return 0;
    }

    if (($user['rol'] ?? '') !== 'superadmin') {
        return (int) ($user['restaurante_id'] ?? 0);
    }

    if ($requestedId > 0) {
        return $requestedId;
    }

    $conn = mr_db();
    $sql = 'SELECT id FROM restaurantes WHERE activo = 1 ORDER BY id ASC LIMIT 1';
    $res = mysqli_query($conn, $sql);
    $row = $res ? mysqli_fetch_assoc($res) : null;

    return $row ? (int) $row['id'] : 0;
}
