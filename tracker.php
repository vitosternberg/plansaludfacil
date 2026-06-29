<?php
/**
 * =======================================================================
 * OMNIFLOW - SCRIPT DE SEGUIMIENTO DE VISITAS
 * =======================================================================
 * Este script registra la visita de un lead a una URL específica y luego
 * lo redirige a esa URL.
 */

// Definimos una constante para que omniflow_connector.php sepa que está siendo incluido.
define('OMNIFLOW_CONNECTOR_INCLUDED', true);

// 1. Obtener y validar los parámetros de la URL.
$lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
$redirect_url = filter_input(INPUT_GET, 'redirect_to', FILTER_SANITIZE_URL);

// Si falta algún dato, redirigir a la página principal como medida de seguridad.
if (!$lead_id || !$redirect_url) {
    header('Location: /');
    exit;
}

try {
    // 2. Incluir los archivos necesarios del conector para usar sus funciones.
    // No necesitamos la librería de correo aquí, solo la configuración y las funciones del conector.
    require_once __DIR__ . '/omniflow_config.php';
    require_once __DIR__ . '/omniflow_connector.php';

    // 3. Obtener una conexión a la base de datos.
    $db = get_client_db_connection();

    // 4. Llamar directamente a la función que registra la visita.
    // Creamos un array de datos similar al que recibiría el conector por POST.
    $datos_visita = ['lead_id' => $lead_id, 'url_visitada' => $redirect_url];
    handle_registrar_visita($db, $datos_visita);

    $db->close();

} catch (Exception $e) {
    // Si algo falla (ej: la conexión a la BD), lo registramos en el log de errores del conector.
    omniflow_error_logger('ERROR_TRACKER', $e->getMessage(), __FILE__, __LINE__);
}

// 5. Redirigir siempre al usuario a la URL de destino, incluso si el registro falla.
// La experiencia del usuario es la prioridad.
header('Location: ' . $redirect_url);
exit;