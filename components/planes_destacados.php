<?php
/**
 * components/planes_destacados.php
 * Muestra 3 planes destacados del catálogo (mejor cobertura, más económico, mejor red).
 * Uso: render_component('planes_destacados')
 */

$cache = [];
$path = __DIR__ . '/../adjuntos/planes_isapre.csv';
if (($h = fopen($path, 'r')) === false) return;
fgetcsv($h, 0, ',', '"', '');
while (($r = fgetcsv($h, 0, ',', '"', '')) !== false) {
    if (count($r) < 10) continue;
    $uf = (float) str_replace(',', '.', $r[3] ?? '0');
    if ($uf < 0.5) continue;
    $cache[] = [
        'isapre'      => trim($r[0] ?? ''),
        'codigo'      => trim($r[1] ?? ''),
        'nombre'      => trim($r[2] ?? ''),
        'uf'          => $uf,
        'prestadores' => (int)($r[5] ?? 0),
        'hosp'        => (int)($r[6] ?? 0),
        'amb'         => (int)($r[7] ?? 0),
    ];
}
fclose($h);
if (empty($cache)) return;

// Pick 3 featured with balanced score
usort($cache, fn($a,$b) => ($b['hosp'] + $b['amb'] - $a['uf']*5) <=> ($a['hosp'] + $a['amb'] - $a['uf']*5));
$featured = array_slice(array_unique(array_map(fn($p) => $p['isapre'].'|'.$p['codigo'], $cache)), 0, 3);
$plans = [];
foreach ($featured as $key) {
    foreach ($cache as $p) {
        if (($p['isapre'].'|'.$p['codigo']) === $key) { $plans[] = $p; break; }
    }
}
if (count($plans) < 3) return;

$colors = [
    ['bg' => 'from-blue-50 to-white', 'border' => 'border-blue-200', 'badge' => 'bg-blue-600', 'badge_text' => 'Recomendado', 'text_color' => 'text-blue-700'],
    ['bg' => 'from-emerald-50 to-white', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-600', 'badge_text' => 'Mejor Precio', 'text_color' => 'text-emerald-700'],
    ['bg' => 'from-purple-50 to-white', 'border' => 'border-purple-200', 'badge' => 'bg-purple-600', 'badge_text' => 'Mayor Cobertura', 'text_color' => 'text-purple-700'],
];
?>
<section class="max-w-4xl mx-auto px-4 py-10">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-3">Planes Destacados</h2>
        <p class="text-gray-500 text-sm max-w-lg mx-auto">Seleccionamos los mejores planes por cobertura, precio y prestadores. Datos actualizados de la Superintendencia de Salud.</p>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
        <?php foreach ($plans as $i => $plan): $c = $colors[$i]; ?>
        <div class="relative bg-gradient-to-b <?= $c['bg'] ?> rounded-2xl p-6 border <?= $c['border'] ?> text-center hover:shadow-lg transition-shadow">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 <?= $c['badge'] ?> text-white text-xs font-bold px-4 py-1 rounded-full"><?= $c['badge_text'] ?></div>
            <div class="w-12 h-12 <?= $c['badge'] ?> text-white rounded-xl flex items-center justify-center mx-auto mb-3 mt-2 text-lg font-bold"><?= substr($plan['isapre'], 0, 1) ?></div>
            <div class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1"><?= htmlspecialchars($plan['isapre']) ?></div>
            <h3 class="text-sm font-bold text-gray-800 mb-3 leading-tight"><?= htmlspecialchars($plan['nombre']) ?></h3>
            <div class="flex justify-center gap-4 text-xs text-gray-500 mb-4">
                <span>🏥 <?= $plan['hosp'] ?>%</span>
                <span>🩺 <?= $plan['amb'] ?>%</span>
                <span>🏢 <?= $plan['prestadores'] ?></span>
            </div>
            <div class="text-2xl font-extrabold <?= $c['text_color'] ?> mb-1"><?= number_format($plan['uf'], 2, ',', '.') ?> UF</div>
            <div class="text-xs text-gray-400 mb-4">/mes · precio base</div>
            <a href="<?= BASE_URL ?>/planes/comparador/?plan=<?= urlencode($plan['codigo']) ?>#comparador" class="inline-block w-full <?= $c['badge'] ?> hover:opacity-90 text-white font-semibold py-2.5 px-4 rounded-xl transition text-sm">
                Ver plan →
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-8">
        <a href="<?= BASE_URL ?>/planes/comparador/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
            Ver todos los planes en el comparador
            <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
        </a>
    </div>
</section>
