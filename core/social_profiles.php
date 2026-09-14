<?php
/**
 * Perfiles oficiales de redes sociales de Plan Salud Fácil.
 */
function psf_social_profiles(): array
{
    return [
        [
            'name' => 'Instagram',
            'url' => 'https://www.instagram.com/plansaludfacil/',
            'icon' => 'mdi:instagram',
        ],
        [
            'name' => 'TikTok',
            'url' => 'https://www.tiktok.com/@plansaludfacil',
            'icon' => 'simple-icons:tiktok',
        ],
        [
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/profile.php?id=61594587830939',
            'icon' => 'mdi:facebook',
        ],
    ];
}

function psf_social_urls(): array
{
    return array_column(psf_social_profiles(), 'url');
}
