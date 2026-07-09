<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Plan Salud Facil'; ?></title> <!-- Título dinámico -->
    <meta name="description" content="<?php echo htmlspecialchars($meta_description ?? 'Cotiza y compara planes de Isapre. Asesoria 100% gratuita y online. Encuentra el mejor plan para ti y tu familia en Plan Salud Facil.'); ?>">
    <link rel="canonical" href="<?php echo 'https://plansaludfacil.cl' . strtok($_SERVER['REQUEST_URI'] ?? '/', '?'); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        /* Estilos CSS personalizados */
        .aviso-ux-con-fondo {
            background-image: url('../img/mama_hijas.jpg'); /* Ajusta la ruta si es necesario */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .form-step { display: none; animation: fadeIn 0.3s ease-out; }
        .form-step.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .progress-step { position: relative; }
        .progress-step:not(:last-child):after {
            content: ''; position: absolute; top: 50%; left: 100%; width: 50px; height: 2px;
            background-color: #d1d5db; transform: translateY(-50%);
        }
        .progress-step.active:not(:last-child):after { background-color: #3b82f6; }
        input:invalid, select:invalid { border-color: #ef4444; }
        input:valid, select:valid { border-color: #10b981; }

        /* Transiciones para el menú móvil (barras laterales deslizantes) */
        .mobile-menu {
            transform: translateX(-100%); /* Oculto por defecto, desliza desde la izquierda */
            transition: transform 0.3s ease-in-out;
        }
        .mobile-menu.open {
            transform: translateX(0); /* Visible */
        }
        .menu-overlay {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out;
            background-color: rgba(0, 0, 0, 0.5); /* Semitransparente */
        }
        .menu-overlay.open {
            opacity: 1; /* Visible */
            visibility: visible;
        }
        /* Estilo para items con íconos */
        .nav-item { display: flex; align-items: center; gap: 0.5rem; color: inherit; }
        .nav-item:hover { color: #93c5fd; }
        .nav-item iconify-icon { color: inherit; transition: color 0.2s ease-in-out; }
        .footer-gradient { background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%);}
        .cta-gradient { background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%); transition: all 0.3s ease; }
        .cta-gradient:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .benefits-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80');
            background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;
        }
        @media (max-width: 768px) { .benefits-section { background-attachment: scroll; } }
    </style>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17127470305"></script>
    <script>
      window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'AW-17127470305');
    </script>
    <!-- Event snippet for Envío de formulario para clientes potenciales conversion page -->
    <script>
      gtag('event', 'conversion', { 'send_to': 'AW-17127470305/7vjyCJyrss8aEOHpgec_', 'value': 1.0, 'currency': 'CLP' });
    </script>
    <!-- Hotjar Tracking Code for https://plansaludfacil.cl/ -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:6455237,hjsv:6};
            a=o.getElementsByTagName('head')[0]; r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv; a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
    <!--clarity-->
    <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "xfx87e9kla");
    </script>
    
    <meta name="google-site-verification" content="STeCF3cjAw8N63nEgrCyo6_CifEvabh7KCovktoIKNI" />

    <!-- Schema.org: Organization -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Plan Salud Facil",
      "url": "https://plansaludfacil.cl",
      "logo": "https://plansaludfacil.cl/img/logo.png",
      "description": "Comparador de planes de Isapre 100% gratuito. Te ayudamos a encontrar el mejor plan de salud segun tus necesidades y presupuesto.",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+56 9 5228 2339",
        "contactType": "customer service",
        "availableLanguage": "Spanish"
      },
      "sameAs": [
        "https://wa.me/56952282339"
      ]
    }
    </script>

    <!-- Schema.org: WebSite -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Plan Salud Facil",
      "url": "https://plansaludfacil.cl",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://plansaludfacil.cl/?s={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <!-- Schema.org: BreadcrumbList dinámico -->
    <?php
    $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    $segments = array_values(array_filter(explode('/', $uri)));
    if (!empty($segments)) {
        $breadcrumbs = [];
        $breadcrumbs[] = ['name' => 'Inicio', 'url' => 'https://plansaludfacil.cl/'];
        $accumulated = '';
        $nameMap = [
            'preguntas-frecuentes' => 'Preguntas Frecuentes',
            'servicios' => 'Servicios',
            'cambio-de-isapre' => 'Cambio de Isapre',
            'planes-individuales' => 'Planes Individuales',
            'planes-familia' => 'Planes Familiares',
            'planes-monoparental' => 'Planes Monoparentales',
            'planes-profesionales' => 'Planes Profesionales',
            'nosotros' => 'Nosotros',
            'empresa' => 'Nuestra Empresa',
            'privacidad' => 'Politica de Privacidad',
            'gracias' => 'Gracias',
        ];
        foreach ($segments as $seg) {
            $accumulated .= '/' . $seg;
            $name = $nameMap[$seg] ?? ucfirst(str_replace('-', ' ', $seg));
            $breadcrumbs[] = ['name' => $name, 'url' => 'https://plansaludfacil.cl' . $accumulated];
        }
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(function($bc, $i) {
                return [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $bc['name'],
                    'item' => $bc['url']
                ];
            }, $breadcrumbs, array_keys($breadcrumbs))
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo "\n</script>\n";
    }
    ?>
    
</head>
<body class="bg-gray-50 min-h-screen font-sans flex flex-col">