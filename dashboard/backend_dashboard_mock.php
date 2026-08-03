<?php
// backend_dashboard_mock.php
// Dashboard de leads — PlanSaludFácil v2.0
// Extrae correctamente datos_adicionales de todas las fuentes (flat + nested)

$DB_HOST = 'localhost';
$DB_USER = 'plansalu_blogger';
$DB_PASS = 'Blog.2025!#';
$DB_NAME = 'plansalu_blog';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('<div style="color:red;padding:20px;">Error de conexión a la base de datos: ' . $mysqli->connect_error . '</div>');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ─── MÉTRICAS ────────────────────────────────────────────────
$total_leads = $new_leads = $contacted_leads = $cerrados = 0;

$res = $mysqli->query("SELECT COUNT(id) as total FROM procesar_formularios");
if ($res) $total_leads = (int)$res->fetch_assoc()['total'];

$res = $mysqli->query("SELECT COUNT(id) as n FROM procesar_formularios WHERE estado = 'Nuevo'");
if ($res) $new_leads = (int)$res->fetch_assoc()['n'];

$res = $mysqli->query("SELECT COUNT(id) as n FROM procesar_formularios WHERE estado = 'Contactado'");
if ($res) $contacted_leads = (int)$res->fetch_assoc()['n'];

$res = $mysqli->query("SELECT COUNT(id) as n FROM procesar_formularios WHERE estado = 'Cerrado'");
if ($res) $cerrados = (int)$res->fetch_assoc()['n'];

// Leads de hoy
$hoy = date('Y-m-d');
$leads_hoy = 0;
$res = $mysqli->query("SELECT COUNT(id) as n FROM procesar_formularios WHERE DATE(fecha_creacion) = '$hoy'");
if ($res) $leads_hoy = (int)$res->fetch_assoc()['n'];

// ─── NORMALIZAR DATOS ADICIONALES ────────────────────────────
function normalizarLead($row) {
    if (empty($row['datos_adicionales'])) return $row;

    $ad = json_decode($row['datos_adicionales'], true);
    if (!is_array($ad)) return $row;

    // ── Estructura A: Flat (formularios del sitio) ──
    $flatMap = [
        'age'              => 'edad',
        'income'           => 'renta',
        'comuna'           => 'region',
        'isapre_actual'    => 'prevision_interes',
        'preferencia_plan' => 'plan_interes',
        'rut'              => 'rut',
        'cargas'           => 'cargas',
        'query_type'       => 'tipo_formulario',
        'tipo_plan'        => 'tipo_plan',
        'pais'             => 'pais',
        'pais_residencia'  => 'pais',
        'origen_lead'      => 'origen_lead',
        'tracking_session_id' => 'tracking_session_id',
    ];

    foreach ($flatMap as $jsonKey => $rowKey) {
        if (isset($ad[$jsonKey]) && empty($row[$rowKey])) {
            $row[$rowKey] = $ad[$jsonKey];
        }
    }

    // Intereses (array → string)
    if (!empty($ad['interests']) && is_array($ad['interests'])) {
        $row['intereses'] = implode(', ', $ad['interests']);
    } elseif (!empty($ad['needs']) && is_array($ad['needs'])) {
        $row['intereses'] = implode(', ', $ad['needs']);
    }

    // Mensaje del formulario
    if (!empty($ad['message'])) {
        $row['mensaje'] = $ad['message'];
    }

    // Preexistencias
    if (isset($ad['preexistence'])) {
        $row['preexistencias'] = ($ad['preexistence'] === 'si') ? 'Sí' : 'No';
        if (!empty($ad['preexistence_text'])) {
            $row['preexistencias'] .= ': ' . $ad['preexistence_text'];
        }
    }

    // Cargas edades (familiar)
    $edades_cargas = [];
    for ($i = 1; $i <= 6; $i++) {
        $key = "carga_edad_$i";
        if (!empty($ad[$key])) $edades_cargas[] = $ad[$key];
    }
    if (!empty($edades_cargas)) {
        $row['edades_cargas'] = implode(', ', $edades_cargas);
    }

    // Complementar renta
    if (!empty($ad['complementar_renta'])) {
        $row['complementar_renta'] = 'Sí';
    }

    // ── Estructura B: Nested (personal.* / salud.*) ──
    if (isset($ad['personal']) && is_array($ad['personal'])) {
        $p = $ad['personal'];
        if (empty($row['rut']))     $row['rut']     = $p['rut'] ?? null;
        if (empty($row['edad']))    $row['edad']    = $p['edad'] ?? null;
        if (empty($row['region']))  $row['region']  = $p['region'] ?? $p['comuna'] ?? null;
        if (empty($row['renta']))   $row['renta']   = $p['renta'] ?? null;
    }

    if (isset($ad['salud']) && is_array($ad['salud'])) {
        $s = $ad['salud'];
        if (empty($row['prevision_interes'])) $row['prevision_interes'] = $s['prevision'] ?? $s['prevision_interes'] ?? null;
        if (empty($row['plan_interes']))      $row['plan_interes']      = $s['plan'] ?? $s['plan_interes'] ?? null;
        if (empty($row['rut']))               $row['rut']               = $s['rut'] ?? null;
        if (empty($row['edad']))              $row['edad']              = $s['edad'] ?? null;
        if (empty($row['region']))            $row['region']            = $s['region'] ?? null;
        if (empty($row['renta']))             $row['renta']             = $s['renta'] ?? null;
        if (empty($row['cargas']))            $row['cargas']            = $s['cargas'] ?? null;
        $row['salud_genero'] = $s['genero'] ?? null;
    }

    // ── Limpiar campos de mensaje para display ──
    if (!empty($row['mensaje'])) {
        $primerLinea = strtok($row['mensaje'], "\n");
        $row['mensaje_resumen'] = mb_substr(strip_tags($primerLinea), 0, 100);
    }

    return $row;
}

