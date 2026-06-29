<?php
/**
 * =======================================================================
 * CONECTOR CIEGO OMNIFLOW (Proxy SQL Seguro)
 * =======================================================================
 * Este archivo actúa como un simple ejecutor de consultas SQL.
 * NO CONTIENE LÓGICA DE NEGOCIO. Recibe consultas desde Heroku,
 * las ejecuta en la base de datos local y devuelve los resultados.
 */

// 1. CARGAR CONFIGURACIÓN LOCAL
@require_once __DIR__ . '/omniflow_config.php';

// Validar que exista la configuración (al menos la key de API)
if (!defined('API_SECRET_KEY')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se encontró la configuración del conector']);
    exit;
}

$api_key_valida = API_SECRET_KEY;

// 2. RECIBIR PAYLOAD JSON SI EXISTE
$json_input = file_get_contents('php://input');
$json_data = json_decode($json_input, true);
if (is_array($json_data)) {
    $_POST = array_merge($_POST, $json_data);
}

// 3. VALIDAR SEGURIDAD (API KEY)
$headers = function_exists('getallheaders') ? getallheaders() : [];
$api_key_recibida = $_SERVER['HTTP_X_API_KEY'] ?? ($headers['X-API-Key'] ?? ($headers['X-Api-Key'] ?? ($_POST['api_key'] ?? '')));

if (empty($api_key_valida) || $api_key_recibida !== $api_key_valida) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

// 3. RECIBIR PAYLOAD
$accion = $_POST['accion'] ?? '';
if ($accion !== 'execute_sql' && $accion !== 'send_email') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    exit;
}

// --- ACCIÓN: ENVIAR CORREO (USANDO CONFIG LOCAL) ---
if ($accion === 'send_email') {
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    
    if (empty($to) || empty($subject) || empty($body)) {
        echo json_encode(['success' => false, 'error' => 'Faltan parámetros de email']);
        exit;
    }
    
    // Intentar cargar PHPMailer local
    $phpmailer_paths = [
        __DIR__ . '/PHPMailer/src/Exception.php',
        dirname(__DIR__) . '/vendor/phpmailer/phpmailer/src/Exception.php',
        '/app/cliente/PHPMailer/src/Exception.php',
        '/app/vendor/phpmailer/phpmailer/src/Exception.php'
    ];
    
    $phpmailer_src = null;
    foreach ($phpmailer_paths as $path) {
        if (file_exists($path)) {
            $phpmailer_src = dirname($path);
            require_once $phpmailer_src . '/Exception.php';
            require_once $phpmailer_src . '/PHPMailer.php';
            require_once $phpmailer_src . '/SMTP.php';
            break;
        }
    }
    
    if (!$phpmailer_src && file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }
    
    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
        echo json_encode(['success' => false, 'error' => 'PHPMailer no encontrado en el servidor cliente']);
        exit;
    }
    
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = defined('SMTP_HOST') ? SMTP_HOST : '';
        $mail->SMTPAuth   = true;
        $mail->Username   = defined('SMTP_USER') ? SMTP_USER : '';
        $mail->Password   = defined('SMTP_PASS') ? SMTP_PASS : '';
        $mail->SMTPSecure = defined('SMTP_SECURE') ? SMTP_SECURE : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = defined('SMTP_PORT') ? SMTP_PORT : 465;
        $mail->CharSet    = 'UTF-8';
        
        $mail->setFrom(defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : $mail->Username, defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Notificación');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        echo json_encode(['success' => true]);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error al enviar correo: ' . $mail->ErrorInfo]);
    }
    exit;
}
// --- FIN ACCIÓN: ENVIAR CORREO ---

$query_action = $_POST['query_action'] ?? ''; // 'query' o 'prepare'

// --- DECODIFICACION Y DESENCRIPTACION DE SQL ---
$sql_payload = $_POST['sql'] ?? '';
$is_encrypted = isset($_POST['encrypted']) && $_POST['encrypted'] == true;

if ($is_encrypted) {
    $raw_data = base64_decode($sql_payload);
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($raw_data, 0, $iv_length);
    $encrypted_sql = substr($raw_data, $iv_length);
    $key = hash('sha256', $api_key_valida, true);
    
    $sql = openssl_decrypt($encrypted_sql, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
} else {
    $sql = base64_decode($sql_payload);
}
// --- FIN DECODIFICACION ---
$params_json = $_POST['params'] ?? '[]';
$types = $_POST['types'] ?? '';

$params = json_decode($params_json, true);
if (!is_array($params)) $params = [];

if (empty($sql)) {
    echo json_encode(['success' => false, 'error' => 'SQL vacío']);
    exit;
}

// 4. CONECTAR A LA BASE DE DATOS
$db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
$db_user = defined('DB_USER') ? DB_USER : '';
$db_pass = defined('DB_PASS') ? DB_PASS : '';
$db_name = defined('DB_NAME') ? DB_NAME : '';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($db->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión DB: ' . $db->connect_error]);
    exit;
}
$db->set_charset("utf8mb4");

// 5. EJECUTAR CONSULTA
$response = ['success' => true];

try {
    if ($query_action === 'query') {
        $result = $db->query($sql);
        if ($result === false) {
            throw new Exception($db->error);
        }
        
        if (is_bool($result)) {
            $response['is_select'] = false;
            $response['affected_rows'] = $db->affected_rows;
            $response['insert_id'] = $db->insert_id;
        } else {
            $response['is_select'] = true;
            $response['rows'] = $result->fetch_all(MYSQLI_ASSOC);
            $response['num_rows'] = $result->num_rows;
        }
    } else if ($query_action === 'prepare') {
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception($db->error);
        }
        
        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        
        $result = $stmt->get_result();
        $response['affected_rows'] = $stmt->affected_rows;
        $response['insert_id'] = $stmt->insert_id;
        
        if ($result !== false) {
            $response['is_select'] = true;
            $response['rows'] = $result->fetch_all(MYSQLI_ASSOC);
            $response['num_rows'] = $result->num_rows;
        } else {
            $response['is_select'] = false;
        }
        $stmt->close();
    } else {
        throw new Exception("Acción de query desconocida");
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

$db->close();
echo json_encode($response);
exit;
