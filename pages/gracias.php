<?php
/**
 * gracias.php — Página de Agradecimiento Post-Formulario (v2026-07-17)
 * Plan Salud Fácil
 * 
 * Recibe ?id=XXX, consulta la BD y muestra los datos del registro recién creado.
 * Todos los formularios (individual, familiar, monoparental, cambio isapre)
 * redirigen aquí después de un envío exitoso.
 */

require_once __DIR__ . '/../config.php';

$record = null;
$record_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($record_id > 0) {
    try {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$db->connect_error) {
            $db->set_charset("utf8mb4");
            $stmt = $db->prepare("SELECT id, nombre, correo, celular, datos_adicionales, fecha_creacion FROM procesar_formularios WHERE id = ?");
            $stmt->bind_param("i", $record_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $record = $row;
                $record['datos_adicionales'] = json_decode($row['datos_adicionales'] ?? '{}', true) ?: [];
            }
            $stmt->close();
            $db->close();
        }
    } catch (Exception $e) {
        error_log("gracias.php DB error: " . $e->getMessage());
    }
}

// Determine el tipo de plan desde datos_adicionales
$tipo_plan = 'Cotización';
$tipo_plan_emoji = '📋';
$ad = []; // Siempre definido para evitar errores en templates
if ($record && !empty($record['datos_adicionales'])) {
    $ad = $record['datos_adicionales'];
    if (!empty($ad['query_type'])) {
        switch ($ad['query_type']) {
            case 'cotizacion_individual': $tipo_plan = 'Plan Individual'; $tipo_plan_emoji = '🧑'; break;
            case 'cotizacion_familiar': $tipo_plan = 'Plan Familiar'; $tipo_plan_emoji = '👨‍👩‍👧‍👦'; break;
            case 'cotizacion_monoparental': $tipo_plan = 'Plan Monoparental'; $tipo_plan_emoji = '👩‍👧'; break;
            default: $tipo_plan = $ad['query_type']; break;
        }
    }
    if (!empty($ad['origen_lead'])) {
        switch ($ad['origen_lead']) {
            case 'monoparental': $tipo_plan = 'Plan Monoparental'; $tipo_plan_emoji = '👩‍👧'; break;
            case 'familiar_biparental': $tipo_plan = 'Plan Familiar'; $tipo_plan_emoji = '👨‍👩‍👧‍👦'; break;
        }
    }
}

// ─── Motor de Cotización Real ─────────────────────────────
function parse_lead_from_message($msg) {
    $lead = [
        'edad' => 30,
        'renta' => 500000,
        'cargas' => 0,
        'comuna' => '',
        'intereses' => ['Hospitalización', 'Atención Ambulatoria'],
    ];
    if (preg_match('/Edad:\s*(\d+)/', $msg, $m))   $lead['edad'] = intval($m[1]);
    if (preg_match('/Renta:\s*([\d.]+)/', $msg, $m)) $lead['renta'] = intval(str_replace('.', '', $m[1]));
    if (preg_match('/Cargas:\s*(\d+)/', $msg, $m))  $lead['cargas'] = intval($m[1]);
    if (preg_match('/Comuna:\s*(.+)/', $msg, $m))    $lead['comuna'] = trim($m[1]);
    if (preg_match('/Intereses:\s*(.+)/', $msg, $m)) {
        $lead['intereses'] = array_map('trim', explode(',', $m[1]));
        $lead['intereses'] = array_filter($lead['intereses'], function($i) { return !empty($i) && $i !== 'Ninguno específico'; });
    }
    if (empty($lead['intereses'])) $lead['intereses'] = ['Hospitalización', 'Atención Ambulatoria'];
    return $lead;
}

