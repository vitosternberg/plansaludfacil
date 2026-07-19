<?php // companias/vida-tres.php — Landing Isapre Vida Tres
require_once __DIR__.'/../../omniflow_config.php';
$page_title='Planes Isapre Vida Tres: Premium, Personalizado y Copagos Bajos | Plan Salud Fácil';
$meta_description='Planes de Isapre Vida Tres para profesionales de alta renta. Atención personalizada, copagos bajos y acceso a las mejores clínicas. Precios 2026.';
$h1='Planes Isapre Vida Tres';
$lead='Vida Tres es una isapre de nicho enfocada en la excelencia de atención. Con planes premium, copagos bajos y una red de clínicas de primer nivel, es la opción ideal para profesionales que valoran la calidad por sobre el precio.';
$svc_name='Planes Vida Tres';$cta_texto = 'Cotiza Express';$cta_link = BASE_URL.'/planes/comparador/';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Isapres','url'=>'BASE_URL/isapres/'],['label'=>'Compañías','url'=>'BASE_URL/isapres/companias/'],['label'=>'Vida Tres','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas'],['id'=>'precios','label'=>'Precios 2026'],['id'=>'experiencia','label'=>'Experiencia VIP'],['id'=>'ideal','label'=>'¿Es para ti?']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas destacadas</h2><ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Hospitalización:</strong> Cobertura de 100% en clínicas de alta gama con copago mínimo.</li>
<li><strong>Ambulatorio:</strong> Consultas con especialistas sin copago o copagos muy bajos.</li>
<li><strong>Maternidad:</strong> Cobertura premium de parto, cesárea y postnatal en clínicas top.</li>
<li><strong>Medicina preventiva:</strong> Chequeos ejecutivos anuales incluidos.</li></ul></section>

<?php require_once __DIR__.'/../../core/helpers_isapre.php'; render_isapre_data('Vida Tres'); ?><section id="experiencia" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Experiencia VIP</h2><ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Atención personalizada con ejecutivo dedicado 24/7.</li><li>Acceso a las mejores clínicas: Alemana, Las Condes, UC Christus.</li><li>Reembolso express (24-48 horas).</li></ul></section>

<section id="ideal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Vida Tres es para ti?</h2><p class="text-gray-700">Vida Tres es ideal para <strong>profesionales de alta renta</strong> que buscan la mejor experiencia de atención posible. Si valoras la rapidez, la personalización y el acceso a las mejores clínicas sin filas, es tu opción premium.</p></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Por qué es más cara Vida Tres?'=>'Por la mejor red de clínicas, copagos más bajos y atención personalizada 24/7.','¿Vale la pena?'=>'Si tu renta lo permite y valoras la calidad de atención, la diferencia en tiempos de espera y acceso a especialistas es significativa.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../layout/seo-page.php';
