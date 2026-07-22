<?php
/**
 * components/planes_destacados.php
 * Muestra 3 planes destacados del catálogo (mejor cobertura, más económico, mejor red).
 * Estilo Tesla Model 3: full-bleed background image + dark overlay + white text.
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

// Pick 3 featured by different criteria
$plans = [];
// 1. Mejor cobertura (highest hosp+amb)
usort($cache, function($a,$b) { return ($b['hosp'] + $b['amb']) - ($a['hosp'] + $a['amb']); });
foreach ($cache as $p) { if (!in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[0] = $p; break; } }
// 2. Más económico (lowest UF with decent coverage)
usort($cache, function($a,$b) { return $a['uf'] - $b['uf']; });
foreach ($cache as $p) { if ($p['hosp'] >= 60 && $p['amb'] >= 50 && !in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[1] = $p; break; } }
// 3. Mejor balance
usort($cache, function($a,$b) { return ($b['hosp'] + $b['amb'] - $b['uf']*3) - ($a['hosp'] + $a['amb'] - $a['uf']*3); });
foreach ($cache as $p) { if (!in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[2] = $p; break; } }
$plans = array_values($plans);
if (count($plans) < 3) return;

$backgrounds = [
    '/img/hero_familia.jpg',
    '/img/madre_orgullosa.jpg',
    '/img/mama_hijas.jpg',
];

$badges = [
    ['text' => 'Recomendado',   'accent' => 'from-blue-500 to-cyan-400'],
    ['text' => 'Mejor Precio',  'accent' => 'from-emerald-500 to-teal-400'],
    ['text' => 'Mayor Cobertura','accent' => 'from-purple-500 to-pink-400'],
];
?>
<section class="max-w-5xl mx-auto px-4 py-10">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-3">Planes Destacados</h2>
        <p class="text-gray-500 text-sm max-w-lg mx-auto">Seleccionamos los mejores planes por cobertura, precio y prestadores. Datos actualizados de la Superintendencia de Salud.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <?php foreach ($plans as $i => $plan): $bg = $backgrounds[$i]; $badge = $badges[$i]; ?>
        <div class="group relative rounded-2xl overflow-hidden min-h-[420px] flex flex-col justify-end transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl"
             style="background-image: url('<?= BASE_URL . $bg ?>'); background-size: cover; background-position: center;">

            <!-- Dark gradient overlay (darker at bottom for text, lighter at top) -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/95 via-gray-900/50 to-gray-900/20 group-hover:from-gray-900/90 group-hover:via-gray-900/45 transition-colors duration-500"></div>

            <!-- Badge flotante -->
            <div class="absolute top-4 left-1/2 -translate-x-1/2 z-20">
                <span class="bg-gradient-to-r <?= $badge['accent'] ?> text-white text-xs font-bold px-5 py-1.5 rounded-full shadow-lg">
                    <?= $badge['text'] ?>
                </span>
            </div>

            <!-- Contenido -->
            <div class="relative z-10 p-6 pt-2 text-center text-white">
                <!-- Logo ISAPRE (si existe) -->
                <?php
                $logoPath = '/img/' . strtolower(str_replace(' ', '', $plan['isapre'])) . '.png';
                $logoFull = __DIR__ . '/..' . $logoPath;
                ?>
                <?php if (file_exists($logoFull)): ?>
                <img src="<?= BASE_URL . $logoPath ?>" alt="<?= htmlspecialchars($plan['isapre']) ?>" class="h-8 mx-auto mb-3 object-contain brightness-0 invert opacity-90">
                <?php else: ?>
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm text-white rounded-xl flex items-center justify-center mx-auto mb-3 text-lg font-bold border border-white/30">
                    <?= mb_substr($plan['isapre'], 0, 1) ?>
                </div>
                <?php endif; ?>

                <div class="text-xs text-white/60 font-medium uppercase tracking-widest mb-1"><?= htmlspecialchars($plan['isapre']) ?></div>
                <h3 class="text-lg font-bold text-white mb-4 leading-tight"><?= htmlspecialchars($plan['nombre']) ?></h3>

                <!-- Stats -->
                <div class="flex justify-center gap-5 text-sm text-white/80 mb-5">
                    <div class="flex flex-col items-center">
                        <span class="text-xl font-bold text-white"><?= $plan['hosp'] ?>%</span>
                        <span class="text-[10px] text-white/50 uppercase tracking-wide mt-0.5">Hospital</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-xl font-bold text-white"><?= $plan['amb'] ?>%</span>
                        <span class="text-[10px] text-white/50 uppercase tracking-wide mt-0.5">Ambulatorio</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span class="text-xl font-bold text-white"><?= $plan['prestadores'] ?></span>
                        <span class="text-[10px] text-white/50 uppercase tracking-wide mt-0.5">Prestadores</span>
                    </div>
                </div>

                <!-- Precio -->
                <div class="mb-5">
                    <div class="text-3xl font-extrabold text-white"><?= number_format($plan['uf'], 2, ',', '.') ?> <span class="text-lg font-medium text-white/70">UF</span></div>
                    <div class="text-xs text-white/50 mt-0.5">por mes · precio base</div>
                </div>

                <!-- CTA -->
                <a href="<?= BASE_URL ?>/planes/detalle/?codigo=<?= urlencode($plan['codigo']) ?>#comparador"
                   class="inline-flex items-center justify-center w-full gap-2 bg-white/15 backdrop-blur-sm hover:bg-white/25 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 text-sm border border-white/20 hover:border-white/40 group/btn">
                    Ver plan
                    <span class="group-hover/btn:translate-x-1 transition-transform duration-300">→</span>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-10">
        <a href="<?= BASE_URL ?>/planes/comparador/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
            Ver todos los planes en el comparador
            <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
        </a>
    </div>
</section>