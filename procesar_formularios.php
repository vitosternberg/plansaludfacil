<?php
/**
 * procesar_contacto.php
 * Procesa el envío del formulario de contacto.
 * Guarda el mensaje en la BD y envía un email al administrador.
 * Ubicación: tu_proyecto_raiz/procesar_contacto.php
 *
 * [VERSION CONTROL] - Última Versión: 2025-07-06
 * - Creado desde cero para procesar el formulario de contacto.
 * - Ahora incluye `config.php` de la raíz del proyecto para las credenciales de la BD.
 * - Inserta los datos en la tabla `ContactMessages`.
 * - Integra PHPMailer para el envío de correos electrónicos.
 * - Obtiene el email del administrador desde la tabla `Settings` (usando la conexión de `config.php`).
 */

// ¡IMPORTANTE!
// Habilita la visualización de errores para depuración. Desactívalo en producción.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establece las cabeceras para la respuesta JSON y el control de acceso.
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Permite solicitudes desde cualquier origen (ajustar en producción)
header("Access-Control-Allow-Methods: POST"); // Permite solo el método POST

// Incluye el archivo de configuración de la base de datos desde la raíz del proyecto.
// Asume que 'config.php' está en el mismo directorio que este archivo 'procesar_contacto.php'.
require_once 'config.php'; 

// Incluye las librerías PHPMailer.
// Asume que la carpeta 'PHPMailer/' está en la raíz de tu proyecto,
// al mismo nivel que este archivo 'procesar_contacto.php'.
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Usa los namespaces de PHPMailer.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Obtiene configuraciones específicas del blog desde la tabla 'Settings'.
 * Esta función se usa aquí para obtener el email del administrador para el envío de correos.
 * Reutiliza la función de conexión `connect_db_simple()` que viene de `config.php`.
 *
 * @return array Un array asociativo de clave => valor de configuración.
 */
function get_all_settings_for_mail_processor() { 
    $conn = connect_db_simple(); 
    $settings = [];
    if ($conn === null) {
        error_log("No se pudo conectar a la DB para obtener settings en procesar_contacto.php.");
        return $settings;
    }

    try {
        $query = "SELECT setting_key, setting_value FROM Settings";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            $result->free();
        } else {
            error_log("Error al obtener configuraciones para el correo en procesar_contacto.php: " . $conn->error);
        }
    } catch (\Throwable $e) {
        error_log("Excepción al obtener configuraciones para el correo en procesar_contacto.php: " . $e->getMessage());
    } finally {
        if ($conn) {
            $conn->close();
        }
    }
    return $settings;
}

