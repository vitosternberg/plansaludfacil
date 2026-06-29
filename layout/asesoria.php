
    <!-- Sección de Beneficios -->
    <section class="benefits-section text-white py-20 md:py-28 px-4">
        <div class="container mx-auto max-w-4xl text-center">
            <!-- Título -->
            <h2 class="text-3xl md:text-4xl font-bold mb-4 animate-fade-in">
                Beneficios de Nuestra Asesoría
            </h2>
            
            <!-- Subtítulo -->
            <p class="text-xl md:text-2xl font-light mb-8 animate-fade-in delay-100">
                Descubre cómo podemos transformar tu experiencia en salud
            </p>
            
            <!-- Texto descriptivo -->
            <div class="bg-black bg-opacity-50 rounded-xl p-6 md:p-8 backdrop-blur-sm animate-fade-in delay-200">
                <p class="text-lg md:text-xl leading-relaxed mb-6">
                    Nuestro equipo de expertos te brinda acompañamiento personalizado, 
                    ahorro garantizado y transparencia en cada paso. Deja atrás la 
                    complejidad del sistema y disfruta de una atención clara y cercana.
                </p>
                
                <!-- Botón de acción -->
                <a href="<?= BASE_URL ?>/servicios/planes-individuales" class="cta-gradient inline-block text-white font-bold py-3 px-8 rounded-lg text-lg">
                    Quiero mi asesoría gratuita
                </a>
            </div>
        </div>
    </section>

    <!-- Sección de iconos/beneficios detallados (opcional) -->
    <div class="container mx-auto px-4 py-12 max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Beneficio 1 -->
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Acompañamiento antes y despues de contratar</h3>
                <p class="text-gray-600">Te guiamos en cada paso del proceso con atención personalizada.</p>
            </div>
            
            <!-- Beneficio 2 -->
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Siempre buscaremos el mejor y mas conveniente Plan de Salud</h3>
                <p class="text-gray-600">Encontramos la mejor opción para ti y tu familia.</p>
            </div>
            
            <!-- Beneficio 3 -->
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <div class="text-blue-600 text-4xl mb-4">
                    <i class="fas fa-search-dollar"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Transparencia</h3>
                <p class="text-gray-600">Sin letra chica ni sorpresas. Todo claro desde el inicio.</p>
            </div>
        </div>
    </div>

    <!-- Font Awesome para íconos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <!-- Animaciones simples -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pequeña animación para los elementos
            const animatedElements = document.querySelectorAll('.animate-fade-in');
            animatedElements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = 1;
                    el.style.transform = 'translateY(0)';
                }, 150 * index);
            });
        });
    </script>
