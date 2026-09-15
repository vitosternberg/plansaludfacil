<?php
/**
 * isapre/fonasa-vs-isapre.php
 */

// ── Tracking Omniflow ────────────────────────────────────
require_once __DIR__ . '/../../core/omniflow_track.php';
require_once __DIR__ . '/../../core/geo_facts.php';

// ── Variables SEO ────────────────────────────────────────
$page_title       = 'FONASA vs ISAPRE: ¿Cuál te Conviene? | Plan Salud Fácil';
$meta_description = 'Comparativa FONASA vs ISAPRE 2026: diferencias en precio, cobertura, clínicas, listas de espera. Descubre cuál sistema te conviene según tu perfil.';
$h1               = 'FONASA vs ISAPRE: ¿Cuál te conviene más?';
$lead             = 'Comparativa detallada entre el sistema público (FONASA) y el privado (ISAPRE): costos, cobertura, calidad de atención y tiempos de espera.';
$svc_name         = 'Comparativa FONASA vs ISAPRE';
$svc_description  = 'Comparación FONASA e ISAPRE: costos, coberturas y recomendación según perfil.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';
$schema_page_type = 'TechArticle';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => 'BASE_URL/isapre/'], ['label' => 'FONASA vs ISAPRE', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [['id' => 'diferencias', 'label' => 'Diferencias principales'], ['id' => 'costos', 'label' => 'Comparativa de costos'], ['id' => 'perfiles', 'label' => 'Según tu perfil'], ['id' => 'cambio', 'label' => 'Cómo cambiarte']];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="diferencias" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Diferencias principales</h2><div class="overflow-x-auto"><table class="w-full bg-white rounded-xl border border-gray-100 shadow-sm text-sm"><thead><tr class="bg-blue-50 text-left"><th class="p-4">Dimensión</th><th class="p-4">FONASA</th><th class="p-4">ISAPRE</th></tr></thead><tbody class="divide-y divide-gray-100"><tr><td class="p-4 font-medium">Sistema</td><td class="p-4">Público</td><td class="p-4">Privado</td></tr><tr><td class="p-4 font-medium">Costo</td><td class="p-4">7% sin costo extra</td><td class="p-4">7% + diferencia si plan es más caro</td></tr><tr><td class="p-4 font-medium">Tiempos espera</td><td class="p-4">Listas en sistema público</td><td class="p-4">Menor tiempo en clínicas privadas</td></tr><tr><td class="p-4 font-medium">Cobertura GES</td><td class="p-4">✅ Gratuita</td><td class="p-4">✅ Cubierta por ley</td></tr></tbody></table></div></section>
<section id="costos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Comparativa de costos</h2><p class="text-gray-700 mb-4">Ejemplo con renta de $1.000.000 (7% = $70.000). En FONASA ese monto cubre la cotización legal. En isapre se aplica al precio del plan en UF:</p><ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>FONASA:</strong> cotizas $70.000; no hay “plan en UF” adicional.</li><li><strong>ISAPRE:</strong> si el plan vale $85.000, pagas $15.000 extra. Si vale menos que $70.000, con la <?= psf_cite('ley_corta') ?> la isapre debe usar el 7% en cobertura, no acumular excedentes como antes de 2024.</li></ul><p class="text-sm text-gray-500 mt-3"><cite><?= psf_cite('supersalud') ?> · GES: <?= (int) psf_geo_facts()['ges_patologias'] ?> patologías (<?= psf_cite('ges_caec') ?>).</cite></p></section>
<section id="perfiles" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué te conviene según tu perfil?</h2><div class="grid md:grid-cols-2 gap-6"><div class="bg-green-50 border border-green-100 rounded-xl p-5"><h3 class="font-bold text-green-900 mb-2">FONASA si…</h3><ul class="list-disc pl-4 text-green-800 space-y-1 text-sm"><li>Ingresos bajos</li><li>Vives cerca de hospital público</li><li>Poca atención médica</li><li>Enfermedades GES</li></ul></div><div class="bg-blue-50 border border-blue-100 rounded-xl p-5"><h3 class="font-bold text-blue-900 mb-2">ISAPRE si…</h3><ul class="list-disc pl-4 text-blue-800 space-y-1 text-sm"><li>Quieres clínicas privadas</li><li>Atención ambulatoria frecuente</li><li>Valoras telemedicina y tiempos de espera más cortos</li></ul></div></div></section>
<section id="cambio" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Cómo cambiarte</h2><ol class="list-decimal pl-6 text-gray-700 space-y-2"><li>Cotiza planes en ISAPREs.</li><li>Firma contrato con la nueva ISAPRE.</li><li>La ISAPRE gestiona el cambio.</li><li>Efectivo el primer día del mes siguiente.</li></ol></section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = ['¿Puedo tener ambos?' => 'No, son excluyentes.', '¿Qué es mejor para familia?' => 'Depende de renta y necesidades. ISAPRE ofrece planes familiares con cobertura para cargas.', '¿Los excedentes se pierden al cambiar?' => 'El saldo de excedentes del régimen anterior no se transfiere a FONASA. Con la Ley Corta el modelo de acumulación ya no opera igual.'];
$faq_titulo = 'Preguntas Frecuentes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
