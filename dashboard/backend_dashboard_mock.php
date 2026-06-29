<?php
// backend_dashboard_mock.php
// Emula la sección dashboard: métricas y últimos 10 leads

$DB_HOST = 'localhost';
$DB_USER = 'plansalu_blogger';
$DB_PASS = 'Blog.2025!#';
$DB_NAME = 'plansalu_blog';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('<div style="color:red">Error de conexión a la base de datos: ' . $mysqli->connect_error . '</div>');
}

// Habilitar reporte de errores
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Métricas
$total_leads = 0;
$new_leads = 0;
$contacted_leads = 0;
$res = $mysqli->query("SELECT COUNT(id) as total FROM procesar_formularios");
if ($res) $total_leads = (int)$res->fetch_assoc()['total'];
$res = $mysqli->query("SELECT COUNT(id) as nuevos FROM procesar_formularios WHERE estado = 'Nuevo'");
if ($res) $new_leads = (int)$res->fetch_assoc()['nuevos'];
$res = $mysqli->query("SELECT COUNT(id) as contactados FROM procesar_formularios WHERE estado = 'Contactado'");
if ($res) $contacted_leads = (int)$res->fetch_assoc()['contactados'];

// Todos los leads
$leads = [];
$res = $mysqli->query("SELECT id, nombre, correo, celular, pais, estado, notas, borrador_respuesta_ia, first_contact_date, second_contact_date, sale_closing_date, fecha_creacion, unsubscribed, datos_adicionales FROM procesar_formularios ORDER BY fecha_creacion DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        // Decodificar datos_adicionales si existe
        if (!empty($row['datos_adicionales'])) {
            $adicionales = json_decode($row['datos_adicionales'], true);
            if (is_array($adicionales)) {
                // Mezclar campos superiores
                $row = array_merge($row, $adicionales);

                // Extraer campos del objeto salud
                if (isset($adicionales['salud']) && is_array($adicionales['salud'])) {
                    foreach ($adicionales['salud'] as $key => $value) {
                        $row['salud_' . $key] = $value;
                    }
                    $row['salud_json'] = json_encode($adicionales['salud'], JSON_UNESCAPED_UNICODE);
                }
            }
        }
        $leads[] = $row;
    }
} else {
    die('<div style="color:red">Error en consulta: ' . $mysqli->error . '</div>');
}

// Debug: mostrar cuántos leads se encontraron
$count_leads = count($leads);
if ($count_leads === 0) {
    $debug_msg = "No se encontraron leads en la base de datos.";
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Cliente (Emulado)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        .container { max-width: 98%; margin: 20px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        h1 { color: #2c3e50; }
        .metrics { display: flex; gap: 2em; margin-bottom: 2em; }
        .metric { background: #eaf6fb; padding: 1em 2em; border-radius: 6px; text-align: center; }
        table { width: 100%; border-collapse: collapse; background: #fff; font-size: 13px; }
        th, td { padding: 8px 6px; border-bottom: 1px solid #eee; white-space: nowrap; }
        th { background: #f0f8ff; }
        tr:last-child td { border-bottom: none; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h1>Dashboard Cliente (Emulado)</h1>
    <?php if (isset($debug_msg)): ?>
        <div style="padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; margin-bottom: 20px;">
            <?= $debug_msg ?> (Total: <?= $count_leads ?> leads)
        </div>
    <?php endif; ?>
    <section class="metrics">
        <div class="metric"><strong>Total Leads</strong><br><?= $total_leads ?></div>
        <div class="metric"><strong>Leads Nuevos</strong><br><?= $new_leads ?></div>
        <div class="metric"><strong>Contactados</strong><br><?= $contacted_leads ?></div>
    </section>
    <section>
        <h2>Todos los Leads</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Celular</th>
                    <th>País</th>
                    <th>RUT</th>
                    <th>Edad</th>
                    <th>Región</th>
                    <th>Previsión</th>
                    <th>Plan</th>
                    <th>Renta</th>
                    <th>Cargas</th>
                    <th>Estado</th>
                    <th>1er Contacto</th>
                    <th>2do Contacto</th>
                    <th>Cierre Venta</th>
                    <th>Fecha Creación</th>
                    <th>Baja</th>
                    <th>Salud (Previsión)</th>
                    <th>Salud (Plan)</th>
                    <th>Salud (Renta)</th>
                    <th>Salud (Cargas)</th>
                    <th>Salud (Edad)</th>
                    <th>Salud (Región)</th>
                    <th>Salud (RUT)</th>
                    <th>Salud (Género)</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($leads as $lead): ?>
                <tr>
                    <td><?= htmlspecialchars($lead['id']) ?></td>
                    <td><?= htmlspecialchars($lead['nombre']) ?></td>
                    <td><?= htmlspecialchars($lead['correo'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['celular'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['pais'] ?? $lead['pais_residencia'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['rut'] ?? $lead['salud_rut'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['edad'] ?? $lead['salud_edad'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['region'] ?? $lead['salud_region'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['prevision_interes'] ?? $lead['salud_prevision'] ?? $lead['salud_prevision_interes'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['plan_interes'] ?? $lead['salud_plan'] ?? $lead['salud_plan_interes'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['renta'] ?? $lead['salud_renta'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['cargas'] ?? $lead['salud_cargas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['estado']) ?></td>
                    <td><?= htmlspecialchars($lead['first_contact_date'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['second_contact_date'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['sale_closing_date'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['fecha_creacion']) ?></td>
                    <td><?= $lead['unsubscribed'] ? '✓' : '-' ?></td>
                    <td><?= htmlspecialchars($lead['salud_prevision'] ?? $lead['salud_prevision_interes'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['salud_plan'] ?? $lead['salud_plan_interes'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['salud_renta'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['salud_cargas'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['salud_edad'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['salud_region'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['salud_rut'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($lead['salud_genero'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
</body>
</html>
