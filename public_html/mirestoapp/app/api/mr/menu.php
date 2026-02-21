<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('GET');

$slug = trim((string) mr_request_param('slug', ''));
$restauranteId = (int) mr_request_param('restaurante_id', 0);

if ($slug === '' && $restauranteId <= 0) {
    mr_json_response([
        'ok' => false,
        'error' => 'Debes enviar slug o restaurante_id.',
    ], 400);
}

$restaurante = mr_get_restaurante($slug !== '' ? $slug : null, $restauranteId > 0 ? $restauranteId : null);
if (!$restaurante || (int) $restaurante['activo'] !== 1) {
    mr_json_response([
        'ok' => false,
        'error' => 'Restaurante no encontrado o inactivo.',
    ], 404);
}

$conn = mr_db();
$restaurantId = (int) $restaurante['id'];

$categorias = [];
$sqlCategorias = 'SELECT id, nombre, orden FROM categorias WHERE restaurante_id = ? AND activo = 1 ORDER BY orden ASC, nombre ASC';
$stmtCategorias = mysqli_prepare($conn, $sqlCategorias);
mysqli_stmt_bind_param($stmtCategorias, 'i', $restaurantId);
mysqli_stmt_execute($stmtCategorias);
$resCategorias = mysqli_stmt_get_result($stmtCategorias);

while ($cat = mysqli_fetch_assoc($resCategorias)) {
    $catId = (int) $cat['id'];

    $sqlProductos = 'SELECT id, nombre, descripcion, precio_base, imagen FROM productos WHERE restaurante_id = ? AND categoria_id = ? AND activo = 1 ORDER BY nombre ASC';
    $stmtProductos = mysqli_prepare($conn, $sqlProductos);
    mysqli_stmt_bind_param($stmtProductos, 'ii', $restaurantId, $catId);
    mysqli_stmt_execute($stmtProductos);
    $resProductos = mysqli_stmt_get_result($stmtProductos);

    $productos = [];
    while ($prod = mysqli_fetch_assoc($resProductos)) {
        $productoId = (int) $prod['id'];

        $variantes = [];
        $sqlVar = 'SELECT id, nombre, precio_adicional FROM producto_variantes WHERE producto_id = ? ORDER BY id ASC';
        $stmtVar = mysqli_prepare($conn, $sqlVar);
        mysqli_stmt_bind_param($stmtVar, 'i', $productoId);
        mysqli_stmt_execute($stmtVar);
        $resVar = mysqli_stmt_get_result($stmtVar);
        while ($var = mysqli_fetch_assoc($resVar)) {
            $variantes[] = [
                'id' => (int) $var['id'],
                'nombre' => $var['nombre'],
                'precio_adicional' => (float) $var['precio_adicional'],
            ];
        }
        mysqli_stmt_close($stmtVar);

        $modificadores = [];
        $sqlMod = 'SELECT id, nombre, precio_adicional, obligatorio FROM producto_modificadores WHERE producto_id = ? ORDER BY id ASC';
        $stmtMod = mysqli_prepare($conn, $sqlMod);
        mysqli_stmt_bind_param($stmtMod, 'i', $productoId);
        mysqli_stmt_execute($stmtMod);
        $resMod = mysqli_stmt_get_result($stmtMod);
        while ($mod = mysqli_fetch_assoc($resMod)) {
            $modificadores[] = [
                'id' => (int) $mod['id'],
                'nombre' => $mod['nombre'],
                'precio_adicional' => (float) $mod['precio_adicional'],
                'obligatorio' => (int) $mod['obligatorio'] === 1,
            ];
        }
        mysqli_stmt_close($stmtMod);

        $productos[] = [
            'id' => $productoId,
            'nombre' => $prod['nombre'],
            'descripcion' => $prod['descripcion'],
            'precio_base' => (float) $prod['precio_base'],
            'imagen' => $prod['imagen'],
            'variantes' => $variantes,
            'modificadores' => $modificadores,
        ];
    }
    mysqli_stmt_close($stmtProductos);

    $categorias[] = [
        'id' => $catId,
        'nombre' => $cat['nombre'],
        'orden' => (int) $cat['orden'],
        'productos' => $productos,
    ];
}

mysqli_stmt_close($stmtCategorias);

mr_json_response([
    'ok' => true,
    'restaurante' => [
        'id' => (int) $restaurante['id'],
        'nombre' => $restaurante['nombre'],
        'slug' => $restaurante['slug'],
        'telefono' => $restaurante['telefono'],
        'email' => $restaurante['email'],
        'direccion' => $restaurante['direccion'],
        'logo' => $restaurante['logo'],
    ],
    'categorias' => $categorias,
]);
