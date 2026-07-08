<?php
require_once __DIR__ . '/../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $visited_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $stmt = $db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)");
        if ($stmt) { $stmt->bind_param("sss", $ip_address, $user_agent, $visited_url); $stmt->execute(); $stmt->close(); }
        $lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
        if ($lead_id) {
            $stmt2 = $db->prepare("INSERT INTO lead_visits (lead_id, url_visitada) VALUES (?, ?)");
            if ($stmt2) { $stmt2->bind_param("is", $lead_id, $visited_url); $stmt2->execute(); $stmt2->close(); }
        }
        $db->close();
    }
} catch (Exception $e) { error_log("Omniflow Tracking Error: " . $e->getMessage()); }

$page_title = "Preguntas Frecuentes sobre ISAPRE | Plan Salud Facil";
$meta_description = "Resuelve todas tus dudas sobre ISAPRE. Costos, coberturas, cambios, cargas, preexistencias y mas.";
include './layout/plantilla.php';
include './layout/header.php';

$categorias = [
    ['titulo' => 'Conceptos basicos', 'icono' => '📘', 'preguntas' => [
        '¿Que es una ISAPRE?' => 'Una ISAPRE es una institucion privada que administra tu cotizacion de salud y ofrece planes con distintas coberturas y beneficios.',
        '¿Como funciona una ISAPRE?' => 'Recibe tu cotizacion legal de salud y la aplica al plan que contrates. Si el plan cuesta mas que tu cotizacion, pagas la diferencia.',
        '¿Que diferencia hay entre ISAPRE y FONASA?' => 'FONASA es el sistema publico de salud e ISAPRE corresponde al sistema privado.',
        '¿Puedo volver a FONASA despues de estar en una ISAPRE?' => 'Si, puedes cambiarte a FONASA siguiendo el procedimiento correspondiente.',
    ]],
    ['titulo' => 'Eleccion y comparacion', 'icono' => '🔍', 'preguntas' => [
        '¿Cual es la mejor ISAPRE?' => 'Depende de tu edad, renta, estado de salud, cargas familiares y las clinicas donde deseas atenderte.',
        '¿Que ISAPRE me conviene?' => 'La mejor opcion depende de tus necesidades medicas, presupuesto y preferencias de atencion.',
        '¿Que ISAPRE tiene mejor cobertura?' => 'La mejor cobertura depende del plan y de las prestaciones que necesites.',
        '¿Que ISAPRE tiene las mejores clinicas?' => 'Cada ISAPRE tiene convenios diferentes con clinicas y centros medicos.',
        '¿Que ISAPRE conviene para una familia?' => 'Conviene un plan con buena cobertura para cargas familiares y hospitalizaciones.',
        '¿Que ISAPRE conviene para trabajadores independientes?' => 'Existen planes especialmente adaptados para trabajadores independientes.',
        '¿Que ISAPRE conviene para adultos mayores?' => 'Es recomendable comparar coberturas, costos y beneficios especificos.',
        '¿Que ISAPRE conviene para jovenes?' => 'Muchos jovenes optan por planes con menor costo y buena cobertura ambulatoria.',
        '¿Como comparar planes de ISAPRE?' => 'Compara precio, cobertura, red de clinicas, topes y beneficios adicionales.',
        '¿Como saber si un plan es conveniente para mi?' => 'Analiza el costo, la cobertura y las clinicas donde deseas atenderte.',
        '¿Que debo considerar antes de contratar una ISAPRE?' => 'Evalua el precio, la cobertura, la red medica, los topes y los beneficios adicionales.',
        '¿Me pueden ayudar a elegir el mejor plan?' => 'Si, un asesor puede comparar distintas alternativas segun tus necesidades.',
    ]],
    ['titulo' => 'Cambio y contratacion', 'icono' => '📝', 'preguntas' => [
        '¿Como puedo cambiarme de ISAPRE?' => 'Solo debes contratar un nuevo plan y la nueva ISAPRE generalmente gestiona el cambio por ti.',
        '¿Cuanto demora cambiarse de ISAPRE?' => 'El cambio normalmente se hace efectivo el primer dia del mes siguiente o segun la fecha indicada en el contrato.',
        '¿Puedo cambiarme de ISAPRE en cualquier momento?' => 'Si, siempre que cumplas con las condiciones legales y contractuales vigentes.',
        '¿Que documentos necesito para contratar una ISAPRE?' => 'Generalmente cedula de identidad, antecedentes laborales y, en algunos casos, declaracion de salud.',
        '¿Puedo contratar una ISAPRE de forma online?' => 'Si, muchas ISAPRE permiten realizar todo el proceso de contratacion en linea.',
        '¿Como puedo cotizar un plan de ISAPRE?' => 'Puedes solicitar una cotizacion entregando datos como edad, renta y grupo familiar.',
    ]],
    ['titulo' => 'Costos y cotizacion', 'icono' => '💰', 'preguntas' => [
        '¿Cuanto cuesta un plan de ISAPRE?' => 'Depende del plan, tu cotizacion, edad, cargas familiares y cobertura elegida.',
        '¿Que pasa con mi 7% de cotizacion?' => 'Tu 7% obligatorio se destina al pago del plan contratado.',
        '¿Debo pagar un adicional al 7%?' => 'Solo si el valor del plan supera el monto de tu cotizacion obligatoria.',
        '¿Como se calcula el valor de un plan?' => 'Se considera tu cotizacion, el plan elegido y las personas que seran beneficiarias.',
    ]],
    ['titulo' => 'Coberturas medicas', 'icono' => '🏥', 'preguntas' => [
        '¿Que cubre un plan de ISAPRE?' => 'Dependiendo del plan, cubre consultas, examenes, hospitalizaciones, cirugias y otros beneficios.',
        '¿Que clinicas puedo usar?' => 'Depende de la red de prestadores y convenios incluidos en tu plan.',
        '¿Puedo atenderme con cualquier medico?' => 'Si, aunque la cobertura puede variar segun si el profesional tiene convenio.',
        '¿Que cobertura tienen las consultas medicas?' => 'Varia segun el plan y el prestador donde te atiendas.',
        '¿Que cobertura tienen las hospitalizaciones?' => 'Cada plan establece porcentajes de cobertura y topes distintos.',
        '¿La ISAPRE cubre cirugias?' => 'Si, siempre que esten contempladas en las coberturas del plan.',
        '¿La ISAPRE cubre examenes medicos?' => 'Si, segun las condiciones y porcentajes definidos en el plan.',
        '¿La ISAPRE cubre medicamentos?' => 'Algunos planes incluyen beneficios o convenios para medicamentos.',
        '¿La ISAPRE cubre urgencias?' => 'Si, la cobertura depende del plan y del centro asistencial.',
        '¿La ISAPRE cubre salud mental?' => 'Muchos planes consideran cobertura para consultas y tratamientos de salud mental.',
        '¿La ISAPRE cubre maternidad?' => 'Si, aunque las coberturas y beneficios dependen del plan contratado.',
        '¿La ISAPRE cubre odontologia?' => 'Algunos planes incluyen beneficios dentales o convenios especiales.',
    ]],
    ['titulo' => 'Cargas familiares', 'icono' => '👨‍👩‍👧‍👦', 'preguntas' => [
        '¿Puedo agregar a mi pareja como carga?' => 'Si, si cumple con los requisitos establecidos por la normativa vigente.',
        '¿Puedo incorporar a mis hijos?' => 'Si, puedes agregarlos como beneficiarios del plan.',
        '¿Cuanto cuesta agregar cargas familiares?' => 'Depende del plan y de las caracteristicas de cada beneficiario.',
    ]],
    ['titulo' => 'Preexistencias y casos especiales', 'icono' => '⚠️', 'preguntas' => [
        '¿Que son las preexistencias?' => 'Son enfermedades o condiciones de salud que existian antes de contratar el plan.',
        '¿Debo declarar enfermedades preexistentes?' => 'Si, cuando la normativa y el proceso de afiliacion lo requieran.',
        '¿Puedo contratar una ISAPRE si tengo una enfermedad?' => 'Dependera de la normativa vigente y de las condiciones aplicables al momento de contratar.',
        '¿Puedo cambiarme de ISAPRE si estoy embarazada?' => 'Si, aunque es importante revisar las condiciones y coberturas antes del cambio.',
        '¿Puedo cambiarme de ISAPRE si estoy con licencia medica?' => 'Depende de la situacion particular y de la normativa vigente.',
    ]],
    ['titulo' => 'Beneficios adicionales', 'icono' => '🎁', 'preguntas' => [
        '¿Que beneficios adicionales ofrecen las ISAPRE?' => 'Algunas incluyen telemedicina, descuentos en farmacias, seguros y programas preventivos.',
        '¿Las ISAPRE tienen telemedicina?' => 'Si, muchas ofrecen consultas medicas virtuales como beneficio.',
        '¿Las ISAPRE tienen descuentos en farmacias?' => 'Varias cuentan con convenios que entregan descuentos en medicamentos.',
    ]],
];

