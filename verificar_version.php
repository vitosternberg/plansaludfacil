<?php
/**
 * Script para verificar qué versión del conector está en el servidor
 * Sube este archivo y accede a: https://plansaludfacil.cl/verificar_version.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== VERIFICACIÓN DE VERSIÓN DEL CONECTOR ===\n\n";

$connector_file = __DIR__ . '/omniflow_connector.php';

if (!file_exists($connector_file)) {
    die("❌ omniflow_connector.php NO ENCONTRADO\n");
}

echo "1. Información del archivo:\n";
echo "   Ubicación: {$connector_file}\n";
echo "   Tamaño: " . filesize($connector_file) . " bytes\n";
echo "   Última modificación: " . date('Y-m-d H:i:s', filemtime($connector_file)) . "\n\n";

echo "2. Mostrando líneas 40-50 (donde está el problema):\n";
echo "   " . str_repeat("-", 70) . "\n";

$lines = file($connector_file);
for ($i = 39; $i < 50 && $i < count($lines); $i++) {
    $line_num = $i + 1;
    echo sprintf("   %3d: %s", $line_num, $lines[$i]);
}
echo "   " . str_repeat("-", 70) . "\n\n";

echo "3. Verificando si tiene el sistema multi-path:\n";
$content = file_get_contents($connector_file);

if (strpos($content, '$phpmailer_paths = [') !== false) {
    echo "   ✅ ENCONTRADO: Sistema multi-path presente\n";
    echo "   ✅ VERSIÓN CORRECTA\n\n";
} else {
    echo "   ❌ NO ENCONTRADO: Sistema multi-path ausente\n";
    echo "   ❌ VERSIÓN ANTIGUA (necesita actualizarse)\n\n";
}

echo "4. Buscar línea problemática:\n";
if (strpos($content, "require_once '/PHPMailer/Exception.php'") !== false) {
    echo "   ❌ ENCONTRADA: Línea antigua sin /src/\n";
    echo "   → El archivo NO se actualizó correctamente\n\n";
} else {
    echo "   ✅ NO ENCONTRADA: Línea antigua no presente\n\n";
}

echo "5. Hash del archivo (primeros 1000 caracteres):\n";
echo "   " . substr(md5(substr($content, 0, 1000)), 0, 16) . "\n\n";

echo "=== INSTRUCCIONES ===\n";
echo "Si ves 'VERSIÓN ANTIGUA', significa que el archivo NO se reemplazó.\n";
echo "Posibles causas:\n";
echo "- El ZIP no se extrajo en la ubicación correcta\n";
echo "- El ZIP se extrajo pero no sobreescribió el archivo\n";
echo "- Hay permisos de escritura insuficientes\n";
echo "\nSOLUCIÓN: Elimina manualmente omniflow_connector.php del servidor\n";
echo "y luego extrae el ZIP de nuevo.\n";
