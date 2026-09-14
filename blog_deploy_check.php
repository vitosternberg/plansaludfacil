<?php
header('Content-Type: text/plain; charset=utf-8');

echo "PSF blog deploy check\n";
echo "time: " . date('c') . "\n";
echo "dir: " . __DIR__ . "\n\n";

$paths = [
    'blog_isapre/PSF_DEPLOY_VERSION.txt',
    'blog_isapre/install-rrss.php',
    'blog_isapre/wp-content/mu-plugins/psf-rrss.php',
    'blog_isapre/wp-content/themes/psf-child/parts/header.html',
    '_ping.php',
];

foreach ($paths as $relative) {
    $full = __DIR__ . '/' . $relative;
    echo $relative . ': ';
    if (!is_readable($full)) {
        echo "MISSING\n";
        continue;
    }
    $size = filesize($full);
    echo $size . ' bytes, mtime ' . date('c', filemtime($full));
    if (str_ends_with($relative, 'psf-rrss.php')) {
        $content = file_get_contents($full);
        echo ', v1.2=' . (str_contains($content, 'psf-blog-rrss-fix') ? 'yes' : 'no');
    }
    if (str_ends_with($relative, 'header.html')) {
        $content = file_get_contents($full);
        echo ', tiktok=' . (str_contains($content, 'tiktok') ? 'yes' : 'no');
    }
    echo "\n";
}

$wpLoad = __DIR__ . '/blog_isapre/wp-load.php';
if (is_readable($wpLoad)) {
    define('WP_USE_THEMES', false);
    require $wpLoad;
    echo "\nWP_CONTENT_DIR: " . WP_CONTENT_DIR . "\n";
    echo "WPMU_PLUGIN_DIR: " . WPMU_PLUGIN_DIR . "\n";
    $live = WPMU_PLUGIN_DIR . '/psf-rrss.php';
    if (is_readable($live)) {
        echo "live mu-plugin: " . filesize($live) . " bytes\n";
    }
}
