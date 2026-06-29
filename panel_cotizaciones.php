<?php
/**
 * panel_cotizaciones.php
 * Panel simple para listar prospectos de la tabla 'cotizaciones'.
 * Ubicación: tu_proyecto_raiz/panel_cotizaciones.php
 *
 * [VERSION CONTROL] - Nueva Versión: 2025-07-09
 * - Muestra un listado paginado de todos los prospectos de la tabla `cotizaciones`.
 * - Incluye todos los campos de la tabla: nombre, rut, email, telefono, genero, edad,
 * cargas, prevision, renta, tipo_plan, fecha_creacion, sale_closing_date,
 * first_contact_date, second_contact_date.
 * - Estilizado con Tailwind CSS.
 * - Incluye funcionalidad básica de paginación.
 */

// Incluye el archivo de configuración de la base de datos desde la raíz del proyecto.
// Asegúrate de que 'config.php' esté en el mismo directorio que este archivo.
require_once 'config.php'; 

// --- Configuración de Paginación ---
$limit = 10; // Número de registros por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- Funciones de Acceso a Datos ---
/**
 * Obtiene el total de prospectos en la tabla cotizaciones.
 * @return int Total de prospectos.
 */
function get_total_cotizaciones() {
    $conn = connect_db_simple();
    if ($conn === null) {
        return 0;
    }
    $total = 0;
    try {
        $result = $conn->query("SELECT COUNT(*) AS total FROM cotizaciones");
        if ($result && $row = $result->fetch_assoc()) {
            $total = $row['total'];
        }
    } catch (Exception $e) {
        error_log("Error al obtener total de cotizaciones: " . $e->getMessage());
    } finally {
        if ($conn) $conn->close();
    }
    return $total;
}

/**
 * Obtiene un segmento de prospectos de la tabla cotizaciones con paginación.
 * @param int $limit Límite de registros.
 * @param int $offset Desplazamiento de registros.
 * @return array Array de prospectos.
 */
function get_paginated_cotizaciones($limit, $offset) {
    $conn = connect_db_simple();
    $cotizaciones = [];
    if ($conn === null) {
        return $cotizaciones;
    }

    try {
        // Asegúrate de que los nombres de las columnas coincidan con tu tabla
        $query = "
            SELECT 
                id, nombre, rut, email, region, telefono, genero, edad,
                cargas, prevision, renta, tipo_plan, fecha_creacion,
                sale_closing_date, first_contact_date, second_contact_date
            FROM cotizaciones
            ORDER BY fecha_creacion DESC
            LIMIT ? OFFSET ?
        ";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            error_log("Error al preparar la consulta de cotizaciones: " . $conn->error);
            return $cotizaciones;
        }
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $cotizaciones[] = $row;
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error al obtener cotizaciones paginadas: " . $e->getMessage());
    } finally {
        if ($conn) $conn->close();
    }
    return $cotizaciones;
}

// Obtener datos
$total_cotizaciones = get_total_cotizaciones();
$cotizaciones_data = get_paginated_cotizaciones($limit, $offset);
$total_pages = ceil($total_cotizaciones / $limit);

?>
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Prospectos - Cotizaciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; color: #334155; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #e0e0e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        /* Estilos específicos para tabla con scroll horizontal */
        .table-container {
            overflow-x: auto;
        }
        .whitespace-nowrap {
            white-space: nowrap; /* Evita que el contenido de las celdas se envuelva */
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <header class="bg-indigo-600 text-white shadow-md py-4">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-center">Panel de Prospectos</h1>
        </div>
    </header>

    <main class="flex-1 container mx-auto px-4 py-8">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Listado de Cotizaciones (Total: <?php echo $total_cotizaciones; ?>)</h2>
            
            <div class="table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RUT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Región</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Género</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previsión</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Renta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cierre Venta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">1ra Interacción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">2da Interacción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($cotizaciones_data)): ?>
                            <tr>
                                <td colspan="16" class="px-6 py-4 text-center text-gray-500">No hay prospectos de cotización para mostrar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cotizaciones_data as $cotizacion): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cotizacion['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($cotizacion['nombre']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['rut']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['telefono']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['region']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['genero']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['edad']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['cargas']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['prevision']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(number_format($cotizacion['renta'], 0, ',', '.')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['tipo_plan']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['fecha_creacion']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['sale_closing_date'] ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['first_contact_date'] ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($cotizacion['second_contact_date'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-8 flex justify-center space-x-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="px-4 py-2 rounded-lg <?php echo ($i === $page) ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Siguiente</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>&copy; 2025 Planes de Isapres. Todos los derechos reservados.</p>
        </div>
    </footer>

</body>
</html>
