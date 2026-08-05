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
        if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
            echo json_encode([
                'success' => true,
                'message' => 'Contacto guardado correctamente (Simulado en local)'
            ]);
            exit();
        }
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
    $contactId = $conn->insert_id;
    $stmt->close();

    // CAMBIO 2026-08-05: Insertar tambien en procesar_formularios
    // para que los datos del modal WhatsApp aparezcan en el dashboard de leads
    $datos_adicionales = json_encode([
        'source' => 'whatsapp_modal',
        'whatsapp_contact_id' => $contactId,
        'date' => $date,
    ], JSON_UNESCAPED_UNICODE);

    $stmt2 = $conn->prepare("INSERT INTO procesar_formularios (id_formulario_tipo, nombre, correo, celular, datos_adicionales) VALUES (?, ?, ?, ?, ?)");
    if ($stmt2) {
        $id_formulario_tipo = 1;
        $correo = '';
        $stmt2->bind_param("issss", $id_formulario_tipo, $data['name'], $correo, $data['phone'], $datos_adicionales);
        $stmt2->execute();
        $stmt2->close();
    }

    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => 'Contacto guardado correctamente',
        'contact_id' => $contactId
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
