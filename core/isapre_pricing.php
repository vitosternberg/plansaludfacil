<?php
/**
 * ISAPRE Pricing Calculator — PlanSaludFácil (PHP)
 * =================================================
 * Módulo independiente para calcular el precio de un plan ISAPRE.
 * Basado en Circular IF/N° 343 (tabla única de factores).
 * 
 * Versión PHP — cero dependencias, compatible con shared hosting.
 * Replica exactamente core/isapre_pricing.py
 */

// ─── TABLA ÚNICA DE FACTORES (Circular IF/N° 343) ───
define('FACTOR_TABLE', [
    // [edad_min, edad_max) => [factor_cotizante, factor_carga]
    [0,  20, 0.6, 0.6],
    [20, 25, 0.9, 0.7],
    [25, 35, 1.0, 0.7],
    [35, 45, 1.3, 0.9],
    [45, 55, 1.4, 1.0],
    [55, 65, 2.0, 1.4],
    [65, 999, 2.4, 2.2],
]);

// ─── GES PRIMA por ISAPRE ───
define('GES_PRIMAS', [
    'Esencial'      => 0.91,
    'Cruz Blanca'   => 0.74,
    'Nueva Masvida' => 1.02,
    'Banmédica'     => 1.10,
    'Vida Tres'     => 1.12,
    'Consalud'      => 1.25,
    'Colmena'       => 1.30,
]);
define('GES_PRIMA_DEFAULT', 1.10);
define('UF_DEFAULT', 38500);

/**
 * Retorna el factor etario oficial para una edad y tipo.
 * @param int $edad
 * @param string $tipo 'cotizante' o 'carga'
 * @return float
 */
function get_factor($edad, $tipo = 'cotizante') {
    $idx = ($tipo === 'cotizante') ? 2 : 3;
    foreach (FACTOR_TABLE as $row) {
        if ($edad >= $row[0] && $edad < $row[1]) {
            return $row[$idx];
        }
    }
    // Fallback: oldest bracket
    $last = FACTOR_TABLE[count(FACTOR_TABLE) - 1];
    return $last[$idx];
}

/**
 * Retorna la prima GES para una ISAPRE específica.
 * @param string|null $isapre
 * @return float
 */
function get_ges_prima($isapre = null) {
    if ($isapre && isset(GES_PRIMAS[$isapre])) {
        return GES_PRIMAS[$isapre];
    }
    return GES_PRIMA_DEFAULT;
}

/**
 * Costo del titular en UF: plan_uf × factor_cotizante(edad).
 */
function calcular_costo_titular($plan_uf, $edad) {
    return $plan_uf * get_factor($edad, 'cotizante');
}

/**
 * Costo de UNA carga en UF: plan_uf × factor_carga(edad) + GES_prima.
 */
function calcular_costo_carga($plan_uf, $edad_carga, $ges_prima = null, $isapre = null) {
    if ($ges_prima === null) {
        $ges_prima = get_ges_prima($isapre);
    }
    return $plan_uf * get_factor($edad_carga, 'carga') + $ges_prima;
}

/**
 * Calcula el precio total mensual de un plan ISAPRE.
 * 
 * @param float $plan_uf        Precio base del plan en UF
 * @param int   $edad_titular   Edad del cotizante
 * @param int   $cargas         Cantidad de cargas
 * @param array|null $edad_cargas  Edades de cada carga (default: 10 años)
 * @param float|null $ges_prima    Prima GES (default: según ISAPRE)
 * @param int|null   $uf_value     Valor UF en CLP (default: 38500)
 * @param string|null $isapre      Nombre ISAPRE para lookup GES
 * @return array
 */
function calcular_precio($plan_uf, $edad_titular, $cargas = 0, $edad_cargas = null, 
                          $ges_prima = null, $uf_value = null, $isapre = null) {
    if ($ges_prima === null) $ges_prima = get_ges_prima($isapre);
    if ($uf_value === null) $uf_value = UF_DEFAULT;
    
    // Titular
    $factor_t = get_factor($edad_titular, 'cotizante');
    $costo_titular = calcular_costo_titular($plan_uf, $edad_titular);
    
    // Cargas
    $costo_cargas = 0.0;
    $factores_cargas = [];
    $detalle_cargas = [];
    
    if ($cargas > 0) {
        if ($edad_cargas === null) {
            $edad_cargas = array_fill(0, $cargas, 10);
        } elseif (count($edad_cargas) < $cargas) {
            $edad_cargas = array_merge($edad_cargas, array_fill(0, $cargas - count($edad_cargas), 10));
        }
        
        for ($i = 0; $i < $cargas; $i++) {
            $edad_c = $edad_cargas[$i];
            $factor_c = get_factor($edad_c, 'carga');
            $costo_c = calcular_costo_carga($plan_uf, $edad_c, $ges_prima, $isapre);
            $factores_cargas[] = $factor_c;
            $costo_cargas += $costo_c;
            $detalle_cargas[] = "Carga " . ($i+1) . "({$edad_c}a): {$plan_uf}×{$factor_c}+{$ges_prima}=" . round($costo_c, 2);
        }
    }
    
    $total_uf = $costo_titular + $costo_cargas;
    $total_clp = (int)round($total_uf * $uf_value);
    
    // Detalle legible
    $partes = ["Titular({$edad_titular}a): {$plan_uf}×{$factor_t}=" . round($costo_titular, 2)];
    $partes = array_merge($partes, $detalle_cargas);
    $partes[] = "Total: " . round($total_uf, 2) . " UF = $" . number_format($total_clp, 0, ',', '.');
    
    return [
        'total_uf'          => round($total_uf, 2),
        'total_clp'         => $total_clp,
        'costo_titular_uf'  => round($costo_titular, 2),
        'costo_cargas_uf'   => round($costo_cargas, 2),
        'factor_titular'    => $factor_t,
        'factor_cargas'     => $factores_cargas,
        'ges_prima'         => $ges_prima,
        'uf_value'          => $uf_value,
        'detalle'           => implode(' | ', $partes),
    ];
}

if (php_sapi_name() === "cli" && realpath($_SERVER["SCRIPT_FILENAME"]) === __FILE__) {
    echo "Test 1 — 30a, solo: " . $r['detalle'] . "\n";
    assert($r['total_uf'] == 2.5 && $r['total_clp'] == 96250, "Test 1 failed");
    
    $r = calcular_precio(2.5, 30, 1, [5], null, null, 'Banmédica');
    echo "Test 2 — 30a + niño 5a (Banmédica): " . $r['detalle'] . "\n";
    
    assert(get_factor(30, 'cotizante') === 1.0, "Factor cotizante 30a");
    assert(get_factor(30, 'carga') === 0.7, "Factor carga 30a");
    assert(get_factor(55, 'cotizante') === 2.0, "Factor cotizante 55a");
    assert(get_factor(55, 'carga') === 1.4, "Factor carga 55a");
    assert(get_factor(70, 'cotizante') === 2.4, "Factor cotizante 70a");
    assert(get_factor(10, 'carga') === 0.6, "Factor carga 10a");
    
    echo "\n✅ Todos los tests pasaron.\n";
    echo "   Tabla oficial Circular IF/N° 343 cargada correctamente.\n";
    echo "   GES primas: " . json_encode(GES_PRIMAS) . "\n";
}
