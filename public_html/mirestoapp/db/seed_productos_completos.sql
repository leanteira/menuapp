-- =============================================================
-- MiRestoApp - SEED COMPLETO DE PRODUCTOS
-- 10 Bebidas, 10 Pizzas, 10 Empanadas, 10 Postres + Helados con tamaños
-- =============================================================

USE serv_mirestoapp;

SET SQL_SAFE_UPDATES = 0;

-- =============================================================
-- OBTENER ID DEL RESTAURANTE DEMO
-- =============================================================
SET @resto_id = (SELECT id FROM restaurantes WHERE slug = 'demo-resto' LIMIT 1);

-- Si no existe, crear uno
INSERT INTO restaurantes (nombre, slug, telefono, email, direccion, activo, created_at)
SELECT 'Resto Demo', 'demo-resto', '1122334455', 'contacto@demo.com', 'Av. Corrientes 1234, CABA', 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM restaurantes WHERE slug = 'demo-resto');

SET @resto_id = (SELECT id FROM restaurantes WHERE slug = 'demo-resto');

-- =============================================================
-- CREAR CATEGORÍAS
-- =============================================================
INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Bebidas', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Bebidas');

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Pizzas', 2, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Pizzas');

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Empanadas', 3, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Empanadas');

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Postres', 4, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Postres');

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
SELECT @resto_id, 'Helados', 5, 1
WHERE NOT EXISTS (SELECT 1 FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Helados');

SET @cat_bebidas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Bebidas');
SET @cat_pizzas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Pizzas');
SET @cat_empanadas = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Empanadas');
SET @cat_postres = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Postres');
SET @cat_helados = (SELECT id FROM categorias WHERE restaurante_id = @resto_id AND nombre = 'Helados');

-- =============================================================
-- 10 BEBIDAS
-- =============================================================
INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo)
VALUES
(@resto_id, @cat_bebidas, 'Coca Cola', 'Bebida gaseosa clásica', 2.50, 1),
(@resto_id, @cat_bebidas, 'Sprite', 'Bebida gaseosa limón', 2.50, 1),
(@resto_id, @cat_bebidas, 'Agua Mineral', 'Agua mineral sin gas litro', 1.50, 1),
(@resto_id, @cat_bebidas, 'Jugo de Naranja', 'Jugo natural de naranja', 3.00, 1),
(@resto_id, @cat_bebidas, 'Jugo de Frutilla', 'Jugo natural de frutilla', 3.50, 1),
(@resto_id, @cat_bebidas, 'Cerveza Artesanal', 'Cerveza rubia artesanal 500ml', 4.50, 1),
(@resto_id, @cat_bebidas, 'Té Helado', 'Té helado casero', 2.75, 1),
(@resto_id, @cat_bebidas, 'Limonada', 'Limonada casera fresca', 2.50, 1),
(@resto_id, @cat_bebidas, 'Café Helado', 'Café con hielo y leche', 3.25, 1),
(@resto_id, @cat_bebidas, 'Batido de Chocolate', 'Batido cremoso de chocolate', 4.00, 1);

-- Variantes para bebidas (tamaños)
SET @beb1 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Coca Cola' LIMIT 1);
SET @beb2 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Sprite' LIMIT 1);
SET @beb3 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Agua Mineral' LIMIT 1);
SET @beb4 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Jugo de Naranja' LIMIT 1);
SET @beb5 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Jugo de Frutilla' LIMIT 1);
SET @beb6 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Cerveza Artesanal' LIMIT 1);
SET @beb7 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Té Helado' LIMIT 1);
SET @beb8 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Limonada' LIMIT 1);
SET @beb9 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Café Helado' LIMIT 1);
SET @beb10 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Batido de Chocolate' LIMIT 1);

INSERT INTO producto_variantes (producto_id, nombre, precio_adicional) VALUES
(@beb1, 'Pequeño 250ml', 0),
(@beb1, 'Mediano 500ml', 0.75),
(@beb1, 'Grande 1L', 1.50),
(@beb2, 'Pequeño 250ml', 0),
(@beb2, 'Mediano 500ml', 0.75),
(@beb2, 'Grande 1L', 1.50),
(@beb3, 'Pequeño 500ml', 0),
(@beb3, 'Grande 1L', 0.50),
(@beb4, 'Vaso 250ml', 0),
(@beb4, 'Jarra 1L', 1.00),
(@beb5, 'Vaso 250ml', 0),
(@beb5, 'Jarra 1L', 1.50),
(@beb6, 'Lata 473ml', 0),
(@beb7, 'Vaso 300ml', 0),
(@beb7, 'Jarra 1L', 0.75),
(@beb8, 'Vaso 300ml', 0),
(@beb8, 'Jarra 1L', 0.75),
(@beb9, 'Con hielo 300ml', 0),
(@beb9, 'Extra grande 500ml', 0.75),
(@beb10, 'Regular 250ml', 0),
(@beb10, 'Grande 400ml', 0.75);

