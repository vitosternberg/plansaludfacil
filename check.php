<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla, solo JSON
header('Content-Type: application/json; charset=utf-8');

$resultado = array(
    'servidor' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'N/A',
    'directorio' => __DIR__,
    'timestamp' => date('Y-m-d H:i:s'),
    'archivos' => array()
);

// 1. Verificar omniflow_connector.php
$connector_path = __DIR__ . '/omniflow_connector.php';
if (file_exists($connector_path)) {
    $resultado['archivos']['omniflow_connector.php'] = array(
        'existe' => true,
        'tamano' => filesize($connector_path),
        'md5' => md5_file($connector_path),
        'modificado' => date('Y-m-d H:i:s', filemtime($connector_path)),
        'permisos' => substr(sprintf('%o', fileperms($connector_path)), -4)
    );
    
    // Contar líneas de forma segura
    $handle = fopen($connector_path, 'r');
    $lineas = 0;
    if ($handle) {
        while (!feof($handle)) {
            fgets($handle);
            $lineas++;
        }
        fclose($handle);
        $resultado['archivos']['omniflow_connector.php']['lineas'] = $lineas;
    }
    
    // Verificar versión
    $contenido = file_get_contents($connector_path, false, null, 0, 500);
    if (strpos($contenido, 'VERSIÓN FINAL REFACTORIZADA') !== false) {
        $resultado['archivos']['omniflow_connector.php']['version'] = 'FINAL REFACTORIZADA';
    } else {
        $resultado['archivos']['omniflow_connector.php']['version'] = 'DESCONOCIDA';
    }
} else {
    $resultado['archivos']['omniflow_connector.php'] = array('existe' => false);
}

// 2. Verificar omniflow_config.php
$config_path = __DIR__ . '/omniflow_config.php';
if (file_exists($config_path)) {
    $resultado['archivos']['omniflow_config.php'] = array(
        'existe' => true,
        'tamano' => filesize($config_path),
        'permisos' => substr(sprintf('%o', fileperms($config_path)), -4)
    );
} else {
    $resultado['archivos']['omniflow_config.php'] = array('existe' => false);
}

// 3. Verificar api.php
$api_path = __DIR__ . '/api.php';
if (file_exists($api_path)) {
    $resultado['archivos']['api.php'] = array(
        'existe' => true,
        'tamano' => filesize($api_path),
        'permisos' => substr(sprintf('%o', fileperms($api_path)), -4)
    );
} else {
    $resultado['archivos']['api.php'] = array('existe' => false);
}

// 4. Valores esperados
$resultado['valores_esperados'] = array(
    'omniflow_connector_md5' => 'c04f086655cd5dfbc1c5998d6176e894',
    'omniflow_connector_lineas' => 1743
);

// 5. Test de conexión MySQL
$resultado['mysql'] = array('intentado' => false);
if (file_exists($config_path)) {
    $config_content = file_get_contents($config_path);
    $config = json_decode($config_content, true);
    
    if ($config && isset($config['db_host'])) {
        $resultado['mysql']['intentado'] = true;
        $mysqli = new mysqli(
            $config['db_host'],
            $config['db_user'],
            $config['db_pass'],
            $config['db_name'],
            isset($config['db_port']) ? $config['db_port'] : 3306
        );
        
        if ($mysqli->connect_error) {
            $resultado['mysql']['conexion'] = false;
            $resultado['mysql']['error'] = $mysqli->connect_error;
        } else {
            $resultado['mysql']['conexion'] = true;
            $resultado['mysql']['version'] = $mysqli->server_info;
            
            $query = "SELECT COUNT(*) as total FROM procesar_formularios";
            $result = $mysqli->query($query);
            if ($result) {
                $row = $result->fetch_assoc();
                $resultado['mysql']['leads_count'] = intval($row['total']);
            }
            
            $mysqli->close();
        }
    }
}

// 6. Estado general
$md5_match = false;
if (isset($resultado['archivos']['omniflow_connector.php']['md5'])) {
    $md5_match = ($resultado['archivos']['omniflow_connector.php']['md5'] === $resultado['valores_esperados']['omniflow_connector_md5']);
}

if (!isset($resultado['archivos']['omniflow_connector.php']['existe']) || !$resultado['archivos']['omniflow_connector.php']['existe']) {
    $resultado['estado'] = 'ERROR: omniflow_connector.php no encontrado';
} elseif (!isset($resultado['archivos']['omniflow_config.php']['existe']) || !$resultado['archivos']['omniflow_config.php']['existe']) {
    $resultado['estado'] = 'ERROR: omniflow_config.php no encontrado';
} elseif (!$md5_match) {
    $resultado['estado'] = 'ADVERTENCIA: omniflow_connector.php difiere de version esperada';
} else {
    $resultado['estado'] = 'OK';
}

echo json_encode($resultado, JSON_PRETTY_PRINT);
?>
