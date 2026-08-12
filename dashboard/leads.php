<?php
/**
 * dashboard/leads.php
 * Dashboard unificado de leads — PlanSaludFácil v2
 * 
 * Consolida datos de procesar_formularios + cotizaciones en una sola vista.
 * Incluye métricas, filtros, búsqueda, paginación y detalle expandible.
 */

require_once __DIR__ . '/../config.php';

// ─── CONEXIÓN ──────────────────────────────────────────────────
$mysqli = connect_db_simple();
if ($mysqli === null) {
    die('<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family:sans-serif;padding:40px;text-align:center;"><h1 style="color:#dc2626;">Error de conexión a la base de datos</h1><p>No se pudo conectar a MySQL. Revisá config.php.</p></body></html>');
}
$mysqli->set_charset("utf8mb4");

// ─── ASEGURAR COLUMNA estado EN cotizaciones ───────────────────
@$mysqli->query("ALTER TABLE cotizaciones ADD COLUMN IF NOT EXISTS estado VARCHAR(30) NOT NULL DEFAULT 'Nuevo' AFTER fecha_creacion");

// ─── ESTADOS CENTRALIZADOS (fuente única de verdad) ─────────────
$ESTADOS = [
    'Nuevo'                   => ['label' => 'Nuevo',                    'icon' => '🆕', 'bg' => 'bg-blue-100',     'fg' => 'text-blue-800'],
    'Contactado'              => ['label' => 'Contactado',               'icon' => '📞', 'bg' => 'bg-amber-100',    'fg' => 'text-amber-800'],
    'En Negociación'          => ['label' => 'En Negociación',           'icon' => '💬', 'bg' => 'bg-indigo-100',   'fg' => 'text-indigo-800'],
    'Cotización Enviada'      => ['label' => 'Cotización Enviada',       'icon' => '📧', 'bg' => 'bg-sky-100',      'fg' => 'text-sky-800'],
    'En Espera de Respuesta'  => ['label' => 'En Espera de Respuesta',   'icon' => '⏳', 'bg' => 'bg-yellow-100',   'fg' => 'text-yellow-800'],
    'No Desea Asesoría'       => ['label' => 'No Desea Asesoría',        'icon' => '🚫', 'bg' => 'bg-rose-100',     'fg' => 'text-rose-800'],
    'Oferta No Corresponde'   => ['label' => 'Oferta No Corresponde',    'icon' => '❌', 'bg' => 'bg-orange-100',   'fg' => 'text-orange-800'],
    'Cerrado'                 => ['label' => 'Cerrado',                  'icon' => '✅', 'bg' => 'bg-emerald-100',  'fg' => 'text-emerald-800'],
    'Contacto Cerrado'        => ['label' => 'Contacto Cerrado',         'icon' => '🔒', 'bg' => 'bg-slate-100',    'fg' => 'text-slate-800'],
    'No Responde'             => ['label' => 'No Responde',              'icon' => '🔇', 'bg' => 'bg-gray-100',     'fg' => 'text-gray-800'],
    'Facturado'               => ['label' => 'Facturado',                'icon' => '💰', 'bg' => 'bg-lime-100',     'fg' => 'text-lime-800'],
];

// ─── HANDLER AJAX: actualizar estado ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_estado') {
    header('Content-Type: application/json');

    $uid    = $_POST['uid'] ?? '';
    $nuevo  = $_POST['estado'] ?? '';
    $validos = array_keys($ESTADOS);

    if (!in_array($nuevo, $validos, true)) {
        echo json_encode(['ok' => false, 'error' => 'Estado inválido']);
        exit;
    }

    // Parsear uid: pf_123 → table=procesar_formularios, id=123
    if (str_starts_with($uid, 'pf_')) {
        $tabla = 'procesar_formularios';
        $id = (int)substr($uid, 3);
    } elseif (str_starts_with($uid, 'ct_')) {
        $tabla = 'cotizaciones';
        $id = (int)substr($uid, 3);
    } else {
        echo json_encode(['ok' => false, 'error' => 'UID inválido']);
        exit;
    }

    $stmt = $mysqli->prepare("UPDATE $tabla SET estado = ? WHERE id = ?");
    $stmt->bind_param('si', $nuevo, $id);
    $ok = $stmt->execute();

    echo json_encode(['ok' => $ok, 'error' => $ok ? null : $mysqli->error]);
    $mysqli->close();
    exit;
}

