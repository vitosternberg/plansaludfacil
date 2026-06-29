<?php
/**
 * layout/header_content.php
 * Contenido del encabezado (barra superior de contacto y navegación principal).
 * Ubicación: tu_proyecto_raiz/layout/header_content.php
 *
 * [VERSION CONTROL] - Nueva Versión: 2025-07-06
 * - Contiene la barra de contacto superior y el header principal.
 * - Incluye los IDs necesarios para el JavaScript del menú móvil.
 * - **No contiene <header> ni <body> ni <html>.**
 */

// Si necesitas el título del blog o el email del admin para el header,
// la página principal que incluya este header debe pasarlos como variables PHP.
$header_blog_title = $page_title ?? 'Plan Salud Facil'; // Asume $page_title viene de la página principal
$header_admin_email = 'contacto@plansaludfacil.cl'; // Email estático o pasado dinámicamente

?>
<!-- Barra de contacto superior -->
<div class="bg-blue-900 text-white text-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-2">
            <!-- Contacto WhatsApp y Email -->
            <div class="flex items-center space-x-4">
                <a href="https://wa.me/56952282339" class="flex items-center hover:text-blue-200 transition" target="_blank">
                    <iconify-icon icon="mdi:whatsapp" width="16" class="mr-1"></iconify-icon>
                    <span class="hidden sm:inline">+56 9 5228 2339</span>
                </a>
                <a href="mailto:<?php echo htmlspecialchars($header_admin_email); ?>" class="flex items-center hover:text-blue-200 transition">
                    <iconify-icon icon="mdi:email-outline" width="16" class="mr-1"></iconify-icon>
                    <span class="hidden sm:inline"><?php echo htmlspecialchars($header_admin_email); ?></span>
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

