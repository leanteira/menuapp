<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);
$conn = mr_db();
$user = mr_user();

$method = $_SERVER['REQUEST_METHOD'];

function mr_validate_producto_scope_mod($conn, $user, $productoId)
{
    $sql = 'SELECT id, restaurante_id FROM productos WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $productoId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $prod = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$prod) {
        mr_json_response(['ok' => false, 'error' => 'Producto no encontrado.'], 404);
    }

    if ($user['rol'] !== 'superadmin' && (int) $prod['restaurante_id'] !== (int) $user['restaurante_id']) {
        mr_json_response(['ok' => false, 'error' => 'No autorizado para este producto.'], 403);
    }
}

if ($method === 'GET') {
    $productoId = (int) mr_request_param('producto_id', 0);
    if ($productoId <= 0) {
        mr_json_response(['ok' => false, 'error' => 'producto_id requerido.'], 400);
    }

    mr_validate_producto_scope_mod($conn, $user, $productoId);

    $stmt = mysqli_prepare($conn, 'SELECT id, nombre, precio_adicional, obligatorio FROM producto_modificadores WHERE producto_id = ? ORDER BY id DESC');
    mysqli_stmt_bind_param($stmt, 'i', $productoId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $modificadores = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $modificadores[] = [
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
            'precio_adicional' => (float) $row['precio_adicional'],
            'obligatorio' => (int) $row['obligatorio'] === 1,
        ];
    }

    mysqli_stmt_close($stmt);
    mr_json_response(['ok' => true, 'modificadores' => $modificadores]);
}

if ($method === 'POST') {
    $payload = mr_get_json_input();
    $action = trim((string) ($payload['action'] ?? 'create'));

    if ($action === 'create') {
        $productoId = (int) ($payload['producto_id'] ?? 0);
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $precioAdicional = (float) ($payload['precio_adicional'] ?? 0);
        $obligatorio = isset($payload['obligatorio']) ? (int) ((bool) $payload['obligatorio']) : 0;

        if ($productoId <= 0 || $nombre === '') {
            mr_json_response(['ok' => false, 'error' => 'Datos inválidos.'], 400);
        }

        mr_validate_producto_scope_mod($conn, $user, $productoId);

        $stmt = mysqli_prepare($conn, 'INSERT INTO producto_modificadores (producto_id, nombre, precio_adicional, obligatorio) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'isdi', $productoId, $nombre, $precioAdicional, $obligatorio);
        mysqli_stmt_execute($stmt);
        $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true, 'id' => $id], 201);
    }

    if ($action === 'update') {
        $id = (int) ($payload['id'] ?? 0);
        $productoId = (int) ($payload['producto_id'] ?? 0);
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $precioAdicional = (float) ($payload['precio_adicional'] ?? 0);
        $obligatorio = isset($payload['obligatorio']) ? (int) ((bool) $payload['obligatorio']) : 0;

        if ($id <= 0 || $productoId <= 0 || $nombre === '') {
            mr_json_response(['ok' => false, 'error' => 'Datos inválidos para actualizar.'], 400);
        }

        mr_validate_producto_scope_mod($conn, $user, $productoId);

        $stmt = mysqli_prepare($conn, 'UPDATE producto_modificadores SET nombre = ?, precio_adicional = ?, obligatorio = ? WHERE id = ? AND producto_id = ?');
        mysqli_stmt_bind_param($stmt, 'sdiii', $nombre, $precioAdicional, $obligatorio, $id, $productoId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true]);
    }

    mr_json_response(['ok' => false, 'error' => 'Acción no soportada.'], 400);
}

mr_json_response(['ok' => false, 'error' => 'Método no permitido.'], 405);
