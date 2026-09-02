<?php
/**
 * components/planes_destacados.php
 * Carrusel responsivo con 6 planes destacados.
 * Auto-scroll + botones ← → + pausa al hover.
 * Uso: render_component('planes_destacados')
 */

require_once __DIR__ . '/../core/planes_data_provider.php';

$plans = pd_get_destacados();
if (count($plans) < 4) return;

$backgrounds = ['/img/hero_familia.jpg','/img/madre_orgullosa.jpg','/img/mama_hijas.jpg','/img/prestadores_red.jpg','/img/hospitalario_top.jpg','/img/joven_activo.png'];
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

    <div id="carouselPlanes" class="relative group overflow-hidden">
        <button onclick="carouselPrev()" class="absolute left-0 top-1/2 -translate-y-1/2 z-30 bg-white/90 hover:bg-white text-gray-700 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 md:-ml-5" aria-label="Anterior">‹</button>

        <div id="carousel-track" class="flex gap-5" style="will-change: transform;">
            <?php for ($dup = 0; $dup < 2; $dup++): ?>
            <?php foreach ($plans as $i => $plan):
                $bg = $backgrounds[$i % count($backgrounds)];
                $badge = $badges[$i % count($badges)];
            ?>
            <div class="carousel-card flex-shrink-0 w-[300px] md:w-[340px] relative rounded-2xl overflow-hidden min-h-[400px] flex flex-col justify-end transition-all duration-300 hover:scale-[1.02]"
                 style="background-image: url('<?= BASE_URL . $bg ?>'); background-size: cover; background-position: center;">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/95 via-gray-900/50 to-gray-900/20"></div>
                <div class="absolute top-4 left-1/2 -translate-x-1/2 z-20">
                    <span class="bg-gradient-to-r <?= $badge['accent'] ?> text-white text-xs font-bold px-5 py-1.5 rounded-full shadow-lg whitespace-nowrap"><?= $badge['text'] ?></span>
                </div>
                <div class="relative z-10 p-6 pt-2 text-center text-white">
                    <?php
                    $logoName = strtolower(str_replace(' ', '', $plan['isapre']));
                    $logoPath = '/img/' . $logoName . '.png';
                    $logoFull = __DIR__ . '/..' . $logoPath;
                    if (file_exists($logoFull)):
                        $imgInfo = @getimagesize($logoFull);
                        $isPng = ($imgInfo && $imgInfo[2] === IMAGETYPE_PNG);
                    ?>
                    <?php if ($isPng): ?>
                    <img src="<?= BASE_URL . $logoPath ?>" alt="<?= htmlspecialchars($plan['isapre']) ?>" class="h-8 mx-auto mb-3 object-contain brightness-0 invert opacity-90">
                    <?php else: ?>
                    <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1 mb-3">
                        <img src="<?= BASE_URL . $logoPath ?>" alt="<?= htmlspecialchars($plan['isapre']) ?>" class="h-7 object-contain">
                    </div>
                    <?php endif; ?>
                    <?php else: ?>
                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm text-white rounded-xl flex items-center justify-center mx-auto mb-3 text-lg font-bold border border-white/30"><?= mb_substr($plan['isapre'], 0, 1) ?></div>
                    <?php endif; ?>
                    <div class="text-xs text-white/60 font-medium uppercase tracking-widest mb-1"><?= htmlspecialchars($plan['isapre']) ?></div>
                    <h3 class="text-lg font-bold text-white mb-4 leading-tight"><?= htmlspecialchars($plan['nombre']) ?></h3>
                    <div class="flex justify-center gap-5 text-sm text-white/80 mb-5">
                        <div class="flex flex-col items-center"><span class="text-xl font-bold text-white"><?= $plan['cobertura_hosp_pct'] ?>%</span><span class="text-[10px] text-white/50 uppercase mt-0.5">Hospital</span></div>
                        <div class="flex flex-col items-center"><span class="text-xl font-bold text-white"><?= $plan['cobertura_amb_pct'] ?>%</span><span class="text-[10px] text-white/50 uppercase mt-0.5">Ambulatorio</span></div>
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
            <?php endfor; ?>
        </div>

        <button onclick="carouselNext()" class="absolute right-0 top-1/2 -translate-y-1/2 z-30 bg-white/90 hover:bg-white text-gray-700 w-10 h-10 rounded-full shadow-lg flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 md:-mr-5" aria-label="Siguiente">›</button>
    </div>

    <script>
    (function(){
        var track = document.getElementById('carousel-track');
        var ct = document.getElementById('carouselPlanes');
        if(!track||!ct)return;
        var cards = track.querySelectorAll('.carousel-card');
        var N = cards.length/2; // cards originales (sin duplicado)
        var W = cards[0]?cards[0].offsetWidth+20:340;
        var idx = 0, timer = null;

        function go(i,anim){
            idx=i; track.style.transition=anim?'transform 0.5s ease-out':'none';
            track.style.transform='translateX('+(-idx*W)+'px)';
        }
        track.addEventListener('transitionend',function(){
            while(idx>=N){ idx-=N; track.style.transition='none'; track.style.transform='translateX('+(-idx*W)+'px)'; track.offsetHeight; }
            while(idx<0){ idx+=N; track.style.transition='none'; track.style.transform='translateX('+(-idx*W)+'px)'; track.offsetHeight; }
        });

        function next(){ go(idx+1,true); resume(); }
        function prev(){
            if(idx<=0){ idx=N; track.style.transition='none'; track.style.transform='translateX('+(-idx*W)+'px)'; track.offsetHeight; }
            go(idx-1,true); resume();
        }
        window.carouselNext=next; window.carouselPrev=prev;

        function step(){ next(); }
        function resume(){ clearTimeout(timer); timer=setTimeout(step,4000); }

        ct.addEventListener('pointerenter',function(){ clearTimeout(timer); });
        ct.addEventListener('pointerleave',resume);

        var tx=0;
        ct.addEventListener('touchstart',function(e){ tx=e.touches[0].clientX; });
        ct.addEventListener('touchend',function(e){ var d=tx-e.changedTouches[0].clientX; if(Math.abs(d)>40)d>0?next():prev(); },{passive:true});

        window.addEventListener('resize',function(){ W=cards[0]?cards[0].offsetWidth+20:340; go(idx,false); });

        timer=setTimeout(step,3000);
    })();
    </script>

    <div class="text-center mt-10">
        <a href="<?= BASE_URL ?>/planes/comparador/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
            Ver todos los planes en el comparador
            <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
        </a>
    </div>
</section>
