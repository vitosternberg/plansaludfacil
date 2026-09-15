<?php
/**
 * isapre/que-es.php
 */
require_once __DIR__ . '/../../core/omniflow_track.php';
require_once __DIR__ . '/../../core/geo_facts.php';

$facts = psf_geo_facts();
$page_title       = '¿Qué es una ISAPRE? Guía Completa | Plan Salud Fácil';
$meta_description = 'Qué es una ISAPRE en Chile: cotización del 7%, diferencia con FONASA, GES (87 patologías) y regulación de la Superintendencia de Salud.';
$h1               = '¿Qué es una ISAPRE?';
$lead             = 'Una ISAPRE (Institución de Salud Previsional) es una aseguradora privada que administra tu cotización legal de salud del 7% y vende planes con copagos, redes y coberturas distintas a FONASA.';
$svc_name         = 'Información sobre ISAPREs';
$svc_description  = 'Guía sobre qué es una ISAPRE, cómo se regula y en qué se diferencia de FONASA.';
$schema_page_type = 'TechArticle';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => 'BASE_URL/isapres/'], ['label' => '¿Qué es?', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

$toc_items = [
    ['id' => 'definicion', 'label' => 'Definición de ISAPRE'],
    ['id' => 'historia', 'label' => 'Historia del sistema'],
    ['id' => 'comparativa', 'label' => 'ISAPRE vs FONASA'],
    ['id' => 'requisitos', 'label' => '¿Quién puede afiliarse?'],
];

ob_start();
?>
<article>
<section id="definicion" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Definición de ISAPRE</h2>
    <p class="text-gray-700 leading-relaxed mb-4">ISAPRE significa <strong>Institución de Salud Previsional</strong>. Son entidades privadas que reciben la cotización legal de salud —el <?= htmlspecialchars($facts['cotizacion_legal']) ?> de la renta imponible— y la aplican a un plan con porcentajes de bonificación, topes, red de prestadores y, en muchos casos, una capa GES y CAEC. Están fiscalizadas por <?= psf_cite('supersalud') ?>.</p>
    <p class="text-gray-700 leading-relaxed mb-4">El afiliado elige un plan cuyo precio se expresa en UF. Si ese valor supera el 7%, paga la diferencia de su bolsillo. Si el 7% alcanza o supera el plan, la isapre debe destinar la cotización a cobertura: con la <?= psf_cite('ley_corta') ?> de <?= (int) $facts['ley_corta_anio'] ?> ya no se “acumulan excedentes” como en el modelo anterior.</p>
    <p class="text-gray-700 leading-relaxed mb-4">En el mercado abierto operan <?= count($facts['isapres_abiertas']) ?> isapres: <?= htmlspecialchars(psf_isapres_abiertas_texto()) ?>. El listado vigente lo publica la Superintendencia; no confundir isapres abiertas con isapres cerradas de empresa ni con marcas que solo aparecen en beneficios específicos como CAEC.</p>
    <p class="text-sm text-gray-500"><cite>Fuente: <?= psf_cite('supersalud') ?> · entidad <?= psf_cite('isapre_wikidata') ?>.</cite></p>
</section>

<section id="historia" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s2-heading">
    <h2 id="s2-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Historia del sistema ISAPRE</h2>
    <p class="text-gray-700 leading-relaxed mb-4">El sistema privado nace en <strong>1981</strong> como alternativa a FONASA. En <strong>2005</strong> el AUGE/GES fija garantías explícitas de oportunidad, calidad y protección financiera para un listado de problemas de salud; el decreto vigente cubre <strong><?= (int) $facts['ges_patologias'] ?> patologías GES</strong>. La cifra exacta la actualiza el Ministerio de Salud y la Superintendencia: no uses “85+” ni “unas ochenta” como si fueran equivalentes.</p>
    <p class="text-gray-700 leading-relaxed mb-4">En <strong>2019</strong> se endurece la regla contra discriminación por sexo en la cotización. En <strong>2023–2024</strong> la Corte Suprema y el Congreso intervienen la tabla de factores y se dicta la Ley Corta, que obliga a ajustar planes al 7% y cambia el régimen de excedentes. GES y CAEC siguen siendo capas distintas del plan diario: el GES es garantía legal; la CAEC es cobertura catastrófica en red cerrada y exige solicitud expresa.</p>
    <ol class="list-decimal pl-6 text-gray-700 space-y-2 mb-4">
        <li><strong>1981:</strong> creación del sistema isapre.</li>
        <li><strong>2005:</strong> AUGE/GES; hoy <?= (int) $facts['ges_patologias'] ?> problemas de salud. <?= psf_cite('ges_caec') ?>.</li>
        <li><strong>2019:</strong> límites a discriminación en la cotización.</li>
        <li><strong>2023–2024:</strong> fallo de factores y <?= psf_cite('ley_corta') ?>.</li>
    </ol>
</section>

<section id="comparativa" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s3-heading">
    <h2 id="s3-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">ISAPRE vs FONASA</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Ambos sistemas cobran el 7%. FONASA opera sobre la red pública e institucional; la isapre vende un plan con copagos y clínicas en convenio. El GES aplica en los dos, con reglas distintas de prestador. La CAEC es beneficio de isapre, no de FONASA: el alto costo en FONASA sigue la modalidad institucional.</p>
    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-xl border border-gray-100 text-sm">
            <thead><tr class="bg-blue-50 text-left"><th class="p-4">Característica</th><th class="p-4">ISAPRE</th><th class="p-4">FONASA</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                <tr><td class="p-4 font-medium">Tipo</td><td class="p-4">Seguro privado regulado</td><td class="p-4">Seguro público</td></tr>
                <tr><td class="p-4 font-medium">Cotización</td><td class="p-4">7% + diferencia si el plan es más caro</td><td class="p-4">7% obligatorio</td></tr>
                <tr><td class="p-4 font-medium">GES</td><td class="p-4"><?= (int) $facts['ges_patologias'] ?> patologías, copagos GES</td><td class="p-4">Mismas patologías, red pública</td></tr>
                <tr><td class="p-4 font-medium">Alto costo</td><td class="p-4">CAEC en red cerrada, con solicitud</td><td class="p-4">Modalidad institucional / copagos de esa red</td></tr>
            </tbody>
        </table>
    </div>
    <p class="text-sm text-gray-500 mt-3"><cite>Normativa: <?= psf_cite('ges_caec') ?>.</cite></p>
</section>

<section id="requisitos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s4-heading">
    <h2 id="s4-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Quién puede afiliarse?</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Pueden cotizar en isapre los trabajadores dependientes con contrato, independientes que declaran renta, pensionados que cotizan sobre su pensión y las cargas legales (cónyuge, hijos y otras que reconozca la normativa). FONASA e isapre son excluyentes: no se cotiza en ambos al mismo tiempo.</p>
    <p class="text-gray-700 leading-relaxed mb-4">La isapre puede pedir declaración de salud. Las preexistencias no se ocultan: omitirlas puede dejar sin cobertura el evento. El cambio de isapre lo gestiona en general la nueva institución y rige el primer día del mes siguiente, según contrato y <?= psf_cite('supersalud') ?>.</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li><strong>Dependientes</strong> con contrato vigente</li>
        <li><strong>Independientes</strong> que emiten boletas</li>
        <li><strong>Pensionados</strong> que cotizan de su pensión</li>
        <li><strong>Cargas familiares</strong> reconocidas por ley</li>
    </ul>
</section>
</article>
<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿Qué significa ISAPRE?' => 'Institución de Salud Previsional: aseguradora privada que administra el 7% y vende un plan. Fiscalizada por la Superintendencia de Salud.',
    '¿Es obligatorio estar en una ISAPRE?' => 'No. Debes cotizar el 7% en FONASA o en una isapre, no en ambos.',
    '¿Cuántas ISAPREs abiertas hay?' => 'Siete en el mercado abierto: ' . psf_isapres_abiertas_texto() . '. Confirma el registro en la Superintendencia.',
    '¿Puedo tener ISAPRE y FONASA?' => 'No. Son sistemas excluyentes.',
];
$faq_titulo = 'Preguntas Frecuentes sobre ISAPRE';

include __DIR__ . '/../../layout/seo-page.php';
