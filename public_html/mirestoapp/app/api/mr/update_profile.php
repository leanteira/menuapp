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
$nombre = trim((string) ($input['nombre'] ?? ''));
$telefono = trim((string) ($input['telefono'] ?? ''));
$email = trim((string) ($input['email'] ?? ''));

if ($nombre === '' || $telefono === '') {
    mr_json_response([
        'ok' => false,
        'error' => 'Nombre y teléfono son obligatorios.',
    ], 400);
}

$clienteId = (int) $_SESSION['cliente_id'];
$conn = mr_db();

// Validar teléfono único (excepto yo)
$sqlCheck = 'SELECT id FROM clientes WHERE telefono = ? AND id <> ? LIMIT 1';
$stmtCheck = mysqli_prepare($conn, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, 'si', $telefono, $clienteId);
mysqli_stmt_execute($stmtCheck);
$resCheck = mysqli_stmt_get_result($stmtCheck);
if (mysqli_fetch_assoc($resCheck)) {
    mysqli_stmt_close($stmtCheck);
    mr_json_response([
        'ok' => false,
        'error' => 'Ese teléfono ya está registrado en otra cuenta.',
    ], 409);
}
mysqli_stmt_close($stmtCheck);

$sqlUpdate = 'UPDATE clientes SET nombre = ?, telefono = ?, email = ? WHERE id = ?';
$stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
if (!$stmtUpdate) {
    mr_json_response([
        'ok' => false,
        'error' => 'No se pudo preparar la actualización de perfil.',
    ], 500);
}

mysqli_stmt_bind_param($stmtUpdate, 'sssi', $nombre, $telefono, $email, $clienteId);
if (!mysqli_stmt_execute($stmtUpdate)) {
    mysqli_stmt_close($stmtUpdate);
    mr_json_response([
        'ok' => false,
        'error' => 'No se pudo actualizar el perfil.',
    ], 500);
}
mysqli_stmt_close($stmtUpdate);

$_SESSION['cliente_nombre'] = $nombre;
$_SESSION['cliente_telefono'] = $telefono;
$_SESSION['cliente_email'] = $email;

mr_json_response([
    'ok' => true,
    'cliente' => [
        'id' => $clienteId,
        'nombre' => $nombre,
        'telefono' => $telefono,
        'email' => $email,
    ],
]);
