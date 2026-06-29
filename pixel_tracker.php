<?php
/**
 * =======================================================================
 * OMNIFLOW - PÍXEL DE SEGUIMIENTO DE APERTURA DE CORREOS
 * =======================================================================
 * Este script es llamado cuando se carga la imagen de seguimiento en un correo.
 * Registra el evento de apertura y devuelve una imagen GIF transparente de 1x1.
 */

// 1. Definir una constante para que el conector sepa que está siendo incluido.
define('OMNIFLOW_CONNECTOR_INCLUDED', true);

// 2. Cargar la configuración y las funciones del conector.
require_once __DIR__ . '/omniflow_config.php';
require_once __DIR__ . '/omniflow_connector.php';

// 3. Obtener los datos de la URL.
$lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);

if ($lead_id) {
    try {
        // 4. Conectarse a la base de datos y registrar la apertura.
        $db = get_client_db_connection();
        
        $stmt = $db->prepare(
            "INSERT INTO email_opens (lead_id, ip_address, user_agent) VALUES (?, ?, ?)"
        );
        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
        
        $stmt->bind_param("iss", $lead_id, $ip_address, $user_agent);
        $stmt->execute();
        $db->close();

    } catch (Exception $e) {
        // Si algo falla, lo registramos en el log de errores, pero no detenemos el script.
        omniflow_error_logger('ERROR_PIXEL_TRACKER', $e->getMessage(), __FILE__, __LINE__);
    }
}

// 5. Servir la imagen GIF transparente de 1x1.
// Esto es crucial para que el cliente de correo no muestre un error de imagen rota.
header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Contenido binario de un GIF transparente de 1x1 píxel.
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICRAEAOw==');

exit;
?>