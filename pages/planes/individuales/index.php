<?php
/**
 * planes/individuales/index.php — Sub-hub Planes Individuales
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
$page_title       = 'Planes de ISAPRE Individuales: Jóvenes, Adultos, Deportistas | Plan Salud Fácil';
$meta_description = 'Planes de ISAPRE individuales para cada etapa de la vida: jóvenes, adultos, deportistas y adultos mayores. Compara coberturas y precios. Asesoría gratuita.';
$h1               = 'Planes de ISAPRE Individuales';
$lead             = 'Si eres soltero o necesitas cobertura solo para ti, estos planes están diseñados para cada perfil: desde el profesional joven que empieza hasta el adulto mayor que busca la mejor protección.';
$svc_name         = 'Planes Individuales de ISAPRE';
$svc_description  = 'Planes de ISAPRE para personas sin cargas: jóvenes, adultos, deportistas y adultos mayores.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => 'BASE_URL/planes/'], ['label' => 'Individuales', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'jovenes', 'label' => 'Plan Joven'],
    ['id' => 'adulto', 'label' => 'Plan Adulto'],
    ['id' => 'deportista', 'label' => 'Plan Deportista'],
    ['id' => 'adulto-mayor', 'label' => 'Plan Adulto Mayor'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="jovenes" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Joven (18-30 años)</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes económicos ideales para profesionales que recién comienzan. Cobertura esencial en consultas, exámenes y urgencias con copagos accesibles. Prioriza la relación precio-cobertura.</p>
    <a href="<?= BASE_URL ?>/planes/individuales/jovenes" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver plan joven
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>

<section id="adulto" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Adulto (30-50 años)</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Cobertura equilibrada para adultos en plena etapa laboral. Buen balance entre precio, cobertura ambulatoria y hospitalaria. Ideal para quienes buscan protección sin exceder su presupuesto.</p>
    <a href="<?= BASE_URL ?>/planes/individuales/adulto" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver plan adulto
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>

<section id="deportista" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Deportista</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes enfocados en personas activas. Incluyen cobertura en medicina deportiva, kinesiología, traumatología y lesiones musculoesqueléticas. Beneficios en gimnasios y centros deportivos.</p>
    <a href="<?= BASE_URL ?>/planes/individuales/deportista" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver plan deportista
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>

<section id="adulto-mayor" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Adulto Mayor (60+ años)</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes con la máxima cobertura para adultos mayores. Priorizan hospitalizaciones, cirugías, enfermedades crónicas y atención geriátrica. Incluyen beneficios en medicamentos y telemedicina.</p>
    <a href="<?= BASE_URL ?>/planes/individuales/adulto-mayor" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver plan adulto mayor
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = [
    '¿Puedo contratar un plan individual si tengo cargas?' => 'No, los planes individuales son solo para el titular. Si tienes cargas necesitas un plan familiar.',
    '¿Qué plan individual es más barato?' => 'Generalmente el plan joven, ya que está diseñado para personas con bajo riesgo de salud y menor uso del sistema.',
    '¿Los planes individuales cubren hospitalización?' => 'Sí, todos los planes incluyen cobertura de hospitalización. El porcentaje varía según el plan (70-90%).',
    '¿Puedo cambiarme de plan individual a uno familiar?' => 'Sí, puedes solicitar el cambio a un plan familiar cuando lo necesites.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Planes Individuales';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
