<?php
/**
 * gracias.php — Página de Agradecimiento Post-Formulario
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

$page_title = '¡Gracias por tu ' . $tipo_plan . '! | Plan Salud Fácil';
include __DIR__ . '/../layout/plantilla.php';
include __DIR__ . '/../layout/header.php';
?>

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

                <p class="text-gray-600 mb-2">Un asesor especializado se contactará contigo en las próximas horas.</p>
                <p class="text-gray-400 text-sm mb-6">Si no ves nuestro correo, revisa tu bandeja de spam o promociones.</p>

            <?php else: ?>
                <!-- Sin registro (sin ID o no encontrado) -->
                <div class="mb-6">
                    <div class="inline-block bg-green-50 rounded-xl px-6 py-4 mb-4 border border-green-100">
                        <iconify-icon icon="mdi:check-circle" class="text-green-500" width="40"></iconify-icon>
                    </div>
                    <p class="text-gray-600">Hemos recibido tu solicitud correctamente.</p>
                    <p class="text-gray-600">Un asesor se contactará contigo pronto.</p>
                </div>
            <?php endif; ?>

            <!-- Contacto directo -->
            <div class="bg-blue-50 rounded-xl p-5 mb-8 text-left border border-blue-100">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <iconify-icon icon="mdi:headset" class="text-blue-600" width="20"></iconify-icon>
                    ¿Prefieres hablar ahora?
                </h3>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="https://wa.me/56952282339" target="_blank" class="flex items-center gap-2 text-green-700 bg-green-100 hover:bg-green-200 px-4 py-2 rounded-lg transition font-medium">
                        <iconify-icon icon="mdi:whatsapp" width="20"></iconify-icon>
                        +56 9 5228 2339
                    </a>
                    <a href="mailto:contacto@plansaludfacil.cl" class="flex items-center gap-2 text-blue-700 bg-blue-100 hover:bg-blue-200 px-4 py-2 rounded-lg transition font-medium">
                        <iconify-icon icon="mdi:email" width="20"></iconify-icon>
                        contacto@plansaludfacil.cl
                    </a>
                </div>
            </div>

            <!-- Countdown + CTA -->
            <div class="flex flex-col items-center">
                <p class="text-gray-400 text-sm mb-4 flex items-center gap-1">
                    <iconify-icon icon="mdi:clock-outline" width="16"></iconify-icon>
                    Serás redirigido al inicio en <span id="countdown" class="font-bold text-gray-600">15</span> segundos
                </p>
                <a href="/" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-md">
                    Volver al inicio ahora
                    <iconify-icon icon="mdi:arrow-right" class="ml-2" width="18"></iconify-icon>
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
    // Countdown
    let seconds = 15;
    const cd = document.getElementById('countdown');
    const interval = setInterval(() => {
        seconds--;
        if (cd) cd.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = '/';
        }
    }, 1000);

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
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
