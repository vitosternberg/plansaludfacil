<?php
/**
 * isapre/que-es.php
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
$page_title       = '¿Qué es una ISAPRE? Guía Completa | Plan Salud Fácil';
$meta_description = '¿Qué es una ISAPRE? Descubre cómo funciona el sistema de salud privado chileno, requisitos, beneficios y diferencias con FONASA. Guía actualizada.';
$h1               = '¿Qué es una ISAPRE?';
$lead             = 'Una ISAPRE (Institución de Salud Previsional) es una entidad privada que administra tu cotización obligatoria de salud del 7% y te ofrece planes con distintas coberturas médicas.';
$svc_name         = 'Información sobre ISAPREs';
$svc_description  = 'Guía completa sobre qué es una ISAPRE y cómo funciona.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'ISAPRE', 'url' => 'BASE_URL/isapres/'], ['label' => '¿Qué es?', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [['id' => 'definicion', 'label' => 'Definición de ISAPRE'], ['id' => 'historia', 'label' => 'Historia del sistema'], ['id' => 'comparativa', 'label' => 'ISAPRE vs FONASA'], ['id' => 'requisitos', 'label' => '¿Quién puede afiliarse?']];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="definicion" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Definición de ISAPRE</h2>
    <p class="text-gray-700 leading-relaxed mb-4">ISAPRE significa <strong>Institución de Salud Previsional</strong>. Son entidades privadas, reguladas por la Superintendencia de Salud, que administran la cotización obligatoria de salud (7% de tu renta imponible) y ofrecen planes de salud con distintas coberturas, redes de prestadores y beneficios adicionales.</p>
    <p class="text-gray-700 leading-relaxed mb-4">Actualmente existen <strong>6 ISAPREs abiertas en Chile</strong>: Banmédica, Cruz Blanca, Colmena, Vida Tres, Nueva Masvida y Consalud.</p>
</section>
<section id="historia" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Historia del sistema ISAPRE</h2><div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6"><ol class="list-decimal pl-6 text-gray-700 space-y-2"><li><strong>1981:</strong> Creación del sistema privado de salud.</li><li><strong>2005:</strong> Ley AUGE/GES garantiza cobertura para 85+ patologías.</li><li><strong>2019:</strong> Reforma que prohíbe discriminación por edad o preexistencias.</li><li><strong>2023-2024:</strong> Fallo Corte Suprema sobre tabla de factores.</li></ol></div></section>
<section id="comparativa" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">ISAPRE vs FONASA</h2><div class="overflow-x-auto"><table class="w-full bg-white rounded-xl border border-gray-100 shadow-sm text-sm"><thead><tr class="bg-blue-50 text-left"><th class="p-4">Característica</th><th class="p-4">ISAPRE</th><th class="p-4">FONASA</th></tr></thead><tbody class="divide-y divide-gray-100"><tr><td class="p-4 font-medium">Tipo</td><td class="p-4">Sistema privado</td><td class="p-4">Sistema público</td></tr><tr><td class="p-4 font-medium">Cotización</td><td class="p-4">7% + diferencia si plan es más caro</td><td class="p-4">7% obligatorio</td></tr><tr><td class="p-4 font-medium">Lista espera</td><td class="p-4">Menor tiempo en clínicas privadas</td><td class="p-4">Listas en hospitales públicos</td></tr></tbody></table></div></section>
<section id="requisitos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28"><h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Quién puede afiliarse?</h2><ul class="list-disc pl-6 text-gray-700 space-y-2"><li><strong>Trabajadores dependientes</strong> con contrato vigente</li><li><strong>Independientes</strong> que emiten boletas de honorarios</li><li><strong>Pensionados</strong> que cotizan de su pensión</li><li><strong>Cargas familiares:</strong> cónyuge, hijos y otras cargas legales</li></ul></section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = ['¿Qué significa ISAPRE?' => 'Institución de Salud Previsional. Entidad privada que administra tu cotización de salud.', '¿Es obligatorio estar en una ISAPRE?' => 'No. Puedes elegir entre ISAPRE o FONASA. La cotización del 7% es obligatoria en ambos.', '¿Cuántas ISAPREs hay?' => 'Actualmente 6 ISAPREs abiertas: Banmédica, Cruz Blanca, Colmena, Vida Tres, Nueva Masvida y Consalud.', '¿Puedo tener ISAPRE y FONASA?' => 'No, son sistemas excluyentes. Debes elegir uno.'];
$faq_titulo = 'Preguntas Frecuentes sobre ISAPRE';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
