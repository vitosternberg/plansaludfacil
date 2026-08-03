<?php
/**
 * .env.example.php — Template de configuración
 * ============================================
 * Copia este archivo como .env.local.php y rellena los valores reales.
 * .env.local.php NUNCA se commitea (está en .gitignore).
 * 
 * Usado por: config.php, omniflow_config.php
 */

return [
    // ── Base de Datos ──
    'DB_HOST' => 'localhost',
    'DB_USER' => 'tu_usuario',
    'DB_PASS' => 'tu_contraseña_segura',
    'DB_NAME' => 'plansalu_blog',
    'DB_PORT' => 3306,

    // ── API ──
    'API_SECRET_KEY' => 'genera-una-clave-secreta-de-al-menos-128-caracteres',

    // ── SMTP ──
    'SMTP_HOST' => 'mail.plansaludfacil.cl',
    'SMTP_USER' => 'mailer@plansaludfacil.cl',
    'SMTP_PASS' => 'tu_contraseña_smtp',
    'SMTP_PORT' => 465,
    'SMTP_ENCRYPTION' => 'ssl',
    'SMTP_FROM_NAME' => 'Plan Salud Fácil',

    // ── Debug ──
    'DEBUG_MODE' => false,
    'SMTP_DEBUG_ENABLED' => false,
];
