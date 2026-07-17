<?php // companias/colmena.php — Landing Isapre Colmena
require_once __DIR__.'/../../omniflow_config.php';
$page_title='Planes Isapre Colmena: Familiares, Individuales y Maternidad | Plan Salud Fácil';
$meta_description='Planes de Isapre Colmena para familias e individuales. Precios reales 2026, cobertura de maternidad y red de prestadores. Cotiza gratis.';
$h1='Planes Isapre Colmena';
$lead='Colmena es una de las isapres más sólidas de Chile, con foco en planes familiares y la mejor cobertura de maternidad del mercado. Si buscas balance entre precio y calidad, Colmena es una excelente opción.';
$svc_name='Planes Colmena';$cta_texto='Cotizar Colmena';$cta_link='https://wa.me/56952282339';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Isapres','url'=>'BASE_URL/isapres/'],['label'=>'Compañías','url'=>'BASE_URL/isapres/companias/'],['label'=>'Colmena','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas'],['id'=>'precios','label'=>'Precios 2026'],['id'=>'maternidad','label'=>'Maternidad'],['id'=>'ideal','label'=>'¿Es para ti?']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas destacadas</h2><ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Maternidad:</strong> La mejor cobertura de parto, cesárea y postnatal del mercado. Ideal para familias en crecimiento.</li>
<li><strong>Pediatría:</strong> Controles de niño sano, vacunas y urgencias pediátricas cubiertas.</li>
<li><strong>Ambulatorio:</strong> Copagos bajos en consultas médicas y exámenes de laboratorio.</li>
<li><strong>Dental:</strong> Planes con limpieza gratis anual y descuentos.</li></ul></section>

<?php require_once __DIR__.'/../../core/helpers_isapre.php'; render_isapre_data('Colmena'); ?><section id="maternidad" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Maternidad en Colmena</h2><p class="text-gray-700 mb-4">Colmena es reconocida por <strong>la mejor cobertura de maternidad</strong>. Sus planes incluyen: parto y cesárea con copago mínimo, controles prenatales cubiertos, postnatal con visitas domiciliarias incluidas, y el programa "Colmena Mamá" de acompañamiento integral.</p></section>

<section id="ideal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Colmena es para ti?</h2><p class="text-gray-700">Colmena es ideal para <strong>familias jóvenes que están creciendo</strong>, parejas que planean tener hijos y padres solteros. Si la maternidad y la cobertura pediátrica son prioridad, es la mejor opción.</p></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Colmena tiene buena cobertura de maternidad?'=>'Sí, es la isapre con mejor cobertura de parto, cesárea y postnatal del mercado chileno.','¿Puedo cambiarme a Colmena?'=>'Sí, con 12 meses de antigüedad en tu isapre actual. Te ayudamos sin costo.','¿Colmena cubre medicamentos?'=>'Sí, tiene convenios con farmacias para descuentos en recetas médicas.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../layout/seo-page.php';
