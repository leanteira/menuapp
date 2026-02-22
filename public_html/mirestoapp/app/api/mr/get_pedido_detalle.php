<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('GET');

if (!isset($_SESSION['cliente_id'])) {
    mr_json_response([
        'ok' => false,
        'error' => 'Usuario no autenticado.',
    ], 401);
}

$pedidoId = isset($_GET['pedido_id']) ? (int) $_GET['pedido_id'] : 0;
if ($pedidoId <= 0) {
    mr_json_response([
        'ok' => false,
        'error' => 'pedido_id inválido.',
    ], 400);
}

$clienteId = (int) $_SESSION['cliente_id'];
$conn = mr_db();

$sqlPedido = 'SELECT p.id, p.tipo, p.estado, p.subtotal, p.costo_envio, p.total, p.created_at, cd.direccion AS direccion_texto
              FROM pedidos p
              LEFT JOIN clientes_direcciones cd ON cd.id = p.direccion_id
              WHERE p.id = ? AND p.cliente_id = ?
              LIMIT 1';
$stmtPedido = mysqli_prepare($conn, $sqlPedido);
mysqli_stmt_bind_param($stmtPedido, 'ii', $pedidoId, $clienteId);
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

$sqlItems = 'SELECT id, nombre_producto, cantidad, precio_unitario, total
             FROM pedido_items
             WHERE pedido_id = ?
             ORDER BY id ASC';
$stmtItems = mysqli_prepare($conn, $sqlItems);
mysqli_stmt_bind_param($stmtItems, 'i', $pedidoId);
mysqli_stmt_execute($stmtItems);
$resItems = mysqli_stmt_get_result($stmtItems);

$items = [];
$itemIds = [];
while ($row = mysqli_fetch_assoc($resItems)) {
    $itemId = (int) $row['id'];
    $itemIds[] = $itemId;
    $items[$itemId] = [
        'id' => $itemId,
        'nombre_producto' => (string) $row['nombre_producto'],
        'cantidad' => (int) $row['cantidad'],
        'precio_unitario' => (float) $row['precio_unitario'],
        'total' => (float) $row['total'],
        'detalles' => [],
    ];
}
mysqli_stmt_close($stmtItems);

if (!empty($itemIds)) {
    $in = implode(',', array_fill(0, count($itemIds), '?'));
    $types = str_repeat('i', count($itemIds));
    $sqlDet = "SELECT pedido_item_id, tipo, nombre, precio FROM pedido_item_detalles WHERE pedido_item_id IN ($in) ORDER BY id ASC";
    $stmtDet = mysqli_prepare($conn, $sqlDet);
    mysqli_stmt_bind_param($stmtDet, $types, ...$itemIds);
    mysqli_stmt_execute($stmtDet);
    $resDet = mysqli_stmt_get_result($stmtDet);

    while ($row = mysqli_fetch_assoc($resDet)) {
        $pid = (int) $row['pedido_item_id'];
        if (isset($items[$pid])) {
            $items[$pid]['detalles'][] = [
                'tipo' => (string) $row['tipo'],
                'nombre' => (string) $row['nombre'],
                'precio' => (float) $row['precio'],
            ];
        }
    }

    mysqli_stmt_close($stmtDet);
}

mr_json_response([
    'ok' => true,
    'pedido' => [
        'id' => (int) $pedido['id'],
        'tipo' => (string) $pedido['tipo'],
        'estado' => (string) $pedido['estado'],
        'direccion' => (string) ($pedido['direccion_texto'] ?? ''),
        'subtotal' => (float) $pedido['subtotal'],
        'costo_envio' => (float) $pedido['costo_envio'],
        'total' => (float) $pedido['total'],
        'created_at' => (string) $pedido['created_at'],
        'items' => array_values($items),
    ],
]);