function motor_cotizacion_real($record) {
    $ad = $record['datos_adicionales'] ?? [];
    $msg = $ad['message'] ?? '';
    
    // Also check structured fields from datos_adicionales (newer format)
    $lead = [
        'edad'    => intval($ad['age'] ?? 0) ?: 30,
        'renta'   => intval($ad['income'] ?? 0) ?: 500000,
        'cargas'  => intval($ad['cargas'] ?? 0),
        'intereses' => $ad['interests'] ?? [],
    ];
    
    // Parse message text for richer data
    if (!empty($msg)) {
        $parsed = parse_lead_from_message($msg);
        if ($lead['edad'] <= 0) $lead['edad'] = $parsed['edad'];
        if ($lead['renta'] <= 0) $lead['renta'] = $parsed['renta'];
        $lead['cargas'] = $parsed['cargas'];
        if (empty($lead['intereses'])) $lead['intereses'] = $parsed['intereses'];
    }
    
    $lead['uf_value'] = 38500;
    
    // Call motor de cotización vía capa de datos (local o API remota)
    require_once __DIR__ . '/../core/planes_data_provider.php';
    
    $result = pd_cotizar($lead);
    if (!$result || isset($result['error']) || empty($result['recomendaciones'])) return [];
    
    // Convert to formato compatible con la vista
    $resultados = [];
    foreach ($result['recomendaciones'] as $rec) {
        $resultados[] = [
            'plan' => [
                'nombre' => $rec['nombre'],
                'isapre' => $rec['isapre'],
                'precio' => $rec['precio_clp'],
                'uf' => $rec['uf'],
                'codigo' => $rec['codigo'],
                'prestadores' => $rec['prestadores'],
                'tope_anual_uf' => $rec['tope_anual_uf'],
                'url' => $rec['url'],
            ],
            'score' => $rec['score'],
            'razones' => $rec['razones'],
        ];
    }
    
    return $resultados;
}

$resultados_cotizacion = $record ? motor_cotizacion_real($record) : [];
$mejor_plan = !empty($resultados_cotizacion) ? $resultados_cotizacion[0] : null;

$page_title = '¡Gracias por tu ' . $tipo_plan . '! | Plan Salud Fácil';
include __DIR__ . '/../layout/plantilla.php';
include __DIR__ . '/../layout/header.php';
?>
<!-- deploy:v2026-07-17-02 -->

<style>
    .confetti-piece {
        position: absolute;
        width: 10px;
        height: 10px;
        opacity: 0.7;
        animation: confetti-fall linear infinite;
    }
    @keyframes confetti-fall {
        0% { transform: translateY(-10vh) rotate(0deg); opacity: 1; }
        100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
    }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.4); }
        50% { box-shadow: 0 0 0 15px rgba(37, 99, 235, 0); }
    }
    .pulse-glow { animation: pulse-glow 2s infinite; }
    @keyframes btn-shine {
        0% { background-position: 200% center; }
        100% { background-position: -200% center; }
    }
    .btn-glow {
        background-size: 200% auto;
        animation: btn-shine 3s linear infinite;
    }
</style>

