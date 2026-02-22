-- Tabla de Gustos de Helado
CREATE TABLE IF NOT EXISTS `helado_gustos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `descripcion` VARCHAR(255),
  `color_hex` VARCHAR(7) DEFAULT '#8ddfff',
  `activo` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar gustos clásicos
INSERT INTO `helado_gustos` (`nombre`, `descripcion`, `color_hex`, `activo`) VALUES
('Vainilla', 'Clásico y cremoso', '#F3E5AB', 1),
('Chocolate', 'Chocolate belga intenso', '#6F4E37', 1),
('Frutilla', 'Frutilla natural y fresca', '#E75480', 1),
('Dulce de leche', 'Dulce de leche argentino', '#D2691E', 1),
('Pistacho', 'Pistacho premium', '#93C572', 1),
('Menta', 'Menta fresca y refrescante', '#98FF98', 1),
('Café', 'Café espresso intenso', '#8B4513', 1),
('Crema americana', 'Suave y clásica', '#F5DEB3', 1),
('Frambuesa', 'Frambuesa natural', '#E30B5C', 1),
('Limón', 'Limón agrio y refrescante', '#FFFF00', 1),
('Almendra', 'Almendra tostada', '#C5B358', 1),
('Menta-Chocolate', 'Combinación refrescante', '#6B8E23', 1);

-- Tabla de relación: Helado x Gusto (para productos helado)
-- Esta tabla permite que cada producto helado tenga gustos seleccionables
CREATE TABLE IF NOT EXISTS `producto_helado_gustos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `producto_id` INT NOT NULL,
  `gusto_id` INT NOT NULL,
  `disponible` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_producto_gusto` (`producto_id`, `gusto_id`),
  FOREIGN KEY (`gusto_id`) REFERENCES `helado_gustos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