// ─── PARÁMETROS ────────────────────────────────────────────────
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 20;
$search   = trim($_GET['search'] ?? '');
$estado   = $_GET['estado'] ?? '';
$origen   = $_GET['origen'] ?? '';
$offset   = ($page - 1) * $perPage;

// ─── MÉTRICAS ──────────────────────────────────────────────────
$metricas = ['total' => 0, 'nuevos' => 0, 'contactados' => 0, 'cerrados' => 0, 'hoy' => 0];
$hoy = date('Y-m-d');

// Helper: suma conteo de una query a una clave de métrica
$sumar = function($mysqli, string $sql, string $clave) use (&$metricas) {
    $r = $mysqli->query($sql);
    if ($r) $metricas[$clave] += (int)$r->fetch_assoc()['n'];
};

// Total
$sumar($mysqli, "SELECT COUNT(id) as n FROM procesar_formularios", 'total');
$sumar($mysqli, "SELECT COUNT(id) as n FROM cotizaciones", 'total');

// Nuevos
$sumar($mysqli, "SELECT COUNT(id) as n FROM procesar_formularios WHERE estado='Nuevo'", 'nuevos');
$sumar($mysqli, "SELECT COUNT(id) as n FROM cotizaciones WHERE COALESCE(estado,'Nuevo')='Nuevo'", 'nuevos');

// Contactados
$sumar($mysqli, "SELECT COUNT(id) as n FROM procesar_formularios WHERE estado='Contactado'", 'contactados');
$sumar($mysqli, "SELECT COUNT(id) as n FROM cotizaciones WHERE estado='Contactado'", 'contactados');

// Cerrados
$sumar($mysqli, "SELECT COUNT(id) as n FROM procesar_formularios WHERE estado='Cerrado'", 'cerrados');
$sumar($mysqli, "SELECT COUNT(id) as n FROM cotizaciones WHERE estado='Cerrado'", 'cerrados');

// Hoy
$sumar($mysqli, "SELECT COUNT(id) as n FROM procesar_formularios WHERE DATE(fecha_creacion)='$hoy'", 'hoy');
$sumar($mysqli, "SELECT COUNT(id) as n FROM cotizaciones WHERE DATE(fecha_creacion)='$hoy'", 'hoy');

// ─── NORMALIZACIÓN ─────────────────────────────────────────────
function normalizarLead(array $row): array {
    if (empty($row['datos_adicionales'])) return $row;

    $ad = json_decode($row['datos_adicionales'], true);
    if (!is_array($ad)) return $row;

    // Desenvolver wrappers
    foreach (['form_data', 'data', 'lead'] as $wrapper) {
        if (isset($ad[$wrapper]) && is_array($ad[$wrapper]) && count($ad) === 1) {
            $ad = $ad[$wrapper];
            break;
        }
    }

    // Mapa flat
    $flatMap = [
        'name' => 'nombre', 'nombre' => 'nombre', 'age' => 'edad',
        'income' => 'renta', 'renta' => 'renta', 'comuna' => 'region',
        'region' => 'region', 'telefono' => 'celular', 'phone' => 'celular',
        'email' => 'correo', 'isapre_actual' => 'prevision_interes',
        'prevision' => 'prevision_interes', 'prevision_actual' => 'prevision_interes',
        'preferencia_plan' => 'plan_interes', 'plan_interes' => 'plan_interes',
        'rut' => 'rut', 'cargas' => 'cargas', 'cargas_familiares' => 'cargas',
        'genero' => 'genero', 'query_type' => 'tipo_formulario',
        'tipo_formulario' => 'tipo_formulario', 'tipo_plan' => 'tipo_plan',
        'pais' => 'pais', 'pais_residencia' => 'pais',
        'origen_lead' => 'origen_lead', 'tracking_session_id' => 'tracking_session_id',
    ];
    foreach ($flatMap as $jsonKey => $rowKey) {
        if (isset($ad[$jsonKey]) && empty($row[$rowKey])) {
            $row[$rowKey] = $ad[$jsonKey];
        }
    }

    // Intereses
    if (!empty($ad['interests']) && is_array($ad['interests'])) {
        $row['intereses'] = implode(', ', $ad['interests']);
    } elseif (!empty($ad['interests']) && is_string($ad['interests'])) {
        $row['intereses'] = $ad['interests'];
    } elseif (!empty($ad['needs']) && is_array($ad['needs'])) {
        $row['intereses'] = implode(', ', $ad['needs']);
    } elseif (!empty($ad['needs']) && is_string($ad['needs'])) {
        $row['intereses'] = $ad['needs'];
    } elseif (!empty($ad['intereses']) && is_string($ad['intereses'])) {
        $row['intereses'] = $ad['intereses'];
    }

    // Mensaje
    if (!empty($ad['message'])) {
        $row['mensaje'] = $ad['message'];
    }

    // Preexistencias
    foreach (['preexistence', 'preexistence_fam', 'preexistencia'] as $k) {
        if (isset($ad[$k])) {
            $row['preexistencias'] = ($ad[$k] === 'si') ? 'Sí' : 'No';
            $txt_key = $k . '_text';
            if (!empty($ad[$txt_key])) {
                $row['preexistencias'] .= ': ' . $ad[$txt_key];
            }
            break;
        }
    }

    // Edades cargas
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

    // Estructura nested personal.* / salud.*
    if (isset($ad['personal']) && is_array($ad['personal'])) {
        $p = $ad['personal'];
        if (empty($row['nombre']))  $row['nombre']  = $p['nombre'] ?? $p['name'] ?? null;
        if (empty($row['correo']))  $row['correo']  = $p['email'] ?? $p['correo'] ?? null;
        if (empty($row['celular'])) $row['celular'] = $p['telefono'] ?? $p['phone'] ?? null;
        if (empty($row['rut']))     $row['rut']     = $p['rut'] ?? null;
        if (empty($row['edad']))    $row['edad']    = $p['edad'] ?? null;
        if (empty($row['region']))  $row['region']  = $p['region'] ?? $p['comuna'] ?? null;
        if (empty($row['renta']))   $row['renta']   = $p['renta'] ?? null;
        if (empty($row['cargas']))  $row['cargas']  = $p['cargas'] ?? null;
        if (empty($row['genero']))  $row['genero']  = $p['genero'] ?? null;
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
        if (empty($row['tipo_plan']))         $row['tipo_plan']         = $s['tipo_plan'] ?? null;
        $row['salud_genero'] = $s['genero'] ?? null;
    }

    // Resumen de mensaje
    if (!empty($row['mensaje'])) {
        $primerLinea = strtok($row['mensaje'], "\n");
        $row['mensaje_resumen'] = mb_substr(strip_tags($primerLinea), 0, 100);
    }

    return $row;
}

