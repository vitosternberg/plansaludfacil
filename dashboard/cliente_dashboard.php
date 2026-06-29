<?php
// cliente/cliente_dashboard.php
// Dashboard y gestión de leads del lado del cliente usando omniflow_connector.php

session_start();
require_once __DIR__ . '/omniflow_connector.php';

// Autenticación local básica (puedes mejorar esto según tu flujo)
if (!isset($_SESSION['cliente_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Obtener métricas y leads usando el conector local
try {
    $metrics = obtenerMetricasDashboard();
    $leads = obtenerLeads();
} catch (Exception $e) {
    $error = $e->getMessage();
}

function obtenerMetricasDashboard() {
    // Simula una llamada al conector local para métricas
    $response = omniflow_api_call('get_dashboard_metrics', []);
    if (!$response['success']) throw new Exception('Error al obtener métricas');
    return $response['data'];
}

function obtenerLeads() {
    // Simula una llamada al conector local para leads
    $response = omniflow_api_call('get_leads', []);
    if (!$response['success']) throw new Exception('Error al obtener leads');
    return $response['data'];
}

function omniflow_api_call($action, $params) {
    // Aquí se llama directamente a funciones del conector local
    // En producción, esto sería una llamada HTTP o require al conector
    if (!function_exists($action)) {
        return ['success' => false, 'data' => null];
    }
    $data = call_user_func($action, $params);
    return ['success' => true, 'data' => $data];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Cliente - Omniflow</title>
    <link rel="stylesheet" href="../public/dashboard.css">
</head>
<body>
<?php require_once __DIR__ . '/../layout/partials/dashboard_header.php'; ?>
<?php require_once __DIR__ . '/../layout/partials/dashboard_sidebar.php'; ?>
<div class="dashboard-main-content">
    <h1>Dashboard del Cliente</h1>
    <?php if (isset($error)): ?>
        <div class="error">Error: <?= htmlspecialchars($error) ?></div>
    <?php else: ?>
        <section class="metrics">
            <h2>Métricas</h2>
            <ul>
                <li>Total Leads: <?= htmlspecialchars($metrics['total_leads'] ?? '-') ?></li>
                <li>Leads Nuevos: <?= htmlspecialchars($metrics['new_leads'] ?? '-') ?></li>
                <li>Leads Contactados: <?= htmlspecialchars($metrics['contacted_leads'] ?? '-') ?></li>
            </ul>
        </section>
        <section class="leads">
            <h2>Gestión de Leads</h2>
            <table border="1">
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
                            <form method="post" action="actualizar_lead.php" style="display:inline;">
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
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../layout/partials/dashboard_footer.php'; ?>
</body>
</html>
