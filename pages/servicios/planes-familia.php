<?php
/**
 * =======================================================================
 * OMNIFLOW - SCRIPT DE SEGUIMIENTO DE VISITAS HÍBRIDO
 * =======================================================================
 */
require_once __DIR__ . '/../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $visited_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $stmt_general = $db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)");
        if ($stmt_general) {
            $stmt_general->bind_param("sss", $ip_address, $user_agent, $visited_url);
            $stmt_general->execute(); 
            $stmt_general->close();
        }

        $lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
        if ($lead_id) {
            $stmt_lead = $db->prepare("INSERT INTO lead_visits (lead_id, url_visitada) VALUES (?, ?)");
            if ($stmt_lead) {
                $stmt_lead->bind_param("is", $lead_id, $visited_url);
                $stmt_lead->execute(); 
                $stmt_lead->close();
            }
        }
        $db->close();
    }
} catch (Exception $e) {
    error_log("Omniflow Tracking Error: " . $e->getMessage());
}

$page_title = 'Planes de Salud Familiar - Plan Salud Fácil';
include __DIR__ . '/../../layout/plantilla.php'; 
include __DIR__ . '/../../layout/header.php';
?>

<main class="bg-gray-50 font-sans pb-20">
    <!-- HERO ARTICLE HEADER -->
    <div class="max-w-4xl mx-auto px-4 pt-12">
        <div class="mb-10 text-center">
            <img src="https://images.unsplash.com/photo-1543269865-cbf427effbad?auto=format&fit=crop&w=1200&q=80" 
                 alt="Planes de salud familiar e Isapre para el hogar" 
                 class="w-full h-auto rounded-2xl shadow-xl object-cover max-h-[400px]">
        </div>

        <!-- Banner Monoparental -->
        <div class="bg-blue-50 border-l-4 border-blue-50 p-4 mb-10 rounded-r-lg flex items-center justify-between flex-wrap gap-4">
            <div>
                <strong class="text-blue-800 text-lg">¿Eres papá o mamá soltero/a?</strong>
                <p class="text-blue-700 text-sm mt-1">Diseñamos un plan enfocado en optimizar hogares de un solo ingreso.</p>
            </div>
            <a href="<?= BASE_URL ?>/servicios/planes-monoparental" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm text-sm whitespace-nowrap">
                Ver Planes Monoparentales
            </a>
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight text-center">
            Planes de Salud Familiar: Protege a Quienes Más Amas en un Solo Lugar
        </h1>
        <h2 class="text-xl md:text-2xl text-gray-600 mb-10 text-center max-w-3xl mx-auto leading-relaxed">
            Asegura la tranquilidad de tu hogar unificando coberturas, optimizando tu presupuesto previsional y accediendo a las mejores clínicas de Chile.
        </h2>

        <!-- PRIMARY CTA -->
        <div class="text-center mb-16">
            <a href="#formulario-contacto" 
               class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold text-lg py-4 px-8 rounded-full shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                🚀 Cotizar Plan Familiar Ahora
            </a>
            <p class="text-sm text-gray-500 mt-4">⏱️ Compara todas las Isapres en un solo click. Asesoría 100% gratis.</p>
        </div>

        <!-- ARTICLE CONTENT -->
        <article class="prose prose-lg max-w-none text-gray-700">
            <p class="mb-8 text-lg">La salud de tu familia no es algo que se deba dejar al azar. Cuando tienes personas a tu cargo, las necesidades médicas se multiplican: desde las vacunas y consultas pediátricas de los más pequeños, hasta los exámenes preventivos de los adultos. Un <strong>plan de salud familiar</strong> integral te permite unificar a todos los miembros de tu hogar bajo una sola póliza o cotización de Isapre, optimizando el uso de tus recursos financieros y garantizando atención médica de calidad cuando más lo necesiten.</p>

            <h3 class="text-2xl font-bold text-gray-900 mt-12 mb-6">Ventajas estratégicas de unificar a tu familia en un solo plan</h3>
            <p class="mb-6">Optar por una cobertura conjunta en lugar de planes individuales separados ofrece beneficios comerciales y logísticos muy importantes para la economía del hogar:</p>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-12">
                <ul class="space-y-6">
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">🤝</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Financiamiento inteligente:</strong>
                            <span class="text-gray-600">Permite sumar o complementar los ingresos de la pareja (en algunas modalidades previsionales) para acceder a un plan de gama más alta con mejores clínicas.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">💸</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Uso eficiente de excedentes:</strong>
                            <span class="text-gray-600">Los excedentes generados por un miembro de la familia pueden ser utilizados para financiar bonos, medicamentos o exámenes de cualquiera de las cargas del plan.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">📊</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Control de gastos centralizado:</strong>
                            <span class="text-gray-600">Sabrás exactamente cuánto destina tu hogar a la salud mes a mes, facilitando la administración del presupuesto familiar.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 mt-12 mb-6">Coberturas críticas que no pueden faltar en tu plan familiar</h3>
            <p class="mb-8">Cuando se trata del bienestar de tu familia, no todos los planes son iguales. Para garantizar que estén verdaderamente protegidos frente a cualquier imprevisto, un buen plan familiar debe incluir:</p>
            
            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0 text-xl">🏥</div>
                    <div>
                        <strong class="block text-gray-900 mb-1">Alta Cobertura Hospitalaria</strong>
                        <p class="text-gray-600 text-sm">Cobertura ideal sobre el 80% o 90% en cirugías e intervenciones complejas en las clínicas de tu preferencia para evitar deudas catastróficas.</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0 text-xl">👶</div>
                    <div>
                        <strong class="block text-gray-900 mb-1">Pediatría y Urgencias</strong>
                        <p class="text-gray-600 text-sm">Topes altos y copagos reducidos para atenciones recurrentes infantiles y cobertura 24/7 en los centros de urgencia más cercanos a tu hogar.</p>
                    </div>
                </div>
            </div>

            <?php render_component('formulario_familia'); ?>
        </article>
    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
