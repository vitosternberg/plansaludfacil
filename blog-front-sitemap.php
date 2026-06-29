<?php
/**
 * Sitemap XML dinámico para el blog (cliente/blog-front)
 * Genera listado de todos los posts con lastmod y priority
 * Acceso: /blog-front-sitemap.php
 */

header('Content-Type: application/xml; charset=utf-8');
require_once __DIR__ . '/blog-plansaludfacil/config.php';

// Construir base URL detectando esquema y host
$host_header = $_SERVER['HTTP_HOST'] ?? SITE_URL;
$host_no_port = preg_replace('/:\d+$/', '', $host_header);
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
$scheme = $is_https ? 'https' : 'http';
$base_url = $scheme . '://' . $host_no_port . '/blog-plansaludfacil';

try {
    $db = get_db_connection();
    $posts = $db->query("SELECT slug, created_at FROM posts WHERE slug IS NOT NULL AND slug != '' ORDER BY created_at DESC");

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // Página principal del blog
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($base_url . '/') . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>1.0</priority>' . "\n";
    echo '  </url>' . "\n";

    // Posts
    if ($posts && $posts->num_rows > 0) {
        while ($post = $posts->fetch_assoc()) {
            echo '  <url>' . "\n";
            echo '    <loc>' . htmlspecialchars($base_url . '/?post=' . urlencode($post['slug'])) . '</loc>' . "\n";
            echo '    <lastmod>' . date('Y-m-d', strtotime($post['created_at'])) . '</lastmod>' . "\n";
            echo '    <changefreq>monthly</changefreq>' . "\n";
            echo '    <priority>0.8</priority>' . "\n";
            echo '  </url>' . "\n";
        }
    }

    echo '</urlset>';

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
    error_log('Sitemap error: ' . $e->getMessage());
}
?>
