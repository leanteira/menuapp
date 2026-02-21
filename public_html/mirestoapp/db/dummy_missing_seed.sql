-- =============================================================
-- MiRestoApp - DUMMY COMPLEMENTARIO (sin recrear estructura)
-- Carga datos demo faltantes sobre una base ya existente.
-- Idempotente: podés ejecutarlo más de una vez.
-- =============================================================

USE serv_mirestoapp;

SET SQL_SAFE_UPDATES = 0;
SET FOREIGN_KEY_CHECKS = 1;

START TRANSACTION;

-- 1) Restaurante demo
INSERT INTO restaurantes (nombre, slug, telefono, email, direccion, activo, created_at)
SELECT 'Resto Demo', 'demo-resto', '1122334455', 'contacto@demo.com', 'Av. Corrientes 1234, CABA', 1, NOW()
WHERE NOT EXISTS (
  SELECT 1 FROM restaurantes WHERE slug = 'demo-resto'
);

SET @resto_id := (
  SELECT id FROM restaurantes WHERE slug = 'demo-resto' ORDER BY id ASC LIMIT 1
);

-- 2) Usuarios demo
INSERT INTO usuarios (restaurante_id, nombre, email, password, rol, activo, created_at)
SELECT NULL, 'Super Admin Demo', 'superadmin@demo.com', 'admin123', 'superadmin', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'superadmin@demo.com');

INSERT INTO usuarios (restaurante_id, nombre, email, password, rol, activo, created_at)
SELECT @resto_id, 'Admin Demo', 'admin@demo.com', 'admin123', 'admin', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'admin@demo.com');

INSERT INTO usuarios (restaurante_id, nombre, email, password, rol, activo, created_at)
SELECT @resto_id, 'Operador Demo', 'operador@demo.com', 'admin123', 'operador', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'operador@demo.com');

INSERT INTO usuarios (restaurante_id, nombre, email, password, rol, activo, created_at)
SELECT @resto_id, 'Repartidor Demo', 'repartidor@demo.com', 'admin123', 'repartidor', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email = 'repartidor@demo.com');

-- 3) Categorías base
INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Pizzas', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Pizzas');

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Empanadas', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Empanadas');

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Bebidas', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Bebidas');

SET @cat_p := (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Pizzas' LIMIT 1);
SET @cat_e := (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Empanadas' LIMIT 1);
SET @cat_b := (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Bebidas' LIMIT 1);

-- 4) Productos base
INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
SELECT @resto_id, @cat_p, 'Pizza Muzzarella', 'Salsa, mozzarella y aceitunas', 8500.00, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Pizza Muzzarella');

INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
SELECT @resto_id, @cat_p, 'Pizza Napolitana', 'Tomate, ajo y mozzarella', 9200.00, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Pizza Napolitana');

INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
SELECT @resto_id, @cat_e, 'Empanada de Carne', 'Carne cortada a cuchillo', 1800.00, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Carne');

INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
SELECT @resto_id, @cat_b, 'Gaseosa 1.5L', 'Coca / Pepsi / Sprite', 3200.00, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Gaseosa 1.5L');

SET @prod_m := (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Pizza Muzzarella' LIMIT 1);
SET @prod_n := (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Pizza Napolitana' LIMIT 1);
SET @prod_ec := (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Carne' LIMIT 1);
SET @prod_g := (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Gaseosa 1.5L' LIMIT 1);

-- 5) Variantes / modificadores
INSERT INTO producto_variantes (producto_id, nombre, precio_adicional)
SELECT @prod_m, 'Chica', 0.00
WHERE @prod_m IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM producto_variantes WHERE producto_id = @prod_m AND nombre = 'Chica');

INSERT INTO producto_variantes (producto_id, nombre, precio_adicional)
SELECT @prod_m, 'Grande', 2200.00
WHERE @prod_m IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM producto_variantes WHERE producto_id = @prod_m AND nombre = 'Grande');

INSERT INTO producto_modificadores (producto_id, nombre, precio_adicional, obligatorio)
SELECT @prod_m, 'Extra mozzarella', 1200.00, 0
WHERE @prod_m IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM producto_modificadores WHERE producto_id = @prod_m AND nombre = 'Extra mozzarella');

INSERT INTO producto_modificadores (producto_id, nombre, precio_adicional, obligatorio)
SELECT @prod_ec, 'Salsa picante', 150.00, 0
WHERE @prod_ec IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM producto_modificadores WHERE producto_id = @prod_ec AND nombre = 'Salsa picante');

-- 6) Zona de envío mínima
INSERT INTO zonas_envio (restaurante_id, nombre, costo_envio, pedido_minimo, activo, tipo_area, centro_lat, centro_lng, radio_metros, poligono_json)
SELECT @resto_id, 'Zona Centro', 1800.00, 8000.00, 1, 'radio', -34.60370000, -58.38160000, 4500.00, NULL
WHERE NOT EXISTS (SELECT 1 FROM zonas_envio WHERE restaurante_id = @resto_id AND nombre = 'Zona Centro');

