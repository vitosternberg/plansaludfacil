<?php
/**
 * planes/detalle.php — Ficha de Plan Individual
 * Muestra los datos de un plan específico según su código.
 * URL: /planes/detalle/?codigo=PPS23300
 */
require_once __DIR__ . '/../../omniflow_config.php';

$codigo = trim($_GET['codigo'] ?? '');
$plan = null;

if (!empty($codigo)) {
    $csvfile = __DIR__ . '/../../adjuntos/planes_isapre.csv';
    if (file_exists($csvfile)) {
        $handle = fopen($csvfile, 'r');
        $headers = fgetcsv($handle, 0, ',', '"', '');
        while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
            if (count($row) >= 5 && trim($row[1] ?? '') === $codigo) {
                $plan = [
                    'isapre'             => trim($row[0] ?? ''),
                    'codigo'             => trim($row[1] ?? ''),
                    'nombre'             => trim($row[2] ?? ''),
                    'uf'                 => trim($row[3] ?? ''),
                    'tope_anual_uf'      => trim($row[4] ?? ''),
                    'prestadores'        => trim($row[5] ?? ''),
                    'cobertura_hosp_pct' => trim($row[6] ?? ''),
                    'cobertura_amb_pct'  => trim($row[7] ?? ''),
                    'url'                => trim($row[8] ?? ''),
                    'region'             => trim($row[9] ?? 'todas'),
                ];
                break;
            }
        }
        fclose($handle);
    }
}

// Variables para template seo-page
$page_title       = $plan ? "{$plan['nombre']} — {$plan['isapre']} | Plan Salud Fácil" : 'Plan no encontrado | Plan Salud Fácil';
$meta_description = $plan ? "Plan {$plan['nombre']} de {$plan['isapre']}. {$plan['uf']} UF/mes. Cobertura hospitalaria {$plan['cobertura_hosp_pct']}%, ambulatoria {$plan['cobertura_amb_pct']}%. {$plan['prestadores']} prestadores." : 'Plan no encontrado.';
$h1               = $plan ? $plan['nombre'] : 'Plan no encontrado';
$lead             = $plan ? "Plan de {$plan['isapre']} · Código {$plan['codigo']}" : '';
$svc_name         = $plan ? $plan['nombre'] : 'Plan';
$svc_description  = $meta_description;
$cta_texto        = 'Cotizar este plan';
$cta_link         = '#formulario';

$breadcrumbs = [
    ['label' => 'Inicio', 'url' => 'BASE_URL/'],
    ['label' => 'Planes', 'url' => 'BASE_URL/planes/'],
    ['label' => $plan ? $plan['nombre'] : 'Plan', 'url' => '#'],
];
foreach ($breadcrumbs as &$bc) {
    $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
}
unset($bc);

$toc_items = [];

ob_start();
if (!$plan): ?>
    <div class="max-w-4xl mx-auto px-4 py-10 text-center">
        <h2 class="text-xl text-gray-600 mb-4">Plan "<?= htmlspecialchars($codigo) ?>" no encontrado</h2>
        <p class="text-gray-500 mb-6">El código ingresado no coincide con ningún plan en nuestra base de datos.</p>
        <a href="<?= BASE_URL ?>/planes/comparador/" class="inline-flex items-center px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition">
            Ir al comparador
        </a>
    </div>
<?php else:
    $hosp = (int)$plan['cobertura_hosp_pct'];
    $amb = (int)$plan['cobertura_amb_pct'];
    $uf = (float)str_replace(',', '.', $plan['uf']);
    $precio = round($uf * 38500);
    $pct7 = round($precio / 196000 * 100);
?>
    <!-- Hero con datos clave del plan -->
    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <div class="text-3xl font-extrabold text-blue-700"><?= number_format($uf, 2, ',', '.') ?> UF</div>
                    <div class="text-sm text-gray-500">/mes · precio base</div>
                    <div class="text-xs text-gray-400 mt-1">~$<?= number_format($precio, 0, ',', '.') ?></div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="text-3xl font-extrabold text-green-700"><?= $hosp ?>%</div>
                    <div class="text-sm text-gray-500">Cobertura Hospitalaria</div>
                    <div class="text-xs text-gray-400 mt-1"><?= $amb ?>% Ambulatoria</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-xl">
                    <div class="text-3xl font-extrabold text-purple-700"><?= $plan['prestadores'] ?></div>
                    <div class="text-sm text-gray-500">Prestadores en convenio</div>
                    <div class="text-xs text-gray-400 mt-1">Tope: <?= $plan['tope_anual_uf'] ?> UF</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Datos completos del plan -->
    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Ficha completa del plan</h2>
            <dl class="grid md:grid-cols-2 gap-4">
                <div><dt class="text-sm text-gray-500">Isapre</dt><dd class="font-semibold text-gray-900"><?= htmlspecialchars($plan['isapre']) ?></dd></div>
                <div><dt class="text-sm text-gray-500">Código</dt><dd class="font-semibold text-gray-900"><?= htmlspecialchars($plan['codigo']) ?></dd></div>
                <div><dt class="text-sm text-gray-500">Nombre del plan</dt><dd class="font-semibold text-gray-900"><?= htmlspecialchars($plan['nombre']) ?></dd></div>
                <div><dt class="text-sm text-gray-500">Precio base mensual</dt><dd class="font-semibold text-gray-900"><?= number_format($uf, 2, ',', '.') ?> UF (~$<?= number_format($precio, 0, ',', '.') ?>)</dd></div>
                <div><dt class="text-sm text-gray-500">Cobertura hospitalaria</dt><dd class="font-semibold text-green-700"><?= $hosp ?>%</dd></div>
                <div><dt class="text-sm text-gray-500">Cobertura ambulatoria</dt><dd class="font-semibold text-green-700"><?= $amb ?>%</dd></div>
                <div><dt class="text-sm text-gray-500">Tope anual</dt><dd class="font-semibold text-gray-900"><?= htmlspecialchars($plan['tope_anual_uf']) ?> UF</dd></div>
                <div><dt class="text-sm text-gray-500">Prestadores en convenio</dt><dd class="font-semibold text-gray-900"><?= htmlspecialchars($plan['prestadores']) ?></dd></div>
                <div><dt class="text-sm text-gray-500">Región</dt><dd class="font-semibold text-gray-900"><?= htmlspecialchars($plan['region']) ?></dd></div>
                <div><dt class="text-sm text-gray-500">Respecto al 7%</dt><dd class="font-semibold <?= $pct7 <= 100 ? 'text-green-700' : 'text-red-600' ?>">~<?= $pct7 ?>% del 7% legal ($196.000 ref.)</dd></div>
            </dl>
        </div>
    </div>

    <!-- CTA -->
    <div class="max-w-4xl mx-auto px-4 py-6 text-center">
        <a href="<?= BASE_URL ?>/planes/comparador/?codigo=<?= urlencode($codigo) ?>#comparador" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-md">
            <iconify-icon icon="mdi:compare" class="mr-2" width="20"></iconify-icon>
            Comparar este plan
        </a>
        <a href="#formulario" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition shadow-md ml-3">
            <iconify-icon icon="mdi:clipboard-text-outline" class="mr-2" width="20"></iconify-icon>
            Solicitar asesoría
        </a>
    </div>

    <!-- Formulario -->
    <div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
        <?php render_component('formulario_individual'); ?>
    </div>
<?php endif;

$secciones_html = ob_get_clean();
$faq_preguntas = [];
$faq_titulo = '';

include __DIR__ . '/../../layout/seo-page.php';
