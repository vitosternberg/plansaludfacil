<?php
/**
 * planes/familiares/preferencia-natal.php
 * URL: /planes/familiares/maternidad/
 * Keyword target: "planes isapre maternidad embarazo"
 */
require_once __DIR__ . '/../../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) { $db->set_charset("utf8mb4"); $ip=$_SERVER['REMOTE_ADDR']??'unknown'; $ua=$_SERVER['HTTP_USER_AGENT']??'unknown'; $url=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; $stmt=$db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?,?,?)"); if($stmt){$stmt->bind_param("sss",$ip,$ua,$url);$stmt->execute();$stmt->close();} $db->close(); }
} catch (Exception $e) { error_log("Omniflow: ".$e->getMessage()); }

$page_title='Planes de Isapre Maternidad: Cobertura de Embarazo y Parto | Plan Salud Fácil';
$meta_description='Planes de Isapre con cobertura de maternidad: prenatal, parto, cesárea, neonatología y primer año del bebé. Cotiza gratis el mejor plan para tu embarazo.';
$h1='Planes de Isapre con Cobertura de Maternidad';
$lead='Si estás planificando un embarazo o ya estás embarazada, la cobertura de maternidad es probablemente tu prioridad número uno. Un parto en clínica privada puede costar entre $1.000.000 y $3.000.000. Con el plan adecuado, pagas una fracción de eso.';
$svc_name='Planes Maternidad';$svc_description='Planes de Isapre con la mejor cobertura de maternidad: parto, cesárea, prenatal, postnatal y primer año del bebé. Compara y cotiza gratis.';$cta_texto = 'Cotiza Express';$cta_link = BASE_URL.'/planes/comparador/';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Planes','url'=>'BASE_URL/planes/'],['label'=>'Familiares','url'=>'BASE_URL/planes/familiares/'],['label'=>'Maternidad','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'cobertura','label'=>'Qué cubre'],['id'=>'precios','label'=>'Precios y copagos'],['id'=>'isapres','label'=>'Mejores isapres'],['id'=>'tips','label'=>'Consejos']];
ob_start();?>
<section id="cobertura" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué cubre un plan con maternidad?</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Control prenatal:</strong> Consultas mensuales, ecografías y exámenes durante todo el embarazo.</li>
<li><strong>Parto natural:</strong> 90-100% de cobertura en clínica. El copago puede ser desde $50.000.</li>
<li><strong>Cesárea:</strong> Misma cobertura que el parto natural. Incluye honorarios de anestesista y pabellón.</li>
<li><strong>Neonatología:</strong> Cuidados del recién nacido en UCI si es necesario. Días de hospitalización del bebé.</li>
<li><strong>Postnatal:</strong> Controles post-parto para la mamá.</li>
<li><strong>Pediatría primer año:</strong> Controles de niño sano, vacunas del PNI, urgencias pediátricas.</li>
<li><strong>Lactancia:</strong> Algunas isapres incluyen consultas gratuitas con especialista en lactancia.</li>
</ul></section>
<section id="precios" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Precios y copagos</h2>
<div class="overflow-x-auto"><table class="w-full bg-white rounded-xl border shadow-sm text-sm"><thead><tr class="bg-blue-50 text-left"><th class="p-4">Concepto</th><th class="p-4">Sin cobertura</th><th class="p-4">Con plan maternidad</th></tr></thead><tbody class="divide-y"><tr><td class="p-4">Parto en clínica</td><td class="p-4">$1.500.000 - $3.000.000</td><td class="p-4">$50.000 - $150.000</td></tr><tr><td class="p-4">Cesárea</td><td class="p-4">$2.000.000 - $4.000.000</td><td class="p-4">$80.000 - $200.000</td></tr><tr><td class="p-4">Día UCI neonatal</td><td class="p-4">$500.000/día</td><td class="p-4">$0 - $50.000/día</td></tr><tr><td class="p-4">Plan mensual (familiar)</td><td class="p-4">—</td><td class="p-4">$120.000 - $180.000</td></tr></tbody></table></div>
<p class="text-sm text-gray-500 mt-3">*Precios referenciales. Varían según isapre, plan y clínica elegida.</p></section>
<section id="isapres" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para maternidad</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>Colmena:</strong> Reconocida por tener la mejor cobertura de maternidad del mercado. Programa "Colmena Mamá" con acompañamiento integral.</li><li><strong>Banmédica:</strong> Acceso a las mejores clínicas para el parto (Santa María, Dávila). Cobertura 100% en varios planes.</li><li><strong>Vida Tres:</strong> Planes premium con cobertura completa de parto y neonatología en clínicas de alta gama.</li></ul></section>
<section id="tips" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Consejos si estás planificando un embarazo</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>Contrata con anticipación:</strong> Lo ideal es tener el plan al menos 3-6 meses antes del embarazo para evitar períodos de carencia.</li><li><strong>Revisa la red de clínicas:</strong> Asegúrate de que la isapre tenga convenio con la clínica donde quieres tener a tu bebé.</li><li><strong>Compara copagos, no solo precios:</strong> Un plan $20.000 más barato al mes puede costarte $500.000 más en el parto.</li><li><strong>Si ya estás embarazada:</strong> Algunas isapres aceptan afiliación durante el embarazo. No asumas que no puedes cambiarte — consúltanos.</li></ul></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Puedo contratar un plan si ya estoy embarazada?'=>'Algunas isapres lo permiten. Depende del caso. Contáctanos y evaluamos tus opciones.', '¿Cubre parto natural y cesárea?'=>'Sí, ambos están cubiertos con la misma cobertura en la mayoría de los planes.', '¿El bebé queda cubierto automáticamente?'=>'Sí, desde el nacimiento. Debes inscribirlo como carga dentro de los primeros 30 días.','¿Hay período de carencia para maternidad?'=>'Si contratas el plan antes del embarazo, no. Si ya estás embarazada, puede aplicar un período de espera de 9-12 meses en algunas isapres.'];
$faq_titulo='Preguntas Frecuentes sobre Maternidad';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_familia'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../../layout/seo-page.php';
