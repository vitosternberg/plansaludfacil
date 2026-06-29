<?php
/**
 * =======================================================================
 * ARCHIVO DE CONFIGURACIÓN DE OMNIFLOW CONNECTOR
 * =======================================================================
 * Edita los valores en este archivo para configurar la conexión a tu
 * base de datos, las credenciales de correo y la clave de API.
 */

// --- MODO DE DEPURACIÓN ---
// Poner en `true` para ver errores detallados en la respuesta JSON.
// ¡IMPORTANTE! Poner en `false` en un entorno de producción.
define('DEBUG_MODE', true);

// --- CONFIGURACIÓN DE LA BASE DE DATOS DEL CLIENTE ---

// --- MODO DE DEPURACIÓN ---
if (!defined('DEBUG_MODE')) {
	define('DEBUG_MODE', true);
}

// --- CONFIGURACIÓN DE LA BASE DE DATOS DEL CLIENTE ---
if (!defined('DB_HOST')) {
	define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
	define('DB_USER', 'plansalu_blogger');
}
if (!defined('DB_PASS')) {
	define('DB_PASS', 'Blog.2025!#');
}
if (!defined('DB_NAME')) {
	define('DB_NAME', 'plansalu_blog');
}

// --- CLAVE SECRETA DE LA API ---
if (!defined('API_SECRET_KEY')) {
	define('API_SECRET_KEY', 'A3kOMUb0MrnX7z8Dh24yoevffKcgFMcOIFedZsn3w5IyxdfXvuSlDfkol4eHOpOoQFoB70ODbaNwyhyLWqVVIaesEXqdxfd3PfBx6GGyRiOsYBWMVjNsufbkDEWZjsiI');
}

// --- CONFIGURACIÓN DE CORREO (SMTP) ---
if (!defined('SMTP_DEBUG_ENABLED')) {
	define('SMTP_DEBUG_ENABLED', false);
}
if (!defined('SMTP_HOST')) {
	define('SMTP_HOST', 'mail.plansaludfacil.cl');
}
if (!defined('SMTP_USER')) {
	define('SMTP_USER', 'mailer@plansaludfacil.cl');
}
if (!defined('SMTP_PASS')) {
	define('SMTP_PASS', 'Mailer.2025');
}
if (!defined('SMTP_PORT')) {
	define('SMTP_PORT', 465);
}
if (!defined('SMTP_ENCRYPTION')) {
	define('SMTP_ENCRYPTION', 'ssl');
}

// --- NOMBRE DEL REMITENTE ---
if (!defined('SMTP_FROM_NAME')) {
	define('SMTP_FROM_NAME', 'Plan salud facil');
}

// --- RUTA DEL ARCHIVO DE BASE DE CONOCIMIENTO ---
if (!defined('KNOWLEDGE_BASE_FILE')) {
	define('KNOWLEDGE_BASE_FILE', __DIR__ . '/knowledge_base_cliente.txt');
}

?>