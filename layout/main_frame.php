<!-- Sección 2 - Aviso UX Mejorada -->
<section class="container mx-auto px-4 py-12 max-w-4xl">
    <!-- Tarjeta con degradado elegante -->
    <div class="rounded-xl shadow-xl overflow-hidden p-8 md:p-10 text-center
                bg-gradient-to-b from-blue-600 via-blue-800 to-gray-700
                border border-blue-200 border-opacity-90
                transform transition-all hover:scale-[1.01] hover:shadow-2xl">
        
        <!-- Titulo principal -->
        <h1 class="text-5xl md:text-3xl font-bold text-white mb-4">
            ¿Cansado de Pagar de Más en tu Isapre?
            <span class="block text-blue-300 mt-2 text-xl">No contrates una Isapre sin Asesoría. Estamos a tu lado para guiarte.</span>
        </h1>
        
        <!-- Subtitulo -->
        <p class="text-blue-100 text-lg md:text-xl mb-8 max-w-2xl mx-auto">
            En menos de 5 minutos, completas tus datos y deja que nuestra experiencia se haga cargo. 
            <span class="block text-blue-200">¡Sin letra chica ni sorpresas!</span>
        </p>
        
        <!-- Botón de acción mejorado -->
        <div class="mt-6">
            <a href="<?= BASE_URL ?>/servicios/planes-individuales" 
               class="inline-block bg-gradient-to-r from-blue-500 to-blue-700
                      hover:from-blue-400 hover:to-blue-600
                      text-white font-bold py-3 px-8 rounded-lg text-lg
                      transition-all duration-300 transform hover:scale-105
                      shadow-lg hover:shadow-blue-500/30">
                Contacta a un Experto
            </a>
        </div>
        
        <!-- Elemento decorativo mejorado -->
        <div class="mt-8 flex justify-center">
            <div class="flex items-center text-sm text-blue-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-blue-200">Proceso rápido y sin compromiso</span>
            </div>
        </div>
    </div>
</section>

<!-- Estilos personalizados -->
<style>
    .aviso-ux-con-fondo {
        background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.9)), 
                          url('https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 1rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
    
    @media (max-width: 768px) {
        .aviso-ux-con-fondo {
            background-attachment: scroll;
        }
    }
</style>