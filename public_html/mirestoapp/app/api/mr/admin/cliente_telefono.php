<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);
mr_require_method('GET');

$conn = mr_db();
$user = mr_user();

$telefono = trim((string) mr_request_param('telefono', ''));
if ($telefono === '') {
    mr_json_response(['ok' => false, 'error' => 'telefono requerido.'], 400);
}

$restauranteId = mr_resolve_restaurante_id($user, (int) mr_request_param('restaurante_id', 0));

if ($restauranteId <= 0) {
    mr_json_response(['ok' => false, 'error' => 'restaurante_id requerido.'], 400);
}

$stmt = mysqli_prepare($conn, 'SELECT id, nombre, telefono, email, created_at FROM clientes WHERE restaurante_id = ? AND telefono = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'is', $restauranteId, $telefono);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$cliente = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$cliente) {
    mr_json_response(['ok' => true, 'cliente' => null, 'direcciones' => [], 'historial' => []]);
}

$clienteId = (int) $cliente['id'];

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

mr_json_response([
    'ok' => true,
    'cliente' => [
        'id' => $clienteId,
        'nombre' => $cliente['nombre'],
        'telefono' => $cliente['telefono'],
        'email' => $cliente['email'],
        'created_at' => $cliente['created_at'],
    ],
    'direcciones' => $direcciones,
    'historial' => $historial,
]);
