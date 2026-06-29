<?php
/**
 * Generador dinámico de Sitemap XML
 * Se alimenta de las rutas definidas en $routes en index.php.
 */

// Usamos el host actual (o uno definido en config)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$base_url = $protocol . $domainName;

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// $routes viene incluido desde index.php
foreach ($routes as $path => $view_file) {
    // Evitar añadir páginas de gracias o políticas que no suelen indexarse como contenido principal
    if (strpos($path, 'gracias') !== false) {
        continue;
    }

    $url = $base_url . $path;
    $date = date('Y-m-d');
    
    // Determinar prioridad y frecuencia de cambio
    $priority = '0.8';
    $changefreq = 'weekly';
    
    if ($path === '/') {
        $priority = '1.0';
        $changefreq = 'daily';
    } elseif (strpos($path, '/servicios') === 0) {
        $priority = '0.9';
        $changefreq = 'weekly';
    }

    echo '  <url>' . PHP_EOL;
    echo '    <loc>' . htmlspecialchars($url) . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . $date . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
    echo '    <priority>' . $priority . '</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

echo '</urlset>' . PHP_EOL;
