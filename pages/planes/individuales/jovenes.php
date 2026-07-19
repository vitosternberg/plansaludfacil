<?php
/**
 * planes/individuales/jovenes.php — Plan Joven (18-30)
 * Foco: Optimiza tu 7% con cobertura ambulatoria y beneficios mes a mes
 */
require_once __DIR__ . '/../../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $stmt = $db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)");
        if ($stmt) { $stmt->bind_param("sss", $ip, $ua, $url); $stmt->execute(); $stmt->close(); }
        $db->close();
    }
} catch (Exception $e) { error_log("Omniflow: " . $e->getMessage()); }

$page_title       = 'Planes de Isapre para Jóvenes Profesionales (18-30) | Plan Salud Fácil';
$meta_description = 'Planes de Isapre para jóvenes profesionales. Optimiza tu 7% legal con cobertura ambulatoria 100%, telemedicina, dental y psicólogo. Cotiza gratis.';
$h1               = 'Planes de Isapre para Jóvenes Profesionales';
$lead             = 'Tienes entre 18 y 30 años, te enfermas poco y quieres que tu 7% de cotización legal realmente te sirva. La estrategia es simple: un plan con baja cobertura hospitalaria (no la necesitas) pero 100% ambulatoria para consultas, dental, psicólogo y telemedicina. Así aprovechas tu plata mes a mes.';
$svc_name         = 'Planes Isapre Jóvenes';
$svc_description  = 'Encuentra el mejor plan de Isapre para jóvenes profesionales. Maximiza tu 7% con cobertura ambulatoria, telemedicina y salud mental.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => 'BASE_URL/planes/'], ['label' => 'Individuales', 'url' => 'BASE_URL/planes/individuales/'], ['label' => 'Jóvenes', 'url' => '#']];
foreach ($breadcrumbs as &$bc) { $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']); } unset($bc);

$toc_items = [['id' => 'estrategia', 'label' => 'La estrategia joven'], ['id' => 'coberturas', 'label' => 'Coberturas clave'], ['id' => '7porciento', 'label' => 'Tu 7% optimizado'], ['id' => 'isapres', 'label' => 'Mejores isapres']];

ob_start();
?>
<section id="estrategia" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">La estrategia del plan joven</h2>
    <p class="text-gray-700 mb-4">Si tienes menos de 30 años, las estadísticas dicen que casi no usas hospitalización. Tu dinero del 7% se va mes a mes y apenas lo ves. La clave está en elegir un plan que <strong>invierta tu 7% en lo que realmente usas</strong>:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li><strong>Consultas médicas ilimitadas</strong> con copago bajo ($3.000-$5.000).</li>
        <li><strong>Salud mental:</strong> psicólogo y psiquiatra con cobertura real (no solo 3 sesiones al año).</li>
        <li><strong>Dental:</strong> limpieza gratis, caries, urgencias.</li>
        <li><strong>Telemedicina 24/7</strong> para no perder tiempo en salas de espera.</li>
    </ul>
</section>

<section id="coberturas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas que realmente importan a tu edad</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li><strong>Ambulatorio 90-100%:</strong> Consultas con especialistas cuando las necesites, sin gastar de más.</li>
        <li><strong>Salud Mental:</strong> Psicología y psiquiatría. La generación más consciente de su salud mental merece cobertura real.</li>
        <li><strong>Telemedicina:</strong> Recetas, licencias y controles desde el celular. Sin filas, sin esperas.</li>
        <li><strong>Dental:</strong> Limpieza gratis anual, caries y urgencias. Sonreír también es salud.</li>
        <li><strong>Kinesiología:</strong> Si haces deporte, que las lesiones no te frenen.</li>
    </ul>
</section>

<section id="7porciento" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Cuánto pagas y qué obtienes?</h2>
    <div class="bg-gray-50 rounded-xl p-6 border">
        <p class="text-gray-700 mb-3"><strong>Ejemplo:</strong> Renta de $1.200.000 → 7% = $84.000/mes.</p>
        <ul class="list-disc pl-6 text-gray-700 space-y-1">
            <li>Plan económico: <strong>$55.000/mes</strong> → Pierdes $29.000 en el sistema.</li>
            <li>Plan optimizado: <strong>$82.000/mes</strong> → Aprovechas casi todo tu 7% con coberturas que usas.</li>
        </ul>
        <p class="text-sm text-gray-500 mt-3"><a href="<?= BASE_URL ?>/asesoria/optimizar-7-porciento/" class="text-blue-600 hover:underline">Aprende más sobre cómo optimizar tu 7% →</a></p>
    </div>
</section>

<section id="isapres" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para jóvenes</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li><strong>Consalud:</strong> Precios bajos, buena cobertura ambulatoria y presencia en regiones.</li>
        <li><strong>Cruz Blanca:</strong> Fuerte en kinesiología, deporte y salud mental.</li>
        <li><strong>Banmédica:</strong> La más grande, ideal si quieres acceso a las mejores clínicas.</li>
    </ul>
    <p class="text-sm text-gray-500 mt-3">Cotiza gratis y te mostramos las 3 mejores opciones según tu perfil exacto. Sin compromiso.</p>
</section>
<?php $secciones_html = ob_get_clean();
$faq_preguntas = ['¿Qué plan de isapre me conviene si soy joven?'=>'Uno con alta cobertura ambulatoria (consultas, dental, psicólogo) y baja hospitalaria. Como te enfermas poco, prioriza lo que usas mes a mes.','¿Puedo tener telemedicina gratis?'=>'Sí, varias isapres incluyen telemedicina sin copago en planes para jóvenes.','¿Cuánto cuesta un plan joven?'=>'Desde $50.000/mes. Un plan optimizado ronda los $75.000-$85.000 para una renta de $1.200.000.'];
$faq_titulo = 'Preguntas Frecuentes';
ob_start(); ?><div id="formulario" class="max-w-4xl mx-auto px-4 py-10"><?php render_component('formulario_individual'); ?></div><?php $secciones_html .= ob_get_clean();
include __DIR__ . '/../../../layout/seo-page.php';
