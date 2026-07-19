<?php
/**
 * isapre/como-funciona.php
 */

// ── Tracking Omniflow ────────────────────────────────────
require_once __DIR__ . '/../../omniflow_config.php';
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
$page_title       = '¿Cómo Funciona una ISAPRE? Explicación Simple | Plan Salud Fácil';
$meta_description = 'Aprende cómo funciona una ISAPRE: cotización, plan de salud, copagos, excedentes y red de prestadores. Todo explicado de forma simple.';
$h1               = '¿Cómo funciona una ISAPRE?';
$lead             = 'El sistema ISAPRE funciona con tu cotización mensual del 7% que se transforma en un plan de salud con coberturas, bonificaciones y acceso a clínicas de tu red.';
$svc_name         = 'Funcionamiento de ISAPRE';
$svc_description  = 'Explicación del sistema ISAPRE: cotización, contratación, coberturas y beneficios.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => 'BASE_URL/isapres/'], ['label' => 'Cómo funciona', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [['id' => 'cotizacion', 'label' => 'La cotización del 7%'], ['id' => 'plan', 'label' => 'El plan de salud'], ['id' => 'copagos', 'label' => 'Copagos y deducibles'], ['id' => 'excedentes', 'label' => 'Excedentes']];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="cotizacion" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">La cotización del 7%</h2><p class="text-gray-700 mb-4">Cada mes, tu empleador descuenta el <strong>7% de tu renta imponible</strong> y lo envía a la ISAPRE. Si eres independiente, pagas esta cotización vía declaración de impuestos.</p><div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-6"><p class="font-semibold text-blue-900">Ejemplo:</p><p class="text-blue-800">Renta $1.000.000 → 7% = <strong>$70.000 mensuales</strong> para tu plan.</p></div></section>
<section id="plan" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">El plan de salud</h2><p class="text-gray-700 mb-4">La ISAPRE ofrece distintos planes. Cada plan define: <strong>porcentaje de bonificación</strong> (70-90%), <strong>tope anual</strong>, <strong>red de prestadores</strong> y <strong>beneficios adicionales</strong> como telemedicina y descuentos.</p></section>
<section id="copagos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Copagos y deducibles</h2><p class="text-gray-700 mb-4">Cuando te atiendes, la ISAPRE paga un porcentaje y tú pagas la diferencia (copago). Ej: consulta $30.000 con 80% de bonificación → ISAPRE paga $24.000, tú pagas $6.000.</p></section>
<section id="excedentes" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Excedentes y la Ley Corta</h2><p class="text-gray-700 mb-4">Desde 2024, con la <strong>Ley Corta de Isapres</strong>, los planes deben ajustarse para usar íntegramente tu cotización del 7%. Esto significa que el 7% va completo a tu plan de salud, y los excedentes —que antes se acumulaban cuando había diferencia entre tu cotización y el precio del plan— ya no se generan bajo las mismas condiciones. Si tu cotización es mayor que el precio base del plan, la isapre debe ofrecerte <strong>beneficios complementarios</strong> en lugar de acumular saldo.</p><p class="text-gray-700">Consulta nuestra <a href="<?= BASE_URL ?>/asesoria/optimizar-7-porciento/" class="text-blue-600 hover:underline">guía para optimizar tu 7%</a> con las nuevas reglas.</p></section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = ['¿El 7% cubre todo?' => 'Con la Ley Corta de Isapres (2024), tu cotización del 7% va íntegra a tu plan de salud. Si el plan cuesta más que tu 7%, pagas la diferencia como cotización adicional. Si cuesta menos, la isapre debe ofrecerte coberturas complementarias.', '¿Qué son los excedentes?' => 'Saldo que se generaba antes de 2024 cuando tu cotización superaba el precio del plan. Hoy, con la Ley Corta, los excedentes ya no se acumulan de la misma forma y se prioriza usar el 7% en coberturas complementarias.', '¿Puedo cambiarme de plan?' => 'Sí, dentro de la misma ISAPRE generalmente una vez al año.'];
$faq_titulo = 'Preguntas Frecuentes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
