<?php
/**
 * isapre/mejores-isapres.php
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
$page_title       = 'Mejores ISAPREs de Chile 2026 | Plan Salud Fácil';
$meta_description = 'Ranking de las mejores ISAPREs de Chile: Banmédica, Cruz Blanca, Colmena, Vida Tres y más. Comparativa de precios, cobertura y beneficios.';
$h1               = 'Las Mejores ISAPREs de Chile';
$lead             = 'Comparativa de las principales ISAPREs: analizamos coberturas, precios, redes de prestadores y satisfacción de afiliados.';
$svc_name         = 'Comparativa ISAPREs Chile';
$svc_description  = 'Ranking y comparativa de ISAPREs en Chile.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => 'BASE_URL/isapre/'], ['label' => 'Mejores', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [['id' => 'ranking', 'label' => 'Ranking 2026'], ['id' => 'detalle', 'label' => 'Detalle por ISAPRE'], ['id' => 'comparativa', 'label' => 'Tabla comparativa'], ['id' => 'elegir', 'label' => 'Cómo elegir']];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="ranking" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Ranking de ISAPREs</h2><div class="overflow-x-auto"><table class="w-full bg-white rounded-xl border border-gray-100 shadow-sm text-sm"><thead><tr class="bg-blue-50 text-left"><th class="p-4">#</th><th class="p-4">ISAPRE</th><th class="p-4">Afiliados</th><th class="p-4">Fortaleza</th></tr></thead><tbody class="divide-y divide-gray-100"><tr><td class="p-4 font-bold text-blue-600">1</td><td class="p-4">Banmédica</td><td class="p-4">1.2M+</td><td class="p-4">Red clínicas más grande</td></tr><tr><td class="p-4 font-bold text-blue-600">2</td><td class="p-4">Cruz Blanca</td><td class="p-4">800K+</td><td class="p-4">Mejor precio/cobertura</td></tr><tr><td class="p-4 font-bold text-blue-600">3</td><td class="p-4">Colmena</td><td class="p-4">700K+</td><td class="p-4">Planes flexibles</td></tr><tr><td class="p-4 font-bold text-blue-600">4</td><td class="p-4">Vida Tres</td><td class="p-4">600K+</td><td class="p-4">Beneficios medicamentos</td></tr><tr><td class="p-4 font-bold text-blue-600">5</td><td class="p-4">Nueva Masvida</td><td class="p-4">400K+</td><td class="p-4">Precios competitivos</td></tr></tbody></table></div></section>
<section id="detalle" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Detalle por ISAPRE</h2><h3 class="text-xl font-semibold mt-6 mb-2">Banmédica</h3><p class="text-gray-700">La más grande. Red de clínicas: Santa María, Dávila, Vespucio. Ideal para atención en Santiago.</p><h3 class="text-xl font-semibold mt-6 mb-2">Cruz Blanca</h3><p class="text-gray-700">Planes accesibles con buena cobertura ambulatoria. Excelente para jóvenes profesionales y familias.</p><h3 class="text-xl font-semibold mt-6 mb-2">Colmena</h3><p class="text-gray-700">Planes modulares personalizables. Buena cobertura dental.</p></section>
<section id="comparativa" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Tabla comparativa</h2><div class="overflow-x-auto"><table class="w-full bg-white rounded-xl border border-gray-100 shadow-sm text-sm"><thead><tr class="bg-blue-50 text-left"><th class="p-4">ISAPRE</th><th class="p-4">Plan joven (ref.)</th><th class="p-4">Telemedicina</th><th class="p-4">App</th></tr></thead><tbody class="divide-y divide-gray-100"><tr><td class="p-4">Banmédica</td><td class="p-4">$56.000</td><td class="p-4">✅</td><td class="p-4">✅</td></tr><tr><td class="p-4">Cruz Blanca</td><td class="p-4">$52.000</td><td class="p-4">✅</td><td class="p-4">✅</td></tr><tr><td class="p-4">Colmena</td><td class="p-4">$48.000</td><td class="p-4">✅</td><td class="p-4">✅</td></tr><tr><td class="p-4">Vida Tres</td><td class="p-4">$54.000</td><td class="p-4">✅</td><td class="p-4">✅</td></tr><tr><td class="p-4">Nueva Masvida</td><td class="p-4">$45.000</td><td class="p-4">✅</td><td class="p-4">✅</td></tr></tbody></table></div></section>
<section id="elegir" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Cómo elegir</h2><ol class="list-decimal pl-6 text-gray-700 space-y-3"><li>Define tu presupuesto (7% + adicional).</li><li>Identifica necesidades (consultas, hospitalización, maternidad).</li><li>Revisa clínicas de tu zona.</li><li>Compara al menos 3 ISAPREs.</li><li>Consulta con un asesor gratuito.</li></ol></section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = ['¿Cuál es la más barata?' => 'Nueva Masvida suele tener precios más competitivos.', '¿Cuál tiene mejor cobertura?' => 'Banmédica ofrece la red más amplia, pero el plan específico determina la cobertura.', '¿Puedo cambiarme?' => 'Sí, en cualquier momento.'];
$faq_titulo = 'Preguntas Frecuentes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
