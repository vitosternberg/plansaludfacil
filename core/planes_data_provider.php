<?php
/**
 * core/planes_data_provider.php
 * Capa única de acceso a datos de planes.
 * =======================================
 * Modo 'local'  (default): lee CSVs/arrays locales (instalación proveedor).
 * Modo 'remote' : consume la API del proveedor; la data NO vive en este host.
 *
 * Configuración (definible en config.php):
 *   PLANES_DATA_SOURCE    'local' | 'remote'
 *   PLANES_API_URL        URL de la API del proveedor
 *   PLANES_API_KEY        key de acceso a la API
 *   PLANES_API_CACHE_TTL  segundos de caché (0 = sin caché persistente)
 */

if (!defined('PLANES_DATA_SOURCE'))    define('PLANES_DATA_SOURCE', 'local');
if (!defined('PLANES_API_URL'))        define('PLANES_API_URL', '');
if (!defined('PLANES_API_KEY'))        define('PLANES_API_KEY', defined('API_SECRET_KEY') ? API_SECRET_KEY : '');
if (!defined('PLANES_API_CACHE_TTL'))  define('PLANES_API_CACHE_TTL', 60);

require_once __DIR__ . '/cotizador_engine.php'; // load_catalog, calcular_precio, motor_cotizar, _normalize_isapre

// $ISAPRES solo existe en la instalación proveedor (no en clientes remotos)
if (file_exists(__DIR__ . '/data_isapres.php')) {
    require_once __DIR__ . '/data_isapres.php';
}

function pd_is_remote() {
    return PLANES_DATA_SOURCE === 'remote';
}

/**
 * Petición a la API del proveedor (solo modo remote). Devuelve el array
 * decodificado `{ok, data|error}`. Con caché en archivo para GET.
 */
function pd_api_request($action, $params = [], $cache = true) {
    $url = rtrim(PLANES_API_URL, '/') . '?key=' . urlencode(PLANES_API_KEY)
         . '&action=' . urlencode($action);
    if ($params) {
        $url .= '&' . http_build_query($params);
    }

    $cache_file = null;
    $ttl = (int) PLANES_API_CACHE_TTL;
    if ($cache && $ttl > 0) {
        $cache_file = sys_get_temp_dir() . '/pd_cache_' . md5($url) . '.json';
        if (is_file($cache_file) && (time() - filemtime($cache_file)) < $ttl) {
            $raw = @file_get_contents($cache_file);
            if ($raw !== false) {
                $d = json_decode($raw, true);
                if (is_array($d)) return $d;
            }
        }
    }

    $resp = false;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
    } else {
        $resp = @file_get_contents($url);
    }

    if ($resp === false) {
        return ['ok' => false, 'error' => 'no se pudo contactar la API de datos'];
    }

    $data = json_decode($resp, true);
    if (!is_array($data)) {
        return ['ok' => false, 'error' => 'respuesta inválida de la API'];
    }

    if ($cache_file !== null && !empty($data['ok'])) {
        @file_put_contents($cache_file, $resp);
    }
    return $data;
}

// ─────────────────────────────────────────────────────────────
//  DATOS
// ─────────────────────────────────────────────────────────────

function pd_get_plan($codigo) {
    if (pd_is_remote()) {
        $d = pd_api_request('detalle', ['codigo' => $codigo]);
        return (!empty($d['ok']) && is_array($d['data'])) ? $d['data'] : null;
    }
    foreach (load_catalog() as $p) {
        if ($p['codigo'] === $codigo) return $p;
    }
    return null;
}

function pd_get_destacados() {
    if (pd_is_remote()) {
        $d = pd_api_request('destacados');
        return (!empty($d['ok']) && is_array($d['data'])) ? $d['data'] : [];
    }

    $cache = [];
    foreach (load_catalog() as $p) {
        if ($p['uf'] < 0.5) continue;
        $cache[] = $p;
    }
    if (empty($cache)) return [];

    $plans = [];
    $unique = function ($plans) {
        $seen = array_column($plans, 'isapre');
        return $seen;
    };

    usort($cache, function ($a, $b) { return ($b['cobertura_hosp_pct'] + $b['cobertura_amb_pct']) - ($a['cobertura_hosp_pct'] + $a['cobertura_amb_pct']); });
    foreach ($cache as $p) { if (!in_array($p['isapre'], $unique($plans))) { $plans[] = $p; break; } }

    usort($cache, function ($a, $b) { return $a['uf'] - $b['uf']; });
    foreach ($cache as $p) { if ($p['cobertura_hosp_pct'] >= 60 && $p['cobertura_amb_pct'] >= 50 && !in_array($p['isapre'], $unique($plans))) { $plans[] = $p; break; } }

    usort($cache, function ($a, $b) { return ($b['cobertura_hosp_pct'] + $b['cobertura_amb_pct'] - $b['uf'] * 3) - ($a['cobertura_hosp_pct'] + $a['cobertura_amb_pct'] - $a['uf'] * 3); });
    foreach ($cache as $p) { if (!in_array($p['isapre'], $unique($plans))) { $plans[] = $p; break; } }

    usort($cache, function ($a, $b) { return $b['prestadores'] - $a['prestadores']; });
    foreach ($cache as $p) { if (!in_array($p['isapre'], $unique($plans))) { $plans[] = $p; break; } }

    usort($cache, function ($a, $b) { return $b['cobertura_hosp_pct'] - $a['cobertura_hosp_pct']; });
    foreach ($cache as $p) { if (!in_array($p['isapre'], $unique($plans))) { $plans[] = $p; break; } }

    usort($cache, function ($a, $b) { return ($b['cobertura_amb_pct'] - $b['uf'] * 5) - ($a['cobertura_amb_pct'] - $a['uf'] * 5); });
    foreach ($cache as $p) { if (!in_array($p['isapre'], $unique($plans))) { $plans[] = $p; break; } }

    return count($plans) < 4 ? [] : $plans;
}

