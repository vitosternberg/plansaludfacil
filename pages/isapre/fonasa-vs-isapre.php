<?php
/**
 * isapre/fonasa-vs-isapre.php
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
$page_title       = 'FONASA vs ISAPRE: ¿Cuál te Conviene? | Plan Salud Fácil';
$meta_description = 'Comparativa FONASA vs ISAPRE 2026: diferencias en precio, cobertura, clínicas, listas de espera. Descubre cuál sistema te conviene según tu perfil.';
$h1               = 'FONASA vs ISAPRE: ¿Cuál te conviene más?';
$lead             = 'Comparativa detallada entre el sistema público (FONASA) y el privado (ISAPRE): costos, cobertura, calidad de atención y tiempos de espera.';
$svc_name         = 'Comparativa FONASA vs ISAPRE';
$svc_description  = 'Comparación FONASA e ISAPRE: costos, coberturas y recomendación según perfil.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => 'BASE_URL/isapre/'], ['label' => 'FONASA vs ISAPRE', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [['id' => 'diferencias', 'label' => 'Diferencias principales'], ['id' => 'costos', 'label' => 'Comparativa de costos'], ['id' => 'perfiles', 'label' => 'Según tu perfil'], ['id' => 'cambio', 'label' => 'Cómo cambiarte']];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="diferencias" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Diferencias principales</h2><div class="overflow-x-auto"><table class="w-full bg-white rounded-xl border border-gray-100 shadow-sm text-sm"><thead><tr class="bg-blue-50 text-left"><th class="p-4">Dimensión</th><th class="p-4">FONASA</th><th class="p-4">ISAPRE</th></tr></thead><tbody class="divide-y divide-gray-100"><tr><td class="p-4 font-medium">Sistema</td><td class="p-4">Público</td><td class="p-4">Privado</td></tr><tr><td class="p-4 font-medium">Costo</td><td class="p-4">7% sin costo extra</td><td class="p-4">7% + diferencia si plan es más caro</td></tr><tr><td class="p-4 font-medium">Tiempos espera</td><td class="p-4">Listas en sistema público</td><td class="p-4">Menor tiempo en clínicas privadas</td></tr><tr><td class="p-4 font-medium">Cobertura GES</td><td class="p-4">✅ Gratuita</td><td class="p-4">✅ Cubierta por ley</td></tr></tbody></table></div></section>
<section id="costos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Comparativa de costos</h2><p class="text-gray-700 mb-4">Ejemplo con renta de $1.000.000:</p><ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>FONASA:</strong> cotizas $70.000 sin costo adicional.</li><li><strong>ISAPRE:</strong> cotizas $70.000. Si el plan cuesta $60.000, ahorras $10.000 en excedentes. Si cuesta $85.000, pagas $15.000 extra.</li></ul></section>
<section id="perfiles" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué te conviene según tu perfil?</h2><div class="grid md:grid-cols-2 gap-6"><div class="bg-green-50 border border-green-100 rounded-xl p-5"><h3 class="font-bold text-green-900 mb-2">FONASA si…</h3><ul class="list-disc pl-4 text-green-800 space-y-1 text-sm"><li>Ingresos bajos</li><li>Vives cerca de hospital público</li><li>Poca atención médica</li><li>Enfermedades GES</li></ul></div><div class="bg-blue-50 border border-blue-100 rounded-xl p-5"><h3 class="font-bold text-blue-900 mb-2">ISAPRE si…</h3><ul class="list-disc pl-4 text-blue-800 space-y-1 text-sm"><li>Quieres clínicas privadas</li><li>Atención ambulatoria frecuente</li><li>Excedentes disponibles</li><li>Valoras telemedicina</li></ul></div></div></section>
<section id="cambio" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Cómo cambiarte</h2><ol class="list-decimal pl-6 text-gray-700 space-y-2"><li>Cotiza planes en ISAPREs.</li><li>Firma contrato con la nueva ISAPRE.</li><li>La ISAPRE gestiona el cambio.</li><li>Efectivo el primer día del mes siguiente.</li></ol></section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = ['¿Puedo tener ambos?' => 'No, son excluyentes.', '¿Qué es mejor para familia?' => 'Depende de renta y necesidades. ISAPRE ofrece planes familiares con cobertura para cargas.', '¿Los excedentes se pierden al cambiar?' => 'Sí, no se transfieren a FONASA.'];
$faq_titulo = 'Preguntas Frecuentes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
