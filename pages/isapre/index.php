<?php
/**
 * isapre/index.php — Hub ISAPRE
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
$page_title       = 'ISAPREs en Chile: Guía Completa 2026 | Plan Salud Fácil';
$meta_description = 'Guía completa sobre ISAPREs en Chile: qué son, cómo funcionan, comparativa con FONASA, Asesoría 100% gratuita.';
$h1               = 'ISAPREs en Chile: Todo lo que necesitas saber';
$lead             = 'Encuentra información clara y actualizada sobre el sistema de salud privado chileno. Desde los conceptos básicos hasta la ISAPREs disponibles en Chile.';
$svc_name         = 'Guía de ISAPREs';
$svc_description  = 'Información completa sobre ISAPREs en Chile: definición, funcionamiento, comparativas y recomendaciones.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'que-es', 'label' => '¿Qué es una ISAPRE?'],
    ['id' => 'como-funciona', 'label' => '¿Cómo funciona?'],
    ['id' => 'fonasa-vs-isapre', 'label' => 'ISAPRE vs FONASA'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="que-es" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué es una ISAPRE?</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Las ISAPREs son instituciones privadas que administran tu cotización obligatoria de salud (7% de tu renta) y te ofrecen planes con distintas coberturas, redes de clínicas y beneficios adicionales. Actualmente existen 7 ISAPREs abiertas al público general en Chile.</p>
    <a href="<?= BASE_URL ?>/isapres/que-es/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Leer guía completa
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>

<section id="como-funciona" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Cómo funciona una ISAPRE?</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Cuando te afilias a una ISAPRE, tu cotización del 7% se aplica al plan que elijas. Si tu cotización del 7% supera el valor base del plan, la Isapre te ofrece beneficios adicionales para optimizar tu cobertura. Los excedentes solo se generan de forma excepcional.</p>
    <a href="<?= BASE_URL ?>/isapres/como-funciona/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Leer guía completa
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>

<section id="fonasa-vs-isapre" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">ISAPRE vs FONASA</h2>
    <p class="text-gray-700 leading-relaxed mb-6">¿Sistema público o privado? Comparamos costos, coberturas, tiempos de espera, calidad de atención y acceso a clínicas para ayudarte a decidir cuál te conviene según tu perfil.</p>
    <a href="<?= BASE_URL ?>/isapres/fonasa-vs-isapre/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver comparativa completa
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = [
    '¿Qué es una ISAPRE?' => 'Una Institución de Salud Previsional privada que administra tu cotización de salud del 7% y ofrece planes médicos.',
    '¿Cuántas ISAPREs hay en Chile?' => 'Actualmente existen 7 ISAPREs abiertas: Banmédica, Cruz Blanca, Colmena, Vida Tres, Nueva Masvida, Consalud y Esencial.',
    '¿Puedo estar en ISAPRE y FONASA al mismo tiempo?' => 'No, son sistemas excluyentes. Debes elegir uno de los dos.',
    '¿Cómo elijo la mejor ISAPRE para mí?' => 'Depende de tu edad, renta, estado de salud, cargas familiares y las clínicas de tu preferencia. Te ayudamos a comparar sin costo.',
];
$faq_titulo = 'Preguntas Frecuentes sobre ISAPRE';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
