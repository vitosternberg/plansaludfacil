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

$page_title = 'Planes de Salud Familiar: Protege a Quienes Más Amas | Plan Salud Fácil';
$meta_description = 'Cotiza y compara planes de Isapre familiares. Unifica las cargas de tu hogar, optimiza excedentes y accede a las mejores clínicas. Asesoría 100% gratis y online.';
include __DIR__ . '/../../layout/plantilla.php'; 
include __DIR__ . '/../../layout/header.php';
?>



<!-- ============================================================ -->
<!-- GEO WRITING STANDARD: Pyramid structure, direct answers, FAQ  -->
<!-- ============================================================ -->

<!-- FAQ Structured Data (Schema.org) para Google AI Overview -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "¿Qué es un plan de salud familiar y quiénes lo necesitan?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Es un contrato de salud previsional que agrupa a un titular y sus cargas legales (cónyuge, hijos) bajo una misma póliza. Ideal para familias que quieren unificar su presupuesto de salud, compartir excedentes y acceder a mejores coberturas con un solo contrato."
      }
    },
    {
      "@type": "Question",
      "name": "¿Conviene unificar las cotizaciones en pareja en un plan familiar?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sí, conviene unificar cuando ambos cotizan y tienen cargas comunes. Permite sumar los ingresos para acceder a un plan de mejor categoría, usar los excedentes de un miembro para cubrir gastos de otro, y simplificar la administración con un solo contrato familiar."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cuáles son las mejores coberturas para un plan familiar?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Un buen plan familiar debe incluir: alta cobertura hospitalaria (sobre 80%), topes pediátricos amplios para consultas y urgencias, bonificación en clínicas preferentes, y cobertura ambulatoria robusta para exámenes y procedimientos de todos los miembros del grupo."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cuánto cuesta un plan de salud familiar en Chile?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "El costo depende de la renta imponible de los cotizantes y del número de cargas. Se descuenta del 7% legal obligatorio de cada titular. Si los ingresos son altos, generan excedentes que pueden usarse en bonos y atenciones para toda la familia. Nuestra asesoría es 100% gratuita."
      }
    },
    {
      "@type": "Question",
      "name": "¿Puedo contratar un plan familiar si algún miembro tiene preexistencias?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sí, puedes contratar un plan familiar aunque algún miembro tenga preexistencias. Analizamos el historial médico del grupo de forma confidencial antes de postular para asegurar que la contratación sea aprobada sin restricciones. No enviamos declaraciones a ciegas."
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
.cta-gradient { background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%); transition: all 0.3s ease; }
.cta-gradient:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
</style>

