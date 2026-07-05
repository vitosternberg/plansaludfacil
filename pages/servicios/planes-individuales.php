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

$page_title = 'Planes de Salud Individuales: Cobertura a tu Medida | Plan Salud Fácil';
$meta_description = 'Encuentra el mejor plan de Isapre para ti sin cargas. Optimiza tu 7%, accede a telemedicina, salud mental y medicina deportiva. Asesoría 100% gratis y online.';
include __DIR__ . '/../../layout/plantilla.php'; 
include __DIR__ . '/../../layout/header.php';
?>

<!-- FAQ Structured Data (Schema.org) para Google AI Overview -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "¿Qué es un plan de salud individual y quiénes lo necesitan?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Es un contrato de salud previsional para un único titular sin beneficiarios. Ideal para profesionales independientes, jóvenes que se independizan de sus padres, o adultos sin cargas familiares. Concentra todo tu 7% en tus propias coberturas, sin promediar riesgos de un grupo."
      }
    },
    {
      "@type": "Question",
      "name": "¿Qué beneficios tiene un plan de salud individual sin cargas?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Los principales beneficios son: máxima eficiencia de tu 7% (todo se destina a tus coberturas), planes enfocados en tus intereses (deporte, salud mental, telemedicina), y generación rápida de excedentes si tu sueldo es alto. Pagas solo por lo que realmente usas."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cómo elijo la mejor Isapre para un plan individual?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Debes comparar al menos tres factores: la red de clínicas en convenio cercanas a tu hogar o trabajo, los topes anuales de cobertura ambulatoria y hospitalaria, y el porcentaje de bonificación en tus prestaciones más frecuentes. En Plan Salud Fácil hacemos esta comparativa por ti, gratis y en menos de 48 horas."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cuánto cuesta un plan de salud individual en Chile?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "El costo mensual depende de tu renta imponible, ya que se descuenta de tu 7% legal obligatorio. Si tu 7% es mayor que el precio del plan, generas excedentes que puedes usar en bonos y atenciones. Nuestra asesoría para encontrar el mejor plan es 100% gratuita."
      }
    },
    {
      "@type": "Question",
      "name": "¿Puedo contratar un plan individual si tengo preexistencias?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sí, puedes contratar un plan individual aunque tengas preexistencias. Analizamos tu historial médico de forma confidencial antes de postular para asegurar que la contratación sea aprobada sin rechazos ni restricciones abusivas. No enviamos tu declaración a ciegas."
      }
    }
  ]
}
</script>

<style>
/* Estilo piramidal AIO: respuesta directa destacada */
.answer-direct {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-left: 4px solid #2563eb;
    padding: 1rem 1.25rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
    font-size: 1.1rem;
    color: #1e3a8a;
}
</style>


