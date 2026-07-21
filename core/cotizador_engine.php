<?php
/**
 * Motor de Cotización Real — PlanSaludFácil (PHP)
 * ===============================================
 * Evalúa 2,231 planes reales contra el perfil del lead.
 * Versión PHP — cero dependencias, compatible con shared hosting.
 * Replica exactamente core/cotizador_engine.py
 */

require_once __DIR__ . '/isapre_pricing.php';

// ─── CONSTANTES ────────────────────────────────────────

define('PLANES_CSV', __DIR__ . '/../adjuntos/planes_isapre.csv');
define('COBERTURAS_CSV', __DIR__ . '/../adjuntos/revision_IA_Planes_isapre.csv');

define('INTERES_MAP', [
    'salud mental'               => 'consulta_pref_max',
    'kinesiología y deporte'     => 'consulta_pref_max',
    'kinesiologia y deporte'     => 'consulta_pref_max',
    'telemedicina'               => 'consulta_libre_max',
    'atención ambulatoria'       => 'consulta_pref_max',
    'atencion ambulatoria'       => 'consulta_pref_max',
    'hospitalización'            => 'hospitalaria_pref_max',
    'hospitalizacion'            => 'hospitalaria_pref_max',
    'maternidad'                 => 'hospitalaria_pref_max',
    'dental'                     => 'consulta_pref_max',
    'farmacia'                   => 'consulta_pref_max',
]);

function _parse_num($s) {
    $s = str_replace(',', '.', trim($s));
    return is_numeric($s) ? (float)$s : 0.0;
}

// ─── 1. CARGAR CATÁLOGO ───────────────────────────────

function load_catalog() {
    $planes = [];
    $handle = fopen(PLANES_CSV, 'r');
    if (!$handle) return $planes;
    
    $headers = fgetcsv($handle, 0, ',', '"', '');
    while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
        if (count($row) < 10) continue;
        $nombre = trim($row[2] ?? '');
        $uf = trim($row[3] ?? '');
        if (empty($nombre) || empty($uf)) continue;
        
        $planes[] = [
            'isapre'             => trim($row[0] ?? ''),
            'codigo'             => trim($row[1] ?? ''),
            'nombre'             => $nombre,
            'uf'                 => _parse_num($uf),
            'tope_anual_uf'      => _parse_num($row[4] ?? '0'),
            'prestadores'        => (int)($row[5] ?? 0),
            'cobertura_hosp_pct' => (int)($row[6] ?? 0),
            'cobertura_amb_pct'  => (int)($row[7] ?? 0),
            'url'                => trim($row[8] ?? ''),
            'region'             => trim($row[9] ?? 'todas'),
        ];
    }
    fclose($handle);
    return $planes;
}

// ─── 2. CARGAR COBERTURAS ─────────────────────────────

function _parse_cov_pct($s) {
    $s = trim(str_replace(' a ', ' ', $s));
    if (empty($s) || strtoupper($s) === 'N/A') return [0, 0];
    preg_match_all('/\d+/', $s, $m);
    $nums = array_map(function($n) { return min((int)$n, 100); }, $m[0]);
    if (count($nums) === 0) return [0, 0];
    if (count($nums) === 1) return [$nums[0], $nums[0]];
    return [min($nums), max($nums)];
}

function _parse_tope($s) {
    $s = trim($s);
    if (empty($s) || strtoupper($s) === 'N/A' || stripos($s, 'SIN TOPE') !== false) return [9999, 9999];
    $s = str_replace('.', '', $s);
    preg_match_all('/[\d.]+/', $s, $m);
    $nums = array_map('floatval', $m[0]);
    if (count($nums) === 0) return [0, 0];
    if (count($nums) === 1) return [$nums[0], $nums[0]];
    return [$nums[0], $nums[count($nums)-1]];
}

function _normalize_isapre($name) {
    $name = trim($name);
    $map = [
        'cruz blanca'   => 'Cruz Blanca',
        'nueva masvida' => 'Nueva Masvida',
        'nueva más vida' => 'Nueva Masvida',
        'banmédica'     => 'Banmédica',
        'banmedica'     => 'Banmédica',
        'vida tres'     => 'Vida Tres',
    ];
    $lower = strtolower($name);
    return isset($map[$lower]) ? $map[$lower] : $name;
}

