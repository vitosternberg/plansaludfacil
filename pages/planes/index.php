<?php
/**
 * planes/index.php — Hub Planes
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
$page_title       = 'Planes de ISAPRE: Encuentra el Ideal para Ti | Plan Salud Fácil';
$meta_description = 'Compara planes de ISAPRE individuales y familiares. Encuentra el plan ideal según tu edad, perfil, presupuesto y necesidades de salud. Asesoría gratuita.';
$h1               = 'Planes de ISAPRE: Encuentra el plan perfecto para ti';
$lead             = 'Cada persona tiene necesidades de salud distintas. Explora planes individuales (jóvenes, adultos, deportistas, adultos mayores) y planes familiares con coberturas adaptadas a cada etapa de la vida.';
$svc_name         = 'Planes de ISAPRE';
$svc_description  = 'Comparativa de planes de ISAPRE individuales y familiares para cada perfil y etapa de vida.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'individuales', 'label' => 'Planes Individuales'],
    ['id' => 'familiares', 'label' => 'Planes Familiares'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="individuales" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Planes Individuales</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes de ISAPRE diseñados para personas solas, según su etapa de vida y perfil de salud. Desde planes económicos para jóvenes hasta coberturas completas para adultos mayores.</p>
    <div class="grid md:grid-cols-2 gap-4">
        <a href="<?= BASE_URL ?>/planes/individuales/jovenes" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition">
            <h3 class="font-bold text-gray-900 mb-1">Plan Joven</h3>
            <p class="text-sm text-gray-600">Para profesionales entre 18-30 años. Cobertura esencial, precio accesible.</p>
        </a>
        <a href="<?= BASE_URL ?>/planes/individuales/adulto" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition">
            <h3 class="font-bold text-gray-900 mb-1">Plan Adulto</h3>
            <p class="text-sm text-gray-600">Para personas entre 30-50 años. Cobertura equilibrada.</p>
        </a>
        <a href="<?= BASE_URL ?>/planes/individuales/deportista" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition">
            <h3 class="font-bold text-gray-900 mb-1">Plan Deportista</h3>
            <p class="text-sm text-gray-600">Para personas activas. Cobertura en medicina deportiva y lesiones.</p>
        </a>
        <a href="<?= BASE_URL ?>/planes/individuales/adulto-mayor" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition">
            <h3 class="font-bold text-gray-900 mb-1">Plan Adulto Mayor</h3>
            <p class="text-sm text-gray-600">Para mayores de 60 años. Máxima cobertura y beneficios.</p>
        </a>
    </div>
</section>

<section id="familiares" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Planes Familiares</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes de ISAPRE para familias, con cobertura para cónyuge, hijos y otras cargas legales. Incluye planes con preferencia natal, para familias con cargas y para familias monoparentales.</p>
    <div class="grid md:grid-cols-2 gap-4">
        <a href="<?= BASE_URL ?>/planes/familiares/preferencia-natal" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition">
            <h3 class="font-bold text-gray-900 mb-1">Preferencia Natal</h3>
            <p class="text-sm text-gray-600">Planes con cobertura reforzada para embarazo, parto y primer año del bebé.</p>
        </a>
        <a href="<?= BASE_URL ?>/planes/familiares/con-cargas" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition">
            <h3 class="font-bold text-gray-900 mb-1">Con Cargas</h3>
            <p class="text-sm text-gray-600">Planes familiares para parejas con hijos y otras cargas legales.</p>
        </a>
        <a href="<?= BASE_URL ?>/planes/familiares/monoparentales" class="block bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md hover:border-blue-200 transition md:col-span-2 md:max-w-md md:mx-auto">
            <h3 class="font-bold text-gray-900 mb-1">Monoparentales</h3>
            <p class="text-sm text-gray-600">Planes para padres o madres solteros con hijos como cargas.</p>
        </a>
    </div>
</section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = [
    '¿Qué plan me conviene más?' => 'Depende de tu edad, estado de salud, presupuesto y si tienes cargas familiares. Un asesor te ayuda a comparar sin costo.',
    '¿Puedo cambiar de plan dentro de la misma ISAPRE?' => 'Sí, generalmente una vez al año puedes solicitar un cambio de plan en tu misma ISAPRE.',
    '¿Los planes familiares cubren a todos mis hijos?' => 'Sí, puedes inscribir a tus hijos como cargas legales hasta los 18 años (o 24 si estudian).',
    '¿Cuánto cuesta un plan de ISAPRE?' => 'El precio depende del plan, tu cotización, edad y cargas. La cotización legal es del 7% de tu renta imponible.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Planes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
