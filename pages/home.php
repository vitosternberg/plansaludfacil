<?php
/**
 * =======================================================================
 * OMNIFLOW - SCRIPT DE SEGUIMIENTO DE VISITAS HÍBRIDO
 * =======================================================================
 */
require_once __DIR__ . '/../omniflow_config.php';
require_once __DIR__ . '/../core/helpers.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $visited_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $stmt_general = $db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)");
        if ($stmt_general) {
            $stmt_general->bind_param("sss", $ip_address, $user_agent, $visited_url);
            $stmt_general->execute(); 
            $stmt_general->close();
        }

        $lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
        if ($lead_id) {
            $stmt_lead = $db->prepare("INSERT INTO lead_visits (lead_id, url_visitada) VALUES (?, ?)");
            if ($stmt_lead) {
                $stmt_lead->bind_param("is", $lead_id, $visited_url);
                $stmt_lead->execute(); 
                $stmt_lead->close();
            }
        }
        $db->close();
    }
} catch (Exception $e) {
    error_log("Omniflow Tracking Error: " . $e->getMessage());
}

$page_title = "Plan Salud Fácil - Tu Comparador de Isapres";
include './layout/plantilla.php'; 
include './layout/header.php';
render_component('hero_moderno', [
    'titulo' => 'Elige tu Plan de Isapre en Minutos',
    'titulo_movil' => 'Cotiza Isapre en minutos',
    'subtitulo' => 'Buscamos las mejores opciones en Isapre. 100% gratuito y sin letra chica.',
    'subtitulo_movil' => '100% gratuito y sin letra chica.',
    'cta_texto' => 'Comenzar mi Cotización',
    'cta_link' => BASE_URL . '/servicios/cambio-de-isapre#formulario-contacto'
]);

// 2. SOCIAL PROOF / MARCAS
render_component('carrusel_marcas', [
    'titulo' => 'Trabajamos con todas las Isapres'
]);

// 3. BENEFICIOS
render_component('grilla_beneficios', [
    'items' => [
        ['icono' => '🚀', 'titulo' => '100% Rápido y Online', 'texto' => 'Atención en línea desde tu celular sin trámites complejos.'],
        ['icono' => '🤝', 'titulo' => 'Asesoría Imparcial', 'texto' => 'Buscamos lo mejor para ti y tu familia, no para la Isapre.'],
        ['icono' => '💰', 'titulo' => 'Servicio Gratuito', 'texto' => 'Nuestra asesoría experta no tiene ningún costo extra para ti.']
    ]
]);

// 4. SILOS TRANSACCIONALES
render_component('tarjetas_servicios', [
    'titulo_seccion' => '¿En qué etapa te encuentras?',
    'servicios' => [
        [
            'titulo' => 'Me quiero cambiar de Isapre', 
            'descripcion' => 'Optimiza tu plan actual y mejora tus coberturas.',
            'link' => BASE_URL . '/servicios/cambio-de-isapre',
            'icono' => '🔄',
            'imagen' => BASE_URL . '/img/card_cambio.jpg'
        ],
        [
            'titulo' => 'Busco un Plan Familiar', 
            'descripcion' => 'Protege a los que más quieres con cobertura médica ampliada.',
            'link' => BASE_URL . '/servicios/planes-familia',
            'icono' => '👨‍👩‍👧‍👦',
            'imagen' => BASE_URL . '/img/card_familia.jpg'
        ],
        [
            'titulo' => 'Primer Plan Individual', 
            'descripcion' => 'Pasa de Fonasa a Isapre con el plan que mejor se adapte a tu bolsillo.',
            'link' => BASE_URL . '/servicios/planes-individuales',
            'icono' => '👤',
            'imagen' => BASE_URL . '/img/card_individual.jpg'
        ],
        [
            'titulo' => 'Plan Monoparental', 
            'descripcion' => 'Planes diseñados para proteger a tus hijos sin desestabilizar hogares de un solo ingreso.',
            'link' => BASE_URL . '/servicios/planes-monoparental',
            'icono' => '🦸‍♀️',
            'imagen' => BASE_URL . '/img/card_monoparental.jpg'
        ]
    ]
]);

// 5. PREGUNTAS FRECUENTES
render_component('faq_acordeon', [
    'titulo' => 'Dudas Frecuentes',
    'preguntas' => [
        '¿Qué es una ISAPRE?' => 'Una ISAPRE es una institución privada que administra tu cotización de salud y ofrece planes con distintas coberturas y beneficios.',
        '¿Cómo comparar planes de ISAPRE?' => 'Compara precio, cobertura, red de clínicas, topes y beneficios adicionales para encontrar el que mejor se adapte a ti.',
        '¿Cómo puedo cambiarme de ISAPRE?' => 'Solo debes contratar un nuevo plan y la nueva ISAPRE generalmente gestiona el cambio por ti.',
        '¿Cuánto cuesta un plan de ISAPRE?' => 'Depende del plan, tu cotización, edad, cargas familiares y cobertura elegida.',
        '¿Qué cubre un plan de ISAPRE?' => 'Dependiendo del plan, cubre consultas, exámenes, hospitalizaciones, cirugías y otros beneficios.',
        '¿Puedo incorporar a mis hijos?' => 'Sí, puedes agregarlos como beneficiarios del plan.',
        '¿Qué son las preexistencias?' => 'Son enfermedades o condiciones de salud que existían antes de contratar el plan.',
        '¿Qué beneficios adicionales ofrecen las ISAPRE?' => 'Algunas incluyen telemedicina, descuentos en farmacias, seguros y programas preventivos.'
    ]
]);

echo '<div class="max-w-4xl mx-auto px-4 pb-6 text-center">
    <a href="' . BASE_URL . '/preguntas-frecuentes" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver todas las preguntas frecuentes
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</div>';

// 6. ENLAZADO AL BLOG
render_component('ultimos_articulos_blog', [
    'titulo' => 'Guías y Consejos de Salud',
    'limite' => 3
]);

// 7. CTA FINAL
render_component('cta_footer', [
    'titulo' => '¿Listo para mejorar tu cobertura médica?',
    'cta_texto' => 'Hablar con un Asesor por WhatsApp',
    'cta_link' => 'https://wa.me/56952282339'
]);

// Chatbot desactivado — toggle en Omnilama > Base de Conocimiento
// render_component('chat_psl_widget');

include './layout/footer.php';