-- =============================================================
-- 10 PIZZAS (Con variantes de tamaño)
-- =============================================================
INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo)
VALUES
(@resto_id, @cat_pizzas, 'Pizza Margherita', 'Tomate, mozzarella, albahaca fresca', 8.50, 1),
(@resto_id, @cat_pizzas, 'Pizza Pepperoni', 'Tomate, mozzarella, pepperoni', 9.50, 1),
(@resto_id, @cat_pizzas, 'Pizza Cuatro Quesos', 'Mozzarella, azul, cheddar, provoleta', 11.00, 1),
(@resto_id, @cat_pizzas, 'Pizza Vegetariana', 'Tomate, champiñones, rúcula, cebolla', 8.75, 1),
(@resto_id, @cat_pizzas, 'Pizza BBQ Chicken', 'Pollo desmenuzado, salsa BBQ, cebolla, mozzarella', 10.50, 1),
(@resto_id, @cat_pizzas, 'Pizza Jamón Serrano', 'Jamón serrano, huevo, mozzarella', 10.00, 1),
(@resto_id, @cat_pizzas, 'Pizza Especial de la Casa', 'Carnes variadas, vegetales frescos, mozzarella', 12.00, 1),
(@resto_id, @cat_pizzas, 'Pizza Hawaiana', 'Jamón, piña, mozzarella, cebolla', 9.75, 1),
(@resto_id, @cat_pizzas, 'Pizza Borde Relleno', 'Rellena de queso fundido en los bordes', 10.25, 1),
(@resto_id, @cat_pizzas, 'Pizza Alitas Picantes', 'Alitas de pollo picantes, cebolla, queso', 11.50, 1);

-- Variantes para pizzas (tamaños)
SET @pizza1 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Margherita' LIMIT 1);
SET @pizza2 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Pepperoni' LIMIT 1);
SET @pizza3 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Cuatro Quesos' LIMIT 1);
SET @pizza4 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Vegetariana' LIMIT 1);
SET @pizza5 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza BBQ Chicken' LIMIT 1);
SET @pizza6 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Jamón Serrano' LIMIT 1);
SET @pizza7 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Especial de la Casa' LIMIT 1);
SET @pizza8 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Hawaiana' LIMIT 1);
SET @pizza9 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Borde Relleno' LIMIT 1);
SET @pizza10 = (SELECT DISTINCT id FROM productos WHERE restaurante_id = @resto_id AND categoria_id = @cat_pizzas AND nombre = 'Pizza Alitas Picantes' LIMIT 1);

-- Insertar variantes para todas las pizzas
INSERT INTO producto_variantes (producto_id, nombre, precio_adicional) VALUES
(@pizza1, 'Pequeña 25cm', -1.00),
(@pizza1, 'Mediana 30cm', 0),
(@pizza1, 'Grande 35cm', 2.00),
(@pizza1, 'Gigante 40cm', 4.00),
(@pizza2, 'Pequeña 25cm', -1.00),
(@pizza2, 'Mediana 30cm', 0),
(@pizza2, 'Grande 35cm', 2.00),
(@pizza2, 'Gigante 40cm', 4.00),
(@pizza3, 'Pequeña 25cm', -1.00),
(@pizza3, 'Mediana 30cm', 0),
(@pizza3, 'Grande 35cm', 2.00),
(@pizza3, 'Gigante 40cm', 4.00),
(@pizza4, 'Pequeña 25cm', -1.00),
(@pizza4, 'Mediana 30cm', 0),
(@pizza4, 'Grande 35cm', 2.00),
(@pizza4, 'Gigante 40cm', 4.00),
(@pizza5, 'Pequeña 25cm', -1.00),
(@pizza5, 'Mediana 30cm', 0),
(@pizza5, 'Grande 35cm', 2.00),
(@pizza5, 'Gigante 40cm', 4.00),
(@pizza6, 'Pequeña 25cm', -1.00),
(@pizza6, 'Mediana 30cm', 0),
(@pizza6, 'Grande 35cm', 2.00),
(@pizza6, 'Gigante 40cm', 4.00),
(@pizza7, 'Pequeña 25cm', -1.00),
(@pizza7, 'Mediana 30cm', 0),
(@pizza7, 'Grande 35cm', 2.00),
(@pizza7, 'Gigante 40cm', 4.00),
(@pizza8, 'Pequeña 25cm', -1.00),
(@pizza8, 'Mediana 30cm', 0),
(@pizza8, 'Grande 35cm', 2.00),
(@pizza8, 'Gigante 40cm', 4.00),
(@pizza9, 'Pequeña 25cm', -1.00),
(@pizza9, 'Mediana 30cm', 0),
(@pizza9, 'Grande 35cm', 2.00),
(@pizza9, 'Gigante 40cm', 4.00),
(@pizza10, 'Pequeña 25cm', -1.00),
(@pizza10, 'Mediana 30cm', 0),
(@pizza10, 'Grande 35cm', 2.00),
(@pizza10, 'Gigante 40cm', 4.00);