function load_coberturas() {
    $coberturas = [];
    $handle = fopen(COBERTURAS_CSV, 'r');
    if (!$handle) return [$coberturas, []];
    
    fgetcsv($handle, 0, ",", chr(34), ""); fgetcsv($handle, 0, ",", chr(34), ""); // skip 2 header rows
    
    while (($row = fgetcsv($handle, 0, ",", chr(34), "")) !== false) {
        if (count($row) < 7) continue;
        $isapre_raw = trim($row[0] ?? '');
        if (empty($isapre_raw)) continue;
        
        $isapre = _normalize_isapre($isapre_raw);
        
        $hosp_pref = _parse_cov_pct($row[2] ?? '');
        $cons_pref = _parse_cov_pct($row[3] ?? '');
        $tope_pref = _parse_tope($row[4] ?? '');
        $hosp_libre = _parse_cov_pct($row[6] ?? '');
        $cons_libre = _parse_cov_pct($row[7] ?? '');
        $tope_libre = _parse_tope($row[8] ?? '');
        $urgencia = _parse_tope(str_replace(',', '.', $row[10] ?? ''));
        
        if (!isset($coberturas[$isapre])) {
            $coberturas[$isapre] = [
                'hospitalaria_pref_min' => $hosp_pref[0], 'hospitalaria_pref_max' => $hosp_pref[1],
                'consulta_pref_min'     => $cons_pref[0], 'consulta_pref_max'     => $cons_pref[1],
                'tope_pref_min'         => $tope_pref[0], 'tope_pref_max'         => $tope_pref[1],
                'hospitalaria_libre_min'=> $hosp_libre[0],'hospitalaria_libre_max'=> $hosp_libre[1],
                'consulta_libre_min'    => $cons_libre[0],'consulta_libre_max'    => $cons_libre[1],
                'urgencia_min'          => $urgencia[0],  'urgencia_max'          => $urgencia[1],
            ];
        }
    }
    fclose($handle);
    
    $defaults = _compute_fallback($coberturas);
    return [$coberturas, $defaults];
}

function _compute_fallback($coberturas) {
    $d = ['hospitalaria_pref' => 70, 'consulta_pref' => 60, 'hospitalaria_libre' => 70, 'consulta_libre' => 50];
    $fields = [
        ['hospitalaria_pref_max', 'hospitalaria_pref_min', 'hospitalaria_pref'],
        ['consulta_pref_max', 'consulta_pref_min', 'consulta_pref'],
        ['hospitalaria_libre_max', 'hospitalaria_libre_min', 'hospitalaria_libre'],
        ['consulta_libre_max', 'consulta_libre_min', 'consulta_libre'],
    ];
    foreach ($fields as $f) {
        $mids = [];
        foreach ($coberturas as $c) {
            $mx = $c[$f[0]] ?? 0;
            $mn = $c[$f[1]] ?? 0;
            if ($mx > 0 && $mx <= 100 && $mn > 0) $mids[] = ($mn + $mx) / 2;
            elseif ($mx > 0 && $mx <= 100) $mids[] = $mx;
        }
        if (count($mids) > 0) $d[$f[2]] = array_sum($mids) / count($mids);
    }
    return $d;
}

// ─── 3. SCORING ────────────────────────────────────────

