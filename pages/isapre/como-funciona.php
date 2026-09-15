<?php
/**
 * isapre/como-funciona.php
 */
require_once __DIR__ . '/../../core/omniflow_track.php';
require_once __DIR__ . '/../../core/geo_facts.php';

$facts = psf_geo_facts();
$page_title       = '¿Cómo Funciona una ISAPRE? Explicación Simple | Plan Salud Fácil';
$meta_description = 'Cómo funciona una ISAPRE: 7% de cotización, plan en UF, copagos, GES, CAEC y qué cambió con la Ley 21.674 (Ley Corta).';
$h1               = '¿Cómo funciona una ISAPRE?';
$lead             = 'Tu empleador (o tú, si eres independiente) destina el 7% de la renta imponible a un plan. Ese plan define copagos, red y topes; GES y CAEC son capas aparte.';
$schema_page_type = 'TechArticle';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => 'BASE_URL/isapres/'], ['label' => 'Cómo funciona', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

$toc_items = [
    ['id' => 'cotizacion', 'label' => 'La cotización del 7%'],
    ['id' => 'plan', 'label' => 'El plan de salud'],
    ['id' => 'copagos', 'label' => 'Copagos y deducibles'],
    ['id' => 'excedentes', 'label' => 'Excedentes y Ley Corta'],
];

ob_start();
?>
<article>
<section id="cotizacion" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="c1">
    <h2 id="c1" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">La cotización del 7%</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Por ley, el <?= htmlspecialchars($facts['cotizacion_legal']) ?> de la renta imponible va a salud. En isapre ese monto se aplica al precio del plan (UF × valor UF del mes). Con renta de $1.000.000 el 7% es <strong>$70.000</strong>. Si el plan sale $85.000, pagas $15.000 extra. Si sale menos que tu 7%, la isapre no te “devuelve la diferencia en efectivo” como en el modelo de excedentes anterior: debe usarla en cobertura, según la <?= psf_cite('ley_corta') ?>.</p>
    <p class="text-gray-700 leading-relaxed mb-4">El independiente paga el 7% sobre la renta que declara. El tope imponible previsional limita la base de cálculo: no es “el 7% de todo lo que ganas” si superas ese tope. La fiscalización del descuento y de los planes es de <?= psf_cite('supersalud') ?>.</p>
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
        <p class="font-semibold text-blue-900">Ejemplo numérico</p>
        <p class="text-blue-800">Renta $1.000.000 → 7% = <strong>$70.000</strong> al mes. Compara ese número con el precio en pesos del plan (UF × UF del día), no solo con el copago de una consulta.</p>
    </div>
</section>

<section id="plan" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="c2">
    <h2 id="c2" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">El plan de salud</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Cada plan fija porcentaje de bonificación (a menudo 70–90% en prestador preferente), tope anual, arancel y red. “Preferente” no es lo mismo que red CAEC: una clínica del día a día puede quedar fuera de la red catastrófica. El GES (<?= (int) $facts['ges_patologias'] ?> patologías) es obligatorio y tiene copago y prestador propios; no sustituye al plan ni a la CAEC.</p>
    <p class="text-gray-700 leading-relaxed">Los precios de catálogo que usamos en el comparador salen de archivos de <?= psf_cite('supersalud') ?> (miles de códigos de plan). Un plan “barato en UF” puede ser caro en copagos si la red no es la que usas. Por eso el comparador muestra UF y pesos, no solo el ranking de marketing.</p>
</section>

<section id="copagos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="c3">
    <h2 id="c3" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Copagos y deducibles</h2>
    <p class="text-gray-700 leading-relaxed mb-4">En una consulta de $30.000 con 80% de bonificación, la isapre cubre $24.000 y tú $6.000, hasta el tope del plan. En hospitalización el copago se dispara si el prestador no es el preferente o si se agota el tope. El deducible CAEC es otra cuenta: <?= (int) $facts['caec_cotizaciones'] ?> cotizaciones pactadas, piso <?= (int) $facts['caec_piso_uf'] ?> UF y techo <?= (int) $facts['caec_tope_uf'] ?> UF por beneficiario y diagnóstico, según la norma que publica <?= psf_cite('ges_caec') ?>.</p>
    <p class="text-gray-700 leading-relaxed">La CAEC no se activa sola: hay que pedir incorporación a la red cerrada. Urgencia vital (Ley de Urgencia) te estabiliza; no equivale a CAEC al 100% en esa clínica. Si el evento es de alto costo, avisa a la isapre dentro de las 48 horas de práctica normativa.</p>
</section>

<section id="excedentes" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="c4">
    <h2 id="c4" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Excedentes y la Ley Corta</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Hasta <?= (int) $facts['ley_corta_anio'] ?>, si el 7% superaba el precio del plan, la diferencia podía acumularse como excedente (bonos, farmacia). Con la <?= psf_cite('ley_corta') ?> los planes deben ajustarse para usar el 7% en cobertura: <strong>no vendas ni compres un plan “para generar excedentes rápidos”</strong> como si el régimen anterior siguiera intacto.</p>
    <p class="text-gray-700 leading-relaxed mb-4">Si tu cotización supera el plan, la isapre debe ofrecer beneficios complementarios, no un saldo tipo billetera. Si el plan es más caro que el 7%, pagas adicional. Para elegir plan cercano al 7% usa la <a href="<?= BASE_URL ?>/asesoria/optimizar-7-porciento/" class="text-blue-700 underline">guía de optimizar el 7%</a> y el <a href="<?= BASE_URL ?>/planes/comparador/" class="text-blue-700 underline">comparador</a>.</p>
    <p class="text-sm text-gray-500"><cite><?= psf_cite('ley_corta') ?> · <?= psf_cite('supersalud') ?>.</cite></p>
</section>
</article>
<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿El 7% cubre todo?' => 'No. Cubre el precio del plan hasta ese monto. Si el plan es más caro, pagas la diferencia. Si es más barato, la Ley Corta exige usar el 7% en cobertura, no acumular excedentes como antes de 2024.',
    '¿Qué son los excedentes?' => 'Saldo que se generaba cuando el 7% superaba el plan. Tras la Ley 21.674 ese mecanismo ya no opera igual: prioriza coberturas complementarias.',
    '¿Puedo cambiarme de plan?' => 'Sí, dentro de la misma isapre suele haber ventanas anuales; el cambio de isapre lo gestiona la nueva institución.',
];
$faq_titulo = 'Preguntas Frecuentes';

include __DIR__ . '/../../layout/seo-page.php';
