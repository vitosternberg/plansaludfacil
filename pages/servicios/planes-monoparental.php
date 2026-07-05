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

$page_title = 'Planes de Salud Monoparentales: Protege a tus Hijos con un Solo Ingreso | Plan Salud Fácil';
$meta_description = 'Cotiza y compara planes de Isapre para familias monoparentales. Optimiza tu 7% como sostén único, accede a coberturas pediátricas y urgencias 24/7. Asesoría 100% gratis y online.';
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
      "name": "¿Qué es un plan de salud monoparental y quiénes lo necesitan?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Es un plan de Isapre diseñado para hogares donde un solo adulto es el titular y sostén económico, con hijos u otras cargas legales a su cargo. Ideal para madres y padres solteros, viudos o separados que necesitan maximizar la protección de sus hijos con un solo ingreso."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cómo elegir la mejor Isapre para una familia monoparental?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Debes priorizar tres factores: copagos fijos y bajos en urgencias pediátricas (porque con niños las urgencias son frecuentes), buena cobertura de licencias médicas (si tú te enfermas, el hogar se queda sin ingresos), y topes de cobertura que no se disparen al agregar cargas."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cuánto cuesta un plan monoparental en Chile?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "El costo depende de tu renta imponible y del número de cargas. Se descuenta de tu 7% legal obligatorio. Al ser un solo cotizante, es clave elegir un plan con primas por carga competitivas para que el valor total no se dispare. Nuestra asesoría es 100% gratuita."
      }
    },
    {
      "@type": "Question",
      "name": "¿Qué coberturas son imprescindibles en un plan monoparental?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Las tres coberturas críticas son: red de urgencia pediátrica 24/7 con copago fijo y conocido, buena aprobación de licencias médicas para el titular (porque un mes sin sueldo es catastrófico), y cobertura catastrófica robusta para proteger el patrimonio familiar ante una cirugía mayor."
      }
    },
    {
      "@type": "Question",
      "name": "¿Puedo contratar un plan monoparental si mi hijo tiene preexistencias?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sí, puedes contratar un plan monoparental aunque tu hijo tenga preexistencias. Analizamos el historial médico de forma confidencial antes de postular para asegurar que la contratación sea aprobada sin restricciones. No enviamos declaraciones a ciegas: primero evaluamos y luego actuamos."
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
            <img src="<?= BASE_URL ?>/img/madre_orgullosa.jpg" 
                 alt="Plan de Isapre monoparental para madres y padres solteros con hijos a cargo" 
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
                Protege a tus hijos con el mejor plan de Isapre <span class="text-blue-600">sin desestabilizar tu presupuesto</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Comparamos todas las Isapres del mercado, encontramos coberturas que cuidan tu bolsillo
                y gestionamos tu contratación 100% online. En menos de 48 horas.
            </p>
            <a href="#formulario"
               class="cta-gradient inline-flex items-center text-white font-bold py-4 px-8 rounded-xl text-lg shadow-lg hover:shadow-xl transition">
                <iconify-icon icon="mdi:whatsapp" width="24" class="mr-2"></iconify-icon>
                Quiero Cotizar mi Plan Monoparental
            </a>
            <p class="text-sm text-gray-400 mt-3">⏱️ Toma menos de 2 minutos · Asesoría 100% gratis</p>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 1: ¿Qué es un plan monoparental?                     -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Qué es un plan de salud monoparental y quiénes lo necesitan?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Es un plan de Isapre diseñado para hogares donde un solo adulto es el titular y
                sostén económico, con hijos u otras cargas a su cargo. Ideal para madres y padres
                solteros, viudos o separados que necesitan maximizar la protección de sus hijos
                administrando un solo ingreso.
            </div>

            <p class="text-gray-600 mb-6">
                Cuando eres el sostén único del hogar, cada decisión financiera debe estar calculada
                y el tiempo es tu recurso más valioso. Elegir la cobertura de Isapre no debería ser
                un dolor de cabeza ni un gasto desproporcionado. Estas son las tres situaciones más
                comunes donde un plan monoparental es la decisión correcta:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Madre o padre soltero</h3>
                    <p class="text-gray-600 text-sm">Tus hijos dependen exclusivamente de ti. Necesitas un plan
                    que priorice urgencias pediátricas, copagos bajos y cobertura de licencias médicas
                    para proteger tu capacidad de generar ingresos.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">Separado/a con tuición</h3>
                    <p class="text-gray-600 text-sm">Al reconfigurar tu hogar, necesitas un plan que se ajuste
                    a tu nueva realidad financiera sin perder las coberturas que tus hijos ya tenían,
                    pero sin pagar por un adulto que ya no es carga.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-green-200">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Viudo/a con hijos menores</h3>
                    <p class="text-gray-600 text-sm">La prioridad es la estabilidad. Buscas un plan con
                    cobertura catastrófica robusta que proteja el patrimonio familiar ante cualquier
                    emergencia médica, sin desequilibrar el presupuesto mensual.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 2: ¿Cómo funciona el proceso de cotización?          -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cómo funciona el proceso de cotización de un plan monoparental?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                En Plan Salud Fácil analizamos tu caso como titular único, comparamos todas las
                Isapres y te presentamos las opciones más eficientes en menos de 48 horas. Tú solo
                nos cuentas quiénes integran tu familia y nosotros hacemos el resto.
            </div>

            <p class="text-gray-600 mb-6">
                A diferencia de consultar con una sola Isapre, nosotros analizamos el mercado completo
                y ajustamos nuestra recomendación a tu realidad financiera de sostén único. Nuestro
                proceso simplifica todo en tres pasos:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-blue-200">1</div>
                    <h3 class="font-bold text-gray-900 mb-2">Análisis de tu caso</h3>
                    <p class="text-gray-600 text-sm">Revisamos tu edad, renta, número de cargas y ubicación
                    para identificar los planes que optimizan la relación precio-cobertura para tu
                    situación de titular único.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-purple-200">2</div>
                    <h3 class="font-bold text-gray-900 mb-2">Comparativa Multi-Isapre</h3>
                    <p class="text-gray-600 text-sm">Evaluamos simultáneamente las ofertas de Banmédica,
                    Colmena, Consalud, Cruz Blanca, Nueva Masvida y Vida Tres, enfocándonos en
                    primas por carga competitivas y copagos pediátricos bajos.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-green-600 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg shadow-green-200">3</div>
                    <h3 class="font-bold text-gray-900 mb-2">Contratación 100% Digital</h3>
                    <p class="text-gray-600 text-sm">Gestionamos las Declaraciones de Salud de cada
                    miembro de tu familia y firmas el nuevo contrato de manera online y 100% legal,
                    sin moverte de tu casa ni hacer filas.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 3: ¿Cómo elijo la mejor Isapre monoparental?         -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cómo elijo la mejor Isapre para una familia monoparental?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Debes priorizar tres factores: copagos fijos y bajos en urgencias pediátricas
                (porque con niños las urgencias son frecuentes), buena cobertura de licencias
                médicas para el titular (si tú te enfermas, el hogar se queda sin ingresos), y
                topes de cobertura que no se disparen al agregar a tus hijos como cargas.
            </div>

            <p class="text-gray-600 mb-6">
                Como único cotizante, no puedes darte el lujo de elegir mal. Cada peso de tu 7%
                debe trabajar al máximo. Estos son los tres criterios en los que debes enfocarte:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-5 bg-blue-50 rounded-xl border border-blue-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:hospital-building" class="text-blue-600" width="20"></iconify-icon>
                        Red de urgencia pediátrica 24/7
                    </h3>
                    <p class="text-gray-600 text-sm">Debes saber exactamente a qué clínica cercana partir a las
                    3 AM con tus hijos, teniendo claro que el copago será bajo y conocido. No puedes
                    improvisar en una emergencia.</p>
                </div>
                <div class="p-5 bg-purple-50 rounded-xl border border-purple-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:file-document-check" class="text-purple-600" width="20"></iconify-icon>
                        Cobertura de licencias médicas
                    </h3>
                    <p class="text-gray-600 text-sm">Seleccionamos Isapres con buenos historiales de aprobación
                    de licencias. Si tú te enfermas y dejas de percibir tu sueldo un mes, el impacto
                    en tu hogar puede ser devastador.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:cash-multiple" class="text-green-600" width="20"></iconify-icon>
                        Primas por carga competitivas
                    </h3>
                    <p class="text-gray-600 text-sm">Analizamos todo el mercado para encontrar las Isapres con
                    los costos base más bajos por cada carga. Así evitas que el valor total se
                    dispare al agregar a tus hijos, pagando solo lo necesario.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 4: Coberturas imprescindibles                         -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Qué coberturas son imprescindibles en un plan monoparental?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Las tres coberturas críticas son: red de urgencia pediátrica 24/7 con copago fijo
                y conocido, buena aprobación de licencias médicas para el titular (porque un mes
                sin sueldo es catastrófico), y cobertura catastrófica robusta para proteger el
                patrimonio familiar ante una cirugía mayor.
            </div>

            <p class="text-gray-600 mb-6">
                Cuando eres el único ingreso del hogar, hay coberturas que no son negociables.
                Estas tres son las que marcan la diferencia entre estar protegido y estar en riesgo:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-5 bg-blue-50 rounded-xl border border-blue-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:shield-check" class="text-blue-600" width="20"></iconify-icon>
                        Blindaje financiero
                    </h3>
                    <p class="text-gray-600 text-sm">Encontramos planes con cobertura catastrófica robusta para
                    ti (porque si tú fallas, el hogar colapsa) y convenios con copagos fijos mínimos
                    para urgencias pediátricas. Tu bolsillo estará protegido ante cualquier imprevisto.</p>
                </div>
                <div class="p-5 bg-purple-50 rounded-xl border border-purple-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:clock-fast" class="text-purple-600" width="20"></iconify-icon>
                        Ahorro de tiempo y burocracia
                    </h3>
                    <p class="text-gray-600 text-sm">Olvídate de leer contratos engorrosos o perder mañanas en
                    sucursales. Nuestra asesoría es 100% digital, transparente y directa: tú sigues
                    con tu vida mientras nosotros hacemos el trabajo pesado.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:account-child" class="text-green-600" width="20"></iconify-icon>
                        Pediatría sin límites
                    </h3>
                    <p class="text-gray-600 text-sm">Topes altos y copagos reducidos para consultas pediátricas
                    recurrentes, vacunas al día y atención de urgencia 24/7 en los centros más
                    cercanos a tu hogar, sin sorpresas en la cuenta.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 5: ¿Cuánto cuesta un plan monoparental?              -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cuánto cuesta un plan de salud monoparental en Chile?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                El costo mensual depende de tu renta imponible y del número de cargas. Se descuenta
                de tu 7% legal obligatorio. Al ser un solo cotizante, es clave elegir un plan con
                primas por carga competitivas para que el valor total no se dispare. Nuestra
                asesoría para encontrar el mejor plan es 100% gratuita.
            </div>

            <p class="text-gray-600 mb-4">
                Muchas familias monoparentales creen que un plan de Isapre está fuera de su alcance.
                La realidad es que:
            </p>

            <ul class="space-y-2 text-gray-600 mb-6">
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>No pagas comisión ni honorarios.</strong> Nuestro servicio es 100% gratuito. Nos financia la Isapre de destino, no tú.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>El 7% ya lo estás pagando por ley.</strong> La pregunta no es si gastar, sino si lo estás usando bien. Un plan bien diseñado te da más protección por el mismo descuento.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>Hay Isapres con primas por carga muy competitivas.</strong> No todas cobran lo mismo por agregar a tus hijos. Nosotros encontramos las que tienen los costos base más bajos para que el valor total sea manejable.</span>
                </li>
            </ul>

            <div class="p-5 bg-blue-50 rounded-xl border border-blue-100 mt-4">
                <p class="text-gray-700 font-medium">
                    <iconify-icon icon="mdi:lightbulb-on-outline" class="text-blue-600 inline mr-2" width="20"></iconify-icon>
                    <strong>Dato clave:</strong> En un plan monoparental, si tu 7% genera excedentes, ese dinero se puede usar en bonos de medicamentos, lentes ópticos y atenciones ambulatorias para ti y tus hijos sin copago adicional. Es tu dinero trabajando para tu familia.
                </p>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SECCIÓN 6: ¿Puedo contratar con preexistencias?              -->
        <!-- ============================================================ -->
        <section class="mb-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Puedo contratar un plan monoparental si mi hijo tiene preexistencias?
            </h2>

            <!-- RESPUESTA DIRECTA -->
            <div class="answer-direct">
                Sí, puedes contratar un plan monoparental aunque tu hijo tenga preexistencias.
                Analizamos el historial médico de cada miembro de tu familia de forma confidencial
                antes de postular para asegurar que la contratación sea aprobada sin restricciones.
                No enviamos declaraciones a ciegas: primero evaluamos y luego actuamos.
            </div>

            <p class="text-gray-600 mb-6">
                Este es uno de los mayores temores al contratar un plan, especialmente cuando hay
                niños con condiciones crónicas o tratamientos en curso. Así manejamos las
                preexistencias en Plan Salud Fácil:
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="p-5 bg-amber-50 rounded-xl border border-amber-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:shield-check" class="text-amber-600" width="20"></iconify-icon>
                        Evaluación previa confidencial por persona
                    </h3>
                    <p class="text-gray-600 text-sm">Revisamos el historial de cada miembro antes de enviar cualquier documento. Si detectamos riesgo de rechazo para tu hijo, te lo decimos y buscamos la Isapre más flexible para ese perfil pediátrico.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:file-document-check" class="text-green-600" width="20"></iconify-icon>
                        Postulación informada y segura
                    </h3>
                    <p class="text-gray-600 text-sm">Solo postulamos a las Isapres donde el perfil completo de tu familia monoparental tiene mayor probabilidad de ser aceptado sin restricciones, basándonos en datos reales del mercado.</p>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- FORMULARIO DE CONTACTO (CTA FINAL)                            -->
        <!-- ============================================================ -->
        <div id="formulario">
            <?php render_component('formulario_familia', ['es_monoparental' => true]); ?>
        </div>

        <!-- ============================================================ -->
        <!-- BLOG CLUSTER: Contenido relacionado desde WordPress           -->
        <!-- ============================================================ -->
        <div class="mt-16">
            <?php 
            $titulo = 'Guías y Consejos de Salud Monoparental';
            $limite = 3;
            $categoria_id = 13; // ID de la categoría "Planes Mono Parentales" en WordPress
            include __DIR__ . '/../../components/ultimos_articulos_blog.php'; 
            ?>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
