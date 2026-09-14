<?php
/**
 * Ping de deploy + sync de RRSS del blog (sin cargar WordPress).
 * Uso: https://plansaludfacil.cl/_ping.php?sync_blog_rrss=1
 */
if (isset($_GET['sync_blog_rrss'])) {
    header('Content-Type: text/plain; charset=utf-8');
    $root = __DIR__;
    echo "PSF blog RRSS sync\n";
    echo "root: {$root}\n";

    $source = $root . '/_psf_blog_functions.php';
    $themeDir = $root . '/blog_isapre/wp-content/themes/psf-child';
    $target = $themeDir . '/functions.php';
    $muDir = $root . '/blog_isapre/wp-content/mu-plugins';
    $muTarget = $muDir . '/psf-rrss.php';
    $headerSrc = $root . '/blog_isapre/wp-content/themes/psf-child/parts/header.html';

    echo 'source: ' . (is_readable($source) ? filesize($source) . ' bytes' : 'MISSING') . "\n";
    echo 'themeDir: ' . (is_dir($themeDir) ? 'ok' : 'MISSING') . "\n";
    echo 'current functions.php: ' . (is_readable($target) ? filesize($target) . ' bytes' : 'MISSING') . "\n";

    if (!is_readable($source)) {
        http_response_code(500);
        exit("missing {$source}\n");
    }
    if (!is_dir($themeDir)) {
        http_response_code(500);
        exit("missing theme dir {$themeDir}\n");
    }

    $content = file_get_contents($source);
    if ($content === false || file_put_contents($target, $content) === false) {
        http_response_code(500);
        exit("failed writing {$target}\n");
    }
    echo 'wrote functions.php: ' . filesize($target) . " bytes\n";

    if (!is_dir($muDir)) {
        mkdir($muDir, 0755, true);
    }
    if (is_dir($muDir)) {
        file_put_contents($muTarget, $content);
        echo 'wrote mu-plugin: ' . filesize($muTarget) . " bytes\n";
    }

    if (is_readable($headerSrc)) {
        echo 'header.html: ' . filesize($headerSrc) . ' bytes tiktok=' . (str_contains((string) file_get_contents($headerSrc), 'tiktok.com') ? 'yes' : 'no') . "\n";
    }

    echo "OK\n";
    exit;
}

echo 'OK - deploy funcionando';
