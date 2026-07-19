<?php
/**
 * servicios/cambio-de-isapre.php
 * Migrado a template SEO piramidal (seo-page.php)
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
$page_title       = 'Cambio de Isapre: Asesoría Gratuita 100% Online | Plan Salud Fácil';
$meta_description = 'Te ayudamos a cambiarte de Isapre gratis y sin trámites. Comparamos todas las Isapres, gestionamos tu Declaración de Salud y firmas online. Asesoría 100% digital.';
$h1               = 'Te ayudamos a cambiarte de Isapre gratis y sin hacer trámites';
$lead             = 'Comparamos todas las Isapres del mercado, gestionamos tu Declaración de Salud y firmas tu nuevo contrato 100% online. En menos de 48 horas.';
$svc_name         = 'Cambio de Isapre';
$svc_description  = 'Asesoría gratuita para cambiarte de Isapre: comparamos planes, gestionamos tu Declaración de Salud y firmas online.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Cambio de Isapre', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'conviene', 'label' => '¿Me conviene cambiarme?'],
    ['id' => 'proceso', 'label' => '¿Cómo funciona el proceso?'],
    ['id' => 'requisitos', 'label' => 'Requisitos para cambiarte'],
    ['id' => 'comparativa', 'label' => '¿A qué Isapre cambiarme?'],
    ['id' => 'preexistencias', 'label' => '¿Qué pasa con las preexistencias?'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>

<section id="conviene" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Me conviene cambiarme de Isapre?</h2>

    <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-600 p-5 rounded-lg mb-6">
        <p class="font-semibold text-blue-900 text-lg">Sí, conviene cambiarse si han pasado más de 12 meses desde que contrataste tu plan actual y tu situación de vida o ingresos cambió. Podrías acceder a mejores coberturas por el mismo 7% de cotización obligatoria, sin pagar ni un peso extra.</p>
    </div>

    <p class="text-gray-700 leading-relaxed mb-4">La mayoría de las personas se mantienen en su Isapre por costumbre, perdiendo la oportunidad de acceder a mejores coberturas y beneficios. Si en el último año tuviste un cambio de sueldo, sumaste cargas familiares o simplemente tu plan actual ya no te satisface, es el momento ideal para comparar.</p>

    <div class="grid md:grid-cols-3 gap-4 mt-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
            <iconify-icon icon="mdi:cash-check" width="32" class="text-green-600 mx-auto mb-2"></iconify-icon>
            <h3 class="font-bold text-gray-900 mb-1">Sin costo extra</h3>
            <p class="text-sm text-gray-600">Nuestro servicio es 100% gratuito. Solo pagas tu cotización mensual.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
            <iconify-icon icon="mdi:clock-fast" width="32" class="text-blue-600 mx-auto mb-2"></iconify-icon>
            <h3 class="font-bold text-gray-900 mb-1">Menos de 48 horas</h3>
            <p class="text-sm text-gray-600">Proceso completamente digital. Sin filas ni papeleos.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-center">
            <iconify-icon icon="mdi:shield-check" width="32" class="text-blue-600 mx-auto mb-2"></iconify-icon>
            <h3 class="font-bold text-gray-900 mb-1">Sin riesgo</h3>
            <p class="text-sm text-gray-600">Revisamos tu caso antes de postular. Si hay riesgo, te avisamos.</p>
        </div>
    </div>
</section>

<section id="proceso" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">¿Cómo funciona el proceso de cambio?</h2>

    <div class="space-y-4">
        <div class="flex gap-4 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">1</div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Comparamos todas las Isapres por ti</h3>
                <p class="text-gray-600 text-sm">Analizamos tu perfil, ingresos, cargas y necesidades de salud para encontrar los 3 mejores planes entre todas las Isapres del mercado.</p>
            </div>
        </div>
        <div class="flex gap-4 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">2</div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Gestionamos tu Declaración de Salud</h3>
                <p class="text-gray-600 text-sm">Te guiamos paso a paso en el llenado de tu Declaración de Salud para maximizar las probabilidades de aceptación sin rechazos.</p>
            </div>
        </div>
        <div class="flex gap-4 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">3</div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Firmas tu nuevo contrato 100% online</h3>
                <p class="text-gray-600 text-sm">Recibes tu plan activado. Sin ir a sucursales, sin papeleo. Todo el proceso toma menos de 48 horas.</p>
            </div>
        </div>
    </div>
</section>

<section id="requisitos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Requisitos para cambiarte de Isapre</h2>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <ul class="space-y-3">
            <li class="flex items-start gap-3">
                <iconify-icon icon="mdi:check-circle" width="22" class="text-green-500 flex-shrink-0 mt-0.5"></iconify-icon>
                <span class="text-gray-700"><strong>Antigüedad mínima de 12 meses</strong> en tu Isapre actual. Es el requisito legal para poder cambiarte.</span>
            </li>
            <li class="flex items-start gap-3">
                <iconify-icon icon="mdi:check-circle" width="22" class="text-green-500 flex-shrink-0 mt-0.5"></iconify-icon>
                <span class="text-gray-700"><strong>No estar con licencia médica vigente</strong> al momento de la postulación.</span>
            </li>
            <li class="flex items-start gap-3">
                <iconify-icon icon="mdi:check-circle" width="22" class="text-green-500 flex-shrink-0 mt-0.5"></iconify-icon>
                <span class="text-gray-700"><strong>Completar la Declaración de Salud</strong> con información veraz sobre tu historial médico. Te guiamos en cada campo.</span>
            </li>
            <li class="flex items-start gap-3">
                <iconify-icon icon="mdi:check-circle" width="22" class="text-green-500 flex-shrink-0 mt-0.5"></iconify-icon>
                <span class="text-gray-700"><strong>Estar al día en tus cotizaciones</strong> de salud. Sin deudas previsionales pendientes.</span>
            </li>
            <li class="flex items-start gap-3">
                <iconify-icon icon="mdi:check-circle" width="22" class="text-green-500 flex-shrink-0 mt-0.5"></iconify-icon>
                <span class="text-gray-700"><strong>Cédula de identidad vigente</strong> para la firma electrónica del contrato.</span>
            </li>
        </ul>
    </div>
</section>

<section id="comparativa" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">¿A qué Isapre me conviene cambiarme?</h2>

    <p class="text-gray-700 leading-relaxed mb-6">La mejor Isapre para ti depende de tu perfil: edad, ingresos, cargas familiares y las clínicas donde prefieres atenderte. Estos son los factores clave que analizamos:</p>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-xl border border-gray-100 shadow-sm text-sm">
            <thead>
                <tr class="bg-blue-50 text-left">
                    <th class="p-4 font-bold">Factor</th>
                    <th class="p-4 font-bold">Qué evaluamos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="p-4 font-medium">Precio del plan</td>
                    <td class="p-4 text-gray-600">Comparamos el costo mensual sobre tu 7% de cotización para que no pagues de más.</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Cobertura ambulatoria</td>
                    <td class="p-4 text-gray-600">Porcentaje de bonificación en consultas, exámenes y procedimientos ambulatorios.</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Cobertura hospitalaria</td>
                    <td class="p-4 text-gray-600">Bonificación en hospitalizaciones, cirugías y días cama. Revisamos topes anuales.</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Red de clínicas</td>
                    <td class="p-4 text-gray-600">Convenios con clínicas y centros médicos cercanos a tu domicilio o trabajo.</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Beneficios extra</td>
                    <td class="p-4 text-gray-600">Telemedicina, descuentos en farmacias, seguros complementarios y programas preventivos.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="text-gray-700 leading-relaxed mt-6">Un asesor analiza estos 5 factores según tu caso específico y te presenta las 3 mejores opciones, sin costo.</p>
</section>

<section id="preexistencias" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">¿Qué pasa si tengo preexistencias?</h2>

    <p class="text-gray-700 leading-relaxed mb-6">Sí, puedes cambiarte de Isapre aunque tengas preexistencias. La clave está en declararlas correctamente y elegir la Isapre adecuada. Así funciona nuestro proceso:</p>

    <div class="grid md:grid-cols-2 gap-4">
        <div class="p-5 bg-blue-50 rounded-xl border border-blue-100">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                <iconify-icon icon="mdi:shield-account" class="text-blue-600" width="20"></iconify-icon>
                Análisis confidencial
            </h3>
            <p class="text-gray-600 text-sm">Revisamos tu historial médico de forma 100% confidencial antes de enviar cualquier documento a las Isapres.</p>
        </div>
        <div class="p-5 bg-green-50 rounded-xl border border-green-100">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                <iconify-icon icon="mdi:file-document-check" class="text-green-600" width="20"></iconify-icon>
                Postulación informada
            </h3>
            <p class="text-gray-600 text-sm">Solo postulamos a las Isapres donde tus preexistencias tienen mayor probabilidad de ser aceptadas.</p>
        </div>
    </div>
</section>

<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = [
    '¿Me conviene cambiarme de Isapre?' => 'Sí, conviene cambiarse si han pasado más de 12 meses desde que contrataste tu plan actual y tu situación de vida o ingresos cambió. Podrías acceder a mejores coberturas por el mismo 7% de cotización.',
    '¿Qué necesito para cambiarme de Isapre?' => 'Solo necesitas tener al menos un año de antigüedad en tu Isapre actual, no estar con licencia médica vigente y completar la Declaración de Salud. Nosotros te guiamos en cada paso.',
    '¿Cómo funciona el proceso de cambio de Isapre?' => 'Nosotros comparamos todas las Isapres del mercado por ti, gestionamos tu Declaración de Salud y firmas el nuevo contrato 100% online. El proceso completo toma menos de 48 horas.',
    '¿Cuánto cuesta cambiarse de Isapre?' => 'Nuestro servicio de asesoría y gestión de cambio es 100% gratuito. Solo pagas la cotización mensual de tu nuevo plan de salud, que se descuenta de tu 7% legal obligatorio.',
    '¿Puedo cambiarme de Isapre si tengo preexistencias?' => 'Sí, puedes cambiarte aunque tengas preexistencias. Analizamos tu historial médico de forma confidencial antes de postular para asegurar que el cambio sea aprobado sin contratiempos ni rechazos.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Cambio de Isapre';

// ── Formulario de contacto ─────────────────────────────
ob_start();
?>
<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_individual'); ?>
</div>
<?php
$secciones_form = ob_get_clean();
$secciones_html .= $secciones_form;

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
