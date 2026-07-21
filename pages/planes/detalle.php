<?php
/**
 * pages/planes/detalle.php — Ficha individual de plan
 * Lee datos desde planes_isapre.csv
 * URL: /planes/detalle/?codigo=PM260334
 */

$codigo = trim($_GET['codigo'] ?? '');
if (empty($codigo)) {
    header('Location: ' . BASE_URL . '/planes/comparador/');
    exit;
}

// Buscar el plan en el CSV
$plan = null;
$path = __DIR__ . '/../../adjuntos/planes_isapre.csv';
if (($h = fopen($path, 'r')) !== false) {
    fgetcsv($h, 0, ',', '"', '');
    while (($r = fgetcsv($h, 0, ',', '"', '')) !== false) {
        if (count($r) < 10) continue;
        if (trim($r[1] ?? '') === $codigo) {
            $plan = [
                'isapre'             => trim($r[0] ?? ''),
                'codigo'             => $codigo,
                'nombre'             => trim($r[2] ?? ''),
                'uf'                 => (float) str_replace(',', '.', $r[3] ?? '0'),
                'tope_anual_uf'      => (float) str_replace(',', '.', $r[4] ?? '0'),
                'prestadores'        => (int)($r[5] ?? 0),
                'cobertura_hosp_pct' => (int)($r[6] ?? 0),
                'cobertura_amb_pct'  => (int)($r[7] ?? 0),
                'region'             => trim($r[9] ?? 'todas'),
            ];
            break;
        }
    }
    fclose($h);
}

if (!$plan) {
    header('Location: ' . BASE_URL . '/planes/comparador/');
    exit;
}

$page_title       = $plan['nombre'] . ' — ' . $plan['isapre'] . ' | Plan Salud Fácil';
$meta_description = 'Ficha del plan ' . $plan['nombre'] . ' de ' . $plan['isapre'] . '. $' . number_format($plan['uf'], 2, ',', '.') . ' UF/mes, ' . $plan['prestadores'] . ' prestadores.';
$h1               = $plan['nombre'];
$lead             = 'Plan de ' . $plan['isapre'] . ' · ' . $plan['prestadores'] . ' prestadores en convenio';
$svc_name         = $plan['nombre'];
$svc_description  = 'Plan de Isapre ' . $plan['isapre'];
$cta_texto        = 'Cotizar este plan';
$cta_link         = '#formulario';
$breadcrumbs      = [
    ['label' => 'Inicio', 'url' => 'BASE_URL/'],
    ['label' => 'Planes', 'url' => 'BASE_URL/planes/'],
    ['label' => 'Detalle', 'url' => '#']
];
foreach ($breadcrumbs as &$bc) $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']);
unset($bc);
$toc_items = [];

$uf_value = 38500;
$precio_clp = round($plan['uf'] * $uf_value);
$pct7 = (string)($plan['cobertura_hosp_pct']);
$pcta = (string)($plan['cobertura_amb_pct']);

ob_start(); ?>
<style>
.answer-direct{background:linear-gradient(135deg,#eff6ff,#f0fdf4);border-left:4px solid #2563eb;padding:16px 20px;border-radius:0 12px 12px 0;margin-bottom:16px;font-size:15px;color:#374151;line-height:1.7}
</style>

<section class="max-w-4xl mx-auto px-4 py-6">
    <div class="bg-white rounded-2xl shadow-sm border p-6 md:p-10 mb-8">
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Columna izquierda: datos del plan -->
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center text-lg font-bold"><?= substr($plan['isapre'], 0, 1) ?></div>
                    <div>
                        <div class="text-sm font-bold text-gray-800"><?= htmlspecialchars($plan['isapre']) ?></div>
                        <div class="text-xs text-gray-400">Código: <?= htmlspecialchars($plan['codigo']) ?></div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Cobertura Hospitalaria</span>
                        <span class="font-bold text-blue-700"><?= $pct7 ?>%</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Cobertura Ambulatoria</span>
                        <span class="font-bold text-blue-700"><?= $pcta ?>%</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Tope Anual</span>
                        <span class="font-bold text-gray-800"><?= number_format($plan['tope_anual_uf'], 0, ',', '.') ?> UF</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Prestadores en convenio</span>
                        <span class="font-bold text-gray-800"><?= $plan['prestadores'] ?></span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Presencia geográfica</span>
                        <span class="font-bold text-gray-800"><?= $plan['region'] === 'todas' ? 'Nacional' : ucfirst($plan['region']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: precio y CTA -->
            <div class="text-center bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100">
                <div class="text-sm text-gray-500 mb-1">Precio base mensual</div>
                <div class="text-4xl font-extrabold text-blue-700 mb-1"><?= number_format($plan['uf'], 2, ',', '.') ?> UF</div>
                <div class="text-lg text-gray-500 mb-4">≈ $<?= number_format($precio_clp, 0, ',', '.') ?></div>
                <div class="text-xs text-gray-400 mb-6">UF referencial: $<?= number_format($uf_value, 0, ',', '.') ?></div>
                <a href="<?= BASE_URL ?>/planes/comparador/?edad=30&renta=1500000&cargas=0#comparador" class="inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition text-lg">
                    Cotizar este plan →
                </a>
                <p class="text-xs text-gray-400 mt-3">Calcula tu precio real según edad y cargas</p>
            </div>
        </div>
    </div>

    <div class="answer-direct mb-8">
        <strong>💡 ¿Sabías?</strong> El precio final de este plan depende de tu edad, número de cargas y valor UF del día. Usa nuestro comparador gratuito para obtener tu precio exacto en segundos.
    </div>
</section>

<div id="formulario" class="max-w-4xl mx-auto px-4 py-10">
    <?php render_component('formulario_individual'); ?>
</div>

<?php
$secciones_html = ob_get_clean();
$faq_preguntas = [
    '¿Este precio es el que voy a pagar?' => 'No. El precio base en UF es referencial. El valor final depende de tu edad exacta, cargas y el valor UF del día de facturación.',
    '¿Puedo contratar este plan directamente?' => 'Sí. Un asesor de Plan Salud Fácil te guía en todo el proceso de contratación sin costo adicional.',
    '¿Qué significa "cobertura hospitalaria ' . $pct7 . '%"?' => 'Significa que la isapre cubre entre el ' . $pct7 . '% del costo de hospitalizaciones en prestadores preferentes. El resto lo pagas como copago.',
];
$faq_titulo = 'Preguntas Frecuentes';

include __DIR__ . '/../../layout/seo-page.php';
