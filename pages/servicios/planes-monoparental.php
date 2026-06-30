<?php
/**
 * =======================================================================
 * PLANES DE SALUD MONOPARENTAL
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

$page_title = 'Planes de Salud Monoparentales - Plan Salud Fácil';
include __DIR__ . '/../../layout/plantilla.php'; 
include __DIR__ . '/../../layout/header.php';
?>

<main class="bg-gray-50 font-sans pb-20">
    <!-- HERO ARTICLE HEADER -->
    <div class="max-w-4xl mx-auto px-4 pt-12">
        <div class="mb-10 text-center">
            <img src="<?= BASE_URL ?>/img/madre_orgullosa.jpg" 
                 alt="Plan Isapre Monoparental" 
                 class="w-full h-auto rounded-2xl shadow-xl object-cover max-h-[400px]">
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 leading-tight text-center">
            Protege a tus Hijos sin Desestabilizar tu Presupuesto: Planes para Hogares de un Solo Ingreso
        </h1>
        <h2 class="text-xl md:text-2xl text-gray-600 mb-10 text-center max-w-3xl mx-auto leading-relaxed">
            Nos encargamos de toda la burocracia y comparamos los planes por ti. Asesoría 100% digital y transparente, para que no pierdas ni un minuto lejos de los tuyos.
        </h2>

        <!-- PRIMARY CTA -->
        <div class="text-center mb-16">
            <a href="#formulario-contacto" 
               class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg py-4 px-8 rounded-full shadow-lg hover:shadow-2xl transition transform hover:-translate-y-1">
                🦸‍♀️ Cotizar Plan Monoparental
            </a>
            <p class="text-sm text-gray-500 mt-4">⏱️ Compara opciones eficientes. Asesoría 100% gratis.</p>
        </div>

        <!-- ARTICLE CONTENT -->
        <article class="prose prose-lg max-w-none text-gray-700">
            <p class="mb-8 text-lg">Sabemos que cuando eres el <strong>sostén único</strong> del hogar, todo el peso económico y logístico recae sobre ti. Cada decisión financiera debe estar calculada y el tiempo es tu recurso más valioso. Por eso, elegir la cobertura de Isapre no debería ser un dolor de cabeza ni un gasto desproporcionado. Nuestro equipo ha diseñado soluciones que optimizan tu 7% legal para proteger a tus cargas sin que tengas que pagar una fortuna de tu bolsillo.</p>

            <h3 class="text-2xl font-bold text-gray-900 mt-12 mb-6">¿Por qué este plan es diferente?</h3>
            <p class="mb-6">Las Isapres tradicionales suelen enfocarse en familias que complementan rentas, pero nosotros ajustamos la oferta a <strong>tu realidad financiera</strong>:</p>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mb-12">
                <ul class="space-y-6">
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">🛡️</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Cero Riesgos Financieros:</strong>
                            <span class="text-gray-600">Encontramos planes con alta cobertura catastrófica para ti (porque si tú fallas, el hogar colapsa) y copagos bajos para urgencias pediátricas.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">⏳</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Ahorro de Tiempo y Burocracia:</strong>
                            <span class="text-gray-600">Olvídate de leer contratos engorrosos. Nuestra asesoría es 100% digital, transparente y directa al punto.</span>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="text-blue-500 mr-4 text-2xl">💰</span>
                        <div>
                            <strong class="text-gray-900 block mb-1">Precio Justo por Carga:</strong>
                            <span class="text-gray-600">Analizamos el mercado para evitar que te "castiguen" financieramente por agregar cargas, asegurando que pagues lo estrictamente necesario.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <h3 class="text-2xl font-bold text-gray-900 mt-12 mb-6">Coberturas Críticas para Ti</h3>
            
            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0 text-xl">🏥</div>
                    <div>
                        <strong class="block text-gray-900 mb-1">Red de Urgencia Fija</strong>
                        <p class="text-gray-600 text-sm">Debes saber exactamente a qué clínica cercana partir a las 3 AM con tus hijos, teniendo claro que el copago será bajo y conocido.</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0 text-xl">📄</div>
                    <div>
                        <strong class="block text-gray-900 mb-1">Licencias sin Problemas</strong>
                        <p class="text-gray-600 text-sm">Seleccionamos Isapres con buenos historiales de aprobación de licencias médicas, porque dejar de percibir tu sueldo un mes sería fatal.</p>
                    </div>
                </div>
            </div>

            <?php render_component('formulario_familia', ['es_monoparental' => true]); ?>
        </article>
    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