<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white flex items-center justify-center p-4">
    <!-- Confetti container -->
    <div id="confetti-container" class="fixed inset-0 pointer-events-none overflow-hidden z-0"></div>

    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl border border-gray-100 relative z-10 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-6 py-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-white/20 p-5 rounded-full pulse-glow">
                    <iconify-icon icon="mdi:check-circle" class="text-white" width="56"></iconify-icon>
                </div>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">
                ¡<?= htmlspecialchars($tipo_plan) ?> Recibida!
            </h1>
            <p class="text-blue-100 text-lg">Estamos procesando tu solicitud</p>
        </div>

        <!-- Content -->
        <div class="p-6 md:p-10 text-center">
            
            <?php if ($record): ?>
                <!-- ID de cotización -->
                <div class="mb-6">
                    <p class="text-gray-600 mb-3">Tu solicitud ha sido registrada con éxito</p>
                    <div class="inline-block bg-blue-50 rounded-xl px-6 py-4 mb-4 border border-blue-100">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">N° de Cotización</p>
                        <p class="text-3xl font-extrabold text-blue-600">#<?= htmlspecialchars($record['id']) ?></p>
                    </div>
                </div>

                <!-- Datos del registro -->
                <div class="bg-gray-50 rounded-xl p-5 mb-6 text-left border border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <iconify-icon icon="mdi:clipboard-text-outline" class="text-blue-600" width="20"></iconify-icon>
                        Resumen de tu solicitud
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-400">Nombre:</span>
                            <span class="text-gray-800 font-medium ml-1"><?= htmlspecialchars($record['nombre'] ?? '—') ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400">Email:</span>
                            <span class="text-gray-800 font-medium ml-1"><?= htmlspecialchars($record['correo'] ?? '—') ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400">Teléfono:</span>
                            <span class="text-gray-800 font-medium ml-1"><?= htmlspecialchars($record['celular'] ?? '—') ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400">Tipo de plan:</span>
                            <span class="text-gray-800 font-medium ml-1"><?= $tipo_plan_emoji . ' ' . htmlspecialchars($tipo_plan) ?></span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-gray-400">Fecha:</span>
                            <span class="text-gray-800 font-medium ml-1"><?= date('d/m/Y H:i', strtotime($record['fecha_creacion'] ?? 'now')) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Planes Recomendados (estilo comparador) -->
                <?php if (!empty($resultados_cotizacion)): 
                    $pct7 = intval(($record['datos_adicionales']['income'] ?? $ad['income'] ?? 1000000) * 0.07);
                    $isapre_colors = ['Banmédica'=>'bg-blue-600','Colmena'=>'bg-yellow-500','Consalud'=>'bg-green-600','Cruz Blanca'=>'bg-indigo-600','Esencial'=>'bg-purple-600','Nueva Masvida'=>'bg-pink-600','Vida Tres'=>'bg-red-600'];
                    $labels = ['🥇 Más Afín', '🥈', '🥉'];
                    $cargas_lead = intval($ad['cargas'] ?? 0);
                ?>
                <?php foreach ($resultados_cotizacion as $i => $alt): 
                    $ap = $alt['plan']; $as = $alt['score']; $ar = $alt['razones'];
                    $bg = $isapre_colors[$ap['isapre']] ?? 'bg-gray-600';
                    $dentro = $ap['precio'] <= $pct7;
                    $diff = $ap['precio'] - $pct7;
                    $scoreColor = $as >= 80 ? '#16a34a' : ($as >= 70 ? '#eab308' : '#dc2626');
                ?>
                <div class="bg-white border rounded-xl p-5 shadow-sm mb-4 <?= $i===0 ? 'border-blue-400 ring-2 ring-blue-100' : '' ?>">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-bold px-3 py-1 rounded-full text-white <?= $i===0 ? 'bg-blue-600' : ($i===1 ? 'bg-emerald-500' : ($i===2 ? 'bg-teal-500' : 'bg-gray-500')) ?>"><?= $labels[$i] ?? '#' . ($i+1) ?></span>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white <?= $bg ?>"><?= htmlspecialchars($ap['isapre']) ?></span>
                        <span class="text-xs text-gray-400"><?= $ap['prestadores'] ?> prestadores · <?= $ap['uf'] ?> UF</span>
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($ap['nombre']) ?></p>
                            <p class="text-gray-500 text-sm">Código: <?= htmlspecialchars($ap['codigo']) ?></p>
                            <div class="flex gap-4 mt-2 text-sm flex-wrap">
                                <span class="text-gray-600">📊 Tope anual: <strong><?= $ap['tope_anual_uf'] ?> UF</strong></span>
                                <span class="text-gray-600">📋 <?= $ap['prestadores'] ?> prestadores</span>
                            </div>
                            <?php if (!empty($ar)): ?>
                            <div class="mt-2 space-y-0.5">
                                <?php foreach (array_slice($ar, 0, 3) as $r): ?>
                                <p class="text-xs text-gray-500">✓ <?= htmlspecialchars($r) ?></p>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-4 md:block md:text-right">
                            <div class="relative" style="width:58px;height:58px">
                                <svg width="58" height="58" class="-rotate-90">
                                    <circle cx="29" cy="29" r="26" fill="none" stroke="#e5e7eb" stroke-width="4"/>
                                    <circle cx="29" cy="29" r="26" fill="none" stroke="<?= $scoreColor ?>" stroke-width="4"
                                            stroke-dasharray="<?= 2 * M_PI * 26 ?>" stroke-dashoffset="<?= 2 * M_PI * 26 * (1 - $as/100) ?>" stroke-linecap="round"/>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center text-sm font-extrabold text-gray-800"><?= $as ?></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400"><?= $dentro ? 'Dentro de tu 7%' : 'Cotización Adicional' ?></p>
                                <p class="text-2xl font-extrabold text-gray-900">$<?= number_format($ap['precio'], 0, ',', '.') ?><span class="text-sm font-normal text-gray-400">/mes</span></p>
                                <?= $dentro ? '<p class="text-xs text-green-600">✓ Cubierto</p>' : "<p class=\"text-xs text-amber-600\">+\$" . number_format($diff, 0, ',', '.') . " extra</p>" ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <!-- Próximos Pasos (CA-09) -->
                <div class="bg-white rounded-xl p-5 mb-6 text-left border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <iconify-icon icon="mdi:timeline-text-outline" class="text-blue-600" width="20"></iconify-icon>
                        ¿Qué sigue ahora?
                    </h3>
                    <ol class="space-y-3">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-7 h-7 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Revisamos tu perfil</p>
                                <p class="text-xs text-gray-500">Un ejecutivo especializado analiza tus datos y necesidades específicas.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-7 h-7 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Te contactamos</p>
                                <p class="text-xs text-gray-500">Recibirás una llamada o WhatsApp con las mejores opciones para ti, sin compromiso.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-7 h-7 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Eliges y contratas</p>
                                <p class="text-xs text-gray-500">Decides el plan que más te convenga y te guiamos en todo el proceso de contratación.</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Cotiza con Experto -->
                <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-xl p-6 mb-6 text-left shadow-xl overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-cyan-400/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    <h3 class="font-bold text-white text-lg mb-1 flex items-center gap-2 relative z-10">
                        <iconify-icon icon="mdi:account-tie-voice" width="22"></iconify-icon>
                        Cotiza con Experto
                    </h3>
                    <p class="text-blue-100 text-sm mb-4 relative z-10">¿Quieres una cotización 100% precisa? Un asesor revisará tu perfil, te contactará y te entregará la mejor opción ajustada a tu situación real.</p>
                    <div class="flex flex-col sm:flex-row gap-2 relative z-10">
                        <input type="email" id="save-email" value="<?= htmlspecialchars($record['correo'] ?? '') ?>" placeholder="tu@email.com" class="flex-1 px-4 py-2.5 bg-white/90 border border-white/20 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
                        <button onclick="saveQuote()" class="btn-glow bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-white font-bold px-5 py-2.5 rounded-lg transition text-sm flex items-center justify-center gap-1.5 shadow-lg shadow-cyan-500/30">
                            <iconify-icon icon="mdi:rocket-launch" width="18"></iconify-icon>
                            Cotizar con Experto
                        </button>
                    </div>
                    <p id="save-msg" class="text-xs text-cyan-200 mt-3 hidden relative z-10">✅ ¡Listo! Un experto revisará tu caso. Recibirás un correo de confirmación en breve.</p>
                </div>

                <!-- Ejecutivo Asignado (CA-06) -->
                <div class="bg-amber-50 rounded-xl p-5 mb-6 text-left border border-amber-100">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <iconify-icon icon="mdi:account-tie" class="text-amber-600" width="20"></iconify-icon>
                        Tu Ejecutivo Asignado
                    </h3>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-amber-200 rounded-full flex items-center justify-center">
                            <iconify-icon icon="mdi:account" class="text-amber-700" width="28"></iconify-icon>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Ejecutivo Plan Salud Fácil</p>
                            <p class="text-xs text-gray-500">Especialista en planes de isapre</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Se contactará contigo <strong>en las próximas horas hábiles</strong> para presentarte las mejores opciones según tu perfil.</p>
                    <p class="text-xs text-gray-400">Si no ves nuestro correo, revisa tu bandeja de spam o promociones.</p>
                </div>

                <!-- Sellos de Confianza (CA-09) -->
                <div class="bg-white rounded-xl p-5 mb-6 text-left border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <iconify-icon icon="mdi:shield-check" class="text-green-600" width="20"></iconify-icon>
                        ¿Por qué confiar en esta evaluación?
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="flex items-start gap-2 text-sm">
                            <iconify-icon icon="mdi:scale-balance" class="text-green-500 flex-shrink-0 mt-0.5" width="16"></iconify-icon>
                            <span class="text-gray-600">Evaluación <strong class="text-gray-800">imparcial</strong> basada en datos reales de la Superintendencia de Salud</span>
                        </div>
                        <div class="flex items-start gap-2 text-sm">
                            <iconify-icon icon="mdi:cash-remove" class="text-green-500 flex-shrink-0 mt-0.5" width="16"></iconify-icon>
                            <span class="text-gray-600"><strong class="text-gray-800">Sin sesgo</strong> por comisión de isapre. Nuestra prioridad eres tú</span>
                        </div>
                        <div class="flex items-start gap-2 text-sm">
                            <iconify-icon icon="mdi:database-check" class="text-green-500 flex-shrink-0 mt-0.5" width="16"></iconify-icon>
                            <span class="text-gray-600">Más de <strong class="text-gray-800">2.000</strong> evaluaciones cerradas en 15 años de experiencia de nuestros asesores</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Principal - Quiero Contratar (CA-17) -->
                <div class="bg-gradient-to-r from-green-600 to-emerald-500 rounded-xl p-6 mb-6 text-center text-white">
                    <h3 class="font-bold text-lg mb-2 flex items-center justify-center gap-2">
                        <iconify-icon icon="mdi:handshake" width="22"></iconify-icon>
                        ¿Listo para contratar el plan más afín a ti?
                    </h3>
                    <p class="text-green-100 text-sm mb-4">Un ejecutivo especializado tomará tu caso hoy mismo y te guiará en cada paso sin costo adicional.</p>
                    <a href="https://wa.me/56952282339?text=<?= urlencode('Hola, mi número de cotización es #' . $record_id . '. Quiero contratar el plan más afín a mi perfil.') ?>" target="_blank" class="inline-flex items-center px-6 py-3 bg-white text-green-700 font-bold rounded-xl hover:bg-green-50 transition shadow-lg text-base">
                        <iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon>
                        Quiero contratar este plan
                    </a>
                    <p class="text-green-200 text-xs mt-3">Sin compromiso. Recibirás asesoría personalizada antes de cualquier decisión.</p>
                </div>

                <!-- Testimonios (CA-08) -->
                <div class="bg-white rounded-xl p-5 mb-6 text-left border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <iconify-icon icon="mdi:account-voice" class="text-purple-600" width="20"></iconify-icon>
                        Personas como tú ya tomaron la decisión
                    </h3>
                    <div class="space-y-3">
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                            <div class="flex items-center gap-1 mb-1">
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                            </div>
                            <p class="text-sm text-gray-700 mb-1">"Ahorré $45.000 mensuales cambiándome de isapre. El ejecutivo me explicó todo paso a paso y en 3 días ya tenía el nuevo plan contratado."</p>
                            <p class="text-xs text-gray-400">— Carolina M., Providencia · Plan Individual</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                            <div class="flex items-center gap-1 mb-1">
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                                <iconify-icon icon="mdi:star" class="text-yellow-500" width="14"></iconify-icon>
                            </div>
                            <p class="text-sm text-gray-700 mb-1">"Tenía dudas de dejar FONASA, pero el comparador me mostró que con mi renta podía acceder a un plan con mejor cobertura por casi lo mismo. La telemedicina incluida fue clave para mí."</p>
                            <p class="text-xs text-gray-400">— Andrés G., La Florida · Plan Familiar</p>
                        </div>
                    </div>
                </div>

                <!-- Validez de la Cotización (CA-16) -->
                <div class="flex items-center gap-2 text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mb-6 justify-center">
                    <iconify-icon icon="mdi:information-outline" width="14"></iconify-icon>
                    <span>Los precios de esta cotización fueron verificados hoy. Las isapres pueden ajustar sus tarifas periódicamente.</span>
                </div>

            <?php else: ?>
                <?php 
                $age = intval($_GET['age'] ?? 0);
                $income = intval($_GET['income'] ?? 0);
                $cargas = intval($_GET['cargas'] ?? -1);
                $intereses_raw = $_GET['intereses'] ?? '';
                $intereses = !empty($intereses_raw) ? array_map('trim', explode(',', $intereses_raw)) : [];
                // Edades de cargas
                $carga_edades_raw = $_GET['carga_edad'] ?? [];
                if (!is_array($carga_edades_raw)) $carga_edades_raw = [$carga_edades_raw];
                $carga_edades = array_values(array_map('intval', array_filter($carga_edades_raw, function($v) { return $v !== ''; })));
                $has_real_data = ($age > 0 && $income > 0 && $cargas >= 0);
                if ($has_real_data) {
                    $cot_record = ['datos_adicionales' => ['age' => $age, 'income' => $income, 'cargas' => $cargas, 'interests' => $intereses, 'edad_cargas' => $carga_edades], 'fecha_creacion' => date('Y-m-d H:i:s')];
                } else {
                    $cot_record = ['datos_adicionales' => ['age' => 30, 'income' => 1500000, 'cargas' => 0, 'interests' => ['Hospitalización', 'Atención Ambulatoria'], 'edad_cargas' => []], 'fecha_creacion' => date('Y-m-d H:i:s')];
                }
                $resultados_cotizacion = motor_cotizacion_real($cot_record);
                ?>
                <div class="mb-6">
                    <div class="inline-block bg-green-50 rounded-xl px-6 py-4 mb-4 border border-green-100">
                        <iconify-icon icon="mdi:check-circle" class="text-green-500" width="40"></iconify-icon>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Solicitud recibida!</h2>
                    <p class="text-gray-600 max-w-md mx-auto"><?= $has_real_data ? 'Estos son los planes más afines a tu perfil:' : 'Un ejecutivo revisará tu caso. Mientras tanto, así funciona nuestro motor con un perfil de ejemplo:' ?></p>
                </div>

                <?php if (!$has_real_data): ?>
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-2 mb-4 text-sm text-amber-700 text-center">
                    ⚠️ Resultados de ejemplo. Completa el formulario para tu cotización personalizada.
                </div>
                <?php endif; ?>

                <?php if (!empty($resultados_cotizacion)): 
                    $pct7_demo = intval(($cot_record['datos_adicionales']['income'] ?? 1500000) * 0.07);
                    $isapre_colors_demo = ['Banmédica'=>'bg-blue-600','Colmena'=>'bg-yellow-500','Consalud'=>'bg-green-600','Cruz Blanca'=>'bg-indigo-600','Esencial'=>'bg-purple-600','Nueva Masvida'=>'bg-pink-600','Vida Tres'=>'bg-red-600'];
                    $labels_demo = ['🥇 Más Afín', '🥈', '🥉'];
                    foreach ($resultados_cotizacion as $i_demo => $alt_demo): 
                        $ap_demo = $alt_demo['plan']; $as_demo = $alt_demo['score']; $ar_demo = $alt_demo['razones'];
                        $bg_demo = $isapre_colors_demo[$ap_demo['isapre']] ?? 'bg-gray-600';
                        $dentro_demo = $ap_demo['precio'] <= $pct7_demo;
                        $diff_demo = $ap_demo['precio'] - $pct7_demo;
                        $sc_demo = $as_demo >= 80 ? '#16a34a' : ($as_demo >= 70 ? '#eab308' : '#dc2626');
                ?>
                <div class="bg-white border rounded-xl p-5 shadow-sm mb-4 <?= $i_demo===0 ? 'border-blue-400 ring-2 ring-blue-100' : '' ?>">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-xs font-bold px-3 py-1 rounded-full text-white <?= $i_demo===0 ? 'bg-blue-600' : ($i_demo===1 ? 'bg-emerald-500' : ($i_demo===2 ? 'bg-teal-500' : 'bg-gray-500')) ?>"><?= $labels_demo[$i_demo] ?? '#' . ($i_demo+1) ?></span>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white <?= $bg_demo ?>"><?= htmlspecialchars($ap_demo['isapre']) ?></span>
                        <span class="text-xs text-gray-400"><?= $ap_demo['prestadores'] ?> prestadores · <?= $ap_demo['uf'] ?> UF</span>
                    </div>
                    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($ap_demo['nombre']) ?></p>
                            <p class="text-gray-500 text-sm">Código: <?= htmlspecialchars($ap_demo['codigo']) ?></p>
                            <div class="flex gap-4 mt-2 text-sm flex-wrap">
                                <span class="text-gray-600">📊 Tope anual: <strong><?= $ap_demo['tope_anual_uf'] ?> UF</strong></span>
                                <span class="text-gray-600">📋 <?= $ap_demo['prestadores'] ?> prestadores</span>
                            </div>
                            <?php if (!empty($ar_demo)): ?>
                            <div class="mt-2 space-y-0.5"><?php foreach (array_slice($ar_demo,0,3) as $r_demo): ?><p class="text-xs text-gray-500">✓ <?= htmlspecialchars($r_demo) ?></p><?php endforeach; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center gap-4 md:block md:text-right">
                            <div class="relative" style="width:58px;height:58px">
                                <svg width="58" height="58" class="-rotate-90"><circle cx="29" cy="29" r="26" fill="none" stroke="#e5e7eb" stroke-width="4"/><circle cx="29" cy="29" r="26" fill="none" stroke="<?= $sc_demo ?>" stroke-width="4" stroke-dasharray="<?= 2*M_PI*26 ?>" stroke-dashoffset="<?= 2*M_PI*26*(1-$as_demo/100) ?>" stroke-linecap="round"/></svg>
                                <span class="absolute inset-0 flex items-center justify-center text-sm font-extrabold text-gray-800"><?= $as_demo ?></span>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400"><?= $dentro_demo ? 'Dentro de tu 7%' : 'Cotización Adicional' ?></p>
                                <p class="text-2xl font-extrabold text-gray-900">$<?= number_format($ap_demo['precio'],0,',','.') ?><span class="text-sm font-normal text-gray-400">/mes</span></p>
                                <?= $dentro_demo ? '<p class="text-xs text-green-600">✓ Cubierto</p>' : '<p class="text-xs text-amber-600">+$'.number_format($diff_demo,0,',','.').' extra</p>' ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>

            <!-- CTAs finales -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?= BASE_URL ?>/planes/comparador/" class="inline-flex items-center px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-md">
                    <iconify-icon icon="mdi:refresh" class="mr-2" width="18"></iconify-icon>
                    Volver a cotizar
                </a>
                <a href="<?= BASE_URL ?>/" class="inline-flex items-center px-5 py-3 bg-white text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition border border-gray-200">
                    <iconify-icon icon="mdi:home" class="mr-2" width="18"></iconify-icon>
                    Ir al inicio
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Conversion Tracking -->
<?php if ($record): ?>
<script>
    // Google Ads conversion
    gtag('event', 'conversion', {
        'send_to': 'AW-17127470305/<?= htmlspecialchars($tipo_plan) ?>',
        'value': 1.0,
        'currency': 'CLP',
        'transaction_id': '<?= htmlspecialchars($record['id']) ?>'
    });

    // PSF Activity Tracker
    if (window.psfActivityTracker) {
        window.psfActivityTracker.trackConversion('form_success', {
            form_type: '<?= htmlspecialchars($tipo_plan) ?>',
            record_id: <?= $record['id'] ?>
        });
    }
