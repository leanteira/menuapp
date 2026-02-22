<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('GET');

// Verificar autenticación
if (!isset($_SESSION['cliente_id'])) {
    mr_json_response([
        'ok' => false,
        'error' => 'Usuario no autenticado.',
    ], 401);
}

$clienteId = (int) $_SESSION['cliente_id'];
$conn = mr_db();

// Obtener direcciones del cliente
$sql = 'SELECT id, direccion, referencia, is_favorita, created_at FROM clientes_direcciones WHERE cliente_id = ? ORDER BY is_favorita DESC, created_at DESC';
$stmt = mysqli_prepare($conn, $sql);
$hasFavoriteColumn = true;

if (!$stmt) {
    // Fallback para esquemas que todavía no tienen is_favorita
    $hasFavoriteColumn = false;
    $sql = 'SELECT id, direccion, referencia, created_at FROM clientes_direcciones WHERE cliente_id = ? ORDER BY created_at DESC';
    $stmt = mysqli_prepare($conn, $sql);
}

if (!$stmt) {
    mr_json_response([
        'ok' => false,
        'error' => 'No se pudieron cargar las direcciones.',
    ], 500);
}

mysqli_stmt_bind_param($stmt, 'i', $clienteId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$direcciones = [];
while ($row = mysqli_fetch_assoc($result)) {
    $direcciones[] = [
        'id' => (int) $row['id'],
        'direccion' => (string) $row['direccion'],
        'referencia' => (string) ($row['referencia'] ?? ''),
        'is_favorita' => $hasFavoriteColumn ? (bool) (int) ($row['is_favorita'] ?? 0) : false,
        'created_at' => (string) $row['created_at'],
    ];
}

mysqli_stmt_close($stmt);

mr_json_response([
    'ok' => true,
    'direcciones' => $direcciones,
]);
