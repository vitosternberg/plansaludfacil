<!-- Barra de contacto superior -->
<div class="bg-blue-900 text-white text-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-2">
            <!-- Contacto WhatsApp y Email -->
            <div class="flex items-center space-x-4">
                <!-- CAMBIO 2026-08-05: wa.me reemplazado por openWspModal() para capturar datos antes de WhatsApp -->
                <button onclick="openWspModal()" class="flex items-center hover:text-blue-200 transition bg-transparent border-none cursor-pointer text-white text-sm">
                    <iconify-icon icon="mdi:whatsapp" width="16" class="mr-1"></iconify-icon>
                    <span class="hidden sm:inline">+56 9 5228 2339</span>
                </button>
                <a href="mailto:contacto@planesdeisapre.cl" class="flex items-center hover:text-blue-200 transition">
                    <iconify-icon icon="mdi:email-outline" width="16" class="mr-1"></iconify-icon>
                    <span class="hidden sm:inline">contacto@plansaludfacil.cl</span>
                </a>
            </div>
            
            <!-- Opcional: Horario de atencion -->
            <div class="hidden md:flex items-center">
                <iconify-icon icon="mdi:clock-outline" width="14" class="mr-1"></iconify-icon>
                <span>Lunes a Viernes: 9:00 - 18:00 hrs</span>
            </div>
        </div>
    </div>
</div>