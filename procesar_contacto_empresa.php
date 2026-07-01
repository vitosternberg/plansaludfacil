<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'config.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function get_contacto_empresa_settings(): array
{
    $conn = connect_db_simple();
    $settings = [];

    if ($conn === null) {
        error_log('No se pudo conectar a la DB para obtener settings en procesar_contacto_empresa.php.');
        return $settings;
    }

    try {
        $result = $conn->query('SELECT setting_key, setting_value FROM Settings');
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            $result->free();
        } else {
            error_log('Error al obtener configuraciones en procesar_contacto_empresa.php: ' . $conn->error);
        }
    } catch (\Throwable $e) {
        error_log('Excepción al obtener configuraciones en procesar_contacto_empresa.php: ' . $e->getMessage());
    } finally {
        $conn->close();
    }

    return $settings;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    exit();
}

if (!empty($_POST['url_website'])) {
    echo json_encode(['success' => true, 'message' => 'Solicitud recibida (filtrado por antispam)']);
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$query_type = trim($_POST['query_type'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $query_type === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben ser rellenados.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El formato del email no es válido.']);
    exit();
}

if ($phone !== '' && !preg_match('/^[0-9]{9}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El formato del teléfono no es válido (9 dígitos numéricos).']);
    exit();
}

$conn = connect_db_simple();
if ($conn === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']);
    exit();
}

try {
    $extra_data = [];
    foreach ($_POST as $key => $value) {
        if (!in_array($key, ['name', 'email', 'phone', 'query_type', 'message', 'url_website'], true)) {
            $extra_data[$key] = $value;
        }
    }

    $datos_adicionales = empty($extra_data) ? null : json_encode($extra_data, JSON_UNESCAPED_UNICODE);
    $url_origen = $_SERVER['HTTP_REFERER'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $sql = 'INSERT INTO contacto_empresa_mensajes (nombre, correo, celular, tipo_consulta, mensaje, datos_adicionales, url_origen, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log('Error al preparar la consulta SQL para contacto_empresa_mensajes: ' . $conn->error);
        throw new \RuntimeException('Error interno del servidor al guardar el mensaje.');
    }

    $stmt->bind_param('sssssssss', $name, $email, $phone, $query_type, $message, $datos_adicionales, $url_origen, $ip_address, $user_agent);

    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error al guardar el mensaje en la base de datos.']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $message_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => '¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.',
        'message_id' => $message_id,
    ]);

    $stmt->close();

    $mail = new PHPMailer(true);
    try {
        $settings = get_contacto_empresa_settings();
        $admin_email = $settings['admin_email'] ?? 'contacto@plansaludfacil.cl';

        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USER, 'Formulario Empresa');
        $mail->addAddress($admin_email, 'Administrador');
        $mail->addAddress('primagen@gmail.com');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Nuevo Mensaje Empresa: ' . htmlspecialchars($query_type);
        $mail->Body = "
            <html><body>
            <h2>Nuevo Mensaje desde Nosotros / Empresa</h2>
            <ul>
                <li><strong>ID:</strong> {$message_id}</li>
                <li><strong>Nombre:</strong> " . htmlspecialchars($name) . "</li>
                <li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>
                <li><strong>Teléfono:</strong> " . ($phone === '' ? 'N/A' : htmlspecialchars($phone)) . "</li>
                <li><strong>Tipo de Consulta:</strong> " . htmlspecialchars($query_type) . "</li>
                <li><strong>Mensaje:</strong><br>" . nl2br(htmlspecialchars($message)) . "</li>
                <li><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</li>
            </ul>
            </body></html>
        ";
        $mail->AltBody = "Nuevo mensaje desde Nosotros / Empresa\n\n"
            . "ID: {$message_id}\n"
            . "Nombre: {$name}\n"
            . "Email: {$email}\n"
            . "Teléfono: " . ($phone === '' ? 'N/A' : $phone) . "\n"
            . "Tipo: {$query_type}\n"
            . "Mensaje:\n{$message}\n";

        $mail->send();
        error_log("Correo de empresa enviado para mensaje ID: {$message_id}");
    } catch (Exception $e) {
        error_log("Error al enviar correo de empresa para ID {$message_id}: {$mail->ErrorInfo}");
    }
} catch (\Throwable $e) {
    error_log('Excepción general en procesar_contacto_empresa.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor.']);
} finally {
    if ($conn) {
        $conn->close();
    }
}
