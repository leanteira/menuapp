<?php

require_once __DIR__ . '/../../mr_bootstrap.php';

mr_require_method('POST');

$payload = mr_get_json_input();

$slug = isset($payload['slug']) ? trim((string) $payload['slug']) : '';
$restauranteId = isset($payload['restaurante_id']) ? (int) $payload['restaurante_id'] : 0;
$tipo = isset($payload['tipo']) ? trim((string) $payload['tipo']) : 'delivery';
$tipo = in_array($tipo, ['delivery', 'retiro', 'telefono'], true) ? $tipo : 'delivery';

$clienteData = isset($payload['cliente']) && is_array($payload['cliente']) ? $payload['cliente'] : [];
$direccionData = isset($payload['direccion']) && is_array($payload['direccion']) ? $payload['direccion'] : [];
$items = isset($payload['items']) && is_array($payload['items']) ? $payload['items'] : [];
$zonaId = isset($payload['zona_id']) ? (int) $payload['zona_id'] : 0;
$metodoPago = isset($payload['metodo_pago']) ? trim((string) $payload['metodo_pago']) : 'contra_entrega';
$observaciones = isset($payload['observaciones']) ? trim((string) $payload['observaciones']) : null;

$latEntrega = isset($direccionData['lat']) && $direccionData['lat'] !== '' ? (float) $direccionData['lat'] : null;
$lngEntrega = isset($direccionData['lng']) && $direccionData['lng'] !== '' ? (float) $direccionData['lng'] : null;

if (empty($items)) {
    mr_json_response(['ok' => false, 'error' => 'El pedido no tiene items.'], 400);
}

$restaurante = mr_get_restaurante($slug !== '' ? $slug : null, $restauranteId > 0 ? $restauranteId : null);
if (!$restaurante || (int) $restaurante['activo'] !== 1) {
    mr_json_response(['ok' => false, 'error' => 'Restaurante no disponible.'], 404);
}
$restauranteId = (int) $restaurante['id'];

$nombreCliente = isset($clienteData['nombre']) ? trim((string) $clienteData['nombre']) : '';
$telefonoCliente = isset($clienteData['telefono']) ? trim((string) $clienteData['telefono']) : '';
$emailCliente = isset($clienteData['email']) ? trim((string) $clienteData['email']) : '';

if ($nombreCliente === '' || $telefonoCliente === '') {
    mr_json_response(['ok' => false, 'error' => 'Datos de cliente incompletos (nombre y teléfono).'], 400);
}

if ($tipo === 'delivery' && empty($direccionData['direccion'])) {
    mr_json_response(['ok' => false, 'error' => 'Para delivery debes indicar dirección.'], 400);
}

$conn = mr_db();
mysqli_begin_transaction($conn);

