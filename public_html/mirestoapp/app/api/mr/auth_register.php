<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('POST');

$input = mr_get_json_input();
$nombre = trim((string) ($input['nombre'] ?? ''));
$telefono = trim((string) ($input['telefono'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));
$password = trim((string) ($input['password'] ?? ''));

if (!$nombre || !$telefono || !$password) {
    mr_json_response([
        'ok' => false,
        'error' => 'Nombre, teléfono y contraseña son requeridos.',
    ], 400);
}

$conn = mr_db();

// Verificar si el teléfono ya existe
$checkSql = 'SELECT id FROM clientes WHERE telefono = ? LIMIT 1';
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, 's', $telefono);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_fetch_assoc($checkResult)) {
    mr_json_response([
        'ok' => false,
        'error' => 'El teléfono ya está registrado.',
    ], 409);
}

// Insertar nuevo cliente (asumimos restaurante_id = 1)
$restauranteId = 1;
$insertSql = 'INSERT INTO clientes (restaurante_id, nombre, telefono, email, stored_password) VALUES (?, ?, ?, ?, ?)';
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, 'issss', $restauranteId, $nombre, $telefono, $email, $password);

if (!mysqli_stmt_execute($insertStmt)) {
    mr_json_response([
        'ok' => false,
        'error' => 'Error al crear la cuenta.',
    ], 500);
}

$clienteId = mysqli_insert_id($conn);

mr_json_response([
    'ok' => true,
    'cliente_id' => (int) $clienteId,
    'nombre' => $nombre,
]);
