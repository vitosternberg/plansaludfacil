<?php
/**
 * planes/familiares/index.php — Planes Familiares
 * 
 * Fusión con estética visual (Schema.org FAQ, answer-direct, grid cards).
 * Contenido: index.php (hub 3 perfiles) + con-cargas.php (coberturas detalladas).
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
$page_title       = 'Planes de ISAPRE Familiares | Cotizar Plan de Salud Isapre | Plan Salud Fácil';
$meta_description = 'Cotiza tu plan de salud isapre familiar. Cotiza tu plan de salud isapre familiar. Planes de ISAPRE para familias con cargas. Protege a tu pareja e hijos con la mejor cobertura. Pediatría, maternidad y beneficios grupales. Cotiza tu plan de isapre y contrata el mejor plan isapre para tu familia.';
$h1               = 'Planes de ISAPRE Familiares';
$lead             = 'Protege a toda tu familia con un plan que se adapte a su composición y necesidades. Desde planes con cobertura reforzada para embarazos hasta opciones para familias monoparentales.';
$svc_name         = 'Planes Familiares de ISAPRE';
$svc_description  = 'Planes de ISAPRE para familias: cobertura para cónyuge, hijos y otras cargas legales. Cotiza tu plan de isapre y contrata el mejor plan isapre para tu familia.';
$cta_texto        = 'Cotizar ahora';
$cta_link         = '#formulario';

// ── Breadcrumbs (3 niveles) ──────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Planes', 'url' => 'BASE_URL/planes/'], ['label' => 'Familiares', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'que-es',      'label' => '¿Qué es?'],
    ['id' => 'coberturas',  'label' => 'Coberturas'],
    ['id' => 'precios',     'label' => 'Precios'],
    ['id' => 'beneficios',  'label' => 'Beneficios'],
    ['id' => 'isapres',     'label' => 'Mejores isapres'],
];

// ── Contenido ────────────────────────────────────────────
ob_start();
?>

<!-- FAQ Schema.org Structured Data -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type":"Question","name":"¿A quiénes puedo incluir como carga familiar?","acceptedAnswer":{"@type":"Answer","text":"Cónyuge, hijos hasta 25 años si estudian, y en algunos casos padres adultos mayores que dependan económicamente de ti."}},
    {"@type":"Question","name":"¿Los planes familiares cubren embarazos?","acceptedAnswer":{"@type":"Answer","text":"Sí, todos los planes cubren embarazo. Los planes con preferencia natal ofrecen coberturas adicionales y mejores condiciones para mamá y bebé."}},
    {"@type":"Question","name":"¿Puedo agregar a mi pareja si no estamos casados?","acceptedAnswer":{"@type":"Answer","text":"Sí, puedes agregar a tu conviviente como carga acreditando la convivencia ante la Isapre."}},
    {"@type":"Question","name":"¿Qué pasa si me separo?","acceptedAnswer":{"@type":"Answer","text":"Cada adulto puede tomar un plan individual conservando su antigüedad. Los hijos quedan como cargas de uno de los padres."}},
    {"@type":"Question","name":"¿Cuánto cuesta un plan familiar?","acceptedAnswer":{"@type":"Answer","text":"Depende de la cantidad de cargas. Familia de 3 desde $120.000/mes, familia de 4 desde $150.000/mes. Cada adulto aporta su 7% y se suman las cotizaciones."}}
  ]
}
</script>

<style>
.answer-direct{background:linear-gradient(135deg,#eff6ff,#f0fdf4);border-left:4px solid #2563eb;padding:16px 20px;border-radius:0 12px 12px 0;margin-bottom:16px;font-size:15px;color:#374151;line-height:1.7}
</style>

<div class="max-w-3xl mx-auto px-4">

<!-- ====== SECCIÓN 1: ¿Qué es un plan familiar? ====== -->
<section id="que-es" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué es un plan de salud familiar?</h2>
    <div class="answer-direct">
        Un plan familiar te permite proteger a tu pareja e hijos bajo un mismo contrato de Isapre, optimizando el 7% de cada adulto y accediendo a beneficios grupales que un plan individual no ofrece. Todos bajo la misma cobertura, sin planes separados.
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-6">
        <div class="text-center p-6">
            <div class="w-16 h-16 bg-pink-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-pink-200">👶</div>
            <h3 class="font-bold text-gray-900 mb-2">Familia en crecimiento</h3>
            <p class="text-gray-600 text-sm">Están esperando un hijo o planean hacerlo. Necesitan la mejor cobertura de maternidad y pediatría desde el día uno.</p>
        </div>
        <div class="text-center p-6">
            <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">👨‍👩‍👧</div>
            <h3 class="font-bold text-gray-900 mb-2">Familia constituida</h3>
            <p class="text-gray-600 text-sm">Pareja con hijos que necesitan cobertura integral. Consultas pediátricas, controles de adulto y programas preventivos para todos.</p>
        </div>
        <div class="text-center p-6">
            <div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">👩‍👦</div>
            <h3 class="font-bold text-gray-900 mb-2">Familia monoparental</h3>
            <p class="text-gray-600 text-sm">Un solo adulto a cargo. Cobertura equilibrada con precios accesibles, priorizando la protección de los niños.</p>
        </div>
    </div>
</section>

<!-- ====== SECCIÓN 2: Coberturas (de con-cargas.php) ====== -->
<section id="coberturas" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas para toda la familia</h2>
    <div class="answer-direct">
        Los planes familiares cubren desde el control del niño sano hasta cirugías complejas. La clave está en elegir uno que equilibre las necesidades de todos los integrantes sin pagar de más.
    </div>

    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <div class="p-5 bg-pink-50 rounded-xl border border-pink-100">
            <h3 class="font-bold text-gray-900 mb-2">👶 Pediatría</h3>
            <p class="text-gray-600 text-sm">Controles de niño sano, vacunas, urgencias pediátricas. Todo cubierto sin letra chica.</p>
        </div>
        <div class="p-5 bg-purple-50 rounded-xl border border-purple-100">
            <h3 class="font-bold text-gray-900 mb-2">🤰 Maternidad</h3>
            <p class="text-gray-600 text-sm">Parto, cesárea, prenatal y postnatal. Cobertura completa para mamá y bebé desde la primera ecografía.</p>
        </div>
        <div class="p-5 bg-blue-50 rounded-xl border border-blue-100">
            <h3 class="font-bold text-gray-900 mb-2">🏥 Hospitalización</h3>
            <p class="text-gray-600 text-sm">Cobertura para todos los integrantes, incluyendo UCI pediátrica y cirugías de alta complejidad.</p>
        </div>
        <div class="p-5 bg-green-50 rounded-xl border border-green-100">
            <h3 class="font-bold text-gray-900 mb-2">🦷 Dental</h3>
            <p class="text-gray-600 text-sm">Limpieza gratis para cada integrante una vez al año. Ortodoncia y tratamientos con copago reducido.</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 mt-4">
        <div class="p-5 bg-amber-50 rounded-xl border border-amber-100">
            <h3 class="font-bold text-gray-900 mb-2">🩺 Ambulatorio</h3>
            <p class="text-gray-600 text-sm">Consultas médicas, especialistas y exámenes con copago familiar reducido para todos.</p>
        </div>
        <div class="p-5 bg-indigo-50 rounded-xl border border-indigo-100">
            <h3 class="font-bold text-gray-900 mb-2">📱 Telemedicina</h3>
            <p class="text-gray-600 text-sm">Consultas rápidas sin salir de casa. Ideal cuando hay niños chicos y cada salida es una odisea.</p>
        </div>
    </div>
</section>

<!-- ====== SECCIÓN 3: Precios ====== -->
<section id="precios" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Cuánto cuesta un plan familiar?</h2>
    <div class="answer-direct">
        Cada adulto aporta su 7% legal. Si ambos trabajan, se suman las cotizaciones y pueden acceder a un mejor plan sin esfuerzo adicional. El precio final depende de la cantidad de cargas y sus edades.
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-6">
        <div class="bg-gradient-to-b from-white to-blue-50 rounded-xl p-6 border border-blue-100 text-center">
            <div class="text-3xl mb-3">👨‍👩‍👧</div>
            <div class="text-2xl font-bold text-blue-700 mb-1">$120.000</div>
            <p class="text-gray-500 text-xs mb-1">desde / mes</p>
            <p class="text-gray-600 text-sm font-medium">Familia de 3</p>
            <p class="text-gray-500 text-xs mt-1">2 adultos + 1 hijo</p>
        </div>
        <div class="bg-gradient-to-b from-white to-emerald-50 rounded-xl p-6 border border-emerald-100 text-center">
            <div class="text-3xl mb-3">👨‍👩‍👧‍👦</div>
            <div class="text-2xl font-bold text-emerald-700 mb-1">$150.000</div>
            <p class="text-gray-500 text-xs mb-1">desde / mes</p>
            <p class="text-gray-600 text-sm font-medium">Familia de 4</p>
            <p class="text-gray-500 text-xs mt-1">2 adultos + 2 hijos</p>
        </div>
        <div class="bg-gradient-to-b from-white to-violet-50 rounded-xl p-6 border border-violet-100 text-center">
            <div class="text-3xl mb-3">👨‍👩‍👧‍👦➕</div>
            <div class="text-2xl font-bold text-violet-700 mb-1">$180.000</div>
            <p class="text-gray-500 text-xs mb-1">desde / mes</p>
            <p class="text-gray-600 text-sm font-medium">Familia de 5+</p>
            <p class="text-gray-500 text-xs mt-1">2 adultos + 3+ hijos</p>
        </div>
    </div>

    <div class="p-5 bg-blue-50 rounded-xl border border-blue-100 mt-6">
        <p class="text-gray-700 font-medium">💡 <strong>Dato clave:</strong> Si ambos trabajan y cada uno gana $1.500.000, entre los dos suman $210.000 mensuales de cotización (7% × 2). Eso les permite acceder a planes familiares de primer nivel.</p>
    </div>
</section>

<!-- ====== SECCIÓN 4: Beneficios ====== -->
<section id="beneficios" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Beneficios de un plan familiar</h2>
    <div class="answer-direct">
        Un solo plan para todos significa: sin papeles separados, un solo copago familiar, excedentes que se comparten, y antigüedad que se conserva si algún día necesitan separar los planes.
    </div>

    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100">
            <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center flex-shrink-0 text-xl">📋</div>
            <div><h3 class="font-bold text-gray-900 mb-1">Un solo plan</h3><p class="text-gray-600 text-sm">Todos bajo la misma cobertura. Sin planes separados que complican la administración.</p></div>
        </div>
        <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100">
            <div class="w-12 h-12 bg-green-600 text-white rounded-xl flex items-center justify-center flex-shrink-0 text-xl">🛡️</div>
            <div><h3 class="font-bold text-gray-900 mb-1">Copago familiar</h3><p class="text-gray-600 text-sm">Topes de gasto anual por grupo familiar, no por persona. Más económico para todos.</p></div>
        </div>
        <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100">
            <div class="w-12 h-12 bg-purple-600 text-white rounded-xl flex items-center justify-center flex-shrink-0 text-xl">💰</div>
            <div><h3 class="font-bold text-gray-900 mb-1">Excedentes compartidos</h3><p class="text-gray-600 text-sm">Si generas excedentes, los usan todos los integrantes. Nadie pierde plata.</p></div>
        </div>
        <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100">
            <div class="w-12 h-12 bg-amber-600 text-white rounded-xl flex items-center justify-center flex-shrink-0 text-xl">🔒</div>
            <div><h3 class="font-bold text-gray-900 mb-1">Antigüedad conjunta</h3><p class="text-gray-600 text-sm">Si luego te separas, cada uno conserva su antigüedad. No empezás de cero.</p></div>
        </div>
    </div>
</section>

<!-- ====== SECCIÓN 5: Mejores isapres para familias ====== -->
<section id="isapres" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Mejores isapres para familias</h2>
    <div class="answer-direct">
        La mejor Isapre para tu familia depende de la etapa en la que estén: ¿están esperando un bebé? ¿tienen hijos chicos? ¿o ya son adolescentes? Cada Isapre tiene fortalezas en distintas etapas.
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-6">
        <div class="bg-gradient-to-b from-white to-yellow-50 rounded-xl p-6 border border-yellow-100 text-center">
            <div class="w-14 h-14 bg-yellow-500 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-lg font-bold">C</div>
            <h3 class="font-bold text-gray-900 mb-2">Colmena</h3>
            <p class="text-gray-600 text-sm">Excelente maternidad y pediatría. La mejor opción si están planificando o esperando un hijo. Cobertura top en parto y postnatal.</p>
        </div>
        <div class="bg-gradient-to-b from-white to-blue-50 rounded-xl p-6 border border-blue-100 text-center">
            <div class="w-14 h-14 bg-blue-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-lg font-bold">B</div>
            <h3 class="font-bold text-gray-900 mb-2">Banmédica</h3>
            <p class="text-gray-600 text-sm">La red más grande de clínicas. Ideal si querés elegir dónde atenderte sin restricciones. Clínica Alemana y Santa María en convenio.</p>
        </div>
        <div class="bg-gradient-to-b from-white to-indigo-50 rounded-xl p-6 border border-indigo-100 text-center">
            <div class="w-14 h-14 bg-indigo-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-lg font-bold">CB</div>
            <h3 class="font-bold text-gray-900 mb-2">Cruz Blanca</h3>
            <p class="text-gray-600 text-sm">Buen equilibrio precio-cobertura. Programas preventivos familiares y telemedicina que ahorran tiempo y traslados.</p>
        </div>
    </div>
</section>

<!-- ====== Tipos de planes familiares ====== -->
<section class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">El plan según tu momento familiar</h2>
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-pink-50 to-white rounded-xl p-6 border border-pink-100 text-center">
            <div class="text-4xl mb-3">🤰</div>
            <h3 class="font-bold text-gray-900 mb-2">Preferencia Natal</h3>
            <p class="text-gray-600 text-sm">Máxima cobertura para embarazo, parto y primer año del bebé. Salas cuna y controles priorizados.</p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100 text-center">
            <div class="text-4xl mb-3">👨‍👩‍👧‍👦</div>
            <h3 class="font-bold text-gray-900 mb-2">Con Cargas</h3>
            <p class="text-gray-600 text-sm">Para familias ya constituidas. Cobertura integral para todos los miembros con beneficios grupales.</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl p-6 border border-purple-100 text-center">
            <div class="text-4xl mb-3">👩‍👦</div>
            <h3 class="font-bold text-gray-900 mb-2">Monoparental</h3>
            <p class="text-gray-600 text-sm">Para un solo adulto con hijos. Precios accesibles sin sacrificar la protección de los niños.</p>
        </div>
    </div>
</section>

</div>

<!-- ====== FORMULARIO ====== -->
<div id="formulario" class="max-w-3xl mx-auto py-10">
    <?php render_component('formulario_familia'); ?>
</div>

<?php
$secciones_html = ob_get_clean();

// ── FAQ ──────────────────────────────────────────────────
$faq_preguntas = [
    '¿A quiénes puedo incluir como carga familiar?' => 'Cónyuge, hijos hasta 25 años si estudian, y en algunos casos padres adultos mayores que dependan económicamente de ti.',
    '¿Los planes familiares cubren embarazos?' => 'Sí, todos los planes cubren embarazo. Los planes con preferencia natal ofrecen coberturas adicionales y mejores condiciones.',
    '¿Puedo agregar a mi pareja si no estamos casados?' => 'Sí, puedes agregar a tu conviviente como carga acreditando la convivencia.',
    '¿Qué pasa si me separo?' => 'Cada adulto puede tomar un plan individual conservando su antigüedad. Los hijos quedan como cargas de uno de los padres.',
    '¿Mis hijos están cubiertos hasta qué edad?' => 'Hasta los 25 años si están estudiando, o de por vida si tienen una discapacidad.',
    '¿Puedo agregar una carga después de contratar el plan?' => 'Sí, puedes agregar cargas en cualquier momento presentando la documentación correspondiente.',
];
$faq_titulo = 'Preguntas Frecuentes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