<!-- Header Principal -->
<header class="bg-gradient-to-r from-orange-600 to-orange-800 text-white shadow-md">
    <div class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center mr-2">
                    <!-- Espacio para logo - reemplazar con imagen real -->
                    <span class="text-blue-700 font-bold">PSF</span>
                </div>
                <span class="text-xl font-bold"><?php echo htmlspecialchars($header_blog_title); ?></span>
            </div>

            <!-- Menu Desktop (visible en md+) -->
            <nav class="hidden md:flex items-center space-x-6">
                <a href="<?= BASE_URL ?>/" class="nav-item transition flex items-center hover:text-orange-200">
                    <iconify-icon icon="mdi:home-outline" width="20" class="mr-1"></iconify-icon>
                    <span>Inicio</span>
                </a>
                
                <!-- Silo Nosotros -->
                <div class="relative group">
                    <button class="nav-item transition flex items-center hover:text-orange-200 focus:outline-none">
                        <iconify-icon icon="mdi:account-group-outline" width="20" class="mr-1"></iconify-icon>
                        <span>Nosotros</span>
                        <iconify-icon icon="mdi:chevron-down" width="16" class="ml-1"></iconify-icon>
                    </button>
                    <div class="absolute left-0 pt-4 w-48 z-50 hidden group-hover:block">
                        <div class="bg-white rounded-md shadow-lg py-1 border border-gray-100">
                            <a href="<?= BASE_URL ?>/nosotros/empresa" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Nuestra Empresa</a>
                        </div>
                    </div>
                </div>

                <!-- Silo Servicios -->
                <div class="relative group">
                    <button class="nav-item transition flex items-center hover:text-orange-200 focus:outline-none">
                        <iconify-icon icon="mdi:heart-pulse" width="20" class="mr-1"></iconify-icon>
                        <span>Servicios</span>
                        <iconify-icon icon="mdi:chevron-down" width="16" class="ml-1"></iconify-icon>
                    </button>
                    <div class="absolute left-0 pt-4 w-56 z-50 hidden group-hover:block">
                        <div class="bg-white rounded-md shadow-lg py-1 border border-gray-100">
                            <a href="<?= BASE_URL ?>/servicios/cambio-de-isapre" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Cambio de Isapre</a>
                            <a href="<?= BASE_URL ?>/servicios/planes-individuales" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Planes Individuales</a>
                            <a href="<?= BASE_URL ?>/servicios/planes-familia" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Planes Familiares</a>
                            <a href="<?= BASE_URL ?>/servicios/planes-monoparental" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Planes Monoparentales</a>
                        </div>
                    </div>
                </div>

                <!-- Silo Blog -->
                <div class="relative group">
                    <button class="nav-item transition flex items-center hover:text-orange-200 focus:outline-none">
                        <iconify-icon icon="mdi:newspaper-variant-outline" width="20" class="mr-1"></iconify-icon>
                        <span>Blog</span>
                        <iconify-icon icon="mdi:chevron-down" width="16" class="ml-1"></iconify-icon>
                    </button>
                    <div class="absolute left-0 pt-4 w-48 z-50 hidden group-hover:block">
                        <div class="bg-white rounded-md shadow-lg py-1 border border-gray-100">
                            <a href="<?= BASE_URL ?>/blog/guias-isapre" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Guías Isapre</a>
                            <a href="<?= BASE_URL ?>/blog/salud-familiar" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Salud Familiar</a>
                            <a href="<?= BASE_URL ?>/blog/planes-por-perfil" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">Planes por Perfil</a>
                        </div>
                    </div>
                </div>
                
                <!-- Buscador Desktop -->
                <div class="relative ml-4">
                    <input type="text" placeholder="Buscar..." 
                           class="py-1 px-3 pr-8 rounded-full text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-40">
                    <button class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-700">
                        <iconify-icon icon="mdi:magnify" width="16"></iconify-icon>
                    </button>
                </div>
            </nav>

            <!-- Boton Hamburguesa (solo movil) -->
            <button id="menu-toggle" class="md:hidden focus:outline-none hover:text-blue-200">
                <iconify-icon icon="mdi:menu" width="24"></iconify-icon>
            </button>
        </div>

        <!-- menu movil (oculto por defecto, se despliega con JS) -->
        <div id="mobile-menu" class="mobile-menu fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-blue-800 to-blue-900 z-50 p-4">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center mr-2">
                        <span class="text-blue-700 font-bold">PSF</span>
                    </div>
                    <span class="text-xl font-bold">Plan Salud Fácil</span>
                </div>
                <button id="menu-close" class="text-white hover:text-blue-200">
                    <iconify-icon icon="mdi:close" width="24"></iconify-icon>
                </button>
            </div>
            <nav class="flex flex-col space-y-2 mb-6 text-white">
                <a href="<?= BASE_URL ?>/" class="nav-item block py-2 px-2 hover:bg-blue-700 rounded transition flex items-center">
                    <iconify-icon icon="mdi:home-outline" width="20" class="mr-2"></iconify-icon>
                    <span>Inicio</span>
                </a>
                
                <div class="pt-2 pb-1 px-2 text-sm font-bold text-blue-300 uppercase tracking-wider">Nosotros</div>
                <a href="<?= BASE_URL ?>/nosotros/empresa" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Empresa</a>
                
                <div class="pt-2 pb-1 px-2 text-sm font-bold text-blue-300 uppercase tracking-wider">Servicios</div>
                <a href="<?= BASE_URL ?>/servicios/cambio-de-isapre" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Cambio de Isapre</a>
                <a href="<?= BASE_URL ?>/servicios/planes-individuales" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Planes Individuales</a>
                <a href="<?= BASE_URL ?>/servicios/planes-familia" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Planes Familiares</a>
                <a href="<?= BASE_URL ?>/servicios/planes-monoparental" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Planes Monoparentales</a>
                
                <div class="pt-2 pb-1 px-2 text-sm font-bold text-blue-300 uppercase tracking-wider">Blog</div>
                <a href="<?= BASE_URL ?>/blog/guias-isapre" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Guías Isapre</a>
                <a href="<?= BASE_URL ?>/blog/salud-familiar" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Salud Familiar</a>
                <a href="<?= BASE_URL ?>/blog/planes-por-perfil" class="block py-1 px-6 hover:bg-blue-700 rounded transition text-sm">Planes por Perfil</a>
            </nav>
            
            <!-- Buscador Movil -->
            <div class="relative">
                <input type="text" placeholder="Buscar..." 
                       class="py-2 px-4 pr-10 rounded-full text-gray-800 w-full focus:outline-none focus:ring-2 focus:ring-blue-300">
                <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-blue-700">
                    <iconify-icon icon="mdi:magnify" width="20"></iconify-icon>
                </button>
            </div>
        </div>

        <!-- Overlay para menu movil -->
        <div id="menu-overlay" class="menu-overlay fixed inset-0 bg-black z-40"></div>
    </div>
</header>
