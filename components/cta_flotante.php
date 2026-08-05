<?php
/**
 * components/cta_flotante.php
 * ⚠️ CAMBIO 2026-08-05: Ahora usa openWspModal() en vez de wa.me directo.
 *    Los datos se capturan en el modal antes de redirigir a WhatsApp.
 *
 * Botón flotante sticky (siempre visible en mobile + desktop).
 */
?>
<button onclick="openWspModal()"
   class="fixed bottom-6 right-6 z-50 flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-5 rounded-full shadow-xl transition-transform hover:scale-110 md:hidden border-none cursor-pointer"
   aria-label="Hablar por WhatsApp">
    <iconify-icon icon="mdi:whatsapp" width="24"></iconify-icon>
    <span class="hidden sm:inline text-sm">WhatsApp</span>
</button>