-- =============================================================
-- 10 EMPANADAS
-- =============================================================
INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo)
VALUES
(@resto_id, @cat_empanadas, 'Empanada de Carne', 'Relleno de carne picada con cebolla', 1.25, 1),
(@resto_id, @cat_empanadas, 'Empanada de Pollo', 'Pollo desmenuzado con vegetales', 1.25, 1),
(@resto_id, @cat_empanadas, 'Empanada de Jamón y Queso', 'Jamón y queso fundido', 1.50, 1),
(@resto_id, @cat_empanadas, 'Empanada de Queso', 'Queso fresco cremoso', 1.00, 1),
(@resto_id, @cat_empanadas, 'Empanada de Atún', 'Atún con huevo y cebolla', 1.50, 1),
(@resto_id, @cat_empanadas, 'Empanada de Verdura', 'Espinaca, zapallo, queso', 1.00, 1),
(@resto_id, @cat_empanadas, 'Empanada de Humita', 'Humita casera de maíz', 1.25, 1),
(@resto_id, @cat_empanadas, 'Empanada de Roquefort', 'Queso roquefort intenso', 1.75, 1),
(@resto_id, @cat_empanadas, 'Empanada de Carne Picante', 'Carne con ajíes picantes', 1.50, 1),
(@resto_id, @cat_empanadas, 'Empanada de Champiñones', 'Champiñones frescos salteados', 1.25, 1);

-- Variantes para empanadas (cantidad)
SET @emp1 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Carne' LIMIT 1);
SET @emp2 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Pollo' LIMIT 1);
SET @emp3 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Jamón y Queso' LIMIT 1);
SET @emp4 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Queso' LIMIT 1);
SET @emp5 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Atún' LIMIT 1);
SET @emp6 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Verdura' LIMIT 1);
SET @emp7 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Humita' LIMIT 1);
SET @emp8 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Roquefort' LIMIT 1);
SET @emp9 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Carne Picante' LIMIT 1);
SET @emp10 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Empanada de Champiñones' LIMIT 1);

INSERT INTO producto_variantes (producto_id, nombre, precio_adicional) VALUES
(@emp1, 'Unidad', 0),
(@emp1, 'Docena', 12.00),
(@emp2, 'Unidad', 0),
(@emp2, 'Docena', 12.00),
(@emp3, 'Unidad', 0),
(@emp3, 'Docena', 15.00),
(@emp4, 'Unidad', 0),
(@emp4, 'Docena', 9.00),
(@emp5, 'Unidad', 0),
(@emp5, 'Docena', 15.00),
(@emp6, 'Unidad', 0),
(@emp6, 'Docena', 9.00),
(@emp7, 'Unidad', 0),
(@emp7, 'Docena', 12.00),
(@emp8, 'Unidad', 0),
(@emp8, 'Docena', 18.00),
(@emp9, 'Unidad', 0),
(@emp9, 'Docena', 15.00),
(@emp10, 'Unidad', 0),
(@emp10, 'Docena', 12.00);

