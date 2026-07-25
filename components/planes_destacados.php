<?php
/**
 * components/planes_destacados.php
 * Carrusel responsivo con 6 planes destacados.
 * Auto-scroll + botones ← → + pausa al hover.
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

// ── Seleccionar 6 planes (uno por ISAPRE distinta) ──
$plans = [];

// 1. Mejor cobertura
usort($cache, function($a,$b) { return ($b['hosp'] + $b['amb']) - ($a['hosp'] + $a['amb']); });
foreach ($cache as $p) { if (!in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[] = $p; break; } }

// 2. Más económico
usort($cache, function($a,$b) { return $a['uf'] - $b['uf']; });
foreach ($cache as $p) { if ($p['hosp'] >= 60 && $p['amb'] >= 50 && !in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[] = $p; break; } }

// 3. Mejor balance
usort($cache, function($a,$b) { return ($b['hosp'] + $b['amb'] - $b['uf']*3) - ($a['hosp'] + $a['amb'] - $a['uf']*3); });
foreach ($cache as $p) { if (!in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[] = $p; break; } }

// 4. Mayor red
usort($cache, function($a,$b) { return $b['prestadores'] - $a['prestadores']; });
foreach ($cache as $p) { if (!in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[] = $p; break; } }

// 5. Top hospitalario
usort($cache, function($a,$b) { return $b['hosp'] - $a['hosp']; });
foreach ($cache as $p) { if (!in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[] = $p; break; } }

// 6. Para jóvenes
usort($cache, function($a,$b) { return ($b['amb'] - $b['uf']*5) - ($a['amb'] - $a['uf']*5); });
foreach ($cache as $p) { if (!in_array($p['isapre'], array_column($plans, 'isapre'))) { $plans[] = $p; break; } }

if (count($plans) < 4) return;

$backgrounds = ['/img/hero_familia.jpg','/img/madre_orgullosa.jpg','/img/mama_hijas.jpg','/img/bebe_sueter_rojo.jpg','/img/madre_orgullosa.jpg','/img/mama_hijas.jpg'];
$badges = [
    ['text'=>'Recomendado','accent'=>'from-blue-500 to-cyan-400'],
    ['text'=>'Mejor Precio','accent'=>'from-emerald-500 to-teal-400'],
    ['text'=>'Mayor Cobertura','accent'=>'from-purple-500 to-pink-400'],
    ['text'=>'Más Prestadores','accent'=>'from-amber-500 to-orange-400'],
    ['text'=>'Top Hospitalario','accent'=>'from-red-500 to-rose-400'],
    ['text'=>'Para Jóvenes','accent'=>'from-green-500 to-lime-400'],
];
?>
<section class="max-w-6xl mx-auto px-4 py-10">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-3">Planes Destacados</h2>
        <p class="text-gray-500 text-sm max-w-lg mx-auto">Seleccionamos los mejores planes por cobertura, precio y prestadores. Datos actualizados de la Superintendencia de Salud.</p>
    </div>

    <!-- Carrusel con botones -->
    <div id="carouselPlanes" class="relative group">
        <!-- Botón Anterior -->
        <button onclick="carouselScroll(-1)" class="absolute left-0 top-1/2 -translate-y-1/2 z-30 bg-white/90 hover:bg-white text-gray-700 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 -ml-2 md:-ml-5" aria-label="Anterior">
            ‹
        </button>

        <!-- Track -->
        <div id="carousel-track" class="flex gap-5 transition-transform duration-500 ease-out" style="will-change: transform;">
            <?php foreach ($plans as $i => $plan):
                $bg = $backgrounds[$i % count($backgrounds)];
                $badge = $badges[$i % count($badges)];
            ?>
            <div class="carousel-card flex-shrink-0 w-[300px] md:w-[340px] relative rounded-2xl overflow-hidden min-h-[400px] flex flex-col justify-end transition-all duration-300 hover:scale-[1.02]"
                 style="background-image: url('<?= BASE_URL . $bg ?>'); background-size: cover; background-position: center;">

                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/95 via-gray-900/50 to-gray-900/20"></div>

                <div class="absolute top-4 left-1/2 -translate-x-1/2 z-20">
                    <span class="bg-gradient-to-r <?= $badge['accent'] ?> text-white text-xs font-bold px-5 py-1.5 rounded-full shadow-lg whitespace-nowrap">
                        <?= $badge['text'] ?>
                    </span>
                </div>

                <div class="relative z-10 p-6 pt-2 text-center text-white">
                    <?php
                    $logoName = strtolower(str_replace(' ', '', $plan['isapre']));
                    $logoPath = '/img/' . $logoName . '.png';
                    $logoFull = __DIR__ . '/..' . $logoPath;
                    if (file_exists($logoFull)):
                    ?>
                    <img src="<?= BASE_URL . $logoPath ?>" alt="<?= htmlspecialchars($plan['isapre']) ?>" class="h-8 mx-auto mb-3 object-contain brightness-0 invert opacity-90">
                    <?php else: ?>
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm text-white rounded-xl flex items-center justify-center mx-auto mb-3 text-lg font-bold border border-white/30">
                        <?= mb_substr($plan['isapre'], 0, 1) ?>
                    </div>
                    <?php endif; ?>

                    <div class="text-xs text-white/60 font-medium uppercase tracking-widest mb-1"><?= htmlspecialchars($plan['isapre']) ?></div>
                    <h3 class="text-lg font-bold text-white mb-4 leading-tight"><?= htmlspecialchars($plan['nombre']) ?></h3>

                    <div class="flex justify-center gap-5 text-sm text-white/80 mb-5">
                        <div class="flex flex-col items-center"><span class="text-xl font-bold text-white"><?= $plan['hosp'] ?>%</span><span class="text-[10px] text-white/50 uppercase mt-0.5">Hospital</span></div>
                        <div class="flex flex-col items-center"><span class="text-xl font-bold text-white"><?= $plan['amb'] ?>%</span><span class="text-[10px] text-white/50 uppercase mt-0.5">Ambulatorio</span></div>
                        <div class="flex flex-col items-center"><span class="text-xl font-bold text-white"><?= $plan['prestadores'] ?></span><span class="text-[10px] text-white/50 uppercase mt-0.5">Prestadores</span></div>
                    </div>

                    <div class="mb-5">
                        <div class="text-3xl font-extrabold text-white"><?= number_format($plan['uf'], 2, ',', '.') ?> <span class="text-lg font-medium text-white/70">UF</span></div>
                        <div class="text-xs text-white/50 mt-0.5">por mes · precio base</div>
                    </div>

                    <a href="<?= BASE_URL ?>/planes/detalle/?codigo=<?= urlencode($plan['codigo']) ?>#comparador"
                       class="inline-flex items-center justify-center w-full gap-2 bg-white/15 backdrop-blur-sm hover:bg-white/25 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 text-sm border border-white/20 hover:border-white/40">
                        Ver plan <span class="group-hover/btn:translate-x-1 transition-transform duration-300">→</span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Botón Siguiente -->
        <button onclick="carouselScroll(1)" class="absolute right-0 top-1/2 -translate-y-1/2 z-30 bg-white/90 hover:bg-white text-gray-700 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 -mr-2 md:-mr-5" aria-label="Siguiente">
            ›
        </button>
    </div>

    <!-- JS: navegación manual + auto-scroll -->
    <script>
    (function() {
        var track = document.getElementById('carousel-track');
        var container = document.getElementById('carouselPlanes');
        if (!track || !container) return;

        var cards = track.querySelectorAll('.carousel-card');
        var cardWidth = cards[0] ? cards[0].offsetWidth + 20 : 340; // 20 = gap
        var totalCards = cards.length;
        var currentIndex = 0;
        var autoTimer = null;
        var isPaused = false;
        var RESUME_DELAY = 4000; // reanudar auto-scroll después de 4s sin interacción

        function scrollToIndex(idx, smooth) {
            // Clamp infinito
            if (idx < 0) idx = totalCards - 1;
            if (idx >= totalCards) idx = 0;
            currentIndex = idx;
            track.style.transition = smooth !== false ? 'transform 0.5s ease-out' : 'none';
            track.style.transform = 'translateX(' + (-currentIndex * cardWidth) + 'px)';
        }

        window.carouselScroll = function(dir) {
            scrollToIndex(currentIndex + dir);
            resetAutoTimer();
        };

        function autoAdvance() {
            if (isPaused) return;
            scrollToIndex(currentIndex + 1);
            autoTimer = setTimeout(autoAdvance, 3500);
        }

        function resetAutoTimer() {
            clearTimeout(autoTimer);
            autoTimer = setTimeout(autoAdvance, RESUME_DELAY);
        }

        // Pausar al hover
        container.addEventListener('mouseenter', function() { isPaused = true; });
        container.addEventListener('mouseleave', function() { isPaused = false; resetAutoTimer(); });

        // Touch swipe
        var touchStartX = 0;
        container.addEventListener('touchstart', function(e) { touchStartX = e.touches[0].clientX; });
        container.addEventListener('touchend', function(e) {
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) {
                window.carouselScroll(diff > 0 ? 1 : -1);
            }
        });

        // Recalcular al redimensionar
        window.addEventListener('resize', function() {
            cardWidth = cards[0] ? cards[0].offsetWidth + 20 : 340;
            scrollToIndex(currentIndex, false);
        });

        // Iniciar
        autoTimer = setTimeout(autoAdvance, 3000);
    })();
    </script>

    <div class="text-center mt-10">
        <a href="<?= BASE_URL ?>/planes/comparador/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
            Ver todos los planes en el comparador
            <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
        </a>
    </div>
</section>
