<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador', 'repartidor']);
mr_require_method('GET');

$conn = mr_db();
$user = mr_user();

$estado = trim((string) mr_request_param('estado', ''));
$soloHoy = (int) mr_request_param('hoy', 0) === 1;
$restauranteFiltro = (int) mr_request_param('restaurante_id', 0);
$limit = (int) mr_request_param('limit', 50);
$limit = max(1, min(200, $limit));

$sql = "SELECT
            p.id,
            p.restaurante_id,
            p.cliente_id,
            p.tipo,
            p.estado,
            p.subtotal,
            p.costo_envio,
            p.total,
            p.observaciones,
            p.created_at,
            c.nombre AS cliente_nombre,
            c.telefono AS cliente_telefono,
            r.nombre AS restaurante_nombre,
            pa.estado AS pago_estado,
            pa.metodo AS pago_metodo
        FROM pedidos p
        LEFT JOIN clientes c ON c.id = p.cliente_id
        LEFT JOIN restaurantes r ON r.id = p.restaurante_id
        LEFT JOIN pagos pa ON pa.id = (
            SELECT p2.id
            FROM pagos p2
            WHERE p2.pedido_id = p.id
            ORDER BY p2.id DESC
            LIMIT 1
        )
        WHERE 1=1";

$types = '';
$params = [];

if ($user['rol'] !== 'superadmin') {
    $sql .= ' AND p.restaurante_id = ?';
    $types .= 'i';
    $params[] = $user['restaurante_id'];
} elseif ($restauranteFiltro > 0) {
    $sql .= ' AND p.restaurante_id = ?';
    $types .= 'i';
    $params[] = $restauranteFiltro;
}

if ($estado !== '') {
    $sql .= ' AND p.estado = ?';
    $types .= 's';
    $params[] = $estado;
}

if ($soloHoy) {
    $sql .= ' AND DATE(p.created_at) = CURDATE()';
}

$sql .= ' ORDER BY p.id DESC LIMIT ?';
$types .= 'i';
$params[] = $limit;

$stmt = mysqli_prepare($conn, $sql);
if ($types !== '') {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$pedidos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pedidos[] = [
        'id' => (int) $row['id'],
        'restaurante_id' => (int) $row['restaurante_id'],
        'restaurante_nombre' => $row['restaurante_nombre'],
        'cliente' => [
            'id' => (int) $row['cliente_id'],
            'nombre' => $row['cliente_nombre'],
            'telefono' => $row['cliente_telefono'],
        ],
        'tipo' => $row['tipo'],
        'estado' => $row['estado'],
        'subtotal' => (float) $row['subtotal'],
        'costo_envio' => (float) $row['costo_envio'],
        'total' => (float) $row['total'],
        'observaciones' => $row['observaciones'],
        'created_at' => $row['created_at'],
        'pago' => [
            'estado' => $row['pago_estado'],
            'metodo' => $row['pago_metodo'],
        ],
    ];
}

mysqli_stmt_close($stmt);

mr_json_response([
    'ok' => true,
    'pedidos' => $pedidos,
]);