$schemaEntities = [];
foreach ($categorias as $cat) {
    foreach ($cat['preguntas'] as $q => $a) {
        $schemaEntities[] = ['@type' => 'Question', 'name' => $q, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a]];
    }
}
?>

<script type="application/ld+json">
<?= json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $schemaEntities], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<main class="bg-gray-50 font-sans">
    <section class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-16 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">Preguntas Frecuentes sobre ISAPRE</h1>
            <p class="text-blue-100 text-lg md:text-xl max-w-2xl mx-auto">Todas tus dudas resueltas en un solo lugar. Explora por categoria o busca la respuesta que necesitas.</p>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 py-10">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php foreach ($categorias as $idx => $cat): ?>
            <a href="#cat-<?= $idx ?>" class="flex items-center gap-2 bg-white rounded-xl p-3 shadow-sm hover:shadow-md transition border border-gray-100 text-sm font-medium text-gray-700 hover:text-blue-700">
                <span class="text-lg"><?= $cat['icono'] ?></span>
                <span><?= htmlspecialchars($cat['titulo']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="max-w-4xl mx-auto px-4 pb-16">
        <?php foreach ($categorias as $idx => $cat): ?>
        <div id="cat-<?= $idx ?>" class="mb-10 scroll-mt-28">
            <h2 class="text-2xl font-bold text-gray-900 mb-1 flex items-center gap-2">
                <span><?= $cat['icono'] ?></span>
                <span><?= htmlspecialchars($cat['titulo']) ?></span>
            </h2>
            <p class="text-gray-500 text-sm mb-4"><?= count($cat['preguntas']) ?> preguntas en esta categoria</p>
            <div class="space-y-2">
                <?php foreach ($cat['preguntas'] as $pregunta => $respuesta): ?>
                <details class="group bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <summary class="flex justify-between items-center cursor-pointer p-4 md:p-5 font-medium text-gray-800 list-none [&::-webkit-details-marker]:hidden">
                        <span class="pr-4 text-sm md:text-base"><?= htmlspecialchars($pregunta) ?></span>
                        <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 text-lg group-open:bg-red-50 group-open:text-red-500 transition-colors">
                            <span class="group-open:hidden">+</span>
                            <span class="hidden group-open:inline">−</span>
                        </span>
                    </summary>
                    <div class="px-4 md:px-5 pb-4 md:pb-5 pt-0 text-gray-600 leading-relaxed border-t border-gray-100 mx-4">
                        <p class="text-sm md:text-base"><?= htmlspecialchars($respuesta) ?></p>
                    </div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    <section class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-12 px-4">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl md:text-3xl font-bold mb-4">¿No encontraste lo que buscabas?</h2>
            <p class="text-blue-100 mb-6">Habla directamente con uno de nuestros asesores. Es rapido, gratuito y sin compromiso.</p>
            <a href="https://wa.me/56952282339" target="_blank" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform hover:scale-105">
                <iconify-icon icon="mdi:whatsapp" width="24" class="mr-2"></iconify-icon>
                Hablar por WhatsApp
            </a>
        </div>
    </section>
</main>

<?php include './layout/footer.php'; ?>
