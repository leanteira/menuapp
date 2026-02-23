<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);
mr_require_method('GET');

$conn = mr_db();
$user = mr_user();

$telefono = trim((string) mr_request_param('telefono', ''));
$clienteIdParam = (int) mr_request_param('cliente_id', 0);

if ($telefono === '' && $clienteIdParam <= 0) {
    mr_json_response(['ok' => false, 'error' => 'telefono o cliente_id requerido.'], 400);
}

$restauranteId = mr_resolve_restaurante_id($user, (int) mr_request_param('restaurante_id', 0));

if ($restauranteId <= 0) {
    mr_json_response(['ok' => false, 'error' => 'restaurante_id requerido.'], 400);
}

function mr_fetch_cliente_data($conn, $restauranteId, $clienteId)
{
    $stmt = mysqli_prepare($conn, 'SELECT id, nombre, telefono, email, created_at FROM clientes WHERE restaurante_id = ? AND id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ii', $restauranteId, $clienteId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $cliente = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$cliente) {
        return null;
    }

    $direcciones = [];
    $stmtDir = mysqli_prepare($conn, 'SELECT id, direccion, referencia, lat, lng, created_at FROM clientes_direcciones WHERE cliente_id = ? ORDER BY id DESC LIMIT 10');
    mysqli_stmt_bind_param($stmtDir, 'i', $clienteId);
    mysqli_stmt_execute($stmtDir);
    $resDir = mysqli_stmt_get_result($stmtDir);
    while ($row = mysqli_fetch_assoc($resDir)) {
        $direcciones[] = [
            'id' => (int) $row['id'],
            'direccion' => $row['direccion'],
            'referencia' => $row['referencia'],
            'lat' => $row['lat'] !== null ? (float) $row['lat'] : null,
            'lng' => $row['lng'] !== null ? (float) $row['lng'] : null,
            'created_at' => $row['created_at'],
        ];
    }
    mysqli_stmt_close($stmtDir);

    $historial = [];
    $stmtHist = mysqli_prepare($conn, 'SELECT id, tipo, estado, total, created_at FROM pedidos WHERE cliente_id = ? AND restaurante_id = ? ORDER BY id DESC LIMIT 20');
    mysqli_stmt_bind_param($stmtHist, 'ii', $clienteId, $restauranteId);
    mysqli_stmt_execute($stmtHist);
    $resHist = mysqli_stmt_get_result($stmtHist);
    while ($row = mysqli_fetch_assoc($resHist)) {
        $historial[] = [
            'id' => (int) $row['id'],
            'tipo' => $row['tipo'],
            'estado' => $row['estado'],
            'total' => (float) $row['total'],
            'created_at' => $row['created_at'],
        ];
    }
    mysqli_stmt_close($stmtHist);

    return [
        'cliente' => [
            'id' => (int) $cliente['id'],
            'nombre' => $cliente['nombre'],
            'telefono' => $cliente['telefono'],
            'email' => $cliente['email'],
            'created_at' => $cliente['created_at'],
        ],
        'direcciones' => $direcciones,
        'historial' => $historial,
    ];
}

if ($clienteIdParam > 0) {
    $data = mr_fetch_cliente_data($conn, $restauranteId, $clienteIdParam);
    if (!$data) {
        mr_json_response(['ok' => true, 'cliente' => null, 'direcciones' => [], 'historial' => [], 'matches' => []]);
    }

    mr_json_response([
        'ok' => true,
        'cliente' => $data['cliente'],
        'direcciones' => $data['direcciones'],
        'historial' => $data['historial'],
        'matches' => [[
            'id' => $data['cliente']['id'],
            'nombre' => $data['cliente']['nombre'],
            'telefono' => $data['cliente']['telefono'],
            'email' => $data['cliente']['email'],
        ]],
    ]);
}

$stmtExact = mysqli_prepare($conn, 'SELECT id, nombre, telefono, email FROM clientes WHERE restaurante_id = ? AND telefono = ? ORDER BY id DESC LIMIT 10');
mysqli_stmt_bind_param($stmtExact, 'is', $restauranteId, $telefono);
mysqli_stmt_execute($stmtExact);
$resExact = mysqli_stmt_get_result($stmtExact);
$exactMatches = [];
while ($row = mysqli_fetch_assoc($resExact)) {
    $exactMatches[] = [
        'id' => (int) $row['id'],
        'nombre' => $row['nombre'],
        'telefono' => $row['telefono'],
        'email' => $row['email'],
    ];
}
mysqli_stmt_close($stmtExact);

$matches = $exactMatches;

if (count($matches) === 0) {
    $telefonoLike = '%' . $telefono . '%';
    $stmtLike = mysqli_prepare($conn, 'SELECT id, nombre, telefono, email FROM clientes WHERE restaurante_id = ? AND telefono LIKE ? ORDER BY id DESC LIMIT 10');
    mysqli_stmt_bind_param($stmtLike, 'is', $restauranteId, $telefonoLike);
    mysqli_stmt_execute($stmtLike);
    $resLike = mysqli_stmt_get_result($stmtLike);
    while ($row = mysqli_fetch_assoc($resLike)) {
        $matches[] = [
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
            'telefono' => $row['telefono'],
            'email' => $row['email'],
        ];
    }
    mysqli_stmt_close($stmtLike);
}

if (count($matches) === 0) {
    mr_json_response(['ok' => true, 'cliente' => null, 'direcciones' => [], 'historial' => [], 'matches' => []]);
}

$selectedClienteId = count($exactMatches) >= 1 ? (int) $exactMatches[0]['id'] : (count($matches) === 1 ? (int) $matches[0]['id'] : 0);

if ($selectedClienteId <= 0) {
    mr_json_response([
        'ok' => true,
        'cliente' => null,
        'direcciones' => [],
        'historial' => [],
        'matches' => $matches,
    ]);
}

$data = mr_fetch_cliente_data($conn, $restauranteId, $selectedClienteId);
if (!$data) {
    mr_json_response(['ok' => true, 'cliente' => null, 'direcciones' => [], 'historial' => [], 'matches' => $matches]);
}

mr_json_response([
    'ok' => true,
    'cliente' => $data['cliente'],
    'direcciones' => $data['direcciones'],
    'historial' => $data['historial'],
    'matches' => $matches,
]);
