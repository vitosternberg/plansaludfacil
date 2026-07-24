<?php
/**
 * run_cron.php — Ejecuta cron_sender.php desde el navegador sin timeout.
 * Uso: https://plansaludfacil.cl/run_cron.php?key=TU_KEY
 */
require_once __DIR__ . '/config.php';
$key = $_GET['key'] ?? '';
if ($key !== API_SECRET_KEY) { http_response_code(401); die('Acceso denegado'); }

// Desactivar límite de tiempo
set_time_limit(0);
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
header('Content-Type: text/plain; charset=utf-8');
header('X-Accel-Buffering: no');

// Flush inicial
echo "⏳ Ejecutando cron_sender...\n\n";
ob_flush(); flush();

// Ejecutar el cron real
ob_start();
require __DIR__ . '/cron_sender.php';
$output = ob_get_clean();

echo $output;
echo "\n✅ Completado.\n";
