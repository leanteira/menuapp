<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador', 'repartidor']);
mr_require_method('POST');

$payload = mr_get_json_input();

$pedidoId = isset($payload['pedido_id']) ? (int) $payload['pedido_id'] : 0;
$nuevoEstado = isset($payload['estado']) ? trim((string) $payload['estado']) : '';

$estadosValidos = ['nuevo', 'confirmado', 'preparando', 'listo', 'enviado', 'entregado', 'cancelado'];
if ($pedidoId <= 0 || !in_array($nuevoEstado, $estadosValidos, true)) {
    mr_json_response([
        'ok' => false,
        'error' => 'Parámetros inválidos.',
    ], 400);
}

$conn = mr_db();
$user = mr_user();

$sqlPedido = 'SELECT id, restaurante_id FROM pedidos WHERE id = ? LIMIT 1';
$stmtPedido = mysqli_prepare($conn, $sqlPedido);
mysqli_stmt_bind_param($stmtPedido, 'i', $pedidoId);
mysqli_stmt_execute($stmtPedido);
$resPedido = mysqli_stmt_get_result($stmtPedido);
$pedido = mysqli_fetch_assoc($resPedido);
mysqli_stmt_close($stmtPedido);

if (!$pedido) {
    mr_json_response([
        'ok' => false,
        'error' => 'Pedido no encontrado.',
    ], 404);
}

if ($user['rol'] !== 'superadmin' && (int) $pedido['restaurante_id'] !== (int) $user['restaurante_id']) {
    mr_json_response([
        'ok' => false,
        'error' => 'No autorizado para modificar este pedido.',
    ], 403);
}

mysqli_begin_transaction($conn);

try {
    $sqlUpd = 'UPDATE pedidos SET estado = ? WHERE id = ?';
    $stmtUpd = mysqli_prepare($conn, $sqlUpd);
    mysqli_stmt_bind_param($stmtUpd, 'si', $nuevoEstado, $pedidoId);
    mysqli_stmt_execute($stmtUpd);
    mysqli_stmt_close($stmtUpd);

    $sqlHist = 'INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at) VALUES (?, ?, ?)';
    $stmtHist = mysqli_prepare($conn, $sqlHist);
    $changedAt = mr_now();
    mysqli_stmt_bind_param($stmtHist, 'iss', $pedidoId, $nuevoEstado, $changedAt);
    mysqli_stmt_execute($stmtHist);
    mysqli_stmt_close($stmtHist);

    mysqli_commit($conn);

    mr_json_response([
        'ok' => true,
        'pedido_id' => $pedidoId,
        'estado' => $nuevoEstado,
        'changed_at' => $changedAt,
    ]);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    mr_json_response([
        'ok' => false,
        'error' => $e->getMessage(),
    ], 422);
}
