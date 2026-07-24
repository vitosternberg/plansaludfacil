<?php
/**
 * Generador dinámico de Sitemap XML
 * Se alimenta de las rutas definidas en $routes en index.php.
 */

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$base_url = 'https://' . $domainName; // forzar HTTPS

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// Páginas internas que no deben indexarse
$noindex = ['/gracias', '/incidentes', '/clasificar', '/reporte-keywords', '/planes/detalle'];

// $routes viene incluido desde index.php
foreach ($routes as $path => $view_file) {
    $skip = false;
    foreach ($noindex as $ni) {
        if (strpos($path, $ni) !== false) { $skip = true; break; }
    }
    if ($skip) continue;

    $url = $base_url . $path;
    $date = date('Y-m-d');

    // Prioridad y frecuencia según tipo de página
    if ($path === '/') {
        $priority = '1.0'; $changefreq = 'daily';
    } elseif (strpos($path, '/planes/') === 0 || strpos($path, '/asesoria/') === 0) {
        $priority = '0.9'; $changefreq = 'weekly';
    } elseif (strpos($path, '/isapres/companias/') === 0) {
        $priority = '0.8'; $changefreq = 'monthly';
    } elseif (strpos($path, '/isapres/') === 0 || strpos($path, '/isapre') === 0) {
        $priority = '0.8'; $changefreq = 'monthly';
    } elseif (strpos($path, '/nosotros') === 0 || $path === '/privacidad' || $path === '/preguntas-frecuentes') {
        $priority = '0.5'; $changefreq = 'monthly';
    } else {
        $priority = '0.7'; $changefreq = 'weekly';
    }

    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . $date . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
    echo '    <priority>' . $priority . '</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// ── Blog posts (referencia al sitemap de WordPress) ──
echo '  <!-- El blog está en /blog_isapre/ con su propio sitemap: ' . $base_url . '/blog_isapre/sitemap.xml -->' . PHP_EOL;

echo '</urlset>' . PHP_EOL;
