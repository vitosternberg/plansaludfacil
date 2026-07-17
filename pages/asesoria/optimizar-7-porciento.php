<?php
/**
 * asesoria/optimizar-7-porciento.php — Optimización del 7% Legal
 */
require_once __DIR__ . '/../../omniflow_config.php';

$page_title       = 'Optimiza tu 7% Legal: Maximiza los Beneficios de tu Cotización | Plan Salud Fácil';
$meta_description = 'Aprende a optimizar tu cotización legal del 7% en Isapre. Con la nueva normativa, te ayudamos a elegir el plan que maximice tus beneficios sin pagar de más.';
$h1               = 'Optimiza tu 7% Legal en Isapre';
$lead             = 'Con los cambios regulatorios recientes (Ley Corta de Isapres), el 7% de tu cotización obligatoria va íntegro a tu plan de salud. Ya no se generan excedentes como antes. Pero eso no significa que no puedas optimizar: te ayudamos a elegir el plan que maximice tus beneficios reales.';
$svc_name         = 'Optimización del 7% Legal';
$svc_description  = 'Guía para optimizar tu cotización del 7% en Isapre post-Ley Corta. Elige el plan que maximice coberturas, beneficios y acceso a prestadores según tu perfil.';
$cta_texto        = 'Optimizar mi plan';
$cta_link         = 'https://wa.me/56952282339';

$breadcrumbs = [
    ['label' => 'Inicio', 'url' => 'BASE_URL/'],
    ['label' => 'Asesoría', 'url' => 'BASE_URL/asesoria/'],
    ['label' => 'Optimizar 7%', 'url' => '#']
];
foreach ($breadcrumbs as &$bc) { $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']); } unset($bc);

$toc_items = [
    ['id' => 'que-cambio', 'label' => '¿Qué cambió?'],
    ['id' => 'como-funciona', 'label' => 'Cómo funciona hoy'],
    ['id' => 'estrategia', 'label' => 'Estrategia de optimización'],
    ['id' => 'ejemplo', 'label' => 'Ejemplo práctico'],
];

ob_start();
?>
<section id="que-cambio" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Qué cambió con la Ley Corta de Isapres?</h2>
    <p class="text-gray-700 leading-relaxed mb-4">Antes, si tu cotización del 7% superaba el precio del plan, la diferencia se acumulaba como <strong>excedentes</strong> que podías usar en farmacias, dental u otras prestaciones. Hoy, con la nueva normativa:</p>
    <ul class="list-disc pl-6 text-gray-700 space-y-2">
        <li>El 7% va <strong>íntegro al plan de salud</strong>.</li>
        <li>No se generan excedentes por diferencia de precio.</li>
        <li>Si el plan cuesta más que tu 7%, debes pagar una <strong>cotización adicional</strong>.</li>
        <li>Si cuesta menos, la diferencia no te la devuelven — se pierde en el sistema.</li>
    </ul>
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mt-4">
        <p class="text-sm text-amber-800"><strong>Importante:</strong> Elegir el plan correcto ahora es más crítico que nunca. Un plan demasiado barato significa que estás regalando plata al sistema. Uno demasiado caro significa que pagas de más de tu bolsillo.</p>
    </div>
</section>

<section id="como-funciona" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">¿Cómo funciona hoy tu 7%?</h2>
    <ol class="list-decimal pl-6 text-gray-700 space-y-3">
        <li>Tu empleador descuenta el <strong>7% de tu renta imponible</strong> y lo envía a la isapre.</li>
        <li>La isapre aplica ese monto al pago de tu plan.</li>
        <li>Si el plan cuesta <strong>exactamente o menos</strong> que tu 7%: todo bien, no pagas nada extra pero tampoco recibes nada de vuelta.</li>
        <li>Si el plan cuesta <strong>más</strong> que tu 7%: la diferencia la pagas tú como cotización adicional.</li>
    </ol>
</section>

<section id="estrategia" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Estrategia para optimizar tu 7%</h2>
    <ul class="list-disc pl-6 text-gray-700 space-y-3">
        <li><strong>Elige un plan cercano a tu 7%:</strong> Busca que el precio del plan esté lo más cerca posible de tu cotización legal, sin pasarse. Así no pagas adicional y aprovechas todo tu 7%.</li>
        <li><strong>Prioriza las coberturas que realmente usas:</strong> Si haces kinesiología todas las semanas, busca un plan con 90%+ de cobertura kinésica, no uno con maternidad que no necesitas.</li>
        <li><strong>Evalúa la red de prestadores en tu comuna:</strong> Un plan más caro con mala red cerca de tu casa es peor que uno más barato con clínicas a 5 minutos.</li>
        <li><strong>Considera tu edad como ventaja:</strong> Si eres joven, puedes optar por planes con menos cobertura hospitalaria y más cobertura ambulatoria/dental/psicológica, maximizando el uso mes a mes.</li>
    </ul>
</section>

<section id="ejemplo" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Ejemplo práctico</h2>
    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
        <p class="text-gray-700 mb-3"><strong>Perfil:</strong> Profesional de 28 años, renta de $1.500.000, sin cargas, vive en Providencia.</p>
        <p class="text-gray-700 mb-3"><strong>7% legal:</strong> $105.000/mes.</p>
        <div class="space-y-2 mb-3">
            <div class="flex justify-between text-sm"><span class="text-red-600">Plan A: $65.000</span><span>❌ $40.000 se pierden en el sistema</span></div>
            <div class="flex justify-between text-sm"><span class="text-green-600">Plan B: $98.000</span><span>✅ Cubierto por tu 7%, aprovechas casi todo</span></div>
            <div class="flex justify-between text-sm"><span class="text-amber-600">Plan C: $120.000</span><span>⚠️ Pagas $15.000 adicional de tu bolsillo</span></div>
        </div>
        <p class="text-sm text-gray-600"><strong>Recomendación:</strong> El Plan B maximiza tu 7% sin costo adicional y con coberturas alineadas a tu perfil (ambulatorio, dental, telemedicina).</p>
    </div>
</section>
<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿Puedo recuperar la diferencia si mi plan cuesta menos que el 7%?' => 'No. Con la nueva normativa, la diferencia no se acumula como excedente ni se devuelve. Por eso es tan importante elegir un plan cercano a tu 7%.',
    '¿Qué pasa si mi plan cuesta más que el 7%?' => 'La diferencia la pagas como cotización adicional. Se descuenta directamente de tu sueldo junto con el 7%.',
    '¿Puedo cambiar de plan para optimizar mi 7%?' => 'Sí. Cada 12 meses puedes cambiar de plan dentro de tu misma isapre, o cambiarte de isapre. Te asesoramos sin costo.',
    '¿Conviene un plan más caro?' => 'Depende. Si las coberturas adicionales justifican el costo extra (ej. necesitas mucha kinesiología o tienes una condición crónica), puede valer la pena. Analizamos tu caso sin compromiso.',
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
