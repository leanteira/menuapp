<?php

require_once __DIR__ . '/../../../mr_auth.php';

mr_require_auth(['superadmin', 'admin', 'operador']);
mr_require_method('POST');

$conn = mr_db();
$user = mr_user();
$payload = mr_get_json_input();

$restauranteId = mr_resolve_restaurante_id($user, (int) ($payload['restaurante_id'] ?? 0));

if ($restauranteId <= 0) {
    mr_json_response(['ok' => false, 'error' => 'restaurante_id inválido.'], 400);
}

$clienteData = isset($payload['cliente']) && is_array($payload['cliente']) ? $payload['cliente'] : [];
$items = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : [];
$tipo = isset($payload['tipo']) ? trim((string) $payload['tipo']) : 'telefono';
$tipo = in_array($tipo, ['delivery', 'retiro', 'telefono'], true) ? $tipo : 'telefono';
$metodoPago = isset($payload['metodo_pago']) ? trim((string) $payload['metodo_pago']) : 'contra_entrega';
$observaciones = isset($payload['observaciones']) ? trim((string) $payload['observaciones']) : null;
$zonaId = isset($payload['zona_id']) ? (int) $payload['zona_id'] : 0;

if (empty($items)) {
    mr_json_response(['ok' => false, 'error' => 'El pedido no tiene items.'], 400);
}

$nombreCliente = trim((string) ($clienteData['nombre'] ?? ''));
$telefonoCliente = trim((string) ($clienteData['telefono'] ?? ''));
$emailCliente = trim((string) ($clienteData['email'] ?? ''));

if ($nombreCliente === '' || $telefonoCliente === '') {
    mr_json_response(['ok' => false, 'error' => 'Nombre y teléfono son obligatorios.'], 400);
}

mysqli_begin_transaction($conn);

