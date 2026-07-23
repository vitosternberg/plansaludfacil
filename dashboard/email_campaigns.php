<?php
/**
 * dashboard/email_campaigns.php
 * Dashboard para gestionar campañas de email masivo.
 * Acceso: ?key=API_SECRET_KEY
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ── Auth ──
$key = $_GET['key'] ?? '';
if ($key !== API_SECRET_KEY) {
    http_response_code(401);
    die('<div style="text-align:center;margin-top:100px;font-family:sans-serif;"><h1>⛔ Acceso denegado</h1><p>Usá <code>?key=tu_api_key</code></p></div>');
}

$conn = connect_db_simple();
if (!$conn) { die('⛔ Error de conexión a BD'); }
$conn->set_charset("utf8mb4");

$BASE_URL = 'https://plansaludfacil.cl/plansaludfacil_new';
$msg = '';
$err = '';

// ═══════════════════════════════════════
//  HELPERS
// ═══════════════════════════════════════

function first_name(string $n): string {
    $p = explode(' ', trim($n));
    return mb_convert_case($p[0], MB_CASE_TITLE, 'UTF-8');
}

function unsubscribe_token(string $e): string {
    return hash('sha256', $e . API_SECRET_KEY);
}

function send_one($conn, $row, $template, $asunto, $campaign_id, $BASE_URL) {
    $log_id = $row['log_id'];
    $email  = $row['correo'];
    $fn     = first_name($row['nombre']);
    $uns    = $BASE_URL . '/unsubscribe.php?email=' . urlencode($email) . '&token=' . $row['unsubscribe_token'];

    $body = str_replace(
        ['{{first_name}}', '{{unsubscribe_url}}'],
        [$fn, $uns],
        $template
    );

    try {
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
        $mail->addAddress($email, $row['nombre']);
        $mail->Subject = $asunto;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>','</p>'], ["\n","\n\n"], $body));
        $mail->send();

        $conn->query("UPDATE email_log SET enviado=1, sent_at=NOW(), error=NULL WHERE id=$log_id");
        $conn->query("UPDATE email_campaigns SET sent_count=sent_count+1 WHERE id=$campaign_id");
        return ['ok' => true, 'who' => "$fn <$email>"];
    } catch (Exception $e) {
        $err = $mail->ErrorInfo ?: $e->getMessage();
        $safe = $conn->real_escape_string(substr($err, 0, 500));
        $conn->query("UPDATE email_log SET enviado=0, error='$safe' WHERE id=$log_id");
        $conn->query("UPDATE email_campaigns SET error_count=error_count+1 WHERE id=$campaign_id");
        return ['ok' => false, 'who' => "$fn <$email>", 'error' => $err];
    }
}

// ═══════════════════════════════════════
//  ACTIONS (POST)
// ═══════════════════════════════════════

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $asunto = trim($_POST['asunto'] ?? '');
        $html   = $_POST['html'] ?? '';
        $nombre = 'campana_' . date('Ymd_His');

        if (empty($asunto) || empty($html)) {
            $err = 'Asunto y HTML son obligatorios.';
        } else {
            $stmt = $conn->prepare("INSERT INTO email_campaigns (nombre, asunto, html_template) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $asunto, $html);
            $stmt->execute();
            $cid = $conn->insert_id;

            // Encolar contactos
            $q = 0;
            $contacts = $conn->query("
                SELECT pf.id, pf.nombre, pf.correo
                FROM procesar_formularios pf
                WHERE pf.unsubscribed = 0
                  AND pf.correo IS NOT NULL AND pf.correo != ''
                  AND pf.id NOT IN (SELECT el.contacto_id FROM email_log el WHERE el.campaign_id = $cid)
            ");
            $ins = $conn->prepare("INSERT INTO email_log (contacto_id, campaign_id, correo, nombre, unsubscribe_token) VALUES (?, ?, ?, ?, ?)");
            while ($c = $contacts->fetch_assoc()) {
                $tok = unsubscribe_token($c['correo']);
                $ins->bind_param("iisss", $c['id'], $cid, $c['correo'], $c['nombre'], $tok);
                $ins->execute(); $q++;
            }
            $conn->query("UPDATE email_campaigns SET total_contacts=$q WHERE id=$cid");
            $msg = "✅ Campaña #$cid creada. $q contactos encolados. <strong>No se ha enviado nada aún.</strong>";
        }
    }

    elseif ($action === 'send_batch') {
        $cid = (int)($_POST['campaign_id'] ?? 0);
        $batch = min((int)($_POST['batch'] ?? 10), 50);

        $cam = $conn->query("SELECT * FROM email_campaigns WHERE id=$cid")->fetch_assoc();
        if (!$cam) { $err = 'Campaña no encontrada.'; }
        else {
            if ($cam['estado'] === 'borrador') {
                $conn->query("UPDATE email_campaigns SET estado='enviando', started_at=NOW() WHERE id=$cid");
            }
            $pending = $conn->query("SELECT id as log_id, correo, nombre, unsubscribe_token FROM email_log WHERE campaign_id=$cid AND enviado=0 LIMIT $batch");
            $sent = 0; $failed = 0;
            while ($row = $pending->fetch_assoc()) {
                $res = send_one($conn, $row, $cam['html_template'], $cam['asunto'], $cid, $BASE_URL);
                if ($res['ok']) $sent++; else $failed++;
                usleep(300000);
            }
            $rem = $conn->query("SELECT COUNT(*) as n FROM email_log WHERE campaign_id=$cid AND enviado=0")->fetch_assoc()['n'];
            if ($rem == 0) {
                $conn->query("UPDATE email_campaigns SET estado='completada', completed_at=NOW() WHERE id=$cid");
            }
            $msg = "📤 Lote enviado: $sent OK, $failed fallidos. Pendientes: $rem.";
        }
    }

    elseif ($action === 'send_all') {
        $cid = (int)($_POST['campaign_id'] ?? 0);
        $cam = $conn->query("SELECT * FROM email_campaigns WHERE id=$cid")->fetch_assoc();
        if (!$cam) { $err = 'Campaña no encontrada.'; }
        else {
            if ($cam['estado'] === 'borrador') {
                $conn->query("UPDATE email_campaigns SET estado='enviando', started_at=NOW() WHERE id=$cid");
            }
            $sent = 0; $failed = 0;
            // Enviar de a 10, pero en loop hasta acabar
            set_time_limit(300);
            while (true) {
                $pending = $conn->query("SELECT id as log_id, correo, nombre, unsubscribe_token FROM email_log WHERE campaign_id=$cid AND enviado=0 LIMIT 10");
                if ($pending->num_rows === 0) break;
                while ($row = $pending->fetch_assoc()) {
                    $res = send_one($conn, $row, $cam['html_template'], $cam['asunto'], $cid, $BASE_URL);
                    if ($res['ok']) $sent++; else $failed++;
                    usleep(400000);
                }
            }
            $conn->query("UPDATE email_campaigns SET estado='completada', completed_at=NOW() WHERE id=$cid");
            $msg = "✅ Campaña completada. $sent enviados, $failed fallidos.";
        }
    }

    elseif ($action === 'retry') {
        $cid = (int)($_POST['campaign_id'] ?? 0);
        $conn->query("UPDATE email_log SET error=NULL WHERE campaign_id=$cid AND enviado=0 AND error IS NOT NULL");
        $n = $conn->affected_rows;
        $msg = "🔄 $n registros fallidos vueltos a encolar. Usá Enviar lote para reintentar.";
    }
}

// ═══════════════════════════════════════
//  DATOS PARA LA VISTA
// ═══════════════════════════════════════

$campaigns = [];
$res = $conn->query("
    SELECT c.*,
           COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND enviado=1),0) as sent,
           COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND enviado=0 AND error IS NULL),0) as pending,
           COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND error IS NOT NULL),0) as failed
    FROM email_campaigns c ORDER BY c.id DESC LIMIT 20
");
while ($r = $res->fetch_assoc()) $campaigns[] = $r;

// Últimos envíos
$recent = $conn->query("
    SELECT el.correo, el.nombre, el.enviado, el.sent_at, el.error, c.nombre as campana
    FROM email_log el
    JOIN email_campaigns c ON c.id = el.campaign_id
    ORDER BY el.id DESC LIMIT 30
");
$logs = [];
while ($r = $recent->fetch_assoc()) $logs[] = $r;

// Totales
$totalContacts = $conn->query("SELECT COUNT(*) as n FROM procesar_formularios WHERE unsubscribed=0 AND correo IS NOT NULL AND correo!=''")->fetch_assoc()['n'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campañas de Email — PlanSaludFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        .progress-bar { transition: width 0.5s ease; }
        pre { white-space: pre-wrap; word-break: break-word; font-size: 12px; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <div class="max-w-6xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">📧 Campañas de Email</h1>
                <p class="text-sm text-gray-500 mt-1"><?= $totalContacts ?> contactos activos · Paso 1: crear campaña → Paso 2: enviar</p>
            </div>
            <a href="?key=<?= urlencode($key) ?>" class="text-sm text-blue-600 hover:underline">🔄 Refrescar</a>
        </div>

        <?php if ($msg): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 mb-6 text-sm"><?= $msg ?></div>
        <?php endif; ?>
        <?php if ($err): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4 mb-6 text-sm"><?= $err ?></div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-5 gap-6">

            <!-- Columna izquierda: Crear campaña -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">🆕 Nueva Campaña</h2>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="action" value="create">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Asunto del correo</label>
                            <input type="text" name="asunto" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none"
                                   placeholder="Ej: ¿Estás pagando sobreprecio en tu Isapre?">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                                HTML del email
                                <span class="font-normal text-gray-400">— usa <code class="bg-gray-100 px-1 rounded">{{first_name}}</code> y <code class="bg-gray-100 px-1 rounded">{{unsubscribe_url}}</code></span>
                            </label>
                            <textarea name="html" required rows="14"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none"
                                      placeholder="Pegá tu HTML aquí..."></textarea>
                        </div>
                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-sm">
                            📋 Crear campaña y encolar contactos
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 mt-3 text-center">Al crear la campaña, los contactos se encolan pero <strong>no se envía nada</strong> hasta que presiones "Enviar".</p>
                </div>
            </div>

            <!-- Columna derecha: Campañas activas -->
            <div class="lg:col-span-3 space-y-5">
                <h2 class="text-lg font-bold text-gray-800">📊 Campañas</h2>

                <?php if (empty($campaigns)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
                    <iconify-icon icon="mdi:email-outline" width="48"></iconify-icon>
                    <p class="mt-3">No hay campañas aún. Creá una en el formulario de la izquierda.</p>
                </div>
                <?php endif; ?>

                <?php foreach ($campaigns as $cam): 
                    $pct = $cam['total_contacts'] > 0 ? round(($cam['sent'] / $cam['total_contacts']) * 100) : 0;
                    $estColor = match($cam['estado']) {
                        'completada' => 'bg-green-100 text-green-700',
                        'enviando'   => 'bg-blue-100 text-blue-700',
                        default      => 'bg-gray-100 text-gray-600'
                    };
                ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium <?= $estColor ?>"><?= $cam['estado'] ?></span>
                                <span class="text-xs text-gray-400">#<?= $cam['id'] ?> — <?= $cam['created_at'] ?></span>
                            </div>
                            <h3 class="font-bold text-gray-800 mt-1"><?= htmlspecialchars($cam['asunto']) ?></h3>
                        </div>
                    </div>

                    <!-- Progress bar -->
                    <div class="mb-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span><?= $cam['sent'] ?> de <?= $cam['total_contacts'] ?> enviados</span>
                            <span><?= $pct ?>%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="progress-bar h-full rounded-full <?= $cam['estado'] === 'completada' ? 'bg-green-500' : 'bg-blue-500' ?>" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>

                    <!-- Stats mini -->
                    <div class="flex gap-4 text-xs text-gray-500 mb-4">
                        <span>✅ <?= $cam['sent'] ?></span>
                        <span>⏳ <?= $cam['pending'] ?></span>
                        <span>❌ <?= $cam['failed'] ?></span>
                    </div>

                    <!-- Botones de acción -->
                    <?php if ($cam['estado'] !== 'completada'): ?>
                    <div class="flex gap-2 flex-wrap">
                        <form method="post" class="inline">
                            <input type="hidden" name="action" value="send_batch">
                            <input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>">
                            <input type="hidden" name="batch" value="10">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                ▶ Enviar lote (10)
                            </button>
                        </form>
                        <form method="post" class="inline">
                            <input type="hidden" name="action" value="send_batch">
                            <input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>">
                            <input type="hidden" name="batch" value="25">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                ▶▶ Enviar 25
                            </button>
                        </form>
                        <form method="post" class="inline" onsubmit="return confirm('¿Enviar TODOS los pendientes de una vez? Esto puede tardar.')">
                            <input type="hidden" name="action" value="send_all">
                            <input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                ⚡ Enviar todo
                            </button>
                        </form>
                        <?php if ($cam['failed'] > 0): ?>
                        <form method="post" class="inline">
                            <input type="hidden" name="action" value="retry">
                            <input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>">
                            <button type="submit" class="bg-amber-100 hover:bg-amber-200 text-amber-800 text-sm font-semibold px-4 py-2 rounded-lg transition">
                                🔄 Reintentar fallidos
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-sm text-green-600 font-medium">✅ Campaña completada</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <!-- Últimos envíos -->
                <?php if (!empty($logs)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-3">📬 Últimos envíos</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-gray-400 uppercase border-b border-gray-100">
                                    <th class="text-left py-2 pr-3">Contacto</th>
                                    <th class="text-left py-2 pr-3">Campaña</th>
                                    <th class="text-left py-2 pr-3">Estado</th>
                                    <th class="text-left py-2">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($logs, 0, 20) as $l): ?>
                                <tr class="border-b border-gray-50">
                                    <td class="py-2 pr-3">
                                        <div class="font-medium text-gray-700"><?= htmlspecialchars($l['nombre']) ?></div>
                                        <div class="text-gray-400"><?= htmlspecialchars($l['correo']) ?></div>
                                    </td>
                                    <td class="py-2 pr-3 text-gray-500"><?= htmlspecialchars($l['campana']) ?></td>
                                    <td class="py-2 pr-3">
                                        <?php if ($l['enviado']): ?>
                                        <span class="text-green-600">✅ Enviado</span>
                                        <?php else: ?>
                                        <span class="text-red-500" title="<?= htmlspecialchars($l['error'] ?? '') ?>">❌ Falló</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 text-gray-400"><?= $l['sent_at'] ? date('d/m H:i', strtotime($l['sent_at'])) : '—' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
