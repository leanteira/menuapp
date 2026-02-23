<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);
mr_require_method('POST');

if (!isset($_FILES['imagen'])) {
	mr_json_response(['ok' => false, 'error' => 'No se recibió archivo de imagen.'], 400);
}

$productoId = (int) ($_POST['producto_id'] ?? 0);
if ($productoId <= 0) {
	mr_json_response(['ok' => false, 'error' => 'producto_id inválido.'], 400);
}

$file = $_FILES['imagen'];
if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
	mr_json_response(['ok' => false, 'error' => 'Error al subir la imagen.'], 400);
}

$size = (int) ($file['size'] ?? 0);
if ($size <= 0 || $size > 4 * 1024 * 1024) {
	mr_json_response(['ok' => false, 'error' => 'La imagen debe pesar hasta 4MB.'], 400);
}

$tmpPath = (string) ($file['tmp_name'] ?? '');
if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
	mr_json_response(['ok' => false, 'error' => 'Archivo de imagen inválido.'], 400);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = $finfo ? (string) finfo_file($finfo, $tmpPath) : '';
if ($finfo) {
	finfo_close($finfo);
}

$extByMime = [
	'image/jpeg' => 'jpg',
	'image/png' => 'png',
	'image/webp' => 'webp',
	'image/gif' => 'gif',
];

if (!isset($extByMime[$mime])) {
	mr_json_response(['ok' => false, 'error' => 'Formato no permitido. Usá JPG, PNG, WEBP o GIF.'], 400);
}

$conn = mr_db();
$user = mr_user();

$sqlProducto = 'SELECT id, imagen FROM productos WHERE id = ?';
if (($user['rol'] ?? '') !== 'superadmin') {
	$sqlProducto .= ' AND restaurante_id = ?';
}
$sqlProducto .= ' LIMIT 1';

$stmtProducto = mysqli_prepare($conn, $sqlProducto);
if (($user['rol'] ?? '') === 'superadmin') {
	mysqli_stmt_bind_param($stmtProducto, 'i', $productoId);
} else {
	$restauranteId = (int) ($user['restaurante_id'] ?? 0);
	mysqli_stmt_bind_param($stmtProducto, 'ii', $productoId, $restauranteId);
}
mysqli_stmt_execute($stmtProducto);
$resProducto = mysqli_stmt_get_result($stmtProducto);
$producto = mysqli_fetch_assoc($resProducto);
mysqli_stmt_close($stmtProducto);

if (!$producto) {
	mr_json_response(['ok' => false, 'error' => 'Producto no encontrado o sin permisos.'], 404);
}

$uploadDir = realpath(__DIR__ . '/../../../../') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'productos';
if ($uploadDir === false) {
	mr_json_response(['ok' => false, 'error' => 'No se pudo resolver ruta de uploads.'], 500);
}

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
	mr_json_response(['ok' => false, 'error' => 'No se pudo crear carpeta de imágenes.'], 500);
}

$ext = $extByMime[$mime];
$safeName = 'prod_' . $productoId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destPath = $uploadDir . DIRECTORY_SEPARATOR . $safeName;

if (!move_uploaded_file($tmpPath, $destPath)) {
	mr_json_response(['ok' => false, 'error' => 'No se pudo guardar la imagen.'], 500);
}

$relativePath = 'assets/images/productos/' . $safeName;

$stmtUpd = mysqli_prepare($conn, 'UPDATE productos SET imagen = ? WHERE id = ?');
mysqli_stmt_bind_param($stmtUpd, 'si', $relativePath, $productoId);
mysqli_stmt_execute($stmtUpd);
mysqli_stmt_close($stmtUpd);

$oldImage = trim((string) ($producto['imagen'] ?? ''));
if ($oldImage !== '' && str_starts_with($oldImage, 'assets/images/productos/')) {
	$oldPath = realpath(__DIR__ . '/../../../../') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldImage);
	if (is_file($oldPath) && $oldPath !== $destPath) {
		@unlink($oldPath);
	}
}

mr_json_response([
	'ok' => true,
	'producto_id' => $productoId,
	'imagen' => $relativePath,
]);

