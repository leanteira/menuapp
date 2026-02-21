-- Stage 3: Zonas avanzadas (manual/radio/polígono)
-- Ejecutar sobre base: serv_mirestoapp

ALTER TABLE zonas_envio
  ADD COLUMN tipo_area ENUM('manual','radio','poligono') NOT NULL DEFAULT 'manual' AFTER activo,
  ADD COLUMN centro_lat DECIMAL(10,8) NULL AFTER tipo_area,
  ADD COLUMN centro_lng DECIMAL(11,8) NULL AFTER centro_lat,
  ADD COLUMN radio_metros DECIMAL(10,2) NULL AFTER centro_lng,
  ADD COLUMN poligono_json TEXT NULL AFTER radio_metros;
