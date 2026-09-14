<?php
require_once __DIR__ . '/../core/social_profiles.php';

$variant = $variant ?? 'header';
$profiles = psf_social_profiles();
$icon_size = $variant === 'footer' ? 22 : 18;
$link_class = $variant === 'header'
    ? 'text-white/90 hover:text-white transition'
    : 'text-blue-200 hover:text-white transition';
?>
<div class="flex items-center gap-3 social-icons social-icons--<?= htmlspecialchars($variant) ?>">
    <?php foreach ($profiles as $profile): ?>
        <a href="<?= htmlspecialchars($profile['url']) ?>"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="<?= htmlspecialchars($profile['name']) ?> de Plan Salud Fácil"
           title="<?= htmlspecialchars($profile['name']) ?>"
           class="<?= $link_class ?>"
           data-track-label="RRSS <?= htmlspecialchars($profile['name']) ?>">
            <iconify-icon icon="<?= htmlspecialchars($profile['icon']) ?>" width="<?= (int) $icon_size ?>"></iconify-icon>
        </a>
    <?php endforeach; ?>
</div>
