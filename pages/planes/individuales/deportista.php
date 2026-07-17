<?php
/**
 * planes/individuales/deportista.php — Plan Deportista
 * Foco: Kinesiología, traumatología, imagenología, medicina deportiva
 */
require_once __DIR__ . '/../../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown'; $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $stmt = $db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)");
        if ($stmt) { $stmt->bind_param("sss", $ip, $ua, $url); $stmt->execute(); $stmt->close(); }
        $db->close();
    }
} catch (Exception $e) { error_log("Omniflow: " . $e->getMessage()); }

$page_title       = 'Mejor Isapre para Deportistas: Kinesiología, Traumatología y Lesiones | Plan Salud Fácil';
$meta_description = 'Planes de Isapre para deportistas con cobertura 90%+ en kinesiología, traumatología e imagenología. Convenios con clínicas deportivas líderes. Cotiza gratis.';
$h1               = 'Planes de Isapre para Deportistas';
$lead             = 'Corres, pedaleas, juegas pádel, haces crossfit... y tu cuerpo lo sabe. Las lesiones deportivas no avisan. Necesitas un plan que cubra kinesiología sin límite, resonancias cuando hagan falta y traumatólogos que entiendan que quieres volver a moverte rápido.';
$svc_name         = 'Planes Isapre Deportistas';
$svc_description  = 'Planes de Isapre con la mejor cobertura en kinesiología, traumatología, resonancias y medicina deportiva. Para personas activas que no pueden parar.';
$cta_texto        = 'Cotizar Plan Deportista';
$cta_link         = 'https://wa.me/56952282339';

$breadcrumbs = [['label'=>'Inicio','url'=>'BASE_URL/'],['label'=>'Planes','url'=>'BASE_URL/planes/'],['label'=>'Individuales','url'=>'BASE_URL/planes/individuales/'],['label'=>'Deportistas','url'=>'#']];
foreach($breadcrumbs as &$bc){$bc['url']=str_replace('BASE_URL/',BASE_URL.'/',$bc['url']);}unset($bc);
$toc_items=[['id'=>'coberturas','label'=>'Coberturas deportivas'],['id'=>'clinicas','label'=>'Clínicas deportivas'],['id'=>'prevencion','label'=>'Medicina preventiva'],['id'=>'isapres','label'=>'Mejores isapres']];
ob_start();?>
<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas que necesitas como deportista</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Kinesiología 90-100%:</strong> Hasta 30 sesiones al año con copago mínimo. Que una lesión no te deje fuera 3 meses.</li>
<li><strong>Traumatología:</strong> Consultas con especialistas en rodilla, hombro, columna. Los que entienden tu deporte.</li>
<li><strong>Imagenología:</strong> Resonancias magnéticas, ecotomografías y radiografías con copago bajo. Diagnóstico rápido, vuelta rápida.</li>
<li><strong>Medicina deportiva:</strong> Evaluaciones de rendimiento, prevención de lesiones, planificación de entrenamiento.</li>
<li><strong>Nutricionista:</strong> Planes alimenticios para mejorar tu rendimiento.</li>
</ul></section>
<section id="clinicas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Clínicas y centros deportivos</h2>
<p class="text-gray-700 mb-4">Las mejores isapres para deportistas tienen convenios con:</p>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Clínica MEDS:</strong> El centro de medicina deportiva más grande de Chile. Atienden a seleccionados nacionales.</li>
<li><strong>Clínica Universidad de Los Andes:</strong> Unidad de medicina deportiva de alto nivel.</li>
<li><strong>UC Christus:</strong> Traumatología y kinesiología con foco en recuperación funcional.</li>
<li><strong>Centros Kinésicos:</strong> Red de kinesiólogos con convenio directo en todo Chile.</li>
</ul></section>
<section id="prevencion" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Medicina preventiva deportiva</h2>
<p class="text-gray-700">Como deportista, prevenir es mejor que curar. Busca planes que incluyan chequeos deportivos preventivos: electrocardiograma, prueba de esfuerzo, evaluación postural y screening de lesiones. Muchas isapres los ofrecen sin costo adicional una vez al año.</p></section>
<section id="isapres" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para deportistas</h2>
<ul class="list-disc pl-6 text-gray-700 space-y-2">
<li><strong>Cruz Blanca:</strong> La mejor cobertura kinésica del mercado. Hasta 100% en varios planes.</li>
<li><strong>Banmédica:</strong> Acceso a MEDS y las mejores clínicas deportivas del país.</li>
<li><strong>Colmena:</strong> Buena relación precio-cobertura para deportistas recreacionales.</li>
</ul></section>
<?php $secciones_html=ob_get_clean();
$faq_preguntas=['¿Cuántas sesiones de kinesiología cubre la isapre?'=>'Depende del plan. Algunos cubren hasta 30 sesiones al año con 90-100% de cobertura.','¿Cubre lesiones deportivas?'=>'Sí, las lesiones ocurridas durante actividad deportiva están cubiertas como cualquier otra lesión.','¿Necesito un plan más caro por ser deportista?'=>'No necesariamente. Hay planes que priorizan cobertura kinésica y ambulatoria sin ser los más caros del mercado.'];
$faq_titulo='Preguntas Frecuentes';
ob_start();?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html.=ob_get_clean();
include __DIR__.'/../../../layout/seo-page.php';
