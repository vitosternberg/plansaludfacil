<?php
/**
 * planes/familiares/con-cargas.php
 * Plan Familiar con Cargas
 */
require_once __DIR__ . '/../../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) { $db->set_charset("utf8mb4"); $ip=$_SERVER['REMOTE_ADDR']??'unknown'; $ua=$_SERVER['HTTP_USER_AGENT']??'unknown'; $url=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; $stmt=$db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?,?,?)"); if($stmt){$stmt->bind_param("sss",$ip,$ua,$url);$stmt->execute();$stmt->close();} $db->close(); }
} catch (Exception $e) { error_log("Omniflow: ".$e->getMessage()); }

$page_title='Planes Familiares con Cargas: Isapre para tu Familia | Plan Salud Fácil';
$meta_description='Planes de Isapre familiares con cargas. Protege a tu pareja e hijos con la mejor cobertura. Compara precios y beneficios para tu grupo familiar.';
$h1='Planes de Isapre Familiares con Cargas';
$lead='Tu familia merece la mejor cobertura. Los planes familiares te permiten proteger a tu pareja e hijos bajo un mismo plan, optimizando el 7% de cada uno y accediendo a beneficios grupales que un plan individual no ofrece.';
$svc_name='Planes Familiares con Cargas';$svc_description='Planes de Isapre para familias con cargas. Cobertura para pareja e hijos, pediatría, maternidad y beneficios grupales. Cotiza gratis.';$cta_texto='Cotizar Plan Familiar';$cta_link='https://wa.me/56952282339';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Planes','url'=>'BASE_URL/planes/'],['label'=>'Familiares','url'=>'BASE_URL/planes/familiares/'],['label'=>'Con Cargas','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas'],['id'=>'precios','label'=>'Precios'],['id'=>'beneficios','label'=>'Beneficios familiares'],['id'=>'isapres','label'=>'Mejores isapres']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas para toda la familia</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Pediatría:</strong> Controles de niño sano, vacunas y urgencias pediátricas cubiertas.</li>
<li><strong>Maternidad:</strong> Parto, cesárea, prenatal y postnatal. Cobertura completa para mamá y bebé.</li>
<li><strong>Hospitalización:</strong> Cobertura para todos los integrantes, incluyendo UCI y cirugías.</li>
<li><strong>Ambulatorio:</strong> Consultas médicas, especialistas y exámenes con copago familiar reducido.</li>
<li><strong>Dental:</strong> Limpieza gratis para cada integrante una vez al año.</li>
</ul></section>
<section id="precios" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Cuánto cuesta un plan familiar?</h2>
<p class="text-gray-700 mb-4">El precio depende de la cantidad de cargas, las edades y la isapre. Como referencia:</p>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Familia de 3 (2 adultos + 1 hijo): desde $120.000/mes.</li><li>Familia de 4 (2 adultos + 2 hijos): desde $150.000/mes.</li><li>Familia de 5 o más: desde $180.000/mes.</li></ul>
<p class="text-sm text-gray-500 mt-2">*Cada adulto aporta su 7%. Si ambos trabajan, se suman las cotizaciones.</p></section>
<section id="beneficios" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Beneficios de un plan familiar</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>Un solo plan:</strong> Todos bajo la misma cobertura. Sin planes separados que complican.</li><li><strong>Copago familiar:</strong> Topes de gasto anual por grupo, no por persona.</li><li><strong>Excedentes compartidos:</strong> Si generas, los usan todos los integrantes.</li><li><strong>Antigüedad conjunta:</strong> Si luego te separas, cada uno conserva su antigüedad.</li></ul></section>
<section id="isapres" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para familias</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>Colmena:</strong> Excelente cobertura de maternidad y pediatría. Ideal para familias en crecimiento.</li><li><strong>Banmédica:</strong> La red más grande. Ideal si quieres elegir dónde atenderte.</li><li><strong>Cruz Blanca:</strong> Buen balance precio-cobertura con foco en prevención.</li></ul></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Puedo agregar a mi pareja si no estamos casados?'=>'Sí, puedes agregar a tu conviviente como carga acreditando la convivencia.','¿Mis hijos están cubiertos hasta qué edad?'=>'Hasta los 25 años si están estudiando, o de por vida si tienen una discapacidad.','¿Qué pasa si me separo?'=>'Cada adulto puede tomar un plan individual conservando su antigüedad. Los hijos quedan como cargas de uno de los padres.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_familia'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../../layout/seo-page.php';
