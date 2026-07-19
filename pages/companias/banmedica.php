<?php // companias/banmedica.php — Landing Isapre Banmédica
require_once __DIR__.'/../../omniflow_config.php';
$page_title='Planes Isapre Banmédica: Cotiza, Compara y Contrata | Plan Salud Fácil';
$meta_description='Planes de Isapre Banmédica para individuales y familias. Compara precios reales 2026, coberturas y red de prestadores. Asesoría gratuita.';
$h1='Planes Isapre Banmédica';
$lead='Banmédica es la isapre más grande de Chile, con la red de clínicas más extensa del país. Si valoras la libertad de elección y el acceso a los mejores centros médicos, aquí encontrarás el plan perfecto para ti.';
$svc_name='Planes Banmédica';$cta_texto = 'Cotiza Express';$cta_link = BASE_URL.'/planes/comparador/';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Isapres','url'=>'BASE_URL/isapres/'],['label'=>'Compañías','url'=>'BASE_URL/isapres/companias/'],['label'=>'Banmédica','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas'],['id'=>'precios','label'=>'Precios 2026'],['id'=>'red','label'=>'Red de prestadores'],['id'=>'ideal','label'=>'¿Es para ti?']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas destacadas</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Hospitalización:</strong> Cobertura de hasta 100% en clínicas de la red. Copagos competitivos.</li>
<li><strong>Ambulatorio:</strong> Consultas médicas y especialistas con copago reducido en su amplia red.</li>
<li><strong>Clínicas propias:</strong> Acceso preferente a Clínica Santa María, Clínica Dávila y Clínica Vespucio.</li>
<li><strong>Maternidad:</strong> Cobertura completa de parto, cesárea y controles prenatales.</li>
</ul></section>

<?php require_once __DIR__.'/../../core/helpers_isapre.php'; render_isapre_data('Banmédica'); ?><section id="red" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Red de prestadores</h2><p class="text-gray-700 mb-4">Banmédica tiene la red de prestadores <strong>más grande de Chile</strong>:</p><ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Clínicas propias: Santa María, Dávila, Vespucio.</li><li>Clínicas en regiones: Concepción, Viña del Mar, Antofagasta.</li><li>Más de 40.000 prestadores médicos a nivel nacional.</li></ul></section>

<section id="ideal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Banmédica es para ti?</h2><p class="text-gray-700">Banmédica es ideal si valoras <strong>acceso rápido a especialistas</strong> y una <strong>red de clínicas de primer nivel</strong>. Es la mejor opción para profesionales urbanos y familias que priorizan la calidad por sobre el precio.</p></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Banmédica es más cara que otras isapres?'=>'En general sí, pero la diferencia se justifica por la calidad de su red de clínicas y la rapidez de atención.','¿Puedo atenderme en cualquier clínica?'=>'Depende del plan. Algunos son de libre elección y otros tienen red preferente.','¿Banmédica acepta preexistencias?'=>'Sí, evalúa caso a caso. Tiene buenas tasas de aceptación para condiciones controladas.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../layout/seo-page.php';
