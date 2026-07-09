<?php
/**
 * pages/planes/individuales/jovenes.php
 * Plan Piloto — Planes de ISAPRE para Jóvenes Profesionales.
 *
 * Estructura: Tracking → Variables SEO → Template → Componentes.
 */

// ── Tracking Omniflow ────────────────────────────────────
require_once __DIR__ . '/../../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
             . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
$page_title       = 'Planes de ISAPRE para Jóvenes Profesionales | Plan Salud Fácil';
$meta_description = 'Planes de ISAPRE para jóvenes profesionales. Cobertura ambulatoria, telemedicina, salud mental y precio accesible. Cotiza gratis y online.';
$h1               = 'Planes de ISAPRE para Jóvenes Profesionales';
$lead             = 'Los mejores planes de salud para jóvenes entre 18 y 35 años, con cobertura ambulatoria completa, telemedicina ilimitada y precios desde tu 7% obligatorio.';
$svc_name         = 'Plan de ISAPRE para Jóvenes Profesionales';
$svc_description  = 'Planes de salud privados para jóvenes profesionales en Chile. Cobertura ambulatoria, salud mental, telemedicina y medicina deportiva. Precios accesibles.';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [
    ['label' => 'Inicio',   'url' => BASE_URL . '/'],
    ['label' => 'Planes',   'url' => BASE_URL . '/planes/'],
    ['label' => 'Individuales', 'url' => BASE_URL . '/planes/individuales/'],
    ['label' => 'Jóvenes',  'url' => '#'],
];

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'que-es',     'label' => '¿Qué es un plan joven?'],
    ['id' => 'beneficios', 'label' => 'Beneficios clave'],
    ['id' => 'comparativa','label' => 'Comparativa de ISAPREs'],
    ['id' => 'requisitos', 'label' => 'Requisitos y cómo contratar'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>
<section id="que-es" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
        ¿Qué es un Plan de ISAPRE para Jóvenes Profesionales?
    </h2>
    <p class="text-gray-700 leading-relaxed mb-4">
        Un plan de ISAPRE para jóvenes profesionales es un contrato de salud privado diseñado
        para personas entre 18 y 35 años, sin cargas familiares, que buscan cobertura médica
        ambulatoria de calidad a un precio accesible.
    </p>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">¿A quién va dirigido?</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-1 mb-4">
        <li>Profesionales independientes que emiten boletas de honorarios</li>
        <li>Jóvenes que se independizan del plan familiar de sus padres</li>
        <li>Trabajadores dependientes que quieren optimizar su 7%</li>
        <li>Deportistas y personas con vida saludable activa</li>
    </ul>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">¿Qué incluye típicamente?</h3>
    <ul class="list-disc pl-6 text-gray-700 space-y-1">
        <li>Consultas médicas ambulatorias con amplia cobertura</li>
        <li>Telemedicina ilimitada (consultas virtuales 24/7)</li>
        <li>Exámenes de laboratorio e imagenología</li>
        <li>Cobertura de salud mental (psicólogo y psiquiatra)</li>
        <li>Medicina deportiva y preventiva</li>
        <li>Urgencias en clínicas de la red</li>
    </ul>
</section>

<section id="beneficios" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s2-heading">
    <h2 id="s2-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
        Beneficios clave de un Plan Joven
    </h2>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                <span class="text-xl">💻</span> Telemedicina ilimitada
            </h3>
            <p class="text-gray-600 text-sm">Consultas médicas virtuales 24/7 sin costo adicional. Ideal para profesionales con poco tiempo.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                <span class="text-xl">🧠</span> Salud mental cubierta
            </h3>
            <p class="text-gray-600 text-sm">Sesiones con psicólogo y psiquiatra con cobertura preferencial en la mayoría de los planes.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                <span class="text-xl">🏃</span> Medicina deportiva
            </h3>
            <p class="text-gray-600 text-sm">Evaluaciones, kinesiología y tratamientos deportivos cubiertos. Ideal para mantener un estilo de vida activo.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                <span class="text-xl">💰</span> Sin costo extra al 7%
            </h3>
            <p class="text-gray-600 text-sm">Planes diseñados para que tu cotización obligatoria cubra el 100% del plan, sin pagos adicionales.</p>
        </div>
    </div>
</section>

<section id="comparativa" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s3-heading">
    <h2 id="s3-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
        Comparativa de ISAPREs para Jóvenes
    </h2>
    <p class="text-gray-700 mb-6">Precios referenciales para un joven de 25 años, renta líquida $800.000, sin cargas.</p>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded-xl border border-gray-100 shadow-sm text-sm">
            <thead>
                <tr class="bg-blue-50 text-left">
                    <th class="p-4 font-semibold text-gray-800">ISAPRE</th>
                    <th class="p-4 font-semibold text-gray-800">Plan sugerido</th>
                    <th class="p-4 font-semibold text-gray-800">Precio mensual</th>
                    <th class="p-4 font-semibold text-gray-800">Cobertura ambulatoria</th>
                    <th class="p-4 font-semibold text-gray-800">Telemedicina</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="p-4 font-medium">Banmédica</td>
                    <td class="p-4">Flex Joven</td>
                    <td class="p-4">$56.000</td>
                    <td class="p-4">80%</td>
                    <td class="p-4">✅</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Cruz Blanca</td>
                    <td class="p-4">Vida Activa</td>
                    <td class="p-4">$52.000</td>
                    <td class="p-4">75%</td>
                    <td class="p-4">✅</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Colmena</td>
                    <td class="p-4">Joven Shield</td>
                    <td class="p-4">$48.000</td>
                    <td class="p-4">70%</td>
                    <td class="p-4">✅</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Vida Tres</td>
                    <td class="p-4">Active Life</td>
                    <td class="p-4">$54.000</td>
                    <td class="p-4">80%</td>
                    <td class="p-4">✅</td>
                </tr>
                <tr>
                    <td class="p-4 font-medium">Nueva Masvida</td>
                    <td class="p-4">Full Salud Joven</td>
                    <td class="p-4">$45.000</td>
                    <td class="p-4">65%</td>
                    <td class="p-4">✅</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="text-xs text-gray-400 mt-2">* Precios referenciales. El costo final depende de tu declaración de salud y la ISAPRE.</p>
</section>

<section id="requisitos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s4-heading">
    <h2 id="s4-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
        Requisitos y cómo contratar
    </h2>
    <ol class="list-decimal pl-6 text-gray-700 space-y-3 mb-6">
        <li><strong>Edad:</strong> entre 18 y 35 años (varía según ISAPRE).</li>
        <li><strong>Renta:</strong> contar con ingresos que permitan cotizar el 7% obligatorio.</li>
        <li><strong>Documentos:</strong> cédula de identidad vigente y, en algunos casos, declaración de salud.</li>
        <li><strong>Sin cargas:</strong> el plan joven típicamente no incluye beneficiarios (para eso existen planes familiares).</li>
        <li><strong>Contratación online:</strong> la mayoría de las ISAPREs permiten completar todo el proceso en línea.</li>
    </ol>
    <p class="text-gray-700">¿Tienes dudas con los requisitos? <a href="https://wa.me/56952282339" class="text-blue-600 hover:underline font-medium">Habla con un asesor</a> y te guiamos paso a paso.</p>
</section>
<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = [
    '¿Cuál es la edad máxima para un plan joven?' => 'Generalmente 35 años, aunque algunas ISAPREs extienden el beneficio hasta los 40.',
    '¿Puedo agregar cargas a mi plan joven?' => 'Los planes jóvenes están diseñados sin cargas. Si necesitas incluir beneficiarios, conviene evaluar un plan familiar.',
    '¿Qué pasa si me cambio de trabajo?' => 'El plan es independiente de tu empleador. Mientras mantengas tu cotización, la cobertura continúa sin cambios.',
    '¿Cubre enfermedades preexistentes?' => 'Depende de la ISAPRE y de tu declaración de salud. Nuestros asesores te ayudan a encontrar la mejor opción según tu caso.',
    '¿Puedo contratar 100% online?' => 'Sí, la mayoría de las ISAPREs permiten completar todo el proceso de cotización y contratación en línea.',
];

$faq_titulo = 'Preguntas Frecuentes sobre Planes Jóvenes';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../../layout/seo-page.php';