try {
    $clienteId = null;

    $sqlCliente = 'SELECT id FROM clientes WHERE restaurante_id = ? AND telefono = ? LIMIT 1';
    $stmtCliente = mysqli_prepare($conn, $sqlCliente);
    mysqli_stmt_bind_param($stmtCliente, 'is', $restauranteId, $telefonoCliente);
    mysqli_stmt_execute($stmtCliente);
    $resCliente = mysqli_stmt_get_result($stmtCliente);

    if ($rowCliente = mysqli_fetch_assoc($resCliente)) {
        $clienteId = (int) $rowCliente['id'];
        mysqli_stmt_close($stmtCliente);

        $sqlUpdCliente = 'UPDATE clientes SET nombre = ?, email = ? WHERE id = ?';
        $stmtUpdCliente = mysqli_prepare($conn, $sqlUpdCliente);
        mysqli_stmt_bind_param($stmtUpdCliente, 'ssi', $nombreCliente, $emailCliente, $clienteId);
        mysqli_stmt_execute($stmtUpdCliente);
        mysqli_stmt_close($stmtUpdCliente);
    } else {
        mysqli_stmt_close($stmtCliente);

        $sqlInsCliente = 'INSERT INTO clientes (restaurante_id, nombre, telefono, email, created_at) VALUES (?, ?, ?, ?, ?)';
        $stmtInsCliente = mysqli_prepare($conn, $sqlInsCliente);
        $now = mr_now();
        mysqli_stmt_bind_param($stmtInsCliente, 'issss', $restauranteId, $nombreCliente, $telefonoCliente, $emailCliente, $now);
        mysqli_stmt_execute($stmtInsCliente);
        $clienteId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmtInsCliente);
    }

    $direccionId = null;
    if (!empty($direccionData['direccion'])) {
        $direccion = trim((string) $direccionData['direccion']);
        $lat = isset($direccionData['lat']) ? (float) $direccionData['lat'] : null;
        $lng = isset($direccionData['lng']) ? (float) $direccionData['lng'] : null;
        $referencia = isset($direccionData['referencia']) ? trim((string) $direccionData['referencia']) : null;

        $sqlDir = 'INSERT INTO clientes_direcciones (cliente_id, direccion, lat, lng, referencia, created_at) VALUES (?, ?, ?, ?, ?, ?)';
        $stmtDir = mysqli_prepare($conn, $sqlDir);
        $now = mr_now();
        mysqli_stmt_bind_param($stmtDir, 'isddss', $clienteId, $direccion, $lat, $lng, $referencia, $now);
        mysqli_stmt_execute($stmtDir);
        $direccionId = (int) mysqli_insert_id($conn);
        mysqli_stmt_close($stmtDir);
    }

    $zona = null;
    $costoEnvio = 0.0;

    $subtotal = 0.0;
    $itemsFinal = [];

    foreach ($items as $itemInput) {
        $productoId = isset($itemInput['producto_id']) ? (int) $itemInput['producto_id'] : 0;
        $cantidad = isset($itemInput['cantidad']) ? (int) $itemInput['cantidad'] : 0;
        $varianteId = isset($itemInput['variante_id']) ? (int) $itemInput['variante_id'] : 0;
        $modificadores = isset($itemInput['modificadores']) && is_array($itemInput['modificadores']) ? $itemInput['modificadores'] : [];

        if ($productoId <= 0 || $cantidad <= 0) {
            throw new Exception('Item de pedido inválido.');
        }

        $sqlProd = 'SELECT id, nombre, precio_base, activo FROM productos WHERE id = ? AND restaurante_id = ? LIMIT 1';
        $stmtProd = mysqli_prepare($conn, $sqlProd);
        mysqli_stmt_bind_param($stmtProd, 'ii', $productoId, $restauranteId);
        mysqli_stmt_execute($stmtProd);
        $resProd = mysqli_stmt_get_result($stmtProd);
        $producto = mysqli_fetch_assoc($resProd);
        mysqli_stmt_close($stmtProd);

        if (!$producto || (int) $producto['activo'] !== 1) {
            throw new Exception('Producto no disponible: ID ' . $productoId);
        }

        $precioUnitario = (float) $producto['precio_base'];
        $detalles = [];

        if ($varianteId > 0) {
            $sqlVar = 'SELECT id, nombre, precio_adicional FROM producto_variantes WHERE id = ? AND producto_id = ? LIMIT 1';
            $stmtVar = mysqli_prepare($conn, $sqlVar);
            mysqli_stmt_bind_param($stmtVar, 'ii', $varianteId, $productoId);
            mysqli_stmt_execute($stmtVar);
            $resVar = mysqli_stmt_get_result($stmtVar);
            $var = mysqli_fetch_assoc($resVar);
            mysqli_stmt_close($stmtVar);

            if (!$var) {
                throw new Exception('Variante inválida para producto ID ' . $productoId);
            }

            $precioUnitario += (float) $var['precio_adicional'];
            $detalles[] = [
                'tipo' => 'variante',
                'nombre' => $var['nombre'],
                'precio' => (float) $var['precio_adicional'],
            ];
        }

        foreach ($modificadores as $modId) {
            $modId = (int) $modId;
            if ($modId <= 0) {
                continue;
            }

            $sqlMod = 'SELECT id, nombre, precio_adicional FROM producto_modificadores WHERE id = ? AND producto_id = ? LIMIT 1';
            $stmtMod = mysqli_prepare($conn, $sqlMod);
            mysqli_stmt_bind_param($stmtMod, 'ii', $modId, $productoId);
            mysqli_stmt_execute($stmtMod);
            $resMod = mysqli_stmt_get_result($stmtMod);
            $mod = mysqli_fetch_assoc($resMod);
            mysqli_stmt_close($stmtMod);

            if (!$mod) {
                throw new Exception('Modificador inválido para producto ID ' . $productoId);
            }

            $precioUnitario += (float) $mod['precio_adicional'];
            $detalles[] = [
                'tipo' => 'modificador',
                'nombre' => $mod['nombre'],
                'precio' => (float) $mod['precio_adicional'],
            ];
        }

        $itemTotal = $precioUnitario * $cantidad;
        $subtotal += $itemTotal;

        $itemsFinal[] = [
            'producto_id' => $productoId,
            'nombre_producto' => $producto['nombre'],
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'total' => $itemTotal,
            'detalles' => $detalles,
        ];
    }

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

    $sqlPedido = 'INSERT INTO pedidos (restaurante_id, cliente_id, direccion_id, zona_id, tipo, estado, subtotal, costo_envio, total, observaciones, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmtPedido = mysqli_prepare($conn, $sqlPedido);
    $estadoInicial = 'nuevo';
    $createdAt = mr_now();
    $direccionIdParam = $direccionId ?: null;
    $zonaIdParam = $zonaId > 0 ? $zonaId : null;

    mysqli_stmt_bind_param(
        $stmtPedido,
        'iiiissdddss',
        $restauranteId,
        $clienteId,
        $direccionIdParam,
        $zonaIdParam,
        $tipo,
        $estadoInicial,
        $subtotal,
        $costoEnvio,
        $total,
        $observaciones,
        $createdAt
    );

    mysqli_stmt_execute($stmtPedido);
    $pedidoId = (int) mysqli_insert_id($conn);
    mysqli_stmt_close($stmtPedido);

    $sqlItem = 'INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?, ?)';
    $stmtItem = mysqli_prepare($conn, $sqlItem);

    $sqlDetalle = 'INSERT INTO pedido_item_detalles (pedido_item_id, tipo, nombre, precio) VALUES (?, ?, ?, ?)';
    $stmtDetalle = mysqli_prepare($conn, $sqlDetalle);

    foreach ($itemsFinal as $item) {
        mysqli_stmt_bind_param(
            $stmtItem,
            'iisidd',
            $pedidoId,
            $item['producto_id'],
            $item['nombre_producto'],
            $item['cantidad'],
            $item['precio_unitario'],
            $item['total']
        );
        mysqli_stmt_execute($stmtItem);
        $pedidoItemId = (int) mysqli_insert_id($conn);

        foreach ($item['detalles'] as $detalle) {
            mysqli_stmt_bind_param(
                $stmtDetalle,
                'issd',
                $pedidoItemId,
                $detalle['tipo'],
                $detalle['nombre'],
                $detalle['precio']
            );
            mysqli_stmt_execute($stmtDetalle);
        }
    }

    mysqli_stmt_close($stmtDetalle);
    mysqli_stmt_close($stmtItem);

    $sqlHist = 'INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at) VALUES (?, ?, ?)';
    $stmtHist = mysqli_prepare($conn, $sqlHist);
    mysqli_stmt_bind_param($stmtHist, 'iss', $pedidoId, $estadoInicial, $createdAt);
    mysqli_stmt_execute($stmtHist);
    mysqli_stmt_close($stmtHist);

    $sqlPago = 'INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia_externa, created_at) VALUES (?, ?, ?, ?, ?, ?)';
    $stmtPago = mysqli_prepare($conn, $sqlPago);
    $estadoPago = 'pendiente';
    $referencia = null;
    mysqli_stmt_bind_param($stmtPago, 'issdss', $pedidoId, $metodoPago, $estadoPago, $total, $referencia, $createdAt);
    mysqli_stmt_execute($stmtPago);
    mysqli_stmt_close($stmtPago);

    mysqli_commit($conn);

    mr_json_response([
        'ok' => true,
        'pedido_id' => $pedidoId,
        'estado' => $estadoInicial,
        'estado_pago' => $estadoPago,
        'totales' => [
            'subtotal' => round($subtotal, 2),
            'costo_envio' => round($costoEnvio, 2),
            'total' => round($total, 2),
        ],
    ], 201);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    mr_json_response([
        'ok' => false,
        'error' => $e->getMessage(),
    ], 422);
}
