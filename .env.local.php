<?php
/**
 * .env.local.php — Secretos de producción
 * ==========================================
 * ¡NUNCA COMMITEAR ESTE ARCHIVO! (está en .gitignore)
 * 
 * Si estás en local (HTTP_HOST = localhost/127.0.0.1), 
 * config.php usará credenciales de desarrollo automáticamente
 * y este archivo se ignora.
 */

return [
    'DB_HOST' => 'localhost',
    'DB_USER' => 'plansalu_blogger',
    'DB_PASS' => 'Blog.2025!#',
    'DB_NAME' => 'plansalu_blog',
    'DB_PORT' => 3306,

    'API_SECRET_KEY' => 'A3kOMUb0MrnX7z8Dh24yoevffKcgFMcOIFedZsn3w5IyxdfXvuSlDfkol4eHOpOoQFoB70ODbaNwyhyLWqVVIaesEXqdxfd3PfBx6GGyRiOsYBWMVjNsufbkDEWZjsiI',

    'SMTP_HOST' => 'mail.plansaludfacil.cl',
    'SMTP_USER' => 'mailer@plansaludfacil.cl',
    'SMTP_PASS' => 'Mailer.2025',
    'SMTP_PORT' => 465,
    'SMTP_ENCRYPTION' => 'ssl',
    'SMTP_FROM_NAME' => 'Plan Salud Fácil',

    'DEBUG_MODE' => false,
    'SMTP_DEBUG_ENABLED' => false,
];
