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

$page_title = 'Cambio de Isapre: Asesoría Gratuita 100% Online | Plan Salud Fácil';
$meta_description = 'Te ayudamos a cambiarte de Isapre gratis y sin trámites. Comparamos todas las Isapres, gestionamos tu Declaración de Salud y firmas online. Asesoría 100% digital.';
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
      "name": "¿Me conviene cambiarme de Isapre?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sí, conviene cambiarse si han pasado más de 12 meses desde que contrataste tu plan actual y tu situación de vida o ingresos cambió. Podrías acceder a mejores coberturas por el mismo 7% de cotización."
      }
    },
    {
      "@type": "Question",
      "name": "¿Qué necesito para cambiarme de Isapre?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Solo necesitas tener al menos un año de antigüedad en tu Isapre actual, no estar con licencia médica vigente y completar la Declaración de Salud. Nosotros te guiamos en cada paso."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cómo funciona el proceso de cambio de Isapre?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Nosotros comparamos todas las Isapres del mercado por ti, gestionamos tu Declaración de Salud y firmas el nuevo contrato 100% online. El proceso completo toma menos de 48 horas."
      }
    },
    {
      "@type": "Question",
      "name": "¿Cuánto cuesta cambiarse de Isapre?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Nuestro servicio de asesoría y gestión de cambio es 100% gratuito. Solo pagas la cotización mensual de tu nuevo plan de salud, que se descuenta de tu 7% legal obligatorio."
      }
    },
    {
      "@type": "Question",
      "name": "¿Puedo cambiarme de Isapre si tengo preexistencias?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sí, puedes cambiarte aunque tengas preexistencias. Analizamos tu historial médico de forma confidencial antes de postular para asegurar que el cambio sea aprobado sin contratiempos ni rechazos."
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

        <div class="mb-10 text-center">
            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?auto=format&fit=crop&w=1200&q=80" 
                 alt="Asesoría para cambio de Isapre online" 
                 class="w-full h-auto rounded-2xl shadow-xl object-cover max-h-[400px]">
        </div>

        <!-- HERO: Respuesta directa a la consulta principal del usuario -->
        <section class="mb-16 text-center">
            <span class="inline-block bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider px-4 py-1.5 rounded-full mb-4">
                Servicio 100% Gratuito
            </span>
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 mb-4 leading-tight">
                Te ayudamos a cambiarte de Isapre <span class="text-blue-600">gratis y sin hacer trámites</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Comparamos todas las Isapres del mercado, gestionamos tu Declaración de Salud
                y firmas tu nuevo contrato 100% online. En menos de 48 horas.
            </p>
            <a href="#formulario"
               class="cta-gradient inline-flex items-center text-white font-bold py-4 px-8 rounded-xl text-lg shadow-lg hover:shadow-xl transition">
                <iconify-icon icon="mdi:whatsapp" width="24" class="mr-2"></iconify-icon>
                Quiero Cambiarme de Isapre
            </a>
            <p class="text-sm text-gray-400 mt-3">⏱️ Toma menos de 2 minutos · Asesoría 100% gratis</p>
        </section>

        <!-- SECCIÓN 1: ¿Me conviene cambiarme de Isapre? -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Me conviene cambiarme de Isapre?
            </h2>

            <div class="answer-direct">
                Sí, conviene cambiarse si han pasado más de 12 meses desde que contrataste tu plan actual
                y tu situación de vida o ingresos cambió. Podrías acceder a mejores coberturas por el mismo
                7% de cotización obligatoria, sin pagar ni un peso extra.
            </div>

            <p class="text-gray-600 mb-6">
                La mayoría de las personas se mantienen en su Isapre por costumbre, perdiendo la oportunidad
                de acceder a mejores beneficios. El mercado de la salud en Chile cambia constantemente: lo que
                ayer era un excelente plan, hoy puede estar obsoleto. Estas son las tres razones más comunes
                por las que nuestros clientes deciden cambiarse:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-sky-50 p-6 rounded-xl">
                    <div class="text-3xl mb-3">💰</div>
                    <h3 class="font-bold text-gray-900 mb-2">Tu sueldo aumentó</h3>
                    <p class="text-gray-600 text-sm">Si tu 7% legal ahora es mayor, estás generando excedentes
                    que podrías aprovechar mejor en un plan con coberturas más altas y mejores clínicas.</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-6 rounded-xl">
                    <div class="text-3xl mb-3">👨‍👩‍👧‍👦</div>
                    <h3 class="font-bold text-gray-900 mb-2">Cambió tu estructura familiar</h3>
                    <p class="text-gray-600 text-sm">Si vas a tener un hijo, tus hijos crecieron y salieron del plan,
                    o te casaste, tu perfil de riesgo cambió y necesitas otra aseguradora.</p>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-6 rounded-xl">
                    <div class="text-3xl mb-3">🏥</div>
                    <h3 class="font-bold text-gray-900 mb-2">No te sirven las clínicas actuales</h3>
                    <p class="text-gray-600 text-sm">Si tu Isapre actual no tiene buena cobertura en los centros
                    médicos cercanos a tu hogar o trabajo, estás pagando por un servicio que no usas.</p>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 2: ¿Qué necesito para cambiarme de Isapre? -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Qué necesito para cambiarme de Isapre?
            </h2>

            <div class="answer-direct">
                Solo necesitas tres cosas: tener al menos un año de antigüedad en tu Isapre actual,
                no estar con licencia médica vigente, y completar la Declaración de Salud.
                Nosotros te guiamos en cada paso para que no tengas que descifrar nada por tu cuenta.
            </div>

            <p class="text-gray-600 mb-6">
                Estos son los requisitos estándar del sistema previsional chileno. Si cumples con ellos,
                podemos iniciar tu cambio de inmediato:
            </p>

            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">1</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Un año de antigüedad como cotizante titular</h3>
                        <p class="text-gray-600 text-sm mt-1">Este es un requisito legal de la Superintendencia de Salud. Si eres carga y quieres pasar a ser titular, aplican reglas distintas — consúltanos.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">2</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">No estar con licencia médica al momento de firmar</h3>
                        <p class="text-gray-600 text-sm mt-1">Si estás con licencia, podemos preparar todo para que el cambio se active apenas finalice. No pierdes la oportunidad.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-lg">3</div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Completar la Declaración de Salud</h3>
                        <p class="text-gray-600 text-sm mt-1">Revisamos tu historial médico de forma confidencial antes de enviar la declaración. Así nos aseguramos de que no haya rechazos ni sorpresas.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 3: ¿Cómo funciona el proceso? -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cómo funciona el proceso de cambio de Isapre?
            </h2>

            <div class="answer-direct">
                Nosotros comparamos todas las Isapres del mercado por ti, gestionamos tu Declaración
                de Salud y firmas el nuevo contrato 100% online. El proceso completo toma menos de
                48 horas y tú no tienes que moverte de tu casa.
            </div>

            <p class="text-gray-600 mb-6">
                Hacer el trámite por tu cuenta implica visitar múltiples sitios web, descifrar tablas
                de factores y arriesgarte a elegir mal. Nuestro servicio simplifica todo en tres pasos:
            </p>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-blue-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-blue-200">1</div>
                    <strong class="block text-gray-900 mb-2">Comparativa Multi-Isapre</strong>
                    <p class="text-gray-600 text-sm">Evaluamos simultáneamente las ofertas vigentes de todas las Isapres
                    para mostrarte solo aquellas que superan las coberturas de tu plan actual.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-purple-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-purple-200">2</div>
                    <strong class="block text-gray-900 mb-2">Análisis de Preexistencias</strong>
                    <p class="text-gray-600 text-sm">Revisamos tu historial médico de forma confidencial antes de postular
                    para garantizar que el cambio sea seguro y aprobado sin contratiempos.</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 text-center">
                    <div class="w-14 h-14 bg-green-600 text-white rounded-xl flex items-center justify-center mx-auto mb-4 text-xl font-bold shadow-lg shadow-green-200">3</div>
                    <strong class="block text-gray-900 mb-2">Firma 100% Digital</strong>
                    <p class="text-gray-600 text-sm">No necesitas ir a ninguna sucursal. Validamos la Declaración de Salud
                    y firmas el nuevo contrato de manera online y 100% legal.</p>
                </div>
            </div>
        </section>

        <!-- SECCIÓN 4: ¿Cuánto cuesta cambiarse de Isapre? -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Cuánto cuesta cambiarse de Isapre?
            </h2>

            <div class="answer-direct">
                Nuestro servicio de asesoría y gestión de cambio es 100% gratuito. No pagas nada por
                nuestro trabajo. Solo pagas la cotización mensual de tu nuevo plan de salud, que se
                descuenta automáticamente de tu 7% legal obligatorio, igual que siempre.
            </div>

            <p class="text-gray-600 mb-4">
                Muchas personas creen que cambiarse de Isapre tiene costos ocultos o comisiones. La realidad es que:
            </p>
            <ul class="space-y-3 text-gray-600 mb-6">
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>No cobramos comisión ni honorarios.</strong> Nos financia la Isapre de destino, no tú.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>No hay multas por cambio.</strong> La ley chilena te permite cambiarte una vez cumplido el año de antigüedad sin penalización.</span>
                </li>
                <li class="flex items-start gap-2">
                    <iconify-icon icon="mdi:check-circle" class="text-green-500 mt-0.5 flex-shrink-0" width="20"></iconify-icon>
                    <span><strong>El nuevo plan puede costar lo mismo o menos.</strong> En muchos casos, encuentras mejores coberturas sin aumentar tu cotización mensual.</span>
                </li>
            </ul>
        </section>

        <!-- SECCIÓN 5: ¿Puedo cambiarme si tengo preexistencias? -->
        <section class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                ¿Puedo cambiarme de Isapre si tengo preexistencias?
            </h2>

            <div class="answer-direct">
                Sí, puedes cambiarte aunque tengas preexistencias. Analizamos tu historial médico de
                forma confidencial antes de postular para asegurar que el cambio sea aprobado sin
                contratiempos. No enviamos tu declaración a ciegas: primero evaluamos y luego actuamos.
            </div>

            <p class="text-gray-600 mb-6">
                Este es el mayor temor de quienes quieren cambiarse, y es completamente entendible.
                Así manejamos las preexistencias en Plan Salud Fácil:
            </p>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="p-5 bg-amber-50 rounded-xl border border-amber-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:shield-check" class="text-amber-600" width="20"></iconify-icon>
                        Evaluación previa confidencial
                    </h3>
                    <p class="text-gray-600 text-sm">Revisamos tu caso antes de enviar cualquier documento a las Isapres. Si detectamos riesgo de rechazo, te lo decimos y buscamos alternativas.</p>
                </div>
                <div class="p-5 bg-green-50 rounded-xl border border-green-100">
                    <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <iconify-icon icon="mdi:file-document-check" class="text-green-600" width="20"></iconify-icon>
                        Postulación informada
                    </h3>
                    <p class="text-gray-600 text-sm">Solo postulamos a las Isapres donde tus preexistencias tienen mayor probabilidad de ser aceptadas, basándonos en datos reales del mercado.</p>
                </div>
            </div>
        </section>

        <!-- FORMULARIO DE CONTACTO (CTA FINAL) -->
        <div id="formulario">
            <?php include __DIR__ . '/../../components/formulario_individual.php'; ?>
        </div>

        <!-- BLOG CLUSTER -->
        <div class="mt-16">
            <?php 
            $titulo = 'Guías y Consejos sobre Isapres';
            $limite = 3;
            $categoria_id = 8;
            include __DIR__ . '/../../components/ultimos_articulos_blog.php'; 
            ?>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
