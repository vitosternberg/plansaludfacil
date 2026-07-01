<?php
// components/hero_moderno.php
// Variables esperadas (opcionales porque tienen default): $titulo, $subtitulo, $cta_texto, $cta_link
$titulo = $titulo ?? 'Encuentra tu Plan de Salud Ideal';
$subtitulo = $subtitulo ?? 'Asesoría gratuita y experta para elegir la mejor Isapre según tu perfil de salud y familia.';
$cta_texto = $cta_texto ?? 'Cotizar Ahora';
$cta_link = $cta_link ?? '/servicios/cambio-de-isapre#formulario-contacto';
?>
<div class="relative mb-12 md:mb-32">
    <!-- Hero Background with Curved Bottom -->
    <section class="hero-bg relative pt-8 md:pt-24 pb-20 md:pb-48 px-4 text-center rounded-b-[50px] md:rounded-b-[100px] shadow-lg">
        <div class="relative z-10 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-2 md:mb-6 leading-none tracking-tight drop-shadow-xl">
                <?= htmlspecialchars($titulo) ?>
            </h1>
            <p class="text-lg md:text-xl text-white mb-10 max-w-2xl mx-auto drop-shadow-md font-medium">
                <?= htmlspecialchars($subtitulo) ?>
            </p>
            
        </div>
    </section>

    <!-- Floating Action Bar (Overlapping) -->
    <div class="relative md:absolute left-0 right-0 -mt-24 md:mt-0 md:-bottom-12 z-20 px-4">
        <form action="<?= BASE_URL ?>/servicios/cambio-de-isapre" method="GET" onsubmit="this.action = this.action + '#formulario-contacto'; return true;" class="max-w-5xl mx-auto bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] p-4 md:p-6 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4 transition-all relative">
            
            <!-- Mobile Avatar (Only on mobile) -->
            <div class="block md:hidden absolute -top-10 left-1/2 transform -translate-x-1/2 z-30">
                <img src="<?= BASE_URL ?>/img/asesor_movil.jpg" alt="Asesor Plan Salud Fácil" class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover">
            </div>
            
            <div class="flex-1 w-full relative group mt-8 md:mt-0">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Renta Líquida</label>
                <div class="relative">
                    <input type="number" name="income" placeholder="Ej. 1500000" class="w-full appearance-none border-2 border-gray-100 bg-gray-50 text-gray-800 font-semibold rounded-xl px-4 py-3 focus:ring-0 focus:border-[#00d2ff] transition-colors">
                    <iconify-icon icon="mdi:cash" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></iconify-icon>
                </div>
            </div>
            
            <div class="hidden md:block w-px h-12 bg-gray-200"></div>
            
            <div class="flex-1 w-full relative group">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Edad</label>
                <input type="number" name="age" placeholder="Ej. 30" class="w-full border-2 border-gray-100 bg-gray-50 text-gray-800 font-semibold rounded-xl px-4 py-3 focus:ring-0 focus:border-[#00d2ff] transition-colors">
            </div>
            
            <div class="hidden md:block w-px h-12 bg-gray-200"></div>

            <div class="flex-1 w-full relative group">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">¿Tienes Cargas?</label>
                <div class="relative">
                    <select name="cargas" class="w-full appearance-none border-2 border-gray-100 bg-gray-50 text-gray-800 font-semibold rounded-xl px-4 py-3 focus:ring-0 focus:border-[#00d2ff] cursor-pointer transition-colors">
                        <option value="">Elige una opción</option>
                        <option value="0">Soy solo yo</option>
                        <option value="1">1 carga</option>
                        <option value="2">2 cargas</option>
                        <option value="3+">3 o más</option>
                    </select>
                    <iconify-icon icon="mdi:chevron-down" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></iconify-icon>
                </div>
            </div>

            <div class="mt-2 md:mt-0 w-full md:w-auto">
                <button type="submit" class="block w-full text-center bg-gradient-to-r from-[#00d2ff] to-[#0284c7] hover:from-[#0284c7] hover:to-[#00d2ff] text-white font-extrabold py-4 px-8 rounded-xl shadow-lg transition-transform hover:-translate-y-1 whitespace-nowrap">
                    Buscar Planes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.hero-bg {
    /* Mantenemos la imagen original pero le damos un tinte azul moderno alineado a nuestra marca */
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.85) 0%, rgba(0, 210, 255, 0.6) 100%), url('<?= BASE_URL ?>/img/hero_familia.jpg');
    background-size: cover;
    background-position: center 35%;
}
</style>
