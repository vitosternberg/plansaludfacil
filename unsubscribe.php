<?php
/**
 * =======================================================================
 * SCRIPT DE UNSUBSCRIBE (DARSE DE BAJA)
 * =======================================================================
 * Este archivo maneja las solicitudes de baja de los correos.
 * Valida la solicitud y actualiza la base de datos del cliente.
 */

// 1. Cargar la configuración para acceder a las credenciales de la BD y la clave secreta.
if (file_exists(__DIR__ . '/omniflow_config.php')) {
    require_once __DIR__ . '/omniflow_config.php';
} else {
    // Si la configuración no existe, no podemos continuar.
    die("Error de configuración del servidor.");
}

// 2. Obtener los datos de la URL.
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

$message = 'Error: La solicitud es inválida o el enlace ha expirado.';
$is_success = false;

// 3. Validar el token para seguridad.
if (!empty($email) && !empty($token)) {
    // Se recalcula el token esperado de la misma forma que se generó.
    $expected_token = hash('sha256', $email . API_SECRET_KEY);

    // Se comparan los tokens de forma segura para evitar ataques de temporización.
    if (hash_equals($expected_token, $token)) {
        try {
            // Conexión a la base de datos del cliente.
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                throw new Exception("Error interno al conectar con la base de datos.");
            }
            $conn->set_charset("utf8mb4");

            // Preparar y ejecutar la actualización.
            $stmt = $conn->prepare("UPDATE procesar_formularios SET unsubscribed = 1 WHERE correo = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $message = 'Has sido dado de baja correctamente. No recibirás más comunicaciones.';
                $is_success = true;
            } else {
                $message = 'Tu correo no se encontró en nuestra lista o ya habías sido dado de baja anteriormente.';
                $is_success = true; // Se considera un éxito para el usuario.
            }
            $conn->close();

        } catch (Exception $e) {
            // Mensaje de error genérico para el usuario.
            $message = 'Ocurrió un error al procesar tu solicitud. Por favor, contacta al soporte.';
            // Opcional: registrar el error real para el administrador.
            // error_log('Error en unsubscribe.php: ' . $e->getMessage());
        }
    }
}

// 4. Mostrar una página de confirmación al usuario.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darse de Baja</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background-color: #f4f4f9; color: #333; }
        .container { text-align: center; padding: 40px; background-color: white; border-radius: 12px; box-shadow: 0 6px 12px rgba(0,0,0,0.1); max-width: 90%; width: 500px; }
        h1 { margin-top: 0; }
        p { color: #666; font-size: 1.1em; line-height: 1.6; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="<?php echo $is_success ? 'success' : 'error'; ?>">
            <?php echo $is_success ? 'Solicitud Procesada' : 'Error en la Solicitud'; ?>
        </h1>
        <p><?php echo htmlspecialchars($message); ?></p>
    </div>
</body>
</html>
