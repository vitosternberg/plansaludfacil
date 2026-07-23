<?php
/**
 * click_tracker.php
 * Tracking de clicks en links de emails de campaña.
 * Registra el click y redirige a la URL original.
 * Parámetros GET: log_id, campaign_id, url (base64url-encoded)
 */

require_once __DIR__ . '/config.php';

$log_id      = filter_input(INPUT_GET, 'log_id', FILTER_VALIDATE_INT);
$campaign_id = filter_input(INPUT_GET, 'campaign_id', FILTER_VALIDATE_INT);
$encoded_url = $_GET['url'] ?? '';

// Decodificar URL
$target_url = $encoded_url ? base64_decode(strtr($encoded_url, '-_', '+/'), true) : '';
if (!$target_url || !filter_var($target_url, FILTER_VALIDATE_URL)) {
    header('Location: /');
    exit;
}

if ($log_id && $campaign_id) {
    try {
        $conn = connect_db_simple();
        if ($conn) {
            $ip   = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
            $ua   = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
            $stmt = $conn->prepare(
                "INSERT INTO email_clicks (log_id, campaign_id, url, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("iisss", $log_id, $campaign_id, $target_url, $ip, $ua);
            $stmt->execute();
            $conn->close();
        }
    } catch (Exception $e) {
        error_log('click_tracker error: ' . $e->getMessage());
    }
}

// Redirigir siempre (el click no debe fallar aunque falle el tracking)
header('Location: ' . $target_url);
exit;
