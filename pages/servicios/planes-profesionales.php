<?php

/**
* =======================================================================
* OMNIFLOW - SCRIPT DE SEGUIMIENTO DE VISITAS HÍBRIDO
* =======================================================================
* 1. Registra TODAS las visitas a esta página en una tabla de log general.
* 2. Si la URL contiene un `lead_id`, también registra la visita en la
*  tabla específica de actividad de leads (`lead_visits`).
*/

// Incluir el archivo de configuración para la conexión a la BD
require_once __DIR__ . '/../../omniflow_config.php';

try {
  // 1. Conectarse a la base de datos
  $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($db->connect_error) {
    throw new Exception("Error de conexión a la BD: " . $db->connect_error);
  }
  $db->set_charset("utf8mb4");

  // --- REGISTRO GENERAL DE VISITAS (LOG DE TRÁFICO) ---
  $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
  $visited_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

  // Asumimos que tienes una tabla `log_visitas_generales`
  $sql_general = "INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)";
  $stmt_general = $db->prepare($sql_general);
  $stmt_general->bind_param("sss", $ip_address, $user_agent, $visited_url);
  $stmt_general->execute();
  $stmt_general->close();


  // --- REGISTRO ESPECÍFICO DE LEADS (SI APLICA) ---
  $lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);

  if ($lead_id) {
    // Si la URL contiene un lead_id, lo registramos en la tabla de actividad de leads.
    $sql_lead = "INSERT INTO lead_visits (lead_id, url_visitada) VALUES (?, ?)";
    $stmt_lead = $db->prepare($sql_lead);
    $stmt_lead->bind_param("is", $lead_id, $visited_url);
    $stmt_lead->execute();
    $stmt_lead->close();
  }

  $db->close();

} catch (Exception $e) {
  // Si algo falla, el error se puede registrar en el log del servidor,
  // pero la página seguirá cargando normalmente para el usuario.
  error_log("Error en el script de seguimiento híbrido: " . $e->getMessage());
}

