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

    $stmt = mysqli_prepare($conn, 'SELECT id, nombre, orden, activo FROM categorias WHERE restaurante_id = ? ORDER BY orden ASC, id DESC');
    mysqli_stmt_bind_param($stmt, 'i', $restauranteId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    $categorias = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $categorias[] = [
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
            'orden' => (int) $row['orden'],
            'activo' => (int) $row['activo'] === 1,
        ];
    }
    mysqli_stmt_close($stmt);

    mr_json_response(['ok' => true, 'categorias' => $categorias]);
}

if ($method === 'POST') {
    $payload = mr_get_json_input();
    $action = trim((string) ($payload['action'] ?? 'create'));

    if ($action === 'create') {
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $orden = (int) ($payload['orden'] ?? 0);
        $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;

        if ($nombre === '') {
            mr_json_response(['ok' => false, 'error' => 'Nombre requerido.'], 400);
        }

        $restauranteId = mr_resolve_restaurante_id($user, (int) ($payload['restaurante_id'] ?? 0));

        if ($restauranteId <= 0) {
            mr_json_response(['ok' => false, 'error' => 'restaurante_id inválido.'], 400);
        }

        $stmt = mysqli_prepare($conn, 'INSERT INTO categorias (restaurante_id, nombre, orden, activo) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'isii', $restauranteId, $nombre, $orden, $activo);
        mysqli_stmt_execute($stmt);
        $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true, 'id' => $id], 201);
    }

    if ($action === 'update') {
        $id = (int) ($payload['id'] ?? 0);
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $orden = (int) ($payload['orden'] ?? 0);
        $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;

        if ($id <= 0 || $nombre === '') {
            mr_json_response(['ok' => false, 'error' => 'Datos inválidos para actualizar.'], 400);
        }

        $sql = 'UPDATE categorias SET nombre = ?, orden = ?, activo = ? WHERE id = ?';
        if ($user['rol'] !== 'superadmin') {
            $sql .= ' AND restaurante_id = ?';
        }

        $stmt = mysqli_prepare($conn, $sql);
        if ($user['rol'] === 'superadmin') {
            mysqli_stmt_bind_param($stmt, 'siii', $nombre, $orden, $activo, $id);
        } else {
            $restauranteId = (int) $user['restaurante_id'];
            mysqli_stmt_bind_param($stmt, 'siiii', $nombre, $orden, $activo, $id, $restauranteId);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true]);
    }

    mr_json_response(['ok' => false, 'error' => 'Acción no soportada.'], 400);
}

mr_json_response(['ok' => false, 'error' => 'Método no permitido.'], 405);
