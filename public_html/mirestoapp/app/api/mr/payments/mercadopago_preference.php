<?php

require_once __DIR__ . '/../../../mr_bootstrap.php';

mr_require_method('POST');

if (!mr_mp_is_enabled()) {
    mr_json_response([
        'ok' => false,
        'error' => 'Mercado Pago no está configurado en el servidor.',
    ], 503);
}

$payload = mr_get_json_input();
$pedidoId = isset($payload['pedido_id']) ? (int) $payload['pedido_id'] : 0;

if ($pedidoId <= 0) {
    mr_json_response(['ok' => false, 'error' => 'pedido_id requerido.'], 400);
}

$conn = mr_db();

$sqlPedido = 'SELECT p.id, p.restaurante_id, p.total, p.estado, r.nombre AS restaurante_nombre
              FROM pedidos p
              INNER JOIN restaurantes r ON r.id = p.restaurante_id
              WHERE p.id = ?
              LIMIT 1';
$stmtPedido = mysqli_prepare($conn, $sqlPedido);
mysqli_stmt_bind_param($stmtPedido, 'i', $pedidoId);
mysqli_stmt_execute($stmtPedido);
$resPedido = mysqli_stmt_get_result($stmtPedido);
$pedido = mysqli_fetch_assoc($resPedido);
mysqli_stmt_close($stmtPedido);

if (!$pedido) {
    mr_json_response(['ok' => false, 'error' => 'Pedido no encontrado.'], 404);
}

if (in_array($pedido['estado'], ['cancelado'], true)) {
    mr_json_response(['ok' => false, 'error' => 'No se puede pagar un pedido cancelado.'], 422);
}

$sqlPago = "SELECT id, estado, metodo, monto
            FROM pagos
            WHERE pedido_id = ? AND metodo = 'mercadopago'
            ORDER BY id DESC
            LIMIT 1";
$stmtPago = mysqli_prepare($conn, $sqlPago);
mysqli_stmt_bind_param($stmtPago, 'i', $pedidoId);
mysqli_stmt_execute($stmtPago);
$resPago = mysqli_stmt_get_result($stmtPago);
$pago = mysqli_fetch_assoc($resPago);
mysqli_stmt_close($stmtPago);

if (!$pago) {
    $sqlInsPago = "INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia_externa, created_at)
                   VALUES (?, 'mercadopago', 'pendiente', ?, NULL, ?)";
    $stmtInsPago = mysqli_prepare($conn, $sqlInsPago);
    $createdAt = mr_now();
    $monto = (float) $pedido['total'];
    mysqli_stmt_bind_param($stmtInsPago, 'ids', $pedidoId, $monto, $createdAt);
    mysqli_stmt_execute($stmtInsPago);
    $pagoId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmtInsPago);
} else {
    $pagoId = (int) $pago['id'];
    if (in_array($pago['estado'], ['aprobado', 'reembolsado'], true)) {
        mr_json_response([
            'ok' => false,
            'error' => 'Este pedido ya tiene un pago cerrado (' . $pago['estado'] . ').',
        ], 409);
    }
}

$cfg = mr_mp_config();
$apiBase = rtrim((string) $cfg['base_url'], '/');
$token = (string) $cfg['access_token'];

$body = [
    'items' => [[
        'title' => 'Pedido #' . $pedidoId . ' - ' . $pedido['restaurante_nombre'],
        'quantity' => 1,
        'currency_id' => 'ARS',
        'unit_price' => (float) $pedido['total'],
    ]],
    'external_reference' => 'pedido:' . $pedidoId . '|pago:' . $pagoId,
    'notification_url' => !empty($cfg['webhook_url']) ? $cfg['webhook_url'] : null,
    'back_urls' => [
        'success' => !empty($cfg['success_url']) ? $cfg['success_url'] : null,
        'pending' => !empty($cfg['pending_url']) ? $cfg['pending_url'] : null,
        'failure' => !empty($cfg['failure_url']) ? $cfg['failure_url'] : null,
    ],
    'auto_return' => 'approved',
    'statement_descriptor' => 'MIRESTOAPP',
];

if (empty($body['notification_url'])) {
    unset($body['notification_url']);
}

$body['back_urls'] = array_filter($body['back_urls']);
if (empty($body['back_urls'])) {
    unset($body['back_urls']);
    unset($body['auto_return']);
}

$mpResponse = mr_http_json_request(
    'POST',
    $apiBase . '/checkout/preferences',
    [
        'Authorization' => 'Bearer ' . $token,
        'X-Idempotency-Key' => 'pref-' . $pedidoId . '-' . $pagoId,
    ],
    $body
);

if (!$mpResponse['ok']) {
    mr_json_response([
        'ok' => false,
        'error' => 'No se pudo crear la preferencia en Mercado Pago.',
        'gateway_status' => $mpResponse['status'],
        'gateway_error' => $mpResponse['data'] ?? $mpResponse['error'],
    ], 502);
}

$pref = $mpResponse['data'] ?? [];
$preferenceId = (string) ($pref['id'] ?? '');

if ($preferenceId !== '') {
    $stmtUpdPago = mysqli_prepare($conn, 'UPDATE pagos SET referencia_externa = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmtUpdPago, 'si', $preferenceId, $pagoId);
    mysqli_stmt_execute($stmtUpdPago);
    mysqli_stmt_close($stmtUpdPago);
}

mr_json_response([
    'ok' => true,
    'pedido_id' => $pedidoId,
    'pago_id' => $pagoId,
    'preference_id' => $preferenceId,
    'init_point' => $pref['init_point'] ?? null,
    'sandbox_init_point' => $pref['sandbox_init_point'] ?? null,
]);