// ─── OBTENER LEADS ───────────────────────────────────────────
$leads = [];
$res = $mysqli->query("
    SELECT id, nombre, correo, celular, pais, estado, notas, borrador_respuesta_ia,
           first_contact_date, second_contact_date, sale_closing_date,
           fecha_creacion, unsubscribed, datos_adicionales
    FROM procesar_formularios
    ORDER BY fecha_creacion DESC
");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $leads[] = normalizarLead($row);
    }
}

$count_leads = count($leads);
$debug_msg = ($count_leads === 0) ? "No se encontraron leads en la base de datos." : null;

$mysqli->close();

// ─── HELPERS ─────────────────────────────────────────────────
function badgeEstado($estado) {
    $map = [
        'Nuevo'      => ['bg' => '#dbeafe', 'fg' => '#1e40af', 'icon' => '🆕'],
        'Contactado' => ['bg' => '#fef3c7', 'fg' => '#92400e', 'icon' => '📞'],
        'Cerrado'    => ['bg' => '#d1fae5', 'fg' => '#065f46', 'icon' => '✅'],
    ];
    $c = $map[$estado] ?? ['bg' => '#f3f4f6', 'fg' => '#374151', 'icon' => '•'];
    return "<span style='background:{$c['bg']};color:{$c['fg']};padding:3px 10px;border-radius:12px;font-size:12px;font-weight:600;white-space:nowrap;'>{$c['icon']} {$estado}</span>";
}

function fmtCLP($val) {
    if (!$val) return '-';
    $n = (int)$val;
    if ($n <= 0) return '-';
    return '$' . number_format($n, 0, ',', '.');
}

