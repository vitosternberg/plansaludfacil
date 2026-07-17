<?php
/**
 * helpers_isapre.php — Datos reales de ISAPREs para páginas de compañías
 * Usa revision_IA_Planes_isapre.csv y el motor de cotización.
 * Requiere: core/cotizador_engine.php
 */
require_once __DIR__ . '/cotizador_engine.php';

function get_isapre_cobertura($isapre_name) {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        $path = __DIR__ . '/../adjuntos/revision_IA_Planes_isapre.csv';
        if (($h = fopen($path, 'r')) === false) return null;
        fgetcsv($h, 0, ',', '"', ''); fgetcsv($h, 0, ',', '"', '');
        while (($r = fgetcsv($h, 0, ',', '"', '')) !== false) {
            if (count($r) < 13) continue;
            $n = _norm($r[0]); if (!$n) continue;
            $cache[$n] = ['hp'=>$r[2]??'-','cp'=>$r[3]??'-','tp'=>$r[4]??'-','hl'=>$r[6]??'-','cl'=>$r[7]??'-','tl'=>$r[8]??'-','urg'=>$r[10]??'-','red'=>$r[12]??''];
        }
        fclose($h);
    }
    return $cache[$isapre_name] ?? null;
}
function _norm($n){$n=trim($n);$m=['cruz blanca'=>'Cruz Blanca','nueva masvida'=>'Nueva Masvida','banmédica'=>'Banmédica','banmedica'=>'Banmédica','vida tres'=>'Vida Tres'];$l=strtolower($n);return $m[$l]??$n;}

function get_isapre_precios($isapre, $edad, $cargas=0) {
    $planes = load_catalog();
    $precios = [];
    foreach ($planes as $p) {
        if ($p['isapre'] !== $isapre) continue;
        $pr = calcular_precio($p['uf'], $edad, $cargas, null, null, null, $p['isapre']);
        $precios[] = $pr['total_clp'];
    }
    if (!$precios) return null;
    sort($precios); $n = count($precios);
    return ['min'=>$precios[0],'max'=>$precios[$n-1],'mediana'=>$precios[(int)($n/2)],'planes'=>$n];
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
