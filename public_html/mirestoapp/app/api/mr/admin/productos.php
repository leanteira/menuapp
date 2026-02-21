<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);

$conn = mr_db();
$user = mr_user();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $restauranteId = mr_resolve_restaurante_id($user, (int) mr_request_param('restaurante_id', 0));

    if ($restauranteId <= 0) {
        mr_json_response(['ok' => false, 'error' => 'restaurante_id requerido.'], 400);
    }

    $sql = 'SELECT p.id, p.categoria_id, p.nombre, p.descripcion, p.precio_base, p.activo, c.nombre AS categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.restaurante_id = ?
            ORDER BY p.id DESC';

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $restauranteId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $productos = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $productos[] = [
            'id' => (int) $row['id'],
            'categoria_id' => (int) $row['categoria_id'],
            'categoria_nombre' => $row['categoria_nombre'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio_base' => (float) $row['precio_base'],
            'activo' => (int) $row['activo'] === 1,
        ];
    }

    mysqli_stmt_close($stmt);
    mr_json_response(['ok' => true, 'productos' => $productos]);
}

if ($method === 'POST') {
    $payload = mr_get_json_input();
    $action = trim((string) ($payload['action'] ?? 'create'));

    if ($action === 'create') {
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $descripcion = trim((string) ($payload['descripcion'] ?? ''));
        $precioBase = (float) ($payload['precio_base'] ?? 0);
        $categoriaId = (int) ($payload['categoria_id'] ?? 0);
        $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;

        if ($nombre === '' || $precioBase < 0) {
            mr_json_response(['ok' => false, 'error' => 'Datos inválidos de producto.'], 400);
        }

        $restauranteId = mr_resolve_restaurante_id($user, (int) ($payload['restaurante_id'] ?? 0));

        if ($restauranteId <= 0) {
            mr_json_response(['ok' => false, 'error' => 'restaurante_id inválido.'], 400);
        }

        $sql = 'INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = mysqli_prepare($conn, $sql);
        $now = mr_now();
        $categoriaParam = $categoriaId > 0 ? $categoriaId : null;
        mysqli_stmt_bind_param($stmt, 'iissdis', $restauranteId, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $now);
        mysqli_stmt_execute($stmt);
        $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true, 'id' => $id], 201);
    }

    if ($action === 'update') {
        $id = (int) ($payload['id'] ?? 0);
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $descripcion = trim((string) ($payload['descripcion'] ?? ''));
        $precioBase = (float) ($payload['precio_base'] ?? 0);
        $categoriaId = (int) ($payload['categoria_id'] ?? 0);
        $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;

        if ($id <= 0 || $nombre === '' || $precioBase < 0) {
            mr_json_response(['ok' => false, 'error' => 'Datos inválidos para actualizar.'], 400);
        }

        $sql = 'UPDATE productos SET categoria_id = ?, nombre = ?, descripcion = ?, precio_base = ?, activo = ? WHERE id = ?';
        if ($user['rol'] !== 'superadmin') {
            $sql .= ' AND restaurante_id = ?';
        }

        $stmt = mysqli_prepare($conn, $sql);
        $categoriaParam = $categoriaId > 0 ? $categoriaId : null;
        if ($user['rol'] === 'superadmin') {
            mysqli_stmt_bind_param($stmt, 'issdii', $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id);
        } else {
            $restauranteId = (int) $user['restaurante_id'];
            mysqli_stmt_bind_param($stmt, 'issdiii', $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id, $restauranteId);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true]);
    }

    mr_json_response(['ok' => false, 'error' => 'Acción no soportada.'], 400);
}

mr_json_response(['ok' => false, 'error' => 'Método no permitido.'], 405);
