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

$page_title = 'Cambio de Isapre - Plan Salud Fácil';
include __DIR__ . '/../../layout/plantilla.php'; 
include __DIR__ . '/../../layout/header.php';
?>

<main class="bg-gray-50 font-sans pb-20">
    <!-- HERO ARTICLE HEADER -->
    <div class="max-w-4xl mx-auto px-4 pt-12">
        <div class="mb-10 text-center">
            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80" 
                 alt="Asesoría para cambio de Isapre online" 
                 class="w-full h-auto rounded-2xl shadow-xl object-cover max-h-[400px]">
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight text-center">
            Cambio de Isapre: Asesoría Gratuita Online
        </h1>
        <h2 class="text-xl md:text-2xl text-gray-600 mb-10 text-center max-w-3xl mx-auto leading-relaxed">
            ¿Sientes que estás pagando de más o que tu cobertura actual no es suficiente? Te ayudamos a cambiarte de Isapre de forma rápida, segura y 100% online.
        </h2>

        <!-- PRIMARY CTA -->
        <div class="text-center mb-16">
            <a href="#formulario-contacto" 
               class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold text-lg py-4 px-8 rounded-full shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                🚀 Cambiarme de Isapre Ahora
            </a>
            <p class="text-sm text-gray-500 mt-4">⏱️ Toma menos de 2 minutos y la asesoría es 100% gratis.</p>
        </div>

        <!-- ARTICLE CONTENT -->
        <article class="prose prose-lg max-w-none text-gray-700">
            <p class="mb-8 text-lg">El mercado de la salud en Chile cambia constantemente. Lo que ayer era un excelente plan, hoy puede estar obsoleto o desalineado con tus necesidades actuales. Cambiarse de Isapre no tiene por qué ser un dolor de cabeza ni un proceso lleno de burocracia. En <strong>PlanSaludFácil</strong> nos encargamos de analizar todo el mercado por ti para encontrar la opción que realmente maximice el valor de tu 7% de cotización obligatoria.</p>

            <h2 class="text-3xl font-bold text-gray-900 mt-12 mb-6">¿Por qué deberías evaluar un cambio de Isapre hoy?</h2>
            <p class="mb-6">La mayoría de las personas se mantienen en su Isapre por costumbre, perdiendo la oportunidad de acceder a mejores beneficios. Las tres razones más comunes para solicitar un cambio incluyen:</p>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-12">
                <ul class="space-y-6">
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">💰</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Tu sueldo aumentó:</strong>
                            <span class="text-gray-600">Si tu 7% legal ahora es mayor, estás generando excedentes que podrías aprovechar mejor en un plan con coberturas más altas.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">👨‍👩‍👧‍👦</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Cambio en tu estructura familiar:</strong>
                            <span class="text-gray-600">Si vas a tener un hijo, si tus hijos ya crecieron y salieron del plan, o si te casaste, tu matriz de riesgo cambió y necesitas otra aseguradora.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">🏥</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Descontento con las clínicas en convenio:</strong>
                            <span class="text-gray-600">Si tu Isapre actual no tiene buena cobertura en los centros médicos cercanos a tu hogar o trabajo, estás pagando por un servicio incómodo.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mt-12 mb-6">Requisitos básicos para solicitar el traslado</h2>
            <p class="mb-6">Para que podamos gestionar tu cambio con éxito, es importante que cumplas con los siguientes requisitos estándar del sistema previsional chileno:</p>
            <ul class="list-decimal pl-6 space-y-3 mb-12 text-gray-600">
                <li>Tener al menos un año de permanencia en tu Isapre actual (si eres el cotizante titular).</li>
                <li>No encontrarte bajo licencia médica vigente al momento de firmar el nuevo contrato.</li>
                <li>Completar de forma fidedigna la Declaración de Salud requerida por la Superintendencia.</li>
            </ul>

            <h2 class="text-3xl font-bold text-gray-900 mt-12 mb-6">¿Cómo te ayudamos en PlanSaludFácil?</h2>
            <p class="mb-8">Hacer el trámite por tu cuenta implica visitar múltiples sitios web, descifrar complejas tablas de factores y arriesgarte a tomar una decisión equivocada. Nuestro servicio simplifica el proceso al mínimo:</p>
            
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4 text-xl">📊</div>
                    <strong class="block text-gray-900 mb-2">Comparativa Multi-Isapre</strong>
                    <p class="text-gray-600 text-sm">Evaluamos simultáneamente las ofertas vigentes de las principales Isapres del país para mostrarte solo aquellas que superan las coberturas de tu plan actual.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4 text-xl">🩺</div>
                    <strong class="block text-gray-900 mb-2">Análisis de Preexistencias</strong>
                    <p class="text-gray-600 text-sm">Revisamos tu historial médico de forma confidencial antes de postular para garantizar que el cambio sea seguro y aprobado sin contratiempos.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4 text-xl">📱</div>
                    <strong class="block text-gray-900 mb-2">Gestión 100% Digital</strong>
                    <p class="text-gray-600 text-sm">No necesitas ir a ninguna sucursal. Validamos la Declaración de Salud y firmamos el nuevo contrato de manera online y 100% legal.</p>
                </div>
            </div>

            <!-- FORMULARIO DE CONTACTO (CTA FINAL) -->
            <?php include __DIR__ . '/../../components/formulario_individual.php'; ?>
            
            <!-- BLOG CLUSTER: Cambio de Isapre / General Isapre -->
            <div class="mt-16">
                <?php 
                $titulo = 'Guías y Consejos sobre Isapres';
                $limite = 3;
                $categoria_id = 8; // ID de la categoría "Isapre" en WordPress
                include __DIR__ . '/../../components/ultimos_articulos_blog.php'; 
                ?>
            </div>
        </article>
    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>