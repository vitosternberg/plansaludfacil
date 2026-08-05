<!-- ⚠️ CAMBIO 2026-08-05: Modal extraído a components/whatsapp_modal.php.
     Este archivo ahora solo contiene el botón flotante. La función openWspModal()
     está disponible globalmente desde el modal incluido en plantilla.php. -->
<!-- Botón de WhatsApp flotante -->
<div class="fixed bottom-6 right-6 z-50">
    <button id="whatsapp-button" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-all duration-300 transform hover:scale-110">
        <iconify-icon icon="mdi:whatsapp" width="32"></iconify-icon>
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var whatsappButton = document.getElementById('whatsapp-button');
    if (!whatsappButton) return;

    // Abrir modal (o scroll al formulario si estamos en página con formulario)
    whatsappButton.addEventListener('click', function() {
        // Buscar formularios por ID — si existe, hacer scroll en vez de abrir WhatsApp
        var formTarget = document.getElementById('comparador')
                      || document.getElementById('form-individual')
                      || document.getElementById('form-familia');
        if (formTarget) {
            formTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
            setTimeout(function() {
                var firstInput = formTarget.querySelector('input, select, textarea');
                if (firstInput) firstInput.focus();
            }, 500);
            return;
        }
        // Delegar al modal global
        if (typeof openWspModal === 'function') {
            openWspModal();
        }
    });
});
</script>
