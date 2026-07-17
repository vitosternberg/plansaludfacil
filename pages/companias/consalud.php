<?php // companias/consalud.php — Landing Isapre Consalud
require_once __DIR__.'/../../omniflow_config.php';
$page_title='Planes Isapre Consalud: Jóvenes, Regiones y Precios Competitivos | Plan Salud Fácil';
$meta_description='Planes de Isapre Consalud con precios reales 2026. La mejor opción para jóvenes profesionales y trabajadores en regiones. Cotiza gratis.';
$h1='Planes Isapre Consalud';
$lead='Consalud tiene fuerte presencia en regiones y destaca por sus precios competitivos en planes individuales, especialmente para jóvenes profesionales. Si buscas buena cobertura ambulatoria sin pagar de más, Consalud es una excelente opción.';
$svc_name='Planes Consalud';$cta_texto='Cotizar Consalud';$cta_link='https://wa.me/56952282339';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Isapres','url'=>'BASE_URL/isapres/'],['label'=>'Compañías','url'=>'BASE_URL/isapres/companias/'],['label'=>'Consalud','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas'],['id'=>'precios','label'=>'Precios 2026'],['id'=>'regiones','label'=>'Presencia en regiones'],['id'=>'ideal','label'=>'¿Es para ti?']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas destacadas</h2><ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Ambulatorio:</strong> Cobertura de hasta 90% en consultas médicas y especialistas. Copagos bajos.</li>
<li><strong>Telemedicina:</strong> Plataforma digital con atención 24/7 incluida en varios planes.</li>
<li><strong>Exámenes:</strong> Buena cobertura en laboratorio e imagenología.</li>
<li><strong>Dental:</strong> Limpieza gratis anual y descuentos en tratamientos.</li></ul></section>

<?php require_once __DIR__.'/../../core/helpers_isapre.php'; render_isapre_data('Consalud'); ?><section id="regiones" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Presencia en regiones</h2><p class="text-gray-700 mb-4">Consalud tiene una de las redes más extensas fuera de Santiago:</p><ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Presencia en más de 30 ciudades de Chile.</li><li>Convenios con clínicas y centros médicos locales.</li><li>Buena cobertura desde Arica a Punta Arenas.</li></ul></section>

<section id="ideal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Consalud es para ti?</h2><p class="text-gray-700">Consalud es ideal para <strong>jóvenes profesionales en regiones</strong> que buscan buena cobertura a precio competitivo. Si valoras la atención ambulatoria, la telemedicina y copagos bajos, es tu mejor alternativa.</p></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Consalud es más barata que otras isapres?'=>'Sí, especialmente en planes individuales para jóvenes. Precios desde $37.460/mes.','¿Tiene cobertura en regiones?'=>'Sí, es una de las isapres con mejor presencia en regiones fuera de Santiago.','¿Cubre telemedicina?'=>'Sí, varios de sus planes incluyen telemedicina 24/7 sin costo adicional.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../layout/seo-page.php';
