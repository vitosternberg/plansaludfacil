<?php
/**
 * planes/familiares/monoparentales.php
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
$page_title       = 'Planes Monoparentales ISAPRE | Plan Salud Fácil';
$meta_description = 'Planes ISAPRE para familias monoparentales. Un ingreso, cobertura completa para titular e hijos. Precios accesibles. Cotiza gratis.';
$h1               = 'Planes Monoparentales';
$lead             = 'Planes para hogares con un solo ingreso. Cobertura para el titular y sus hijos a precios pensados en tu realidad.';
$svc_name         = 'Plan Monoparental';
$svc_description  = 'Planes para familias monoparentales: un titular con hijos.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => 'BASE_URL/planes/'], ['label' => 'Familiares', 'url' => 'BASE_URL/planes/familiares/'], ['label' => 'Monoparentales', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [['id' => 'que-es', 'label' => 'Definición'], ['id' => 'cobertura', 'label' => 'Cobertura'], ['id' => 'comparativa', 'label' => 'Comparativa'], ['id' => 'ahorro', 'label' => 'Tips ahorro']];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="que-es" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué es un plan monoparental?</h2><p class="text-gray-700 mb-4">Un solo titular (madre o padre) incluye a sus hijos como cargas. Más económico que un plan familiar tradicional porque solo hay un adulto cotizando.</p></section>
<section id="cobertura" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Cobertura</h2><ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Pediatría prioritaria</li><li>Urgencias 24/7</li><li>Hospitalización</li><li>Salud mental</li><li>Telemedicina</li></ul></section>
<section id="comparativa" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Comparativa</h2><p class="text-gray-700 mb-4">Ej: mamá 35a + 2 hijos.</p><div class="overflow-x-auto"><table class="w-full bg-white rounded-xl border shadow-sm text-sm"><thead><tr class="bg-blue-50 text-left"><th class="p-4">ISAPRE</th><th class="p-4">Plan</th><th class="p-4">Precio</th></tr></thead><tbody class="divide-y"><tr><td class="p-4">Cruz Blanca</td><td class="p-4">Familia Simple</td><td class="p-4">$95.000</td></tr><tr><td class="p-4">Colmena</td><td class="p-4">Mamá Full</td><td class="p-4">$88.000</td></tr><tr><td class="p-4">Banmédica</td><td class="p-4">Tu Familia</td><td class="p-4">$105.000</td></tr></tbody></table></div></section>
<section id="ahorro" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Tips de ahorro</h2><ul class="list-disc pl-6 text-gray-700 space-y-2"><li>Usa telemedicina para controles simples</li><li>Agrupa consultas de todos los hijos el mismo día</li><li>Aprovecha excedentes para copagos</li><li>Usa programas preventivos gratuitos</li></ul></section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = ['¿Sin estar casado/a?' => 'Sí, la filiación es lo que importa.', '¿Más de 3 hijos?' => 'Sin límite máximo.', '¿Padre no custodio?' => 'Sí, cualquiera de los padres puede agregarlos.'];
$faq_titulo = 'Preguntas Frecuentes';

// ── Formulario de contacto ─────────────────────────────
ob_start();
?>
<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_familia'); ?>
</div>
<?php
$secciones_html .= ob_get_clean();

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
