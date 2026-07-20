<?php // companias/cruz-blanca.php — Landing Isapre Cruz Blanca — cambio de plan isapre Cruz Blanca (datos reales del motor)
require_once __DIR__.'/../../omniflow_config.php';
$page_title='Planes Isapre Cruz Blanca: Deporte, Kinesiología y Salud Mental | Plan Salud Fácil';
$meta_description='Planes de Isapre Cruz Blanca con foco en kinesiología, deporte y salud mental. Precios reales 2026 calculados con tabla oficial. Cotiza gratis.';
$h1='Planes Isapre Cruz Blanca';
$lead='Cruz Blanca destaca por sus planes con fuerte cobertura en kinesiología, medicina deportiva y salud mental. Si eres una persona activa que valora la prevención y el bienestar integral, Cruz Blanca es para ti.';
$svc_name='Planes Cruz Blanca';$cta_texto = 'Cotiza Express';$cta_link = BASE_URL.'/planes/comparador/';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Isapres','url'=>'BASE_URL/isapres/'],['label'=>'Compañías','url'=>'BASE_URL/isapres/companias/'],['label'=>'Cruz Blanca','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas'],['id'=>'coberturas-reales','label'=>'Coberturas Reales'],['id'=>'precios-reales','label'=>'Precios Reales'],['id'=>'ideal','label'=>'¿Es para ti?']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas destacadas</h2><ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Kinesiología:</strong> Hasta 100% de cobertura en sesiones. Ideal para deportistas y personas activas.</li>
<li><strong>Salud Mental:</strong> Consultas psicológicas y psiquiátricas con copago reducido.</li>
<li><strong>Telemedicina:</strong> Atención online sin costo adicional en varios planes.</li></ul>
<p class="text-sm text-gray-400 mt-4">Los datos a continuación se generan automáticamente desde nuestro motor de cotización (2,231 planes, Circular IF/N° 343).</p></section>
<?php require_once __DIR__.'/../../core/helpers_isapre.php';
$itemlist_jsonld = render_isapre_plans_jsonld('Cruz Blanca'); render_isapre_hero_stats('Cruz Blanca'); render_isapre_data('Cruz Blanca'); ?>
<section id="ideal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Cruz Blanca es para ti?</h2><p class="text-gray-700">Cruz Blanca es ideal para <strong>deportistas, personas activas</strong> y quienes priorizan la <strong>salud mental</strong>. Si la kinesiología, la telemedicina y el bienestar integral son importantes para ti, es tu mejor elección.</p></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Cubre todas las sesiones de kinesiología?'=>'Depende del plan, pero varios cubren hasta 100% de las sesiones con un tope anual.','¿Incluye psicólogo?'=>'Sí, los planes incluyen cobertura de salud mental con copago reducido.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../layout/seo-page.php';