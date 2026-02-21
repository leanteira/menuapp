<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);
mr_require_method('GET');

$conn = mr_db();
$user = mr_user();

$days = (int) mr_request_param('days', 30);
$days = max(1, min(365, $days));

$restauranteIdFilter = mr_resolve_restaurante_id($user, (int) mr_request_param('restaurante_id', 0));
if ($restauranteIdFilter <= 0) {
    $restauranteIdFilter = null;
}

$whereBase = ' WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) ';
$typesBase = 'i';
$paramsBase = [$days];

if ($restauranteIdFilter !== null) {
    $whereBase .= ' AND p.restaurante_id = ? ';
    $typesBase .= 'i';
    $paramsBase[] = $restauranteIdFilter;
}

$stmt = mysqli_prepare(
    $conn,
    'SELECT
        COUNT(*) AS pedidos_totales,
        SUM(CASE WHEN p.estado = "cancelado" THEN 1 ELSE 0 END) AS pedidos_cancelados,
        ROUND(AVG(CASE WHEN p.estado <> "cancelado" THEN p.total END), 2) AS ticket_promedio,
        ROUND(SUM(CASE WHEN p.estado <> "cancelado" THEN p.total ELSE 0 END), 2) AS ventas_totales
     FROM pedidos p' . $whereBase
);
mysqli_stmt_bind_param($stmt, $typesBase, ...$paramsBase);
mysqli_stmt_execute($stmt);
$overviewResult = mysqli_stmt_get_result($stmt);
$overviewRow = mysqli_fetch_assoc($overviewResult) ?: [];
mysqli_stmt_close($stmt);

$pedidosTotales = (int) ($overviewRow['pedidos_totales'] ?? 0);
$pedidosCancelados = (int) ($overviewRow['pedidos_cancelados'] ?? 0);
$cancelRate = $pedidosTotales > 0 ? round(($pedidosCancelados * 100) / $pedidosTotales, 2) : 0;

$stmt = mysqli_prepare(
    $conn,
    'SELECT
        HOUR(p.created_at) AS hora,
        COUNT(*) AS pedidos,
        ROUND(SUM(CASE WHEN p.estado <> "cancelado" THEN p.total ELSE 0 END), 2) AS ventas
     FROM pedidos p' . $whereBase . '
     GROUP BY HOUR(p.created_at)
     ORDER BY hora ASC'
);
mysqli_stmt_bind_param($stmt, $typesBase, ...$paramsBase);
mysqli_stmt_execute($stmt);
$hoursResult = mysqli_stmt_get_result($stmt);

$ventasPorHora = [];
while ($row = mysqli_fetch_assoc($hoursResult)) {
    $ventasPorHora[] = [
        'hora' => str_pad((string) $row['hora'], 2, '0', STR_PAD_LEFT) . ':00',
        'pedidos' => (int) $row['pedidos'],
        'ventas' => (float) $row['ventas'],
    ];
}
mysqli_stmt_close($stmt);

$topProductsSql = 'SELECT
        pi.producto_id,
        pi.nombre_producto,
        SUM(pi.cantidad) AS unidades,
        ROUND(SUM(pi.total), 2) AS monto
     FROM pedido_items pi
     INNER JOIN pedidos p ON p.id = pi.pedido_id
     WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
       AND p.estado <> "cancelado"';
$topTypes = 'i';
$topParams = [$days];

if ($restauranteIdFilter !== null) {
    $topProductsSql .= ' AND p.restaurante_id = ?';
    $topTypes .= 'i';
    $topParams[] = $restauranteIdFilter;
}

$topProductsSql .= ' GROUP BY pi.producto_id, pi.nombre_producto ORDER BY unidades DESC, monto DESC LIMIT 5';

$stmt = mysqli_prepare($conn, $topProductsSql);
mysqli_stmt_bind_param($stmt, $topTypes, ...$topParams);
mysqli_stmt_execute($stmt);
$topResult = mysqli_stmt_get_result($stmt);

$topProductos = [];
while ($row = mysqli_fetch_assoc($topResult)) {
    $topProductos[] = [
        'producto_id' => (int) $row['producto_id'],
        'nombre' => $row['nombre_producto'],
        'unidades' => (int) $row['unidades'],
        'monto' => (float) $row['monto'],
    ];
}
mysqli_stmt_close($stmt);

$recurrentSql = 'SELECT
                COUNT(*) AS clientes_unicos,
                SUM(CASE WHEN t.total_pedidos >= 2 THEN 1 ELSE 0 END) AS clientes_recurrentes
         FROM (
             SELECT p.cliente_id, COUNT(*) AS total_pedidos
             FROM pedidos p
             WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                 AND p.cliente_id IS NOT NULL';

$recurrentTypes = 'i';
$recurrentParams = [$days];

if ($restauranteIdFilter !== null) {
    $recurrentSql .= ' AND p.restaurante_id = ?';
    $recurrentTypes .= 'i';
    $recurrentParams[] = $restauranteIdFilter;
}

$recurrentSql .= ' GROUP BY p.cliente_id
    ) t';

$stmt = mysqli_prepare($conn, $recurrentSql);
mysqli_stmt_bind_param($stmt, $recurrentTypes, ...$recurrentParams);
mysqli_stmt_execute($stmt);
$recurrentResult = mysqli_stmt_get_result($stmt);
$recurrentRow = mysqli_fetch_assoc($recurrentResult) ?: [];
mysqli_stmt_close($stmt);

$clientesUnicos = (int) ($recurrentRow['clientes_unicos'] ?? 0);
$clientesRecurrentes = (int) ($recurrentRow['clientes_recurrentes'] ?? 0);
$tasaRecurrentes = $clientesUnicos > 0 ? round(($clientesRecurrentes * 100) / $clientesUnicos, 2) : 0;

mr_json_response([
    'ok' => true,
    'filtros' => [
        'days' => $days,
        'restaurante_id' => $restauranteIdFilter,
    ],
    'overview' => [
        'pedidos_totales' => $pedidosTotales,
        'pedidos_cancelados' => $pedidosCancelados,
        'cancel_rate' => $cancelRate,
        'ticket_promedio' => (float) ($overviewRow['ticket_promedio'] ?? 0),
        'ventas_totales' => (float) ($overviewRow['ventas_totales'] ?? 0),
    ],
    'ventas_por_hora' => $ventasPorHora,
    'top_productos' => $topProductos,
    'recurrentes' => [
        'clientes_unicos' => $clientesUnicos,
        'clientes_recurrentes' => $clientesRecurrentes,
        'tasa_recurrentes' => $tasaRecurrentes,
    ],
]);
