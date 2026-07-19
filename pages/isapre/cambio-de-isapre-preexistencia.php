<?php
/**
 * isapres/cambio-de-isapre/con-preexistencia.php
 * Sub-página SEO: Cambio de Isapre con Preexistencia
 */

require_once __DIR__ . '/../../omniflow_config.php';

$page_title       = 'Cambio de Isapre con Preexistencia: Guía Completa | Plan Salud Fácil';
$meta_description = '¿Tienes preexistencias y quieres cambiarte de isapre? Te explicamos cómo funciona, qué isapres aceptan y cómo maximizar tus probabilidades de aprobación. Asesoría gratuita.';
$h1               = 'Cambio de Isapre con Preexistencia';
$lead             = 'Tener una condición de salud preexistente no te impide cambiarte de isapre. Con la estrategia correcta, puedes encontrar un plan que te acepte sin restricciones o con las mínimas posibles.';
$svc_name         = 'Cambio de Isapre con Preexistencia';
$svc_description  = 'Guía para cambiarte de isapre teniendo preexistencias: qué isapres son más flexibles, cómo completar la Declaración de Salud y qué esperar del proceso.';
$cta_texto = 'Cotiza Express';
$cta_link         = BASE_URL.'/planes/comparador/';

$breadcrumbs = [
    ['label' => 'Inicio', 'url' => 'BASE_URL/'],
    ['label' => 'Isapres', 'url' => 'BASE_URL/isapres/'],
    ['label' => 'Cambio de Isapre', 'url' => 'BASE_URL/isapres/cambio-de-isapre/'],
    ['label' => 'Con Preexistencia', 'url' => '#']
];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

$toc_items = [
    ['id' => 'que-es', 'label' => '¿Qué es una preexistencia?'],
    ['id' => 'proceso', 'label' => 'El proceso de cambio'],
    ['id' => 'isapres', 'label' => 'Isapres más flexibles'],
    ['id' => 'consejos', 'label' => 'Consejos prácticos'],
];

ob_start();
?>
<section id="que-es" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué se considera una preexistencia?</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Una preexistencia es cualquier condición de salud diagnosticada, en tratamiento o conocida antes de contratar un nuevo plan de isapre. Puede incluir enfermedades crónicas (diabetes, hipertensión), condiciones de salud mental, lesiones, cirugías previas o embarazo en curso.</p>
    <p class="text-gray-700 leading-relaxed">La ley chilena permite que las isapres evalúen las preexistencias al momento de la afiliación, pero <strong>no pueden rechazarte arbitrariamente</strong>. Deben justificar cualquier restricción.</p>
</section>

<section id="proceso" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">El proceso de cambio con preexistencias</h2>
    <ol class="list-decimal pl-6 text-gray-700 space-y-3">
        <li><strong>Análisis confidencial:</strong> Revisamos tu historial médico de forma 100% privada antes de enviar cualquier documento a las isapres.</li>
        <li><strong>Selección de isapres:</strong> Identificamos cuáles isapres tienen mayor probabilidad de aceptar tu perfil sin restricciones o con las mínimas posibles.</li>
        <li><strong>Declaración de Salud:</strong> Te ayudamos a completarla correctamente. La omisión puede causar la anulación del contrato.</li>
        <li><strong>Respuesta de la isapre:</strong> La isapre puede aceptar sin restricciones, aceptar con restricciones (carecer de ciertas coberturas por un tiempo) o rechazar. Si rechaza, postulamos a la siguiente mejor opción.</li>
    </ol>
</section>

<section id="isapres" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Isapres más flexibles con preexistencias</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Cada isapre tiene políticas distintas para evaluar preexistencias. Algunas son más restrictivas que otras. Basado en datos reales de postulaciones, estas isapres suelen tener mayores tasas de aceptación:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li><strong>Banmédica:</strong> Alta tasa de aceptación. Evalúa caso a caso.</li>
        <li><strong>Cruz Blanca:</strong> Flexible con condiciones controladas.</li>
        <li><strong>Colmena:</strong> Buena opción para preexistencias leves.</li>
    </ul>
    <p class="text-sm text-gray-500 mt-3">*Esto es una referencia basada en nuestra experiencia. Cada caso se evalúa individualmente.</p>
</section>

<section id="consejos" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Consejos prácticos</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li><strong>No omitas información</strong> en la Declaración de Salud. Si la isapre descubre una omisión, puede anular tu contrato.</li>
        <li><strong>Solicita tu historial médico</strong> antes de iniciar el proceso para saber exactamente qué información tiene tu isapre actual.</li>
        <li><strong>Considera mantener tu plan actual</strong> si los beneficios de cambiarte no justifican una posible restricción de cobertura.</li>
        <li><strong>Asesórate con expertos.</strong> Nosotros analizamos tu caso sin costo y solo postulamos donde hay altas probabilidades de éxito.</li>
    </ul>
</section>
<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿Me pueden rechazar por una preexistencia?' => 'Sí, pero la isapre debe justificarlo. Si una te rechaza, podemos postular a otra con políticas más flexibles.',
    '¿Cuánto tiempo puede durar una restricción por preexistencia?' => 'Generalmente 18 a 36 meses, dependiendo de la condición y la isapre.',
    '¿El embarazo se considera preexistencia?' => 'Sí. Si estás embarazada, el cambio de isapre tiene consideraciones especiales. Te recomendamos revisar nuestra guía específica.',
    '¿Puedo cambiarme si tengo una enfermedad crónica?' => 'Sí, pero es posible que la isapre aplique una restricción temporal de cobertura para esa condición específica.',
];
$faq_titulo = 'Preguntas Frecuentes';

ob_start();
?>
<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_individual'); ?>
</div>
<?php
$secciones_html .= ob_get_clean();

include __DIR__ . '/../../layout/seo-page.php';
