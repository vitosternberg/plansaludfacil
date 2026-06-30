<?php
header('Content-Type: application/json');

require_once 'config.php';

try {
    // Validar datos recibidos
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['phone']) || strlen($data['phone']) !== 9 || !is_numeric($data['phone'])) {
        throw new Exception('Número de teléfono inválido');
    }
    
    if (empty($data['name'])) {
        throw new Exception('Nombre no puede estar vacío');
    }

    // Conexión a la base de datos
    $conn = connect_db_simple();
    if ($conn === null) {
        throw new Exception('Error de conexión a la base de datos');
    }

    // Insertar en la base de datos
    $date = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO whatsapp_contacts (phone, name, date_created) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception('Error preparando la consulta');
    }
    
    $stmt->bind_param("sss", $data['phone'], $data['name'], $date);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => 'Contacto guardado correctamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>