// ─── HELPERS ───────────────────────────────────────────────────
function selectEstado(string $estado, string $uid): string {
    global $ESTADOS;

    // Si el estado no coincide con ninguno conocido, mostrar badge neutro
    $efectivo = isset($ESTADOS[$estado]) ? $estado : null;
    if ($efectivo === null) {
        return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap bg-gray-100 text-gray-700">• ' . htmlspecialchars($estado ?: 'Nuevo') . '</span>';
    }
    $c = $ESTADOS[$efectivo];

    $html = '<select class="estado-select cursor-pointer appearance-none text-center rounded-full text-xs font-semibold py-1 px-2 border-0 outline-none transition ' . $c['bg'] . ' ' . $c['fg'] . '" data-uid="' . htmlspecialchars($uid) . '" onclick="event.stopPropagation()" onchange="cambiarEstado(this)" style="min-width:140px">';
    foreach ($ESTADOS as $val => $style) {
        $sel = $val === $efectivo ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($val) . '"' . $sel . '>' . $style['icon'] . ' ' . $style['label'] . '</option>';
    }
    $html .= '</select>';
    return $html;
}

function badgeEstado(string $estado): string {
    global $ESTADOS;
    $c = $ESTADOS[$estado] ?? ['bg' => 'bg-gray-100', 'fg' => 'text-gray-700', 'icon' => '•'];
    return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap ' . $c['bg'] . ' ' . $c['fg'] . '">' . $c['icon'] . ' ' . htmlspecialchars($estado) . '</span>';
}

function fmtCLP($val): string {
    if (!$val) return '-';
    $n = (int)$val;
    if ($n <= 0) return '-';
    return '$' . number_format($n, 0, ',', '.');
}

function fmtDate($val): string {
    if (!$val || $val === '0000-00-00 00:00:00') return '-';
    if (substr($val, -8) === '00:00:00') return substr($val, 0, 10);
    return $val;
}

function fmtTel($val): string {
    $val = preg_replace('/\D/', '', (string)$val);
    if (strlen($val) === 9) {
        return substr($val, 0, 1) . ' ' . substr($val, 1, 4) . ' ' . substr($val, 5);
    }
    return $val ?: '-';
}

// ─── CONSTRUIR QUERY PRINCIPAL ─────────────────────────────────
// Unificamos ambas tablas con UNION ALL. procesar_formularios → prefijo "pf_",
// cotizaciones → prefijo "ct_". Luego normalizamos en PHP.

