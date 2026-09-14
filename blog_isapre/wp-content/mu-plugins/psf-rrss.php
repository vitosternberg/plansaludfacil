<?php
/**
 * Plugin Name: PSF RRSS Links
 * Description: Enlaces oficiales de redes sociales en el blog Plan Salud Fácil.
 * Version: 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

function psf_blog_social_profiles(): array
{
    return [
        [
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/profile.php?id=61594587830939',
            'class' => 'facebook',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14 8h3V4h-3c-2.8 0-5 2.2-5 5v3H6v4h3v8h4v-8h3.2l.8-4H13V9c0-.6.4-1 1-1z"/></svg>',
        ],
        [
            'name' => 'Instagram',
            'url' => 'https://www.instagram.com/plansaludfacil/',
            'class' => 'instagram',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm10 2H7a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm-5 3.2A3.8 3.8 0 1 1 8.2 12 3.8 3.8 0 0 1 12 8.2zm0 1.6A2.2 2.2 0 1 0 14.2 12 2.2 2.2 0 0 0 12 9.8zM17.4 6.6a1 1 0 1 1-1 1 1 1 0 0 1 1-1z"/></svg>',
        ],
        [
            'name' => 'TikTok',
            'url' => 'https://www.tiktok.com/@plansaludfacil',
            'class' => 'tiktok',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path fill="currentColor" d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>',
        ],
    ];
}

function psf_blog_follow_markup(): string
{
    $links = '';
    foreach (psf_blog_social_profiles() as $profile) {
        $links .= sprintf(
            '<a class="ri-blog-follow__icon ri-blog-follow__icon--%1$s" href="%2$s" target="_blank" rel="noopener noreferrer" aria-label="%3$s">%4$s</a>',
            esc_attr($profile['class']),
            esc_url($profile['url']),
            esc_attr($profile['name']),
            $profile['icon']
        );
    }

    return '<div class="ri-blog-follow" data-psf-rrss="1" aria-label="Sígueme en redes sociales"><span class="ri-blog-follow__label">Sígueme</span>' . $links . '</div>';
}

function psf_blog_follow_css(): string
{
    return <<<'CSS'
.ri-blog-follow{display:inline-flex;align-items:center;gap:10px;margin:0;padding:0;white-space:nowrap;height:40px}
.ri-blog-follow__label{font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#0369a1}
.ri-blog-follow__icon{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:999px;background:#0369a1;border:1px solid #0369a1;color:#f0f9ff!important;text-decoration:none;transition:background .2s,border-color .2s,color .2s}
.ri-blog-follow__icon svg,.ri-blog-follow__icon svg path{fill:currentColor!important;color:inherit}
.ri-blog-follow__icon:hover,.ri-blog-follow__icon:focus-visible{background:#0284c7;border-color:#0284c7;color:#fff!important}
header .ri-blog-follow__icon,footer .ri-blog-follow__icon{color:#f0f9ff!important}
header .ri-blog-follow__icon:visited,footer .ri-blog-follow__icon:visited{color:#f0f9ff!important}
header .ri-blog-follow{margin-left:0}
footer .ri-blog-follow{margin-top:8px}
CSS;
}

function psf_blog_replace_follow_blocks(string $html): string
{
    if (strpos($html, 'ri-blog-follow') === false) {
        return $html;
    }

    $markup = psf_blog_follow_markup();

    $html = preg_replace(
        '/<div class="ri-blog-follow"[^>]*>.*?<\/div>/s',
        $markup,
        $html
    ) ?? $html;

    if (strpos($html, 'ri-blog-header-right') !== false && substr_count($html, 'data-psf-rrss="1"') < 1) {
        $html = preg_replace(
            '/(<div class="wp-block-group ri-blog-header-right[^"]*"[^>]*>)(\s*)/',
            '$1' . $markup . '$2',
            $html,
            1
        ) ?? $html;
    }

    if (strpos($html, '<footer') !== false && substr_count($html, 'data-psf-rrss="1"') < 2) {
        $html = preg_replace(
            '/(<footer[^>]*>)/',
            '$1' . $markup,
            $html,
            1
        ) ?? $html;
    }

    return $html;
}

function psf_blog_rrss_buffer_start(): void
{
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }

    ob_start('psf_blog_replace_follow_blocks');
}

add_action('init', 'psf_blog_rrss_buffer_start', 0);

add_action('wp_head', function (): void {
    echo '<style id="psf-blog-follow-css">' . psf_blog_follow_css() . '</style>';
}, 20);

add_filter('render_block', function (string $block_content, array $block = []): string {
    $block_content = psf_blog_replace_follow_blocks($block_content);

    $class_name = $block['attrs']['className'] ?? '';
    if ($class_name && strpos($class_name, 'ri-blog-header-right') !== false && strpos($block_content, 'data-psf-rrss="1"') === false) {
        $block_content = preg_replace(
            '/(<div class="wp-block-group ri-blog-header-right[^"]*"[^>]*>)/',
            '$1' . psf_blog_follow_markup(),
            $block_content,
            1
        ) ?? $block_content;
    }

    if (($block['blockName'] ?? '') === 'core/template-part' && ($block['attrs']['slug'] ?? '') === 'footer' && strpos($block_content, 'data-psf-rrss="1"') === false) {
        $block_content .= psf_blog_follow_markup();
    }

    return $block_content;
}, 100, 2);

add_action('wp_footer', function (): void {
    $profiles = array_map(
        static fn(array $profile): array => [
            'class' => $profile['class'],
            'url' => $profile['url'],
            'name' => $profile['name'],
            'icon' => $profile['icon'],
        ],
        psf_blog_social_profiles()
    );
    ?>
    <script id="psf-blog-rrss-fix">
    (function () {
        var profiles = <?php echo wp_json_encode($profiles); ?>;
        function patchFollowBlocks() {
            document.querySelectorAll('.ri-blog-follow').forEach(function (box) {
                if (box.getAttribute('data-psf-rrss') === '1') {
                    return;
                }
                var label = box.querySelector('.ri-blog-follow__label');
                var labelHtml = label ? label.outerHTML : '<span class="ri-blog-follow__label">Sígueme</span>';
                var links = profiles.map(function (profile) {
                    return '<a class="ri-blog-follow__icon ri-blog-follow__icon--' + profile.class + '" href="' + profile.url + '" target="_blank" rel="noopener noreferrer" aria-label="' + profile.name + '">' + profile.icon + '</a>';
                }).join('');
                box.innerHTML = labelHtml + links;
                box.setAttribute('data-psf-rrss', '1');
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', patchFollowBlocks);
        } else {
            patchFollowBlocks();
        }
    })();
    </script>
    <?php
}, 99);

add_action('init', static function (): void {
    $option = 'psf_rrss_version';
    $version = '1.2.0';
    if (get_option($option) === $version) {
        return;
    }
    update_option($option, $version, false);
    if (function_exists('litespeed_purge_all')) {
        litespeed_purge_all();
    }
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
}, 999);
