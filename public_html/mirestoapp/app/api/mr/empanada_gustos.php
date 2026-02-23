<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('GET');

$conn = mr_db();

$sql = 'SELECT id, nombre, descripcion FROM empanada_gustos WHERE activo = 1 ORDER BY id ASC';
$result = mysqli_query($conn, $sql);

if (!$result) {
    mr_json_response([
        'ok' => false,
        'error' => 'Error al cargar los gustos de empanadas.',
    ], 500);
}

$gustos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $gustos[] = [
        'id' => (int) $row['id'],
        'nombre' => (string) $row['nombre'],
        'descripcion' => (string) $row['descripcion'],
    ];
}

mr_json_response([
    'ok' => true,
    'gustos' => $gustos,
]);
