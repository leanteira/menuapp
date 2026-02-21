<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);

$conn = mr_db();
$user = mr_user();
$method = $_SERVER['REQUEST_METHOD'];

function mr_normalize_polygon($raw)
{
    if (!is_array($raw)) {
        return null;
    }

    $clean = [];
    foreach ($raw as $point) {
        if (!is_array($point)) {
            continue;
        }

        if (!isset($point['lat']) || !isset($point['lng'])) {
            continue;
        }

        $clean[] = [
            'lat' => (float) $point['lat'],
            'lng' => (float) $point['lng'],
        ];
    }

    return count($clean) >= 3 ? $clean : null;
}

if ($method === 'GET') {
    $restauranteId = mr_resolve_restaurante_id($user, (int) mr_request_param('restaurante_id', 0));

    if ($restauranteId <= 0) {
        mr_json_response(['ok' => false, 'error' => 'restaurante_id requerido.'], 400);
    }

    $sql = 'SELECT id, nombre, costo_envio, pedido_minimo, activo,
                   COALESCE(tipo_area, "manual") AS tipo_area,
                   centro_lat, centro_lng, radio_metros, poligono_json
            FROM zonas_envio
            WHERE restaurante_id = ?
            ORDER BY id DESC';

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $restauranteId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $zonas = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $zonas[] = [
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
            'costo_envio' => (float) $row['costo_envio'],
            'pedido_minimo' => (float) $row['pedido_minimo'],
            'activo' => (int) $row['activo'] === 1,
            'tipo_area' => $row['tipo_area'],
            'centro_lat' => $row['centro_lat'] !== null ? (float) $row['centro_lat'] : null,
            'centro_lng' => $row['centro_lng'] !== null ? (float) $row['centro_lng'] : null,
            'radio_metros' => $row['radio_metros'] !== null ? (float) $row['radio_metros'] : null,
            'poligono' => $row['poligono_json'] ? json_decode($row['poligono_json'], true) : null,
        ];
    }

    mysqli_stmt_close($stmt);
    mr_json_response(['ok' => true, 'zonas' => $zonas]);
}

if ($method === 'POST') {
    $payload = mr_get_json_input();
    $action = trim((string) ($payload['action'] ?? 'create'));

    $nombre = trim((string) ($payload['nombre'] ?? ''));
    $costoEnvio = (float) ($payload['costo_envio'] ?? 0);
    $pedidoMinimo = (float) ($payload['pedido_minimo'] ?? 0);
    $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;
    $tipoArea = trim((string) ($payload['tipo_area'] ?? 'manual'));

    if (!in_array($tipoArea, ['manual', 'radio', 'poligono'], true)) {
        $tipoArea = 'manual';
    }

    $centroLat = isset($payload['centro_lat']) && $payload['centro_lat'] !== '' ? (float) $payload['centro_lat'] : null;
    $centroLng = isset($payload['centro_lng']) && $payload['centro_lng'] !== '' ? (float) $payload['centro_lng'] : null;
    $radioMetros = isset($payload['radio_metros']) && $payload['radio_metros'] !== '' ? (float) $payload['radio_metros'] : null;
    $polygon = mr_normalize_polygon($payload['poligono'] ?? null);
    $poligonoJson = $polygon ? json_encode($polygon, JSON_UNESCAPED_UNICODE) : null;

    if ($nombre === '') {
        mr_json_response(['ok' => false, 'error' => 'Nombre requerido.'], 400);
    }

    if ($tipoArea === 'radio' && ($centroLat === null || $centroLng === null || $radioMetros === null || $radioMetros <= 0)) {
        mr_json_response(['ok' => false, 'error' => 'Para zona tipo radio debes indicar centro_lat, centro_lng y radio_metros.'], 400);
    }

    if ($tipoArea === 'poligono' && !$polygon) {
        mr_json_response(['ok' => false, 'error' => 'Para zona tipo polígono debes indicar al menos 3 puntos válidos.'], 400);
    }

    if ($action === 'create') {
        $restauranteId = mr_resolve_restaurante_id($user, (int) ($payload['restaurante_id'] ?? 0));

        if ($restauranteId <= 0) {
            mr_json_response(['ok' => false, 'error' => 'restaurante_id inválido.'], 400);
        }

        $sql = 'INSERT INTO zonas_envio (restaurante_id, nombre, costo_envio, pedido_minimo, activo, tipo_area, centro_lat, centro_lng, radio_metros, poligono_json)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'isddisddds', $restauranteId, $nombre, $costoEnvio, $pedidoMinimo, $activo, $tipoArea, $centroLat, $centroLng, $radioMetros, $poligonoJson);
        mysqli_stmt_execute($stmt);
        $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true, 'id' => $id], 201);
    }

    if ($action === 'update') {
        $id = (int) ($payload['id'] ?? 0);
        if ($id <= 0) {
            mr_json_response(['ok' => false, 'error' => 'id inválido para actualizar.'], 400);
        }

        $sql = 'UPDATE zonas_envio SET nombre = ?, costo_envio = ?, pedido_minimo = ?, activo = ?, tipo_area = ?, centro_lat = ?, centro_lng = ?, radio_metros = ?, poligono_json = ? WHERE id = ?';
        if ($user['rol'] !== 'superadmin') {
            $sql .= ' AND restaurante_id = ?';
        }

        $stmt = mysqli_prepare($conn, $sql);
        if ($user['rol'] === 'superadmin') {
            mysqli_stmt_bind_param($stmt, 'sddisdddsi', $nombre, $costoEnvio, $pedidoMinimo, $activo, $tipoArea, $centroLat, $centroLng, $radioMetros, $poligonoJson, $id);
        } else {
            $restauranteId = (int) $user['restaurante_id'];
            mysqli_stmt_bind_param($stmt, 'sddisdddsii', $nombre, $costoEnvio, $pedidoMinimo, $activo, $tipoArea, $centroLat, $centroLng, $radioMetros, $poligonoJson, $id, $restauranteId);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true]);
    }

    mr_json_response(['ok' => false, 'error' => 'Acción no soportada.'], 400);
}

mr_json_response(['ok' => false, 'error' => 'Método no permitido.'], 405);
