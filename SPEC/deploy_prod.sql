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
  `categoria` enum('bug','mejora','tarea','riesgo','CHR') NOT NULL DEFAULT 'tarea',
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

-- INC-7: WhatsApp sin captura de datos (CERRADO — commit b24d9d1)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Gracias: botón WhatsApp no captura nombre y teléfono antes de redirigir',
  'El botón de WhatsApp en gracias.php abría directamente wa.me sin pedir confirmación ni capturar datos del usuario.',
  'bug', 'alta', 'cerrado', 'CodeWhale',
  'pages/gracias.php:523,593',
  'Fix: agregado modal con campos nombre + teléfono, validación, guardado en BD y tracking antes de abrir WhatsApp. Commit b24d9d1.',
  NOW()
);

-- INC-8: Sección confianza — texto desactualizado (CERRADO — commit pendiente)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Gracias: sección "¿Por qué confiar?" dice 1.000 cotizaciones este año',
  'La sección de confianza mostraba "Más de 1.000 cotizaciones realizadas este año".',
  'CHR', 'baja', 'cerrado', 'CodeWhale',
  'pages/gracias.php:350',
  'CHR-01 (antes INC-8). Fix: cambiado a "Más de 2.000 evaluaciones cerradas en 15 años de experiencia de nuestros asesores". Commit pendiente.',
  NOW()
);

-- INC-9: Botones "Completar datos" eliminados por redundantes (CERRADO — commit 2ebb793)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Gracias: botones "Completar datos para Plan Individual" redundantes — el usuario ya viene de completar sus datos',
  'Los botones debajo de cada plan en gracias.php no tenían sentido porque el lead ya completó sus datos. Solo tendrían sentido en el comparador.',
  'CHR', 'baja', 'cerrado', 'CodeWhale',
  'pages/gracias.php:260-262',
  'CHR-02 (antes INC-9). Fix: eliminados los botones y las variables $formUrl/$formLabel. Commit 2ebb793.',
  NOW()
);

-- INC-10: Fusión index.php + adulto.php (CERRADO — commit pendiente)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'SEO: fusión de index.php + adulto.php — eliminado breadcrumb "Adultos" y página duplicada',
  'Breadcrumb decía "Adultos" (inexistente). Se fusionó contenido detallado de adulto.php dentro de index.php. Ruta /planes/individuales/adultos/ redirige a /planes/individuales/.',
  'CHR', 'media', 'cerrado', 'CodeWhale',
  'pages/planes/individuales/index.php, pages/planes/individuales/adulto.php, index.php',
  'CHR-03 (antes INC-10). Fix: index.php ahora contiene todo adulto.php + formulario + FAQ fusionado. Breadcrumb 3 niveles. Commit pendiente.',
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

-- INC-11: Estética aplicada al index fusionado (CERRADO — commit 0b0eb97)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'UX: adoptar estética de /servicios/planes-individuales.php en el index fusionado de /planes/individuales/',
  'El index fusionado ahora tiene: Schema.org FAQPage JSON-LD, secciones con tarjetas numeradas, answer-direct destacado, grid cards con íconos y degradados, y la jerarquía visual rica del original.',
  'CHR', 'media', 'cerrado', 'CodeWhale',
  'pages/planes/individuales/index.php',
   'CHR-04 (antes INC-11). Fix: reconstruido con 5 secciones card, Schema.org FAQ, answer-direct styling, grid de perfiles, coberturas detalladas y footer vía seo-page.php. Commit 0b0eb97.',
   NOW()
);

-- INC-12: Comparador — botones a #formulario con datos (CERRADO — commits a91f29f + b238211)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Comparador: botones "Completar datos" aterrizan en #formulario con datos del cotizador',
  'Los botones del comparador ahora enlazan a /individuales/ y /familiares/ con #formulario y query params (age, income, cargas). Probado y funcionando en producción.',
  'bug', 'alta', 'cerrado', 'CodeWhale',
  'pages/planes/comparador.php:185-187',
  'Fix: cambiado a rutas fusionadas con #formulario y datos. Commits a91f29f + b238211.',
  NOW()
);

-- INC-13: Hero — botón "Cotizar Express" (CERRADO — commits a91f29f + 190fed6 + 0eb7e3f + 39b0b87 + 5641f10 + 816fe48 + 1c30ef4)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Hero: botón verde "Cotizar Express" con validación, datos y scroll al formulario',
  'Hero ahora tiene 2 botones: "Buscar Planes" y "Cotizar Express". Con validación de campos, recolección de carga_edad[], params edad/renta, y #comparador al aterrizar.',
  'CHR', 'media', 'cerrado', 'CodeWhale',
  'components/hero_moderno.php:64-74',
  'CHR-05 (antes INC-13). Fix: 6 commits acumulados en hero_moderno.php. Validación, params, carga_edad[], scroll #comparador.',
  NOW()
);

-- INC-14: CTAs isapre → formulario/comparador (CERRADO — commit 139f00c)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'UX: CTAs redirigen a formulario/comparador + WhatsApp oculto en desktop',
  'Planes individuales/familiares: "Cotizar ahora" → #formulario. 22 páginas isapre/companias: "Cotiza Express" → /planes/comparador/. WhatsApp flotante con md:hidden.',
  'CHR', 'media', 'cerrado', 'CodeWhale',
  'pages/planes/individuales/index.php, pages/planes/familiares/index.php, ~22 páginas isapre/companias, components/cta_flotante.php',
   'CHR-06 (antes INC-14). Fix: 25 archivos modificados. CTAs estandarizados, WhatsApp solo mobile. Commit 139f00c.',
   NOW()
);

-- INC-15: Filtro regional en cotizador (CERRADO — commit 7939101)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Cotizador: filtrar planes por región del usuario (17% de planes son regionales)',
  '385 de 2,231 planes tienen restricción regional (SUR: 83, NORTE: 76, CENTRO: 226). Un usuario de Santiago no debería ver planes del Norte o Sur. Se implementó: (1) Taggeo de planes por región en el CSV, (2) Mapeo comuna→región con 90+ comunas, (3) Filtro en motor_cotizar.',
  'CHR', 'media', 'cerrado', 'CodeWhale',
  'core/cotizador_engine.php, adjuntos/planes_isapre.csv, scripts/enrich_planes.py',
   'CHR-07 (antes INC-15). Fix: columna region en CSV + función comuna_to_region() + filtro en motor_cotizar(). Commit 7939101.',
   NOW()
);

-- ============================================================
-- 5. Tabla para análisis de keywords (clasificador + reportes)
-- ============================================================

CREATE TABLE IF NOT EXISTS `analisis_busquedas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_keywords` int(11) NOT NULL DEFAULT 0,
  `total_transaccional` int(11) NOT NULL DEFAULT 0,
  `total_informativa` int(11) NOT NULL DEFAULT 0,
  `total_navegacion` int(11) NOT NULL DEFAULT 0,
  `keywords_input` text DEFAULT NULL COMMENT 'Keywords originales ingresadas',
  `resultados_json` longtext DEFAULT NULL COMMENT 'Resultados clasificados en JSON',
  PRIMARY KEY (`id`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