function pd_get_isapre_cobertura($isapre_name) {
    if (pd_is_remote()) {
        $d = pd_api_request('cobertura', ['isapre' => $isapre_name]);
        return (!empty($d['ok']) && is_array($d['data'])) ? $d['data'] : null;
    }

    $path = __DIR__ . '/../adjuntos/revision_IA_Planes_isapre.csv';
    if (!is_file($path)) return null;
    if (($h = fopen($path, 'r')) === false) return null;
    fgetcsv($h, 0, ',', '"', ''); fgetcsv($h, 0, ',', '"', '');

    $target = _normalize_isapre($isapre_name);
    $found = null;
    while (($r = fgetcsv($h, 0, ',', '"', '')) !== false) {
        if (count($r) < 13) continue;
        $n = _normalize_isapre($r[0]);
        if (!$n) continue;
        if ($n === $target) {
            $found = ['hp' => $r[2] ?? '-', 'cp' => $r[3] ?? '-', 'tp' => $r[4] ?? '-',
                      'hl' => $r[6] ?? '-', 'cl' => $r[7] ?? '-', 'tl' => $r[8] ?? '-',
                      'urg' => $r[10] ?? '-', 'red' => $r[12] ?? ''];
            break;
        }
    }
    fclose($h);
    return $found;
}

function pd_get_isapre_precios($isapre, $edad, $cargas = 0) {
    if (pd_is_remote()) {
        $d = pd_api_request('precios', ['isapre' => $isapre, 'edad' => $edad, 'cargas' => $cargas]);
        return (!empty($d['ok']) && is_array($d['data'])) ? $d['data'] : null;
    }

    $precios = [];
    foreach (load_catalog() as $p) {
        if ($p['isapre'] !== $isapre) continue;
        $pr = calcular_precio($p['uf'], $edad, $cargas, null, null, null, $p['isapre']);
        $precios[] = $pr['total_clp'];
    }
    if (!$precios) return null;
    sort($precios);
    $n = count($precios);
    return ['min' => $precios[0], 'max' => $precios[$n - 1], 'mediana' => $precios[(int)($n / 2)], 'planes' => $n];
}

function pd_get_isapres() {
    if (pd_is_remote()) {
        $d = pd_api_request('isapres');
        return (!empty($d['ok']) && is_array($d['data'])) ? $d['data'] : [];
    }
    global $ISAPRES;
    return (isset($ISAPRES) && is_array($ISAPRES)) ? array_values($ISAPRES) : [];
}

function pd_search($filters = []) {
    if (pd_is_remote()) {
        $d = pd_api_request('search', $filters);
        if (empty($d['ok']) || !is_array($d['data'])) return ['total' => 0, 'planes' => []];
        return ['total' => (int)($d['data']['total'] ?? 0), 'planes' => ($d['data']['planes'] ?? [])];
    }

    $isapre   = isset($filters['isapre']) ? _normalize_isapre($filters['isapre']) : null;
    $min_uf   = isset($filters['min_uf']) ? (float)$filters['min_uf'] : null;
    $max_uf   = isset($filters['max_uf']) ? (float)$filters['max_uf'] : null;
    $min_hosp = isset($filters['min_hosp']) ? (int)$filters['min_hosp'] : null;
    $min_amb  = isset($filters['min_amb']) ? (int)$filters['min_amb'] : null;
    $region   = isset($filters['region']) ? trim($filters['region']) : null;
    $q        = isset($filters['q']) ? mb_strtolower(trim($filters['q']), 'UTF-8') : null;
    $limit    = isset($filters['limit']) ? min((int)$filters['limit'], 100) : 50;
    $offset   = isset($filters['offset']) ? max((int)$filters['offset'], 0) : 0;

    $result = [];
    foreach (load_catalog() as $p) {
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

    return ['total' => count($result), 'planes' => array_slice($result, $offset, $limit)];
}

function pd_get_isapre_stats($isapre) {
    if (pd_is_remote()) {
        $d = pd_api_request('isapre_stats', ['isapre' => $isapre]);
        return (!empty($d['ok']) && is_array($d['data'])) ? $d['data'] : null;
    }

    $isapre = _normalize_isapre($isapre);
    $all = [];
    foreach (load_catalog() as $p) {
        if ($p['uf'] < 0.5) continue;
        $all[] = $p;
    }
    $plans = array_values(array_filter($all, function ($p) use ($isapre) {
        return $p['isapre'] === $isapre;
    }));
    if (empty($plans)) return null;

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

    return [
        'isapre'    => $isapre,
        'count'     => $count,
        'avg_hosp'  => $avg_h,
        'avg_amb'   => $avg_a,
        'avg_uf'    => $avg_uf,
        'avg_prest' => $avg_prest,
        'global'    => $global,
        'top'       => ['cheapest' => $cheapest, 'best_cov' => $best_cov, 'best_net' => $best_net, 'balanced' => $balanced],
    ];
}

function pd_cotizar($lead) {
    if (pd_is_remote()) {
        $d = pd_api_request('cotizar', ['lead' => json_encode($lead)], false);
        if (!empty($d['ok']) && is_array($d['data'])) return $d['data'];
        return ['error' => isset($d['error']) ? $d['error'] : 'error en la cotización'];
    }
    return motor_cotizar($lead);
}
