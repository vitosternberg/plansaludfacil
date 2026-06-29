<?php
// update_first_contact_condition.php

// Habilitar la visualización de errores en el navegador
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'plansalu_blog';
$username = 'plansalu_blogger';
$password = 'Blog.2025!#';

try {
    // Conexión a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión exitosa a la base de datos. hola mundo a todos <br>";

    // Consulta para actualizar el campo first_contact_date con la fecha actual
    // para registros con estado "Nuevo", first_contact_date = NULL y fecha_creacion > 3 días
    $sql_update = "UPDATE procesar_formularios 
                   SET first_contact_date = NOW() 
                   WHERE estado = 'Nuevo' 
                   AND first_contact_date IS NULL 
                   AND fecha_creacion <= NOW() - INTERVAL 3 DAY";
    $stmt_update = $pdo->prepare($sql_update);

    // Ejecutar el UPDATE
    $stmt_update->execute();

    // Verificar si el UPDATE afectó alguna fila
    $filas_afectadas = $stmt_update->rowCount();
    if ($filas_afectadas > 0) {
        echo "El campo first_contact_date fue actualizado correctamente para $filas_afectadas registros.<br>";
    } else {
        echo "No se actualizó ningún registro. Verifica si hay registros que cumplan las condiciones.<br>";
    }
} catch (PDOException $e) {
    echo "Error al conectar o consultar la base de datos: " . $e->getMessage();
}
?>