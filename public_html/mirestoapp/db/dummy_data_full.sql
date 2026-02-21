-- =============================================================
-- MiRestoApp - REBUILD FULL
-- Reconstruye estructura completa + datos demo + usuarios login
-- =============================================================

CREATE DATABASE IF NOT EXISTS serv_mirestoapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE serv_mirestoapp;

SET SQL_SAFE_UPDATES = 0;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS pedido_estados_historial;
DROP TABLE IF EXISTS pedido_item_detalles;
DROP TABLE IF EXISTS pedido_items;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS clientes_direcciones;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS producto_modificadores;
DROP TABLE IF EXISTS producto_variantes;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS zonas_envio;
DROP TABLE IF EXISTS restaurantes;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
-- ESTRUCTURA
-- =============================================================

CREATE TABLE restaurantes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  slug VARCHAR(150) DEFAULT NULL,
  telefono VARCHAR(50) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  direccion TEXT,
  logo VARCHAR(255) DEFAULT NULL,
  activo TINYINT(4) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_restaurantes_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categorias (
  id INT(11) NOT NULL AUTO_INCREMENT,
  restaurante_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  orden INT(11) DEFAULT 0,
  activo TINYINT(4) DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_categorias_restaurante_id (restaurante_id),
  CONSTRAINT fk_categorias_restaurante FOREIGN KEY (restaurante_id) REFERENCES restaurantes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE productos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  restaurante_id INT(11) DEFAULT NULL,
  categoria_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  descripcion TEXT,
  precio_base DECIMAL(10,2) DEFAULT NULL,
  imagen VARCHAR(255) DEFAULT NULL,
  activo TINYINT(4) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_productos_restaurante_id (restaurante_id),
  KEY idx_productos_categoria_id (categoria_id),
  CONSTRAINT fk_productos_restaurante FOREIGN KEY (restaurante_id) REFERENCES restaurantes (id) ON DELETE CASCADE,
  CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE producto_variantes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  producto_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  precio_adicional DECIMAL(10,2) DEFAULT 0.00,
  PRIMARY KEY (id),
  KEY idx_producto_variantes_producto_id (producto_id),
  CONSTRAINT fk_producto_variantes_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE producto_modificadores (
  id INT(11) NOT NULL AUTO_INCREMENT,
  producto_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  precio_adicional DECIMAL(10,2) DEFAULT 0.00,
  obligatorio TINYINT(4) DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_producto_modificadores_producto_id (producto_id),
  CONSTRAINT fk_producto_modificadores_producto FOREIGN KEY (producto_id) REFERENCES productos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuarios (
  id INT(11) NOT NULL AUTO_INCREMENT,
  restaurante_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  password VARCHAR(255) DEFAULT NULL,
  rol ENUM('superadmin','admin','operador','repartidor') DEFAULT 'operador',
  activo TINYINT(4) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_usuarios_email (email),
  KEY idx_usuarios_restaurante_id (restaurante_id),
  CONSTRAINT fk_usuarios_restaurante FOREIGN KEY (restaurante_id) REFERENCES restaurantes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE zonas_envio (
  id INT(11) NOT NULL AUTO_INCREMENT,
  restaurante_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  costo_envio DECIMAL(10,2) DEFAULT NULL,
  pedido_minimo DECIMAL(10,2) DEFAULT NULL,
  activo TINYINT(4) DEFAULT 1,
  tipo_area ENUM('manual','radio','poligono') NOT NULL DEFAULT 'manual',
  centro_lat DECIMAL(10,8) DEFAULT NULL,
  centro_lng DECIMAL(11,8) DEFAULT NULL,
  radio_metros DECIMAL(10,2) DEFAULT NULL,
  poligono_json TEXT,
  PRIMARY KEY (id),
  KEY idx_zonas_envio_restaurante_id (restaurante_id),
  CONSTRAINT fk_zonas_envio_restaurante FOREIGN KEY (restaurante_id) REFERENCES restaurantes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clientes (
  id INT(11) NOT NULL AUTO_INCREMENT,
  restaurante_id INT(11) DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  telefono VARCHAR(50) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_clientes_restaurante_id (restaurante_id),
  KEY idx_clientes_telefono (telefono),
  CONSTRAINT fk_clientes_restaurante FOREIGN KEY (restaurante_id) REFERENCES restaurantes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clientes_direcciones (
  id INT(11) NOT NULL AUTO_INCREMENT,
  cliente_id INT(11) DEFAULT NULL,
  direccion TEXT,
  lat DECIMAL(10,8) DEFAULT NULL,
  lng DECIMAL(11,8) DEFAULT NULL,
  referencia VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_clientes_direcciones_cliente_id (cliente_id),
  CONSTRAINT fk_clientes_direcciones_cliente FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pedidos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  restaurante_id INT(11) DEFAULT NULL,
  cliente_id INT(11) DEFAULT NULL,
  direccion_id INT(11) DEFAULT NULL,
  zona_id INT(11) DEFAULT NULL,
  tipo ENUM('delivery','retiro','telefono') DEFAULT 'delivery',
  estado ENUM('nuevo','confirmado','preparando','listo','enviado','entregado','cancelado') DEFAULT 'nuevo',
  subtotal DECIMAL(10,2) DEFAULT NULL,
  costo_envio DECIMAL(10,2) DEFAULT 0.00,
  total DECIMAL(10,2) DEFAULT NULL,
  observaciones TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pedidos_restaurante_id (restaurante_id),
  KEY idx_pedidos_cliente_id (cliente_id),
  KEY idx_pedidos_direccion_id (direccion_id),
  KEY idx_pedidos_zona_id (zona_id),
  CONSTRAINT fk_pedidos_restaurante FOREIGN KEY (restaurante_id) REFERENCES restaurantes (id) ON DELETE CASCADE,
  CONSTRAINT fk_pedidos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON DELETE SET NULL,
  CONSTRAINT fk_pedidos_direccion FOREIGN KEY (direccion_id) REFERENCES clientes_direcciones (id) ON DELETE SET NULL,
  CONSTRAINT fk_pedidos_zona FOREIGN KEY (zona_id) REFERENCES zonas_envio (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pedido_items (
  id INT(11) NOT NULL AUTO_INCREMENT,
  pedido_id INT(11) DEFAULT NULL,
  producto_id INT(11) DEFAULT NULL,
  nombre_producto VARCHAR(150) DEFAULT NULL,
  cantidad INT(11) DEFAULT NULL,
  precio_unitario DECIMAL(10,2) DEFAULT NULL,
  total DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_pedido_items_pedido_id (pedido_id),
  CONSTRAINT fk_pedido_items_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pedido_item_detalles (
  id INT(11) NOT NULL AUTO_INCREMENT,
  pedido_item_id INT(11) DEFAULT NULL,
  tipo ENUM('variante','modificador') DEFAULT NULL,
  nombre VARCHAR(150) DEFAULT NULL,
  precio DECIMAL(10,2) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_pedido_item_detalles_pedido_item_id (pedido_item_id),
  CONSTRAINT fk_pedido_item_detalles_pedido_item FOREIGN KEY (pedido_item_id) REFERENCES pedido_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pedido_estados_historial (
  id INT(11) NOT NULL AUTO_INCREMENT,
  pedido_id INT(11) DEFAULT NULL,
  estado VARCHAR(50) DEFAULT NULL,
  changed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pedido_estados_historial_pedido_id (pedido_id),
  CONSTRAINT fk_pedido_estados_historial_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pagos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  pedido_id INT(11) DEFAULT NULL,
  metodo VARCHAR(100) DEFAULT NULL,
  estado ENUM('pendiente','aprobado','rechazado','reembolsado') DEFAULT 'pendiente',
  monto DECIMAL(10,2) DEFAULT NULL,
  referencia_externa VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_pagos_pedido_id (pedido_id),
  CONSTRAINT fk_pagos_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- DATA DEMO
-- =============================================================

START TRANSACTION;

SET @slug = 'demo-resto';
SET @email_super = 'superadmin@demo.com';
SET @email_admin = 'admin@demo.com';
SET @email_oper = 'operador@demo.com';
SET @email_rep = 'repartidor@demo.com';

INSERT INTO restaurantes (nombre, slug, telefono, email, direccion, activo, created_at)
VALUES ('Resto Demo', @slug, '1122334455', 'contacto@demo.com', 'Av. Corrientes 1234, CABA', 1, NOW());
SET @resto_id = LAST_INSERT_ID();

INSERT INTO usuarios (restaurante_id, nombre, email, password, rol, activo, created_at)
VALUES
(NULL, 'Super Admin Demo', @email_super, 'admin123', 'superadmin', 1, NOW()),
(@resto_id, 'Admin Demo', @email_admin, 'admin123', 'admin', 1, NOW()),
(@resto_id, 'Operador Demo', @email_oper, 'admin123', 'operador', 1, NOW()),
(@resto_id, 'Repartidor Demo', @email_rep, 'admin123', 'repartidor', 1, NOW());

INSERT INTO categorias (restaurante_id, nombre, orden, activo)
VALUES
(@resto_id, 'Pizzas', 1, 1),
(@resto_id, 'Empanadas', 2, 1),
(@resto_id, 'Bebidas', 3, 1),
(@resto_id, 'Postres', 4, 1);

SET @cat_p = (SELECT id FROM categorias WHERE restaurante_id=@resto_id AND nombre='Pizzas' LIMIT 1);
SET @cat_e = (SELECT id FROM categorias WHERE restaurante_id=@resto_id AND nombre='Empanadas' LIMIT 1);
SET @cat_b = (SELECT id FROM categorias WHERE restaurante_id=@resto_id AND nombre='Bebidas' LIMIT 1);
SET @cat_d = (SELECT id FROM categorias WHERE restaurante_id=@resto_id AND nombre='Postres' LIMIT 1);

INSERT INTO productos (restaurante_id, categoria_id, nombre, descripcion, precio_base, activo, created_at)
VALUES
(@resto_id, @cat_p, 'Pizza Muzzarella', 'Salsa, mozzarella y aceitunas', 8500.00, 1, NOW()),
(@resto_id, @cat_p, 'Pizza Napolitana', 'Tomate, ajo y mozzarella', 9200.00, 1, NOW()),
(@resto_id, @cat_e, 'Empanada de Carne', 'Carne cortada a cuchillo', 1800.00, 1, NOW()),
(@resto_id, @cat_e, 'Empanada JyQ', 'Jamón y queso', 1700.00, 1, NOW()),
(@resto_id, @cat_b, 'Gaseosa 1.5L', 'Coca / Pepsi / Sprite', 3200.00, 1, NOW()),
(@resto_id, @cat_b, 'Agua 500ml', 'Sin gas', 1400.00, 1, NOW()),
(@resto_id, @cat_d, 'Flan Casero', 'Con dulce y crema', 2600.00, 1, NOW());

SET @prod_m = (SELECT id FROM productos WHERE restaurante_id=@resto_id AND nombre='Pizza Muzzarella' LIMIT 1);
SET @prod_n = (SELECT id FROM productos WHERE restaurante_id=@resto_id AND nombre='Pizza Napolitana' LIMIT 1);
SET @prod_ec = (SELECT id FROM productos WHERE restaurante_id=@resto_id AND nombre='Empanada de Carne' LIMIT 1);
SET @prod_g = (SELECT id FROM productos WHERE restaurante_id=@resto_id AND nombre='Gaseosa 1.5L' LIMIT 1);

INSERT INTO producto_variantes (producto_id, nombre, precio_adicional)
VALUES
(@prod_m, 'Chica', 0.00), (@prod_m, 'Grande', 2200.00),
(@prod_n, 'Chica', 0.00), (@prod_n, 'Grande', 2400.00);

INSERT INTO producto_modificadores (producto_id, nombre, precio_adicional, obligatorio)
VALUES
(@prod_m, 'Extra mozzarella', 1200.00, 0),
(@prod_m, 'Sin aceitunas', 0.00, 0),
(@prod_n, 'Extra ajo', 300.00, 0),
(@prod_ec, 'Salsa picante', 150.00, 0);

INSERT INTO zonas_envio (restaurante_id, nombre, costo_envio, pedido_minimo, activo, tipo_area, centro_lat, centro_lng, radio_metros, poligono_json)
VALUES
(@resto_id, 'Zona Centro', 1800.00, 8000.00, 1, 'radio', -34.60370000, -58.38160000, 4500.00, NULL),
(@resto_id, 'Zona Norte', 2500.00, 10000.00, 1, 'manual', NULL, NULL, NULL, NULL),
(@resto_id, 'Zona Sur', 3000.00, 12000.00, 1, 'manual', NULL, NULL, NULL, NULL);

SET @zona_centro = (SELECT id FROM zonas_envio WHERE restaurante_id=@resto_id AND nombre='Zona Centro' LIMIT 1);

INSERT INTO clientes (restaurante_id, nombre, telefono, email, created_at)
VALUES
(@resto_id, 'Juan Pérez', '1155551111', 'juan@demo.com', NOW()),
(@resto_id, 'María Gómez', '1155552222', 'maria@demo.com', NOW()),
(@resto_id, 'Carlos López', '1155553333', 'carlos@demo.com', NOW());

SET @cli_juan = (SELECT id FROM clientes WHERE restaurante_id=@resto_id AND telefono='1155551111' LIMIT 1);
SET @cli_maria = (SELECT id FROM clientes WHERE restaurante_id=@resto_id AND telefono='1155552222' LIMIT 1);
SET @cli_carlos = (SELECT id FROM clientes WHERE restaurante_id=@resto_id AND telefono='1155553333' LIMIT 1);

INSERT INTO clientes_direcciones (cliente_id, direccion, lat, lng, referencia, created_at)
VALUES
(@cli_juan, 'Av. Corrientes 1234, CABA', -34.60370, -58.38160, 'Piso 5 Dpto B', NOW()),
(@cli_maria, 'Av. Rivadavia 4321, CABA', -34.60990, -58.38830, 'Casa puerta negra', NOW()),
(@cli_carlos, 'Cabildo 1500, CABA', -34.55900, -58.45600, 'Timbre 3', NOW());

SET @dir_juan = (SELECT id FROM clientes_direcciones WHERE cliente_id=@cli_juan ORDER BY id DESC LIMIT 1);
SET @dir_maria = (SELECT id FROM clientes_direcciones WHERE cliente_id=@cli_maria ORDER BY id DESC LIMIT 1);

INSERT INTO pedidos (restaurante_id, cliente_id, direccion_id, zona_id, tipo, estado, subtotal, costo_envio, total, observaciones, created_at)
VALUES
(@resto_id, @cli_juan, @dir_juan, @zona_centro, 'delivery', 'nuevo', 12500.00, 1800.00, 14300.00, 'Tocar timbre 2 veces', DATE_SUB(NOW(), INTERVAL 20 MINUTE)),
(@resto_id, @cli_maria, @dir_maria, @zona_centro, 'telefono', 'confirmado', 9800.00, 1800.00, 11600.00, 'Pedido tomado por operador', DATE_SUB(NOW(), INTERVAL 90 MINUTE));

SET @pedido_1 = (SELECT id FROM pedidos WHERE restaurante_id=@resto_id ORDER BY id ASC LIMIT 1);
SET @pedido_2 = (SELECT id FROM pedidos WHERE restaurante_id=@resto_id ORDER BY id DESC LIMIT 1);

INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, total)
VALUES
(@pedido_1, @prod_m, 'Pizza Muzzarella', 1, 10700.00, 10700.00),
(@pedido_1, @prod_g, 'Gaseosa 1.5L', 1, 1800.00, 1800.00),
(@pedido_2, @prod_n, 'Pizza Napolitana', 1, 11600.00, 11600.00);

SET @it_1 = (SELECT id FROM pedido_items WHERE pedido_id=@pedido_1 AND producto_id=@prod_m LIMIT 1);
SET @it_2 = (SELECT id FROM pedido_items WHERE pedido_id=@pedido_2 AND producto_id=@prod_n LIMIT 1);

INSERT INTO pedido_item_detalles (pedido_item_id, tipo, nombre, precio)
VALUES
(@it_1, 'variante', 'Grande', 2200.00),
(@it_1, 'modificador', 'Sin aceitunas', 0.00),
(@it_2, 'variante', 'Grande', 2400.00);

INSERT INTO pedido_estados_historial (pedido_id, estado, changed_at)
VALUES
(@pedido_1, 'nuevo', DATE_SUB(NOW(), INTERVAL 20 MINUTE)),
(@pedido_2, 'nuevo', DATE_SUB(NOW(), INTERVAL 95 MINUTE)),
(@pedido_2, 'confirmado', DATE_SUB(NOW(), INTERVAL 85 MINUTE));

INSERT INTO pagos (pedido_id, metodo, estado, monto, referencia_externa, created_at)
VALUES
(@pedido_1, 'contra_entrega', 'pendiente', 14300.00, NULL, DATE_SUB(NOW(), INTERVAL 20 MINUTE)),
(@pedido_2, 'mercadopago', 'aprobado', 11600.00, 'mp_dummy_001', DATE_SUB(NOW(), INTERVAL 80 MINUTE));

COMMIT;

-- =============================================================
-- LOGIN DEMO
-- URL: /app/login.php
-- superadmin@demo.com / admin123
-- admin@demo.com      / admin123
-- operador@demo.com   / admin123
-- repartidor@demo.com / admin123
-- =============================================================
