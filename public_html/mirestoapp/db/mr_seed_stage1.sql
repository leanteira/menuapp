-- Seed inicial para Fase 1 MVP
-- Ejecutar sobre base: serv_mirestoapp

INSERT INTO restaurantes (nombre, slug, telefono, email, direccion, activo, created_at)
VALUES ('Resto Demo', 'demo-resto', '1122334455', 'demo@resto.com', 'CABA', 1, NOW());

SET @resto_id = LAST_INSERT_ID();

INSERT INTO usuarios (restaurante_id, nombre, email, password, rol, activo, created_at)
VALUES (@resto_id, 'Admin Demo', 'admin@demo.com', 'admin123', 'admin', 1, NOW());

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
VALUES
(@resto_id, 'Pizzas', 1, 1),
(@resto_id, 'Empanadas', 2, 1),
(@resto_id, 'Bebidas', 3, 1);

SET @cat_pizzas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Pizzas' LIMIT 1);
SET @cat_empanadas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Empanadas' LIMIT 1);
SET @cat_bebidas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Bebidas' LIMIT 1);

INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
VALUES
(@resto_id, @cat_pizzas, 'Pizza Muzzarella', 'Salsa, muzza y aceitunas', 8500.00, 1, NOW()),
(@resto_id, @cat_empanadas, 'Empanada de Carne', 'Carne cortada a cuchillo', 1800.00, 1, NOW()),
(@resto_id, @cat_bebidas, 'Gaseosa 1.5L', 'Coca / Pepsi / Sprite', 3200.00, 1, NOW());

SET @prod_pizza = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Pizza Muzzarella' LIMIT 1);

INSERT INTO producto_variantes (producto_id, nombre, precio_adicional)
VALUES
(@prod_pizza, 'Chica', 0.00),
(@prod_pizza, 'Grande', 2200.00);

INSERT INTO producto_modificadores (producto_id, nombre, precio_adicional, obligatorio)
VALUES
(@prod_pizza, 'Extra muzzarella', 1200.00, 0),
(@prod_pizza, 'Sin aceitunas', 0.00, 0);

INSERT INTO zonas_envio (restaurante_id, nombre, costo_envio, pedido_minimo, activo)
VALUES
(@resto_id, 'Zona Centro', 1800.00, 8000.00, 1);
