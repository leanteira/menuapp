<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);

$conn = mr_db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = 'SELECT id, nombre, descripcion, activo FROM empanada_gustos ORDER BY id DESC';
    $res = mysqli_query($conn, $sql);
    if (!$res) {
        mr_json_response(['ok' => false, 'error' => 'No se pudo cargar los gustos.'], 500);
    }

    $gustos = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $gustos[] = [
            'id' => (int) $row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'activo' => (int) $row['activo'] === 1,
        ];
    }

    mr_json_response(['ok' => true, 'gustos' => $gustos]);
}

if ($method === 'POST') {
    $payload = mr_get_json_input();
    $action = trim((string) ($payload['action'] ?? 'create'));
    $nombre = trim((string) ($payload['nombre'] ?? ''));
    $descripcion = trim((string) ($payload['descripcion'] ?? ''));
    $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;

    if ($nombre === '') {
        mr_json_response(['ok' => false, 'error' => 'Nombre requerido.'], 400);
    }

    if ($action === 'create') {
        $now = mr_now();
        $stmt = mysqli_prepare($conn, 'INSERT INTO empanada_gustos (nombre, descripcion, activo, created_at) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssis', $nombre, $descripcion, $activo, $now);
        mysqli_stmt_execute($stmt);
        $id = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true, 'id' => $id], 201);
    }

    if ($action === 'update') {
        $id = (int) ($payload['id'] ?? 0);
        if ($id <= 0) {
            mr_json_response(['ok' => false, 'error' => 'ID invalido.'], 400);
        }

        $stmt = mysqli_prepare($conn, 'UPDATE empanada_gustos SET nombre = ?, descripcion = ?, activo = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ssii', $nombre, $descripcion, $activo, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        mr_json_response(['ok' => true]);
    }

    mr_json_response(['ok' => false, 'error' => 'Accion no soportada.'], 400);
}

mr_json_response(['ok' => false, 'error' => 'Metodo no permitido.'], 405);
