<?php
/**
 * pages/nosotros.php — Sobre Plan Salud Fácil
 * Página institucional con ventajas competitivas, trayectoria y valores.
 */
require_once __DIR__ . '/../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $stmt = $db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)");
        if ($stmt) { $stmt->bind_param("sss", $ip, $ua, $url); $stmt->execute(); $stmt->close(); }
        $db->close();
    }
} catch (Exception $e) { error_log("Omniflow: " . $e->getMessage()); }

$page_title       = 'Sobre Plan Salud Fácil | Asesoría Real en Isapres desde 2009';
$meta_description = 'Plan Salud Fácil: más de 15 años asesorando a chilenos en la elección y cambio de su plan de Isapre. Asesoría humana real, sin robots ni plataformas automatizadas.';
$h1               = 'Sobre Plan Salud Fácil';
$lead             = 'No somos una plataforma de autoservicio. Somos un equipo real de asesores que te acompañan desde la primera cotización hasta la firma del contrato.';
$svc_name         = 'Plan Salud Fácil';
$svc_description  = 'Asesoría personalizada en planes de Isapre desde 2009.';
$cta_texto        = 'Cotizar ahora';
$cta_link         = '#formulario';
$breadcrumbs      = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Nosotros', 'url' => '#']];
foreach ($breadcrumbs as &$bc) $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']); unset($bc);
$toc_items = [['id' => 'que-nos-hace', 'label' => 'Qué nos hace diferentes'], ['id' => 'trayectoria', 'label' => 'Trayectoria'], ['id' => 'equipo', 'label' => 'Nuestro equipo']];
ob_start(); ?>
<style>.answer-direct{background:linear-gradient(135deg,#eff6ff,#f0fdf4);border-left:4px solid #2563eb;padding:16px 20px;border-radius:0 12px 12px 0;margin-bottom:16px;font-size:15px;color:#374151;line-height:1.7}</style>

<!-- ====== Qué nos hace diferentes ====== -->
<section id="que-nos-hace" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Qué nos hace diferentes</h2>
    <div class="answer-direct">Mientras las plataformas digitales solo te muestran números, nosotros te ofrecemos algo que un algoritmo no puede reemplazar: un asesor real que entiende tu situación y te guía paso a paso.</div>

    <div class="grid md:grid-cols-2 gap-6 mt-6">
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100">
            <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg">🤝</div>
            <h3 class="font-bold text-gray-900 mb-2">Asesoría humana real</h3>
            <p class="text-gray-600 text-sm">Un ejecutivo especializado revisa tu caso, te explica las opciones y te recomienda el mejor plan según tu situación familiar, médica y financiera. No hablás con un chatbot.</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-white rounded-xl p-6 border border-green-100">
            <div class="w-12 h-12 bg-green-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg">📋</div>
            <h3 class="font-bold text-gray-900 mb-2">Gestión completa del cambio</h3>
            <p class="text-gray-600 text-sm">No solo comparás: nosotros hacemos el trámite de cambio de Isapre por ti. Llenamos los formularios, gestionamos la desafiliación y te acompañamos hasta que tu nuevo plan esté activo.</p>
        </div>
        <div class="bg-gradient-to-br from-cyan-50 to-white rounded-xl p-6 border border-cyan-100">
            <div class="w-12 h-12 bg-cyan-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg">📧</div>
            <h3 class="font-bold text-gray-900 mb-2">Cotizar con Experto</h3>
            <p class="text-gray-600 text-sm">Ingresás tus datos, un asesor los revisa, y te envía por correo los mejores planes con cobertura detallada. Sin presiones. Sin compromiso. Solo información clara y útil.</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-50 to-white rounded-xl p-6 border border-emerald-100">
            <div class="w-12 h-12 bg-emerald-600 text-white rounded-xl flex items-center justify-center mb-3 text-lg">💬</div>
            <h3 class="font-bold text-gray-900 mb-2">Contacto directo por WhatsApp</h3>
            <p class="text-gray-600 text-sm">¿Tenés una duda rápida? Escribinos por WhatsApp y un asesor te responde. Sin formularios interminables ni menús automáticos. Conversación directa con una persona real.</p>
        </div>
    </div>
</section>

<!-- ====== Trayectoria ====== -->
<section id="trayectoria" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Más de 15 años de trayectoria</h2>
    <div class="answer-direct">Desde 2009 asesorando a miles de chilenos en la elección y cambio de su plan de Isapre. Conocemos el sistema, sus reglas y sus excepciones como nadie.</div>

    <div class="grid grid-cols-3 gap-4 mt-6 text-center">
        <div class="bg-white rounded-xl p-6 border">
            <div class="text-3xl font-extrabold text-blue-700">2.000+</div>
            <div class="text-sm text-gray-500 mt-2">evaluaciones cerradas</div>
        </div>
        <div class="bg-white rounded-xl p-6 border">
            <div class="text-3xl font-extrabold text-blue-700">15+</div>
            <div class="text-sm text-gray-500 mt-2">años de experiencia</div>
        </div>
        <div class="bg-white rounded-xl p-6 border">
            <div class="text-3xl font-extrabold text-blue-700">7</div>
            <div class="text-sm text-gray-500 mt-2">isapres analizadas</div>
        </div>
    </div>

    <p class="text-gray-600 mt-6">Hemos visto cambios de ley, crisis sanitarias, alzas de planes y miles de casos particulares. Esa experiencia no está en ningún algoritmo: está en nuestro equipo. Cada cotización se beneficia de 15 años de conocimiento acumulado sobre cómo funciona realmente el sistema de Isapres en Chile.</p>
</section>

<!-- ====== Nuestro equipo ====== -->
<section id="equipo" class="mb-8 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Lo que nos mueve</h2>
    <div class="answer-direct">Creemos que elegir un plan de salud no debería ser un salto al vacío. Debería ser una decisión informada, acompañada y sin presiones.</div>

    <div class="grid md:grid-cols-3 gap-4 mt-6 text-center">
        <div class="p-6">
            <div class="text-3xl mb-3">🔍</div>
            <h3 class="font-bold text-gray-900 mb-2">Transparencia</h3>
            <p class="text-gray-600 text-sm">Te mostramos todas las opciones, no solo las que nos convienen. Datos reales de la Superintendencia de Salud.</p>
        </div>
        <div class="p-6">
            <div class="text-3xl mb-3">🛡️</div>
            <h3 class="font-bold text-gray-900 mb-2">Confianza</h3>
            <p class="text-gray-600 text-sm">Sin compromiso. Sin letra chica. Te damos la información para que decidas con tranquilidad.</p>
        </div>
        <div class="p-6">
            <div class="text-3xl mb-3">⚡</div>
            <h3 class="font-bold text-gray-900 mb-2">Cercanía</h3>
            <p class="text-gray-600 text-sm">Hablamos tu idioma. Sin tecnicismos innecesarios. Te explicamos todo como si fueras de la familia.</p>
        </div>
    </div>
</section>

<!-- ====== Formulario ====== -->
<div id="formulario" class="max-w-4xl mx-auto py-10">
    <?php render_component('formulario_individual'); ?>
</div>

<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿Plan Salud Fácil es una isapre?' => 'No. Somos un servicio de asesoría independiente. Te ayudamos a comparar y contratar planes de cualquier isapre sin costo para ti.',
    '¿Por qué es gratis?' => 'Las isapres nos pagan una comisión por cada contrato gestionado, similar a un corredor de seguros. Vos no pagás nada adicional.',
    '¿Trabajan con todas las isapres?' => 'Sí. Analizamos planes de Banmédica, Colmena, Consalud, Cruz Blanca, Esencial, Nueva Masvida y Vida Tres.',
    '¿Cuánto demora el cambio de isapre?' => 'Entre 3 y 10 días hábiles, dependiendo de la isapre y de tu situación particular. Nosotros gestionamos todo el trámite.',
];
$faq_titulo = 'Preguntas Frecuentes';

include __DIR__ . '/../layout/seo-page.php';
