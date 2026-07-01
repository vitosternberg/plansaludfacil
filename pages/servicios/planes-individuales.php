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

$page_title = 'Planes de Salud Individuales - Plan Salud Fácil';
include __DIR__ . '/../../layout/plantilla.php'; 
include __DIR__ . '/../../layout/header.php';
?>

<main class="bg-gray-50 font-sans pb-20">
    <!-- HERO ARTICLE HEADER -->
    <div class="max-w-4xl mx-auto px-4 pt-12">
        <div class="mb-10 text-center">
            <img src="<?= BASE_URL ?>/img/mountain_biking_hero.jpg" 
                 alt="Plan de salud individual para profesionales y trabajadores" 
                 class="w-full h-auto rounded-2xl shadow-xl object-cover max-h-[400px]">
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight text-center">
            Planes de Salud Individuales: Cobertura Médica a tu Medida
        </h1>
        <h2 class="text-xl md:text-2xl text-gray-600 mb-10 text-center max-w-3xl mx-auto leading-relaxed">
            Encuentra el plan de Isapre ideal para tu perfil, optimiza tu 7% obligatorio y accede a beneficios exclusivos sin pagar de más por coberturas que no usas.
        </h2>

        <!-- PRIMARY CTA -->
        <div class="text-center mb-16">
            <a href="#formulario-contacto" 
               class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold text-lg py-4 px-8 rounded-full shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                🚀 Cotizar Plan Individual Ahora
            </a>
            <p class="text-sm text-gray-500 mt-4">⏱️ Recibe una comparativa personalizada de todas las Isapres gratis.</p>
        </div>

        <!-- ARTICLE CONTENT -->
        <article class="prose prose-lg max-w-none text-gray-700">
            <p class="mb-8 text-lg">Cuando no tienes cargas familiares, tus prioridades de salud son completamente distintas. No necesitas financiar pediatría ni urgencias infantiles; en su lugar, buscas optimizar tu presupuesto para obtener la mejor cobertura en telemedicina, consultas de especialidad, salud mental o medicina deportiva. Un <strong>plan de salud individual</strong> te permite concentrar todo el valor de tu cotización legal en ti mismo, asegurando una protección robusta en las clínicas que realmente prefieres visitar.</p>

            <h3 class="text-2xl font-bold text-gray-900 mt-12 mb-6">¿Qué es un plan de salud individual y quiénes lo necesitan?</h3>
            <p class="mb-4">Es un contrato de salud previsional diseñado para un único titular, sin beneficiarios asociados. Este tipo de póliza es la opción perfecta para profesionales independientes, jóvenes solteros que se están independizando de sus padres, o adultos cuyos hijos ya crecieron y salieron del grupo familiar.</p>
            <p class="mb-8">La gran ventaja de este formato es la personalización. Al no tener que promediar los riesgos de un grupo, las Isapres ofrecen estructuras de planes muy ágiles que se adaptan con precisión a tu nivel de ingresos y a tu estilo de vida actual.</p>

            <h3 class="text-2xl font-bold text-gray-900 mt-12 mb-6">Beneficios de contratar una cobertura médica exclusiva para ti</h3>
            <p class="mb-6">Tomar el control de tu previsión de salud con un plan unipersonal ofrece importantes ventajas estratégicas:</p>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-12">
                <ul class="space-y-6">
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">⚡</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Máxima eficiencia de tu 7%:</strong>
                            <span class="text-gray-600">Al no tener cargas adicionales, el total de tu cotización se destina a mejorar tus topes anuales, reducir tus copagos y acceder a habitaciones individuales en caso de hospitalización.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">🎯</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Coberturas enfocadas en tus intereses:</strong>
                            <span class="text-gray-600">Puedes elegir planes que privilegien la kinesiología si haces deporte, beneficios dentales, o excelentes convenios en farmacias y salud mental.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">💰</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Generación rápida de excedentes:</strong>
                            <span class="text-gray-600">Si tu sueldo es alto, un plan individual bien diseñado te permitirá acumular dinero mes a mes, el cual puedes usar en bonos, lentes ópticos o atenciones ambulatorias.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <?php render_component('formulario_individual'); ?>
            
            <!-- BLOG CLUSTER: Planes Individuales -->
            <div class="mt-16">
                <?php 
                $titulo = 'Guías y Consejos para Profesionales y Jóvenes';
                $limite = 3;
                $categoria_id = 12; // ID de la categoría "Planes Individuales" en WordPress
                include __DIR__ . '/../../components/ultimos_articulos_blog.php'; 
                ?>
            </div>
        </article>
    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
