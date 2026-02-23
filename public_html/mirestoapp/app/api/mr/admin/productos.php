<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);

$conn = mr_db();
$user = mr_user();
$method = $_SERVER['REQUEST_METHOD'];

$hasCodigoColumn = false;
$resCodigoCol = mysqli_query($conn, "SHOW COLUMNS FROM productos LIKE 'codigo'");
if ($resCodigoCol) {
    $hasCodigoColumn = mysqli_num_rows($resCodigoCol) > 0;
    mysqli_free_result($resCodigoCol);
}

$hasComboGustosColumn = false;
$resComboCol = mysqli_query($conn, "SHOW COLUMNS FROM productos LIKE 'es_combo_gustos'");
if ($resComboCol) {
    $hasComboGustosColumn = mysqli_num_rows($resComboCol) > 0;
    mysqli_free_result($resComboCol);
}

if ($method === 'GET') {
    $restauranteId = mr_resolve_restaurante_id($user, (int) mr_request_param('restaurante_id', 0));

    if ($restauranteId <= 0) {
        mr_json_response(['ok' => false, 'error' => 'restaurante_id requerido.'], 400);
    }

    $codigoSelect = $hasCodigoColumn ? 'p.codigo' : 'NULL';
    $comboSelect = $hasComboGustosColumn ? 'p.es_combo_gustos' : '0';

    $sql = 'SELECT p.id, ' . $codigoSelect . ' AS codigo, ' . $comboSelect . ' AS es_combo_gustos, p.categoria_id, p.nombre, p.descripcion, p.precio_base, p.activo, c.nombre AS categoria_nombre
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
            'codigo' => $row['codigo'],
            'es_combo_gustos' => (int) $row['es_combo_gustos'] === 1,
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
        $codigo = strtoupper(trim((string) ($payload['codigo'] ?? '')));
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $descripcion = trim((string) ($payload['descripcion'] ?? ''));
        $precioBase = (float) ($payload['precio_base'] ?? 0);
        $categoriaId = (int) ($payload['categoria_id'] ?? 0);
        $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;
        $esComboGustos = isset($payload['es_combo_gustos']) ? (int) ((bool) $payload['es_combo_gustos']) : 0;

        $codigo = preg_replace('/[^A-Z0-9\-]/', '', $codigo);

        if ($nombre === '' || $precioBase < 0) {
            mr_json_response(['ok' => false, 'error' => 'Datos inválidos de producto.'], 400);
        }

        $restauranteId = mr_resolve_restaurante_id($user, (int) ($payload['restaurante_id'] ?? 0));

        if ($restauranteId <= 0) {
            mr_json_response(['ok' => false, 'error' => 'restaurante_id inválido.'], 400);
        }

        if ($hasCodigoColumn && $codigo !== '') {
            $stmtChk = mysqli_prepare($conn, 'SELECT id FROM productos WHERE restaurante_id = ? AND codigo = ? LIMIT 1');
            mysqli_stmt_bind_param($stmtChk, 'is', $restauranteId, $codigo);
            mysqli_stmt_execute($stmtChk);
            $resChk = mysqli_stmt_get_result($stmtChk);
            $exists = mysqli_fetch_assoc($resChk);
            mysqli_stmt_close($stmtChk);

            if ($exists) {
                mr_json_response(['ok' => false, 'error' => 'El código de producto ya existe en este restaurante.'], 409);
            }
        }

        $now = mr_now();
        $categoriaParam = $categoriaId > 0 ? $categoriaId : null;

        if ($hasCodigoColumn && $hasComboGustosColumn) {
            $sql = 'INSERT INTO productos (restaurante_id, codigo, es_combo_gustos, categoria_id, nombre, descripcion, precio_base, activo, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isiissdis', $restauranteId, $codigo, $esComboGustos, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $now);
            mysqli_stmt_execute($stmt);
            $id = (int) mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            if ($codigo === '') {
                $codigoGenerado = 'P' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
                $stmtGen = mysqli_prepare($conn, 'UPDATE productos SET codigo = ? WHERE id = ?');
                mysqli_stmt_bind_param($stmtGen, 'si', $codigoGenerado, $id);
                mysqli_stmt_execute($stmtGen);
                mysqli_stmt_close($stmtGen);
            }
        } else if ($hasCodigoColumn) {
            $sql = 'INSERT INTO productos (restaurante_id, codigo, categoria_id, nombre, descripcion, precio_base, activo, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isissdis', $restauranteId, $codigo, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $now);
            mysqli_stmt_execute($stmt);
            $id = (int) mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            if ($codigo === '') {
                $codigoGenerado = 'P' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
                $stmtGen = mysqli_prepare($conn, 'UPDATE productos SET codigo = ? WHERE id = ?');
                mysqli_stmt_bind_param($stmtGen, 'si', $codigoGenerado, $id);
                mysqli_stmt_execute($stmtGen);
                mysqli_stmt_close($stmtGen);
            }
        } else if ($hasComboGustosColumn) {
            $sql = 'INSERT INTO productos (restaurante_id, es_combo_gustos, categoria_id, nombre, descripcion, precio_base, activo, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'iiissdis', $restauranteId, $esComboGustos, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $now);
            mysqli_stmt_execute($stmt);
            $id = (int) mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        } else {
            $sql = 'INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?)';
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'iissdis', $restauranteId, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $now);
            mysqli_stmt_execute($stmt);
            $id = (int) mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        }

        mr_json_response(['ok' => true, 'id' => $id], 201);
    }

    if ($action === 'update') {
        $id = (int) ($payload['id'] ?? 0);
        $codigo = strtoupper(trim((string) ($payload['codigo'] ?? '')));
        $nombre = trim((string) ($payload['nombre'] ?? ''));
        $descripcion = trim((string) ($payload['descripcion'] ?? ''));
        $precioBase = (float) ($payload['precio_base'] ?? 0);
        $categoriaId = (int) ($payload['categoria_id'] ?? 0);
        $activo = isset($payload['activo']) ? (int) ((bool) $payload['activo']) : 1;
        $esComboGustos = isset($payload['es_combo_gustos']) ? (int) ((bool) $payload['es_combo_gustos']) : 0;

        $codigo = preg_replace('/[^A-Z0-9\-]/', '', $codigo);

        if ($id <= 0 || $nombre === '' || $precioBase < 0) {
            mr_json_response(['ok' => false, 'error' => 'Datos inválidos para actualizar.'], 400);
        }

        $restauranteIdForCode = (int) ($user['rol'] === 'superadmin'
            ? mr_resolve_restaurante_id($user, (int) ($payload['restaurante_id'] ?? 0))
            : ($user['restaurante_id'] ?? 0));

        if ($hasCodigoColumn && $codigo !== '' && $restauranteIdForCode > 0) {
            $stmtChk = mysqli_prepare($conn, 'SELECT id FROM productos WHERE restaurante_id = ? AND codigo = ? AND id <> ? LIMIT 1');
            mysqli_stmt_bind_param($stmtChk, 'isi', $restauranteIdForCode, $codigo, $id);
            mysqli_stmt_execute($stmtChk);
            $resChk = mysqli_stmt_get_result($stmtChk);
            $exists = mysqli_fetch_assoc($resChk);
            mysqli_stmt_close($stmtChk);

            if ($exists) {
                mr_json_response(['ok' => false, 'error' => 'El código de producto ya existe en este restaurante.'], 409);
            }
        }

        if ($hasCodigoColumn && $hasComboGustosColumn) {
            $sql = 'UPDATE productos SET codigo = ?, es_combo_gustos = ?, categoria_id = ?, nombre = ?, descripcion = ?, precio_base = ?, activo = ? WHERE id = ?';
        } else if ($hasCodigoColumn) {
            $sql = 'UPDATE productos SET codigo = ?, categoria_id = ?, nombre = ?, descripcion = ?, precio_base = ?, activo = ? WHERE id = ?';
        } else if ($hasComboGustosColumn) {
            $sql = 'UPDATE productos SET es_combo_gustos = ?, categoria_id = ?, nombre = ?, descripcion = ?, precio_base = ?, activo = ? WHERE id = ?';
        } else {
            $sql = 'UPDATE productos SET categoria_id = ?, nombre = ?, descripcion = ?, precio_base = ?, activo = ? WHERE id = ?';
        }
        if ($user['rol'] !== 'superadmin') {
            $sql .= ' AND restaurante_id = ?';
        }

        $stmt = mysqli_prepare($conn, $sql);
        $categoriaParam = $categoriaId > 0 ? $categoriaId : null;
        if ($user['rol'] === 'superadmin' && $hasCodigoColumn && $hasComboGustosColumn) {
            mysqli_stmt_bind_param($stmt, 'siissdii', $codigo, $esComboGustos, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id);
        } else if ($user['rol'] === 'superadmin' && $hasCodigoColumn) {
            mysqli_stmt_bind_param($stmt, 'sissdii', $codigo, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id);
        } else if ($user['rol'] === 'superadmin' && $hasComboGustosColumn) {
            mysqli_stmt_bind_param($stmt, 'iissdii', $esComboGustos, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id);
        } else if ($user['rol'] === 'superadmin') {
            mysqli_stmt_bind_param($stmt, 'issdii', $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id);
        } else if ($hasCodigoColumn && $hasComboGustosColumn) {
            $restauranteId = (int) $user['restaurante_id'];
            mysqli_stmt_bind_param($stmt, 'siissdiii', $codigo, $esComboGustos, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id, $restauranteId);
        } else if ($hasCodigoColumn) {
            $restauranteId = (int) $user['restaurante_id'];
            mysqli_stmt_bind_param($stmt, 'sissdiii', $codigo, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id, $restauranteId);
        } else if ($hasComboGustosColumn) {
            $restauranteId = (int) $user['restaurante_id'];
            mysqli_stmt_bind_param($stmt, 'iissdiii', $esComboGustos, $categoriaParam, $nombre, $descripcion, $precioBase, $activo, $id, $restauranteId);
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