// Subquery A: procesar_formularios
$sqlPF = "SELECT 
    CONCAT('pf_', id) as uid, id, nombre, correo, celular, 
    estado, notas, 
    first_contact_date, second_contact_date, sale_closing_date,
    fecha_creacion, datos_adicionales,
    'formulario' as origen, '' as rut
FROM procesar_formularios";

// Subquery B: cotizaciones (columnas reales: cargas, renta, tipo_plan, first_contact_date, etc.)
$sqlCT = "SELECT 
    CONCAT('ct_', id) as uid, id, nombre, email as correo, telefono as celular,
    COALESCE(estado, 'Nuevo') as estado, '' as notas,
    first_contact_date, second_contact_date, sale_closing_date,
    fecha_creacion,
    JSON_OBJECT(
        'rut', IFNULL(rut,''), 'region', IFNULL(region,''), 
        'genero', IFNULL(genero,''), 'edad', IFNULL(edad,''),
        'cargas', IFNULL(cargas, ''), 
        'prevision', IFNULL(prevision,''),
        'renta', IFNULL(renta, ''), 
        'tipo_plan', IFNULL(tipo_plan,'')
    ) as datos_adicionales,
    'cotizacion' as origen, rut
FROM cotizaciones";

// WHERE dinámico
$whereSQL = '';
$whereParts = [];
if ($search !== '') {
    $s = $mysqli->real_escape_string($search);
    $whereParts[] = "(nombre LIKE '%$s%' OR correo LIKE '%$s%' OR celular LIKE '%$s%')";
}
if ($estado !== '') {
    $e = $mysqli->real_escape_string($estado);
    $whereParts[] = "estado = '$e'";
}
if (!empty($whereParts)) {
    $whereSQL = ' WHERE ' . implode(' AND ', $whereParts);
}

// Elegir fuente(s)
if ($origen === 'formulario') {
    $sqlFull = $sqlPF . $whereSQL;
} elseif ($origen === 'cotizacion') {
    $sqlFull = $sqlCT . $whereSQL;
} else {
    // Ambas: aplicar WHERE dentro de cada subquery antes del UNION
    $sqlFull = "SELECT * FROM (($sqlPF $whereSQL) UNION ALL ($sqlCT $whereSQL)) AS unificada";
}

// Contar total para paginación
$countSql = "SELECT COUNT(*) as n FROM ($sqlFull) AS c";
$totalLeads = 0;
$cr = $mysqli->query($countSql);
if ($cr) {
    $totalLeads = (int)$cr->fetch_assoc()['n'];
}
$totalPages = max(1, ceil($totalLeads / $perPage));

// Query paginada
$sqlFull .= " ORDER BY fecha_creacion DESC LIMIT $perPage OFFSET $offset";

$leads = [];
$res = $mysqli->query($sqlFull);
if (!$res) {
    $queryError = $mysqli->error;
} else {
    while ($row = $res->fetch_assoc()) {
        $leads[] = normalizarLead($row);
    }
}

$mysqli->close();