try {
    $clienteId = null;

    $stmtCliente = mysqli_prepare($conn, 'SELECT id FROM clientes WHERE restaurante_id = ? AND telefono = ? LIMIT 1');
    mysqli_stmt_bind_param($stmtCliente, 'is', $restauranteId, $telefonoCliente);
    mysqli_stmt_execute($stmtCliente);
    $resCliente = mysqli_stmt_get_result($stmtCliente);

    if ($row = mysqli_fetch_assoc($resCliente)) {
        $clienteId = (int) $row['id'];
        mysqli_stmt_close($stmtCliente);

        $stmtUpd = mysqli_prepare($conn, 'UPDATE clientes SET nombre = ?, email = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmtUpd, 'ssi', $nombreCliente, $emailCliente, $clienteId);
        mysqli_stmt_execute($stmtUpd);
        mysqli_stmt_close($stmtUpd);
    } else {
        mysqli_stmt_close($stmtCliente);

        $stmtIns = mysqli_prepare($conn, 'INSERT INTO clientes (restaurante_id, nombre, telefono, email, created_at) VALUES (?, ?, ?, ?, ?)');
        $now = mr_now();
        mysqli_stmt_bind_param($stmtIns, 'issss', $restauranteId, $nombreCliente, $telefonoCliente, $emailCliente, $now);
        mysqli_stmt_execute($stmtIns);
        $clienteId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmtIns);
    }

    $direccionId = null;
    $latEntrega = null;
    $lngEntrega = null;
    $direccionData = isset($payload['direccion']) && is_array($payload['direccion']) ? $payload['direccion'] : [];
    if (!empty($direccionData['direccion'])) {
        $direccion = trim((string) $direccionData['direccion']);
        $referencia = trim((string) ($direccionData['referencia'] ?? ''));
        $lat = isset($direccionData['lat']) && $direccionData['lat'] !== '' ? (float) $direccionData['lat'] : null;
        $lng = isset($direccionData['lng']) && $direccionData['lng'] !== '' ? (float) $direccionData['lng'] : null;
        $latEntrega = $lat;
        $lngEntrega = $lng;

        $stmtDir = mysqli_prepare($conn, 'INSERT INTO clientes_direcciones (cliente_id, direccion, lat, lng, referencia, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        $now = mr_now();
        mysqli_stmt_bind_param($stmtDir, 'isddss', $clienteId, $direccion, $lat, $lng, $referencia, $now);
        mysqli_stmt_execute($stmtDir);
        $direccionId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmtDir);
    }

    $subtotal = 0.0;
    $itemsFinal = [];

    foreach ($items as $itemInput) {
        $productoId = (int) ($itemInput['producto_id'] ?? 0);
        $cantidad = (int) ($itemInput['cantidad'] ?? 0);
        $varianteId = (int) ($itemInput['variante_id'] ?? 0);
        $mods = isset($itemInput['modificadores']) && is_array($itemInput['modificadores']) ? $itemInput['modificadores'] : [];

        if ($productoId <= 0 || $cantidad <= 0) {
            throw new Exception('Item inválido.');
        }

        $stmtProd = mysqli_prepare($conn, 'SELECT id, nombre, precio_base, activo FROM productos WHERE id = ? AND restaurante_id = ? LIMIT 1');
        mysqli_stmt_bind_param($stmtProd, 'ii', $productoId, $restauranteId);
        mysqli_stmt_execute($stmtProd);
        $resProd = mysqli_stmt_get_result($stmtProd);
        $prod = mysqli_fetch_assoc($resProd);
        mysqli_stmt_close($stmtProd);

        if (!$prod || (int) $prod['activo'] !== 1) {
            throw new Exception('Producto no disponible: ' . $productoId);
        }

        $precioUnitario = (float) $prod['precio_base'];
        $detalles = [];

        if ($varianteId > 0) {
            $stmtVar = mysqli_prepare($conn, 'SELECT nombre, precio_adicional FROM producto_variantes WHERE id = ? AND producto_id = ? LIMIT 1');
            mysqli_stmt_bind_param($stmtVar, 'ii', $varianteId, $productoId);
            mysqli_stmt_execute($stmtVar);
            $resVar = mysqli_stmt_get_result($stmtVar);
            $var = mysqli_fetch_assoc($resVar);
            mysqli_stmt_close($stmtVar);

            if (!$var) {
                throw new Exception('Variante inválida.');
            }

            $plus = (float) $var['precio_adicional'];
            $precioUnitario += $plus;
            $detalles[] = ['tipo' => 'variante', 'nombre' => $var['nombre'], 'precio' => $plus];
        }

        foreach ($mods as $modIdRaw) {
            $modId = (int) $modIdRaw;
            if ($modId <= 0) {
                continue;
            }

            $stmtMod = mysqli_prepare($conn, 'SELECT nombre, precio_adicional FROM producto_modificadores WHERE id = ? AND producto_id = ? LIMIT 1');
            mysqli_stmt_bind_param($stmtMod, 'ii', $modId, $productoId);
            mysqli_stmt_execute($stmtMod);
            $resMod = mysqli_stmt_get_result($stmtMod);
            $mod = mysqli_fetch_assoc($resMod);
            mysqli_stmt_close($stmtMod);

            if (!$mod) {
                throw new Exception('Modificador inválido.');
            }

            $plus = (float) $mod['precio_adicional'];
            $precioUnitario += $plus;
            $detalles[] = ['tipo' => 'modificador', 'nombre' => $mod['nombre'], 'precio' => $plus];
        }

        $itemTotal = $precioUnitario * $cantidad;
        $subtotal += $itemTotal;

        $itemsFinal[] = [
            'producto_id' => $productoId,
            'nombre_producto' => $prod['nombre'],
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'total' => $itemTotal,
            'detalles' => $detalles,
        ];
    }

    $zona = null;
    $costoEnvio = 0.0;
    if ($tipo === 'delivery') {
        $zona = mr_resolve_delivery_zone($restauranteId, $zonaId, $latEntrega, $lngEntrega);
        if (!$zona) {
            throw new Exception('No se pudo determinar una zona de envío válida para la dirección indicada.');
        }

        $costoEnvio = (float) $zona['costo_envio'];
        $pedidoMinimoZona = (float) $zona['pedido_minimo'];
        if ($pedidoMinimoZona > 0 && $subtotal < $pedidoMinimoZona) {
            throw new Exception('No alcanzás el pedido mínimo de la zona seleccionada ($' . number_format($pedidoMinimoZona, 2, '.', '') . ').');
        }

        $zonaId = (int) $zona['id'];
    }

    $total = $subtotal + $costoEnvio;
    $estado = 'nuevo';
    $createdAt = mr_now();

    $stmtPedido = mysqli_prepare($conn, 'INSERT INTO pedidos (restaurante_id, cliente_id, direccion_id, zona_id, tipo, estado, subtotal, costo_envio, total, observaciones, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $zonaIdParam = $zonaId > 0 ? $zonaId : null;
    mysqli_stmt_bind_param($stmtPedido, 'iiiissdddss', $restauranteId, $clienteId, $direccionId, $zonaIdParam, $tipo, $estado, $subtotal, $costoEnvio, $total, $observaciones, $createdAt);
    mysqli_stmt_execute($stmtPedido);
    $pedidoId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmtPedido);

    $stmtItem = mysqli_prepare($conn, 'INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?, ?)');
    $stmtDet = mysqli_prepare($conn, 'INSERT INTO pedido_item_detalles (pedido_item_id, tipo, nombre, precio) VALUES (?, ?, ?, ?)');

    foreach ($itemsFinal as $item) {
        mysqli_stmt_bind_param($stmtItem, 'iisidd', $pedidoId, $item['producto_id'], $item['nombre_producto'], $item['cantidad'], $item['precio_unitario'], $item['total']);
        mysqli_stmt_execute($stmtItem);
        $pedidoItemId = (int) mysqli_insert_id($conn);

        foreach ($item['detalles'] as $detalle) {
            mysqli_stmt_bind_param($stmtDet, 'issd', $pedidoItemId, $detalle['tipo'], $detalle['nombre'], $detalle['precio']);
            mysqli_stmt_execute($stmtDet);
        }
    }

    mysqli_stmt_close($stmtDet);
    mysqli_stmt_close($stmtItem);

    $stmtHist = mysqli_prepare($conn, 'INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at) VALUES (?, ?, ?)');
    mysqli_stmt_bind_param($stmtHist, 'iss', $pedidoId, $estado, $createdAt);
    mysqli_stmt_execute($stmtHist);
    mysqli_stmt_close($stmtHist);

    $estadoPago = 'pendiente';
    $referencia = null;
    $stmtPago = mysqli_prepare($conn, 'INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia_externa, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmtPago, 'issdss', $pedidoId, $metodoPago, $estadoPago, $total, $referencia, $createdAt);
    mysqli_stmt_execute($stmtPago);
    mysqli_stmt_close($stmtPago);

    mysqli_commit($conn);

    mr_json_response([
        'ok' => true,
        'pedido_id' => $pedidoId,
        'estado' => $estado,
        'total' => round($total, 2),
    ], 201);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    mr_json_response(['ok' => false, 'error' => $e->getMessage()], 422);
}
