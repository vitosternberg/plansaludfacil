<?php
/**
 * =======================================================================
 * SCRIPT DE PRUEBA DE CONCEPTO - ACTUALIZACIÓN DIRECTA DE LEADS (CON LISTADO Y FORMATO)
 * =======================================================================
 * Este script se conecta directamente a la base de datos y ejecuta la
 * lógica de actualización de estado. La salida está formateada para
 * una fácil lectura tanto en el navegador como en la terminal.
 */

// Habilitar la visualización de todos los errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- CONFIGURACIÓN DE LA BASE DE DATOS ---
define('DB_HOST', 'localhost');
define('DB_USER', 'plansalu_blogger');
define('DB_PASS', 'Blog.2025!#');
define('DB_NAME', 'plansalu_blog');
// --- FIN DE LA CONFIGURACIÓN ---


// --- DATOS DE PRUEBA ---
$datos_de_prueba = [
    'id_lead' => 1,
    'nuevo_estado' => 'Nuevo'
];
// --- FIN DE DATOS DE PRUEBA ---

// Inicia un bloque de texto preformateado para respetar los saltos de línea y espacios.
echo '<pre>';

echo "Iniciando script de actualización...\n";
echo "-------------------------------------\n";
echo "Lead a procesar: ID #" . $datos_de_prueba['id_lead'] . " | Nuevo estado: '" . $datos_de_prueba['nuevo_estado'] . "'\n";

try {
    // 1. Conexión a la base de datos
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        throw new Exception('Error de conexión a la base de datos: ' . $db->connect_error);
    }
    $db->set_charset("utf8mb4");
    echo "Conexión a la base de datos exitosa.\n";

    // 2. Ejecutar la función principal
    handle_actualizar_estado_lead($db, $datos_de_prueba);

    // 3. Cerrar la conexión
    $db->close();
    echo "-------------------------------------\n";
    echo "Script finalizado.\n";

} catch (Exception $e) {
    echo "\nERROR CRÍTICO: " . $e->getMessage() . "\n";
}

// Cierra el bloque de texto preformateado.
echo '</pre>';


// --- DEFINICIÓN DE LA FUNCIÓN PRINCIPAL ---

function handle_actualizar_estado_lead($db, $datos) {
    $id_lead = $datos['id_lead'] ?? 0;
    $nuevo_estado = $datos['nuevo_estado'] ?? '';

    if (empty($nuevo_estado) || $id_lead <= 0) {
        send_json_error('Datos inválidos.');
        return;
    }

    $sql_individual = "UPDATE procesar_formularios SET estado = ? WHERE id = ?";
    $stmt_individual = $db->prepare($sql_individual);
    $stmt_individual->bind_param("si", $nuevo_estado, $id_lead);

    if ($stmt_individual->execute()) {
        echo "\n=> El estado del lead #{$id_lead} se actualizó a '{$nuevo_estado}'.\n";

        if ($nuevo_estado === 'Nuevo') {
            echo "   -> El estado es 'Nuevo', aplicando validaciones de fechas de contacto...\n";
            
            // --- PRIMERA VALIDACIÓN: Actualizar first_contact_date ---
            $sql_select_first = "SELECT id, nombre, fecha_creacion FROM procesar_formularios 
                                 WHERE estado = 'Nuevo' AND first_contact_date IS NULL AND fecha_creacion <= NOW() - INTERVAL 3 DAY";
            $result_first = $db->query($sql_select_first);
            $leads_a_actualizar_1 = $result_first->fetch_all(MYSQLI_ASSOC);

            if (!empty($leads_a_actualizar_1)) {
                echo "\n   --- FASE 1: Leads para actualizar 'first_contact_date' ---\n";
                $ids_para_update_1 = [];
                foreach ($leads_a_actualizar_1 as $lead) {
                    echo "      - ID: {$lead['id']}, Nombre: {$lead['nombre']}, Creado: {$lead['fecha_creacion']}\n";
                    $ids_para_update_1[] = $lead['id'];
                }
                
                $placeholders = implode(',', array_fill(0, count($ids_para_update_1), '?'));
                $sql_update_first = "UPDATE procesar_formularios SET first_contact_date = NOW() WHERE id IN ($placeholders)";
                $stmt_update_first = $db->prepare($sql_update_first);
                $stmt_update_first->bind_param(str_repeat('i', count($ids_para_update_1)), ...$ids_para_update_1);
                $stmt_update_first->execute();
                echo "   -> RESULTADO FASE 1: Se actualizó la fecha para " . $stmt_update_first->affected_rows . " lead(s).\n";
            } else {
                echo "\n   --- FASE 1: No se encontraron leads para la primera actualización de contacto.\n";
            }

            // --- SEGUNDA VALIDACIÓN: Actualizar second_contact_date ---
            $sql_select_second = "SELECT id, nombre, first_contact_date FROM procesar_formularios 
                                  WHERE estado = 'Nuevo' AND first_contact_date IS NOT NULL AND second_contact_date IS NULL AND first_contact_date <= NOW() - INTERVAL 3 DAY";
            $result_second = $db->query($sql_select_second);
            $leads_a_actualizar_2 = $result_second->fetch_all(MYSQLI_ASSOC);

            if (!empty($leads_a_actualizar_2)) {
                echo "\n   --- FASE 2: Leads para actualizar 'second_contact_date' ---\n";
                $ids_para_update_2 = [];
                foreach ($leads_a_actualizar_2 as $lead) {
                    echo "      - ID: {$lead['id']}, Nombre: {$lead['nombre']}, Primer Contacto: {$lead['first_contact_date']}\n";
                    $ids_para_update_2[] = $lead['id'];
                }

                $placeholders = implode(',', array_fill(0, count($ids_para_update_2), '?'));
                $sql_update_second = "UPDATE procesar_formularios SET second_contact_date = NOW() WHERE id IN ($placeholders)";
                $stmt_update_second = $db->prepare($sql_update_second);
                $stmt_update_second->bind_param(str_repeat('i', count($ids_para_update_2)), ...$ids_para_update_2);
                $stmt_update_second->execute();
                echo "   -> RESULTADO FASE 2: Se actualizó la fecha para " . $stmt_update_second->affected_rows . " lead(s).\n";
            } else {
                echo "\n   --- FASE 2: No se encontraron leads para la segunda actualización de contacto.\n";
            }
        }
    } else {
        send_json_error('Error al ejecutar la consulta de actualización de estado.');
    }
}


// --- FUNCIONES AUXILIARES ---
function send_json_success($data) {
    echo "\n[OPERACIÓN EXITOSA]: " . json_encode($data) . "\n";
}

function send_json_error($message) {
    echo "\n[OPERACIÓN FALLIDA]: " . json_encode(['message' => $message]) . "\n";
}

?>
