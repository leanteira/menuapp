<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('POST');

$input = mr_get_json_input();
$telefono = trim((string) ($input['telefono'] ?? ''));
$password = trim((string) ($input['password'] ?? ''));

if (!$telefono || !$password) {
    mr_json_response([
        'ok' => false,
        'error' => 'Teléfono y contraseña son requeridos.',
    ], 400);
}

$conn = mr_db();

// Buscar cliente por teléfono
$sql = 'SELECT id, nombre, email, stored_password FROM clientes WHERE telefono = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $telefono);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cliente = mysqli_fetch_assoc($result);

if (!$cliente) {
    mr_json_response([
        'ok' => false,
        'error' => 'Teléfono o contraseña incorrectos.',
    ], 401);
}

// Verificar contraseña (en producción usar password_hash/password_verify)
// Por ahora usamos comparación simple para la demo
$storedPass = $cliente['stored_password'] ?? $password;
if ($password !== $storedPass) {
    mr_json_response([
        'ok' => false,
        'error' => 'Teléfono o contraseña incorrectos.',
    ], 401);
}

// Iniciar sesión
session_start();
$_SESSION['cliente_id'] = (int) $cliente['id'];
$_SESSION['cliente_nombre'] = (string) $cliente['nombre'];
$_SESSION['cliente_telefono'] = (string) $telefono;
$_SESSION['cliente_email'] = (string) $cliente['email'];

mr_json_response([
    'ok' => true,
    'cliente_id' => (int) $cliente['id'],
    'nombre' => (string) $cliente['nombre'],
]);
