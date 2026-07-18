<?php
/**
 * planes/familiares/index.php — Planes Familiares (hub unificado)
 * 
 * Fusión de index.php (hub con 3 perfiles) + con-cargas.php (contenido detallado).
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
$page_title       = 'Planes de ISAPRE Familiares con Cargas: Cobertura para tu Familia | Plan Salud Fácil';
$meta_description = 'Planes de ISAPRE para familias con cargas. Protege a tu pareja e hijos con la mejor cobertura. Pediatría, maternidad y beneficios grupales. Cotiza gratis.';
$h1               = 'Planes de ISAPRE Familiares';
$lead             = 'Tu familia merece la mejor cobertura. Los planes familiares te permiten proteger a tu pareja e hijos bajo un mismo plan, optimizando el 7% de cada uno y accediendo a beneficios grupales que un plan individual no ofrece.';
$svc_name         = 'Planes Familiares de ISAPRE';
$svc_description  = 'Planes de ISAPRE para familias: cobertura para cónyuge, hijos y otras cargas legales. Cotiza gratis.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs (3 niveles) ──────────────────────────────
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

<!-- ====== Preferencia Natal (del index original) ====== -->
<section id="preferencia-natal" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Preferencia Natal</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes familiares con cobertura reforzada para embarazo, parto, control prenatal y el primer año de vida del bebé. Incluye programas de acompañamiento, salas cuna y cobertura pediátrica prioritaria. Ideal para parejas que están planificando o esperando un hijo.</p>
</section>

<!-- ====== Plan con Cargas (fusionado: contenido completo de con-cargas.php) ====== -->
<section id="con-cargas" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">

    <!-- Coberturas para toda la familia -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas para toda la familia</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-8">
        <li><strong>Pediatría:</strong> Controles de niño sano, vacunas y urgencias pediátricas cubiertas.</li>
        <li><strong>Maternidad:</strong> Parto, cesárea, prenatal y postnatal. Cobertura completa para mamá y bebé.</li>
        <li><strong>Hospitalización:</strong> Cobertura para todos los integrantes, incluyendo UCI y cirugías.</li>
        <li><strong>Ambulatorio:</strong> Consultas médicas, especialistas y exámenes con copago familiar reducido.</li>
        <li><strong>Dental:</strong> Limpieza gratis para cada integrante una vez al año.</li>
    </ul>

    <!-- ¿Cuánto cuesta un plan familiar? -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Cuánto cuesta un plan familiar?</h2>
    <p class="text-gray-700 mb-4">El precio depende de la cantidad de cargas, las edades y la isapre. Como referencia:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-2">
        <li>Familia de 3 (2 adultos + 1 hijo): desde $120.000/mes.</li>
        <li>Familia de 4 (2 adultos + 2 hijos): desde $150.000/mes.</li>
        <li>Familia de 5 o más: desde $180.000/mes.</li>
    </ul>
    <p class="text-sm text-gray-500 mb-8">*Cada adulto aporta su 7%. Si ambos trabajan, se suman las cotizaciones.</p>

    <!-- Beneficios de un plan familiar -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Beneficios de un plan familiar</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-8">
        <li><strong>Un solo plan:</strong> Todos bajo la misma cobertura. Sin planes separados que complican.</li>
        <li><strong>Copago familiar:</strong> Topes de gasto anual por grupo, no por persona.</li>
        <li><strong>Excedentes compartidos:</strong> Si generas, los usan todos los integrantes.</li>
        <li><strong>Antigüedad conjunta:</strong> Si luego te separas, cada uno conserva su antigüedad.</li>
    </ul>

    <!-- Mejores isapres para familias -->
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para familias</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2 mb-8">
        <li><strong>Colmena:</strong> Excelente cobertura de maternidad y pediatría. Ideal para familias en crecimiento.</li>
        <li><strong>Banmédica:</strong> La red más grande. Ideal si quieres elegir dónde atenderte.</li>
        <li><strong>Cruz Blanca:</strong> Buen balance precio-cobertura con foco en prevención.</li>
    </ul>

</section>

<!-- ====== Plan Monoparental (del index original) ====== -->
<section id="monoparentales" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Plan Monoparental</h2>
    <p class="text-gray-700 leading-relaxed mb-6">Planes adaptados a padres o madres solteros con hijos como cargas. Cobertura equilibrada con precios accesibles, priorizando la protección de los niños sin descuidar la salud del titular. Ideal para familias con un solo adulto a cargo.</p>
</section>

<!-- ====== Formulario de cotización (heredado de con-cargas.php) ====== -->
<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_familia'); ?>
</div>

<?php
$secciones_html = ob_get_clean();

// ── FAQ fusionado ────────────────────────────────────────
$faq_preguntas = [
    '¿A quiénes puedo incluir como carga familiar?' => 'Cónyuge, hijos hasta 18 años (25 si estudian), y en algunos casos padres adultos mayores que dependan económicamente de ti.',
    '¿Los planes familiares cubren embarazos?' => 'Sí, todos los planes cubren embarazo. Los planes con preferencia natal ofrecen coberturas adicionales y mejores condiciones.',
    '¿Puedo agregar a mi pareja si no estamos casados?' => 'Sí, puedes agregar a tu conviviente como carga acreditando la convivencia.',
    '¿Mis hijos están cubiertos hasta qué edad?' => 'Hasta los 25 años si están estudiando, o de por vida si tienen una discapacidad.',
    '¿Qué pasa si me separo?' => 'Cada adulto puede tomar un plan individual conservando su antigüedad. Los hijos quedan como cargas de uno de los padres.',
    '¿Puedo agregar una carga después de contratar el plan?' => 'Sí, puedes agregar cargas en cualquier momento presentando la documentación correspondiente.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Planes Familiares';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