function fmtDate($val) {
    if (!$val || $val === '0000-00-00 00:00:00') return '-';
    if (substr($val, -8) === '00:00:00') return substr($val, 0, 10);
    return $val;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Leads — PlanSaludFácil</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background: #f1f5f9; color: #1e293b; }
        .header { background: linear-gradient(135deg, #1e40af, #3b82f6); color: #fff; padding: 20px 30px; }
        .header h1 { font-size: 24px; font-weight: 700; }
        .header .sub { font-size: 13px; opacity: .8; margin-top: 4px; }

        .metrics-bar { display: flex; gap: 16px; padding: 16px 30px; background: #fff; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; }
        .metric { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 20px; min-width: 120px; text-align: center; }
        .metric .num { font-size: 28px; font-weight: 800; color: #1e40af; }
        .metric .label { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin-top: 2px; }

        .toolbar { display: flex; gap: 12px; padding: 12px 30px; background: #fff; border-bottom: 1px solid #e2e8f0; flex-wrap: wrap; align-items: center; }
        .toolbar input { padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 260px; }
        .toolbar input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
        .toolbar .count { font-size: 13px; color: #64748b; margin-left: auto; }

        .table-wrap { overflow-x: auto; padding: 0 30px 30px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        th { background: #f8fafc; padding: 10px 10px; text-align: left; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; white-space: nowrap; position: sticky; top: 0; }
        td { padding: 9px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        tr:hover td { background: #f8fafc; }

        .msg-toggle { cursor: pointer; color: #3b82f6; font-size: 12px; text-decoration: underline; }
        .msg-full { display: none; font-size: 12px; color: #64748b; margin-top: 4px; max-width: 300px; white-space: pre-wrap; word-break: break-word; }
        .msg-full.open { display: block; }

        @media (max-width: 768px) {
            .header, .metrics-bar, .toolbar, .table-wrap { padding-left: 12px; padding-right: 12px; }
            .toolbar input { width: 100%; }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>📊 Dashboard de Leads</h1>
    <div class="sub">PlanSaludFácil — Total: <?= $count_leads ?> leads | Hoy: <?= $leads_hoy ?> nuevos</div>
</div>

<?php if ($debug_msg): ?>
<div style="padding:16px 30px;background:#fff3cd;border-bottom:1px solid #ffc107;color:#856404;font-weight:500;">
    <?= $debug_msg ?>
</div>
<?php endif; ?>

<div class="metrics-bar">
    <div class="metric"><div class="num"><?= $total_leads ?></div><div class="label">Total Leads</div></div>
    <div class="metric"><div class="num"><?= $new_leads ?></div><div class="label">🆕 Nuevos</div></div>
    <div class="metric"><div class="num"><?= $contacted_leads ?></div><div class="label">📞 Contactados</div></div>
    <div class="metric"><div class="num"><?= $cerrados ?></div><div class="label">✅ Cerrados</div></div>
    <div class="metric"><div class="num"><?= $leads_hoy ?></div><div class="label">Hoy</div></div>
</div>

<div class="toolbar">
    <input type="text" id="search" placeholder="🔍 Buscar por nombre, email, teléfono…" oninput="filtrar()">
    <span class="count" id="visible-count">Mostrando <?= $count_leads ?> leads</span>
</div>

<div class="table-wrap">
    <table id="leads-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Tipo</th>
                <th>Origen</th>
                <th>Sist. Actual</th>
                <th>Comuna</th>
                <th>Edad</th>
                <th>Renta</th>
                <th>Cargas</th>
                <th>Intereses</th>
                <th>Estado</th>
                <th>Notas</th>
                <th>1er Contacto</th>
                <th>2do Contacto</th>
                <th>Cierre</th>
                <th>Creado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $l): ?>
            <tr data-search="<?= htmlspecialchars(strtolower(
                implode(' ', array_filter([
                    $l['id'], $l['nombre'], $l['correo'], $l['celular'],
                    $l['tipo_formulario'] ?? '', $l['prevision_interes'] ?? '',
                    $l['region'] ?? '', $l['intereses'] ?? '', $l['mensaje'] ?? ''
                ]))
            )) ?>">
                <td><?= $l['id'] ?></td>
                <td><strong><?= htmlspecialchars($l['nombre']) ?></strong></td>
                <td><a href="mailto:<?= htmlspecialchars($l['correo']) ?>" style="color:#2563eb;"><?= htmlspecialchars($l['correo'] ?? '-') ?></a></td>
                <td><?= htmlspecialchars($l['celular'] ?? '-') ?></td>
                <td><?= htmlspecialchars($l['tipo_formulario'] ?? $l['tipo_plan'] ?? '-') ?></td>
                <td><?= htmlspecialchars($l['origen_lead'] ?? '-') ?></td>
                <td><?= htmlspecialchars($l['prevision_interes'] ?? '-') ?></td>
                <td><?= htmlspecialchars($l['region'] ?? '-') ?></td>
                <td><?= htmlspecialchars($l['edad'] ?? '-') ?></td>
                <td><?= fmtCLP($l['renta'] ?? null) ?></td>
                <td><?= htmlspecialchars($l['cargas'] ?? '-') ?></td>
                <td style="max-width:180px;">
                    <?php if (!empty($l['intereses'])): ?>
                        <span style="font-size:11px;"><?= htmlspecialchars(mb_substr($l['intereses'], 0, 80)) ?></span>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                    <?php if (!empty($l['mensaje'])): ?>
                        <br><span class="msg-toggle" onclick="this.nextElementSibling.classList.toggle('open')">📝 ver mensaje</span>
                        <div class="msg-full"><?= htmlspecialchars($l['mensaje']) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= badgeEstado($l['estado']) ?></td>
                <td style="max-width:150px;font-size:12px;">
                    <?php if (!empty($l['notas'])): ?>
                        <?= htmlspecialchars(mb_substr($l['notas'], 0, 80)) ?>
                    <?php else: ?>
                        <span style="color:#94a3b8;">-</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:12px;"><?= fmtDate($l['first_contact_date'] ?? null) ?></td>
                <td style="font-size:12px;"><?= fmtDate($l['second_contact_date'] ?? null) ?></td>
                <td style="font-size:12px;"><?= fmtDate($l['sale_closing_date'] ?? null) ?></td>
                <td style="font-size:12px;"><?= htmlspecialchars($l['fecha_creacion']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function filtrar() {
    const q = document.getElementById('search').value.toLowerCase();
    const rows = document.querySelectorAll('#leads-table tbody tr');
    let visible = 0;
    rows.forEach(r => {
        const match = !q || r.dataset.search.includes(q);
        r.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('visible-count').textContent =
        q ? `Mostrando ${visible} de <?= $count_leads ?> leads` : `Mostrando <?= $count_leads ?> leads`;
}
</script>

</body>
</html>
