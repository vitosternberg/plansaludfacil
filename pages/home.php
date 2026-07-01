<?php
/**
 * =======================================================================
 * OMNIFLOW - SCRIPT DE SEGUIMIENTO DE VISITAS HÍBRIDO
 * =======================================================================
 */
require_once __DIR__ . '/../omniflow_config.php';
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
    'subtitulo' => '100% gratuito y sin letra chica.',
    'cta_texto' => 'Comenzar mi Cotización',
    'cta_link' => BASE_URL . '/servicios/cambio-de-isapre#formulario-contacto'
]);

// 2. SOCIAL PROOF / MARCAS
render_component('carrusel_marcas', [
    'titulo' => 'Trabajamos con las mejores Isapres de Chile'
]);

// 3. BENEFICIOS
render_component('grilla_beneficios', [
    'items' => [
        ['icono' => '🚀', 'titulo' => '100% Rápido y Online', 'texto' => 'Sin papeleos ni trámites engorrosos. Todo desde tu celular.'],
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
            'icono' => '🔄'
        ],
        [
            'titulo' => 'Busco un Plan Familiar', 
            'descripcion' => 'Protege a los que más quieres con cobertura médica ampliada.',
            'link' => BASE_URL . '/servicios/planes-familia',
            'icono' => '👨‍👩‍👧‍👦'
        ],
        [
            'titulo' => 'Primer Plan Individual', 
            'descripcion' => 'Pasa de Fonasa a Isapre con el plan que mejor se adapte a tu bolsillo.',
            'link' => BASE_URL . '/servicios/planes-individuales',
            'icono' => '👤'
        ],
        [
            'titulo' => 'Plan Monoparental', 
            'descripcion' => 'Planes diseñados para proteger a tus hijos sin desestabilizar hogares de un solo ingreso.',
            'link' => BASE_URL . '/servicios/planes-monoparental',
            'icono' => '🦸‍♀️'
        ]
    ]
]);

// 5. PREGUNTAS FRECUENTES
render_component('faq_acordeon', [
    'titulo' => 'Dudas Frecuentes',
    'preguntas' => [
        '¿Tiene algún costo usar PlanSaludFacil?' => 'No, nuestro servicio es 100% gratuito para el usuario final. Las Isapres cubren nuestros honorarios de gestión.',
        '¿Puedo cotizar si actualmente estoy en Fonasa?' => '¡Por supuesto! Te ayudamos a evaluar si te conviene dar el salto al sistema privado según tu renta y necesidades de salud.',
        '¿Qué pasa si tengo preexistencias médicas?' => 'Nuestros asesores expertos buscarán las mejores alternativas legales y planes que puedan aceptar tu declaración de salud.'
    ]
]);

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

include './layout/footer.php';