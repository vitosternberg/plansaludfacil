<?php
$DB_HOST = 'localhost';
$DB_USER = 'plansalu_blogger';
$DB_PASS = 'Blog.2025!?#';
$DB_NAME = 'plansalu_blog';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Error conexion: ' . $mysqli->connect_error);
}

$res = $mysqli->query("SELECT id, nombre, correo, celular, datos_adicionales FROM procesar_formularios WHERE id = 150");
if ($row = $res->fetch_assoc()) {
    echo "<h2>ID 150 - Raw Data</h2>";
    echo "<pre>";
    echo "nombre (columna): [" . $row['nombre'] . "]\n";
    echo "correo: " . $row['correo'] . "\n";
    echo "celular: " . $row['celular'] . "\n";
    echo "\n--- datos_adicionales (JSON) ---\n";
    $json = json_decode($row['datos_adicionales'], true);
    if ($json) {
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo "(vacio o no valido): " . var_export($row['datos_adicionales'], true);
    }
    echo "</pre>";
} else {
    echo "ID 150 no encontrado";
}

// Also check what the flat/nested structure looks like
echo "<h2>Ultimos 5 registros con datos_adicionales</h2>";
$res = $mysqli->query("SELECT id, nombre, correo, datos_adicionales FROM procesar_formularios WHERE datos_adicionales IS NOT NULL AND datos_adicionales != '' AND datos_adicionales != '{}' ORDER BY id DESC LIMIT 5");
while ($row = $res->fetch_assoc()) {
    echo "<h3>ID {$row['id']} - {$row['nombre']}</h3>";
    $json = json_decode($row['datos_adicionales'], true);
    $keys = $json ? array_keys($json) : [];
    echo "<pre>Top-level keys: " . implode(', ', $keys) . "\n";
    echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "</pre><hr>";
}

$mysqli->close();
