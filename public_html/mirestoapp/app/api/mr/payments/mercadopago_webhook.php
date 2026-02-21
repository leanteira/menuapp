<?php

require_once __DIR__ . '/../../../mr_bootstrap.php';

$cfg = mr_mp_config();
if (empty($cfg['access_token'])) {
    http_response_code(503);
    echo 'Mercado Pago no configurado';
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['POST', 'GET'], true)) {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

$payload = [];
if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    if ($raw) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $payload = $decoded;
        }
    }
}

$type = $payload['type'] ?? ($_GET['type'] ?? ($_GET['topic'] ?? ''));
$dataId = $payload['data']['id'] ?? ($_GET['data_id'] ?? ($_GET['id'] ?? null));

if ($type !== 'payment' || empty($dataId)) {
    http_response_code(200);
    echo 'ignored';
    exit;
}

$apiBase = rtrim((string) $cfg['base_url'], '/');
$token = (string) $cfg['access_token'];

$paymentResp = mr_http_json_request(
    'GET',
    $apiBase . '/v1/payments/' . urlencode((string) $dataId),
    [
        'Authorization' => 'Bearer ' . $token,
    ]
);

if (!$paymentResp['ok']) {
    http_response_code(502);
    echo 'gateway_error';
    exit;
}

$payment = $paymentResp['data'] ?? [];
$externalReference = (string) ($payment['external_reference'] ?? '');
$mpStatus = (string) ($payment['status'] ?? '');
$internalStatus = mr_mp_status_to_internal($mpStatus);

$pedidoId = 0;
$pagoId = 0;

if ($externalReference !== '') {
    $parts = explode('|', $externalReference);
    foreach ($parts as $part) {
        $pair = explode(':', $part, 2);
        if (count($pair) !== 2) {
            continue;
        }

        if ($pair[0] === 'pedido') {
            $pedidoId = (int) $pair[1];
        }

        if ($pair[0] === 'pago') {
            $pagoId = (int) $pair[1];
        }
    }
}

$conn = mr_db();

if ($pagoId <= 0 && $pedidoId > 0) {
    $stmtLookup = mysqli_prepare($conn, "SELECT id FROM pagos WHERE pedido_id = ? AND metodo = 'mercadopago' ORDER BY id DESC LIMIT 1");
    mysqli_stmt_bind_param($stmtLookup, 'i', $pedidoId);
    mysqli_stmt_execute($stmtLookup);
    $resLookup = mysqli_stmt_get_result($stmtLookup);
    if ($row = mysqli_fetch_assoc($resLookup)) {
        $pagoId = (int) $row['id'];
    }
    mysqli_stmt_close($stmtLookup);
}

if ($pagoId <= 0) {
    http_response_code(200);
    echo 'payment_not_mapped';
    exit;
}

$monto = isset($payment['transaction_amount']) ? (float) $payment['transaction_amount'] : null;
$referenceExternal = (string) ($payment['id'] ?? $dataId);

if ($monto !== null) {
    $stmtUpd = mysqli_prepare($conn, 'UPDATE pagos SET estado = ?, monto = ?, referencia_externa = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmtUpd, 'sdsi', $internalStatus, $monto, $referenceExternal, $pagoId);
} else {
    $stmtUpd = mysqli_prepare($conn, 'UPDATE pagos SET estado = ?, referencia_externa = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmtUpd, 'ssi', $internalStatus, $referenceExternal, $pagoId);
}

mysqli_stmt_execute($stmtUpd);
mysqli_stmt_close($stmtUpd);

if ($pedidoId > 0 && $internalStatus === 'aprobado') {
    $stmtGet = mysqli_prepare($conn, 'SELECT estado FROM pedidos WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmtGet, 'i', $pedidoId);
    mysqli_stmt_execute($stmtGet);
    $resGet = mysqli_stmt_get_result($stmtGet);
    $pedidoActual = mysqli_fetch_assoc($resGet);
    mysqli_stmt_close($stmtGet);

    if ($pedidoActual && $pedidoActual['estado'] === 'nuevo') {
        $stmtPedido = mysqli_prepare($conn, "UPDATE pedidos SET estado = 'confirmado' WHERE id = ?");
        mysqli_stmt_bind_param($stmtPedido, 'i', $pedidoId);
        mysqli_stmt_execute($stmtPedido);
        mysqli_stmt_close($stmtPedido);

        $changedAt = mr_now();
        $estadoHist = 'confirmado';
        $stmtHist = mysqli_prepare($conn, 'INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at) VALUES (?, ?, ?)');
        mysqli_stmt_bind_param($stmtHist, 'iss', $pedidoId, $estadoHist, $changedAt);
        mysqli_stmt_execute($stmtHist);
        mysqli_stmt_close($stmtHist);
    }
}

http_response_code(200);
echo 'ok';
