<?php
/**
 * dashboard/email_campaigns.php
 * Dashboard para gestionar campañas de email masivo + listas externas.
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

$BASE_URL = 'https://plansaludfacil.cl';
$msg = ''; $err = '';
$tab = $_GET['tab'] ?? 'campaigns'; // campaigns | lists

// ═══════════════════════════════════════
//  HELPERS
// ═══════════════════════════════════════

function first_name(string $n): string {
    $p = explode(' ', trim($n));
    return mb_convert_case($p[0], MB_CASE_TITLE, 'UTF-8');
}

function gen_token(string $e): string {
    return hash('sha256', $e . API_SECRET_KEY);
}

function send_one($conn, $row, $template, $asunto, $cid, $BASE_URL, $source = 'contact') {
    $email   = $row['correo'];
    $fn      = first_name($row['nombre']);
    $uns     = $BASE_URL . '/unsubscribe.php?email=' . urlencode($email) . '&token=' . $row['unsubscribe_token'];
    $body    = str_replace(['{{first_name}}','{{unsubscribe_url}}'], [$fn, $uns], $template);

    try {
        // ── Tracking pixel: registrar apertura ──
        $pixel  = '<img src="' . $BASE_URL . '/pixel_tracker.php?log_id=' . $row['log_id'] . '&campaign_id=' . $cid . '" width="1" height="1" alt="" style="display:none">';
        if (stripos($body, '</body>') !== false) {
            $body = str_ireplace('</body>', $pixel . '</body>', $body);
        } else {
            $body .= $pixel;
        }

        // ── Click tracking: envolver links ──
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

        // ── Enviar ──
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

        $log_id = $row['log_id'];
        $conn->query("UPDATE email_log SET enviado=1, sent_at=NOW(), error=NULL WHERE id=$log_id");
        $conn->query("UPDATE email_campaigns SET sent_count=sent_count+1 WHERE id=$cid");
        return ['ok' => true, 'who' => "$fn <$email>"];
    } catch (Exception $e) {
        $err = $mail->ErrorInfo ?: $e->getMessage();
        $safe = $conn->real_escape_string(substr($err, 0, 500));
        $conn->query("UPDATE email_log SET enviado=0, error='$safe' WHERE id=$log_id");
        $conn->query("UPDATE email_campaigns SET error_count=error_count+1 WHERE id=$cid");
        return ['ok' => false, 'who' => "$fn <$email>", 'error' => $err];
    }
}

// ═══════════════════════════════════════
//  ACTIONS (POST)
// ═══════════════════════════════════════

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Crear campaña ──
    if ($action === 'create') {
        $asunto = trim($_POST['asunto'] ?? '');
        $html   = $_POST['html'] ?? '';
        $source = $_POST['source'] ?? 'contact'; // contact | list
        $list_id = (int)($_POST['list_id'] ?? 0);
        $nombre = 'campana_' . date('Ymd_His');

        if (empty($asunto) || empty($html)) {
            $err = 'Asunto y HTML son obligatorios.';
        } elseif ($source === 'list' && $list_id < 1) {
            $err = 'Seleccioná una lista externa.';
        } else {
            $stmt = $conn->prepare("INSERT INTO email_campaigns (nombre, asunto, html_template) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $asunto, $html);
            $stmt->execute();
            $cid = $conn->insert_id;
            $q = 0;

            if ($source === 'list') {
                $contacts = $conn->query("
                    SELECT id, nombre, correo, unsubscribe_token
                    FROM email_list_contacts
                    WHERE list_id = $list_id AND unsubscribed = 0
                ");
                $ins = $conn->prepare("INSERT INTO email_log (campaign_id, source, contacto_id, list_contact_id, correo, nombre, unsubscribe_token) VALUES (?, 'list', 0, ?, ?, ?, ?)");
                while ($c = $contacts->fetch_assoc()) {
                    $ins->bind_param("iisss", $cid, $c['id'], $c['correo'], $c['nombre'], $c['unsubscribe_token']);
                    $ins->execute(); $q++;
                }
            } else {
                $contacts = $conn->query("
                    SELECT pf.id, pf.nombre, pf.correo
                    FROM procesar_formularios pf
                    WHERE pf.unsubscribed = 0 AND pf.correo IS NOT NULL AND pf.correo != ''
                      AND pf.id NOT IN (SELECT el.contacto_id FROM email_log el WHERE el.campaign_id = $cid AND el.source='contact')
                ");
                $ins = $conn->prepare("INSERT INTO email_log (campaign_id, source, contacto_id, correo, nombre, unsubscribe_token) VALUES (?, 'contact', ?, ?, ?, ?)");
                while ($c = $contacts->fetch_assoc()) {
                    $tok = gen_token($c['correo']);
                    $ins->bind_param("iisss", $cid, $c['id'], $c['correo'], $c['nombre'], $tok);
                    $ins->execute(); $q++;
                }
            }
            $conn->query("UPDATE email_campaigns SET total_contacts=$q WHERE id=$cid");
            $msg = "✅ Campaña #$cid creada. $q contactos encolados. <strong>No se ha enviado nada aún.</strong>";
        }
    }

    // ── Enviar lote ──
    elseif ($action === 'send_batch') {
        $cid = (int)($_POST['campaign_id'] ?? 0);
        $batch = min((int)($_POST['batch'] ?? 10), 50);

        $cam = $conn->query("SELECT * FROM email_campaigns WHERE id=$cid")->fetch_assoc();
        if (!$cam) { $err = 'Campaña no encontrada.'; }
        else {
            if ($cam['estado'] === 'borrador') {
                $conn->query("UPDATE email_campaigns SET estado='enviando', started_at=NOW() WHERE id=$cid");
            }
            $pending = $conn->query("
                SELECT el.id as log_id, el.correo, el.nombre, el.unsubscribe_token, el.source, el.contacto_id, el.list_contact_id
                FROM email_log el WHERE el.campaign_id=$cid AND el.enviado=0 LIMIT $batch
            ");
            $sent = 0; $failed = 0; $i = 0;
            while ($row = $pending->fetch_assoc()) {
                $res = send_one($conn, $row, $cam['html_template'], $cam['asunto'], $cid, $BASE_URL, $row['source']);
                if ($res['ok']) $sent++; else $failed++;
                $i++;
                usleep(800000); // 0.8s entre emails (~75/min)
                if ($i % 20 === 0) sleep(5); // pausa de 5s cada 20 emails
            }
            $rem = $conn->query("SELECT COUNT(*) as n FROM email_log WHERE campaign_id=$cid AND enviado=0")->fetch_assoc()['n'];
            if ($rem == 0) {
                $conn->query("UPDATE email_campaigns SET estado='completada', completed_at=NOW() WHERE id=$cid");
            }
            $msg = "📤 Lote enviado: $sent OK, $failed fallidos. Pendientes: $rem.";
        }
    }

    // ── Enviar todo ──
    elseif ($action === 'send_all') {
        $cid = (int)($_POST['campaign_id'] ?? 0);
        $cam = $conn->query("SELECT * FROM email_campaigns WHERE id=$cid")->fetch_assoc();
        if (!$cam) { $err = 'Campaña no encontrada.'; }
        else {
            if ($cam['estado'] === 'borrador') {
                $conn->query("UPDATE email_campaigns SET estado='enviando', started_at=NOW() WHERE id=$cid");
            }
            $sent = 0; $failed = 0;
            set_time_limit(600);
            while (true) {
                $pending = $conn->query("SELECT el.id as log_id, el.correo, el.nombre, el.unsubscribe_token, el.source FROM email_log el WHERE el.campaign_id=$cid AND el.enviado=0 LIMIT 10");
                if ($pending->num_rows === 0) break;
                while ($row = $pending->fetch_assoc()) {
                    $res = send_one($conn, $row, $cam['html_template'], $cam['asunto'], $cid, $BASE_URL, $row['source']);
                    if ($res['ok']) $sent++; else $failed++;
                    usleep(1000000); // 1s entre emails (~60/min)
                }
                sleep(8); // pausa de 8s entre lotes de 10
            }
            $conn->query("UPDATE email_campaigns SET estado='completada', completed_at=NOW() WHERE id=$cid");
            $msg = "✅ Campaña completada. $sent enviados, $failed fallidos.";
        }
    }

    // ── Retry ──
    elseif ($action === 'retry') {
        $cid = (int)($_POST['campaign_id'] ?? 0);
        $conn->query("UPDATE email_log SET error=NULL WHERE campaign_id=$cid AND enviado=0 AND error IS NOT NULL");
        $n = $conn->affected_rows;
        $msg = "🔄 $n registros fallidos vueltos a encolar.";
    }

    // ── Test SMTP ──
    elseif ($action === 'test_smtp') {
        $debug = '';
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
            $mail->SMTPDebug  = 3;
            $mail->Debugoutput = function($s, $l) use (&$debug) { $debug .= "$s\n"; };
            $mail->setFrom(SMTP_USER, 'Plan Salud Fácil');
            $mail->addAddress(SMTP_USER, 'Admin');
            $mail->Subject = 'SMTP Test — ' . date('Y-m-d H:i:s');
            $mail->Body    = '<p>SMTP funcionando.</p>';
            $mail->send();
            $msg = "✅ SMTP OK — test enviado a " . SMTP_USER;
        } catch (Exception $e) {
            $err = "<strong>Error SMTP:</strong> " . ($mail->ErrorInfo ?: $e->getMessage());
            if (!empty($debug)) {
                $err .= "<br><pre style='font-size:11px;max-height:300px;overflow:auto;background:#fef2f2;padding:8px;border-radius:8px;margin-top:8px'>" . htmlspecialchars($debug) . "</pre>";
            }
        }
    }

    // ── Crear lista desde CSV ──
    elseif ($action === 'create_list') {
        $list_name = trim($_POST['list_name'] ?? 'Lista ' . date('Y-m-d H:i'));
        $csv_data  = trim($_POST['csv_data'] ?? '');

        if (empty($csv_data)) {
            $err = 'Pegá los datos CSV (nombre, email).';
        } else {
            $conn->query("INSERT INTO email_lists (nombre) VALUES ('" . $conn->real_escape_string($list_name) . "')");
            $lid = $conn->insert_id;
            $added = 0; $skipped = 0;
            $lines = explode("\n", $csv_data);
            $ins = $conn->prepare("INSERT INTO email_list_contacts (list_id, nombre, correo, unsubscribe_token) VALUES (?, ?, ?, ?)");

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $parts = str_getcsv($line);
                if (count($parts) < 2) continue;
                $name  = trim($parts[0]);
                $email = trim($parts[1]);
                if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $skipped++; continue; }
                $tok = gen_token($email);
                $ins->bind_param("isss", $lid, $name, $email, $tok);
                if ($ins->execute()) $added++; else $skipped++;
            }
            $conn->query("UPDATE email_lists SET total_contacts=$added WHERE id=$lid");
            $msg = "✅ Lista <strong>$list_name</strong> creada (#$lid). $added contactos agregados, $skipped omitidos.";
            $tab = 'lists';
        }
    }

    // ── Eliminar lista ──
    elseif ($action === 'delete_list') {
        $lid = (int)($_POST['list_id'] ?? 0);
        $conn->query("DELETE FROM email_list_contacts WHERE list_id=$lid");
        $conn->query("DELETE FROM email_lists WHERE id=$lid");
        $msg = "🗑️ Lista #$lid eliminada.";
        $tab = 'lists';
    }
}

// ═══════════════════════════════════════
//  DATOS PARA LA VISTA
// ═══════════════════════════════════════

try {

// Campañas
$campaigns = [];
$res = @$conn->query("
    SELECT c.*,
           COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND enviado=1),0) as sent,
           COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND enviado=0 AND error IS NULL),0) as pending,
           COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND error IS NOT NULL),0) as failed,
           COALESCE((SELECT COUNT(DISTINCT log_id) FROM email_opens WHERE campaign_id=c.id),0) as opens,
           COALESCE((SELECT COUNT(DISTINCT log_id) FROM email_clicks WHERE campaign_id=c.id),0) as clicks
    FROM email_campaigns c ORDER BY c.id DESC LIMIT 20
");
if (!$res) {
    $res = $conn->query("
        SELECT c.*,
               COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND enviado=1),0) as sent,
               COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND enviado=0 AND error IS NULL),0) as pending,
               COALESCE((SELECT COUNT(*) FROM email_log WHERE campaign_id=c.id AND error IS NOT NULL),0) as failed,
               0 as opens,
               0 as clicks
        FROM email_campaigns c ORDER BY c.id DESC LIMIT 20
    ");
}
if ($res) while ($r = $res->fetch_assoc()) $campaigns[] = $r;

// Últimos envíos
$logs = [];
$r2 = @$conn->query("SELECT el.*, c.nombre as campana FROM email_log el JOIN email_campaigns c ON c.id=el.campaign_id ORDER BY el.id DESC LIMIT 30");
if ($r2) while ($l = $r2->fetch_assoc()) $logs[] = $l;

// Listas externas
$lists = [];
$r3 = @$conn->query("SELECT * FROM email_lists ORDER BY id DESC");
if ($r3) while ($l = $r3->fetch_assoc()) $lists[] = $l;

// Contadores
$tc = @$conn->query("SELECT COUNT(*) as n FROM procesar_formularios WHERE unsubscribed=0 AND correo IS NOT NULL AND correo!=''");
$totalContacts = ($tc && $r = $tc->fetch_assoc()) ? ($r['n'] ?? 0) : 0;

} catch (\Throwable $e) {
    $campaigns = [];
    $logs = [];
    $lists = [];
    $totalContacts = 0;
}
$sources = [
    'contact' => '📇 BD de contactos (procesar_formularios) — ' . $totalContacts . ' activos',
    'list'    => '📋 Lista externa (contactos cargados por CSV)',
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campañas de Email — PlanSaludFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>.progress-bar { transition: width 0.5s ease; }</style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">
<div class="max-w-6xl mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">📧 Campañas de Email</h1>
        <div class="flex gap-2">
            <form method="post" class="inline"><input type="hidden" name="action" value="test_smtp">
                <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium px-3 py-1.5 rounded-lg transition">🔧 Test SMTP</button>
            </form>
            <a href="?key=<?= urlencode($key) ?>&tab=<?= $tab ?>" class="text-sm text-blue-600 hover:underline">🔄 Refrescar</a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 mb-6 bg-white rounded-xl p-1 shadow-sm border border-gray-100 w-fit">
        <a href="?key=<?= urlencode($key) ?>&tab=campaigns" class="px-5 py-2 rounded-lg text-sm font-medium transition <?= $tab==='campaigns' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">📊 Campañas</a>
        <a href="?key=<?= urlencode($key) ?>&tab=lists" class="px-5 py-2 rounded-lg text-sm font-medium transition <?= $tab==='lists' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">📋 Listas Externas</a>
    </div>

    <?php if ($msg): ?><div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 mb-6 text-sm"><?= $msg ?></div><?php endif; ?>
    <?php if ($err): ?><div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4 mb-6 text-sm"><?= $err ?></div><?php endif; ?>

    <!-- ═══════════════════ TAB: CAMPAÑAS ═══════════════════ -->
    <?php if ($tab === 'campaigns'): ?>
    <div class="grid lg:grid-cols-5 gap-6">

        <!-- Columna izquierda: formulario -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4">
                <h2 class="text-lg font-bold text-gray-800 mb-4">🆕 Nueva Campaña</h2>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="action" value="create">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Origen de contactos</label>
                        <select name="source" id="campaignSource" onchange="toggleSource()" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <?php foreach ($sources as $val => $label): ?>
                            <option value="<?= $val ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="listSelector" style="display:none">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lista a usar</label>
                        <select name="list_id" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400">
                            <option value="">— Elegir lista —</option>
                            <?php foreach ($lists as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nombre']) ?> (<?= $l['total_contacts'] ?> contactos)</option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (empty($lists)): ?>
                        <p class="text-xs text-amber-600 mt-1">⚠️ No hay listas. Creá una en la pestaña "Listas Externas".</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Asunto del correo</label>
                        <input type="text" name="asunto" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none" placeholder="Ej: ¿Estás pagando sobreprecio en tu Isapre?">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">HTML del email <span class="font-normal text-gray-400">— usa <code class="bg-gray-100 px-1 rounded">{{first_name}}</code> y <code class="bg-gray-100 px-1 rounded">{{unsubscribe_url}}</code></span></label>
                        <textarea name="html" required rows="12" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none" placeholder="Pegá tu HTML aquí..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-sm">📋 Crear campaña y encolar contactos</button>
                </form>
                <p class="text-xs text-gray-400 mt-3 text-center">Al crear la campaña, los contactos se encolan pero <strong>no se envía nada</strong> hasta que presiones "Enviar".</p>
            </div>
        </div>

        <!-- Columna derecha: campañas -->
        <div class="lg:col-span-3 space-y-5">
            <h2 class="text-lg font-bold text-gray-800">📊 Campañas</h2>
            <?php if (empty($campaigns)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
                <iconify-icon icon="mdi:email-outline" width="48"></iconify-icon>
                <p class="mt-3">No hay campañas aún.</p>
            </div>
            <?php endif; ?>

            <?php foreach ($campaigns as $cam):
                $pct = $cam['total_contacts'] > 0 ? round(($cam['sent'] / $cam['total_contacts']) * 100) : 0;
                $estColor = match($cam['estado']) { 'completada'=>'bg-green-100 text-green-700', 'enviando'=>'bg-blue-100 text-blue-700', default=>'bg-gray-100 text-gray-600' };
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
                <div class="mb-3">
                    <div class="flex justify-between text-xs text-gray-500 mb-1"><span><?= $cam['sent'] ?> de <?= $cam['total_contacts'] ?></span><span><?= $pct ?>%</span></div>
                    <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden"><div class="progress-bar h-full rounded-full <?= $cam['estado']==='completada'?'bg-green-500':'bg-blue-500' ?>" style="width:<?= $pct ?>%"></div></div>
                </div>
                <?php $open_rate = $cam['sent'] > 0 ? round(($cam['opens'] / $cam['sent']) * 100) : 0; ?>
                <?php $click_rate = $cam['sent'] > 0 ? round(($cam['clicks'] / $cam['sent']) * 100) : 0; ?>
                <div class="flex gap-4 text-xs text-gray-500 mb-2"><span>✅ <?= $cam['sent'] ?></span><span>⏳ <?= $cam['pending'] ?></span><span>❌ <?= $cam['failed'] ?></span></div>
                <div class="flex gap-4 text-xs text-gray-400 mb-4"><span>👁 <?= $cam['opens'] ?> aperturas (<?= $open_rate ?>%)</span><span>👆 <?= $cam['clicks'] ?> clicks (<?= $click_rate ?>%)</span></div>
                <?php if ($cam['estado'] !== 'completada'): ?>
                <div class="flex gap-2 flex-wrap">
                    <form method="post" class="inline"><input type="hidden" name="action" value="send_batch"><input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>"><input type="hidden" name="batch" value="10"><button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">▶ Enviar 10</button></form>
                    <form method="post" class="inline"><input type="hidden" name="action" value="send_batch"><input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>"><input type="hidden" name="batch" value="25"><button class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">▶▶ Enviar 25</button></form>
                    <form method="post" class="inline" onsubmit="return confirm('¿Enviar TODOS los pendientes?')"><input type="hidden" name="action" value="send_all"><input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>"><button class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">⚡ Enviar todo</button></form>
                    <?php if ($cam['failed'] > 0): ?><form method="post" class="inline"><input type="hidden" name="action" value="retry"><input type="hidden" name="campaign_id" value="<?= $cam['id'] ?>"><button class="bg-amber-100 hover:bg-amber-200 text-amber-800 text-sm font-semibold px-4 py-2 rounded-lg transition">🔄 Retry</button></form><?php endif; ?>
                </div>
                <?php else: ?><div class="text-sm text-green-600 font-medium">✅ Completada</div><?php endif; ?>
            </div>
            <?php endforeach; ?>

            <?php if (!empty($logs)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3">📬 Últimos envíos</h3>
                <div class="overflow-x-auto"><table class="w-full text-xs">
                    <thead><tr class="text-gray-400 uppercase border-b border-gray-100"><th class="text-left py-2 pr-3">Contacto</th><th class="text-left py-2 pr-3">Campaña</th><th class="text-left py-2 pr-3">Estado</th><th class="text-left py-2">Fecha</th></tr></thead>
                    <tbody>
                    <?php foreach (array_slice($logs,0,20) as $l): ?>
                    <tr class="border-b border-gray-50">
                        <td class="py-2 pr-3"><div class="font-medium text-gray-700"><?= htmlspecialchars($l['nombre']) ?></div><div class="text-gray-400"><?= htmlspecialchars($l['correo']) ?></div></td>
                        <td class="py-2 pr-3 text-gray-500"><?= htmlspecialchars($l['campana']) ?></td>
                        <td class="py-2 pr-3">
                            <?php if ($l['enviado']): ?><span class="text-green-600">✅</span>
                            <?php else: ?><span class="text-red-500">❌</span><?php if (!empty($l['error'])): ?><div class="text-[10px] text-red-400 mt-0.5 max-w-[180px] truncate" title="<?= htmlspecialchars($l['error']) ?>"><?= htmlspecialchars($l['error']) ?></div><?php endif; ?><?php endif; ?>
                        </td>
                        <td class="py-2 text-gray-400"><?= $l['sent_at'] ? date('d/m H:i', strtotime($l['sent_at'])) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ═══════════════════ TAB: LISTAS ═══════════════════ -->
    <?php if ($tab === 'lists'): ?>
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Crear lista -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">📋 Nueva Lista Externa</h2>
            <form method="post">
                <input type="hidden" name="action" value="create_list">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre de la lista</label>
                    <input type="text" name="list_name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" placeholder="Ej: Leads Julio 2026">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Datos CSV <span class="font-normal text-gray-400">— Formato: <code>Nombre, email</code> (uno por línea)</span></label>
                    <textarea name="csv_data" required rows="10" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400" placeholder="Juan Pérez, juan@gmail.com&#10;María García, maria@hotmail.com&#10;..."></textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-sm">📥 Cargar lista</button>
            </form>
            <p class="text-xs text-gray-400 mt-3">También podés pegar desde Excel: copiá las columnas Nombre y Email, pegalas acá.</p>
        </div>

        <!-- Listas existentes -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-gray-800">📚 Listas guardadas</h2>
            <?php if (empty($lists)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center text-gray-400">
                <iconify-icon icon="mdi:format-list-bulleted" width="48"></iconify-icon>
                <p class="mt-3">No hay listas externas aún.</p>
            </div>
            <?php endif; ?>
            <?php foreach ($lists as $l): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800"><?= htmlspecialchars($l['nombre']) ?></h3>
                        <p class="text-xs text-gray-400 mt-0.5">#<?= $l['id'] ?> · <?= $l['total_contacts'] ?> contactos · <?= $l['created_at'] ?></p>
                    </div>
                    <form method="post" onsubmit="return confirm('¿Eliminar esta lista y todos sus contactos?')">
                        <input type="hidden" name="action" value="delete_list">
                        <input type="hidden" name="list_id" value="<?= $l['id'] ?>">
                        <button class="text-xs text-red-500 hover:text-red-700 font-medium">🗑️ Eliminar</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
// Mostrar/ocultar selector de lista según origen
function toggleSource() {
    var src = document.getElementById('campaignSource').value;
    document.getElementById('listSelector').style.display = src === 'list' ? 'block' : 'none';
}
toggleSource();
</script>
</body>
</html>
