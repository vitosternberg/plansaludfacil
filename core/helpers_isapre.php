<?php
/**
 * helpers_isapre.php — Datos reales de ISAPREs para páginas de compañías
 * Usa revision_IA_Planes_isapre.csv y el motor de cotización.
 * Requiere: core/cotizador_engine.php
 */
require_once __DIR__ . '/planes_data_provider.php';

function get_isapre_cobertura($isapre_name) {
    return pd_get_isapre_cobertura($isapre_name);
}

function get_isapre_precios($isapre, $edad, $cargas=0) {
    return pd_get_isapre_precios($isapre, $edad, $cargas);
}

function render_isapre_data($isapre) {
    $cov = get_isapre_cobertura($isapre);
    if (!$cov) return;
    $perfiles = [['Joven',30,0],['Adulto',45,0],['Adulto Mayor',65,0],['Carga Niño',5,1],['Carga Adulto',35,1]];
    ?>
    <section id="coberturas-reales" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Coberturas Reales <?= htmlspecialchars($isapre) ?></h2>
        <p class="text-sm text-gray-500 mb-6">Datos actualizados · planes vigentes comercializados</p>
        <div class="overflow-x-auto bg-white rounded-xl border mb-8">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 text-xs"><tr><th class="p-3 text-left">Tipo</th><th class="p-3">Hospitalaria</th><th class="p-3">Consulta</th><th class="p-3">Tope Anual</th><th class="p-3">Urgencia</th></tr></thead>
                <tbody class="divide-y">
                    <tr><td class="p-3 font-medium">Preferente</td><td class="p-3 text-center"><?= htmlspecialchars($cov['hp']) ?></td><td class="p-3 text-center"><?= htmlspecialchars($cov['cp']) ?></td><td class="p-3 text-center"><?= htmlspecialchars($cov['tp']) ?></td><td class="p-3 text-center"><?= htmlspecialchars($cov['urg']) ?></td></tr>
                    <?php if (!empty($cov['hl'])): ?>
                    <tr class="bg-gray-50"><td class="p-3 font-medium">Libre Elección</td><td class="p-3 text-center"><?= htmlspecialchars($cov['hl']) ?></td><td class="p-3 text-center"><?= htmlspecialchars($cov['cl']) ?></td><td class="p-3 text-center"><?= htmlspecialchars($cov['tl'] ?: '—') ?></td><td class="p-3 text-center">—</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($cov['red'])): ?><p class="text-sm text-gray-500 mb-6">🏥 Red: <?= htmlspecialchars(str_replace("\n",', ',$cov['red'])) ?></p><?php endif; ?>
    </section>
    <section id="precios-reales" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Precios Reales <?= htmlspecialchars($isapre) ?></h2>
        <p class="text-sm text-gray-500 mb-6">Calculados con tabla oficial Circular IF/N° 343 · UF $38.500</p>
        <div class="overflow-x-auto bg-white rounded-xl border">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 text-xs"><tr><th class="p-3 text-left">Perfil</th><th class="p-3 text-right">Más Barato</th><th class="p-3 text-right">Más Caro</th><th class="p-3 text-right">Recomendado</th><th class="p-3 text-right">Planes</th></tr></thead>
                <tbody class="divide-y">
                    <?php foreach ($perfiles as $i => [$l,$e,$c]): $pr = get_isapre_precios($isapre,$e,$c); if(!$pr) continue; ?>
                    <tr class="<?= $i%2?'bg-gray-50':'' ?>"><td class="p-3 font-medium"><?= $l ?> (<?= $e ?>a)</td><td class="p-3 text-right">$<?= number_format($pr['min'],0,',','.') ?></td><td class="p-3 text-right text-gray-500">$<?= number_format($pr['max'],0,',','.') ?></td><td class="p-3 text-right text-blue-600 font-semibold">$<?= number_format($pr['mediana'],0,',','.') ?></td><td class="p-3 text-right text-gray-400"><?= $pr['planes'] ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-3">Precios desde el catálogo de <?= $pr['planes'] ?? '—' ?> planes. Varían según edad exacta, cargas y valor UF del día.</p>
    </section>
    <?php
}

