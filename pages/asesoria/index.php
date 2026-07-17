<?php
/**
 * asesoria/index.php — Hub de Servicios y Trámites
 */
require_once __DIR__ . '/../../omniflow_config.php';

$page_title       = 'Asesoría de Isapre Personalizada: Cambio, Preexistencias y Optimización | Plan Salud Fácil';
$meta_description = 'Servicios de asesoría de isapre 100% gratuitos: cambio de isapre, evaluación de preexistencias y optimización de tu 7% legal. Sin costo.';
$h1               = 'Asesoría de Isapre Personalizada';
$lead             = 'Nuestro equipo de expertos te acompaña en cada trámite. Desde cambiarte de isapre hasta optimizar tu cotización legal, todo sin costo para ti.';
$svc_name         = 'Asesoría de Isapre';
$svc_description  = 'Servicios gratuitos de asesoría: cambio de isapre, evaluación de preexistencias y optimización del 7% legal obligatorio.';
$cta_texto        = 'Hablar con un asesor';
$cta_link         = 'https://wa.me/56952282339';

$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Asesoría', 'url' => '#']];
foreach ($breadcrumbs as &$bc) { $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']); } unset($bc);

$toc_items = [
    ['id' => 'cambio', 'label' => 'Cambio de Isapre'],
    ['id' => 'preexistencias', 'label' => 'Evaluación de Preexistencias'],
    ['id' => 'optimizar', 'label' => 'Optimizar tu 7%'],
];

$servicios = [
    ['slug' => 'cambio-de-isapre', 'icono' => 'mdi:swap-horizontal', 'titulo' => 'Cambio de Isapre', 'desc' => 'Te guiamos paso a paso en el cambio de isapre. Comparamos todas las opciones, gestionamos tu Declaración de Salud y firmas 100% online. Sin costo.'],
    ['slug' => 'evaluacion-preexistencias', 'icono' => 'mdi:clipboard-pulse', 'titulo' => 'Evaluación de Preexistencias', 'desc' => '¿Tienes una condición preexistente? Analizamos tu caso de forma confidencial y solo postulamos donde hay altas probabilidades de aceptación.'],
    ['slug' => 'optimizar-7-porciento', 'icono' => 'mdi:chart-line', 'titulo' => 'Optimizar tu 7% Legal', 'desc' => 'Con la nueva normativa, el 7% va íntegro a la isapre. Te ayudamos a elegir el plan que maximice tus beneficios sin pagar de más.'],
];

ob_start();
foreach ($servicios as $s):
?>
<section id="<?= $s['slug'] ?>" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 flex items-center gap-2">
        <iconify-icon icon="<?= $s['icono'] ?>" class="text-blue-600" width="28"></iconify-icon>
        <?= $s['titulo'] ?>
    </h2>
    <p class="text-gray-700 leading-relaxed mb-4"><?= $s['desc'] ?></p>
    <a href="<?= BASE_URL ?>/asesoria/<?= $s['slug'] ?>/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver más
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>
<?php endforeach;
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿La asesoría tiene algún costo?' => 'No. Todos nuestros servicios de asesoría son 100% gratuitos. Nos pagan las isapres, no tú.',
    '¿Cuánto demora el proceso?' => 'Depende del servicio. Un cambio de isapre toma entre 48 horas y 7 días hábiles. La evaluación de preexistencias la tienes en 24 horas.',
    '¿Puedo hacer varios trámites a la vez?' => 'Sí. Por ejemplo, puedes evaluar un cambio de isapre mientras optimizamos tu 7% actual.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Asesoría';

include __DIR__ . '/../../layout/seo-page.php';