</script>
<?php endif; ?>

<script>
    // Save quote — envía la cotización al correo
    async function saveQuote() {
        const email = document.getElementById('save-email').value;
        const msgEl = document.getElementById('save-msg');
        if (!email) { msgEl.textContent = 'Ingresa tu correo primero'; msgEl.classList.remove('hidden'); return; }
        msgEl.textContent = 'Enviando...'; msgEl.classList.remove('hidden');
        try {
            const resp = await fetch('<?= BASE_URL ?>/api/cotizar.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    nombre: '<?= htmlspecialchars($record['nombre'] ?? 'Usuario', ENT_QUOTES) ?>',
                    edad: <?= intval($ad['age'] ?? 30) ?>,
                    renta: <?= intval($ad['income'] ?? 1000000) ?>,
                    cargas: <?= intval($ad['cargas'] ?? 0) ?>,
                    intereses: <?= json_encode($ad['interests'] ?? ['Hospitalización'], JSON_UNESCAPED_UNICODE) ?>,
                    email: email,
                    record_id: <?= $record_id ?? 0 ?>
                })
            });
            const data = await resp.json();
            if (data.recomendaciones) {
                if (data.email_sent) {
                    msgEl.textContent = '✅ ¡Listo! Un experto revisará tu caso. Recibirás un correo de confirmación en breve.';
                    msgEl.className = 'text-xs text-cyan-200 mt-3';
                    msgEl.classList.remove('hidden');
                } else {
                    msgEl.textContent = '⚠️ No pudimos enviar el correo. ¿Puedes intentar de nuevo?';
                    msgEl.className = 'text-xs text-white mt-3';
                    msgEl.classList.remove('hidden');
                }
            }
        } catch(e) {
            msgEl.textContent = 'Error de conexión. Intenta más tarde.';
            msgEl.className = 'text-xs text-white mt-3';
            msgEl.classList.remove('hidden');
        }
    }

    // Abrir modal de WhatsApp
    function trackWhatsApp(e) {
        e.preventDefault();
        document.getElementById('wsp-modal').classList.remove('hidden');
        document.getElementById('wsp-nombre').focus();
    }

    // Enviar desde el modal de WhatsApp
    async function sendWhatsApp() {
        const nombre = document.getElementById('wsp-nombre').value.trim();
        const telefono = document.getElementById('wsp-telefono').value.trim();
        const errEl = document.getElementById('wsp-error');
        
        if (!nombre) { errEl.textContent = 'Ingresa tu nombre'; errEl.classList.remove('hidden'); return; }
        if (!telefono || telefono.length < 9) { errEl.textContent = 'Ingresa un teléfono válido (9 dígitos)'; errEl.classList.remove('hidden'); return; }
        errEl.classList.add('hidden');
        
        // Guardar en BD
        try {
            await fetch('<?= BASE_URL ?>/guardar_whatsapp.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    name: nombre,
                    phone: telefono,
                    source: 'gracias_cotizacion',
                    record_id: <?= $record_id ?>
                })
            });
        } catch(e) { /* no bloquear */ }
        
        // Track conversión
        if (window.psfActivityTracker) {
            window.psfActivityTracker.trackConversion('whatsapp_click', {
                record_id: <?= $record_id ?>,
                form_type: '<?= htmlspecialchars($tipo_plan) ?>'
            });
        }
        if (typeof gtag !== 'undefined') {
            gtag('event', 'conversion', {
                'send_to': 'AW-17127470305/whatsapp',
                'value': 1.0,
                'currency': 'CLP'
            });
        }
        
        // Cerrar modal y abrir WhatsApp
        document.getElementById('wsp-modal').classList.add('hidden');
        window.open('https://wa.me/56952282339?text=<?= urlencode('Hola, mi número de cotización es #' . $record_id . '. Quisiera más información sobre los planes recomendados.') ?>', '_blank');
    }

    // Confetti
    (function() {
        const container = document.getElementById('confetti-container');
        const colors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#ec4899'];
        for (let i = 0; i < 60; i++) {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.left = Math.random() * 100 + '%';
            piece.style.width = (Math.random() * 8 + 4) + 'px';
            piece.style.height = (Math.random() * 8 + 4) + 'px';
            piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            piece.style.animationDuration = (Math.random() * 3 + 3) + 's';
            piece.style.animationDelay = Math.random() * 4 + 's';
            piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
            container.appendChild(piece);
        }
    })();

    // Modal WhatsApp
    const wspModal = document.getElementById('wsp-modal');
    wspModal.addEventListener('click', function(e) { if (e.target === wspModal) wspModal.classList.add('hidden'); });
</script>

<!-- Modal WhatsApp -->
<div id="wsp-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="z-index:9999">
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-[min(92vw,400px)]">
        <button onclick="document.getElementById('wsp-modal').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        <h3 class="text-lg font-bold text-gray-800 mb-1">¿Hablamos por WhatsApp?</h3>
        <p class="text-sm text-gray-500 mb-4">Déjanos tu nombre y teléfono para enviarte los planes recomendados.</p>
        <div class="space-y-3">
            <input id="wsp-nombre" type="text" placeholder="Tu nombre" value="<?= htmlspecialchars($record['nombre'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            <input id="wsp-telefono" type="tel" placeholder="Teléfono (9 dígitos)" value="<?= htmlspecialchars($record['celular'] ?? '') ?>" maxlength="9" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            <p id="wsp-error" class="hidden text-xs text-red-600"></p>
            <button onclick="sendWhatsApp()" class="w-full py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition flex items-center justify-center gap-2">
                <iconify-icon icon="mdi:whatsapp" width="20"></iconify-icon>
                Enviar y abrir WhatsApp
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

