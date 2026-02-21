-- =============================================================
-- MiRestoApp - Dummy data + usuarios para login (idempotente)
-- Base esperada: serv_mirestoapp
-- =============================================================

SET SQL_SAFE_UPDATES = 0;
START TRANSACTION;

-- -------------------------------------------------------------
-- 1) Limpieza controlada de datos demo previos
-- -------------------------------------------------------------

SET @demo_slug = 'demo-resto';
SET @demo_admin_email = 'admin@demo.com';
SET @demo_operador_email = 'operador@demo.com';
SET @demo_repartidor_email = 'repartidor@demo.com';
SET @demo_superadmin_email = 'superadmin@demo.com';

SELECT id INTO @demo_resto_id
FROM restaurantes
WHERE slug = @demo_slug
LIMIT 1;

DELETE FROM usuarios
WHERE email IN (@demo_admin_email, @demo_operador_email, @demo_repartidor_email, @demo_superadmin_email);

-- Si existía el restaurante demo, al borrarlo se limpian cascadas asociadas
DELETE FROM restaurantes
WHERE slug = @demo_slug;

-- -------------------------------------------------------------
-- 2) Restaurante demo
-- -------------------------------------------------------------

INSERT INTO restaurantes (nombre, slug, telefono, email, direccion, logo, activo, created_at)
VALUES ('Resto Demo', @demo_slug, '1122334455', 'contacto@demo.com', 'Av. Siempre Viva 742, CABA', NULL, 1, NOW());

SET @resto_id = LAST_INSERT_ID();

-- -------------------------------------------------------------
-- 3) Usuarios de acceso
-- Nota: password en texto plano apropósito para bootstrap.
-- login.php actualmente acepta password_verify() o igualdad directa.
-- -------------------------------------------------------------

INSERT INTO usuarios (restaurante_id, nombre, email, password, rol, activo, created_at)
VALUES
(NULL, 'Super Admin Demo', @demo_superadmin_email, 'admin123', 'superadmin', 1, NOW()),
(@resto_id, 'Admin Demo', @demo_admin_email, 'admin123', 'admin', 1, NOW()),
(@resto_id, 'Operador Demo', @demo_operador_email, 'admin123', 'operador', 1, NOW()),
(@resto_id, 'Repartidor Demo', @demo_repartidor_email, 'admin123', 'repartidor', 1, NOW());

-- -------------------------------------------------------------
-- 4) Categorías
-- -------------------------------------------------------------

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
VALUES
(@resto_id, 'Pizzas', 1, 1),
(@resto_id, 'Empanadas', 2, 1),
(@resto_id, 'Bebidas', 3, 1),
(@resto_id, 'Postres', 4, 1);

SET @cat_pizzas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Pizzas' LIMIT 1);
SET @cat_empanadas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Empanadas' LIMIT 1);
SET @cat_bebidas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Bebidas' LIMIT 1);
SET @cat_postres = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Postres' LIMIT 1);

-- -------------------------------------------------------------
-- 5) Productos
-- -------------------------------------------------------------

INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, imagen, activo, created_at)
VALUES
(@resto_id, @cat_pizzas, 'Pizza Muzzarella', 'Salsa, mozzarella y aceitunas', 8500.00, NULL, 1, NOW()),
(@resto_id, @cat_pizzas, 'Pizza Napolitana', 'Tomate, ajo y mozzarella', 9200.00, NULL, 1, NOW()),
(@resto_id, @cat_empanadas, 'Empanada de Carne', 'Carne cortada a cuchillo', 1800.00, NULL, 1, NOW()),
(@resto_id, @cat_empanadas, 'Empanada de Jamón y Queso', 'Clásica', 1700.00, NULL, 1, NOW()),
(@resto_id, @cat_bebidas, 'Gaseosa 1.5L', 'Coca / Pepsi / Sprite', 3200.00, NULL, 1, NOW()),
(@resto_id, @cat_bebidas, 'Agua 500ml', 'Sin gas', 1400.00, NULL, 1, NOW()),
(@resto_id, @cat_postres, 'Flan Casero', 'Con dulce de leche y crema', 2600.00, NULL, 1, NOW());

SET @prod_muzza = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Pizza Muzzarella' LIMIT 1);
SET @prod_napo = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Pizza Napolitana' LIMIT 1);
SET @prod_emp_carne = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Carne' LIMIT 1);
SET @prod_gaseosa = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Gaseosa 1.5L' LIMIT 1);

-- Variantes
INSERT INTO producto_variantes (producto_id, nombre, precio_adicional)
VALUES
(@prod_muzza, 'Chica', 0.00),
(@prod_muzza, 'Grande', 2200.00),
(@prod_napo, 'Chica', 0.00),
(@prod_napo, 'Grande', 2400.00);

-- Modificadores
INSERT INTO producto_modificadores (producto_id, nombre, precio_adicional, obligatorio)
VALUES
(@prod_muzza, 'Extra mozzarella', 1200.00, 0),
(@prod_muzza, 'Sin aceitunas', 0.00, 0),
(@prod_napo, 'Extra ajo', 300.00, 0),
(@prod_emp_carne, 'Salsa picante', 150.00, 0);

-- -------------------------------------------------------------
-- 6) Zonas de envío
-- Compatible con esquema original y extendido (tipo_area default)
-- -------------------------------------------------------------

INSERT INTO zonas_envio (restaurante_id, nombre, costo_envio, pedido_minimo, activo)
VALUES
(@resto_id, 'Zona Centro', 1800.00, 8000.00, 1),
(@resto_id, 'Zona Norte', 2500.00, 10000.00, 1),
(@resto_id, 'Zona Sur', 3000.00, 12000.00, 1);

