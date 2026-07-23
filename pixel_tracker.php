<?php
/**
 * pixel_tracker.php
 * Píxel de seguimiento de apertura de emails (1x1 GIF transparente).
 * Llamado como <img> desde el body del email.
 * Parámetros GET: log_id, campaign_id
 */

require_once __DIR__ . '/config.php';

$log_id      = filter_input(INPUT_GET, 'log_id', FILTER_VALIDATE_INT);
$campaign_id = filter_input(INPUT_GET, 'campaign_id', FILTER_VALIDATE_INT);

if ($log_id && $campaign_id) {
    try {
        $conn = connect_db_simple();
        if ($conn) {
            $ip   = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
            $ua   = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
            $stmt = $conn->prepare(
                "INSERT INTO email_opens (log_id, campaign_id, ip_address, user_agent) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iiss", $log_id, $campaign_id, $ip, $ua);
            $stmt->execute();
            $conn->close();
        }
    } catch (Exception $e) {
        // Silencioso — el pixel nunca debe fallar visiblemente
        error_log('pixel_tracker error: ' . $e->getMessage());
    }
}

// Servir GIF transparente 1x1 (siempre, aunque falle el registro)
header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICRAEAOw==');
exit;
