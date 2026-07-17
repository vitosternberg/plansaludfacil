<?php
/**
 * guardar_nota.php
 * Endpoint para Omniflow/Omnilama: guarda una nota en el detalle del lead.
 * Recibe lead_id y nota, actualiza la tabla procesar_formularios.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit();
}

// Cargar configuración
require_once __DIR__ . '/omniflow_config.php';

// Validar API Key
$headers = function_exists('getallheaders') ? getallheaders() : [];
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? ($headers['X-API-Key'] ?? ($headers['X-Api-Key'] ?? ($_POST['api_key'] ?? '')));

if (empty($api_key) || $api_key !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

// Obtener datos
$lead_id = intval($_POST['lead_id'] ?? $_POST['id'] ?? 0);
$nota = trim($_POST['nota'] ?? $_POST['note'] ?? $_POST['notas'] ?? '');

if ($lead_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de lead inválido']);
    exit();
}

// Conectar a BD
$db_host = defined('DB_HOST') ? DB_HOST : 'localhost';
$db_user = defined('DB_USER') ? DB_USER : '';
$db_pass = defined('DB_PASS') ? DB_PASS : '';
$db_name = defined('DB_NAME') ? DB_NAME : '';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($db->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit();
}
$db->set_charset("utf8mb4");

// Actualizar nota
$stmt = $db->prepare("UPDATE procesar_formularios SET notas = ? WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta']);
    $db->close();
    exit();
}

$stmt->bind_param("si", $nota, $lead_id);
$success = $stmt->execute();

if ($success) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Nota guardada correctamente', 'lead_id' => $lead_id]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Nota guardada (sin cambios)', 'lead_id' => $lead_id]);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar la nota: ' . $stmt->error]);
}

$stmt->close();
$db->close();
