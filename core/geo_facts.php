<?php
/**
 * Hechos y citas oficiales para GEO (fuente única).
 */

if (!function_exists('psf_canonical_url')) {
    function psf_canonical_url(): string
    {
        $path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $path = rtrim((string) $path, '/') ?: '/';
        $host = $_SERVER['HTTP_HOST'] ?? 'plansaludfacil.cl';
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        return $proto . '://' . $host . $path;
    }
}

if (!function_exists('psf_site_origin')) {
    function psf_site_origin(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'plansaludfacil.cl';
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        return $proto . '://' . $host;
    }
}

if (!function_exists('psf_geo_facts')) {
    function psf_geo_facts(): array
    {
        return [
            'ges_patologias' => 87,
            'isapres_abiertas' => [
                'Banmédica',
                'Colmena',
                'Consalud',
                'Cruz Blanca',
                'Esencial',
                'Nueva Masvida',
                'Vida Tres',
            ],
            'caec_cotizaciones' => 30,
            'caec_tope_uf' => 126,
            'caec_piso_uf' => 60,
            'caec_multiple_cotizaciones' => 43,
            'caec_multiple_tope_uf' => 181,
            'cotizacion_legal' => '7%',
            'ley_corta_anio' => 2024,
        ];
    }
}

if (!function_exists('psf_geo_citations')) {
    function psf_geo_citations(): array
    {
        return [
            'supersalud' => [
                'url' => 'https://www.superdesalud.gob.cl/',
                'label' => 'Superintendencia de Salud',
            ],
            'ges_caec' => [
                'url' => 'https://www.superdesalud.gob.cl/consultas-y-orientacion/coberturas-ges-y-caec/',
                'label' => 'Superintendencia — GES y CAEC',
            ],
            'ley_corta' => [
                'url' => 'https://www.bcn.cl/leychile/navegar?idNorma=1203946',
                'label' => 'Ley 21.674 (Ley Corta de Isapres)',
            ],
            'isapre_wikidata' => [
                'url' => 'https://www.wikidata.org/wiki/Q3150080',
                'label' => 'Isapre (Wikidata)',
            ],
        ];
    }
}

if (!function_exists('psf_cite')) {
    function psf_cite(string $key, ?string $label = null): string
    {
        $citations = psf_geo_citations();
        if (!isset($citations[$key])) {
            return '';
        }
        $href = htmlspecialchars($citations[$key]['url'], ENT_QUOTES, 'UTF-8');
        $text = htmlspecialchars($label ?? $citations[$key]['label'], ENT_QUOTES, 'UTF-8');

        return '<a href="' . $href . '" rel="noopener noreferrer" class="text-blue-700 underline underline-offset-2 hover:text-blue-900">' . $text . '</a>';
    }
}

if (!function_exists('psf_cite_urls')) {
    function psf_cite_urls(array $keys): array
    {
        $citations = psf_geo_citations();
        $urls = [];
        foreach ($keys as $key) {
            if (isset($citations[$key])) {
                $urls[] = $citations[$key]['url'];
            }
        }

        return $urls;
    }
}

if (!function_exists('psf_isapres_abiertas_texto')) {
    function psf_isapres_abiertas_texto(): string
    {
        $list = psf_geo_facts()['isapres_abiertas'];
        $last = array_pop($list);

        return implode(', ', $list) . ' y ' . $last;
    }
}

if (!function_exists('psf_faq_entities_from_pairs')) {
    /** @param array<string, string> $pairs */
    function psf_faq_entities_from_pairs(array $pairs): array
    {
        $entities = [];
        foreach ($pairs as $question => $answer) {
            $entities[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => trim(strip_tags((string) $answer)),
                ],
            ];
        }

        return $entities;
    }
}