/**
 * Genera Schema.org JSON-LD ItemList con todos los planes de una isapre.
 * Datos desde planes_isapre.csv (2,231 planes).
 */
function render_isapre_plans_jsonld($isapre_name) {
    $data = pd_search(['isapre' => $isapre_name, 'limit' => 100]);
    $plans = $data['planes'] ?? [];
    if (empty($plans)) return;

    $host = $_SERVER['HTTP_HOST'] ?? 'plansaludfacil.cl';
    $item_list = [];
    foreach ($plans as $i => $p) {
        $item_list[] = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $p['nombre'],
            'url' => 'https://' . $host . BASE_URL . '/planes/comparador/?codigo=' . $p['codigo'],
        ];
    }

    $json_ld = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'name' => 'Planes Isapre ' . $isapre_name,
        'description' => 'Catálogo de planes de salud de Isapre ' . $isapre_name . ' vigentes. Fuente: Superintendencia de Salud.',
        'numberOfItems' => count($item_list),
        'itemListElement' => $item_list,
    ];
    return '<script type="application/ld+json">' . "\n" .
           json_encode($json_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) .
           "\n</script>";
}

/**
 * Genera sección de stats + top planes destacados para página de compañía.
 * Datos desde planes_isapre.csv.
 */
function render_isapre_hero_stats($isapre_name) {
    $stats = pd_get_isapre_stats($isapre_name);
    if (!$stats) return;

    $count = $stats['count'];
    $avg_h = $stats['avg_hosp'];
    $avg_a = $stats['avg_amb'];
    $avg_uf = $stats['avg_uf'];
    $avg_prest = $stats['avg_prest'];

    $global_h = $stats['global']['avg_hosp'];
    $global_a = $stats['global']['avg_amb'];
    $global_uf = $stats['global']['avg_uf'];
    $global_prest = $stats['global']['avg_prest'];

    $cheapest = $stats['top']['cheapest'];
    $best_cov = $stats['top']['best_cov'];
    $best_net = $stats['top']['best_net'];
    $balanced = $stats['top']['balanced'];
    ?>
<section class="max-w-4xl mx-auto px-4 py-6">
    <!-- Stats bar -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-8 text-center">
        <div class="bg-white rounded-xl p-4 border"><div class="text-2xl font-bold text-blue-700"><?= $count ?></div><div class="text-xs text-gray-500 mt-1">planes analizados</div></div>
        <div class="bg-white rounded-xl p-4 border"><div class="text-2xl font-bold text-blue-700"><?= $avg_h ?>%</div><div class="text-xs text-gray-500 mt-1">hosp. promedio</div></div>
        <div class="bg-white rounded-xl p-4 border"><div class="text-2xl font-bold text-blue-700"><?= $avg_a ?>%</div><div class="text-xs text-gray-500 mt-1">amb. promedio</div></div>
        <div class="bg-white rounded-xl p-4 border"><div class="text-2xl font-bold text-blue-700"><?= number_format($avg_uf,2,',','.') ?> UF</div><div class="text-xs text-gray-500 mt-1">precio base prom.</div></div>
        <div class="bg-white rounded-xl p-4 border"><div class="text-2xl font-bold text-blue-700"><?= $avg_prest ?></div><div class="text-xs text-gray-500 mt-1">prestadores en red</div></div>
    </div>

    <!-- Top 4 destacados -->
    <div class="grid md:grid-cols-4 gap-5 mb-8">
        <div class="bg-gradient-to-b from-white to-green-50 rounded-2xl p-5 border border-green-200 text-center">
            <div class="text-xs text-green-600 font-bold uppercase mb-2">Más económico</div>
            <div class="text-sm font-bold text-gray-800 mb-1"><?= htmlspecialchars($cheapest['nombre']) ?></div>
            <div class="flex justify-center gap-3 text-xs text-gray-500 mb-2"><span>Hosp. <?= $cheapest['cobertura_hosp_pct'] ?>%</span><span>Amb. <?= $cheapest['cobertura_amb_pct'] ?>%</span></div>
            <div class="text-lg font-extrabold text-green-700"><?= number_format($cheapest['uf'],2,',','.') ?> UF</div>
        </div>
        <div class="bg-gradient-to-b from-white to-blue-50 rounded-2xl p-5 border border-blue-200 text-center">
            <div class="text-xs text-blue-600 font-bold uppercase mb-2">Mejor cobertura</div>
            <div class="text-sm font-bold text-gray-800 mb-1"><?= htmlspecialchars($best_cov['nombre']) ?></div>
            <div class="flex justify-center gap-3 text-xs text-gray-500 mb-2"><span>Hosp. <?= $best_cov['cobertura_hosp_pct'] ?>%</span><span>Amb. <?= $best_cov['cobertura_amb_pct'] ?>%</span></div>
            <div class="text-lg font-extrabold text-blue-700"><?= number_format($best_cov['uf'],2,',','.') ?> UF</div>
        </div>
        <div class="bg-gradient-to-b from-white to-purple-50 rounded-2xl p-5 border border-purple-200 text-center">
            <div class="text-xs text-purple-600 font-bold uppercase mb-2">Mayor red</div>
            <div class="text-sm font-bold text-gray-800 mb-1"><?= htmlspecialchars($best_net['nombre']) ?></div>
            <div class="flex justify-center gap-3 text-xs text-gray-500 mb-2"><span><?= $best_net['prestadores'] ?> prestadores</span></div>
            <div class="text-lg font-extrabold text-purple-700"><?= number_format($best_net['uf'],2,',','.') ?> UF</div>
        </div>
        <div class="bg-gradient-to-b from-white to-amber-50 rounded-2xl p-5 border border-amber-200 text-center">
            <div class="text-xs text-amber-600 font-bold uppercase mb-2">Más equilibrado</div>
            <div class="text-sm font-bold text-gray-800 mb-1"><?= htmlspecialchars($balanced['nombre']) ?></div>
            <div class="flex justify-center gap-3 text-xs text-gray-500 mb-2"><span>Hosp. <?= $balanced['cobertura_hosp_pct'] ?>%</span><span>Amb. <?= $balanced['cobertura_amb_pct'] ?>%</span></div>
            <div class="text-lg font-extrabold text-amber-700"><?= number_format($balanced['uf'],2,',','.') ?> UF</div>
        </div>
    </div>

    <!-- Comparativa vs promedio -->
    <div class="bg-white rounded-2xl border p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">¿Cómo se compara <?= htmlspecialchars($isapre_name) ?>?</h3>
        <div class="space-y-3">
            <?php 
            $metrics = [
                ['Cobertura Hospitalaria', $avg_h, $global_h, '%', $avg_h >= $global_h],
                ['Cobertura Ambulatoria', $avg_a, $global_a, '%', $avg_a >= $global_a],
                ['Precio Promedio', $avg_uf, $global_uf, ' UF', $avg_uf <= $global_uf],
                ['Prestadores por Plan', $avg_prest, $global_prest, '', $avg_prest >= $global_prest],
            ];
            foreach ($metrics as [$label, $val, $gl, $unit, $good]):
                $diff = $unit === '%' ? $val - $gl : round($val - $gl, 2);
                $color = $good ? 'text-green-600' : 'text-red-500';
                $sign = $diff > 0 ? '+' : '';
            ?>
            <div class="flex items-center gap-3 text-sm">
                <span class="w-48 font-medium text-gray-700"><?= $label ?></span>
                <div class="flex-1 bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width:<?= $unit==='%' ? min($val,100) : min($val*20,100) ?>%"></div>
                </div>
                <span class="w-16 text-right font-bold"><?= $val ?><?= $unit ?></span>
                <span class="w-24 text-right <?= $color ?> text-xs"><?= $sign.$diff ?><?= $unit ?> vs prom.</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
    <?php
}
