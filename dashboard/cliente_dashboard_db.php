<?php
// cliente_dashboard_db.php
// Dashboard y gestión de leads en PHP, accediendo directo a la base de datos

// Configuración de la base de datos
$DB_HOST = 'localhost';
$DB_USER = 'plansalu_blogger';
$DB_PASS = 'Blog.2025!#';
$DB_NAME = 'plansalu_blog';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('<div style="color:red">Error de conexión a la base de datos</div>');
}

// Obtener métricas
$total_leads = 0;
$new_leads = 0;
$contacted_leads = 0;
$res = $mysqli->query("SELECT COUNT(id) as total FROM procesar_formularios");
if ($res) $total_leads = (int)$res->fetch_assoc()['total'];
$res = $mysqli->query("SELECT COUNT(id) as nuevos FROM procesar_formularios WHERE estado = 'Nuevo'");
if ($res) $new_leads = (int)$res->fetch_assoc()['nuevos'];
$res = $mysqli->query("SELECT COUNT(id) as contactados FROM procesar_formularios WHERE estado = 'Contactado'");
if ($res) $contacted_leads = (int)$res->fetch_assoc()['contactados'];

// Actualizar estado de lead si se envió el formulario
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lead_id'], $_POST['nuevo_estado'])) {
    $lead_id = intval($_POST['lead_id']);
    $nuevo_estado = $_POST['nuevo_estado'];
    $stmt = $mysqli->prepare("UPDATE procesar_formularios SET estado = ? WHERE id = ?");
    $stmt->bind_param('si', $nuevo_estado, $lead_id);
    if ($stmt->execute()) {
        $msg = '<div class="success">Estado actualizado correctamente</div>';
    } else {
        $msg = '<div class="error">Error al actualizar</div>';
    }
    $stmt->close();
}

// Obtener leads
$leads = [];
$res = $mysqli->query("SELECT id, nombre, correo as email, estado FROM procesar_formularios ORDER BY fecha_creacion DESC LIMIT 50");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $leads[] = $row;
    }
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Cliente (PHP)</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f4f4; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px #0001; }
        h1 { color: #2c3e50; }
        .metrics { display: flex; gap: 2em; margin-bottom: 2em; }
        .metric { background: #eaf6fb; padding: 1em 2em; border-radius: 6px; text-align: center; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; border-bottom: 1px solid #eee; }
        th { background: #f0f8ff; }
        tr:last-child td { border-bottom: none; }
        select, button { padding: 5px 10px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h1>Dashboard Cliente (PHP)</h1>
    <?= $msg ?>
    <section class="metrics">
        <div class="metric"><strong>Total Leads</strong><br><?= $total_leads ?></div>
        <div class="metric"><strong>Leads Nuevos</strong><br><?= $new_leads ?></div>
        <div class="metric"><strong>Contactados</strong><br><?= $contacted_leads ?></div>
    </section>
    <section>
        <h2>Gestión de Leads</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($leads as $lead): ?>
                <tr>
                    <td><?= htmlspecialchars($lead['id']) ?></td>
                    <td><?= htmlspecialchars($lead['nombre']) ?></td>
                    <td><?= htmlspecialchars($lead['email']) ?></td>
                    <td><?= htmlspecialchars($lead['estado']) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="lead_id" value="<?= htmlspecialchars($lead['id']) ?>">
                            <select name="nuevo_estado">
                                <option value="Nuevo" <?= $lead['estado'] === 'Nuevo' ? 'selected' : '' ?>>Nuevo</option>
                                <option value="Contactado" <?= $lead['estado'] === 'Contactado' ? 'selected' : '' ?>>Contactado</option>
                                <option value="Cerrado" <?= $lead['estado'] === 'Cerrado' ? 'selected' : '' ?>>Cerrado</option>
                            </select>
                            <button type="submit">Actualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
</body>
</html>
