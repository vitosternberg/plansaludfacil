-- Gestión de Incidentes Interna - Plan Salud Fácil
-- Tabla para tracking de issues, bugs y mejoras

CREATE TABLE IF NOT EXISTS `incidentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` enum('bug','mejora','tarea','riesgo') NOT NULL DEFAULT 'tarea',
  `criticidad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `estado` enum('abierto','en_progreso','resuelto','cerrado') NOT NULL DEFAULT 'abierto',
  `responsable` varchar(100) DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL COMMENT 'URL, componente o lead_id relacionado',
  `resolucion` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_cierre` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_criticidad` (`criticidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de ejemplo
INSERT INTO `incidentes` (`titulo`, `descripcion`, `categoria`, `criticidad`, `estado`, `origen`) VALUES
('Timeout SMTP en formulario de contacto', 'El envío de correo desde procesar_formularios.php tarda 25s en localhost porque intenta conectar a mail.plansaludfacil.cl', 'bug', 'alta', 'resuelto', 'procesar_formularios.php'),
('Dashboard no accesible en entorno local', 'Las rutas /cliente/ y /dashboard/ no están mapeadas en el front controller', 'bug', 'media', 'abierto', 'index.php'),
('Formularios de asesoría usan componente incorrecto', 'cambio-de-isapre.php y evaluacion-preexistencias.php renderizan formulario_individual en vez de un form específico', 'bug', 'media', 'abierto', 'pages/asesoria/'),
('Falta tracking SMS en envíos de campaña', 'No existe mecanismo para rastrear apertura/click de SMS enviados a leads', 'mejora', 'baja', 'abierto', 'Omniflow'),
('Páginas de compañías usan contenido placeholder', 'consalud.php, nueva-masvida.php y vida-tres.php tienen contenido copiado de otras isapres', 'tarea', 'media', 'resuelto', 'pages/companias/'),
('Agregar Schema.org BreadcrumbList a todas las páginas', 'Mejora SEO implementando breadcrumbs estructurados en el layout', 'mejora', 'baja', 'abierto', 'layout/seo-page.php');