// ─── HELPERS PARA ESTADO EN SELECT ─────────────────────────────
function selected(string $current, string $value): string {
    return $current === $value ? 'selected' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Leads — PlanSaludFácil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eff6ff', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af' }
                    }
                }
            }
        }
    </script>
    <style>
        .detail-row { display: none !important; }
        .detail-row.open { display: table-row !important; }
        .expand-icon { transition: transform 0.2s; display: inline-block; }
        .expand-icon.open { transform: rotate(90deg); }

        /* ─── Mobile card layout (below md breakpoint) ─── */
        @media (max-width: 767px) {
            .leads-table thead { display: none; }
            .leads-table,
            .leads-table tbody,
            .leads-table tr,
            .leads-table td { display: block; }

            .leads-table tr {
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                margin-bottom: 0.75rem;
                padding: 0.5rem 0;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            }
            .leads-table tr.border-b { border-bottom: 1px solid #e2e8f0; }
            .leads-table tr.detail-row {
                border-top: none;
                border-radius: 0 0 0.75rem 0.75rem;
                margin-top: -0.75rem;
                border-color: #e2e8f0;
            }
            .leads-table tr.detail-row.open { display: block !important; }

            .leads-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.4rem 0.75rem;
                border: none;
                text-align: right;
                font-size: 0.8125rem;
            }
            .leads-table td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.6875rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                white-space: nowrap;
                margin-right: 0.75rem;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">

<!-- HEADER -->
<header class="bg-gradient-to-r from-brand-800 to-blue-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">📊 Dashboard de Leads</h1>
            <p class="text-blue-200 text-sm mt-0.5">PlanSaludFácil · Vista unificada</p>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="bg-white/10 px-3 py-1.5 rounded-lg"><?= $totalLeads ?> leads totales</span>
            <span class="bg-white/10 px-3 py-1.5 rounded-lg"><?= $metricas['hoy'] ?> hoy</span>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

<!-- MÉTRICAS -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
        <div class="text-3xl font-extrabold text-brand-700"><?= $metricas['total'] ?></div>
        <div class="text-xs uppercase tracking-wide text-slate-500 mt-1">Total Leads</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
        <div class="text-3xl font-extrabold text-blue-600"><?= $metricas['nuevos'] ?></div>
        <div class="text-xs uppercase tracking-wide text-slate-500 mt-1">🆕 Nuevos</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
        <div class="text-3xl font-extrabold text-amber-600"><?= $metricas['contactados'] ?></div>
        <div class="text-xs uppercase tracking-wide text-slate-500 mt-1">📞 Contactados</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
        <div class="text-3xl font-extrabold text-emerald-600"><?= $metricas['cerrados'] ?></div>
        <div class="text-xs uppercase tracking-wide text-slate-500 mt-1">✅ Cerrados</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 text-center">
        <div class="text-3xl font-extrabold text-violet-600"><?= $metricas['hoy'] ?></div>
        <div class="text-xs uppercase tracking-wide text-slate-500 mt-1">📅 Hoy</div>
    </div>
</div>

<!-- BARRA DE BÚSQUEDA + FILTROS -->
<form method="get" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
    <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar por nombre, email o teléfono…" class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-600 focus:border-brand-600 outline-none">
        </div>
        <select name="estado" class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:ring-2 focus:ring-brand-600 outline-none">
            <option value="">Todos los estados</option>
            <?php foreach ($ESTADOS as $ek => $ev): ?>
            <option value="<?= htmlspecialchars($ek) ?>" <?= selected($estado, $ek) ?>><?= $ev['icon'] ?> <?= htmlspecialchars($ev['label']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="origen" class="border border-slate-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:ring-2 focus:ring-brand-600 outline-none">
            <option value="">Todos los orígenes</option>
            <option value="formulario" <?= selected($origen, 'formulario') ?>>📝 Formulario web</option>
            <option value="cotizacion" <?= selected($origen, 'cotizacion') ?>>🔢 Cotización</option>
        </select>
        <button type="submit" class="bg-brand-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-brand-800 transition">Filtrar</button>
        <?php if ($search || $estado || $origen): ?>
            <a href="leads.php" class="text-sm text-slate-500 hover:text-slate-700 py-2.5">✕ Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<!-- TABLA DE LEADS -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm leads-table">
            <thead>
                <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-slate-200">
                    <th class="px-3 py-3 w-10"></th>
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Nombre</th>
                    <th class="px-3 py-3">Contacto</th>
                    <th class="px-3 py-3">Origen</th>
                    <th class="px-3 py-3 hidden md:table-cell">Edad</th>
                    <th class="px-3 py-3 hidden md:table-cell">Renta</th>
                    <th class="px-3 py-3 hidden lg:table-cell">Previsión</th>
                    <th class="px-3 py-3 hidden lg:table-cell">Intereses</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3 hidden xl:table-cell">Notas</th>
                    <th class="px-3 py-3 hidden xl:table-cell">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="12" class="px-6 py-12 text-center text-slate-400">
                        <?php if (!empty($queryError)): ?>
                        <div class="text-4xl mb-2">⚠️</div>
                        <p class="text-lg font-medium text-red-600">Error en la consulta SQL</p>
                        <p class="text-sm text-red-500 font-mono mt-2"><?= htmlspecialchars($queryError) ?></p>
                        <?php else: ?>
                        <div class="text-4xl mb-2">📭</div>
                        <p class="text-lg font-medium">No se encontraron leads</p>
                        <p class="text-sm"><?= $search || $estado || $origen ? 'Probá ajustando los filtros.' : 'Aún no hay datos en la base de datos.' ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($leads as $idx => $l): 
                    $rowId = 'lead-' . $idx;
                    $origenBadge = ($l['origen'] === 'cotizacion') 
                        ? '<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">Cotización</span>'
                        : '<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Formulario</span>';
                    $tipoForm = !empty($l['tipo_formulario']) 
                        ? str_replace(['cotizacion_', '_'], ['', ' '], $l['tipo_formulario']) 
                        : ($l['origen'] === 'cotizacion' ? 'cotización' : 'contacto');
                ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer" onclick="toggleDetail('<?= $rowId ?>')">
                    <td class="px-3 py-3" data-label="">
                        <span class="expand-icon text-slate-400" id="<?= $rowId ?>-icon">▶</span>
                    </td>
                    <td class="px-3 py-3 font-mono text-xs text-slate-500" data-label="ID">#<?= htmlspecialchars($l['uid']) ?></td>
                    <td class="px-3 py-3 font-medium text-slate-800 max-w-[160px] truncate" title="<?= htmlspecialchars($l['nombre'] ?? '') ?>" data-label="Nombre">
                        <?= htmlspecialchars($l['nombre'] ?: '-') ?>
                    </td>
                    <td class="px-3 py-3" data-label="Contacto">
                        <div class="text-slate-700 text-xs"><?= htmlspecialchars($l['correo'] ?: '-') ?></div>
                        <div class="text-slate-400 text-xs"><?= fmtTel($l['celular'] ?? '') ?></div>
                    </td>
                    <td class="px-3 py-3" data-label="Origen"><?= $origenBadge ?></td>
                    <td class="px-3 py-3 hidden md:table-cell text-xs" data-label="Edad"><?= htmlspecialchars($l['edad'] ?? '-') ?></td>
                    <td class="px-3 py-3 hidden md:table-cell text-xs font-mono" data-label="Renta"><?= fmtCLP($l['renta'] ?? null) ?></td>
                    <td class="px-3 py-3 hidden lg:table-cell text-xs max-w-[120px] truncate" data-label="Previsión"><?= htmlspecialchars($l['prevision_interes'] ?? '-') ?></td>
                    <td class="px-3 py-3 hidden lg:table-cell text-xs max-w-[140px] truncate" title="<?= htmlspecialchars($l['intereses'] ?? '') ?>" data-label="Intereses">
                        <?= !empty($l['intereses']) ? htmlspecialchars(mb_substr($l['intereses'], 0, 60)) : '-' ?>
                    </td>
                    <td class="px-3 py-3" data-label="Estado" onclick="event.stopPropagation()"><?= selectEstado($l['estado'], $l['uid']) ?></td>
                    <td class="px-3 py-3 hidden xl:table-cell text-xs text-slate-500 max-w-[150px] truncate" data-label="Notas">
                        <?= !empty($l['notas']) ? htmlspecialchars(mb_substr($l['notas'], 0, 60)) : '<span class="text-slate-300">-</span>' ?>
                    </td>
                    <td class="px-3 py-3 hidden xl:table-cell text-xs text-slate-400 whitespace-nowrap" data-label="Fecha">
                        <?= fmtDate($l['fecha_creacion']) ?>
                    </td>
                </tr>
                <!-- DETALLE EXPANDIBLE -->
                <tr class="detail-row bg-slate-50/70" id="<?= $rowId ?>-detail">
                    <td colspan="12" class="px-6 py-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <!-- Columna 1: Datos personales -->
                            <div>
                                <h4 class="font-semibold text-slate-700 mb-2 text-xs uppercase tracking-wide">Datos Personales</h4>
                                <dl class="space-y-1 text-xs">
                                    <div class="flex justify-between"><dt class="text-slate-400">Nombre</dt><dd class="text-slate-700 font-medium"><?= htmlspecialchars($l['nombre'] ?: '-') ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">Email</dt><dd class="text-slate-700"><?= htmlspecialchars($l['correo'] ?: '-') ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">Teléfono</dt><dd class="text-slate-700"><?= fmtTel($l['celular'] ?? '') ?></dd></div>
                                    <?php if (!empty($l['rut'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">RUT</dt><dd class="text-slate-700"><?= htmlspecialchars($l['rut']) ?></dd></div>
                                    <?php endif; ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Edad</dt><dd class="text-slate-700"><?= htmlspecialchars($l['edad'] ?? '-') ?></dd></div>
                                    <?php if (!empty($l['genero']) || !empty($l['salud_genero'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Género</dt><dd class="text-slate-700"><?= htmlspecialchars($l['genero'] ?? $l['salud_genero'] ?? '-') ?></dd></div>
                                    <?php endif; ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Región</dt><dd class="text-slate-700"><?= htmlspecialchars($l['region'] ?? '-') ?></dd></div>
                                    <?php if (!empty($l['pais'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">País</dt><dd class="text-slate-700"><?= htmlspecialchars($l['pais']) ?></dd></div>
                                    <?php endif; ?>
                                </dl>
                            </div>

                            <!-- Columna 2: Datos de salud / plan -->
                            <div>
                                <h4 class="font-semibold text-slate-700 mb-2 text-xs uppercase tracking-wide">Salud y Plan</h4>
                                <dl class="space-y-1 text-xs">
                                    <div class="flex justify-between"><dt class="text-slate-400">Renta</dt><dd class="text-slate-700 font-mono"><?= fmtCLP($l['renta'] ?? null) ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">Cargas</dt><dd class="text-slate-700"><?= htmlspecialchars($l['cargas'] ?? '-') ?></dd></div>
                                    <?php if (!empty($l['edades_cargas'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Edades cargas</dt><dd class="text-slate-700"><?= htmlspecialchars($l['edades_cargas']) ?></dd></div>
                                    <?php endif; ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Previsión</dt><dd class="text-slate-700"><?= htmlspecialchars($l['prevision_interes'] ?? '-') ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">Plan interés</dt><dd class="text-slate-700"><?= htmlspecialchars($l['plan_interes'] ?? '-') ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">Tipo plan</dt><dd class="text-slate-700"><?= htmlspecialchars($l['tipo_plan'] ?? '-') ?></dd></div>
                                    <?php if (!empty($l['complementar_renta'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Compl. renta</dt><dd class="text-slate-700"><?= htmlspecialchars($l['complementar_renta']) ?></dd></div>
                                    <?php endif; ?>
                                    <?php if (isset($l['preexistencias'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Preexistencias</dt><dd class="text-slate-700"><?= htmlspecialchars($l['preexistencias']) ?></dd></div>
                                    <?php endif; ?>
                                </dl>
                            </div>

                            <!-- Columna 3: Intereses y mensaje -->
                            <div>
                                <h4 class="font-semibold text-slate-700 mb-2 text-xs uppercase tracking-wide">Intereses</h4>
                                <p class="text-xs text-slate-600 mb-3"><?= !empty($l['intereses']) ? htmlspecialchars($l['intereses']) : '<span class="text-slate-300">No especificados</span>' ?></p>
                                <?php if (!empty($l['mensaje'])): ?>
                                <h4 class="font-semibold text-slate-700 mb-1 text-xs uppercase tracking-wide">Mensaje</h4>
                                <p class="text-xs text-slate-600 bg-white rounded p-2 border border-slate-100 max-h-24 overflow-y-auto whitespace-pre-wrap"><?= htmlspecialchars($l['mensaje']) ?></p>
                                <?php endif; ?>
                                <?php if (!empty($l['borrador_respuesta_ia'])): ?>
                                <h4 class="font-semibold text-slate-700 mb-1 mt-2 text-xs uppercase tracking-wide">🤖 Borrador IA</h4>
                                <p class="text-xs text-slate-500 bg-purple-50 rounded p-2 border border-purple-100 max-h-24 overflow-y-auto"><?= htmlspecialchars(mb_substr($l['borrador_respuesta_ia'], 0, 200)) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Columna 4: Timeline y tracking -->
                            <div>
                                <h4 class="font-semibold text-slate-700 mb-2 text-xs uppercase tracking-wide">Timeline</h4>
                                <dl class="space-y-1 text-xs">
                                    <div class="flex justify-between"><dt class="text-slate-400">Creado</dt><dd class="text-slate-700"><?= fmtDate($l['fecha_creacion']) ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">1er contacto</dt><dd class="text-slate-700"><?= fmtDate($l['first_contact_date'] ?? null) ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">2do contacto</dt><dd class="text-slate-700"><?= fmtDate($l['second_contact_date'] ?? null) ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">Cierre</dt><dd class="text-slate-700"><?= fmtDate($l['sale_closing_date'] ?? null) ?></dd></div>
                                    <div class="flex justify-between"><dt class="text-slate-400">Estado</dt><dd onclick="event.stopPropagation()"><?= selectEstado($l['estado'], $l['uid']) ?></dd></div>
                                    <?php if (!empty($l['origen_lead'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Origen lead</dt><dd class="text-slate-700"><?= htmlspecialchars($l['origen_lead']) ?></dd></div>
                                    <?php endif; ?>
                                    <?php if (!empty($l['tracking_session_id'])): ?>
                                    <div class="flex justify-between"><dt class="text-slate-400">Session ID</dt><dd class="text-slate-700 font-mono text-[10px]"><?= htmlspecialchars(substr($l['tracking_session_id'], 0, 16)) ?>…</dd></div>
                                    <?php endif; ?>
                                    <?php if (!empty($l['unsubscribed'])): ?>
                                    <div class="mt-2"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">⚠️ Unsubscribed</span></div>
                                    <?php endif; ?>
                                </dl>
                                <?php if (!empty($l['notas'])): ?>
                                <h4 class="font-semibold text-slate-700 mb-1 mt-3 text-xs uppercase tracking-wide">📝 Notas</h4>
                                <p class="text-xs text-slate-600 bg-amber-50 rounded p-2 border border-amber-100 max-h-20 overflow-y-auto"><?= htmlspecialchars($l['notas']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PAGINACIÓN -->
    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between px-6 py-3 border-t border-slate-200 bg-slate-50/50">
        <span class="text-xs text-slate-500">
            Mostrando <?= (($page-1)*$perPage)+1 ?>–<?= min($page*$perPage, $totalLeads) ?> de <?= $totalLeads ?>
        </span>
        <div class="flex gap-1">
            <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 bg-white hover:bg-slate-100 transition">← Anterior</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++):
            ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="px-3 py-1.5 text-xs font-medium rounded-lg border <?= $i === $page ? 'bg-brand-700 text-white border-brand-700' : 'border-slate-300 bg-white hover:bg-slate-100' ?> transition"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 bg-white hover:bg-slate-100 transition">Siguiente →</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</div><!-- /container -->

<script>
function toggleDetail(rowId) {
    const detail = document.getElementById(rowId + '-detail');
    const icon = document.getElementById(rowId + '-icon');
    detail.classList.toggle('open');
    icon.classList.toggle('open');
}

function cambiarEstado(select) {
    const uid = select.dataset.uid;
    const nuevoEstado = select.value;
    const originalEstado = select.querySelector('option[selected]')?.value || select.dataset.prev;

    // Guardar estado anterior para posible rollback
    if (!select.dataset.prev) select.dataset.prev = originalEstado || select.value;

    // Feedback visual inmediato
    const prevColor = select.className;
    select.className = select.className.replace(/bg-\w+-\d+/g, 'bg-gray-100');
    select.classList.add('opacity-60');
    select.disabled = true;

    const formData = new FormData();
    formData.append('action', 'update_estado');
    formData.append('uid', uid);
    formData.append('estado', nuevoEstado);

    fetch('leads.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            // Actualizar clases según nuevo estado
            select.className = select.className.replace(/bg-\w+-\d+/g, '');
            const styles = {
                'Nuevo':                   { bg: 'bg-blue-100',     fg: 'text-blue-800' },
                'Contactado':              { bg: 'bg-amber-100',    fg: 'text-amber-800' },
                'En Negociación':          { bg: 'bg-indigo-100',   fg: 'text-indigo-800' },
                'Cotización Enviada':      { bg: 'bg-sky-100',      fg: 'text-sky-800' },
                'En Espera de Respuesta':  { bg: 'bg-yellow-100',   fg: 'text-yellow-800' },
                'No Desea Asesoría':       { bg: 'bg-rose-100',     fg: 'text-rose-800' },
                'Oferta No Corresponde':   { bg: 'bg-orange-100',   fg: 'text-orange-800' },
                'Cerrado':                 { bg: 'bg-emerald-100',  fg: 'text-emerald-800' },
                'Contacto Cerrado':        { bg: 'bg-slate-100',    fg: 'text-slate-800' },
                'No Responde':             { bg: 'bg-gray-100',     fg: 'text-gray-800' },
                'Facturado':               { bg: 'bg-lime-100',     fg: 'text-lime-800' }
            };
            const s = styles[nuevoEstado] || { bg: 'bg-gray-100', fg: 'text-gray-700' };
            select.className = select.className + ' ' + s.bg + ' ' + s.fg;
            select.dataset.prev = nuevoEstado;
            // Actualizar todos los selects del mismo lead en la página
            document.querySelectorAll('.estado-select[data-uid="' + uid + '"]').forEach(el => {
                el.value = nuevoEstado;
                el.className = select.className;
                el.dataset.prev = nuevoEstado;
            });
        } else {
            console.error('Error al actualizar estado:', data.error);
            select.value = select.dataset.prev;
            select.className = prevColor;
        }
    })
    .catch(err => {
        console.error('Error de red:', err);
        select.value = select.dataset.prev;
        select.className = prevColor;
    })
    .finally(() => {
        select.classList.remove('opacity-60');
        select.disabled = false;
    });
}
</script>

</body>
</html>