function score_plan($plan, $lead, $cobertura, $defaults) {
    $score = 0.0;
    $reasons = [];
    
    // Si no hay cobertura externa, usar los datos del plan mismo
    if ($cobertura === null) {
        $h = $plan['cobertura_hosp_pct'] ?? 0;
        $a = $plan['cobertura_amb_pct'] ?? 0;
        if ($h > 0 || $a > 0) {
            $cobertura = [
                'hospitalaria_pref_max' => $h, 'hospitalaria_pref_min' => $h,
                'consulta_pref_max'     => $a, 'consulta_pref_min'     => $a,
                'consulta_libre_max'    => $a, 'consulta_libre_min'    => $a,
            ];
        }
    }
    $cov = ($cobertura !== null) ? $cobertura : $defaults;
    $renta = (int)($lead['renta'] ?? 500000);
    $edad = (int)($lead['edad'] ?? 30);
    $cargas = (int)($lead['cargas'] ?? 0);
    $uf_value = (int)($lead['uf_value'] ?? UF_DEFAULT);
    $cotizacion_legal = $renta * 0.07;
    
    // ── 1. Precio (35 pts) ──
    $edad_cargas_arr = $lead['edad_cargas'] ?? null;
    $pricing = calcular_precio($plan['uf'], $edad, $cargas, $edad_cargas_arr, null, $uf_value, $plan['isapre']);
    $precio_plan = $pricing['total_clp'];
    $factor = $pricing['factor_titular'];
    
    $ratio = $cotizacion_legal > 0 ? $precio_plan / $cotizacion_legal : 999;
    $extra = (int)($precio_plan - $cotizacion_legal);
    
    if ($ratio <= 1.0) {
        $pts = 35;
        $reasons[] = "Tu 7% legal ($" . number_format($cotizacion_legal, 0, ',', '.') . ") cubre el plan completo";
    } elseif ($ratio <= 1.10) {
        $pts = 28;
        $reasons[] = "Leve cotización adicional: +$" . number_format($extra, 0, ',', '.') . "/mes";
    } elseif ($ratio <= 1.25) {
        $pts = 20;
        $reasons[] = "Requiere $" . number_format($extra, 0, ',', '.') . " adicional mensual";
    } elseif ($ratio <= 1.5) {
        $pts = 12;
        $reasons[] = "Requiere $" . number_format($extra, 0, ',', '.') . " adicional mensual";
    } elseif ($ratio <= 2.0) {
        $pts = 6;
        $reasons[] = "Alto costo adicional: +$" . number_format($extra, 0, ',', '.') . "/mes sobre tu 7%";
    } else {
        $pts = 2;
        $reasons[] = "Muy por encima de tu 7%: +$" . number_format($extra, 0, ',', '.') . "/mes adicional";
    }
    
    if ($edad > 35) {
        $reasons[] = "Factor etario ×" . number_format($factor, 1) . " por edad ({$edad} años)";
    }
    if ($cargas > 0) {
        $reasons[] = "Incluye {$cargas} carga(s): +{$pricing['costo_cargas_uf']} UF total (plan × factor edad + GES {$pricing['ges_prima']} UF c/u)";
    }
    $score += $pts;
    
    // ── 2. Cobertura intereses (30 pts) ──
    if (!empty($lead['intereses'])) {
        $total_cov = 0;
        $interes_count = 0;
        foreach ($lead['intereses'] as $interes) {
            $key = INTERES_MAP[strtolower($interes)] ?? null;
            if ($key === null) continue;
            
            $mid_cov = null;
            if (isset($cov[$key]) && $cov[$key] > 0) {
                $base_key = str_replace('_max', '', $key);
                $min_key = $base_key . '_min';
                if (isset($cov[$min_key]) && ($cov[$min_key] ?? 0) > 0) {
                    $mid_cov = ($cov[$min_key] + $cov[$key]) / 2;
                } else {
                    $mid_cov = $cov[$key];
                }
            }
            
            if ($mid_cov === null || $mid_cov == 0) {
                if (strpos($key, '_libre') !== false) {
                    $alt_key = str_replace('_libre', '_pref', $key);
                    if (isset($cov[$alt_key]) && ($cov[$alt_key] ?? 0) > 0) {
                        $base_key = str_replace('_max', '', $alt_key);
                        $min_key = $base_key . '_min';
                        if (isset($cov[$min_key]) && ($cov[$min_key] ?? 0) > 0) {
                            $mid_cov = ($cov[$min_key] + $cov[$alt_key]) / 2;
                        } else {
                            $mid_cov = $cov[$alt_key];
                        }
                    }
                }
            }
            
            if ($mid_cov === null || $mid_cov == 0) {
                $fallback_key = str_replace('_max', '', str_replace('_libre', '', $key));
                $mid_cov = $defaults[$fallback_key] ?? 50;
            }
            
            $total_cov += $mid_cov;
            $interes_count++;
        }
        
        if ($interes_count > 0) {
            $avg_cov = $total_cov / $interes_count;
            $coverage_pts = ($avg_cov / 100) * 30;
            if ($ratio > 1.5) $coverage_pts *= (1.5 / $ratio);
            $score += $coverage_pts;
            
            if ($avg_cov >= 85) $reasons[] = "Excelente cobertura para tus intereses (~" . round($avg_cov) . "%)";
            elseif ($avg_cov >= 70) $reasons[] = "Buena cobertura para tus intereses (~" . round($avg_cov) . "%)";
            else $reasons[] = "Cobertura estándar para tus intereses (~" . round($avg_cov) . "%)";
        }
    }
    
    // ── 3. Red de prestadores (15 pts) ──
    $prest = $plan['prestadores'];
    if ($prest >= 30) {
        $score += 15;
        $reasons[] = "Red amplia: {$prest} prestadores en convenio";
    } elseif ($prest >= 15) {
        $score += 10;
        $reasons[] = "Buena red: {$prest} prestadores";
    } elseif ($prest >= 5) {
        $score += 5;
    } else {
        $score += 2;
    }
    
    // ── 4. Sin cargas + plan económico (5 pts) ──
    if ($cargas == 0 && $plan['uf'] < 3.5) {
        $score += 5;
        $reasons[] = "Plan individual optimizado (sin costo por cargas)";
    }
    
    // ── 5. ISAPRE conocida (3 pts) ──
    if (in_array($plan['isapre'], ['Banmédica', 'Cruz Blanca', 'Consalud'])) {
        $score += 3;
    }
    
    return [round(min($score, 100), 1), $reasons];
}