<main class="bg-gray-50 font-sans pb-20">
    <div class="max-w-4xl mx-auto px-4 pt-12">

        <!-- ============================================================ -->
        <!-- IMAGEN PRINCIPAL DEL SERVICIO                                 -->
        <!-- ============================================================ -->
        <div class="mb-10 text-center">
            <img src="<?= BASE_URL ?>/img/mountain_biking_hero.jpg" 
                 alt="Plan de salud individual para profesionales y trabajadores sin cargas" 
                 class="w-full h-auto rounded-2xl shadow-xl object-cover max-h-[400px]">
        </div>

        <!-- ============================================================ -->
        <!-- HERO: Respuesta directa a la pregunta principal del usuario   -->
        <!-- ============================================================ -->
        <section class="mb-16 text-center">
            <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full mb-4">
                Servicio 100% Gratuito
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                Encuentra el mejor plan de salud individual <span class="text-blue-600">sin pagar de más ni hacer trámites</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Comparamos todas las Isapres del mercado, encontramos las coberturas que realmente necesitas
                y gestionamos tu contratación 100% online. En menos de 48 horas.
            </p>
            <a href="#formulario"
               class="cta-gradient inline-flex items-center text-white font-bold py-4 px-8 rounded-xl text-lg shadow-lg hover:shadow-xl transition">
                <iconify-icon icon="mdi:whatsapp" width="24" class="mr-2"></iconify-icon>
                Quiero Cotizar mi Plan Individual
            </a>
            <p class="text-sm text-gray-400 mt-3">⏱️ Toma menos de 2 minutos · Asesoría 100% gratis</p>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 1: ¿Qué es un plan de salud individual?              -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Qué es un plan de salud individual y quiénes lo necesitan?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Es un contrato de salud previsional para un único titular sin beneficiarios. Ideal para
                profesionales independientes, jóvenes que se independizan de sus padres, o adultos sin
                cargas familiares. Concentra todo tu 7% en tus propias coberturas, sin tener que
                promediar los riesgos de un grupo familiar.
            </div>

            <p class="text-gray-600 mb-6">
                Cuando no tienes cargas familiares, tus prioridades de salud son completamente distintas.
                No necesitas financiar pediatría ni urgencias infantiles; en su lugar, buscas optimizar tu
                presupuesto para obtener la mejor cobertura en telemedicina, consultas de especialidad,
                salud mental o medicina deportiva. Estas son las tres situaciones más comunes donde
                un plan individual es la decisión correcta:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Eres profesional independiente</h3>
                    <p class="text-gray-600 text-sm">Emitir boletas no significa renunciar a buena salud. Concentras tu 7% en coberturas ambulatorias de alto uso y generas excedentes.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">Te independizas de tus padres</h3>
                    <p class="text-gray-600 text-sm">Al cumplir la mayoría de edad o al terminar tus estudios, necesitas tu propia protección con coberturas pensadas para tu etapa de vida.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-green-200">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Adulto sin cargas familiares</h3>
                    <p class="text-gray-600 text-sm">Tienes ingresos estables pero no necesitas plan familiar. Quieres pagar solo por lo que usas y acceder rápido a especialistas y clínicas.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 2: ¿Qué beneficios tiene un plan individual?         -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Qué beneficios tiene un plan de salud individual sin cargas?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Los tres beneficios principales son: máxima eficiencia de tu 7% (todo se destina a tus
                coberturas), planes enfocados en tus intereses (deporte, salud mental, telemedicina), y
                generación rápida de excedentes si tu sueldo es alto. Pagas solo por lo que realmente usas.
            </div>

            <p class="text-gray-600 mb-6">
                Tomar el control de tu previsión de salud con un plan unipersonal ofrece ventajas
                estratégicas que un plan familiar simplemente no puede igualar:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-blue-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-blue-200">⚡</div>
                    <strong class="block text-gray-900 mb-2">Máxima eficiencia de tu 7%</strong>
                    <p class="text-gray-600 text-sm">Al no tener cargas, el total de tu cotización se destina a
                    mejorar tus topes anuales, reducir copagos y acceder a habitación individual en hospitalización.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-purple-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-purple-200">🎯</div>
                    <strong class="block text-gray-900 mb-2">Coberturas enfocadas en ti</strong>
                    <p class="text-gray-600 text-sm">Elige planes que privilegien kinesiología si haces deporte,
                    telemedicina si viajas, salud mental o excelentes convenios dentales y farmacéuticos.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-green-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-green-200">💰</div>
                    <strong class="block text-gray-900 mb-2">Generas excedentes más rápido</strong>
                    <p class="text-gray-600 text-sm">Si tu sueldo es alto, acumularás dinero mes a mes que puedes
                    usar en bonos, lentes ópticos, medicamentos o atenciones ambulatorias sin copago.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 3: ¿Cómo funciona el proceso de cotización?          -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cómo funciona el proceso de cotización de un plan individual?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                En Plan Salud Fácil comparamos todas las Isapres por ti, analizamos tus necesidades
                reales de salud y te presentamos las mejores opciones en menos de 48 horas. Tú solo
                tienes que contarnos tu situación y nosotros hacemos el resto.
            </div>

            <p class="text-gray-600 mb-6">
                Hacerlo por tu cuenta implica visitar múltiples sitios web, descifrar tablas de factores
                y arriesgarte a elegir un plan inadecuado. Nuestro proceso simplifica todo en tres pasos:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Análisis de tu perfil</h3>
                    <p class="text-gray-600 text-sm">Revisamos tu edad, renta, ubicación geográfica y
                    prestaciones que más utilizas para identificar los planes que mejor se ajustan a tus
                    necesidades reales.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">Comparativa Multi-Isapre</h3>
                    <p class="text-gray-600 text-sm">Evaluamos simultáneamente las ofertas vigentes de
                    Banmédica, Colmena, Consalud, Cruz Blanca, Nueva Masvida y Vida Tres para mostrarte
                    solo las que te convienen.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-green-200">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Contratación 100% Digital</h3>
                    <p class="text-gray-600 text-sm">Gestionamos tu Declaración de Salud y firmas el nuevo
                    contrato de manera online y 100% legal, sin moverte de tu casa ni hacer filas en
                    ninguna sucursal.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 4: ¿Cómo elijo la mejor Isapre?                      -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cómo elijo la mejor Isapre para un plan individual?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Debes comparar al menos tres factores: la red de clínicas en convenio cercanas a tu hogar
                o trabajo, los topes anuales de cobertura ambulatoria y hospitalaria, y el porcentaje de
                bonificación en tus prestaciones más frecuentes. En Plan Salud Fácil hacemos esta
                comparativa por ti, gratis y en menos de 48 horas.
            </div>

            <p class="text-gray-600 mb-6">
                Elegir por tu cuenta puede ser abrumador. Hay más de 8 Isapres abiertas en Chile, cada una
                con múltiples planes que cambian cada año. Para tomar una buena decisión, enfócate en
                estos tres criterios:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-5 bg-blue-50 rounded-xl border border-blue-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:map-marker-radius" class="text-blue-600" width="20"></iconify-icon>
                        Red de clínicas cercana
                    </h3>
                    <p class="text-gray-600 text-sm">Revisa qué clínicas y centros médicos están en convenio cerca de tu hogar o trabajo. No sirve tener la mejor cobertura si el prestador te queda a 2 horas de distancia.</p>
                </div>
                <div class="p-5 bg-purple-50 rounded-xl border border-purple-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:shield-check" class="text-purple-600" width="20"></iconify-icon>
                        Topes anuales de cobertura
                    </h3>
                    <p class="text-gray-600 text-sm">Compara los topes máximos en UF para hospitalización y procedimientos ambulatorios. Un tope bajo te puede dejar con gastos millonarios en caso de una cirugía.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:chart-line" class="text-green-600" width="20"></iconify-icon>
                        Bonificación real en tus prestaciones
                    </h3>
                    <p class="text-gray-600 text-sm">No te fijes solo en el porcentaje genérico. Pregunta cuánto te bonifican realmente por consulta de especialidad, kinesiología o exámenes, que es lo que más usarás.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 5: ¿Cuánto cuesta un plan de salud individual?       -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cuánto cuesta un plan de salud individual en Chile?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                El costo mensual depende de tu renta imponible, ya que se descuenta de tu 7% legal
                obligatorio. Si tu 7% es mayor que el precio del plan, generas excedentes que puedes usar
                en bonos y atenciones. Nuestra asesoría para encontrar el mejor plan es 100% gratuita.
            </div>

            <p class="text-gray-600 mb-4">
                Muchas personas creen que un plan individual es más caro que uno familiar. La realidad
                es que:
            </p>

            <ul class="space-y-2 text-gray-600 mb-6">
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>No pagas comisión ni honorarios.</strong> Nuestro servicio es 100% gratuito. Nos financia la Isapre de destino, no tú.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>El 7% es obligatorio por ley.</strong> Ya lo estás pagando, la pregunta es si lo estás usando bien. Nosotros nos aseguramos de que así sea.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>Puedes terminar pagando menos que en Fonasa.</strong> Si tu 7% genera excedentes, ese dinero vuelve a ti en forma de bonos, copagos reducidos y atenciones sin desembolso.</span>
                </li>
            </ul>

            <div class="p-5 bg-blue-50 rounded-xl border border-blue-100 mt-4">
                <p class="text-gray-700 font-medium">
                    <iconify-icon icon="mdi:lightbulb-on-outline" class="text-blue-600 inline mr-2" width="20"></iconify-icon>
                    <strong>Dato clave:</strong> Los planes individuales suelen tener primas más bajas que los familiares porque solo aseguran a una persona. Esto significa que, con el mismo 7%, puedes acceder a mejores coberturas.
                </p>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 6: ¿Puedo contratar un plan individual con preexistencias? -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Puedo contratar un plan individual si tengo preexistencias?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Sí, puedes contratar un plan individual aunque tengas preexistencias. Analizamos tu
                historial médico de forma confidencial antes de postular para asegurar que la
                contratación sea aprobada sin rechazos ni restricciones. No enviamos tu declaración
                a ciegas: primero evaluamos y luego actuamos.
            </div>

            <p class="text-gray-600 mb-6">
                Este es el mayor temor de quienes quieren contratar un plan teniendo condiciones
                preexistentes, y es completamente entendible. Así manejamos las preexistencias
                en Plan Salud Fácil:
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="p-5 bg-amber-50 rounded-xl border border-amber-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:shield-check" class="text-amber-600" width="20"></iconify-icon>
                        Evaluación previa confidencial
                    </h3>
                    <p class="text-gray-600 text-sm">Revisamos tu caso antes de enviar cualquier documento a las Isapres. Si detectamos riesgo de rechazo, te lo decimos y buscamos alternativas viables.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:file-document-check" class="text-green-600" width="20"></iconify-icon>
                        Postulación informada y segura
                    </h3>
                    <p class="text-gray-600 text-sm">Solo postulamos a las Isapres donde tus preexistencias tienen mayor probabilidad de ser aceptadas sin restricciones, basándonos en datos reales del mercado.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- FORMULARIO DE CONTACTO (CTA FINAL)                            -->
        <!-- ============================================================ -->
        <div id="formulario">
            <?php render_component('formulario_individual'); ?>
        </div>

        <!-- ============================================================ -->
        <!-- BLOG CLUSTER: Contenido relacionado                          -->
        <!-- ============================================================ -->
        <div class="mt-16">
            <?php 
            $titulo = 'Guías y Consejos para Profesionales y Jóvenes';
            $limite = 3;
            $categoria_id = 12;
            include __DIR__ . '/../../components/ultimos_articulos_blog.php'; 
            ?>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
