<?php
// index.php (Front Controller / Router)

// Polyfills para compatibilidad PHP 7.x
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return substr($haystack, -strlen($needle)) === $needle;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return strpos($haystack, $needle) === 0;
    }
}

// Soporte para servidor integrado de PHP
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file(__DIR__ . $path)) {
        return false;
    }
}

// Cargar helpers del sistema (motor de componentes)
require_once __DIR__ . '/core/helpers.php';

$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'];

// Autodetectar si el sitio corre en una subcarpeta
$doc_root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
$dir = str_replace('\\', '/', __DIR__);
$base_path = str_replace($doc_root, '', $dir);
if ($base_path === '') $base_path = '/';

$base_url = $base_path === '/' ? '' : $base_path;
define('BASE_URL', $base_url);

if ($base_path !== '/' && strpos($path, $base_path) === 0) {
    $path = substr($path, strlen($base_path));
}
if ($path === '' || $path === false) {
    $path = '/';
}

// Normalizar: quitar slash final (excepto raíz)
if ($path !== '/' && str_ends_with($path, '/')) {
    $path = rtrim($path, '/');
}

// ─── Redirecciones 301 (URLs antiguas → nuevas) ───
$redirects = [
    // Archivos PHP antiguos
    '/index.php' => '/',
    '/contacto.php' => '/nosotros/empresa',
    '/guia_definitiva_eleccion_isapre.php' => '/asesoria/cambio-de-isapre/',
    '/politicaPrivacidad.php' => '/privacidad',
    '/gracias.php' => '/gracias',
    // Servicios (antiguo)
    '/servicios/planes-individuales' => '/planes/individuales/',
    '/servicios/planes-familia' => '/planes/familiares/',
    '/servicios/cambio-de-isapre' => '/asesoria/cambio-de-isapre/',
    '/servicios/planes-monoparental' => '/planes/familiares/monoparentales/',
    // Isapre → Isapres
    '/isapre/mejores-isapres' => '/isapres/',
    '/isapre' => '/isapres/',
    // Compañías antiguas → nuevas
    '/companias' => '/isapres/companias/',
    '/companias/banmedica' => '/isapres/companias/banmedica/',
    '/companias/colmena' => '/isapres/companias/colmena/',
    '/companias/cruz-blanca' => '/isapres/companias/cruz-blanca/',
    '/companias/consalud' => '/isapres/companias/consalud/',
    '/companias/nueva-masvida' => '/isapres/companias/nueva-masvida/',
    '/companias/vida-tres' => '/isapres/companias/vida-tres/',
    '/isapre/cambio-de-isapre' => '/asesoria/cambio-de-isapre/',
    '/isapre/cambio-de-isapre-a-otra' => '/asesoria/cambio-de-isapre/',
    '/isapre/cambio-de-isapre-preexistencia' => '/asesoria/evaluacion-preexistencias/',
    // Planes (antiguo)
    '/isapre/cambio-de-isapre-embarazada' => '/planes/familiares/maternidad/',
    '/planes/individuales/adulto' => '/planes/individuales/',
    '/planes/individuales/adultos' => '/planes/individuales/',
    '/planes/individuales/deportista' => '/planes/individuales/deportistas/',
    '/planes/familiares/preferencia-natal' => '/planes/familiares/maternidad/',
    // Rutas con # → rutas / (corrección SEO)
    '/isapres/cambio-de-isapre' => '/asesoria/cambio-de-isapre/',
    '/isapres/cambio-de-isapre/a-otra-isapre' => '/asesoria/cambio-de-isapre/',
    '/isapres/cambio-de-isapre/con-preexistencia' => '/asesoria/evaluacion-preexistencias/',
    // Otras
    '/nosotros/privacidad' => '/privacidad',
    '/isapres/mejores-isapres' => '/isapres/companias/',
];

if (array_key_exists($path, $redirects)) {
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    $location = BASE_URL . $redirects[$path];
    if (!empty($qs)) $location .= '?' . $qs;
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: " . $location);
    exit();
}

