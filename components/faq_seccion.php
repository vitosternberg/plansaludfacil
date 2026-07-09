<?php
/**
 * components/faq_seccion.php
 * Mini-FAQ section (3-5 preguntas) con Schema.org FAQPage JSON-LD.
 *
 * Variables esperadas:
 *   $faq_preguntas  — Array asociativo ['Pregunta' => 'Respuesta', ...]
 *   $faq_titulo     — Título H2 de la sección (string, default: 'Preguntas Frecuentes')
 */
$faq_preguntas = $faq_preguntas ?? [];
$faq_titulo    = $faq_titulo ?? 'Preguntas Frecuentes';

if (empty($faq_preguntas)) return;

// Construir Schema.org FAQPage
$schemaFaq = [];
foreach ($faq_preguntas as $q => $a) {
    $schemaFaq[] = [
        '@type'          => 'Question',
        'name'           => $q,
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text'  => $a,
        ],
    ];
}
?>

<!-- Schema.org FAQPage -->
<script type="application/ld+json">
<?= json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => $schemaFaq,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<section id="preguntas" class="max-w-4xl mx-auto px-4 py-12" aria-labelledby="faq-heading">
    <h2 id="faq-heading" class="text-2xl md:text-3xl font-bold text-gray-900 text-center mb-8">
        <?= htmlspecialchars($faq_titulo) ?>
    </h2>

    <div class="space-y-2 max-w-3xl mx-auto">
        <?php foreach ($faq_preguntas as $pregunta => $respuesta): ?>
        <details class="group bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
            <summary class="flex justify-between items-center cursor-pointer p-4 md:p-5 font-medium text-gray-800 list-none [&::-webkit-details-marker]:hidden">
                <span class="pr-4 text-sm md:text-base"><?= htmlspecialchars($pregunta) ?></span>
                <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 text-lg group-open:bg-red-50 group-open:text-red-500 transition-colors">
                    <span class="group-open:hidden">+</span>
                    <span class="hidden group-open:inline">−</span>
                </span>
            </summary>
            <div class="px-4 md:px-5 pb-4 md:pb-5 text-gray-600 leading-relaxed border-t border-gray-100 mx-4">
                <p class="text-sm md:text-base"><?= htmlspecialchars($respuesta) ?></p>
            </div>
        </details>
        <?php endforeach; ?>
    </div>
</section>
