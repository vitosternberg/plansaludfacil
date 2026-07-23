<?php
/**
 * pixel_tracker.php
 * Píxel de seguimiento de apertura de emails (1x1 GIF transparente).
 * Parámetros GET: log_id, campaign_id
 */

// DB creds (autónomo, sin dependencias)
$is_local = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || ($_SERVER['HTTP_HOST'] ?? '') === '127.0.0.1';
$db_host = $is_local ? '127.0.0.1' : 'localhost';
$db_user = $is_local ? 'root' : 'plansalu_blogger';
$db_pass = $is_local ? '' : 'Blog.2025!#';
$db_name = 'plansalu_blog';

$log_id      = filter_input(INPUT_GET, 'log_id', FILTER_VALIDATE_INT);
$campaign_id = filter_input(INPUT_GET, 'campaign_id', FILTER_VALIDATE_INT);

if ($log_id && $campaign_id) {
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, 3306);
        if (!$conn->connect_error) {
            $conn->set_charset("utf8mb4");
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';
            $stmt = $conn->prepare("INSERT INTO email_opens (log_id, campaign_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iiss", $log_id, $campaign_id, $ip, $ua);
                $stmt->execute();
            }
            $conn->close();
        }
    } catch (\Throwable $e) {}
}

header('Content-Type: image/gif');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICRAEAOw==');
exit;
