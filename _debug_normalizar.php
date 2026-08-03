<?php
header('Content-Type: text/plain; charset=utf-8');

// Same DB connection as dashboard
$mysqli = new mysqli('localhost', 'plansalu_blogger', 'Blog.2025!#', 'plansalu_blog');
if ($mysqli->connect_errno) die("DB error: " . $mysqli->connect_error);

// Copy of normalizarLead EXACTLY as in backend_dashboard_mock.php
function normalizarLead($row) {
    if (empty($row['datos_adicionales'])) return $row;

    $ad = json_decode($row['datos_adicionales'], true);
    if (!is_array($ad)) return $row;

    foreach (['form_data', 'data', 'lead'] as $wrapper) {
        if (isset($ad[$wrapper]) && is_array($ad[$wrapper]) && count($ad) === 1) {
            $ad = $ad[$wrapper];
            break;
        }
    }

    $flatMap = [
        'name'              => 'nombre',
        'nombre'            => 'nombre',
        'age'               => 'edad',
        'income'            => 'renta',
        'renta'             => 'renta',
        'comuna'            => 'region',
        'region'            => 'region',
        'telefono'          => 'celular',
        'phone'             => 'celular',
        'email'             => 'correo',
        'isapre_actual'     => 'prevision_interes',
        'prevision'         => 'prevision_interes',
        'prevision_actual'  => 'prevision_interes',
        'preferencia_plan'  => 'plan_interes',
        'plan_interes'      => 'plan_interes',
        'rut'               => 'rut',
        'cargas'            => 'cargas',
        'cargas_familiares' => 'cargas',
        'genero'            => 'genero',
        'query_type'        => 'tipo_formulario',
        'tipo_formulario'   => 'tipo_formulario',
        'tipo_plan'         => 'tipo_plan',
        'pais'              => 'pais',
        'pais_residencia'   => 'pais',
        'origen_lead'       => 'origen_lead',
        'tracking_session_id' => 'tracking_session_id',
    ];

    foreach ($flatMap as $jsonKey => $rowKey) {
        if (isset($ad[$jsonKey]) && empty($row[$rowKey])) {
            $row[$rowKey] = $ad[$jsonKey];
        }
    }

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

    if (!empty($ad['message'])) {
        $row['mensaje'] = $ad['message'];
    }

    $pre_key = null;
    foreach (['preexistence', 'preexistence_fam', 'preexistencia'] as $k) {
        if (isset($ad[$k])) { $pre_key = $k; break; }
    }
    if ($pre_key) {
        $row['preexistencias'] = ($ad[$pre_key] === 'si') ? 'Si' : 'No';
        $txt_key = $pre_key . '_text';
        if (!empty($ad[$txt_key])) {
            $row['preexistencias'] .= ': ' . $ad[$txt_key];
        }
    }

    $edades_cargas = [];
    for ($i = 1; $i <= 6; $i++) {
        $key = "carga_edad_$i";
        if (!empty($ad[$key])) $edades_cargas[] = $ad[$key];
    }
    if (!empty($edades_cargas)) {
        $row['edades_cargas'] = implode(', ', $edades_cargas);
    }

    if (!empty($ad['complementar_renta'])) {
        $row['complementar_renta'] = 'Si';
    }

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
    }

    if (!empty($row['mensaje'])) {
        $primerLinea = strtok($row['mensaje'], "\n");
        $row['mensaje_resumen'] = mb_substr(strip_tags($primerLinea), 0, 100);
    }

    return $row;
}

// Fetch ID 103
$res = $mysqli->query("SELECT id, nombre, correo, celular, pais, estado, notas, borrador_respuesta_ia, first_contact_date, second_contact_date, sale_closing_date, fecha_creacion, unsubscribed, datos_adicionales FROM procesar_formularios WHERE id = 103");
$row = $res->fetch_assoc();

echo "=== RAW datos_adicionales ===\n";
echo substr($row['datos_adicionales'], 0, 200) . "\n\n";

echo "=== json_last_error after decode ===\n";
$ad = json_decode($row['datos_adicionales'], true);
echo "json_last_error: " . json_last_error() . " (" . json_last_error_msg() . ")\n";
echo "is_array: " . (is_array($ad) ? 'yes' : 'no') . "\n";
echo "count: " . count($ad) . "\n\n";

echo "=== Key presence in decoded JSON ===\n";
foreach (['age','income','comuna','isapre_actual','cargas','query_type','interests','needs','message','tipo_plan','origen_lead'] as $k) {
    echo "  $k: " . (isset($ad[$k]) ? "YES (" . (is_array($ad[$k]) ? 'array' : var_export($ad[$k], true)) . ")" : "NO") . "\n";
}

echo "\n=== AFTER normalizarLead ===\n";
$normalized = normalizarLead($row);
$fields = ['tipo_formulario','tipo_plan','origen_lead','prevision_interes','region','edad','renta','cargas','intereses','mensaje'];
foreach ($fields as $f) {
    $val = $normalized[$f] ?? null;
    echo "  $f: " . var_export($val, true) . (empty($val) ? " (EMPTY!)" : "") . "\n";
}

echo "\n=== PHP version ===\n";
echo phpversion() . "\n";