-- =============================================================
-- 10 POSTRES
-- =============================================================
INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo)
VALUES
(@resto_id, @cat_postres, 'Tiramisú', 'Clásico italiano con mascarpone y café', 4.50, 1),
(@resto_id, @cat_postres, 'Brownie Chocolate', 'Brownie casero con chocolate intenso', 3.75, 1),
(@resto_id, @cat_postres, 'Cheesecake', 'Quesillo cremoso con frutos rojos', 4.75, 1),
(@resto_id, @cat_postres, 'Flan Casero', 'Flan tradicional con dulce de leche', 2.50, 1),
(@resto_id, @cat_postres, 'Mousse de Chocolate', 'Mousse aireado de chocolate belga', 3.50, 1),
(@resto_id, @cat_postres, 'Ensalada de Frutas', 'Frutas frescas de estación', 3.25, 1),
(@resto_id, @cat_postres, 'Ensalada Rusa', 'Papas, huevo, mayonesa, vegetales', 2.75, 1),
(@resto_id, @cat_postres, 'Torta Negra', 'Torta casera con chocolate y nueces', 5.00, 1),
(@resto_id, @cat_postres, 'Buñuelos de Viento', 'Buñuelos esponjosos con doble de dulce', 3.00, 1),
(@resto_id, @cat_postres, 'Mille Hojas', 'Hojaldre con crema pastelera', 4.00, 1);

-- =============================================================
-- HELADOS CON TAMAÑOS
-- =============================================================
INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo)
VALUES
(@resto_id, @cat_helados, 'Surtido de Helado', 'Variedad de sabores de nuestros helados premium', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Negra Premium', 'Helado intenso de chocolate negro', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Dulce de Leche', 'Cremoso dulce de leche argentino', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Frutilla', 'Frutilla natural fresca', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Vainilla', 'Vainilla clásica cremosa', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Menta Chocolate', 'Menta refrescante con chips de chocolate', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Pistacho', 'Pistacho premium tostado', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Almendras', 'Almendras tostadas y crujientes', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Café', 'Café espresso intenso y cremoso', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Crema Americana', 'Crema americana suave', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Frambuesa', 'Frambuesa natural', 6.00, 1),
(@resto_id, @cat_helados, 'Helado Limón', 'Limón agrio y refrescante', 6.00, 1);

-- Obtener IDs de los helados para agregar variantes
SET @hel1 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Surtido de Helado' LIMIT 1);
SET @hel2 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Negra Premium' LIMIT 1);
SET @hel3 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Dulce de Leche' LIMIT 1);
SET @hel4 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Frutilla' LIMIT 1);
SET @hel5 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Vainilla' LIMIT 1);
SET @hel6 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Menta Chocolate' LIMIT 1);
SET @hel7 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Pistacho' LIMIT 1);
SET @hel8 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Almendras' LIMIT 1);
SET @hel9 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Café' LIMIT 1);
SET @hel10 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Crema Americana' LIMIT 1);
SET @hel11 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Frambuesa' LIMIT 1);
SET @hel12 = (SELECT id FROM productos WHERE restaurante_id = @resto_id AND nombre = 'Helado Limón' LIMIT 1);

