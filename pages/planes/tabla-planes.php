<?php
/**
 * Tabla de planes de ISAPRE — paginada en el servidor.
 * La data se obtiene a través de core/planes_data_provider.php:
 *   - instalación proveedor: lee los CSVs locales
 *   - instalación cliente: consume la API del proveedor (no tiene la data)
 * Nunca se embebe el dataset completo en el navegador.
 *
 * Params GET: ?q=texto & isapre=Nombre & page=N
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../core/planes_data_provider.php';

$PER_PAGE = 50;

$q      = trim($_GET['q'] ?? '');
$isapre = trim($_GET['isapre'] ?? '');
$page   = max((int)($_GET['page'] ?? 1), 1);

$filters = ['limit' => $PER_PAGE, 'offset' => ($page - 1) * $PER_PAGE];
if ($q !== '')      $filters['q'] = $q;
if ($isapre !== '') $filters['isapre'] = $isapre;

$data  = pd_search($filters);
$planes = $data['planes'];
$total  = (int)$data['total'];
$totalPages = max(1, (int)ceil($total / $PER_PAGE));

$ISAPRE_LIST = ['Banmédica', 'Colmena', 'Consalud', 'Cruz Blanca', 'Esencial', 'Nueva Masvida', 'Vida Tres'];

function tabla_plan_url($p) {
    return BASE_URL . '/planes/detalle/?codigo=' . urlencode($p['codigo']);
}
function tabla_plan_uf($p) {
    return number_format((float)$p['uf'], 2, ',', '.');
}
function tabla_plan_tope($p) {
    return number_format((float)$p['tope_anual_uf'], 0, ',', '.');
}
function tabla_page_url($page) {
    $params = $_GET;
    $params['page'] = $page;
    if ($page <= 1) unset($params['page']);
    $qs = http_build_query($params);
    return strtok($_SERVER['REQUEST_URI'], '?') . ($qs !== '' ? '?' . $qs : '');
}
?>
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes de ISAPRE — PlanSaludFácil</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/tailwind.min.css">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; }
        tr:hover td { background: #f1f5f9; }
    </style>
</head>
<body class="min-h-screen">

<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Planes de ISAPRE</h1>
        <p class="text-sm text-gray-500 mt-1"><?= number_format($total, 0, ',', '.') ?> planes · 7 ISAPREs · Datos actualizados julio 2026</p>
    </div>

    <!-- Controls -->
    <form method="get" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 flex flex-wrap gap-3 items-center">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Buscar por nombre, código o ISAPRE..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <select name="isapre" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Todas las ISAPREs</option>
            <?php foreach ($ISAPRE_LIST as $is): ?>
                <option value="<?= htmlspecialchars($is) ?>" <?= $isapre === $is ? 'selected' : '' ?>><?= htmlspecialchars($is) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">Filtrar</button>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">ISAPRE</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Nombre del Plan</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Código</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">UF/mes</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Tope Anual UF</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Prestadores</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Link</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($planes)): ?>
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No se encontraron planes con esos filtros.</td></tr>
                    <?php else: ?>
                        <?php foreach ($planes as $p): ?>
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 text-gray-900"><?= htmlspecialchars($p['isapre']) ?></td>
                            <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($p['nombre']) ?></td>
                            <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($p['codigo']) ?></td>
                            <td class="px-4 py-3 text-right text-gray-900"><?= tabla_plan_uf($p) ?></td>
                            <td class="px-4 py-3 text-right text-gray-900"><?= tabla_plan_tope($p) ?></td>
                            <td class="px-4 py-3 text-right text-gray-900"><?= (int)$p['prestadores'] ?></td>
                            <td class="px-4 py-3 text-center">
                                <a href="<?= htmlspecialchars(tabla_plan_url($p)) ?>" class="text-blue-600 hover:text-blue-800 underline">Ver</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3">
            <div class="text-sm text-gray-500">
                Página <?= $page ?> de <?= $totalPages ?> · mostrando <?= count($planes) ?> de <?= number_format($total, 0, ',', '.') ?>
            </div>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="<?= htmlspecialchars(tabla_page_url($page - 1)) ?>" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">← Anterior</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= htmlspecialchars(tabla_page_url($page + 1)) ?>" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Siguiente →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <p class="text-xs text-gray-400 mt-4 text-center">
        Precios en UF · No constituye asesoría legal o financiera
    </p>
</div>
</body>
</html>
