<?php
/**
 * config.php
 * Archivo de configuración para la conexión a la base de datos MySQL.
 * Ubicación: tu_proyecto_raiz/config.php
 *
 * [VERSION CONTROL] - Nueva Versión: 2025-07-06
 * - Creado en la raíz del proyecto para centralizar las credenciales de DB
 * para `contact.php` y `procesar_contacto.php`.
 * - Contiene las credenciales y la función `connect_db_simple()`.
 */


define('DB_HOST', 'localhost');
define('DB_USER', 'plansalu_blogger'); 
define('DB_PASS', 'Blog.2025!#');    
define('DB_NAME', 'plansalu_blog');
define('API_SECRET_KEY', 'A3kOMUb0MrnX7z8Dh24yoevffKcgFMcOIFedZsn3w5IyxdfXvuSlDfkol4eHOpOoQFoB70ODbaNwyhyLWqVVIaesEXqdxfd3PfBx6GGyRiOsYBWMVjNsufbkDEWZjsiI');

// --- NUEVA CONFIGURACIÓN DE CORREO (SMTP) ---
define('SMTP_DEBUG_ENABLED', true);           // Poner en 'false' para producción, 'true' para depurar.
define('SMTP_HOST', 'mail.plansaludfacil.cl');       // El servidor SMTP de tu proveedor de correo
define('SMTP_USER', 'mailer@plansaludfacil.cl'); // Tu dirección de correo
define('SMTP_PASS', 'Mailer.2025'); // La contraseña de tu correo
define('SMTP_PORT', 465);                     // Puerto SMTP (587 para TLS, 465 para SSL)




/**
 * Establece una conexión a la base de datos MySQL.
 * @return mysqli|null Objeto de conexión mysqli en caso de éxito, o null en caso de error.
 */
function connect_db_simple() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            error_log("Error de conexión a la base de datos: " . $conn->connect_error);
            return null;
        }
        $conn->set_charset("utf8mb4"); // Asegura el soporte de caracteres especiales
        return $conn;
    } catch (Exception $e) {
        error_log("Excepción en connect_db_simple(): " . $e->getMessage());
        return null;
    }
}

?>
