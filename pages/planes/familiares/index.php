<?php
/**
 * planes/familiares/index.php — Sub-hub Planes Familiares
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
$page_title       = 'Planes de ISAPRE Familiares: Natal, Cargas, Monoparental | Plan Salud Fácil';
$meta_description = 'Planes de ISAPRE para familias: preferencia natal, con cargas y monoparentales. Cobertura completa para ti y los tuyos. Asesoría 100% gratuita.';
$h1               = 'Planes de ISAPRE Familiares';
$lead             = 'Protege a toda tu familia con un plan que se adapte a su composición y necesidades. Desde planes con cobertura reforzada para embarazos hasta opciones para familias monoparentales.';
$svc_name         = 'Planes Familiares de ISAPRE';
$svc_description  = 'Planes de ISAPRE para familias: cobertura para cónyuge, hijos y otras cargas legales.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => 'BASE_URL/planes/'], ['label' => 'Familiares', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'preferencia-natal', 'label' => 'Preferencia Natal'],
    ['id' => 'con-cargas', 'label' => 'Plan con Cargas'],
    ['id' => 'monoparentales', 'label' => 'Plan Monoparental'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="preferencia-natal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Preferencia Natal</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes familiares con cobertura reforzada para embarazo, parto, control prenatal y el primer año de vida del bebé. Incluye programas de acompañamiento, salas cuna y cobertura pediátrica prioritaria. Ideal para parejas que están planificando o esperando un hijo.</p>
    <a href="<?= BASE_URL ?>/planes/familiares/preferencia-natal" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver plan preferencia natal
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>

<section id="con-cargas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan con Cargas</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes para familias constituidas: pareja con hijos u otras cargas legales. Cobertura integral para todos los miembros, con beneficios en consultas pediátricas, controles de adulto y programas preventivos familiares.</p>
    <a href="<?= BASE_URL ?>/planes/familiares/con-cargas" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver plan con cargas
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>

<section id="monoparentales" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Monoparental</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes adaptados a padres o madres solteros con hijos como cargas. Cobertura equilibrada con precios accesibles, priorizando la protección de los niños sin descuidar la salud del titular. Ideal para familias con un solo adulto a cargo.</p>
    <a href="<?= BASE_URL ?>/planes/familiares/monoparentales" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver plan monoparental
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = [
    '¿A quiénes puedo incluir como carga familiar?' => 'Cónyuge, hijos hasta 18 años (24 si estudian), y en algunos casos padres adultos mayores que dependan económicamente de ti.',
    '¿Los planes familiares cubren embarazos?' => 'Sí, todos los planes cubren embarazo. Los planes con preferencia natal ofrecen coberturas adicionales y mejores condiciones.',
    '¿Puedo agregar una carga después de contratar el plan?' => 'Sí, puedes agregar cargas en cualquier momento presentando la documentación correspondiente.',
    '¿Qué pasa si mis hijos cumplen la edad límite como cargas?' => 'Deben contratar su propio plan. Algunas ISAPREs ofrecen planes de continuidad para ex-cargas.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Planes Familiares';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
