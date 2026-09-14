<?php
/**
 * Ping de deploy + sincronización opcional del blog.
 * Uso: https://plansaludfacil.cl/_ping.php?sync_blog_rrss=1
 */
if (isset($_GET['sync_blog_rrss'])) {
    header('Content-Type: text/plain; charset=utf-8');

    $root = __DIR__;
    $source = $root . '/blog_isapre/wp-content/mu-plugins/psf-rrss.php';
    $wpLoad = $root . '/blog_isapre/wp-load.php';

    echo "PSF blog RRSS sync\n";
    echo "root: {$root}\n";

    if (!is_readable($source)) {
        http_response_code(500);
        exit("missing source: {$source}\n");
    }

    if (!is_readable($wpLoad)) {
        http_response_code(500);
        exit("missing wp-load: {$wpLoad}\n");
    }

    define('WP_USE_THEMES', false);
    require $wpLoad;

    if (!is_dir(WPMU_PLUGIN_DIR)) {
        mkdir(WPMU_PLUGIN_DIR, 0755, true);
    }

    $target = WPMU_PLUGIN_DIR . '/psf-rrss.php';
    $content = file_get_contents($source);
    if ($content === false || file_put_contents($target, $content) === false) {
        http_response_code(500);
        exit("failed writing {$target}\n");
    }

    $headerSource = $root . '/blog_isapre/wp-content/themes/psf-child/parts/header.html';
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

    echo "OK mu-plugin: {$target} (" . filesize($target) . " bytes)\n";
    echo "v1.2=" . (str_contains($content, 'psf-blog-rrss-fix') ? 'yes' : 'no') . "\n";
    echo "header: {$headerTarget}\n";
    exit;
}

echo 'OK - deploy funcionando';
