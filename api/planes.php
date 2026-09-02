<?php
/**
 * api/planes.php — API de datos de planes (fuente de verdad en ESTE servidor)
 * ============================================================================
 * Mantiene la data (planes_isapre.csv + data_isapres.php + revision_IA) en
 * el servidor del proveedor. Los sitios cliente consumen esta API y NUNCA
 * reciben el dataset completo: cada acción devuelve resultados filtrados o
 * computados, con límite de filas.
 *
 * Auth: ?key=API_SECRET_KEY  o  header X-Api-Key
 *
 * Acciones:
 *   ?action=search     filtra planes (isapre, min_uf, max_uf, min_hosp,
 *                      min_amb, region, q, limit<=100, offset)
 *   ?action=detalle    un plan por código
 *   ?action=cobertura  cobertura de una isapre
 *   ?action=isapres    resumen curado por isapre (data_isapres.php)
 *   ?action=destacados 6 planes destacados (uno por isapre)
 *   ?action=precios    min/max/mediana de precios por isapre+edad+cargas
 *   ?action=cotizar    motor de cotización completo (param: lead=JSON)
 *   ?action=ping       estado y conteos
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../core/cotizador_engine.php'; // trae isapre_pricing + load_catalog + motor_cotizar
require_once __DIR__ . '/../core/data_isapres.php';     // $ISAPRES

// ── Auth ──
$key = $_GET['key'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? '');
if (!is_string($key)) $key = '';
if (!hash_equals(API_SECRET_KEY, $key)) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'no autorizado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Api-Key');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function respond($data) {
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}
function fail($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_GET['action'] ?? 'search';

switch ($action) {

    // ── Ping / estado ──
    case 'ping': {
        $planes = load_catalog();
        respond([
            'servicio'         => 'planes-api',
            'version'          => '1.0',
            'total_planes'     => count($planes),
            'total_isapres'    => count(array_unique(array_column($planes, 'isapre'))),
            'server_time'      => date('c'),
        ]);
    }

    // ── Búsqueda filtrada (nunca el dataset completo) ──
    case 'search': {
        $isapre   = isset($_GET['isapre']) ? _normalize_isapre($_GET['isapre']) : null;
        $min_uf   = isset($_GET['min_uf']) ? (float)$_GET['min_uf'] : null;
        $max_uf   = isset($_GET['max_uf']) ? (float)$_GET['max_uf'] : null;
        $min_hosp = isset($_GET['min_hosp']) ? (int)$_GET['min_hosp'] : null;
        $min_amb  = isset($_GET['min_amb']) ? (int)$_GET['min_amb'] : null;
        $region   = isset($_GET['region']) ? trim($_GET['region']) : null;
        $q        = isset($_GET['q']) ? mb_strtolower(trim($_GET['q']), 'UTF-8') : null;
        $limit    = min((int)($_GET['limit'] ?? 50), 100);
        $offset   = max((int)($_GET['offset'] ?? 0), 0);

        $planes = load_catalog();
        $result = [];
        foreach ($planes as $p) {
            if ($isapre && $p['isapre'] !== $isapre) continue;
            if ($min_uf !== null && $p['uf'] < $min_uf) continue;
            if ($max_uf !== null && $p['uf'] > $max_uf) continue;
            if ($min_hosp !== null && $p['cobertura_hosp_pct'] < $min_hosp) continue;
            if ($min_amb !== null && $p['cobertura_amb_pct'] < $min_amb) continue;
            if ($region && $p['region'] !== 'todas' && $p['region'] !== $region) continue;
            if ($q && mb_strpos(mb_strtolower($p['nombre'], 'UTF-8'), $q) === false
                   && mb_strpos(mb_strtolower($p['isapre'], 'UTF-8'), $q) === false
                   && mb_strpos(mb_strtolower($p['codigo'], 'UTF-8'), $q) === false) continue;
            $result[] = $p;
        }

        $total = count($result);
        respond([
            'total'  => $total,
            'limit'  => $limit,
            'offset' => $offset,
            'planes' => array_slice($result, $offset, $limit),
        ]);
    }

    // ── Un plan por código ──
    case 'detalle': {
        $codigo = trim($_GET['codigo'] ?? '');
        if ($codigo === '') fail('falta param codigo');
        foreach (load_catalog() as $p) {
            if ($p['codigo'] === $codigo) respond($p);
        }
        fail('plan no encontrado', 404);
    }

    // ── Cobertura por isapre (desde revision_IA) ──
    case 'cobertura': {
        $isapre = _normalize_isapre($_GET['isapre'] ?? '');
        if ($isapre === '') fail('falta param isapre');
        $path = __DIR__ . '/../adjuntos/revision_IA_Planes_isapre.csv';
        $found = null;
        if (($h = fopen($path, 'r')) !== false) {
            fgetcsv($h, 0, ',', '"', ''); fgetcsv($h, 0, ',', '"', '');
            while (($r = fgetcsv($h, 0, ',', '"', '')) !== false) {
                if (count($r) < 13) continue;
                $n = _normalize_isapre($r[0]);
                if (!$n) continue;
                if ($n === $isapre) {
                    $found = [
                        'isapre' => $n,
                        'hp' => $r[2] ?? '-', 'cp' => $r[3] ?? '-', 'tp' => $r[4] ?? '-',
                        'hl' => $r[6] ?? '-', 'cl' => $r[7] ?? '-', 'tl' => $r[8] ?? '-',
                        'urg' => $r[10] ?? '-', 'red' => $r[12] ?? '',
                    ];
                    break;
                }
            }
            fclose($h);
        }
        if ($found === null) fail('cobertura no encontrada', 404);
        respond($found);
    }

    // ── Resumen curado por isapre (data_isapres.php) ──
    case 'isapres': {
        respond(array_values($ISAPRES));
    }

    // ── 6 planes destacados (misma selección que el carrusel original) ──
    case 'destacados': {
        $cache = [];
        foreach (load_catalog() as $p) {
            if ($p['uf'] < 0.5) continue;
            $cache[] = $p;
        }
        $plans = [];
        if (!empty($cache)) {
            $seen = function ($plans) { return array_column($plans, 'isapre'); };

            usort($cache, function ($a, $b) { return ($b['cobertura_hosp_pct'] + $b['cobertura_amb_pct']) - ($a['cobertura_hosp_pct'] + $a['cobertura_amb_pct']); });
            foreach ($cache as $p) { if (!in_array($p['isapre'], $seen($plans))) { $plans[] = $p; break; } }

            usort($cache, function ($a, $b) { return $a['uf'] - $b['uf']; });
            foreach ($cache as $p) { if ($p['cobertura_hosp_pct'] >= 60 && $p['cobertura_amb_pct'] >= 50 && !in_array($p['isapre'], $seen($plans))) { $plans[] = $p; break; } }

            usort($cache, function ($a, $b) { return ($b['cobertura_hosp_pct'] + $b['cobertura_amb_pct'] - $b['uf'] * 3) - ($a['cobertura_hosp_pct'] + $a['cobertura_amb_pct'] - $a['uf'] * 3); });
            foreach ($cache as $p) { if (!in_array($p['isapre'], $seen($plans))) { $plans[] = $p; break; } }

            usort($cache, function ($a, $b) { return $b['prestadores'] - $a['prestadores']; });
            foreach ($cache as $p) { if (!in_array($p['isapre'], $seen($plans))) { $plans[] = $p; break; } }

            usort($cache, function ($a, $b) { return $b['cobertura_hosp_pct'] - $a['cobertura_hosp_pct']; });
            foreach ($cache as $p) { if (!in_array($p['isapre'], $seen($plans))) { $plans[] = $p; break; } }

            usort($cache, function ($a, $b) { return ($b['cobertura_amb_pct'] - $b['uf'] * 5) - ($a['cobertura_amb_pct'] - $a['uf'] * 5); });
            foreach ($cache as $p) { if (!in_array($p['isapre'], $seen($plans))) { $plans[] = $p; break; } }
        }
        respond(array_values(array_slice($plans, 0, 6)));
    }

    // ── Rango de precios por isapre+edad+cargas ──
    case 'precios': {
        $isapre = _normalize_isapre($_GET['isapre'] ?? '');
        $edad   = (int)($_GET['edad'] ?? 30);
        $cargas = (int)($_GET['cargas'] ?? 0);
        if ($isapre === '') fail('falta param isapre');
        $precios = [];
        foreach (load_catalog() as $p) {
            if ($p['isapre'] !== $isapre) continue;
            $pr = calcular_precio($p['uf'], $edad, $cargas, null, null, null, $p['isapre']);
            $precios[] = $pr['total_clp'];
        }
        if (!$precios) fail('sin precios para esa isapre', 404);
        sort($precios);
        $n = count($precios);
        respond([
            'isapre'  => $isapre,
            'edad'    => $edad,
            'cargas'  => $cargas,
            'min'     => $precios[0],
            'max'     => $precios[$n - 1],
            'mediana' => $precios[(int)($n / 2)],
            'planes'  => $n,
        ]);
    }

    // ── Motor de cotización completo ──
    case 'cotizar': {
        $lead_json = $_GET['lead'] ?? ($_POST['lead'] ?? '');
        if ($lead_json === '' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $lead_json = file_get_contents('php://input');
        }
        $lead = json_decode($lead_json, true);
        if (!is_array($lead) || empty($lead['renta'])) {
            fail('falta param lead (JSON con campo renta)');
        }
        $r = motor_cotizar($lead);
        if (isset($r['error'])) fail($r['error']);
        respond($r);
    }

    // ── Stats agregadas por isapre (para página de compañía) ──
    case 'isapre_stats': {
        $isapre = _normalize_isapre($_GET['isapre'] ?? '');
        if ($isapre === '') fail('falta param isapre');
        $all = [];
        foreach (load_catalog() as $p) {
            if ($p['uf'] < 0.5) continue;
            $all[] = $p;
        }
        $plans = array_values(array_filter($all, function ($p) use ($isapre) {
            return $p['isapre'] === $isapre;
        }));
        if (empty($plans)) fail('isapre sin datos', 404);

        $count = count($plans);
        $avg_h = round(array_sum(array_column($plans, 'cobertura_hosp_pct')) / $count);
        $avg_a = round(array_sum(array_column($plans, 'cobertura_amb_pct')) / $count);
        $avg_uf = round(array_sum(array_column($plans, 'uf')) / $count, 2);
        $avg_prest = round(array_sum(array_column($plans, 'prestadores')) / $count);

        $all_count = count($all);
        $global = [
            'avg_hosp'  => round(array_sum(array_column($all, 'cobertura_hosp_pct')) / $all_count),
            'avg_amb'   => round(array_sum(array_column($all, 'cobertura_amb_pct')) / $all_count),
            'avg_uf'    => round(array_sum(array_column($all, 'uf')) / $all_count, 2),
            'avg_prest' => round(array_sum(array_column($all, 'prestadores')) / $all_count),
        ];

        usort($plans, fn($a, $b) => $a['uf'] <=> $b['uf']);
        $cheapest = $plans[0];
        usort($plans, fn($a, $b) => ($b['cobertura_hosp_pct'] + $b['cobertura_amb_pct']) <=> ($a['cobertura_hosp_pct'] + $a['cobertura_amb_pct']));
        $best_cov = $plans[0];
        usort($plans, fn($a, $b) => $b['prestadores'] <=> $a['prestadores']);
        $best_net = $plans[0];
        usort($plans, fn($a, $b) => ($b['prestadores'] + $b['cobertura_hosp_pct'] + $b['cobertura_amb_pct'] - $a['uf']) <=> ($a['prestadores'] + $a['cobertura_hosp_pct'] + $a['cobertura_amb_pct'] - $a['uf']));
        $balanced = $plans[0];

        respond([
            'isapre'    => $isapre,
            'count'     => $count,
            'avg_hosp'  => $avg_h,
            'avg_amb'   => $avg_a,
            'avg_uf'    => $avg_uf,
            'avg_prest' => $avg_prest,
            'global'    => $global,
            'top'       => ['cheapest' => $cheapest, 'best_cov' => $best_cov, 'best_net' => $best_net, 'balanced' => $balanced],
        ]);
    }

    default:
        fail('accion desconocida', 404);
}
