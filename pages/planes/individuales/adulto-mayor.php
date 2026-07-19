<?php
/**
 * planes/individuales/adulto-mayor.php — Plan Adulto Mayor (60+)
 * Foco: Seguridad, transparencia, filtro previo de preexistencias, sin rechazo
 */
require_once __DIR__ . '/../../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) { $db->set_charset("utf8mb4"); $ip=$_SERVER['REMOTE_ADDR']??'unknown'; $ua=$_SERVER['HTTP_USER_AGENT']??'unknown'; $url=(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; $stmt=$db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?,?,?)"); if($stmt){$stmt->bind_param("sss",$ip,$ua,$url);$stmt->execute();$stmt->close();} $db->close(); }
} catch (Exception $e) { error_log("Omniflow: ".$e->getMessage()); }

$page_title='Planes de Isapre para Adulto Mayor (60+): Sin Rechazo | Plan Salud Fácil';
$meta_description='Planes de Isapre para adultos mayores. Evaluación confidencial de preexistencias, sin rechazo asegurado. Te ayudamos con la Declaración de Salud. Cotiza gratis.';
$h1='Planes de Isapre para Adulto Mayor';
$lead='El mayor miedo al cotizar un plan de isapre después de los 60 es el rechazo. En Plan Salud Fácil lo entendemos. Por eso evaluamos tu caso primero de forma confidencial, y solo postulamos a las isapres donde tengas altas probabilidades de aceptación. Sin sorpresas, sin rechazos innecesarios.';
$svc_name='Planes Isapre Adulto Mayor';$svc_description='Planes de Isapre para adultos mayores de 60 años. Evaluación confidencial de preexistencias y acompañamiento personalizado en tu Declaración de Salud.';$cta_texto = 'Cotiza Express';$cta_link = BASE_URL.'/planes/comparador/';
$breadcrumbs=[['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Planes','url'=>'BASE_URL/planes/'],['label'=>'Individuales','url'=>'BASE_URL/planes/individuales/'],['label'=>'Adulto Mayor','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas prioritarias'],['id'=>'preexistencias','label'=>'Preexistencias y aceptación'],['id'=>'filtro','label'=>'Nuestro filtro previo'],['id'=>'isapres','label'=>'Mejores isapres']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas prioritarias para tu edad</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Hospitalización 100%:</strong> La cobertura más importante. Cirugías, urgencias, UCI. Sin topes sorpresa.</li>
<li><strong>Enfermedades crónicas:</strong> Diabetes, hipertensión, cardiopatías. Cobertura continua de medicamentos y controles.</li>
<li><strong>Especialistas preferentes:</strong> Cardiología, geriatría, oftalmología, traumatología. Sin esperas de meses.</li>
<li><strong>Medicamentos:</strong> Descuentos sustanciales en fármacos de uso crónico. Esto puede ahorrarte cientos de miles al año.</li>
<li><strong>Telemedicina:</strong> Controles rutinarios desde casa. Ideal si tu movilidad es reducida.</li>
<li><strong>Kinesiología y rehabilitación:</strong> Recuperación post-cirugía o manejo de dolor crónico.</li>
</ul></section>

<!-- CAMPO DE PREEVALUACIÓN (CA-04 HU3) -->
<section id="preexistencias" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28 bg-amber-50 rounded-2xl -mx-4 px-8">
<h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Tienes alguna condición de salud preexistente?</h2>
<p class="text-gray-700 mb-4">No te preocupes. Tener una condición preexistente <strong>no significa que te van a rechazar</strong>. Significa que debemos elegir bien la isapre y el plan.</p>
<form class="space-y-4 max-w-lg" onsubmit="event.preventDefault(); handlePreeval(this);">
<div class="flex gap-6">
<label class="flex items-center cursor-pointer"><input type="radio" name="tiene_preexistencia" value="si" class="w-5 h-5 text-blue-600" onchange="document.getElementById('preeval_detalle').classList.remove('hidden')"><span class="ml-2 text-gray-700 font-medium">Sí</span></label>
<label class="flex items-center cursor-pointer"><input type="radio" name="tiene_preexistencia" value="no" class="w-5 h-5 text-blue-600" onchange="document.getElementById('preeval_detalle').classList.add('hidden'); document.getElementById('preeval_resultado').classList.add('hidden')"><span class="ml-2 text-gray-700 font-medium">No</span></label>
</div>
<div id="preeval_detalle" class="hidden space-y-3">
<textarea name="preeval_desc" rows="3" placeholder="De forma 100% confidencial, cuéntanos brevemente tu condición. Un asesor especializado analizará tu caso antes de cualquier postulación." class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>
<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition text-sm">Evaluar mi caso sin compromiso</button>
</div>
</form>
<div id="preeval_resultado" class="hidden mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
<p class="text-green-800 text-sm"><strong>✅ Recibido.</strong> Un asesor especializado en preexistencias analizará tu caso de forma confidencial y te contactará con las isapres que tienen mayor probabilidad de aceptarte. Sin costo, sin compromiso.</p>
</div>
<script>function handlePreeval(f){document.getElementById('preeval_resultado').classList.remove('hidden');}</script>
</section>

<section id="filtro" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Nuestro filtro previo: así funciona</h2>
<ol class="list-decimal pl-6 text-gray-700 space-y-3">
<li><strong>Análisis confidencial:</strong> Revisamos tu historial médico de forma privada. No enviamos nada a las isapres sin tu autorización.</li>
<li><strong>Selección de isapres:</strong> Identificamos cuáles tienen mayor probabilidad de aceptar tu perfil sin restricciones.</li>
<li><strong>Declaración de Salud:</strong> Te ayudamos a completarla correctamente. Sin omisiones, sin errores que puedan anular tu contrato después.</li>
<li><strong>Postulación:</strong> Solo enviamos tu caso a las isapres donde las probabilidades de éxito son altas. Si una rechaza, tenemos plan B.</li>
</ol></section>

<section id="isapres" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para adulto mayor</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>Nueva MasVida:</strong> La más flexible con preexistencias. Alta tasa de aceptación en +60.</li><li><strong>Banmédica:</strong> La mejor red de clínicas. Ideal si necesitas atención de alta complejidad.</li><li><strong>Colmena:</strong> Buena relación precio-cobertura para el segmento adulto mayor.</li></ul></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Me van a rechazar por mi edad?'=>'No necesariamente. Varias isapres aceptan adultos mayores, especialmente si el plan es adecuado. Nuestro filtro previo maximiza tus probabilidades.','¿Qué pasa si tengo una enfermedad crónica?'=>'Se puede cotizar igual. Algunas isapres son más flexibles que otras. Evaluamos tu caso y te decimos cuáles te aceptan.','¿Cubren todos los medicamentos?'=>'Depende del plan. La mayoría cubre medicamentos con receta con descuentos significativos. Los de uso crónico suelen tener convenios especiales.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../../layout/seo-page.php';
