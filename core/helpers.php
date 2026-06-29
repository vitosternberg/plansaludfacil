<?php
/**
 * Renderiza un componente aislado.
 * 
 * @param string $component_name El nombre del archivo en la carpeta components (sin .php)
 * @param array $data Variables a inyectar dentro del componente
 */
function render_component($component_name, $data = []) {
    // Extrae las variables del array para que estén disponibles localmente en el componente
    // Ejemplo: ['titulo' => 'Hola'] se convierte en $titulo = 'Hola'
    extract($data);
    
    $component_path = __DIR__ . '/../components/' . $component_name . '.php';
    
    if (file_exists($component_path)) {
        require $component_path;
    } else {
        echo "<!-- Error: Componente '{$component_name}' no encontrado -->";
    }
}