-- Variantes de tamaño para helados
INSERT INTO producto_variantes (producto_id, nombre, precio_adicional) VALUES
-- Surtido
(@hel1, '1/4 Kg (~250ml)', -2.00),
(@hel1, '1/2 Kg (~500ml)', 0),
(@hel1, '3/4 Kg (~750ml)', 3.00),
(@hel1, '1 Kg (~1000ml)', 5.00),
(@hel1, '1 1/2 Kg (~1500ml)', 8.00),
(@hel1, '2 Kg (~2000ml)', 10.00),
-- Negra Premium
(@hel2, '1/4 Kg (~250ml)', -2.00),
(@hel2, '1/2 Kg (~500ml)', 0),
(@hel2, '3/4 Kg (~750ml)', 3.00),
(@hel2, '1 Kg (~1000ml)', 5.00),
(@hel2, '1 1/2 Kg (~1500ml)', 8.00),
(@hel2, '2 Kg (~2000ml)', 10.00),
-- Dulce de Leche
(@hel3, '1/4 Kg (~250ml)', -2.00),
(@hel3, '1/2 Kg (~500ml)', 0),
(@hel3, '3/4 Kg (~750ml)', 3.00),
(@hel3, '1 Kg (~1000ml)', 5.00),
(@hel3, '1 1/2 Kg (~1500ml)', 8.00),
(@hel3, '2 Kg (~2000ml)', 10.00),
-- Frutilla
(@hel4, '1/4 Kg (~250ml)', -2.00),
(@hel4, '1/2 Kg (~500ml)', 0),
(@hel4, '3/4 Kg (~750ml)', 3.00),
(@hel4, '1 Kg (~1000ml)', 5.00),
(@hel4, '1 1/2 Kg (~1500ml)', 8.00),
(@hel4, '2 Kg (~2000ml)', 10.00),
-- Vainilla
(@hel5, '1/4 Kg (~250ml)', -2.00),
(@hel5, '1/2 Kg (~500ml)', 0),
(@hel5, '3/4 Kg (~750ml)', 3.00),
(@hel5, '1 Kg (~1000ml)', 5.00),
(@hel5, '1 1/2 Kg (~1500ml)', 8.00),
(@hel5, '2 Kg (~2000ml)', 10.00),
-- Menta Chocolate
(@hel6, '1/4 Kg (~250ml)', -2.00),
(@hel6, '1/2 Kg (~500ml)', 0),
(@hel6, '3/4 Kg (~750ml)', 3.00),
(@hel6, '1 Kg (~1000ml)', 5.00),
(@hel6, '1 1/2 Kg (~1500ml)', 8.00),
(@hel6, '2 Kg (~2000ml)', 10.00),
-- Pistacho
(@hel7, '1/4 Kg (~250ml)', -2.00),
(@hel7, '1/2 Kg (~500ml)', 0),
(@hel7, '3/4 Kg (~750ml)', 3.00),
(@hel7, '1 Kg (~1000ml)', 5.00),
(@hel7, '1 1/2 Kg (~1500ml)', 8.00),
(@hel7, '2 Kg (~2000ml)', 10.00),
-- Almendras
(@hel8, '1/4 Kg (~250ml)', -2.00),
(@hel8, '1/2 Kg (~500ml)', 0),
(@hel8, '3/4 Kg (~750ml)', 3.00),
(@hel8, '1 Kg (~1000ml)', 5.00),
(@hel8, '1 1/2 Kg (~1500ml)', 8.00),
(@hel8, '2 Kg (~2000ml)', 10.00),
-- Café
(@hel9, '1/4 Kg (~250ml)', -2.00),
(@hel9, '1/2 Kg (~500ml)', 0),
(@hel9, '3/4 Kg (~750ml)', 3.00),
(@hel9, '1 Kg (~1000ml)', 5.00),
(@hel9, '1 1/2 Kg (~1500ml)', 8.00),
(@hel9, '2 Kg (~2000ml)', 10.00),
-- Crema Americana
(@hel10, '1/4 Kg (~250ml)', -2.00),
(@hel10, '1/2 Kg (~500ml)', 0),
(@hel10, '3/4 Kg (~750ml)', 3.00),
(@hel10, '1 Kg (~1000ml)', 5.00),
(@hel10, '1 1/2 Kg (~1500ml)', 8.00),
(@hel10, '2 Kg (~2000ml)', 10.00),
-- Frambuesa
(@hel11, '1/4 Kg (~250ml)', -2.00),
(@hel11, '1/2 Kg (~500ml)', 0),
(@hel11, '3/4 Kg (~750ml)', 3.00),
(@hel11, '1 Kg (~1000ml)', 5.00),
(@hel11, '1 1/2 Kg (~1500ml)', 8.00),
(@hel11, '2 Kg (~2000ml)', 10.00),
-- Limón
(@hel12, '1/4 Kg (~250ml)', -2.00),
(@hel12, '1/2 Kg (~500ml)', 0),
(@hel12, '3/4 Kg (~750ml)', 3.00),
(@hel12, '1 Kg (~1000ml)', 5.00),
(@hel12, '1 1/2 Kg (~1500ml)', 8.00),
(@hel12, '2 Kg (~2000ml)', 10.00);