// ─── Mapeo de rutas activas ───
$routes = [
    // Home
    '/' => 'pages/home.php',

    // Corporativo
    '/clasificar' => 'pages/clasificar.php',
    '/reporte-keywords' => 'pages/reporte-keywords.php',
    '/nosotros' => 'pages/nosotros.php',
    '/nosotros/empresa' => 'pages/nosotros.php',
    '/privacidad' => 'pages/nosotros/privacidad.php',
    '/preguntas-frecuentes' => 'pages/preguntas-frecuentes.php',
    '/gracias' => 'pages/gracias.php',

    // ── ISAPRES (Hub educativo) ──
    '/isapres' => 'pages/isapre/index.php',
    '/isapres/que-es' => 'pages/isapre/que-es.php',
    '/isapres/como-funciona' => 'pages/isapre/como-funciona.php',
    '/isapres/fonasa-vs-isapre' => 'pages/isapre/fonasa-vs-isapre.php',

    // ── PLANES ISAPRE (Hub transaccional) ──
    '/planes' => 'pages/planes/index.php',
    '/planes/individuales' => 'pages/planes/individuales/index.php',
    '/planes/individuales/jovenes' => 'pages/planes/individuales/jovenes.php',
    '/planes/individuales/adultos' => 'pages/planes/individuales/index.php',
    '/planes/individuales/deportistas' => 'pages/planes/individuales/deportista.php',
    '/planes/individuales/adulto-mayor' => 'pages/planes/individuales/adulto-mayor.php',
    '/planes/familiares' => 'pages/planes/familiares/index.php',
    '/planes/familiares/con-cargas' => 'pages/planes/familiares/index.php',
    '/planes/familiares/monoparentales' => 'pages/planes/familiares/monoparentales.php',
    '/planes/familiares/maternidad' => 'pages/planes/familiares/preferencia-natal.php',
    '/planes/detalle' => 'pages/planes/detalle.php',
    '/planes/comparador' => 'pages/planes/comparador.php',

    // ── ISAPRES > Compañías (sub-nivel: marcas/empresas) ──
    '/isapres/companias' => 'pages/companias/index.php',
    '/isapres/companias/banmedica' => 'pages/companias/banmedica.php',
    '/isapres/companias/colmena' => 'pages/companias/colmena.php',
    '/isapres/companias/cruz-blanca' => 'pages/companias/cruz-blanca.php',
    '/isapres/companias/consalud' => 'pages/companias/consalud.php',
    '/isapres/companias/nueva-masvida' => 'pages/companias/nueva-masvida.php',
    '/isapres/companias/vida-tres' => 'pages/companias/vida-tres.php',

    // ── ASESORÍA (Hub de servicios) ──
    '/asesoria' => 'pages/asesoria/index.php',
    '/asesoria/cambio-de-isapre' => 'pages/asesoria/cambio-de-isapre.php',
    '/asesoria/evaluacion-preexistencias' => 'pages/asesoria/evaluacion-preexistencias.php',
    '/asesoria/optimizar-7-porciento' => 'pages/asesoria/optimizar-7-porciento.php',

    // ── INTERNO ──
    '/incidentes' => 'pages/incidentes.php',
];

// Generador de Sitemap Dinámico
if ($path === '/sitemap.xml') {
    require_once __DIR__ . '/core/sitemap.php';
    exit();
}

// Robots.txt dinámico
if ($path === '/robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nAllow: /\nDisallow: /api/\nDisallow: /cliente/\n\nSitemap: https://plansaludfacil.cl/sitemap.xml\n";
    exit();
}

if (array_key_exists($path, $routes)) {
    $file = $routes[$path];
    if (file_exists($file)) {
        require_once $file;
    } else {
        http_response_code(404);
        echo "404 - Vista no encontrada";
    }
} else {
    http_response_code(404);
    echo "<h1>404 - Página no encontrada</h1>";
}
