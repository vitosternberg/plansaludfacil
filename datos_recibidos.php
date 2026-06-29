<?php
// 1. CONFIGURACIÓN E INCLUSIONES
header('Content-Type: application/json');
require_once 'config.php'; // Carga las credenciales de la BD y SMTP

// Incluir manualmente los archivos de PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Usar las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 2. CONEXIÓN A LA BASE DE DATOS
$conn = connect_db_simple();

if ($conn === null) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo establecer conexión con la base de datos.']);
    exit();
}

// 3. OBTENER Y DECODIFICAR DATOS DEL FORMULARIO
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if ($data === null) {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibieron datos o el formato JSON es inválido.']);
    exit();
}

// 4. PREPARAR DATOS PARA LA INSERCIÓN
try {
    $nombre = $data['personal']['nombre'] ?? null;
    $correo = $data['personal']['email'] ?? null;
    $celular = $data['personal']['telefono'] ?? null;
    $datos_adicionales = json_encode($data, JSON_UNESCAPED_UNICODE);
    
    if ($datos_adicionales === false) {
        throw new Exception('Error al codificar los datos adicionales a JSON.');
    }
    $id_formulario_tipo = 1;
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos esenciales en la solicitud: ' . $e->getMessage()]);
    exit();
}

// 5. INSERTAR EN LA BASE DE DATOS
$sql = "INSERT INTO procesar_formularios (id_formulario_tipo, nombre, correo, celular, datos_adicionales) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al preparar la consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param("issss", $id_formulario_tipo, $nombre, $correo, $celular, $datos_adicionales);

if ($stmt->execute()) {
    $last_id = $conn->insert_id;

    // Llamar a ambas funciones de envío de correo
    enviarCorreoUsuario($data, $last_id); // Envía correo al cliente
    enviarCorreoAdmin($data, $last_id);   // Envía correo de notificación interna
    
    http_response_code(201);
    echo json_encode([
        'mensaje' => 'Datos guardados correctamente.',
        'cotizacion_id' => $last_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al guardar los datos: ' . $stmt->error]);
}

// 6. CERRAR RECURSOS
$stmt->close();
$conn->close();


// --- FUNCIONES DE CORREO ---

/**
 * Envía el correo de confirmación al usuario que llenó el formulario.
 */
function enviarCorreoUsuario($formData, $cotizacionId) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Destinatario: el usuario
        $mail->setFrom(SMTP_USER, 'PlanSaludFacil.cl');
        $mail->addAddress($formData['personal']['email'], $formData['personal']['nombre']);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Hemos recibido tu cotización | ID: ' . $cotizacionId;
        
        $body  = "<h1>¡Hola, " . htmlspecialchars($formData['personal']['nombre']) . "!</h1>";
        $body .= "<p>Hemos recibido tus datos correctamente. Un ejecutivo te contactará a la brevedad.</p>";
        $body .= "<p><strong>Número de tu solicitud:</strong> " . $cotizacionId . "</p>";
        $body .= "<h2>Resumen de tus datos:</h2>";
        $body .= "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        $body .= "<tr><td colspan='2' style='background-color: #f2f2f2;'><strong>Datos Personales</strong></td></tr>";
        $body .= "<tr><td><strong>Nombre:</strong></td><td>" . htmlspecialchars($formData['personal']['nombre']) . "</td></tr>";
        $body .= "<tr><td><strong>RUT:</strong></td><td>" . htmlspecialchars($formData['personal']['rut']) . "</td></tr>";
        $body .= "<tr><td><strong>Email:</strong></td><td>" . htmlspecialchars($formData['personal']['email']) . "</td></tr>";
        $body .= "<tr><td><strong>Teléfono:</strong></td><td>" . htmlspecialchars($formData['personal']['telefono']) . "</td></tr>";
        $body .= "<tr><td><strong>Región:</strong></td><td>" . htmlspecialchars(ucfirst($formData['personal']['region'])) . "</td></tr>";
        $body .= "<tr><td><strong>Edad:</strong></td><td>" . htmlspecialchars($formData['personal']['edad']) . "</td></tr>";
        $body .= "<tr><td><strong>Género:</strong></td><td>" . htmlspecialchars(ucfirst($formData['personal']['genero'])) . "</td></tr>";
        $body .= "<tr><td colspan='2' style='background-color: #f2f2f2;'><strong>Datos de Salud</strong></td></tr>";
        $body .= "<tr><td><strong>Cargas Familiares:</strong></td><td>" . htmlspecialchars($formData['salud']['cargas']) . "</td></tr>";
        $body .= "<tr><td><strong>Previsión Actual:</strong></td><td>" . htmlspecialchars($formData['salud']['prevision']) . "</td></tr>";
        $body .= "<tr><td><strong>Renta Imponible:</strong></td><td>$" . number_format($formData['salud']['renta'], 0, ',', '.') . "</td></tr>";
        $body .= "<tr><td><strong>Tipo de Plan:</strong></td><td>" . htmlspecialchars(ucfirst($formData['salud']['tipo_plan'])) . "</td></tr>";
        $body .= "</table>";
        $body .= "<p>Gracias por preferirnos.</p>";
        
        $mail->Body = $body;
        $mail->AltBody = 'Hemos recibido tus datos correctamente. Tu ID de solicitud es: ' . $cotizacionId;

        $mail->send();
    } catch (Exception $e) {
        error_log("Correo al USUARIO no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}");
    }
}

