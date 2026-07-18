<?php
/**
 * planes/individuales/index.php — Planes Individuales
 * 
 * Fusión con estética de /servicios/planes-individuales.php
 * Contenido: index.php (hub 4 perfiles) + adulto.php (coberturas detalladas)
 * Estilo: Schema.org FAQ JSON-LD, answer-direct, tarjetas numeradas, grid cards
 * Punto de retorno: git revert <este_commit>
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
$page_title       = 'Planes de Salud Individuales: Cobertura a tu Medida | Plan Salud Fácil';
$meta_description = 'Encuentra el mejor plan de Isapre para ti sin cargas. Cobertura en hospitalización, ambulatorio y prevención. Asesoría 100% gratis y online.';
$h1               = 'Planes de ISAPRE Individuales';
$lead             = 'Un plan de salud pensado para ti, sin cargas. Desde el profesional joven que empieza hasta el adulto mayor que busca la mejor protección.';
$svc_name         = 'Planes Individuales de ISAPRE';
$svc_description  = 'Planes de ISAPRE para personas sin cargas: jóvenes, adultos, deportistas y adultos mayores. Cotiza gratis.';
$cta_texto        = 'Cotizar por WhatsApp';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs (3 niveles) ──────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => 'BASE_URL/planes/'], ['label' => 'Individuales', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'que-es',        'label' => '¿Qué es?'],
    ['id' => 'beneficios',    'label' => 'Beneficios'],
    ['id' => 'perfiles',      'label' => 'Tu perfil'],
    ['id' => 'coberturas',    'label' => 'Coberturas'],
    ['id' => 'isapres',       'label' => 'Mejores isapres'],
];

// ── Contenido (se renderiza dentro de seo-page.php) ─────
ob_start();
?>

<!-- FAQ Schema.org Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type":"Question","name":"¿Qué es un plan de salud individual?","acceptedAnswer":{"@type":"Answer","text":"Es un contrato previsional para un único titular sin beneficiarios. Ideal para profesionales independientes, jóvenes que inician su vida laboral, o adultos sin cargas."}},
    {"@type":"Question","name":"¿Los planes individuales cubren hospitalización?","acceptedAnswer":{"@type":"Answer","text":"Sí, todos los planes incluyen cobertura de hospitalización (70-90%). Muchos incluyen urgencias en el extranjero."}},
    {"@type":"Question","name":"¿Puedo pasar de plan individual a familiar?","acceptedAnswer":{"@type":"Answer","text":"Sí. La mayoría de las isapres permiten migrar sin perder antigüedad ni re-evaluar tu Declaración de Salud."}},
    {"@type":"Question","name":"¿Qué chequeos preventivos cubre?","acceptedAnswer":{"@type":"Answer","text":"Muchos incluyen chequeo ejecutivo anual, dermatología preventiva, evaluación cardiovascular, ginecología/urología."}},
    {"@type":"Question","name":"¿Conviene un plan más caro entre 30 y 55?","acceptedAnswer":{"@type":"Answer","text":"Generalmente sí. Una hospitalización inesperada puede salir muy cara sin buena cobertura."}}
  ]
}
</script>

<style>
.answer-direct{background:linear-gradient(135deg,#eff6ff,#f0fdf4);border-left:4px solid #2563eb;padding:16px 20px;border-radius:0 12px 12px 0;margin-bottom:16px;font-size:15px;color:#374151;line-height:1.7}
</style>

<!-- ====== SECCIÓN 1: ¿Qué es? ====== -->
<section id="que-es" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué es un plan de salud individual?</h2>
    <div class="answer-direct">
        Un plan de salud individual es un contrato previsional para un único titular sin beneficiarios. Ideal para profesionales independientes, jóvenes que inician su vida laboral, o adultos sin cargas familiares. Concentra todo tu 7% en tus propias coberturas.
    </div>
    <p class="text-gray-600 mb-6">Sin cargas familiares, tus prioridades son distintas: optimizar tu presupuesto en telemedicina, especialistas, salud mental o medicina deportiva.</p>
    <div class="grid md:grid-cols-3 gap-6">
        <div class="text-center p-6"><div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">1</div><h3 class="font-bold text-gray-900 mb-2">Profesional independiente</h3><p class="text-gray-600 text-sm">Concentras tu 7% en coberturas ambulatorias de alto uso y generas excedentes.</p></div>
        <div class="text-center p-6"><div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">2</div><h3 class="font-bold text-gray-900 mb-2">Te independizas</h3><p class="text-gray-600 text-sm">Al empezar tu vida laboral, necesitas tu propia protección con coberturas para tu etapa.</p></div>
        <div class="text-center p-6"><div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-green-200">3</div><h3 class="font-bold text-gray-900 mb-2">Adulto sin cargas</h3><p class="text-gray-600 text-sm">Ingresos estables, acceso rápido a especialistas y clínicas sin pagar de más.</p></div>
    </div>
</section>

<!-- ====== SECCIÓN 2: Beneficios ====== -->
<section id="beneficios" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Beneficios de un plan individual</h2>
    <div class="answer-direct">Máxima eficiencia de tu 7%, coberturas enfocadas en tus intereses, y generación rápida de excedentes si tu sueldo es alto. Pagas solo por lo que realmente usas.</div>
    <div class="grid md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center"><div class="w-14 h-14 bg-blue-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-blue-200">⚡</div><strong class="block text-gray-900 mb-2">Eficiencia del 7%</strong><p class="text-gray-600 text-sm">Todo tu 7% va a tus coberturas. Sin promediar con cargas. Mejores topes y menos copagos.</p></div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center"><div class="w-14 h-14 bg-purple-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-purple-200">🎯</div><strong class="block text-gray-900 mb-2">Enfocado en ti</strong><p class="text-gray-600 text-sm">Kinesiología, telemedicina, salud mental. Los beneficios que realmente necesitas.</p></div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center"><div class="w-14 h-14 bg-green-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-green-200">💰</div><strong class="block text-gray-900 mb-2">Excedentes rápidos</strong><p class="text-gray-600 text-sm">Sueldo alto = acumulás rápido. Usalos en bonos, lentes, medicamentos o atenciones sin copago.</p></div>
    </div>
    <div class="p-5 bg-blue-50 rounded-xl border border-blue-100 mt-6"><p class="text-gray-700 font-medium">💡 <strong>Dato clave:</strong> Los planes individuales tienen primas más bajas que los familiares. Con el mismo 7%, accedés a mejores coberturas.</p></div>
</section>

<!-- ====== SECCIÓN 3: Perfiles ====== -->
<section id="perfiles" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">El plan ideal según tu perfil</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100"><div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg font-bold">🧑‍💻</div><h3 class="font-bold text-gray-900 mb-2">Plan Joven (18-30)</h3><p class="text-gray-600 text-sm">Económico. Cobertura esencial en consultas, exámenes y urgencias con copagos accesibles.</p></div>
        <div class="bg-gradient-to-br from-emerald-50 to-white rounded-xl p-6 border border-emerald-100"><div class="w-12 h-12 bg-emerald-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg font-bold">🏢</div><h3 class="font-bold text-gray-900 mb-2">Plan Adulto (30-55)</h3><p class="text-gray-600 text-sm">Equilibrado. Hospitalización 90%+, ambulatorio 80-90%, telemedicina y proyección familiar.</p></div>
        <div class="bg-gradient-to-br from-orange-50 to-white rounded-xl p-6 border border-orange-100"><div class="w-12 h-12 bg-orange-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg font-bold">🏃</div><h3 class="font-bold text-gray-900 mb-2">Plan Deportista</h3><p class="text-gray-600 text-sm">Medicina deportiva, kinesiología, traumatología. Para quienes el cuerpo es su herramienta.</p></div>
        <div class="bg-gradient-to-br from-violet-50 to-white rounded-xl p-6 border border-violet-100"><div class="w-12 h-12 bg-violet-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg font-bold">🧓</div><h3 class="font-bold text-gray-900 mb-2">Plan Adulto Mayor (60+)</h3><p class="text-gray-600 text-sm">Máxima cobertura. Hospitalizaciones, enfermedades crónicas y atención geriátrica.</p></div>
    </div>
</section>

<!-- ====== SECCIÓN 4: Coberturas detalladas (de adulto.php) ====== -->
<section id="coberturas" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Cobertura equilibrada para tu etapa</h2>
    <div class="answer-direct">Un plan adulto (30-55) debe priorizar hospitalización sobre 90%, buen ambulatorio, telemedicina premium, y flexibilidad para migrar a familiar cuando tu vida cambie.</div>
    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <div class="p-5 bg-blue-50 rounded-xl border border-blue-100"><h3 class="font-bold text-gray-900 mb-2">🏥 Hospitalización 90%+</h3><p class="text-gray-600 text-sm">Cirugías, accidentes, urgencias. Cubierto. Incluye UCI y habitación individual.</p></div>
        <div class="p-5 bg-green-50 rounded-xl border border-green-100"><h3 class="font-bold text-gray-900 mb-2">🩺 Ambulatorio 80-90%</h3><p class="text-gray-600 text-sm">Consultas con especialistas, exámenes, imagenología. Acceso rápido.</p></div>
        <div class="p-5 bg-purple-50 rounded-xl border border-purple-100"><h3 class="font-bold text-gray-900 mb-2">📱 Telemedicina premium</h3><p class="text-gray-600 text-sm">Consultas sin salir de casa. Ideal si viajas o tenés poco tiempo.</p></div>
        <div class="p-5 bg-amber-50 rounded-xl border border-amber-100"><h3 class="font-bold text-gray-900 mb-2">🌍 Cobertura internacional</h3><p class="text-gray-600 text-sm">Urgencias en el extranjero. Tranquilidad si viajas por trabajo o placer.</p></div>
    </div>

    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 mt-8">Medicina preventiva</h2>
    <p class="text-gray-600 mb-4">A los 30-55, prevenir es la diferencia entre un susto y un problema grave:</p>
    <div class="grid md:grid-cols-2 gap-4">
        <div class="flex items-start gap-3 p-3"><div class="w-8 h-8 bg-green-600 text-white rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold">✓</div><span class="text-gray-700 text-sm">Chequeo ejecutivo anual (sangre, cardiaco, imagenología)</span></div>
        <div class="flex items-start gap-3 p-3"><div class="w-8 h-8 bg-green-600 text-white rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold">✓</div><span class="text-gray-700 text-sm">Dermatología preventiva (control de lunares)</span></div>
        <div class="flex items-start gap-3 p-3"><div class="w-8 h-8 bg-green-600 text-white rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold">✓</div><span class="text-gray-700 text-sm">Evaluación cardiovascular (electro, prueba de esfuerzo)</span></div>
        <div class="flex items-start gap-3 p-3"><div class="w-8 h-8 bg-green-600 text-white rounded-lg flex items-center justify-center flex-shrink-0 text-sm font-bold">✓</div><span class="text-gray-700 text-sm">Ginecología / Urología preventiva</span></div>
    </div>

    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 mt-8">Preparado para el futuro</h2>
    <div class="p-5 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl border border-blue-100"><p class="text-gray-700">Quizás hoy cotizas solo, pero en 2 años podrías tener pareja e hijos. Elegí un plan que permita <strong>migrar a familiar sin perder antigüedad</strong> ni re-evaluar tu Declaración de Salud.</p></div>
</section>

<!-- ====== SECCIÓN 5: Mejores isapres ====== -->
<section id="isapres" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para planes individuales</h2>
    <div class="answer-direct">La mejor isapre depende de: acceso a las clínicas que preferís, cobertura ambulatoria, y facilidad para migrar a familiar cuando lo necesites.</div>
    <div class="grid md:grid-cols-3 gap-6 mt-6">
        <div class="bg-gradient-to-b from-white to-blue-50 rounded-xl p-6 border border-blue-100 text-center"><div class="w-14 h-14 bg-blue-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-lg font-bold">B</div><h3 class="font-bold text-gray-900 mb-2">Banmédica</h3><p class="text-gray-600 text-sm">Red más grande. Mejores clínicas sin esperar. Cobertura internacional sólida.</p></div>
        <div class="bg-gradient-to-b from-white to-yellow-50 rounded-xl p-6 border border-yellow-100 text-center"><div class="w-14 h-14 bg-yellow-500 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-lg font-bold">C</div><h3 class="font-bold text-gray-900 mb-2">Colmena</h3><p class="text-gray-600 text-sm">Ideal si proyectás familia. Maternidad top y buena red de clínicas.</p></div>
        <div class="bg-gradient-to-b from-white to-indigo-50 rounded-xl p-6 border border-indigo-100 text-center"><div class="w-14 h-14 bg-indigo-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-lg font-bold">CB</div><h3 class="font-bold text-gray-900 mb-2">Cruz Blanca</h3><p class="text-gray-600 text-sm">Equilibrio precio-cobertura. Telemedicina y salud mental destacadas.</p></div>
    </div>
</section>

<!-- ====== FORMULARIO ====== -->
<div id="formulario" class="max-w-4xl mx-auto py-10">
    <?php render_component('formulario_individual'); ?>
</div>

<?php
$secciones_html = ob_get_clean();

// ── FAQ ──────────────────────────────────────────────────
$faq_preguntas = [
    '¿Puedo contratar un plan individual si tengo cargas?' => 'No, los planes individuales son solo para el titular. Si tienes cargas necesitas un plan familiar.',
    '¿Qué plan individual es más barato?' => 'Generalmente el plan joven, diseñado para personas con bajo riesgo de salud y menor uso del sistema.',
    '¿Los planes individuales cubren hospitalización?' => 'Sí, todos los planes incluyen cobertura de hospitalización. El porcentaje varía según el plan (70-90%).',
    '¿Puedo pasar de plan individual a familiar?' => 'Sí. La mayoría de las isapres permiten migrar sin perder antigüedad ni pasar por nueva evaluación de salud.',
    '¿Qué chequeos preventivos cubre?' => 'Muchos incluyen chequeo ejecutivo anual, dermatología, evaluación cardiovascular y ginecología/urología preventiva.',
    '¿Conviene un plan más caro entre 30 y 55 años?' => 'Generalmente sí. Una hospitalización inesperada puede salir muy cara sin buena cobertura.',
];
$faq_titulo = 'Preguntas Frecuentes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