// Solo procesar la solicitud si es de tipo POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // HONEYPOT ANTISPAM CHECK
    if (!empty($_POST['url_website'])) {
        // Es un bot, porque un humano no ve este campo.
        // Fingimos éxito para engañar al bot, pero no procesamos nada.
        echo json_encode(['success' => true, 'message' => 'Solicitud recibida (filtrado por antispam)']);
        exit();
    }

    // Recoge y limpia los datos enviados por el formulario.
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $query_type = $_POST['query_type'] ?? '';
    $message = trim($_POST['message'] ?? '');

    // Validaciones del lado del servidor.
    // Aunque el frontend ya valida, estas son cruciales por seguridad.
    if (empty($name) || empty($email) || empty($query_type) || empty($message)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Todos los campos obligatorios deben ser rellenados.']);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'El formato del email no es válido.']);
        exit();
    }
    if (!empty($phone) && !preg_match('/^[0-9]{9}$/', $phone)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'El formato del teléfono no es válido (9 dígitos numéricos).']);
        exit();
    }

    // Intenta conectar a la base de datos.
    $conn = connect_db_simple(); 
    if ($conn === null) {
        if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
            // MOCK PARA LOCALHOST CUANDO NO HAY DB
            echo json_encode([
                "success" => true,
                "message" => "¡Mensaje enviado con éxito! (Simulado en local)",
                "message_id" => 999
            ]);
            exit();
        }
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos.']);
        exit();
    }

    try {
        // Empaquetar query_type, message y otros campos extras en datos_adicionales como JSON
        $extra_data = ['query_type' => $query_type, 'message' => $message];
        foreach ($_POST as $key => $value) {
            if (!in_array($key, ['name', 'email', 'phone', 'query_type', 'message', 'url_website'])) {
                $extra_data[$key] = $value;
            }
        }
        $datos_adicionales = json_encode($extra_data, JSON_UNESCAPED_UNICODE);
        $tipo_plan = $_POST['tipo_plan'] ?? '';
        $is_contacto_empresa = $tipo_plan === 'contacto_empresa';

        if ($is_contacto_empresa) {
            $sql = "INSERT INTO contacto_empresa_mensajes (nombre, correo, celular, tipo_consulta, mensaje, datos_adicionales, url_origen, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                error_log("Error al preparar la consulta SQL para contacto_empresa_mensajes: " . $conn->error);
                throw new Exception("Error interno del servidor al guardar el mensaje.");
            }

            $url_origen = $_SERVER['HTTP_REFERER'] ?? null;
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $stmt->bind_param("sssssssss", $name, $email, $phone, $query_type, $message, $datos_adicionales, $url_origen, $ip_address, $user_agent);
        } else {
            // Compatibilidad con los formularios antiguos del sitio.
            $sql = "INSERT INTO procesar_formularios (id_formulario_tipo, nombre, correo, celular, datos_adicionales) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                error_log("Error al preparar la consulta SQL para procesar_formularios: " . $conn->error);
                throw new Exception("Error interno del servidor al guardar el mensaje.");
            }

            $id_formulario_tipo = 1;
            $stmt->bind_param("issss", $id_formulario_tipo, $name, $email, $phone, $datos_adicionales);
        }
        
        // Ejecuta la consulta de inserción.
        if ($stmt->execute()) {
            $message_id = $conn->insert_id; // Obtiene el ID del mensaje recién insertado.
            
            // Envía la respuesta JSON de éxito al frontend INMEDIATAMENTE.
            // Esto evita que el usuario espere si el envío de correo es lento o falla.
            echo json_encode([
                "success" => true,
                "message" => "¡Mensaje enviado con éxito! Nos pondremos en contacto contigo pronto.",
                "message_id" => $message_id
            ]);
            
            // --- Inicia el proceso de envío de correo electrónico con PHPMailer ---
            $mail = new PHPMailer(true);
            try {
                $blog_settings = get_all_settings_for_mail_processor(); 
                $admin_email = $blog_settings['admin_email'] ?? 'admin@miblog.com'; // Fallback si no está configurado.

                // Configuración del servidor SMTP.
                
                $mail->isSMTP();
                $mail->Host       = 'mail.plansaludfacil.cl';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'mailer@plansaludfacil.cl';
                $mail->Password   = 'Mailer.2025';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // O PHPMailer::ENCRYPTION_STARTTLS para puerto 587
                $mail->Port       = 465; // 465 para SMTPS, 587 para STARTTLS

                // Configura el remitente y los destinatarios.
                $mail->setFrom('mailer@plansaludfacil.cl', 'Formulario de Contacto');
                $mail->addAddress($admin_email, 'Administrador'); 
                $mail->addAddress('primagen@gmail.com'); 
                $mail->addReplyTo($email, $name); 

                // Contenido del correo.
                $mail->isHTML(true); 
                $mail->Subject = 'Nuevo Mensaje de Contacto: ' . htmlspecialchars($query_type);
                $mail->Body    = "
                    <html><body>
                    <h2>Nuevo Mensaje de Contacto Recibido</h2>
                    <p>Has recibido un nuevo mensaje a través del formulario de contacto.</p>
                    <ul>
                        <li><strong>ID del Mensaje:</strong> {$message_id}</li>
                        <li><strong>Nombre:</strong> " . htmlspecialchars($name) . "</li>
                        <li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>
                        <li><strong>Teléfono:</strong> " . (empty($phone) ? 'N/A' : htmlspecialchars($phone)) . "</li>
                        <li><strong>Tipo de Consulta:</strong> " . htmlspecialchars($query_type) . "</li>
                        <li><strong>Mensaje:</strong><br>" . nl2br(htmlspecialchars($message)) . "</li>
                        <li><strong>Fecha/Hora:</strong> " . date('Y-m-d H:i:s') . "</li>
                    </ul>
                    </body></html>
                ";
                $mail->AltBody = "Nuevo mensaje de contacto:\n\n"
                               . "ID: {$message_id}\n"
                               . "Nombre: {$name}\n"
                               . "Email: {$email}\n"
                               . "Teléfono: " . (empty($phone) ? 'N/A' : $phone) . "\n"
                               . "Tipo: {$query_type}\n"
                               . "Mensaje:\n{$message}\n\n"
                               . "Fecha/Hora: " . date('Y-m-d H:i:s') . "\n";

                $mail->send();
                error_log("Correo de contacto enviado para mensaje ID: {$message_id}");

            } catch (Exception $e) {
                error_log("Error al enviar correo de contacto para ID {$message_id}: {$mail->ErrorInfo}");
            }

        } else {
            http_response_code(500); 
            echo json_encode(["success" => false, "message" => "Error al guardar el mensaje en la base de datos."]);
        }
        $stmt->close(); 

    } catch (\Throwable $e) {
        error_log("Excepción general en procesar_contacto.php: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error interno del servidor."]);
    } finally {
        if ($conn) {
            $conn->close();
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
}
?>
