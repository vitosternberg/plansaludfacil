<?php
/**
 * JSON-LD @graph único por página.
 */
require_once __DIR__ . '/geo_facts.php';
if (is_readable(__DIR__ . '/social_profiles.php')) {
    require_once __DIR__ . '/social_profiles.php';
} elseif (!function_exists('psf_social_urls')) {
    function psf_social_urls(): array
    {
        return [
            'https://www.instagram.com/plansaludfacil/',
            'https://www.tiktok.com/@plansaludfacil',
            'https://www.facebook.com/profile.php?id=61594587830939',
        ];
    }
}

if (!function_exists('psf_schema_emit')) {
function psf_schema_emit(): void
{
    $origin = psf_site_origin();
    $page_url = psf_canonical_url();
    $org_id = $origin . '/#org';
    $website_id = $origin . '/#website';
    $page_id = $page_url . '#webpage';

    $same_as = array_merge(psf_social_urls(), ['https://wa.me/56952282339']);

    $graph = [
        [
            '@type' => 'Organization',
            '@id' => $org_id,
            'name' => 'Plan Salud Fácil',
            'url' => $origin . '/',
            'logo' => $origin . '/img/logo.png',
            'description' => 'Comparador de planes de Isapre. Datos de Superintendencia de Salud y asesoría gratuita.',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+56 9 5228 2339',
                'contactType' => 'customer service',
                'availableLanguage' => 'Spanish',
                'areaServed' => 'CL',
            ],
            'sameAs' => $same_as,
        ],
        [
            '@type' => 'WebSite',
            '@id' => $website_id,
            'name' => 'Plan Salud Fácil',
            'url' => $origin . '/',
            'publisher' => ['@id' => $org_id],
            'inLanguage' => 'es-CL',
        ],
    ];

    $page_type = $GLOBALS['schema_page_type'] ?? 'WebPage';
    $headline = $GLOBALS['h1'] ?? ($GLOBALS['page_title'] ?? 'Plan Salud Fácil');
    $description = $GLOBALS['meta_description'] ?? '';
    $allowed = ['TechArticle', 'Service', 'FAQPage', 'WebPage'];
    if (!in_array($page_type, $allowed, true)) {
        $page_type = 'WebPage';
    }

    $page_node = [
        '@type' => $page_type,
        '@id' => $page_id,
        'url' => $page_url,
        'name' => $headline,
        'headline' => $headline,
        'description' => $description,
        'inLanguage' => 'es-CL',
        'isPartOf' => ['@id' => $website_id],
        'publisher' => ['@id' => $org_id],
        'author' => ['@id' => $org_id],
        'about' => [
            [
                '@type' => 'Thing',
                'name' => 'Isapre',
                'sameAs' => psf_geo_citations()['isapre_wikidata']['url'],
            ],
            [
                '@type' => 'Organization',
                'name' => 'Superintendencia de Salud',
                'sameAs' => psf_geo_citations()['supersalud']['url'],
            ],
        ],
        'citation' => psf_cite_urls(['supersalud', 'ges_caec', 'ley_corta']),
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Chile',
        ],
    ];

    if ($page_type === 'Service') {
        $page_node['provider'] = ['@id' => $org_id];
        $page_node['serviceType'] = $GLOBALS['svc_name'] ?? 'Comparador de planes de Isapre';
        $page_node['category'] = $GLOBALS['svc_category'] ?? 'Planes de ISAPRE';
        unset($page_node['headline']);
    }

    if ($page_type === 'FAQPage') {
        $faq_entities = $GLOBALS['schema_faq_entities'] ?? [];
        if (!empty($faq_entities)) {
            $page_node['mainEntity'] = $faq_entities;
        }
        $page_node['citation'] = psf_cite_urls(['supersalud', 'ges_caec']);
    }

    $graph[] = $page_node;

    $breadcrumbs = $GLOBALS['breadcrumbs'] ?? [];
    if (!empty($breadcrumbs) && is_array($breadcrumbs)) {
        $items = [];
        $pos = 1;
        foreach ($breadcrumbs as $bc) {
            $item_url = $bc['url'] ?? $page_url;
            if ($item_url === '#' || $item_url === '') {
                $item_url = $page_url;
            } elseif (!preg_match('#^https?://#', $item_url)) {
                $item_url = rtrim($origin, '/') . '/' . ltrim($item_url, '/');
            }
            $items[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $bc['label'] ?? '',
                'item' => rtrim($item_url, '/') ?: $origin,
            ];
        }
        $graph[] = [
            '@type' => 'BreadcrumbList',
            '@id' => $page_url . '#breadcrumb',
            'itemListElement' => $items,
        ];
    }

    echo '<script type="application/ld+json">' . "\n";
    echo json_encode(
        [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
    );
    echo "\n</script>\n";
}
}
