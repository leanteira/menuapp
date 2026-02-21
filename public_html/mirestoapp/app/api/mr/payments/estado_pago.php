<?php

require_once __DIR__ . '/../../../mr_bootstrap.php';

mr_require_method('GET');

$pedidoId = (int) mr_request_param('pedido_id', 0);
if ($pedidoId <= 0) {
    mr_json_response(['ok' => false, 'error' => 'pedido_id requerido.'], 400);
}

$conn = mr_db();
$stmt = mysqli_prepare($conn, 'SELECT id, metodo, estado, monto, referencia_externa, created_at FROM pagos WHERE pedido_id = ? ORDER BY id DESC LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $pedidoId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$pago = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$pago) {
    mr_json_response(['ok' => false, 'error' => 'Pago no encontrado para el pedido.'], 404);
}

mr_json_response([
    'ok' => true,
    'pago' => [
        'id' => (int) $pago['id'],
        'metodo' => $pago['metodo'],
        'estado' => $pago['estado'],
        'monto' => (float) $pago['monto'],
        'referencia_externa' => $pago['referencia_externa'],
        'created_at' => $pago['created_at'],
    ],
]);
