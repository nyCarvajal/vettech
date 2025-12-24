-- Definiciones de tablas para el inquilino (tenant)
-- Tablas: tipo_identificacions, items, areas, departamentos, municipios

-- Tabla: tipo_identificacions
CREATE TABLE `tipo_identificacions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: items
CREATE TABLE `items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(300) NOT NULL,
  `cantidad` INT NULL,
  `costo` DECIMAL(10,2) NULL,
  `valor` DECIMAL(10,2) NULL,
  `tipo` INT NULL,
  `area` INT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: areas
CREATE TABLE `areas` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: departamentos
CREATE TABLE `departamentos` (
  `id` BIGINT UNSIGNED NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `codigo` INT NOT NULL,
  `pais_id` INT NULL DEFAULT 52,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: municipios (almacena el departamento al que pertenece)
CREATE TABLE `municipios` (
  `id` BIGINT UNSIGNED NOT NULL,
  `departamento_id` BIGINT UNSIGNED NOT NULL,
  `codigo` INT NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `municipios_departamento_fk` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
