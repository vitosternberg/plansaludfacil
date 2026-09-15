<?php
/**
 * Tracking de visitas Omniflow. No imprime HTML.
 */
if (!defined('OMNIFLOW_TRACKED')) {
    define('OMNIFLOW_TRACKED', true);
    $omniflow_config = dirname(__DIR__) . '/omniflow_config.php';
    if (is_readable($omniflow_config)) {
        require_once $omniflow_config;
    }
    try {
        if (defined('DB_HOST')) {
            $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$db->connect_error) {
                $db->set_charset('utf8mb4');
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
                $stmt = $db->prepare('INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)');
                if ($stmt) {
                    $stmt->bind_param('sss', $ip, $ua, $url);
                    $stmt->execute();
                    $stmt->close();
                }
                $lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
                if ($lead_id) {
                    $stmt2 = $db->prepare('INSERT INTO lead_visits (lead_id, url_visitada) VALUES (?, ?)');
                    if ($stmt2) {
                        $stmt2->bind_param('is', $lead_id, $url);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                }
                $db->close();
            }
        }
    } catch (Exception $e) {
        error_log('Omniflow Tracking Error: ' . $e->getMessage());
    }
}
