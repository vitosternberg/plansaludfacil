<?php
require_once 'conexion.php';

header('Content-Type: application/json');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener datos JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
    exit;
}

// Validar datos completos
if (empty($data['personal']) || empty($data['salud'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

// Validar RUT chileno
function validarRUT($rut) {
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    if (strlen($rut) < 2) return false;
    
    $dv = strtoupper(substr($rut, -1));
    $cuerpo = substr($rut, 0, -1);
    
    if (!ctype_digit($cuerpo)) return false;
    
    $suma = 0;
    $multiplo = 2;
    
    for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
        $suma += $multiplo * $cuerpo[$i];
        $multiplo = $multiplo == 7 ? 2 : $multiplo + 1;
    }
    
    $dvEsperado = 11 - ($suma % 11);
    $dvEsperado = $dvEsperado == 10 ? 'K' : ($dvEsperado == 11 ? '0' : $dvEsperado);
    
    return $dv == $dvEsperado;
}

if (!validarRUT($data['personal']['rut'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'RUT inválido']);
    exit;
}

// Validar edad (18-65)
if ($data['personal']['edad'] < 18 || $data['personal']['edad'] > 65) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Edad fuera del rango permitido']);
    exit;
}

// Insertar en base de datos
try {
    $conn->begin_transaction();

    // 1. Insertar datos personales
    $stmt = $conn->prepare("INSERT INTO cotizaciones (
        nombre, rut, email, region, telefono, genero, edad,
        cargas_familiares, prevision, renta_imponible, plan_libre_eleccion,
        fecha_creacion, ip_cliente
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $stmt->bind_param("ssssssisssss",
        $data['personal']['nombre'],
        $data['personal']['rut'],
        $data['personal']['email'],
        $data['personal']['region'],
        $data['personal']['telefono'],
        $data['personal']['genero'],
        $data['personal']['edad'],
        $data['salud']['cargas'],
        $data['salud']['prevision'],
        $data['salud']['renta'],
        $data['salud']['plan_libre'],
        $ip
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error al guardar cotización: " . $stmt->error);
    }
    
    $cotizacion_id = $conn->insert_id;
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'cotizacion_id' => $cotizacion_id,
        'message' => 'Cotización guardada correctamente'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>