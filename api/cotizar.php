<?php
/**
 * api/cotizar.php
 * Endpoint que recibe datos del lead, devuelve recomendaciones y opcionalmente las envía por correo.
 * 
 * Método: POST
 * Body (JSON): { "nombre": "...", "edad": 27, "renta": 1300000, "cargas": 0, "intereses": [...], "email": "..." }
 * 
 * Respuesta (JSON): { "recomendaciones": [...], "email_sent": true/false, ... }
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Usa POST.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Leer input JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['renta'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos del lead. Campos requeridos: renta.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Valores por defecto
$lead = [
    'nombre'      => $input['nombre'] ?? 'Usuario',
    'edad'        => (int)($input['edad'] ?? 30),
    'renta'       => (int)($input['renta'] ?? 1000000),
    'cargas'      => (int)($input['cargas'] ?? 0),
    'uf_value'    => (int)($input['uf_value'] ?? 38500),
    'intereses'   => $input['intereses'] ?? ['Hospitalización', 'Atención Ambulatoria'],
    'edad_cargas' => $input['edad_cargas'] ?? null,
];

// Ejecutar motor PHP (sin dependencia de Python — compatible con shared hosting)
require_once __DIR__ . '/../core/cotizador_engine.php';

$result = motor_cotizar($lead);

if (isset($result['error'])) {
    http_response_code(500);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// Enriquecer con datos adicionales para el frontend
$result['timestamp'] = date('c');
$result['total_planes_evaluados'] = 2231;

// ─── ENVÍO DE COTIZACIÓN POR CORREO ───
$email_sent = false;
$recipient_email = trim($input['email'] ?? '');

if (!empty($recipient_email) && filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
    // Cargar PHPMailer
    $phpmailer_loaded = false;
    $phpmailer_base = __DIR__ . '/../PHPMailer/src/';
    if (file_exists($phpmailer_base . 'Exception.php')) {
        require_once $phpmailer_base . 'Exception.php';
        require_once $phpmailer_base . 'PHPMailer.php';
        require_once $phpmailer_base . 'SMTP.php';
        $phpmailer_loaded = true;
    }

    if ($phpmailer_loaded && class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        // Armar contenido del correo
        $nombre = htmlspecialchars($input['nombre'] ?? 'Usuario');
        $pct7 = intval($input['renta'] * 0.07);
        $isapre_colors = [
            'Banmédica' => '#2563eb', 'Colmena' => '#eab308', 'Consalud' => '#16a34a',
            'Cruz Blanca' => '#4f46e5', 'Esencial' => '#9333ea', 'Nueva Masvida' => '#db2777',
            'Vida Tres' => '#dc2626'
        ];

        $planes_html = '';
        $top = array_slice($result['recomendaciones'] ?? [], 0, 3);
        $labels = ['🥇 Más Afín', '🥈 Recomendado', '🥉 Alternativa'];
        foreach ($top as $i => $rec) {
            $bg = $isapre_colors[$rec['isapre']] ?? '#6b7280';
            $precio_formateado = '$' . number_format($rec['precio_clp'], 0, ',', '.');
            $dentro_7pct = $rec['precio_clp'] <= $pct7 ? '✅ Dentro del 7%' : '⚠️ $' . number_format($rec['precio_clp'] - $pct7, 0, ',', '.') . ' extra';
            $planes_html .= "
            <tr>
                <td style='padding:16px;border-bottom:1px solid #e5e7eb;background:" . ($i === 0 ? '#eff6ff' : '#fff') . "'>
                    <div style='display:flex;align-items:center;gap:8px;margin-bottom:4px'>
                        <span style='font-size:14px;font-weight:700'>" . $labels[$i] . "</span>
                        <span style='display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700;color:#fff;background:" . $bg . "'>" . htmlspecialchars($rec['isapre']) . "</span>
                    </div>
                    <div style='font-size:19px;font-weight:800;color:#1f2937;margin-bottom:2px'>" . $precio_formateado . " <span style='font-size:12px;color:#6b7280'>/mes</span></div>
                    <div style='font-size:12px;color:" . ($rec['precio_clp'] <= $pct7 ? '#16a34a' : '#dc2626') . "'>" . $dentro_7pct . "</div>
                    <div style='font-size:12px;color:#6b7280;margin-top:4px'>" . htmlspecialchars(implode(', ', array_slice($rec['razones'] ?? [], 0, 2))) . "</div>
                </td>
            </tr>";
        }

        $email_body = "
        <!DOCTYPE html><html><body style='font-family:-apple-system,BlinkMacSystemFont,sans-serif;background:#f3f4f6;padding:0;margin:0'>
        <div style='max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,0.08)'>
            <div style='background:linear-gradient(135deg,#1e40af,#3b82f6);padding:24px;text-align:center'>
                <h1 style='color:#fff;font-size:22px;margin:0 0 4px'>Tu Cotización de Isapre</h1>
                <p style='color:#bfdbfe;font-size:13px;margin:0'>Plan Salud Fácil · Válida por 7 días</p>
            </div>
            <div style='padding:24px'>
                <p style='font-size:15px;color:#374151;margin:0 0 8px'>Hola <strong>" . $nombre . "</strong>,</p>
                <p style='font-size:14px;color:#6b7280;margin:0 0 20px'>Estos son los mejores planes para tu perfil (edad: " . intval($lead['edad']) . " años, renta: $" . number_format($lead['renta'], 0, ',', '.') . ", 7% legal: $" . number_format($pct7, 0, ',', '.') . "):</p>
                <table style='width:100%;border-collapse:collapse;margin-bottom:20px'>" . $planes_html . "</table>
                <p style='font-size:13px;color:#6b7280;margin:0 0 16px'>Estos precios fueron verificados hoy. Las isapres pueden ajustar sus tarifas periódicamente.</p>
                <a href='https://plansaludfacil.cl' style='display:block;text-align:center;background:#2563eb;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;font-size:14px'>Ver comparador completo</a>
                <p style='font-size:12px;color:#9ca3af;text-align:center;margin-top:16px'>Plan Salud Fácil · Asesoría gratuita · Sin compromiso</p>
            </div>
        </div>
        </body></html>";

        try {
            require_once __DIR__ . '/../config.php';
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom('mailer@plansaludfacil.cl', 'Plan Salud Fácil');
            $mail->addAddress($recipient_email);
            $mail->isHTML(true);
            $mail->Subject = 'Tu cotización de Isapre - Plan Salud Fácil';
            $mail->Body    = $email_body;
            $mail->send();
            $email_sent = true;
        } catch (\Exception $e) {
            error_log("api/cotizar.php email error: " . $e->getMessage());
        }
    }
}

$result['email_sent'] = $email_sent;

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
