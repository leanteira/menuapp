<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('POST');

// Verificar autenticación
if (!isset($_SESSION['cliente_id'])) {
    mr_json_response([
        'ok' => false,
        'error' => 'Usuario no autenticado.',
    ], 401);
}

$input = mr_get_json_input();
$direccion = trim((string) ($input['direccion'] ?? ''));
$referencia = trim((string) ($input['referencia'] ?? ''));
$is_favorita = (int) ($input['is_favorita'] ?? 0);

if (!$direccion) {
    mr_json_response([
        'ok' => false,
        'error' => 'La dirección es requerida.',
    ], 400);
}

$clienteId = (int) $_SESSION['cliente_id'];
$conn = mr_db();

$direccionLookup = mb_strtolower(trim($direccion), 'UTF-8');
$referenciaLookup = mb_strtolower(trim($referencia), 'UTF-8');

$sqlExisting = 'SELECT id FROM clientes_direcciones WHERE cliente_id = ? AND LOWER(TRIM(direccion)) = ? AND LOWER(TRIM(COALESCE(referencia, ""))) = ? LIMIT 1';
$stmtExisting = mysqli_prepare($conn, $sqlExisting);
if ($stmtExisting) {
    mysqli_stmt_bind_param($stmtExisting, 'iss', $clienteId, $direccionLookup, $referenciaLookup);
    mysqli_stmt_execute($stmtExisting);
    $resExisting = mysqli_stmt_get_result($stmtExisting);
    $existing = mysqli_fetch_assoc($resExisting);
    mysqli_stmt_close($stmtExisting);

    if ($existing) {
        $existingId = (int) $existing['id'];

        if ($is_favorita) {
            $resetSql = 'UPDATE clientes_direcciones SET is_favorita = 0 WHERE cliente_id = ?';
            $resetStmt = mysqli_prepare($conn, $resetSql);
            if ($resetStmt) {
                mysqli_stmt_bind_param($resetStmt, 'i', $clienteId);
                mysqli_stmt_execute($resetStmt);
                mysqli_stmt_close($resetStmt);
            }

            $favSql = 'UPDATE clientes_direcciones SET is_favorita = 1 WHERE id = ? AND cliente_id = ?';
            $favStmt = mysqli_prepare($conn, $favSql);
            if ($favStmt) {
                mysqli_stmt_bind_param($favStmt, 'ii', $existingId, $clienteId);
                mysqli_stmt_execute($favStmt);
                mysqli_stmt_close($favStmt);
            }
        }

        mr_json_response([
            'ok' => true,
            'direccion_id' => $existingId,
            'reused' => true,
        ]);
    }
}

$hasFavoriteColumn = true;

// Si es favorita, desmarcar otras como favorita
if ($is_favorita) {
    $updateSql = 'UPDATE clientes_direcciones SET is_favorita = 0 WHERE cliente_id = ?';
    $updateStmt = mysqli_prepare($conn, $updateSql);
    if ($updateStmt) {
        mysqli_stmt_bind_param($updateStmt, 'i', $clienteId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    } else {
        $hasFavoriteColumn = false;
    }
}

// Insertar nueva dirección
$insertSql = 'INSERT INTO clientes_direcciones (cliente_id, direccion, referencia, is_favorita) VALUES (?, ?, ?, ?)';
$insertStmt = mysqli_prepare($conn, $insertSql);

if ($insertStmt) {
    mysqli_stmt_bind_param($insertStmt, 'issi', $clienteId, $direccion, $referencia, $is_favorita);
} else {
    $hasFavoriteColumn = false;
    $insertSql = 'INSERT INTO clientes_direcciones (cliente_id, direccion, referencia) VALUES (?, ?, ?)';
    $insertStmt = mysqli_prepare($conn, $insertSql);
    if (!$insertStmt) {
        mr_json_response([
            'ok' => false,
            'error' => 'No se pudo preparar el guardado de dirección.',
        ], 500);
    }
    mysqli_stmt_bind_param($insertStmt, 'iss', $clienteId, $direccion, $referencia);
}

if (!mysqli_stmt_execute($insertStmt)) {
    mr_json_response([
        'ok' => false,
        'error' => 'Error al guardar la dirección.',
    ], 500);
}

mysqli_stmt_close($insertStmt);

$direccionId = mysqli_insert_id($conn);

mr_json_response([
    'ok' => true,
    'direccion_id' => (int) $direccionId,
]);
