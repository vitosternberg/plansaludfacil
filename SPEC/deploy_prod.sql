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

-- ============================================================
-- 4. Plataforma de Incidentes (tabla + issues cerrados 2026-07-17)
-- ============================================================

CREATE TABLE IF NOT EXISTS `incidentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` enum('bug','mejora','tarea','riesgo') NOT NULL DEFAULT 'tarea',
  `criticidad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `estado` enum('abierto','en_progreso','resuelto','cerrado') NOT NULL DEFAULT 'abierto',
  `responsable` varchar(100) DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL COMMENT 'URL, componente o lead_id',
  `resolucion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_criticidad` (`criticidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- INC-1: Age input bug (commit 7a508ef)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Comparador: input de edad fuerza 18 o 65 (oninput rompe números de 2 dígitos)',
  'Al escribir edad de 2 dígitos, el oninput fuerza 18 en el primer dígito y 65 en el segundo.',
  'bug', 'alta', 'cerrado', 'CodeWhale',
  'pages/planes/comparador.php',
  'Fix: cambiado oninput por onchange. Commit 7a508ef.',
  NOW()
);

-- INC-2: 404 rutas subdirectorio (commits 7a508ef + cb49f24)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  '404 en rutas desde subdirectorio /plansaludfacil_new/ (BASE_URL ausente en JS)',
  'hero_moderno, formularios y gracias usaban rutas absolutas sin BASE_URL. Redirecciones JS a raíz del dominio.',
  'bug', 'critica', 'cerrado', 'CodeWhale',
  'components/hero_moderno.php, formulario_individual.php, formulario_familia.php, pages/gracias.php',
  'Fix: inyectado const BASE_URL en JS de cada componente. Commits 7a508ef + cb49f24.',
  NOW()
);

-- INC-3: Home icon (commit cb49f24)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Ícono Home dirige a /pages/BASE_URL/ (constante no definida en PHP)',
  'header.php usa <?= BASE_URL ?> pero sin router la constante no existe. PHP 7.x la interpreta como string literal.',
  'bug', 'critica', 'cerrado', 'CodeWhale',
  'layout/header.php, config.php',
  'Fix: autodetección de subdirectorio en header.php + config.php. Commit cb49f24.',
  NOW()
);

-- INC-4: saveQuote ruta (commit c6f733a)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Gracias: botón Guardar Cotización arroja Error (ruta /api/cotizar.php sin BASE_URL)',
  'saveQuote() usaba fetch("/api/cotizar.php") sin BASE_URL. En subdirectorio daba 404.',
  'bug', 'alta', 'cerrado', 'CodeWhale',
  'pages/gracias.php:565',
  'Fix: cambiado a fetch(<?= BASE_URL ?>/api/cotizar.php). Commit c6f733a.',
  NOW()
);

-- INC-5: saveQuote email real (commits 94c46f3 + b1877be)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Gracias: botón Guardar Cotización no envía correo real (email nunca se manda)',
  'saveQuote() recolectaba email pero no lo enviaba al backend. api/cotizar.php no tenía lógica de envío.',
  'bug', 'alta', 'cerrado', 'CodeWhale',
  'pages/gracias.php:565, api/cotizar.php',
   'Fix: Frontend envía nombre+email. Backend usa PHPMailer vía omniflow_config.php con top 3 planes y botón WhatsApp verde. Commits 94c46f3 + b1877be.',
   NOW()
);

-- INC-6: FAQ respuestas imprecisas (commit 92e376c)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'FAQ: respuestas imprecisas sobre trabajadores independientes y declaración de preexistencias',
  '1) "¿Qué ISAPRE conviene para trabajadores independientes?" decía que existen planes especiales para independientes (falso en Chile: todos acceden a los mismos planes). 2) "¿Debo declarar enfermedades preexistentes?" era ambiguo, sugería que podía ser opcional (es obligatorio por ley).',
  'bug', 'media', 'cerrado', 'CodeWhale',
  'pages/preguntas-frecuentes.php',
  'Fix: corregidas ambas respuestas con información precisa. Commit 92e376c.',
  NOW()
);
