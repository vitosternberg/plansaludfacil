<?php
$titulo_seccion = $titulo_seccion ?? 'Nuestros Servicios';
$servicios = $servicios ?? [];
?>
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-5xl font-extrabold text-center text-gray-900 mb-12">
            <?= htmlspecialchars($titulo_seccion) ?>
        </h2>
        
        <!-- Contenedor: Carrusel en móvil, Grilla en desktop -->
        <div class="flex flex-nowrap overflow-x-auto snap-x snap-mandatory gap-6 pb-8 md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-8 md:overflow-visible md:pb-0 scrollbar-hide">
            
            <?php foreach ($servicios as $servicio): ?>
                <div class="snap-center shrink-0 w-[85vw] sm:w-[320px] md:w-auto bg-white rounded-3xl shadow-lg hover:shadow-xl transition-all flex flex-col overflow-hidden border border-gray-100 group">
                    
                    <!-- Imagen de Portada -->
                    <?php if(!empty($servicio['imagen'])): ?>
                    <div class="h-52 w-full relative overflow-hidden">
                        <img src="<?= htmlspecialchars($servicio['imagen']) ?>" alt="<?= htmlspecialchars($servicio['titulo']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <!-- Gradiente para que el ícono resalte -->
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                        <!-- Ícono superpuesto -->
                        <div class="absolute bottom-4 left-5 text-4xl bg-white/20 backdrop-blur-sm p-2 rounded-xl border border-white/30">
                            <?= $servicio['icono'] ?? '🌟' ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="h-52 w-full bg-gradient-to-br from-[#00d2ff] to-[#0284c7] flex items-center justify-center text-7xl">
                        <?= $servicio['icono'] ?? '🌟' ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Contenido -->
                    <div class="p-6 md:p-8 flex flex-col flex-1">
                        <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-3 leading-tight">
                            <?= htmlspecialchars($servicio['titulo']) ?>
                        </h3>
                        <p class="text-gray-600 mb-8 flex-1 text-sm md:text-base">
                            <?= htmlspecialchars($servicio['descripcion']) ?>
                        </p>
                        
                        <!-- Botón -->
                        <a href="<?= htmlspecialchars($servicio['link']) ?>" class="block w-full text-center bg-gray-50 text-gray-800 hover:text-white hover:bg-[#00d2ff] font-bold py-3.5 px-4 rounded-xl transition-colors border border-gray-200 hover:border-transparent shadow-sm hover:shadow-md">
                            Cotizar Ahora
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            
        </div>
    </div>
</section>

<style>
/* Ocultar barra de scroll en el carrusel móvil para que se vea más limpio */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