function rank_plans($planes, $lead, $coberturas, $defaults, $top_n = 5) {
    $resultados = [];
    foreach ($planes as $plan) {
        $cov = $coberturas[$plan['isapre']] ?? null;
        list($s, $reasons) = score_plan($plan, $lead, $cov, $defaults);
        $resultados[] = [$plan, $s, $reasons];
    }
    
    usort($resultados, function($a, $b) { return $b[1] <=> $a[1]; });
    
    // Diversify: top plan per ISAPRE
    $seen = [];
    $diversified = [];
    foreach ($resultados as $item) {
        $isapre = $item[0]['isapre'];
        if (!in_array($isapre, $seen)) {
            $diversified[] = $item;
            $seen[] = $isapre;
        }
        if (count($diversified) >= $top_n) break;
    }
    
    if (count($diversified) < $top_n) {
        foreach ($resultados as $item) {
            if (!in_array($item, $diversified, true)) {
                $diversified[] = $item;
            }
            if (count($diversified) >= $top_n) break;
        }
    }
    
    return array_slice($diversified, 0, $top_n);
}

// ─── 4. MAIN ──────────────────────────────────────────

// ─── MAPEO COMUNA → MACRO-ZONA ──────────────────────
function comuna_to_region($comuna) {
    $comuna = trim(mb_strtolower($comuna, 'UTF-8'));
    $map = [
        // Norte (Arica a Coquimbo)
        'arica','iquique','alto hospicio','antofagasta','calama','tocopilla','mejillones',
        'copiapo','vallenar','caldera','la serena','coquimbo','ovalle','illapel','vicuña',
        // Centro (Valparaíso a Maule)
        'valparaiso','viña del mar','quilpue','villa alemana','san antonio','los andes',
        'quillota','la calera','limache','concon','santiago','providencia','las condes',
        'ñuñoa','la florida','maipu','puente alto','san bernardo','la reina','vitacura',
        'lo barnechea','peñalolen','macul','san miguel','la cisterna','el bosque',
        'recoleta','independencia','estacion central','quilicura','huechuraba','renca',
        'cerro navia','lo prado','pudahuel','conchali','pedro aguirre cerda','san joaquin',
        'la granja','san ramon','lo espejo','quinta normal','rancagua','machali',
        'talca','curico','linares','constitucion','san fernando','santa cruz','pichilemu',
        // Sur (Ñuble a Magallanes)
        'chillan','concepcion','talcahuano','san pedro de la paz','coronel','los angeles',
        'tome','penco','chiguayante','lebu','arauco','temuco','padre las casas','villarríca',
        'pucon','valdivia','la union','osorno','puerto montt','puerto varas','castro',
        'ancud','quellon','coyhaique','punta arenas',
    ];
    if (in_array($comuna, $map)) {
        // Check which zone
        $norte_limit = array_search('vicuña', $map);
        $sur_start = array_search('chillan', $map);
        $idx = array_search($comuna, $map);
        if ($idx <= $norte_limit) return 'norte';
        if ($idx >= $sur_start) return 'sur';
        return 'centro';
    }
    return 'centro'; // default: Santiago
}

