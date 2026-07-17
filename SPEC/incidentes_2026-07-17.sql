-- Incidentes de bugs corregidos + nuevo bug reportado
-- Fecha: 2026-07-17
-- Para ejecutar en producción: phpMyAdmin → plansalu_blog → incidentes → SQL

-- INC-1: Age input bug (CERRADO — fix commit 7a508ef)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Comparador: input de edad fuerza 18 o 65 (oninput rompe números de 2 dígitos)',
  'Al escribir edad de 2 dígitos (ej. 23), el oninput fuerza 18 en el primer dígito y 65 en el segundo. Tipo: number con min=18 max=65.',
  'bug', 'alta', 'cerrado', 'CodeWhale',
  'pages/planes/comparador.php',
  'Fix: cambiado oninput por onchange. Commit 7a508ef.',
  NOW()
);

-- INC-2: 404 rutas subdirectorio (CERRADO — fix commits 7a508ef + cb49f24)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  '404 en rutas desde subdirectorio /plansaludfacil_new/ (BASE_URL ausente en JS)',
  'hero_moderno, formulario_individual, formulario_familia y gracias usaban rutas absolutas hardcodeadas sin BASE_URL. Redirecciones JS apuntaban a raíz del dominio.',
  'bug', 'critica', 'cerrado', 'CodeWhale',
  'components/hero_moderno.php, formulario_individual.php, formulario_familia.php, pages/gracias.php',
  'Fix: inyectado const BASE_URL en JS de cada componente. Commits 7a508ef + cb49f24.',
  NOW()
);

-- INC-3: Home icon (CERRADO — fix commit cb49f24)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen, resolucion, fecha_cierre) VALUES (
  'Ícono Home dirige a /pages/BASE_URL/ (constante no definida en PHP)',
  'header.php usa <?= BASE_URL ?> pero si la página no pasa por el router, la constante no existe. PHP 7.x interpreta constantes no definidas como string literal.',
  'bug', 'critica', 'cerrado', 'CodeWhale',
  'layout/header.php, config.php',
  'Fix: autodetección de subdirectorio como fallback en header.php + config.php. Commit cb49f24.',
  NOW()
);

-- INC-4: saveQuote button error (ABIERTO — fix en próximo commit)
INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, estado, responsable, origen) VALUES (
  'Gracias: botón Guardar Cotización arroja Error (ruta /api/cotizar.php sin BASE_URL)',
  'saveQuote() en gracias.php llama fetch("/api/cotizar.php") con ruta absoluta sin BASE_URL. En subdirectorio debería ser /plansaludfacil_new/api/cotizar.php.',
  'bug', 'alta', 'abierto', 'CodeWhale',
  'pages/gracias.php:565'
);
