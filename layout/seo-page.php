<?php
/**
 * layout/seo-page.php
 * Template base para páginas de contenido SEO piramidal.
 *
 * Uso en cada página hija:
 *   1. Definir variables: $page_title, $meta_description, $h1, $lead,
 *      $breadcrumbs, $toc_items, $secciones (html), $faq_preguntas,
 *      $svc_name, $svc_description.
 *   2. Incluir este archivo.
 *
 * NO incluye tracking Omniflow — cada página debe incluirlo antes.
 * Se espera que el tracking ya haya corrido antes de llegar aquí.
 */

// ── Valores por defecto ───────────────────────────────────
$page_title       = $page_title       ?? 'Plan Salud Fácil';
$meta_description = $meta_description ?? 'Cotiza y compara planes de Isapre. Asesoría 100% gratuita y online.';
$h1               = $h1               ?? $page_title;
$lead             = $lead             ?? '';
$breadcrumbs      = $breadcrumbs      ?? [];
$toc_items        = $toc_items        ?? [];
$secciones_html   = $secciones_html   ?? '';
$faq_preguntas    = $faq_preguntas    ?? [];
$faq_titulo       = $faq_titulo       ?? 'Preguntas Frecuentes';
$svc_name         = $svc_name         ?? $h1;
$svc_description  = $svc_description  ?? $meta_description;
$cta_texto        = $cta_texto        ?? 'Cotizar Ahora';
// ⚠️ CAMBIO 2026-08-05: default wa.me eliminado. Si no se sobreescribe, se usa modal.
$cta_link         = $cta_link         ?? '';
// ───────────────────────────────────────────────────────────

include './layout/plantilla.php';
include './layout/header.php';
?>

<main class="bg-gray-50 font-sans">

    <?php
    // 1. Hero SEO
    render_component('hero_seo', [
        'h1'          => $h1,
        'lead'        => $lead,
        'breadcrumbs' => $breadcrumbs,
        'cta_texto'   => $cta_texto,
        'cta_link'    => $cta_link,
    ]);

    // 2. Schema Service
    render_component('schema_service', [
        'svc_name'        => $svc_name,
        'svc_description' => $svc_description,
    ]);

    // 3. Tabla de Contenidos
    if (!empty($toc_items)) {
        render_component('indice_contenido', [
            'toc_items' => $toc_items,
        ]);
    }

    // 4. Secciones de contenido (H2 + H3 en HTML directo)
    if ($secciones_html) {
        echo $secciones_html;
    }

    // 5. Mini-FAQ — con fondo para separar visualmente del formulario
    if (!empty($faq_preguntas)) {
        ?>
        <div class="bg-gradient-to-b from-gray-50 to-gray-100 border-t border-gray-200 py-10">
        <?php
        render_component('faq_seccion', [
            'faq_preguntas' => $faq_preguntas,
            'faq_titulo'    => $faq_titulo,
        ]);
        ?>
        </div>
        <?php
    }

    // 6. CTA Final
    ?>
    <?php $is_wsp_cta = $cta_link && (strpos($cta_link, 'wa.me') !== false); ?>
    <section class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-12 px-4 mt-12" id="cotizar">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">¿Listo para cotizar tu plan?</h2>
            <p class="text-blue-100 mb-6">Habla con un asesor sin costo. Te ayudamos a encontrar el mejor plan según tu perfil.</p>
            <?php if ($is_wsp_cta): ?>
            <!-- ⚠️ CAMBIO 2026-08-05: wa.me -> openWspModal() -->
            <button onclick="openWspModal()"
               class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform hover:scale-105 border-none cursor-pointer">
                <iconify-icon icon="mdi:whatsapp" width="24" class="mr-2"></iconify-icon>
                <?= htmlspecialchars($cta_texto ?: 'Cotizar por WhatsApp') ?>
            </button>
            <?php elseif ($cta_link): ?>
            <!-- ⚠️ CAMBIO 2026-08-05: link interno sin icono WhatsApp -->
            <a href="<?= htmlspecialchars($cta_link) ?>"
               class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform hover:scale-105">
                <iconify-icon icon="mdi:arrow-right" width="24" class="mr-2"></iconify-icon>
                <?= htmlspecialchars($cta_texto) ?>
            </a>
            <?php else: ?>
            <!-- Sin link definido, mostrar boton modal por defecto -->
            <button onclick="openWspModal()"
               class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform hover:scale-105 border-none cursor-pointer">
                <iconify-icon icon="mdi:whatsapp" width="24" class="mr-2"></iconify-icon>
                <?= htmlspecialchars($cta_texto ?: 'Cotizar por WhatsApp') ?>
            </button>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php
// 7. CTA Flotante
render_component('cta_flotante');

include './layout/footer.php';
