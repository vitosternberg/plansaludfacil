<?php
/**
 * pixel_tracker.php
 * Píxel de seguimiento de apertura de emails (1x1 GIF transparente).
 * Parámetros GET: log_id, campaign_id
 * Autónomo — no requiere config.php externo.
 */

// ── DB creds (mismas que config.php) ──
$is_local = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || ($_SERVER['HTTP_HOST'] ?? '') === '127.0.0.1';
define('DB_HOST', $is_local ? '127.0.0.1' : 'localhost');
define('DB_USER', $is_local ? 'root' : 'plansalu_blogger');
define('DB_PASS', $is_local ? '' : 'Blog.2025!#');
define('DB_NAME', 'plansalu_blog');

$log_id      = filter_input(INPUT_GET, 'log_id', FILTER_VALIDATE_INT);
$campaign_id = filter_input(INPUT_GET, 'campaign_id', FILTER_VALIDATE_INT);

if ($log_id && $campaign_id) {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
        if (!$conn->connect_error) {
            $conn->set_charset("utf8mb4");
            $ip   = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
            $ua   = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
            $stmt = $conn->prepare("INSERT INTO email_opens (log_id, campaign_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iiss", $log_id, $campaign_id, $ip, $ua);
                $stmt->execute();
            }
            $conn->close();
        }
    } catch (\Throwable $e) {
        // Silencioso
    }
}

// Servir GIF transparente 1x1 (siempre)
header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICRAEAOw==');
exit;
