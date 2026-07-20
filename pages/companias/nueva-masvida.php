<?php // companias/nueva-masvida.php — Landing Isapre Nueva MasVida
require_once __DIR__.'/../../omniflow_config.php';
$page_title='Planes Isapre Nueva MasVida: Adultos Mayores y Planes Accesibles | Plan Salud Fácil';
$meta_description='Planes de Isapre Nueva MasVida para adultos mayores y familias. Precios reales 2026. Cobertura accesible y buena aceptación de preexistencias.';
$h1='Planes Isapre Nueva MasVida';
$lead='Nueva MasVida se enfoca en planes accesibles con buena cobertura base. Destaca especialmente para adultos mayores por su política flexible de aceptación de preexistencias. Si buscas un plan sin complicaciones, es para ti.';
$svc_name='Planes Nueva MasVida';$cta_texto = 'Cotiza Express';$cta_link = BASE_URL.'/planes/comparador/';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Isapres','url'=>'BASE_URL/isapres/'],['label'=>'Compañías','url'=>'BASE_URL/isapres/companias/'],['label'=>'Nueva MasVida','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas'],['id'=>'precios','label'=>'Precios 2026'],['id'=>'adulto-mayor','label'=>'Adulto Mayor'],['id'=>'ideal','label'=>'¿Es para ti?']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas destacadas</h2><ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Enfermedades crónicas:</strong> Cobertura para diabetes, hipertensión y condiciones de largo tratamiento.</li>
<li><strong>Hospitalización:</strong> Cobertura sólida, clave para adultos mayores.</li>
<li><strong>Consultas médicas:</strong> Copagos accesibles en medicina general y especialidades.</li>
<li><strong>Farmacia:</strong> Descuentos en medicamentos de uso crónico.</li></ul></section>

<?php require_once __DIR__.'/../../core/helpers_isapre.php';
render_isapre_plans_jsonld('Nueva Masvida'); render_isapre_data('Nueva Masvida'); ?><section id="adulto-mayor" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Nueva MasVida para Adultos Mayores</h2><p class="text-gray-700 mb-4">Nueva MasVida es una de las isapres con <strong>mejores políticas para adultos mayores</strong>:</p><ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Alta tasa de aceptación de preexistencias.</li><li>Evaluación flexible de la Declaración de Salud.</li><li>Planes con copagos fijos para mayor previsibilidad.</li></ul></section>

<section id="ideal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Nueva MasVida es para ti?</h2><p class="text-gray-700">Nueva MasVida es ideal para <strong>adultos mayores</strong> y <strong>personas con condiciones crónicas</strong> que necesitan cobertura de medicamentos y controles regulares sin las restricciones de otras isapres.</p></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Aceptan preexistencias?'=>'Sí, Nueva MasVida tiene políticas más flexibles que el promedio.','¿Es buena para adultos mayores?'=>'Sí, es una de las isapres más recomendadas para personas sobre 60 años.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../layout/seo-page.php';
