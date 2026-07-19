<?php
/**
 * components/cta_flotante.php
 * WhatsApp sticky button flotante (siempre visible en mobile + desktop).
 *
 * Variables esperadas:
 *   $cta_numero   — Número WhatsApp sin + (string, default: '56952282339')
 *   $cta_mensaje  — Mensaje predefinido (string, opcional)
 */

$cta_numero  = $cta_numero ?? '56952282339';
$cta_mensaje = $cta_mensaje ?? 'Hola, quisiera asesoría para cotizar un plan de Isapre.';
$cta_url     = 'https://wa.me/' . $cta_numero . '?text=' . urlencode($cta_mensaje);
?>

<a href="<?= htmlspecialchars($cta_url) ?>"
   target="_blank"
   rel="noopener"
   class="fixed bottom-6 right-6 z-50 flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-5 rounded-full shadow-xl transition-transform hover:scale-110 md:hidden"
   aria-label="Hablar por WhatsApp">
    <iconify-icon icon="mdi:whatsapp" width="24"></iconify-icon>
    <span class="hidden sm:inline text-sm">WhatsApp</span>
</a>
