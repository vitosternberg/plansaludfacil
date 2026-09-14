<?php
/**
 * Instalador one-shot de RRSS para el blog.
 * Visitar: https://plansaludfacil.cl/blog_isapre/install-rrss.php
 * Eliminar este archivo después de usarlo.
 */
header('Content-Type: text/plain; charset=utf-8');

$root = __DIR__;
$wpLoad = $root . '/wp-load.php';

if (!is_readable($wpLoad)) {
    http_response_code(500);
    exit("No se encontró wp-load.php en {$root}\n");
}

define('WP_USE_THEMES', false);
require $wpLoad;

$source = WP_CONTENT_DIR . '/mu-plugins/psf-rrss.php';
$target = WPMU_PLUGIN_DIR . '/psf-rrss.php';

if (!is_dir(WPMU_PLUGIN_DIR)) {
    mkdir(WPMU_PLUGIN_DIR, 0755, true);
}

$repoSource = $root . '/wp-content/mu-plugins/psf-rrss.php';
if (!is_readable($source) && is_readable($repoSource)) {
    $source = $repoSource;
}

if (!is_readable($source)) {
    http_response_code(500);
    exit("No se encontró psf-rrss.php en {$source}\n");
}

$content = file_get_contents($source);
if ($content === false || file_put_contents($target, $content) === false) {
    http_response_code(500);
    exit("No se pudo escribir {$target}\n");
}

$headerSource = $root . '/wp-content/themes/psf-child/parts/header.html';
$headerTarget = get_stylesheet_directory() . '/parts/header.html';
if (is_readable($headerSource)) {
    copy($headerSource, $headerTarget);
}

if (function_exists('litespeed_purge_all')) {
    litespeed_purge_all();
}

if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

echo "OK: RRSS instalado en el blog\n";
echo "mu-plugin: {$target} (" . filesize($target) . " bytes)\n";
echo "header: {$headerTarget}\n";
echo "Elimina install-rrss.php del servidor.\n";
