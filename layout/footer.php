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
<!-- Modal de WhatsApp -->
<div id="wsp-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <!-- Fondo oscuro -->
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeWspModal()"></div>
    
    <!-- Contenedor del Modal -->
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4 transform transition-all">
        <button onclick="closeWspModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
            <iconify-icon icon="mdi:close" width="24"></iconify-icon>
        </button>
        
        <div class="text-center mb-6">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <iconify-icon icon="mdi:whatsapp" width="32" class="text-green-500"></iconify-icon>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">Habla con un Asesor</h3>
            <p class="text-gray-500 text-sm mt-2">Déjanos tus datos para brindarte una atención más personalizada.</p>
        </div>

        <form id="wsp-form" onsubmit="event.preventDefault(); submitWspForm();">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tu Nombre</label>
                <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-colors" placeholder="Ej. Juan Pérez">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono (9 dígitos)</label>
                <input type="tel" name="phone" required pattern="[0-9]{9}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-colors" placeholder="9 1234 5678">
            </div>
            
            <div id="wsp-msg" class="hidden mb-4 text-sm font-medium p-3 rounded-lg text-center"></div>

            <button type="submit" id="wsp-submit-btn" class="w-full flex justify-center items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-colors">
                <iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon>
                Continuar al Chat
            </button>
        </form>
    </div>
</div>

<script>
    function openWspModal() {
        const modal = document.getElementById('wsp-modal');
        modal.classList.remove('hidden');
        // Ocultar menú móvil si está abierto
        const mobileMenu = document.getElementById('mobile-menu');
        const menuOverlay = document.getElementById('menu-overlay');
        if (mobileMenu && mobileMenu.classList.contains('open')) {
            mobileMenu.classList.remove('open');
            menuOverlay.classList.remove('open');
            document.body.style.overflow = 'auto';
        }
    }

    function closeWspModal() {
        document.getElementById('wsp-modal').classList.add('hidden');
    }

    async function submitWspForm() {
        const form = document.getElementById('wsp-form');
        const btn = document.getElementById('wsp-submit-btn');
        const msg = document.getElementById('wsp-msg');
        
        const name = form.querySelector('[name="name"]').value;
        const phone = form.querySelector('[name="phone"]').value;

        btn.disabled = true;
        btn.innerHTML = '<iconify-icon icon="mdi:loading" width="20" class="mr-2 animate-spin"></iconify-icon> Conectando...';
        msg.classList.add('hidden');

        try {
            const response = await fetch('<?= BASE_URL ?>/guardar_whatsapp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: name, phone: phone })
            });
            const data = await response.json();

            if (data.success) {
                // Redirigir a whatsapp real
                const whatsappNumber = '56952282339'; // El nro de la empresa
                const text = encodeURIComponent(`Hola, soy ${name}. Quisiera asesoría para cotizar un plan de Isapre.`);
                window.location.href = `https://wa.me/${whatsappNumber}?text=${text}`;
                
                setTimeout(() => {
                    closeWspModal();
                    form.reset();
                    btn.disabled = false;
                    btn.innerHTML = '<iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon> Continuar al Chat';
                }, 1000);
            } else {
                throw new Error(data.message || 'Error al guardar los datos');
            }
        } catch (error) {
            msg.textContent = error.message;
            msg.className = 'mb-4 text-sm font-medium p-3 rounded-lg text-center bg-red-50 text-red-700';
            btn.disabled = false;
            btn.innerHTML = '<iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon> Continuar al Chat';
        }
    }
</script>
<script src="<?= BASE_URL ?>/js/validaciones.js?v=<?= time() ?>"></script>
</body>
</html>
