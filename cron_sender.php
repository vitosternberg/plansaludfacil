<?php
/**
 * cron_sender.php
 * Envía hasta 100 emails pendientes por ejecución.
 * Diseñado para correr cada hora vía cron.
 * Uso: php cron_sender.php
 *   o desde cron: 0 * * * * php /path/to/cron_sender.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Solo ejecutar desde CLI o con key
$is_cli = (php_sapi_name() === 'cli');
$key = $_GET['key'] ?? '';
if (!$is_cli && $key !== API_SECRET_KEY) {
    http_response_code(401);
    die('Acceso denegado');
}

$MAX_PER_RUN = 100;       // máximo de emails por ejecución
$SLEEP_BETWEEN = 800000;  // 0.8s entre emails
$PAUSE_EVERY = 25;        // pausa cada N emails
$PAUSE_SECONDS = 8;       // segundos de pausa

$conn = connect_db_simple();
if (!$conn) { die("⛔ Error de conexión\n"); }
$conn->set_charset("utf8mb4");

$BASE_URL = 'https://plansaludfacil.cl';

// ── Helper (copia de send_one para cron) ──
function cron_send_one($conn, $row, $template, $asunto, $cid, $BASE_URL) {
    $email  = $row['correo'];
    $fn     = explode(' ', trim($row['nombre']))[0];
    $fn     = mb_convert_case($fn, MB_CASE_TITLE, 'UTF-8');
    $uns    = $BASE_URL . '/unsubscribe.php?email=' . urlencode($email) . '&token=' . $row['unsubscribe_token'];
    $body   = str_replace(['{{first_name}}','{{unsubscribe_url}}'], [$fn, $uns], $template);
    $log_id = $row['log_id'];

    try {
        // Tracking pixel
        $pixel = '<img src="' . $BASE_URL . '/pixel_tracker.php?log_id=' . $log_id . '&campaign_id=' . $cid . '" width="1" height="1" alt="" style="display:none">';
        if (stripos($body, '</body>') !== false) {
            $body = str_ireplace('</body>', $pixel . '</body>', $body);
        } else {
            $body .= $pixel;
        }

        // Click tracking
        $tracked = preg_replace_callback(
            '/href="(https?:\/\/[^"]+)"/i',
            function($m) use ($BASE_URL, $row, $cid) {
                $url = $m[1];
                if (stripos($url, 'unsubscribe') !== false) return $m[0];
                $encoded = strtr(base64_encode($url), '+/', '-_');
                return 'href="' . $BASE_URL . '/click_tracker.php?log_id=' . $row['log_id'] . '&campaign_id=' . $cid . '&url=' . urlencode($encoded) . '"';
            },
            $body
        );
        if ($tracked !== null) $body = $tracked;

        // Enviar
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;
        $mail->setFrom(SMTP_USER, 'Plan Salud Fácil');
        $mail->isHTML(true);
        $mail->addAddress($email, $row['nombre']);
        $mail->Subject = $asunto;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>','</p>'], ["\n","\n\n"], $body));
        $mail->send();

        $conn->query("UPDATE email_log SET enviado=1, sent_at=NOW(), error=NULL WHERE id=$log_id");
        $conn->query("UPDATE email_campaigns SET sent_count=sent_count+1 WHERE id=$cid");
        return ['ok' => true, 'who' => "$fn <$email>"];
    } catch (\Throwable $e) {
        $err = method_exists($e, 'getErrorInfo') ? $e->getErrorInfo() : $e->getMessage();
        $safe = $conn->real_escape_string(substr($err, 0, 500));
        $conn->query("UPDATE email_log SET enviado=0, error='$safe' WHERE id=$log_id");
        $conn->query("UPDATE email_campaigns SET error_count=error_count+1 WHERE id=$cid");
        return ['ok' => false, 'who' => "$fn <$email>", 'error' => $err];
    }
}

// ── Buscar campaña activa más antigua ──
$cam = $conn->query("
    SELECT c.* FROM email_campaigns c
    WHERE c.estado IN ('enviando','borrador')
    ORDER BY c.id ASC LIMIT 1
")->fetch_assoc();

if (!$cam) {
    echo date('Y-m-d H:i:s') . " — Sin campañas pendientes\n";
    exit(0);
}

$cid = $cam['id'];

// Marcar como enviando si está en borrador
if ($cam['estado'] === 'borrador') {
    $conn->query("UPDATE email_campaigns SET estado='enviando', started_at=NOW() WHERE id=$cid");
}

// ── Enviar lote ──
$pending = $conn->query("
    SELECT el.id as log_id, el.correo, el.nombre, el.unsubscribe_token, el.source
    FROM email_log el
    WHERE el.campaign_id=$cid AND el.enviado=0 AND el.error IS NULL
    ORDER BY el.id ASC LIMIT $MAX_PER_RUN
");

$sent = 0; $failed = 0; $i = 0;
while ($row = $pending->fetch_assoc()) {
    $res = cron_send_one($conn, $row, $cam['html_template'], $cam['asunto'], $cid, $BASE_URL);
    if ($res['ok']) {
        $sent++;
    } else {
        $failed++;
        echo "  ❌ " . $res['who'] . " → " . ($res['error'] ?? '?') . "\n";
    }
    $i++;
    usleep($SLEEP_BETWEEN);
    if ($i % $PAUSE_EVERY === 0 && $i < $MAX_PER_RUN) {
        sleep($PAUSE_SECONDS);
    }
}

// ── Verificar si terminó ──
$rem = $conn->query("SELECT COUNT(*) as n FROM email_log WHERE campaign_id=$cid AND enviado=0 AND error IS NULL")->fetch_assoc()['n'];
if ($rem == 0) {
    $conn->query("UPDATE email_campaigns SET estado='completada', completed_at=NOW() WHERE id=$cid");
}

$total_sent = $conn->query("SELECT sent_count FROM email_campaigns WHERE id=$cid")->fetch_assoc()['sent_count'];
$total = $cam['total_contacts'];
echo date('Y-m-d H:i:s') . " — Campaña #$cid: $sent OK, $failed fallidos | Total: $total_sent/$total | Pendientes: $rem\n";

$conn->close();