-- =============================================================
-- GUSTOS DE HELADO Y RELACIONES
-- =============================================================
-- Tabla de gustos (si no existe)
INSERT INTO helado_gustos (nombre, descripcion, color_hex, activo) VALUES
('Vainilla', 'Clásico y cremoso', '#F3E5AB', 1),
('Chocolate', 'Chocolate belga intenso', '#6F4E37', 1),
('Frutilla', 'Frutilla natural y fresca', '#E75480', 1),
('Dulce de Leche', 'Dulce de leche argentino', '#D2691E', 1),
('Pistacho', 'Pistacho premium', '#93C572', 1),
('Menta', 'Menta fresca y refrescante', '#98FF98', 1),
('Café', 'Café espresso intenso', '#8B4513', 1),
('Crema Americana', 'Suave y clásica', '#F5DEB3', 1),
('Frambuesa', 'Frambuesa natural', '#E30B5C', 1),
('Limón', 'Limón agrio y refrescante', '#FFFF00', 1),
('Almendra', 'Almendra tostada', '#C5B358', 1),
('Menta-Chocolate', 'Combinación refrescante', '#6B8E23', 1),
('Banana Split', 'Banana, chocolate, frutilla', '#F5DEB3', 1),
('Rocky Road', 'Chocolate, malvavisco, nueces', '#8B4513', 1),
('Avellana', 'Avellana tostada', '#A0522D', 1),
('Cereza', 'Cereza natural dulce', '#DC143C', 1)
ON DUPLICATE KEY UPDATE activo = 1;

-- Asignar gustos a los productos de helado
SET @gust_vainilla = (SELECT id FROM helado_gustos WHERE nombre = 'Vainilla' LIMIT 1);
SET @gust_chocolate = (SELECT id FROM helado_gustos WHERE nombre = 'Chocolate' LIMIT 1);
SET @gust_frutilla = (SELECT id FROM helado_gustos WHERE nombre = 'Frutilla' LIMIT 1);
SET @gust_dulce_leche = (SELECT id FROM helado_gustos WHERE nombre = 'Dulce de Leche' LIMIT 1);
SET @gust_pistacho = (SELECT id FROM helado_gustos WHERE nombre = 'Pistacho' LIMIT 1);
SET @gust_menta = (SELECT id FROM helado_gustos WHERE nombre = 'Menta' LIMIT 1);
SET @gust_cafe = (SELECT id FROM helado_gustos WHERE nombre = 'Café' LIMIT 1);
SET @gust_crema = (SELECT id FROM helado_gustos WHERE nombre = 'Crema Americana' LIMIT 1);

-- Surtido puede llevar todos los gustos
INSERT IGNORE INTO producto_helado_gustos (producto_id, gusto_id, disponible) VALUES
(@hel1, @gust_vainilla, 1),
(@hel1, @gust_chocolate, 1),
(@hel1, @gust_frutilla, 1),
(@hel1, @gust_dulce_leche, 1),
(@hel1, @gust_pistacho, 1),
(@hel1, @gust_menta, 1),
(@hel1, @gust_cafe, 1),
(@hel1, @gust_crema, 1);

-- Gustos específicos por helado
INSERT IGNORE INTO producto_helado_gustos (producto_id, gusto_id, disponible) VALUES
(@hel2, @gust_chocolate, 1),
(@hel3, @gust_dulce_leche, 1),
(@hel4, @gust_frutilla, 1),
(@hel5, @gust_vainilla, 1),
(@hel6, @gust_menta, 1),
(@hel7, @gust_pistacho, 1),
(@hel8, (SELECT id FROM helado_gustos WHERE nombre = 'Almendra' LIMIT 1), 1),
(@hel9, @gust_cafe, 1),
(@hel10, @gust_crema, 1),
(@hel11, (SELECT id FROM helado_gustos WHERE nombre = 'Frambuesa' LIMIT 1), 1),
(@hel12, (SELECT id FROM helado_gustos WHERE nombre = 'Limón' LIMIT 1), 1);

COMMIT;

-- =============================================================
-- Mostrar resumen de lo insertado
-- =============================================================
SELECT 'BEBIDAS' as Categoría, COUNT(*) as Total FROM productos WHERE categoria_id = @cat_bebidas
UNION ALL
SELECT 'PIZZAS', COUNT(*) FROM productos WHERE categoria_id = @cat_pizzas
UNION ALL
SELECT 'EMPANADAS', COUNT(*) FROM productos WHERE categoria_id = @cat_empanadas
UNION ALL
SELECT 'POSTRES', COUNT(*) FROM productos WHERE categoria_id = @cat_postres
UNION ALL
SELECT 'HELADOS', COUNT(*) FROM productos WHERE categoria_id = @cat_helados;

SELECT CONCAT('Total de variantes: ', COUNT(*)) as Resumen FROM producto_variantes;
SELECT CONCAT('Total de gustos de helado: ', COUNT(*)) as Resumen FROM helado_gustos;
