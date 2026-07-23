<?php
/**
 * campaign_sender.php
 * Gestor de campañas de email con PHPMailer + unsubscribe tracking.
 * 
 * USO (en producción):
 *   ?key=API_SECRET_KEY&action=init          → crea campaña + encola contactos
 *   ?key=API_SECRET_KEY&action=status        → estado actual
 *   ?key=API_SECRET_KEY&action=send          → enviar lote de 10
 *   ?key=API_SECRET_KEY&action=send&batch=25 → enviar lote de 25 (máx 50)
 *   ?key=API_SECRET_KEY&action=retry         → reintentar fallidos
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ── Auth ──
$key = $_GET['key'] ?? '';
if ($key !== API_SECRET_KEY) {
    http_response_code(401);
    die("⛔ No autorizado. Usa ?key=API_SECRET_KEY\n");
}

// ── Params ──
$action     = $_GET['action'] ?? 'status';
$campaign_id = (int)($_GET['campaign'] ?? 0);
$batch      = min((int)($_GET['batch'] ?? 10), 50);

$conn = connect_db_simple();
if (!$conn) { die("⛔ Error de conexión a BD\n"); }
$conn->set_charset("utf8mb4");

$BASE_URL = 'https://plansaludfacil.cl/plansaludfacil_new';

header('Content-Type: text/plain; charset=utf-8');

// ══════════════════════════════════════════════════
//  HELPERS
// ══════════════════════════════════════════════════

function first_name(string $nombre): string {
    $parts = explode(' ', trim($nombre));
    return mb_convert_case($parts[0], MB_CASE_TITLE, 'UTF-8');
}

function unsubscribe_token(string $email): string {
    return hash('sha256', $email . API_SECRET_KEY);
}

function unsubscribe_url(string $email): string {
    global $BASE_URL;
    $token = unsubscribe_token($email);
    return $BASE_URL . '/unsubscribe.php?email=' . urlencode($email) . '&token=' . $token;
}

function build_html(string $template, array $replace): string {
    return str_replace(array_keys($replace), array_values($replace), $template);
}

function make_mailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = SMTP_PORT;
    $mail->setFrom(SMTP_USER, 'Plan Salud Fácil');
    $mail->isHTML(true);
    return $mail;
}

// ══════════════════════════════════════════════════
//  ACTIONS
// ══════════════════════════════════════════════════

if ($action === 'init') {
    // ── Crear campaña desde template ──
    $templatePath = __DIR__ . '/_email_templates/evaluacion_plan.html';
    if (!file_exists($templatePath)) { die("⛔ Template no encontrado: $templatePath\n"); }
    
    $html   = file_get_contents($templatePath);
    $nombre = 'evaluacion_plan_v1';
    $asunto = '¿Estás pagando sobreprecio en tu Isapre?';

    // Ver si ya existe
    $check = $conn->query("SELECT id FROM email_campaigns WHERE nombre='$nombre'");
    if ($check && $check->num_rows > 0) {
        $existing = $check->fetch_assoc();
        $campaign_id = (int)$existing['id'];
        echo "⚠️  Campaña '$nombre' ya existe (id=$campaign_id). Usando existente.\n";
    } else {
        $stmt = $conn->prepare("INSERT INTO email_campaigns (nombre, asunto, html_template) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $asunto, $html);
        $stmt->execute();
        $campaign_id = $conn->insert_id;
        echo "✅ Campaña creada: $nombre (id=$campaign_id)\n";
    }

    // ── Encolar contactos elegibles ──
    $queued = 0;
    $skipped = 0;
    
    $contacts = $conn->query("
        SELECT pf.id, pf.nombre, pf.correo
        FROM procesar_formularios pf
        WHERE pf.unsubscribed = 0
          AND pf.correo IS NOT NULL AND pf.correo != ''
          AND pf.id NOT IN (
              SELECT el.contacto_id FROM email_log el WHERE el.campaign_id = $campaign_id
          )
    ");

    $stmt = $conn->prepare("INSERT INTO email_log (contacto_id, campaign_id, correo, nombre, unsubscribe_token) VALUES (?, ?, ?, ?, ?)");
    
    while ($c = $contacts->fetch_assoc()) {
        $token = unsubscribe_token($c['correo']);
        $stmt->bind_param("iisss", $c['id'], $campaign_id, $c['correo'], $c['nombre'], $token);
        if ($stmt->execute()) {
            $queued++;
        } else {
            $skipped++;
        }
    }

    // Actualizar contadores
    $conn->query("UPDATE email_campaigns SET total_contacts = $queued WHERE id = $campaign_id");
    
    echo "📋 Contactos encolados: $queued ($skipped omitidos)\n";
    echo "🔗 Para enviar: ?key=XXX&action=send&campaign=$campaign_id\n";
}

elseif ($action === 'status') {
    // ── Mostrar todas las campañas ──
    $result = $conn->query("
        SELECT c.*, 
               (SELECT COUNT(*) FROM email_log WHERE campaign_id = c.id AND enviado = 1) as sent,
               (SELECT COUNT(*) FROM email_log WHERE campaign_id = c.id AND enviado = 0) as pending,
               (SELECT COUNT(*) FROM email_log WHERE campaign_id = c.id AND error IS NOT NULL) as failed
        FROM email_campaigns c
        ORDER BY c.id DESC
        LIMIT 10
    ");
    
    echo "═══════════════════════════════════════════\n";
    echo "  CAMPAÑAS DE EMAIL\n";
    echo "═══════════════════════════════════════════\n\n";
    
    while ($cam = $result->fetch_assoc()) {
        $pct = $cam['total_contacts'] > 0 ? round($cam['sent'] / $cam['total_contacts'] * 100) : 0;
        echo "📧 [{$cam['id']}] {$cam['nombre']} — {$cam['estado']}\n";
        echo "    Total: {$cam['total_contacts']} | Enviados: {$cam['sent']} ($pct%) | Pendientes: {$cam['pending']} | Fallidos: {$cam['failed']}\n";
        if ($cam['started_at']) echo "    Inicio: {$cam['started_at']}\n";
        if ($cam['completed_at']) echo "    Fin: {$cam['completed_at']}\n";
        echo "\n";
    }
}

elseif ($action === 'send') {
    if (!$campaign_id) { die("⛔ Especifica ?campaign=ID\n"); }
    
    // Verificar campaña
    $cam = $conn->query("SELECT * FROM email_campaigns WHERE id = $campaign_id")->fetch_assoc();
    if (!$cam) { die("⛔ Campaña no encontrada\n"); }
    
    // Marcar como enviando
    if ($cam['estado'] === 'borrador') {
        $conn->query("UPDATE email_campaigns SET estado='enviando', started_at=NOW() WHERE id=$campaign_id");
    }
    
    // Obtener lote pendiente
    $pending = $conn->query("
        SELECT el.id as log_id, el.correo, el.nombre, el.unsubscribe_token, el.contacto_id
        FROM email_log el
        WHERE el.campaign_id = $campaign_id AND el.enviado = 0
        LIMIT $batch
    ");
    
    $toSend = $pending->num_rows;
    if ($toSend === 0) {
        $conn->query("UPDATE email_campaigns SET estado='completada', completed_at=NOW() WHERE id=$campaign_id");
        die("✅ Campaña completada. No quedan pendientes.\n");
    }
    
    $template = $cam['html_template'];
    $asunto   = $cam['asunto'];
    
    echo "📤 Enviando lote de $toSend emails...\n\n";
    
    $sent = 0;
    $failed = 0;
    $errors = [];
    
    while ($row = $pending->fetch_assoc()) {
        $log_id   = $row['log_id'];
        $email    = $row['correo'];
        $fn       = first_name($row['nombre']);
        $uns_link = $BASE_URL . '/unsubscribe.php?email=' . urlencode($email) . '&token=' . $row['unsubscribe_token'];
        
        $body = build_html($template, [
            '{{first_name}}'      => $fn,
            '{{unsubscribe_url}}' => $uns_link,
        ]);
        
        try {
            $mail = make_mailer();
            $mail->addAddress($email, $row['nombre']);
            $mail->Subject = "$asunto";
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>','</p>'], ["\n","\n\n"], $body));
            
            $mail->send();
            
            $conn->query("UPDATE email_log SET enviado=1, sent_at=NOW(), error=NULL WHERE id=$log_id");
            $conn->query("UPDATE email_campaigns SET sent_count = sent_count + 1 WHERE id=$campaign_id");
            
            echo "  ✅ $fn <$email>\n";
            $sent++;
            
        } catch (Exception $e) {
            $err = $mail->ErrorInfo ?: $e->getMessage();
            $safeErr = $conn->real_escape_string(substr($err, 0, 500));
            $conn->query("UPDATE email_log SET enviado=0, error='$safeErr' WHERE id=$log_id");
            $conn->query("UPDATE email_campaigns SET error_count = error_count + 1 WHERE id=$campaign_id");
            
            echo "  ❌ $fn <$email> — $err\n";
            $failed++;
            $errors[] = "$email: $err";
        }
        
        // Pausa entre envíos para no saturar SMTP
        usleep(300000); // 300ms
    }
    
    // Si ya no quedan pendientes, completar
    $remaining = $conn->query("SELECT COUNT(*) as n FROM email_log WHERE campaign_id=$campaign_id AND enviado=0")->fetch_assoc()['n'];
    if ($remaining == 0) {
        $conn->query("UPDATE email_campaigns SET estado='completada', completed_at=NOW() WHERE id=$campaign_id");
    }
    
    echo "\n───────────────────────────────────────────\n";
    echo "Lote: $sent enviados, $failed fallidos\n";
    echo "Pendientes totales: $remaining\n";
}

elseif ($action === 'retry') {
    if (!$campaign_id) { die("⛔ Especifica ?campaign=ID\n"); }
    
    // Resetear fallidos a pendientes
    $conn->query("UPDATE email_log SET error=NULL, enviado=0 WHERE campaign_id=$campaign_id AND enviado=0 AND error IS NOT NULL");
    
    $reset = $conn->affected_rows;
    echo "🔄 $reset registros fallidos vueltos a encolar.\n";
    echo "🔗 Para reenviar: ?key=XXX&action=send&campaign=$campaign_id\n";
}

else {
    echo "Acciones: init | status | send | retry\n";
    echo "  ?key=XXX&action=init               → crear campaña + encolar\n";
    echo "  ?key=XXX&action=status              → ver progreso\n";
    echo "  ?key=XXX&action=send&campaign=ID    → enviar lote (default 10)\n";
    echo "  ?key=XXX&action=send&campaign=ID&batch=25 → lote de 25\n";
    echo "  ?key=XXX&action=retry&campaign=ID   → reintentar fallidos\n";
}