SET @zona_centro := (SELECT id FROM zonas_envio WHERE restaurante_id = @resto_id AND nombre = 'Zona Centro' LIMIT 1);

-- 7) Cliente demo + dirección
INSERT INTO clientes (restaurante_id, nombre, telefono, email, created_at)
SELECT @resto_id, 'Juan Pérez', '1155551111', 'juan@demo.com', NOW()
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE restaurante_id = @resto_id AND telefono = '1155551111');

SET @cli_juan := (SELECT id FROM clientes WHERE restaurante_id = @resto_id AND telefono = '1155551111' LIMIT 1);

INSERT INTO clientes_direcciones (cliente_id, direccion, lat, lng, referencia, created_at)
SELECT @cli_juan, 'Av. Corrientes 1234, CABA', -34.60370, -58.38160, 'Piso 5 Dpto B', NOW()
WHERE @cli_juan IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM clientes_direcciones
    WHERE cliente_id = @cli_juan AND direccion = 'Av. Corrientes 1234, CABA'
  );

SET @dir_juan := (
  SELECT id FROM clientes_direcciones
  WHERE cliente_id = @cli_juan
  ORDER BY id DESC LIMIT 1
);

-- 8) Pedido demo si no hay pedidos en ese restaurante
INSERT INTO pedidos (restaurante_id, cliente_id, direccion_id, zona_id, tipo, estado, subtotal, costo_envio, total, observaciones, created_at)
SELECT @resto_id, @cli_juan, @dir_juan, @zona_centro, 'delivery', 'nuevo', 12500.00, 1800.00, 14300.00, 'Tocar timbre 2 veces', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pedidos WHERE restaurante_id = @resto_id);

SET @pedido_demo := (
  SELECT id FROM pedidos WHERE restaurante_id = @resto_id ORDER BY id DESC LIMIT 1
);

INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, total)
SELECT @pedido_demo, @prod_m, 'Pizza Muzzarella', 1, 10700.00, 10700.00
WHERE @pedido_demo IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM pedido_items WHERE pedido_id = @pedido_demo AND producto_id = @prod_m);

INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, total)
SELECT @pedido_demo, @prod_g, 'Gaseosa 1.5L', 1, 1800.00, 1800.00
WHERE @pedido_demo IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM pedido_items WHERE pedido_id = @pedido_demo AND producto_id = @prod_g);

INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at)
SELECT @pedido_demo, 'nuevo', NOW()
WHERE @pedido_demo IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM pedido_estados_historial WHERE pedido_id = @pedido_demo AND estado = 'nuevo');

INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia_externa, created_at)
SELECT @pedido_demo, 'contra_entrega', 'pendiente', 14300.00, NULL, NOW()
WHERE @pedido_demo IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM pagos WHERE pedido_id = @pedido_demo);

COMMIT;

-- Login demo:
-- superadmin@demo.com / admin123
-- admin@demo.com      / admin123
-- operador@demo.com   / admin123
-- repartidor@demo.com / admin123
