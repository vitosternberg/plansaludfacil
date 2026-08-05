<?php
/**
 * components/whatsapp_modal.php
 * Modal reutilizable de captura de datos antes de redirigir a WhatsApp.
 *
 * Expone la función global openWspModal() para que cualquier botón
 * del sitio pueda abrir el modal sin duplicar código.
 *
 * Los datos capturados (nombre + teléfono) se guardan en la BD
 * vía guardar_whatsapp.php antes de abrir WhatsApp.
 *
 * ⚠️ CAMBIO 2026-08-05: Extraído desde layout/wsp.php para unificar
 *    todos los botones WhatsApp del sitio. Punto de retorno: revertir
 *    este archivo + wsp.php + header.php + cta_flotante.php + hero_seo.php
 *    + seo-page.php + home.php a versiones previas a esta fecha.
 */

// Evitar inclusión múltiple del HTML del modal
if (!defined('WSP_MODAL_INCLUDED')) {
    define('WSP_MODAL_INCLUDED', true);
?>
<!-- Popup/Modal de WhatsApp (componente reutilizable) -->
<div id="whatsapp-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[9998] flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Contacto por WhatsApp</h3>
            <button id="close-wsp-modal" class="text-gray-500 hover:text-gray-700">
                <iconify-icon icon="mdi:close" width="24"></iconify-icon>
            </button>
        </div>
        
        <form id="whatsapp-form" class="space-y-4">
            <div>
                <label for="whatsapp-number" class="block text-sm font-medium text-gray-700 mb-1">Número de WhatsApp*</label>
                <input type="tel" id="whatsapp-number" name="phone" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="Ej: 56912345678">
                <p class="mt-1 text-sm text-red-600 hidden" id="wsp-phone-error">Ingrese un número válido (9 dígitos)</p>
            </div>
            
            <div>
                <label for="whatsapp-name" class="block text-sm font-medium text-gray-700 mb-1">Tu Nombre*</label>
                <input type="text" id="whatsapp-name" name="name" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="Ej: Juan Pérez">
            </div>
            
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                <iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon>
                Enviar Mensaje
            </button>
        </form>
    </div>
</div>

<script>
// ⚠️ CAMBIO 2026-08-05: openWspModal() global para unificar todos los botones WhatsApp
(function() {
    if (window._wspModalInitialized) return;
    window._wspModalInitialized = true;

    window.openWspModal = function() {
        var modal = document.getElementById('whatsapp-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Focus en el primer campo
        setTimeout(function() {
            var phoneInput = document.getElementById('whatsapp-number');
            if (phoneInput) phoneInput.focus();
        }, 100);
    };

    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('whatsapp-modal');
        if (!modal) return;
        
        var closeBtn = document.getElementById('close-wsp-modal');
        var form = document.getElementById('whatsapp-form');
        var phoneInput = document.getElementById('whatsapp-number');
        var phoneError = document.getElementById('wsp-phone-error');

        // Validación de teléfono en tiempo real
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                var phone = this.value.replace(/\D/g, '');
                this.value = phone;
                if (phone.length === 9) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                    if (phoneError) phoneError.classList.add('hidden');
                } else {
                    this.classList.remove('border-green-500');
                    this.classList.add('border-red-500');
                    if (phoneError) phoneError.classList.remove('hidden');
                }
            });
        }

        // Cerrar modal
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }

        // Cerrar al hacer clic fuera del modal
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // Enviar formulario
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var phone = phoneInput ? phoneInput.value.replace(/\D/g, '') : '';
                var nameInput = document.getElementById('whatsapp-name');
                var name = nameInput ? nameInput.value : '';
                
                if (phone.length !== 9) {
                    if (phoneError) phoneError.classList.remove('hidden');
                    return;
                }

                var submitBtn = form.querySelector('button[type="submit"]');
                var originalHTML = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="inline-block animate-spin mr-2">↻</span> Procesando...';
                submitBtn.disabled = true;

                fetch('guardar_whatsapp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        phone: phone,
                        name: name,
                        date: new Date().toISOString()
                    })
                })
                .then(function(response) {
                    if (!response.ok) throw new Error('Error al guardar los datos');
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        var message = encodeURIComponent('Hola soy ' + name + ', y tengo interés en los planes de Isapre');
                        window.open('https://wa.me/56952282339?text=' + message, '_blank');
                        modal.classList.add('hidden');
                        document.body.style.overflow = '';
                    } else {
                        throw new Error(data.message || 'Error en la respuesta');
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    alert('Hubo un error: ' + error.message);
                })
                .finally(function() {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                });
            });
        }
    });
})();
</script>
<?php
} // fin if WSP_MODAL_INCLUDED
?>