// --- FIN DEL SCRIPT DE SEGUIMIENTO ---
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checklist de Optimización de Planes de Salud | Plan Salud Facil</title>
 
  <script src="https://cdn.tailwindcss.com"></script>
 
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
 
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
 
  <script>
   tailwind.config = {
    theme: {
     extend: {
      fontFamily: {
       sans: ['Poppins', 'sans-serif'],
      },
     }
    }
   }
  </script>

  <style>
    /* Estilos para los items de navegación del header */
    .nav-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem;
      border-radius: 0.375rem;
    }
    .nav-item:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
   
    /* Oculta el menú y el overlay por defecto */
    .mobile-menu, .menu-overlay {
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    /* Animación de entrada para el menú móvil */
    .mobile-menu {
      transform: translateX(-100%);
      transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
    }

    /* Clase 'is-open' que activa la visibilidad y animación */
    .is-open {
      opacity: 1;
      visibility: visible;
    }
    .is-open.mobile-menu {
      transform: translateX(0);
    }
  </style>
</head>
<body class="bg-slate-50 font-sans text-gray-800 leading-relaxed">

  <?php
    // Variables que el header necesita para funcionar
    $page_title = 'Plan Salud Facil';
    $header_admin_email = 'contacto@plansaludfacil.cl';
  ?>

  <div class="bg-blue-900 text-white text-sm">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center py-2">
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
       
        <div class="hidden md:flex items-center">
          <iconify-icon icon="mdi:clock-outline" width="14" class="mr-1"></iconify-icon>
          <span>Lunes a Viernes: 9:00 - 18:00 hrs</span>
        </div>
      </div>
    </div>
  </div>

  <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-md sticky top-0 z-30">
    <div class="container mx-auto px-4 py-3">
      <div class="flex justify-between items-center">
        <div class="flex items-center">
          <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center mr-2">
            <span class="text-blue-700 font-bold">PSF</span>
          </div>
          <span class="text-xl font-bold"><?php echo htmlspecialchars($page_title); ?></span>
        </div>

        <nav class="hidden md:flex items-center space-x-6">
          <a href="index.php" class="nav-item transition">
            <iconify-icon icon="mdi:home-outline" width="20"></iconify-icon>
            <span>Inicio</span>
          </a>
          <a href="cotizador.php" class="nav-item transition">
            <iconify-icon icon="mdi:lightning-bolt-outline" width="20"></iconify-icon>
            <span>Cotizar Ahora</span>
          </a>
          <a href="#" class="nav-item transition">
            <iconify-icon icon="mdi:newspaper-variant-outline" width="20"></iconify-icon>
            <span>Blog</span>
          </a>
          <a href="contacto.php" class="nav-item transition">
            <iconify-icon icon="mdi:email-outline" width="20"></iconify-icon>
            <span>Contacto</span>
          </a>
        </nav>

        <button id="menu-toggle" class="md:hidden focus:outline-none hover:text-blue-200">
          <iconify-icon icon="mdi:menu" width="24"></iconify-icon>
        </button>
      </div>

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
        <nav class="flex flex-col space-y-4 mb-6">
          <a href="index.php" class="nav-item block py-2 transition"><span>Inicio</span></a>
          <a href="cotizador.php" class="nav-item block py-2 transition"><span>Cotizar Ahora</span></a>
          <a href="#" class="nav-item block py-2 transition"><span>Blog</span></a>
          <a href="contacto.php" class="nav-item block py-2 transition"><span>Contacto</span></a>
        </nav>
      </div>

      <div id="menu-overlay" class="menu-overlay fixed inset-0 bg-black bg-opacity-50 z-40"></div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto p-6">
   
    <div class="text-center py-12 px-6">
      <h1 class="text-4xl md:text-5xl font-bold text-gray-900">Puntos Clave para la Optimización de su Plan de Isapre</h1>
      <p class="max-w-3xl mx-auto mt-4 text-gray-600">Como profesional de la salud, usted conoce el sistema. Esta guía es un resumen ejecutivo para verificar rápidamente los 4 pilares de un plan de salud óptimo, ahorrándole un tiempo valioso.</p>
    </div>
  
    <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
     
      <article class="bg-white rounded-2xl shadow-lg p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center mb-4">
          <span class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-500 text-white font-bold text-xl mr-4">1</span>
          <h2 class="text-xl font-semibold text-gray-900">Validación de Prioridades y Cargas</h2>
        </div>
        <p class="text-gray-600">Sabemos que sus prioridades son claras. El objetivo es asegurar que su plan actual o futuro cubra de forma óptima a todo su grupo familiar, incluyendo preexistencias y preferencias de prestadores, sin pagar de más por coberturas innecesarias.</p>
      </article>

      <article class="bg-white rounded-2xl shadow-lg p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center mb-4">
          <span class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-500 text-white font-bold text-xl mr-4">2</span>
          <h2 class="text-xl font-semibold text-gray-900">Análisis: Cobertura Hospitalaria vs. Ambulatoria</h2>
        </div>
        <p class="text-gray-600">Más allá de los porcentajes, es clave analizar la estructura de bonificación y los topes. Un plan debe tener un equilibrio costo-efectivo entre la cobertura para procedimientos de alto costo (hospitalaria) y el uso frecuente de consultas y exámenes (ambulatoria).</p>
      </article>

      <article class="bg-white rounded-2xl shadow-lg p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center mb-4">
          <span class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-500 text-white font-bold text-xl mr-4">3</span>
          <h2 class="text-xl font-semibold text-gray-900">Optimización del Gasto Anual (Precio vs. Copago)</h2>
        </div>
        <p class="text-gray-600">El costo real de un plan se mide anualmente. Realizamos un análisis para determinar el punto de equilibrio donde una mensualidad ligeramente mayor puede significar un ahorro sustancial en copagos, maximizando el retorno de su inversión en salud.</p>
      </article>

      <article class="bg-white rounded-2xl shadow-lg p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="flex items-center mb-4">
          <span class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-500 text-white font-bold text-xl mr-4">4</span>
          <h2 class="text-xl font-semibold text-gray-900">Verificación Estratégica de la Red de Prestadores</h2>
        </div>
        <p class="text-gray-600">Asegurar que su red de clínicas y colegas de confianza esté en la cobertura preferente es fundamental. Verificamos que su elección de plan no solo cubra, sino que optimice el acceso a la red que usted valora profesional y personalmente.</p>
      </article>
    </section>

    <section class="text-center bg-white rounded-2xl shadow-lg py-12 px-8 my-16">
      <h2 class="text-3xl font-bold text-gray-900">¿Necesita una segunda opinión experta? Deje el análisis en nuestras manos.</h2>
      <p class="max-w-2xl mx-auto mt-3 text-gray-600">Nuestro servicio gratuito se especializa en el análisis técnico de miles de planes para profesionales como usted. Encontramos la opción con el mejor costo-beneficio, permitiéndole delegar esta tarea con total confianza. Sin compromiso.</p>
      <a href="https://plansaludfacil.cl/cotizador.php" class="inline-block bg-blue-500 text-white font-semibold py-3 px-8 rounded-full mt-6 transition-all duration-300 hover:bg-blue-600 hover:-translate-y-1">
        Solicitar Asesoría Especializada
      </a>
    </section>
  </main>
 
  <footer class="text-center py-8">
    <p class="text-sm text-gray-500">Guía creada por <strong class="font-semibold">Plan Salud Fácil</strong> | www.plansaludfacil.cl</p>
  </footer>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const menuToggle = document.getElementById('menu-toggle');
      const menuClose = document.getElementById('menu-close');
      const mobileMenu = document.getElementById('mobile-menu');
      const menuOverlay = document.getElementById('menu-overlay');

      const openMenu = () => {
        mobileMenu.classList.add('is-open');
        menuOverlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
      };

      const closeMenu = () => {
        mobileMenu.classList.remove('is-open');
        menuOverlay.classList.remove('is-open');
        document.body.style.overflow = '';
      };

      menuToggle.addEventListener('click', openMenu);
      menuClose.addEventListener('click', closeMenu);
      menuOverlay.addEventListener('click', closeMenu);
    });
  </script>

</body>
</html>