/**
 * Envía un correo de notificación al administrador.
 */
function enviarCorreoAdmin($formData, $cotizacionId) {
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Destinatarios: correos internos
        $mail->setFrom(SMTP_USER, 'Notificaciones Web');
        $mail->addAddress('mailer@plansaludfacil.cl', 'Administrador'); // Destinatario principal
        $mail->addCC('primage@gmail.com');                              // Con copia a

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Nueva Cotización Recibida - ID: ' . $cotizacionId;

        $body  = "<h1>Nueva Cotización Recibida</h1>";
        $body .= "<p>Se ha recibido un nuevo formulario en el sitio web con el ID: <strong>" . $cotizacionId . "</strong>.</p>";
        $body .= "<h2>Datos del Cliente:</h2>";
        $body .= "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        $body .= "<tr><td colspan='2' style='background-color: #f2f2f2;'><strong>Datos Personales</strong></td></tr>";
        $body .= "<tr><td><strong>Nombre:</strong></td><td>" . htmlspecialchars($formData['personal']['nombre']) . "</td></tr>";
        $body .= "<tr><td><strong>RUT:</strong></td><td>" . htmlspecialchars($formData['personal']['rut']) . "</td></tr>";
        $body .= "<tr><td><strong>Email:</strong></td><td>" . htmlspecialchars($formData['personal']['email']) . "</td></tr>";
        $body .= "<tr><td><strong>Teléfono:</strong></td><td>" . htmlspecialchars($formData['personal']['telefono']) . "</td></tr>";
        $body .= "<tr><td><strong>Región:</strong></td><td>" . htmlspecialchars(ucfirst($formData['personal']['region'])) . "</td></tr>";
        $body .= "<tr><td><strong>Edad:</strong></td><td>" . htmlspecialchars($formData['personal']['edad']) . "</td></tr>";
        $body .= "<tr><td><strong>Género:</strong></td><td>" . htmlspecialchars(ucfirst($formData['personal']['genero'])) . "</td></tr>";
        $body .= "<tr><td colspan='2' style='background-color: #f2f2f2;'><strong>Datos de Salud</strong></td></tr>";
        $body .= "<tr><td><strong>Cargas Familiares:</strong></td><td>" . htmlspecialchars($formData['salud']['cargas']) . "</td></tr>";
        $body .= "<tr><td><strong>Previsión Actual:</strong></td><td>" . htmlspecialchars($formData['salud']['prevision']) . "</td></tr>";
        $body .= "<tr><td><strong>Renta Imponible:</strong></td><td>$" . number_format($formData['salud']['renta'], 0, ',', '.') . "</td></tr>";
        $body .= "<tr><td><strong>Tipo de Plan:</strong></td><td>" . htmlspecialchars(ucfirst($formData['salud']['tipo_plan'])) . "</td></tr>";
        $body .= "</table>";

        $mail->Body = $body;
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Correo al ADMIN no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}");
    }
}