SET @zona_centro = (SELECT id FROM zonas_envio WHERE restaurante_id = @resto_id AND nombre = 'Zona Centro' LIMIT 1);

-- Si existen columnas extendidas, dejamos una zona de radio básica
SET @has_tipo_area = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'zonas_envio'
    AND COLUMN_NAME = 'tipo_area'
);

SET @sql_update_zona = IF(
  @has_tipo_area > 0,
  CONCAT('UPDATE zonas_envio SET tipo_area = ''radio'', centro_lat = -34.6037, centro_lng = -58.3816, radio_metros = 4500 WHERE id = ', @zona_centro),
  'SELECT 1'
);
PREPARE stmt_zona FROM @sql_update_zona;
EXECUTE stmt_zona;
DEALLOCATE PREPARE stmt_zona;

-- -------------------------------------------------------------
-- 7) Clientes + direcciones
-- -------------------------------------------------------------

INSERT INTO clientes (restaurante_id, nombre, telefono, email, created_at)
VALUES
(@resto_id, 'Juan Pérez', '1155551111', 'juan@demo.com', NOW()),
(@resto_id, 'María Gómez', '1155552222', 'maria@demo.com', NOW());

SET @cli_juan = (SELECT id FROM clientes WHERE restaurante_id = @resto_id AND telefono = '1155551111' LIMIT 1);
SET @cli_maria = (SELECT id FROM clientes WHERE restaurante_id = @resto_id AND telefono = '1155552222' LIMIT 1);

INSERT INTO clientes_direcciones (cliente_id, direccion, lat, lng, referencia, created_at)
VALUES
(@cli_juan, 'Av. Corrientes 1234, CABA', -34.60370, -58.38160, 'Piso 5 Dpto B', NOW()),
(@cli_maria, 'Av. Rivadavia 4321, CABA', -34.60990, -58.38830, 'Casa con puerta negra', NOW());

SET @dir_juan = (SELECT id FROM clientes_direcciones WHERE cliente_id = @cli_juan ORDER BY id DESC LIMIT 1);
SET @dir_maria = (SELECT id FROM clientes_direcciones WHERE cliente_id = @cli_maria ORDER BY id DESC LIMIT 1);

-- -------------------------------------------------------------
-- 8) Pedidos dummy (para ver dashboard/panel)
-- -------------------------------------------------------------

-- Pedido 1 (delivery, nuevo)
INSERT INTO pedidos (restaurante_id, cliente_id, direccion_id, zona_id, tipo, estado, subtotal, costo_envio, total, observaciones, created_at)
VALUES
(@resto_id, @cli_juan, @dir_juan, @zona_centro, 'delivery', 'nuevo', 12500.00, 1800.00, 14300.00, 'Tocar timbre 2 veces', DATE_SUB(NOW(), INTERVAL 20 MINUTE));
SET @pedido_1 = LAST_INSERT_ID();

INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, total)
VALUES
(@pedido_1, @prod_muzza, 'Pizza Muzzarella', 1, 10700.00, 10700.00),
(@pedido_1, @prod_gaseosa, 'Gaseosa 1.5L', 1, 1800.00, 1800.00);

SET @item_1 = (SELECT id FROM pedido_items WHERE pedido_id = @pedido_1 AND producto_id = @prod_muzza LIMIT 1);
INSERT INTO pedido_item_detalles (pedido_item_id, tipo, nombre, precio)
VALUES
(@item_1, 'variante', 'Grande', 2200.00),
(@item_1, 'modificador', 'Sin aceitunas', 0.00);

INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at)
VALUES (@pedido_1, 'nuevo', DATE_SUB(NOW(), INTERVAL 20 MINUTE));

INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia_externa, created_at)
VALUES (@pedido_1, 'contra_entrega', 'pendiente', 14300.00, NULL, DATE_SUB(NOW(), INTERVAL 20 MINUTE));

-- Pedido 2 (teléfono, confirmado)
INSERT INTO pedidos (restaurante_id, cliente_id, direccion_id, zona_id, tipo, estado, subtotal, costo_envio, total, observaciones, created_at)
VALUES
(@resto_id, @cli_maria, @dir_maria, @zona_centro, 'telefono', 'confirmado', 9800.00, 1800.00, 11600.00, 'Pedido tomado por operador', DATE_SUB(NOW(), INTERVAL 90 MINUTE));
SET @pedido_2 = LAST_INSERT_ID();

INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, total)
VALUES
(@pedido_2, @prod_napo, 'Pizza Napolitana', 1, 11600.00, 11600.00);

SET @item_2 = (SELECT id FROM pedido_items WHERE pedido_id = @pedido_2 AND producto_id = @prod_napo LIMIT 1);
INSERT INTO pedido_item_detalles (pedido_item_id, tipo, nombre, precio)
VALUES
(@item_2, 'variante', 'Grande', 2400.00);

INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at)
VALUES
(@pedido_2, 'nuevo', DATE_SUB(NOW(), INTERVAL 95 MINUTE)),
(@pedido_2, 'confirmado', DATE_SUB(NOW(), INTERVAL 85 MINUTE));

INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia_externa, created_at)
VALUES (@pedido_2, 'mercadopago', 'aprobado', 11600.00, 'mp_dummy_001', DATE_SUB(NOW(), INTERVAL 80 MINUTE));

COMMIT;

-- =============================================================
-- Credenciales (login MR)
-- URL: /app/login.php
-- -------------------------------------------------------------
-- superadmin@demo.com / admin123
-- admin@demo.com      / admin123
-- operador@demo.com   / admin123
-- repartidor@demo.com / admin123
-- =============================================================
