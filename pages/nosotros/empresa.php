<?php
/**
 * nosotros/empresa.php
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
$page_title       = 'Nuestra Empresa - Plan Salud Fácil | Comparador de Isapres';
$meta_description = 'Conoce a Plan Salud Fácil, tu comparador de Isapres 100% gratuito. Asesoría imparcial para encontrar el mejor plan de salud según tus necesidades y presupuesto.';
$h1               = 'Sobre PlanSaludFácil';
$lead             = 'Conoce cómo estamos transformando la forma en que los chilenos eligen y gestionan su previsión de salud. Asesoría gratuita, imparcial y 100% digital.';
$svc_name         = 'Plan Salud Fácil';
$svc_description  = 'Comparador de Isapres 100% gratuito. Asesoría imparcial, acompañamiento continuo y búsqueda del mejor plan de salud para cada chileno.';
$cta_texto        = 'Hablar con un Asesor';
$cta_link         = 'https://wa.me/56952282339';

// ── Breadcrumbs ──────────────────────────────────────────
$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Nuestra Empresa', 'url' => '#']];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

// ── ToC ──────────────────────────────────────────────────
$toc_items = [
    ['id' => 'acompanamiento', 'label' => 'Acompañamiento continuo'],
    ['id' => 'imparcialidad', 'label' => 'Búsqueda imparcial'],
    ['id' => 'expertos', 'label' => 'Equipo experto'],
    ['id' => 'rapidez', 'label' => 'Proceso 100% digital'],
    ['id' => 'ahorro', 'label' => 'Ahorro garantizado'],
];

// ── Secciones de contenido ───────────────────────────────
ob_start();
?>

<section id="acompanamiento" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28" aria-labelledby="s1-heading">
    <h2 id="s1-heading" class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">¿Por qué elegir a PlanSaludFácil?</h2>
    <p class="text-gray-700 leading-relaxed mb-8 text-lg">Descubre cómo podemos transformar tu experiencia en el sistema de salud chileno. Nuestro equipo de expertos te brinda un servicio personalizado, diseñado para garantizar la optimización de tu presupuesto y darte tranquilidad en cada paso.</p>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex gap-6 items-start">
        <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">🤝</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Acompañamiento continuo: Antes, durante y después de contratar</h3>
            <p class="text-gray-600">No somos simplemente un comparador online; somos tus asesores de confianza a largo plazo. Te guiamos de manera personalizada desde la evaluación inicial y la firma de tu contrato, hasta la resolución de dudas futuras. Nuestro compromiso no termina cuando firmas.</p>
        </div>
    </div>
</section>

<section id="imparcialidad" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex gap-6 items-start">
        <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">⚖️</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Búsqueda imparcial del plan de salud más conveniente</h3>
            <p class="text-gray-600">Analizamos todas las opciones del mercado sin favoritismos comerciales. Nuestro único objetivo es cruzar tus necesidades reales de salud con el plan que mejor las cubra al menor costo posible. No recibimos comisiones por recomendarte una Isapre específica.</p>
        </div>
    </div>
</section>

<section id="expertos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex gap-6 items-start">
        <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">🎓</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Equipo experto en el sistema de salud chileno</h3>
            <p class="text-gray-600">Contamos con asesores especializados que conocen a fondo cada Isapre, sus planes, convenios y letra chica. Te traducimos la complejidad del sistema a recomendaciones claras y accionables para que tomes la mejor decisión.</p>
        </div>
    </div>
</section>

<section id="rapidez" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex gap-6 items-start">
        <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">⚡</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Proceso 100% digital, rápido y sin trámites</h3>
            <p class="text-gray-600">Todo el proceso — desde la cotización hasta la firma del contrato — se realiza online. Sin filas, sin papeles, sin perder tiempo. En menos de 48 horas puedes estar disfrutando de tu nuevo plan de salud.</p>
        </div>
    </div>
</section>

<section id="ahorro" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex gap-6 items-start">
        <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">💰</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Ahorro garantizado en tu plan de salud</h3>
            <p class="text-gray-600">Optimizamos cada peso de tu 7% de cotización. Al comparar objetivamente, muchos de nuestros clientes descubren que pueden acceder a mejores coberturas pagando exactamente lo mismo — o incluso menos — de lo que pagan actualmente.</p>
        </div>
    </div>
</section>

<?php
$secciones_html = ob_get_clean();

// ── Mini-FAQ ─────────────────────────────────────────────
$faq_preguntas = [
    '¿PlanSaludFácil es gratuito?' => 'Sí, nuestro servicio de comparación y asesoría es 100% gratuito para el usuario. Las Isapres cubren nuestros honorarios de gestión, por lo que tú no pagas nada adicional.',
    '¿Son imparciales en sus recomendaciones?' => 'Totalmente. No favorecemos a ninguna Isapre en particular. Comparamos objetivamente todas las opciones del mercado y te mostramos las que mejor se ajustan a tu perfil y necesidades.',
    '¿Cuánto demora el proceso de cambio de Isapre?' => 'Menos de 48 horas en la mayoría de los casos. Todo el proceso es digital: comparación, Declaración de Salud y firma del contrato.',
    '¿Qué pasa después de contratar? ¿Me dejan solo?' => 'No. Nuestro acompañamiento es permanente. Si tienes dudas sobre coberturas, copagos o necesitas ayuda con tu Isapre, seguimos a tu lado.',
];
$faq_titulo = 'Preguntas Frecuentes sobre PlanSaludFácil';

// ── Renderizar template ─────────────────────────────────
include __DIR__ . '/../../layout/seo-page.php';
