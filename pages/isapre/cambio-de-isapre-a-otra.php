<?php
/**
 * isapres/cambio-de-isapre/a-otra-isapre.php
 * Sub-página SEO: Cambio de Isapre a otra Isapre
 */

require_once __DIR__ . '/../../omniflow_config.php';

$page_title       = 'Cambio de Isapre a Otra Isapre: Guía Paso a Paso | Plan Salud Fácil';
$meta_description = 'Aprende cómo cambiarte de isapre a otra isapre en Chile. Requisitos, plazos, Declaración de Salud y acompañamiento gratuito. Sin costo adicional.';
$h1               = 'Cambio de Isapre a Otra Isapre';
$lead             = 'Si ya estás en una isapre pero quieres cambiarte a otra con mejores coberturas, menor precio o beneficios que se ajusten mejor a tu perfil, aquí te explicamos todo lo que necesitas saber.';
$svc_name         = 'Cambio de Isapre a Otra Isapre';
$svc_description  = 'Guía completa para cambiarte de una isapre a otra: requisitos, plazos, documentación y acompañamiento personalizado sin costo.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

$breadcrumbs = [
    ['label' => 'Inicio', 'url' => 'BASE_URL/'],
    ['label' => 'Isapres', 'url' => 'BASE_URL/isapres/'],
    ['label' => 'Cambio de Isapre', 'url' => 'BASE_URL/isapres/cambio-de-isapre/'],
    ['label' => 'A otra Isapre', 'url' => '#']
];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

$toc_items = [
    ['id' => 'requisitos', 'label' => 'Requisitos'],
    ['id' => 'pasos', 'label' => 'Pasos para cambiarte'],
    ['id' => 'plazos', 'label' => 'Plazos y tiempos'],
    ['id' => 'beneficios', 'label' => 'Beneficios de cambiarte'],
];

ob_start();
?>
<section id="requisitos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Requisitos para cambiarte de Isapre</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li>Tener al menos <strong>12 meses de antigüedad</strong> en tu isapre actual.</li>
        <li>No estar con licencia médica vigente al momento del cambio.</li>
        <li>Completar la <strong>Declaración de Salud</strong> (nosotros te ayudamos).</li>
        <li>No tener cotizaciones impagas.</li>
    </ul>
</section>

<section id="pasos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Pasos para cambiarte de una Isapre a otra</h2>
    <ol class="list-decimal pl-6 text-gray-700 space-y-4">
        <li><strong>Evalúa tu situación actual:</strong> Revisa tu plan, coberturas y precio. Identifica qué te gustaría mejorar.</li>
        <li><strong>Cotiza alternativas:</strong> Nosotros comparamos todas las isapres por ti según tu edad, renta, cargas e intereses.</li>
        <li><strong>Elige tu nuevo plan:</strong> Te presentamos las 3 mejores opciones y decides la que más te convenga.</li>
        <li><strong>Firma la Declaración de Salud:</strong> Te guiamos en el llenado. Si tienes preexistencias, analizamos cuál isapre tiene mayor probabilidad de aceptarte sin restricciones.</li>
        <li><strong>Firma el nuevo contrato:</strong> 100% online. Tu nueva isapre se activa el primer día del mes siguiente.</li>
    </ol>
</section>

<section id="plazos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plazos y tiempos</h2>
    <p class="text-gray-700 leading-relaxed mb-4">El proceso completo de cambio de isapre toma entre <strong>48 horas y 7 días hábiles</strong>, dependiendo de la complejidad de tu perfil. La Isapre tiene hasta 30 días para pronunciarse sobre tu solicitud, pero en la práctica las respuestas llegan mucho antes.</p>
    <p class="text-gray-700">El cambio se hace efectivo el <strong>primer día del mes siguiente</strong> a la aceptación de tu solicitud. Durante la transición, mantienes tu cobertura actual sin interrupciones.</p>
</section>

<section id="beneficios" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Beneficios de cambiarte de Isapre</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li>Accede a <strong>mejores coberturas</strong> por el mismo 7% de cotización legal.</li>
        <li>Elige una isapre con <strong>mejor red de prestadores</strong> en tu comuna.</li>
        <li>Obtén beneficios que hoy no tienes: telemedicina, dental, kinesiología, medicamentos.</li>
        <li>Ajusta tu plan a tu <strong>nueva realidad</strong> (cambio de renta, edad, cargas).</li>
    </ul>
</section>
<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿Puedo cambiarme de isapre en cualquier momento?' => 'Sí, siempre que tengas al menos 12 meses de antigüedad en tu isapre actual y no estés con licencia médica vigente.',
    '¿Pierdo mis coberturas durante el cambio?' => 'No. Tu cobertura actual se mantiene hasta el último día del mes. La nueva isapre comienza a regir el primer día del mes siguiente.',
    '¿Cuánto cuesta cambiarse de isapre?' => 'Nuestro servicio de asesoría es 100% gratuito. Solo pagas la cotización mensual de tu nuevo plan.',
    '¿Puedo conservar mis excedentes?' => 'Los excedentes generados en tu isapre actual no se transfieren. Te recomendamos usarlos antes del cambio.',
];
$faq_titulo = 'Preguntas Frecuentes';

// ── Formulario ──────────────────────────────────────────
ob_start();
?>
<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_individual'); ?>
</div>
<?php
$secciones_html .= ob_get_clean();

include __DIR__ . '/../../layout/seo-page.php';
