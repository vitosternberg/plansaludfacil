-- ============================================================
-- PlanSaludFácil — Script de despliegue a producción
-- Ejecutar en: plansalu_blog (phpMyAdmin o consola MySQL)
-- ============================================================

-- 1. Tabla para guardar contactos de WhatsApp
CREATE TABLE IF NOT EXISTS `whatsapp_contacts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `phone` varchar(9) NOT NULL,
    `name` varchar(255) NOT NULL,
    `date_created` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabla para log de visitas generales (analytics)
CREATE TABLE IF NOT EXISTS `log_visitas_generales` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `url_visitada` varchar(500) DEFAULT NULL,
    `fecha_visita` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_url` (`url_visitada`(191)),
    KEY `idx_fecha` (`fecha_visita`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabla para tipos de formulario (requerida por procesar_formularios)
CREATE TABLE IF NOT EXISTS `tipos_formulario` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL,
    `descripcion` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar tipo de formulario por defecto si no existe
INSERT IGNORE INTO `tipos_formulario` (`id`, `nombre`, `descripcion`) VALUES 
(1, 'Contacto General', 'Formulario de contacto del sitio web');
