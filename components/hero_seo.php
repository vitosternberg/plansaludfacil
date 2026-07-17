<?php
/**
 * components/hero_seo.php
 * Hero section optimizada para SEO: H1, breadcrumb visible, lead text, CTA.
 *
 * Variables esperadas:
 *   $h1             — Título H1 (string)
 *   $lead           — Texto introductorio (string, opcional)
 *   $breadcrumbs    — Array de ['label' => '...', 'url' => '...'] (opcional)
 *   $cta_texto      — Texto del botón CTA (string, opcional, default: 'Cotizar Ahora')
 *   $cta_link       — URL del CTA (string, opcional, default: '#formulario')
 *   $hero_class     — Clases extra para el fondo (string, opcional)
 */
$h1            = $h1 ?? 'Plan Salud Fácil';
$lead          = $lead ?? '';
$breadcrumbs   = $breadcrumbs ?? [];
$cta_texto     = $cta_texto ?? 'Cotizar Ahora';
$cta_link      = $cta_link ?? '#formulario';
$hero_class    = $hero_class ?? 'bg-gradient-to-r from-blue-800 to-blue-900';
?>

<section class="<?= htmlspecialchars($hero_class) ?> text-white py-16 px-4" aria-labelledby="hero-heading">
    <div class="max-w-4xl mx-auto">

        <?php if (!empty($breadcrumbs)): ?>
        <!-- Breadcrumb visible -->
        <nav aria-label="Breadcrumb" class="mb-4">
            <ol class="flex flex-wrap items-center text-blue-200 text-sm gap-1">
                <?php $total = count($breadcrumbs); ?>
                <?php foreach ($breadcrumbs as $i => $bc): ?>
                    <li class="flex items-center">
                        <?php if ($i < $total - 1): ?>
                            <?php $bc_url = rtrim($bc['url'], '/'); ?>
                            <a href="<?= htmlspecialchars($bc_url) ?>" class="hover:text-white transition underline-offset-2 hover:underline">
                                <?= htmlspecialchars($bc['label']) ?>
                            </a>
                            <span class="mx-2 text-blue-400" aria-hidden="true">›</span>
                        <?php else: ?>
                            <span class="text-white font-medium" aria-current="page"><?= htmlspecialchars($bc['label']) ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>

        <!-- Schema.org BreadcrumbList -->
        <script type="application/ld+json">
        <?php
        $bc_items = [];
        $pos = 1;
        foreach ($breadcrumbs as $bc) {
            $bc_items[] = [
                '@type' => 'ListItem',
                'position' => $pos,
                'name' => $bc['label'],
                'item' => $bc['url'] !== '#' ? rtrim($bc['url'], '/') : null,
            ];
            $pos++;
        }
        echo json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $bc_items,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        ?>
        </script>
        <?php endif; ?>

        <h1 id="hero-heading" class="text-3xl md:text-5xl font-bold mb-4 leading-tight">
            <?= htmlspecialchars($h1) ?>
        </h1>

        <?php if ($lead): ?>
        <p class="text-blue-100 text-lg md:text-xl max-w-2xl mb-6">
            <?= htmlspecialchars($lead) ?>
        </p>
        <?php endif; ?>

        <?php if ($cta_texto && $cta_link): ?>
        <a href="<?= htmlspecialchars($cta_link) ?>"
           class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-transform hover:scale-105">
            <iconify-icon icon="mdi:whatsapp" width="22" class="mr-2"></iconify-icon>
            <?= htmlspecialchars($cta_texto) ?>
        </a>
        <?php endif; ?>

    </div>
</section>
