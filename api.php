<?php
/**
 * PROXY MÍNIMO DEL CLIENTE
 * Solo ejecuta queries enviadas desde Heroku y retorna resultados
 * NO contiene lógica de negocio
 */

header('Content-Type: application/json');

// Cargar configuración local (formato PHP)
if (file_exists(__DIR__ . '/omniflow_config.php')) {
    require_once __DIR__ . '/omniflow_config.php';
    // Verifica que las constantes estén definidas
    if (!defined('API_SECRET_KEY') || !defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASS') || !defined('DB_NAME')) {
        die(json_encode(['error' => 'Configuración inválida: faltan constantes']));
    }
} else {
    die(json_encode(['error' => 'Archivo de configuración no encontrado']));
}

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Método no permitido']));
}

// Validar API Key
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';
if (empty($api_key) || $api_key !== API_SECRET_KEY) {
    http_response_code(401);
    die(json_encode(['error' => 'API Key inválida']));
}

// Leer parámetros
$query_type = $_POST['query_type'] ?? ''; // 'select', 'insert', 'update', 'delete'
$sql = $_POST['sql'] ?? '';
$params = json_decode($_POST['params'] ?? '[]', true);

if (empty($query_type) || empty($sql)) {
    http_response_code(400);
    die(json_encode(['error' => 'Parámetros incompletos']));
}

// Conectar a BD local
$db = new mysqli(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    defined('DB_PORT') ? DB_PORT : 3306
);

if ($db->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Error de conexión BD: ' . $db->connect_error]));
}

$db->set_charset('utf8mb4');

try {
    // Preparar statement
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        throw new Exception('Error preparando query: ' . $db->error);
    }

    // Bind params si existen
    if (!empty($params)) {
        $types = '';
        $bind_params = [];

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $bind_params[] = $param;
        }

        $stmt->bind_param($types, ...$bind_params);
    }

    // Ejecutar
    $stmt->execute();

    // Procesar resultado según tipo
    $response = ['success' => true];

    switch ($query_type) {
        case 'select':
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $response['data'] = $rows;
            break;

        case 'insert':
            $response['insert_id'] = $stmt->insert_id;
            $response['affected_rows'] = $stmt->affected_rows;
            break;

        case 'update':
        case 'delete':
            $response['affected_rows'] = $stmt->affected_rows;
            break;
    }

    $stmt->close();
    $db->close();

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>