<main class="bg-gray-50 font-sans pb-20">
    <div class="max-w-4xl mx-auto px-4 pt-12">

        <!-- ============================================================ -->
        <!-- IMAGEN PRINCIPAL DEL SERVICIO                                 -->
        <!-- ============================================================ -->
        <div class="mb-10 text-center">
            <img src="<?= BASE_URL ?>/img/hero_familia.jpg" 
                 alt="Planes de salud familiar e Isapre para el hogar" 
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
                Protege a tu familia con el mejor plan de Isapre <span class="text-blue-600">sin pagar de más ni hacer trámites</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Comparamos todas las Isapres del mercado, encontramos la cobertura ideal para tu grupo familiar
                y gestionamos tu contratación 100% online. En menos de 48 horas.
            </p>
            <a href="#formulario"
               class="cta-gradient inline-flex items-center text-white font-bold py-4 px-8 rounded-xl text-lg shadow-lg hover:shadow-xl transition">
                <iconify-icon icon="mdi:whatsapp" width="24" class="mr-2"></iconify-icon>
                Quiero Cotizar mi Plan Familiar
            </a>
            <p class="text-sm text-gray-400 mt-3">⏱️ Toma menos de 2 minutos · Asesoría 100% gratis</p>
        </section>

        <!-- ============================================================ -->
        <!-- Banner Monoparental (cross-link)                             -->
        <!-- ============================================================ -->
        <div class="bg-blue-50 border border-blue-100 p-4 mb-12 rounded-xl flex items-center justify-between flex-wrap gap-4">
            <div>
                <strong class="text-blue-800 text-lg">¿Eres papá o mamá soltero/a?</strong>
                <p class="text-blue-700 text-sm mt-1">Tenemos un plan enfocado en optimizar hogares de un solo ingreso.</p>
            </div>
            <a href="<?= BASE_URL ?>/servicios/planes-monoparental" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm text-sm whitespace-nowrap">
                Ver Planes Monoparentales →
            </a>
        </div>

        <!-- ============================================================ -->
        <!-- SECCIÓN 1: ¿Qué es un plan de salud familiar?                -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Qué es un plan de salud familiar y quiénes lo necesitan?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Es un contrato de salud previsional que agrupa a un titular y sus cargas legales
                (cónyuge o conviviente civil, hijos) bajo una misma póliza. Ideal para familias que
                quieren unificar su presupuesto de salud, compartir excedentes y acceder a mejores
                coberturas con un solo contrato.
            </div>

            <p class="text-gray-600 mb-6">
                La salud de tu familia no debería depender de múltiples pólizas desconectadas. Cuando
                tienes personas a tu cargo, las necesidades médicas se multiplican: vacunas, consultas
                pediátricas, exámenes preventivos y urgencias. Estas son las tres situaciones más
                comunes donde un plan familiar es la decisión correcta:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Familia con hijos en edad escolar</h3>
                    <p class="text-gray-600 text-sm">Consultas pediátricas, urgencias y ortodoncia son recurrentes. Un buen plan familiar reduce drásticamente los copagos y te da acceso a las mejores clínicas.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">Pareja que planifica tener hijos</h3>
                    <p class="text-gray-600 text-sm">Unificar antes del embarazo te permite acceder a cobertura de parto, neonatología y pediatría desde el día uno, sin periodos de carencia sorpresivos.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-green-200">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Hogar con adulto mayor a cargo</h3>
                    <p class="text-gray-600 text-sm">Si cuidas a un padre o madre mayor, el plan familiar te permite unificar coberturas ambulatorias y hospitalarias para todas las generaciones del hogar.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 2: ¿Cómo funciona el proceso de cotización?          -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cómo funciona el proceso de cotización de un plan familiar?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                En Plan Salud Fácil analizamos la composición de tu grupo familiar, comparamos todas
                las Isapres y te presentamos las mejores opciones en menos de 48 horas. Tú solo nos
                cuentas quiénes integran tu familia y nosotros hacemos el resto.
            </div>

            <p class="text-gray-600 mb-6">
                Hacerlo por tu cuenta implica calcular primas para cada carga, comparar tablas de
                factores por tramo de edad y arriesgarte a elegir un plan que no cubra lo que tu
                familia realmente necesita. Nuestro proceso simplifica todo en tres pasos:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Análisis de tu grupo familiar</h3>
                    <p class="text-gray-600 text-sm">Revisamos la edad, sexo y necesidades médicas de cada carga para identificar el plan que optimiza la relación precio-cobertura para todo el grupo.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">Comparativa Multi-Isapre</h3>
                    <p class="text-gray-600 text-sm">Evaluamos simultáneamente las ofertas de Banmédica, Colmena, Consalud, Cruz Blanca, Nueva Masvida y Vida Tres para mostrarte solo las que convienen a tu familia.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-green-200">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Contratación 100% Digital</h3>
                    <p class="text-gray-600 text-sm">Gestionamos las Declaraciones de Salud de cada miembro y firman el nuevo contrato de manera online y 100% legal, sin moverte de tu casa ni hacer filas.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 3: ¿Conviene unificar cotizaciones en pareja?        -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Conviene unificar las cotizaciones en pareja en un plan familiar?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Sí, conviene unificar cuando ambos cotizan y tienen cargas comunes. Permite sumar
                los ingresos de la pareja para acceder a un plan de mejor categoría, usar los
                excedentes de un miembro para cubrir gastos del otro, y simplificar la
                administración con un solo contrato familiar en vez de dos o más separados.
            </div>

            <p class="text-gray-600 mb-6">
                Muchas parejas mantienen planes separados por inercia, sin saber que unificar
                puede traer beneficios financieros y de cobertura importantes:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-blue-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-blue-200">🤝</div>
                    <strong class="block text-gray-900 mb-2">Acceso a mejor plan</strong>
                    <p class="text-gray-600 text-sm">La suma de rentas de ambos cotizantes permite optar a
                    planes de categoría superior con mejores clínicas y mayor cobertura hospitalaria.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-purple-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-purple-200">💰</div>
                    <strong class="block text-gray-900 mb-2">Excedentes compartidos</strong>
                    <p class="text-gray-600 text-sm">Los excedentes generados por uno pueden financiar
                    bonos, medicamentos o exámenes de cualquiera de las cargas, maximizando el uso
                    del presupuesto de salud del hogar.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-green-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-green-200">📋</div>
                    <strong class="block text-gray-900 mb-2">Una sola administración</strong>
                    <p class="text-gray-600 text-sm">Un solo contrato, una sola fecha de pago, una sola
                    declaración de salud grupal. Menos trámites, menos papeles y control total del
                    gasto en salud familiar mes a mes.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 4: Coberturas clave para un plan familiar            -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cuáles son las coberturas clave que debe tener un plan familiar?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Un buen plan familiar debe incluir tres coberturas esenciales: alta cobertura
                hospitalaria (sobre 80%) para proteger tu patrimonio ante una cirugía, topes
                pediátricos amplios para consultas y urgencias infantiles, y bonificación robusta
                en clínicas preferentes para que toda la familia se atienda donde realmente quieres.
            </div>

            <p class="text-gray-600 mb-6">
                Cuando se trata del bienestar de tu familia, no todos los planes son iguales.
                Para garantizar que estén verdaderamente protegidos frente a cualquier imprevisto,
                enfócate en estos tres pilares:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-5 bg-blue-50 rounded-xl border border-blue-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:hospital-building" class="text-blue-600" width="20"></iconify-icon>
                        Alta Cobertura Hospitalaria
                    </h3>
                    <p class="text-gray-600 text-sm">Busca planes con bonificación sobre el 80% en cirugías e
                    intervenciones complejas. Un tope bajo en hospitalización puede significar deudas
                    millonarias para tu familia.</p>
                </div>
                <div class="p-5 bg-purple-50 rounded-xl border border-purple-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:baby-face-outline" class="text-purple-600" width="20"></iconify-icon>
                        Pediatría y Urgencias 24/7
                    </h3>
                    <p class="text-gray-600 text-sm">Topes altos y copagos reducidos para consultas pediátricas
                    recurrentes, vacunas y atención de urgencia en los centros más cercanos a tu
                    hogar, sin límites por evento.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:stethoscope" class="text-green-600" width="20"></iconify-icon>
                        Cobertura Ambulatoria Robusta
                    </h3>
                    <p class="text-gray-600 text-sm">Exámenes preventivos, consultas de especialidad,
                    kinesiología y salud mental para todos los miembros del grupo, sin tener que
                    pagar diferencias excesivas de bolsillo.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 5: ¿Cuánto cuesta un plan de salud familiar?         -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cuánto cuesta un plan de salud familiar en Chile?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                El costo mensual depende de la renta imponible de los cotizantes y del número de
                cargas. Se descuenta del 7% legal obligatorio de cada titular. Si los ingresos son
                altos, generan excedentes que pueden usarse en bonos y atenciones para toda la
                familia. Nuestra asesoría para encontrar el mejor plan es 100% gratuita.
            </div>

            <p class="text-gray-600 mb-4">
                Muchas familias creen que un plan familiar es más caro que varios individuales.
                La realidad es que:
            </p>

            <ul class="space-y-2 text-gray-600 mb-6">
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>No pagas comisión ni honorarios.</strong> Nuestro servicio es 100% gratuito. Nos financia la Isapre de destino, no tú.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>Las primas grupales suelen ser más eficientes.</strong> Al asegurar a varias personas bajo un mismo contrato, el riesgo se distribuye y puedes acceder a mejores condiciones que con planes individuales separados.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>El 7% ya lo estás pagando.</strong> La pregunta no es si gastar o no, sino si lo estás usando bien. Un plan bien diseñado te da más por el mismo descuento legal.</span>
                </li>
            </ul>

            <div class="p-5 bg-blue-50 rounded-xl border border-blue-100 mt-4">
                <p class="text-gray-700 font-medium">
                    <iconify-icon icon="mdi:lightbulb-on-outline" class="text-blue-600 inline mr-2" width="20"></iconify-icon>
                    <strong>Dato clave:</strong> En un plan familiar, si uno de los cotizantes gana más, su 7% más alto puede cubrir las primas de todo el grupo. La pareja con menor renta puede generar excedentes que se transforman en bonos para copagos, medicamentos y atenciones dentales para todos.
                </p>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 6: ¿Puedo contratar un plan familiar con preexistencias? -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Puedo contratar un plan familiar si algún miembro tiene preexistencias?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Sí, puedes contratar un plan familiar aunque algún miembro del grupo tenga
                preexistencias. Analizamos el historial médico de cada integrante de forma
                confidencial antes de postular para asegurar que la contratación sea aprobada
                sin restricciones. No enviamos declaraciones a ciegas: primero evaluamos y
                luego actuamos.
            </div>

            <p class="text-gray-600 mb-6">
                Este es uno de los mayores temores al contratar un plan familiar, especialmente
                cuando hay niños con condiciones crónicas o adultos con tratamientos en curso.
                Así manejamos las preexistencias en Plan Salud Fácil:
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="p-5 bg-amber-50 rounded-xl border border-amber-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:shield-check" class="text-amber-600" width="20"></iconify-icon>
                        Evaluación previa confidencial por persona
                    </h3>
                    <p class="text-gray-600 text-sm">Revisamos el historial de cada miembro antes de enviar cualquier documento. Si detectamos riesgo de rechazo para alguien, te lo decimos y buscamos la Isapre más flexible para ese perfil.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:file-document-check" class="text-green-600" width="20"></iconify-icon>
                        Postulación grupal informada
                    </h3>
                    <p class="text-gray-600 text-sm">Solo postulamos a las Isapres donde el perfil completo de tu familia tiene mayor probabilidad de ser aceptado sin restricciones, basándonos en datos reales del mercado.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- FORMULARIO DE CONTACTO (CTA FINAL)                            -->
        <!-- ============================================================ -->
        <div id="formulario">
            <?php render_component('formulario_familia'); ?>
        </div>

        <!-- ============================================================ -->
        <!-- BLOG CLUSTER: Contenido relacionado desde WordPress           -->
        <!-- ============================================================ -->
        <div class="mt-16">
            <?php 
            $titulo = 'Guías y Consejos de Salud Familiar';
            $limite = 3;
            $categoria_id = 11; // ID de la categoría "Planes Familiares" en WordPress
            include __DIR__ . '/../../components/ultimos_articulos_blog.php'; 
            ?>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
