<?php
/**
 * planes/individuales/adulto.php — Plan Adulto (30-55)
 * Foco: Estabilidad, prevención, coberturas internacionales y proyección familiar
 */
require_once __DIR__ . '/../../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) { $db->set_charset("utf8mb4"); $ip=$_SERVER['REMOTE_ADDR']??'unknown'; $ua=$_SERVER['HTTP_USER_AGENT']??'unknown'; $url=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; $stmt=$db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?,?,?)"); if($stmt){$stmt->bind_param("sss",$ip,$ua,$url);$stmt->execute();$stmt->close();} $db->close(); }
} catch (Exception $e) { error_log("Omniflow: ".$e->getMessage()); }

$page_title='Planes de Isapre Individuales: Cobertura a tu Medida | Plan Salud Fácil';
$meta_description='Planes de Isapre individuales sin cargas. Cobertura equilibrada en hospitalización, ambulatorio y prevención. Cotiza gratis.';
$h1='Planes de Isapre Individuales';
$lead='Un plan de salud pensado para ti, sin cargas. Cobertura equilibrada entre consultas, hospitalización y prevención.';
$svc_name='Planes Individuales';$svc_description='Planes de Isapre individuales sin cargas. Cobertura en hospitalización, ambulatorio y prevención.';$cta_texto='Cotizar Plan Individual';$cta_link='https://wa.me/56952282339';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Planes','url'=>'BASE_URL/planes/'],['label'=>'Individuales','url'=>'BASE_URL/planes/individuales/'],['label'=>'Adultos','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Cobertura equilibrada'],['id'=>'prevencion','label'=>'Medicina preventiva'],['id'=>'futuro','label'=>'Proyección familiar'],['id'=>'isapres','label'=>'Mejores isapres']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Cobertura equilibrada para tu etapa</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Hospitalización 90%+:</strong> Porque ahora sí puede pasar. Cirugías, accidentes, urgencias. Cubierto.</li>
<li><strong>Ambulatorio 80-90%:</strong> Consultas con especialistas, exámenes, imagenología.</li>
<li><strong>Maternidad opcional:</strong> Si tu familia crece, tu plan se adapta. Cobertura de parto y postnatal cuando lo necesites.</li>
<li><strong>Telemedicina premium:</strong> Consultas rápidas sin perder medio día en una clínica.</li>
<li><strong>Cobertura internacional:</strong> Varios planes incluyen cobertura de urgencias en el extranjero. Ideal si viajas por trabajo.</li>
</ul></section>
<section id="prevencion" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Medicina preventiva: tu mejor inversión</h2>
<p class="text-gray-700 mb-4">A los 30-50, prevenir es la diferencia entre un susto y un problema grave. Busca planes que incluyan:</p>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Chequeo ejecutivo anual completo (sangre, cardiaco, imagenología).</li><li>Dermatología preventiva (control de lunares).</li><li>Evaluación cardiovascular (electrocardiograma, prueba de esfuerzo).</li><li>Ginecología / Urología preventiva.</li></ul></section>
<section id="futuro" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Tu plan, preparado para el futuro</h2>
<p class="text-gray-700">Quizás hoy cotizas solo, pero en 2 años podrías tener pareja e hijos. Elige un plan individual que permita <strong>convertirse fácilmente a familiar</strong> sin perder antigüedad ni tener que re-evaluar tu Declaración de Salud. Así, cuando tu familia crezca, tu cobertura crece contigo sin trabas burocráticas.</p></section>
<section id="isapres" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para adultos</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>Banmédica:</strong> La red más grande. Ideal si quieres acceso a las mejores clínicas sin esperar.</li><li><strong>Colmena:</strong> Excelente para quienes proyectan formar familia (maternidad top).</li><li><strong>Cruz Blanca:</strong> Buen equilibrio precio-cobertura con foco en prevención.</li></ul></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Puedo pasar de plan individual a familiar?'=>'Sí. La mayoría de las isapres permiten migrar de individual a familiar sin perder antigüedad ni pasar por nueva evaluación de salud.','¿Qué chequeos preventivos cubre?'=>'Depende del plan, pero muchos incluyen chequeo ejecutivo anual con exámenes de sangre, electrocardiograma y evaluación general.','¿Conviene un plan más caro a esta edad?'=>'Generalmente sí. Entre 30 y 55 años, una hospitalización inesperada puede salir muy cara sin buena cobertura. La diferencia de precio se justifica.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../../layout/seo-page.php';