function motor_cotizar($lead_json) {
    $lead = is_string($lead_json) ? json_decode($lead_json, true) : $lead_json;
    if (!$lead || empty($lead['renta'])) return ['error' => 'Datos del lead inválidos'];
    
    $planes = load_catalog();
    list($coberturas, $defaults) = load_coberturas();
    
    // Filtrar por región si se proporcionó comuna
    $region_lead = null;
    if (!empty($lead['comuna'])) {
        $region_lead = comuna_to_region($lead['comuna']);
    }
    if ($region_lead && $region_lead !== 'centro') {
        $planes_before = count($planes);
        $planes = array_filter($planes, function($p) use ($region_lead) {
            $r = $p['region'] ?? 'todas';
            return $r === 'todas' || $r === $region_lead;
        });
        $planes = array_values($planes); // re-index
        error_log("motor_cotizar: filtro región $region_lead → " . count($planes) . " de $planes_before planes");
    }
    
    $top = rank_plans($planes, $lead, $coberturas, $defaults, 5);
    
    $result = [
        'lead'                      => $lead,
        'cotizacion_legal_7pct'     => (int)($lead['renta'] * 0.07),
        'uf_value'                  => (int)($lead['uf_value'] ?? UF_DEFAULT),
        'total_planes_evaluados'    => count($planes),
        'recomendaciones'           => [],
    ];
    
    foreach ($top as $item) {
        list($plan, $score, $reasons) = $item;
        $edad_cargas_arr = $lead['edad_cargas'] ?? null;
        $pricing = calcular_precio($plan['uf'], (int)($lead['edad'] ?? 30), 
                                   (int)($lead['cargas'] ?? 0), $edad_cargas_arr, null, 
                                   (int)($lead['uf_value'] ?? UF_DEFAULT), $plan['isapre']);
        $result['recomendaciones'][] = [
            'isapre'        => $plan['isapre'],
            'nombre'        => $plan['nombre'],
            'codigo'        => $plan['codigo'],
            'uf'            => $plan['uf'],
            'precio_clp'    => $pricing['total_clp'],
            'prestadores'   => $plan['prestadores'],
            'tope_anual_uf' => $plan['tope_anual_uf'],
            'url'           => $plan['url'],
            'score'         => $score,
            'razones'       => $reasons,
        ];
    }
    
    return $result;
}

// ─── CLI test ──────────────────────────────────────────
if (basename(__FILE__) === 'cotizador_engine.php' && php_sapi_name() === 'cli') {
    $lead = [
        "nombre"    => "Kathya Andrade",
        "edad"      => 27,
        "renta"     => 1300000,
        "cargas"    => 0,
        "uf_value"  => 38500,
        "intereses" => ["Salud Mental", "Kinesiología y Deporte", "Telemedicina", "Excedentes"],
    ];
    
    $result = motor_cotizar($lead);
    
    echo "Catálogo: {$result['total_planes_evaluados']} planes\n";
    echo "Lead: {$lead['nombre']}, {$lead['edad']}a, $" . number_format($lead['renta'], 0, ',', '.') . ", 7%=$" . number_format($result['cotizacion_legal_7pct'], 0, ',', '.') . "\n\n";
    
    foreach ($result['recomendaciones'] as $i => $rec) {
        $icon = $i === 0 ? '🏆' : '  ';
        $dentro = $rec['precio_clp'] <= $result['cotizacion_legal_7pct'] ? '✅' : '+';
        echo "{$icon} #" . ($i+1) . " [{$rec['score']}/100] {$rec['isapre']}\n";
        echo "     Plan: {$rec['nombre']}\n";
        echo "     UF: {$rec['uf']} | CLP: $" . number_format($rec['precio_clp'], 0, ',', '.') . "/mes | Prest: {$rec['prestadores']}\n";
        foreach (array_slice($rec['razones'], 0, 3) as $r) {
            echo "     → {$r}\n";
        }
        echo "\n";
    }
}

