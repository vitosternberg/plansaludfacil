<?php
header('Content-Type: application/json');

require_once 'mi-blog/php/config.php';


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
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertar en la base de datos
    $stmt = $conn->prepare("INSERT INTO whatsapp_contacts (phone, name, date_created) VALUES (:phone, :name, :date)");
    $stmt->bindParam(':phone', $data['phone']);
    $stmt->bindParam(':name', $data['name']);
    $stmt->bindParam(':date', $data['date']);
    $stmt->execute();

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