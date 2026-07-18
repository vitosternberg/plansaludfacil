<?php
/**
 * planes/individuales/index.php — Planes Individuales (hub unificado)
 * 
 * Fusión de index.php (hub con 4 perfiles) + adulto.php (contenido detallado).
 * Commit de fusión: reemplaza la sección "Plan Adulto" genérica con el contenido
 * completo de adulto.php (coberturas, prevención, proyección familiar, isapres).
 * 
 * Punto de retorno: git revert <este_commit> para volver a la versión anterior.
 */

// ── Tracking Omniflow ────────────────────────────────────
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
        $lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
        if ($lead_id) {
            $stmt2 = $db->prepare("INSERT INTO lead_visits (lead_id, url_visitada) VALUES (?, ?)");
            if ($stmt2) { $stmt2->bind_param("is", $lead_id, $url); $stmt2->execute(); $stmt2->close(); }
        }
        $db->close();
    }
} catch (Exception $e) { error_log("Omniflow Tracking Error: " . $e->getMessage()); }

// ── Variables SEO ────────────────────────────────────────
$page_title       = 'Planes de ISAPRE Individuales: Cobertura a tu Medida | Plan Salud Fácil';
$meta_description = 'Planes de ISAPRE individuales para cada etapa de la vida. Cobertura equilibrada en hospitalización, ambulatorio y prevención. Cotiza gratis sin compromiso.';
$h1               = 'Planes de ISAPRE Individuales';
$lead             = 'Un plan de salud pensado para ti, sin cargas. Desde el profesional joven que empieza hasta el adulto mayor que busca la mejor protección.';
$svc_name         = 'Planes Individuales de ISAPRE';
$svc_description  = 'Planes de ISAPRE para personas sin cargas: jóvenes, adultos, deportistas y adultos mayores. Cotiza gratis.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs (3 niveles, sin "Adultos") ───────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => 'BASE_URL/planes/'], ['label' => 'Individuales', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'jovenes',       'label' => 'Plan Joven'],
    ['id' => 'adulto',        'label' => 'Plan Adulto'],
    ['id' => 'deportista',    'label' => 'Plan Deportista'],
    ['id' => 'adulto-mayor',  'label' => 'Plan Adulto Mayor'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>

<!-- ====== Plan Joven (del index original) ====== -->
<section id="jovenes" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Joven (18-30 años)</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes económicos ideales para profesionales que recién comienzan. Cobertura esencial en consultas, exámenes y urgencias con copagos accesibles. Prioriza la relación precio-cobertura.</p>
</section>

<!-- ====== Plan Adulto (fusionado: contenido completo de adulto.php) ====== -->
<section id="adulto" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">

    <!-- Cobertura equilibrada -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Cobertura equilibrada para tu etapa</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-8">
        <li><strong>Hospitalización 90%+:</strong> Porque ahora sí puede pasar. Cirugías, accidentes, urgencias. Cubierto.</li>
        <li><strong>Ambulatorio 80-90%:</strong> Consultas con especialistas, exámenes, imagenología.</li>
        <li><strong>Maternidad opcional:</strong> Si tu familia crece, tu plan se adapta. Cobertura de parto y postnatal cuando lo necesites.</li>
        <li><strong>Telemedicina premium:</strong> Consultas rápidas sin perder medio día en una clínica.</li>
        <li><strong>Cobertura internacional:</strong> Varios planes incluyen cobertura de urgencias en el extranjero. Ideal si viajas por trabajo.</li>
    </ul>

    <!-- Medicina preventiva -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Medicina preventiva: tu mejor inversión</h2>
    <p class="text-gray-700 mb-4">A los 30-50, prevenir es la diferencia entre un susto y un problema grave. Busca planes que incluyan:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-8">
        <li>Chequeo ejecutivo anual completo (sangre, cardiaco, imagenología).</li>
        <li>Dermatología preventiva (control de lunares).</li>
        <li>Evaluación cardiovascular (electrocardiograma, prueba de esfuerzo).</li>
        <li>Ginecología / Urología preventiva.</li>
    </ul>

    <!-- Proyección familiar -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Tu plan, preparado para el futuro</h2>
    <p class="text-gray-700 mb-8">Quizás hoy cotizas solo, pero en 2 años podrías tener pareja e hijos. Elige un plan individual que permita <strong>convertirse fácilmente a familiar</strong> sin perder antigüedad ni tener que re-evaluar tu Declaración de Salud. Así, cuando tu familia crezca, tu cobertura crece contigo sin trabas burocráticas.</p>

    <!-- Mejores isapres para adultos -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para adultos</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-8">
        <li><strong>Banmédica:</strong> La red más grande. Ideal si quieres acceso a las mejores clínicas sin esperar.</li>
        <li><strong>Colmena:</strong> Excelente para quienes proyectan formar familia (maternidad top).</li>
        <li><strong>Cruz Blanca:</strong> Buen equilibrio precio-cobertura con foco en prevención.</li>
    </ul>

</section>

<!-- ====== Plan Deportista (del index original) ====== -->
<section id="deportista" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Deportista</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes enfocados en personas activas. Incluyen cobertura en medicina deportiva, kinesiología, traumatología y lesiones musculoesqueléticas. Beneficios en gimnasios y centros deportivos.</p>
</section>

<!-- ====== Plan Adulto Mayor (del index original) ====== -->
<section id="adulto-mayor" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Adulto Mayor (60+ años)</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes con la máxima cobertura para adultos mayores. Priorizan hospitalizaciones, cirugías, enfermedades crónicas y atención geriátrica. Incluyen beneficios en medicamentos y telemedicina.</p>
</section>

<!-- ====== Formulario de cotización (heredado de adulto.php) ====== -->
<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_individual'); ?>
</div>

<?php
$secciones_html = ob_get_clean();

// ── FAQ fusionado (index + adulto, sin duplicados) ───────
$faq_preguntas = [
    '¿Puedo contratar un plan individual si tengo cargas?' => 'No, los planes individuales son solo para el titular. Si tienes cargas necesitas un plan familiar.',
    '¿Qué plan individual es más barato?' => 'Generalmente el plan joven, ya que está diseñado para personas con bajo riesgo de salud y menor uso del sistema.',
    '¿Los planes individuales cubren hospitalización?' => 'Sí, todos los planes incluyen cobertura de hospitalización. El porcentaje varía según el plan (70-90%).',
    '¿Puedo pasar de plan individual a familiar?' => 'Sí. La mayoría de las isapres permiten migrar de individual a familiar sin perder antigüedad ni pasar por nueva evaluación de salud.',
    '¿Qué chequeos preventivos cubre?' => 'Depende del plan, pero muchos incluyen chequeo ejecutivo anual con exámenes de sangre, electrocardiograma y evaluación general.',
    '¿Conviene un plan más caro a esta edad (30-55)?' => 'Generalmente sí. Una hospitalización inesperada puede salir muy cara sin buena cobertura. La diferencia de precio se justifica.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Planes Individuales';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
