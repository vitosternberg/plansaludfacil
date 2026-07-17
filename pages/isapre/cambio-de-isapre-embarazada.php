<?php
/**
 * isapres/cambio-de-isapre/embarazada.php
 * Sub-página SEO: Cambio de Isapre Estando Embarazada
 */

require_once __DIR__ . '/../../omniflow_config.php';

$page_title       = 'Cambio de Isapre Estando Embarazada: ¿Es Posible? | Plan Salud Fácil';
$meta_description = '¿Embarazada y quieres cambiarte de isapre? Conoce las condiciones especiales, qué cubre la nueva isapre y cómo planificar el cambio antes o después del parto.';
$h1               = 'Cambio de Isapre Estando Embarazada';
$lead             = 'El embarazo es una de las etapas donde más necesitas una buena cobertura de salud. Si estás evaluando cambiarte de isapre estando embarazada, aquí te explicamos todas las consideraciones especiales.';
$svc_name         = 'Cambio de Isapre Embarazada';
$svc_description  = '¿Embarazada y quieres cambiarte de isapre? Guía completa sobre condiciones, plazos, cobertura de parto y cómo obtener el mejor plan para tu embarazo y postparto.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

$breadcrumbs = [
    ['label' => 'Inicio', 'url' => 'BASE_URL/'],
    ['label' => 'Isapres', 'url' => 'BASE_URL/isapres/'],
    ['label' => 'Cambio de Isapre', 'url' => 'BASE_URL/isapres/cambio-de-isapre/'],
    ['label' => 'Embarazada', 'url' => '#']
];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

$toc_items = [
    ['id' => 'es-posible', 'label' => '¿Es posible?'],
    ['id' => 'condiciones', 'label' => 'Condiciones especiales'],
    ['id' => 'cobertura', 'label' => 'Qué cubre la nueva isapre'],
    ['id' => 'recomendacion', 'label' => 'Nuestra recomendación'],
];

ob_start();
?>
<section id="es-posible" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Es posible cambiarse de isapre estando embarazada?</h2>
    <p class="text-gray-700 leading-relaxed mb-4"><strong>Sí, es posible</strong>, pero tiene condiciones especiales. El embarazo se considera una preexistencia, por lo que la nueva isapre puede:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li>Aceptarte <strong>sin restricciones</strong> (algunas isapres lo hacen).</li>
        <li>Aceptarte <strong>con restricción</strong> de cobertura para el parto y atenciones relacionadas al embarazo por un período.</li>
        <li><strong>Rechazar</strong> la solicitud (poco frecuente, pero posible).</li>
    </ul>
</section>

<section id="condiciones" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Condiciones especiales para embarazadas</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-3">
        <li><strong>Declaración de Salud:</strong> Debes declarar el embarazo. Omitirlo puede causar la anulación del contrato.</li>
        <li><strong>Planes con cobertura de maternidad:</strong> Necesitas un plan que incluya cobertura de parto y postnatal. No todos los planes lo incluyen automáticamente.</li>
        <li><strong>Período de carencia:</strong> Algunas isapres aplican un período de espera antes de cubrir el parto (generalmente 9-12 meses desde la afiliación).</li>
        <li><strong>Cambio post-parto:</strong> Muchas mujeres prefieren esperar al nacimiento del bebé y luego cambiarse a un plan familiar que cubra a ambos.</li>
    </ul>
</section>

<section id="cobertura" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Qué cubre la nueva isapre durante el embarazo</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Depende de la isapre y del plan que elijas. En general:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li><strong>Controles prenatales:</strong> Generalmente cubiertos con copago.</li>
        <li><strong>Parto y cesárea:</strong> Puede estar sujeto a período de carencia. Algunas isapres lo cubren de inmediato.</li>
        <li><strong>Postnatal:</strong> Los controles post-parto suelen estar cubiertos.</li>
        <li><strong>Hospitalización del recién nacido:</strong> Una vez nacido, debes agregarlo como carga en tu plan.</li>
    </ul>
</section>

<section id="recomendacion" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Nuestra recomendación</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Si estás en tu primer o segundo trimestre y tu isapre actual no te ofrece buena cobertura de maternidad, <strong>vale la pena evaluar el cambio</strong> cuanto antes. Si estás en el tercer trimestre, generalmente recomendamos esperar al parto y luego cambiarte a un plan familiar.</p>
    <p class="text-gray-700">Cada caso es distinto. Podemos analizar tu situación sin costo y decirte exactamente qué opciones tienes y qué isapre te conviene más.</p>
</section>
<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿Puedo cambiarme de isapre si ya estoy embarazada?' => 'Sí, pero debes declararlo. Algunas isapres aceptan sin restricciones, otras aplican período de carencia para el parto.',
    '¿Cuándo conviene más cambiarse?' => 'Idealmente en el primer trimestre, para cumplir con posibles períodos de carencia antes del parto.',
    '¿Qué pasa con mi licencia prenatal?' => 'La licencia prenatal la paga la isapre en la que estás afiliada al momento de iniciar la licencia, no la nueva.',
    '¿Puedo agregar a mi bebé al nuevo plan?' => 'Sí, una vez nacido lo agregas como carga. Te recomendamos elegir un plan que se pueda convertir fácilmente a familiar.',
];
$faq_titulo = 'Preguntas Frecuentes';

ob_start();
?>
<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_individual'); ?>
</div>
<?php
$secciones_html .= ob_get_clean();

include __DIR__ . '/../../layout/seo-page.php';
