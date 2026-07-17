<?php
/**
 * companias/index.php — Hub de Marcas
 * Comparativa de Isapres con intención directa de búsqueda
 */
require_once __DIR__ . '/../../omniflow_config.php';

$page_title       = 'Comparativa de Isapres en Chile: Planes, Precios y Coberturas | Plan Salud Fácil';
$meta_description = 'Compara todas las Isapres de Chile: Banmédica, Colmena, Cruz Blanca, Consalud, Nueva MasVida y Vida Tres. Encuentra el mejor plan según tu perfil. Asesoría gratuita.';
$h1               = 'Comparativa de Isapres en Chile';
$lead             = 'Cada isapre tiene fortalezas distintas. Algunas destacan en precio, otras en cobertura o red de prestadores. Te ayudamos a encontrar la que mejor se adapte a ti.';
$svc_name         = 'Comparativa de Isapres';
$svc_description  = 'Compara planes y precios de todas las Isapres de Chile. Encuentra la mejor opción según tu edad, renta, cargas e intereses de cobertura.';

$breadcrumbs = [['label' => 'Inicio', 'url' => 'BASE_URL/'], ['label' => 'Compañías', 'url' => '#']];
foreach ($breadcrumbs as &$bc) { $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']); } unset($bc);

$toc_items = [
    ['id' => 'banmedica', 'label' => 'Banmédica'],
    ['id' => 'colmena', 'label' => 'Colmena'],
    ['id' => 'cruz-blanca', 'label' => 'Cruz Blanca'],
    ['id' => 'consalud', 'label' => 'Consalud'],
    ['id' => 'nueva-masvida', 'label' => 'Nueva MasVida'],
    ['id' => 'vida-tres', 'label' => 'Vida Tres'],
];

ob_start();
$companias = [
    ['slug' => 'banmedica', 'nombre' => 'Banmédica', 'desc' => 'La isapre más grande de Chile. Destaca por su amplia red de prestadores y clínicas propias. Ideal si buscas libertad de elección y acceso a los mejores centros médicos del país.', 'ideal' => 'Profesionales que valoran la red de clínicas y la rapidez de atención.'],
    ['slug' => 'colmena', 'nombre' => 'Colmena', 'desc' => 'Tradicionalmente fuerte en el segmento familiar. Ofrece buenos planes para grupos familiares con cobertura equilibrada y precios competitivos en el segmento medio.', 'ideal' => 'Familias que buscan balance entre precio y cobertura.'],
    ['slug' => 'cruz-blanca', 'nombre' => 'Cruz Blanca', 'desc' => 'Destaca por sus planes con cobertura de kinesiología, deporte y salud mental. Buena opción para personas activas que valoran la medicina preventiva.', 'ideal' => 'Deportistas y personas interesadas en salud mental y telemedicina.'],
    ['slug' => 'consalud', 'nombre' => 'Consalud', 'desc' => 'Fuerte presencia en regiones y buena cobertura ambulatoria. Precios competitivos en planes individuales, especialmente para jóvenes profesionales.', 'ideal' => 'Jóvenes profesionales en regiones que buscan precio competitivo.'],
    ['slug' => 'nueva-masvida', 'nombre' => 'Nueva MasVida', 'desc' => 'Enfoque en planes accesibles con buena cobertura base. Destaca en el segmento de adultos mayores por sus políticas de aceptación.', 'ideal' => 'Adultos mayores y personas que buscan un plan sin complicaciones.'],
    ['slug' => 'vida-tres', 'nombre' => 'Vida Tres', 'desc' => 'Isapre de nicho con foco en atención personalizada. Ofrece planes premium con copagos bajos y acceso a clínicas de alta gama.', 'ideal' => 'Profesionales de renta alta que buscan la mejor experiencia de atención.'],
];

foreach ($companias as $c):
?>
<section id="<?= $c['slug'] ?>" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2"><?= $c['nombre'] ?></h2>
    <p class="text-gray-700 leading-relaxed mb-3"><?= $c['desc'] ?></p>
    <p class="text-sm text-gray-500 mb-4"><strong>Ideal para:</strong> <?= $c['ideal'] ?></p>
    <a href="<?= BASE_URL ?>/companias/<?= $c['slug'] ?>/" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition">
        Ver planes de <?= $c['nombre'] ?>
        <iconify-icon icon="mdi:arrow-right" width="20" class="ml-1"></iconify-icon>
    </a>
</section>
<?php endforeach;

$secciones_html = ob_get_clean();
$faq_preguntas = [
    '¿Cuál es la mejor isapre de Chile?' => 'Depende de tu perfil. No existe una isapre universalmente mejor. La ideal para ti depende de tu edad, renta, cargas, comuna e intereses de cobertura.',
    '¿Puedo cambiarme de isapre si no me gusta?' => 'Sí, cada 12 meses puedes cambiarte. Nuestro equipo te ayuda sin costo.',
    '¿Todas las isapres cuestan lo mismo?' => 'No. Cada isapre tiene distintos planes con precios que varían según la cobertura. El precio se descuenta de tu 7% legal.',
];
$faq_titulo = 'Preguntas Frecuentes sobre Isapres';

include __DIR__ . '/../../layout/seo-page.php';
