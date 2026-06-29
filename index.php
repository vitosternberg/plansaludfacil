<?php
// index.php (Front Controller / Router)

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

// Autodetectar si el sitio corre en una subcarpeta usando DOCUMENT_ROOT (100% fiable en cPanel/XAMPP)
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

// Redirecciones 301 para SEO (de URLs antiguas a nuevas estructuras de Silo)
$redirects = [
    '/index.php' => '/',
    '/contacto.php' => '/nosotros/empresa',
    '/cotizador.php' => '/servicios/planes-individuales',
    '/guia_definitiva_eleccion_isapre.php' => '/servicios/cambio-de-isapre',
    '/guia_definitiva_eleccion_isapre_profesionales.php' => '/servicios/planes-profesionales',
    '/politicaPrivacidad.php' => '/nosotros/privacidad',
    '/gracias.php' => '/gracias'
];

if (array_key_exists($path, $redirects)) {
    header("HTTP/1.1 301 Moved Permanently");
    // Añadimos BASE_URL para que no redirija a la raíz del dominio si está en subcarpeta
    header("Location: " . BASE_URL . $redirects[$path]);
    exit();
}

// Mapeo de rutas a archivos de vista
$routes = [
    '/' => 'pages/home.php',
    '/nosotros/empresa' => 'pages/nosotros/empresa.php',
    '/nosotros/privacidad' => 'pages/nosotros/privacidad.php',
    '/servicios/planes-individuales' => 'pages/servicios/planes-individuales.php',
    '/servicios/planes-profesionales' => 'pages/servicios/planes-profesionales.php',
    '/servicios/cambio-de-isapre' => 'pages/servicios/cambio-de-isapre.php',
    '/servicios/planes-familia' => 'pages/servicios/planes-familia.php',
    '/servicios/planes-monoparental' => 'pages/servicios/planes-monoparental.php',
    '/gracias' => 'pages/gracias.php'
];

// Generador de Sitemap Dinámico
if ($path === '/sitemap.xml') {
    require_once __DIR__ . '/core/sitemap.php';
    exit();
}

// Comprobar si la ruta exacta existe
if (array_key_exists($path, $routes)) {
    $file = $routes[$path];
    if (file_exists($file)) {
        require_once $file;
    } else {
        http_response_code(404);
        echo "404 - Vista no encontrada (".$file.")";
    }
} else {
    // Lógica para subcategorías dinámicas del blog o rutas no mapeadas
    // Aquí podemos expandir más adelante para atrapar todo lo de /blog/
    
    // Si no es ninguna de las anteriores, devolver 404 real
    // En el futuro, aquí cargaremos un layout/404.php
    http_response_code(404);
    echo "<h1>404 - Página no encontrada</h1><p>La ruta $path no existe.</p>";
}
