<section class="container mx-auto px-4 py-2 max-w-6xl">
    <!-- Tarjeta con imagen de fondo elegante -->
    <div class="aviso-ux-con-fondo rounded-xl shadow-2xl overflow-hidden p-8 md:p-12 text-center
                border border-blue-200 border-opacity-30
                transform transition-all hover:shadow-blue-500/40">
        
        <!-- Contenido con fondo semitransparente para mejor legibilidad -->
        <div class="backdrop-blur-sm bg-blue-500/10 p-6 md:p-8 rounded-lg">
            <!-- Titulo principal mejorado -->
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
                ¿Cansado de Pagar de Más en tu Isapre?
                <span class="block text-blue-200 mt-4 text-2xl md:text-3xl font-light">
                    No contrates una Isapre sin Asesoría Profesional
                </span>
            </h1>
            
            <!-- Subtitulo mejorado -->
            <p class="text-blue-100 text-lg md:text-xl mb-8 max-w-2xl mx-auto leading-relaxed">
                En menos de 5 minutos, completas tus datos y nuestros expertos encuentran el mejor plan para ti.
                <span class="block text-blue-200 font-medium mt-2">¡Sin letra chica ni sorpresas!</span>
            </p>
            
            <!-- Botones de acción mejorados -->
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4">
                <a href="<?= BASE_URL ?>/servicios/planes-individuales" 
                   class="inline-block bg-gradient-to-r from-blue-500 to-blue-600
                          hover:from-blue-400 hover:to-blue-500
                          text-white font-semibold py-4 px-8 rounded-lg text-lg
                          transition-all duration-300 transform hover:scale-[1.03]
                          shadow-lg hover:shadow-blue-500/30 border border-blue-400">
                    Contacta a un Experto
                </a>
                <!--<a href="#comparador" 
                   class="inline-block bg-transparent border-2 border-blue-300
                          hover:bg-blue-900/30 text-blue-100 font-semibold py-4 px-8 rounded-lg text-lg
                          transition-all duration-300 transform hover:scale-[1.03]">
                    Comparar Planes-->
                </a>
            </div>
            
            <!-- Elemento de confianza mejorado -->
            <div class="mt-10 flex flex-wrap justify-center gap-6">
                <div class="flex items-center text-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Sin costo de asesoría</span>
                </div>
                <div class="flex items-center text-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Proceso rápido</span>
                </div>
                <div class="flex items-center text-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Asesoría especializada</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estilos personalizados -->
<style>
    .aviso-ux-con-fondo {
        background-image: linear-gradient(rgba(3, 15, 39, 0.85), rgba(5, 45, 95, 0.9)), 
                          url('https://images.unsplash.com/photo-1450101499163-c8848c66ca85?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    @media (max-width: 768px) {
        .aviso-ux-con-fondo {
            background-attachment: scroll;
            padding: 2rem 1rem;
        }
    }
</style>