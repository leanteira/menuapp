<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('GET');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['cliente_id'])) {
    mr_json_response([
        'ok' => false,
        'error' => 'Usuario no autenticado.',
    ], 401);
}

$clienteId = (int) $_SESSION['cliente_id'];
$conn = mr_db();

// Obtener pedidos del cliente
$sql = 'SELECT 
    p.id, 
    p.tipo, 
    p.estado, 
    p.total,
    p.created_at,
    cd.direccion AS direccion_texto,
    (
        SELECT COUNT(*)
        FROM pedido_items pi
        WHERE pi.pedido_id = p.id
    ) AS items_count
FROM pedidos p
LEFT JOIN clientes_direcciones cd ON cd.id = p.direccion_id
WHERE p.cliente_id = ?
ORDER BY p.created_at DESC';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $clienteId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pedidos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pedidos[] = [
        'id' => (int) $row['id'],
        'tipo' => (string) $row['tipo'],
        'estado' => (string) $row['estado'],
        'total' => (float) $row['total'],
        'direccion' => (string) ($row['tipo'] === 'delivery' ? ($row['direccion_texto'] ?? 'Sin especificar') : 'Retiro en local'),
        'items_count' => (int) $row['items_count'],
        'created_at' => (string) $row['created_at'],
    ];
}

mysqli_stmt_close($stmt);

mr_json_response([
    'ok' => true,
    'pedidos' => $pedidos,
]);
