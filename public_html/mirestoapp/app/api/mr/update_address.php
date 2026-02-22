<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('POST');

if (!isset($_SESSION['cliente_id'])) {
    mr_json_response([
        'ok' => false,
        'error' => 'Usuario no autenticado.',
    ], 401);
}

$input = mr_get_json_input();
$direccionId = (int) ($input['direccion_id'] ?? 0);
$direccion = trim((string) ($input['direccion'] ?? ''));
$referencia = trim((string) ($input['referencia'] ?? ''));
$isFavorita = (int) ($input['is_favorita'] ?? 0);

if ($direccionId <= 0 || $direccion === '') {
    mr_json_response([
        'ok' => false,
        'error' => 'Datos de dirección inválidos.',
    ], 400);
}

$clienteId = (int) $_SESSION['cliente_id'];
$conn = mr_db();

// Verificar que la dirección pertenezca al cliente
$sqlOwner = 'SELECT id FROM clientes_direcciones WHERE id = ? AND cliente_id = ? LIMIT 1';
$stmtOwner = mysqli_prepare($conn, $sqlOwner);
mysqli_stmt_bind_param($stmtOwner, 'ii', $direccionId, $clienteId);
mysqli_stmt_execute($stmtOwner);
$resOwner = mysqli_stmt_get_result($stmtOwner);
$ownerRow = mysqli_fetch_assoc($resOwner);
mysqli_stmt_close($stmtOwner);

if (!$ownerRow) {
    mr_json_response([
        'ok' => false,
        'error' => 'Dirección no encontrada.',
    ], 404);
}

$hasFavoriteColumn = true;
if ($isFavorita) {
    $sqlResetFav = 'UPDATE clientes_direcciones SET is_favorita = 0 WHERE cliente_id = ?';
    $stmtResetFav = mysqli_prepare($conn, $sqlResetFav);
    if ($stmtResetFav) {
        mysqli_stmt_bind_param($stmtResetFav, 'i', $clienteId);
        mysqli_stmt_execute($stmtResetFav);
        mysqli_stmt_close($stmtResetFav);
    } else {
        $hasFavoriteColumn = false;
    }
}

$sqlUpdate = 'UPDATE clientes_direcciones SET direccion = ?, referencia = ?, is_favorita = ? WHERE id = ? AND cliente_id = ?';
$stmtUpdate = mysqli_prepare($conn, $sqlUpdate);

if ($stmtUpdate) {
    mysqli_stmt_bind_param($stmtUpdate, 'ssiii', $direccion, $referencia, $isFavorita, $direccionId, $clienteId);
} else {
    $hasFavoriteColumn = false;
    $sqlUpdate = 'UPDATE clientes_direcciones SET direccion = ?, referencia = ? WHERE id = ? AND cliente_id = ?';
    $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
    if (!$stmtUpdate) {
        mr_json_response([
            'ok' => false,
            'error' => 'No se pudo preparar la actualización de dirección.',
        ], 500);
    }
    mysqli_stmt_bind_param($stmtUpdate, 'ssii', $direccion, $referencia, $direccionId, $clienteId);
}

if (!mysqli_stmt_execute($stmtUpdate)) {
    mysqli_stmt_close($stmtUpdate);
    mr_json_response([
        'ok' => false,
        'error' => 'No se pudo actualizar la dirección.',
    ], 500);
}
mysqli_stmt_close($stmtUpdate);

mr_json_response([
    'ok' => true,
    'direccion' => [
        'id' => $direccionId,
        'direccion' => $direccion,
        'referencia' => $referencia,
        'is_favorita' => $hasFavoriteColumn ? (bool) $isFavorita : false,
    ],
]);
