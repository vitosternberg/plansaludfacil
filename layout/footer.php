<?php
/**
 * layout/footer.php
 * Contenido del pie de p���gina y etiquetas de cierre HTML.
 * Ubicaci���n: tu_proyecto_raiz/layout/footer.php
 *
 * [VERSION CONTROL] - Nueva Versi���n: 2025-07-06
 * - Contiene el footer y las etiquetas de cierre `</body>` y `</html>`.
 * - Incluye el JavaScript global para el men��� m���vil.
 */
?>
<footer class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-6 mt-auto footer-gradient">
    <div class="container mx-auto px-4 text-center text-sm">
        <p>&copy; 2025 Plan Salud Facil. Todos los derechos reservados.</p>
        <div class="mt-2 space-x-4">
            <a href="/nosotros/privacidad" class="hover:text-blue-200 transition">Politica de Privacidad</a>
            <a href="#" class="hover:text-blue-200 transition">Terminos de Servicio</a>
        </div>
    </div>
</footer>

<script>
    // [VERSION CONTROL] - L���gica JavaScript para el men��� m���vil - 2025-07-06
    // Estas referencias deben coincidir con los IDs en tu `header_content.php`
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuClose = document.getElementById('menu-close');
    const menuOverlay = document.getElementById('menu-overlay');

    function toggleMobileMenu() {
        if (mobileMenu) {
            mobileMenu.classList.toggle('open'); // Usa la clase 'open' para el transform
        }
        if (menuOverlay) {
            menuOverlay.classList.toggle('open'); // Usa la clase 'open' para la opacidad/visibilidad
        }
        // Controla el scroll del body cuando el men��� est��� abierto
        if (mobileMenu.classList.contains('open')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }

    // Aseg���rate de que los elementos existan antes de a���adir los event listeners
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleMobileMenu);
    }
    if (menuClose) {
        menuClose.addEventListener('click', toggleMobileMenu);
    }
    if (menuOverlay) {
        menuOverlay.addEventListener('click', toggleMobileMenu);
    }

    // [VERSION CONTROL] - Lgica de bsqueda (ejemplo) - 2025-07-06
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                // alert(`Buscando: ${this.value}`); // Descomentar para depuracin
                // Aqu ir la lgica real de bsqueda
            }
        });
    });
</script>
<script src="<?= BASE_URL ?>/js/validaciones.js"></script>
</body>
</html>
