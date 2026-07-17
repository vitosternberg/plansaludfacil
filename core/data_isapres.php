<?php
/**
 * core/data_isapres.php
 * Datos reales de isapres chilenas — fuente: queplan.cl / QuVi.cl (julio 2026)
 * 
 * Usado por el comparador, páginas de compañías, y cualquier componente
 * que necesite ground-truth data en vez de datos inventados.
 */

$ISAPRES = [
    'Banmédica' => [
        'slug'            => 'banmedica',
        'num_planes'      => 290,
        'precio_uf'       => ['min' => 0.94, 'max' => 8.82, 'avg' => 3.06],
        'cobertura'       => ['hosp' => 74, 'amb' => 63],
        'prestadores'     => 43,
        'historia'        => 'Más de 35 años, 31 sucursales, 8000 convenios, 585K beneficiarios. Parte del Grupo Banmédica.',
        'clinicas'        => ['Clínica Las Condes', 'Clínica Alemana', 'Clínica Santa María', 'Red UC Christus', 'Clínica INDISA', 'Clínica UAndes', 'Vida Integra'],
        'planes_top'      => [
            ['nombre' => 'Salud Platinum One', 'codigo' => 'BPPO26071012', 'uf' => 4.55, 'tope_anual' => 11000, 'prestadores_plan' => 16, 'hosp' => '100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/BPPO26071012'],
            ['nombre' => 'Salud Black',        'codigo' => 'BPB260656', 'uf' => 8.82, 'tope_anual' => 11000, 'prestadores_plan' => 17, 'hosp' => '90-100%', 'amb' => '80-90%', 'url' => 'https://www.quvi.cl/plan/BPB260656'],
            ['nombre' => 'Salud Conecta Clásico', 'codigo' => 'BSCC260100', 'uf' => 0.94, 'tope_anual' => 5000, 'prestadores_plan' => 3, 'hosp' => '40%', 'amb' => '40%', 'url' => 'https://www.quvi.cl/plan/BSCC260100'],
            ['nombre' => 'Salud Superior Reg. Sur', 'codigo' => 'BSS8F260588', 'uf' => 3.59, 'tope_anual' => 7000, 'prestadores_plan' => 25, 'hosp' => '80%', 'amb' => '60%', 'url' => 'https://www.quvi.cl/plan/BSS8F260588'],
        ],
        'precios'         => [
            'individual' => [
                30 => ['min' =>  67371, 'max' => 376384],
                40 => ['min' =>  78430, 'max' => 480146],
                50 => ['min' =>  82116, 'max' => 514733],
            ],
            'pareja' => [
                30 => ['min' => 134742, 'max' =>  752767],
                40 => ['min' => 156859, 'max' =>  960292],
                50 => ['min' => 164232, 'max' => 1029467],
            ],
            'familia' => [
                30 => ['min' => 187368, 'max' =>  990801],
                40 => ['min' => 209485, 'max' => 1198326],
                50 => ['min' =>     'N/A', 'max' =>     'N/A'],
            ],
        ],
    ],

    'Colmena' => [
        'slug'            => 'colmena',
        'num_planes'      => 456,
        'precio_uf'       => ['min' => 1.15, 'max' => 5.32, 'avg' => 2.58],
        'cobertura'       => ['hosp' => 78, 'amb' => 66],
        'prestadores'     => 71,
        'historia'        => 'Fundada en 1981, pionera en el sistema. Fusión con Nueva MasVida en 2023.',
        'clinicas'        => ['Clínica INDISA', 'Red UC Christus', 'Clínica Alemana', 'Clínica Las Condes', 'Clínica Santa María', 'Clínica UAndes'],
        'planes_top'      => [
            ['nombre' => 'Colmena Master', 'codigo' => 'MS22610080', 'uf' => 4.52, 'tope_anual' => 8500, 'prestadores_plan' => 13, 'hosp' => '100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/MS22610080'],
            ['nombre' => 'Colmena Pro',    'codigo' => 'PR32610080', 'uf' => 5.32, 'tope_anual' => 8500, 'prestadores_plan' => 12, 'hosp' => '100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/PR32610080'],
            ['nombre' => 'Colmena Star',   'codigo' => 'ST2264040', 'uf' => 1.15, 'tope_anual' => 5500, 'prestadores_plan' => 8, 'hosp' => '40-50%', 'amb' => '40%', 'url' => 'https://www.quvi.cl/plan/ST2264040'],
            ['nombre' => 'Colmena Max',    'codigo' => 'MX22610050', 'uf' => 3.20, 'tope_anual' => 7500, 'prestadores_plan' => 63, 'hosp' => '80%', 'amb' => '60%', 'url' => 'https://www.quvi.cl/plan/MX22610050'],
        ],
        'precios'         => [
            'individual' => [
                30 => ['min' =>  85724, 'max' => 249249],
                40 => ['min' =>  99253, 'max' => 311836],
                50 => ['min' => 103762, 'max' => 332698],
            ],
            'pareja' => [
                30 => ['min' => 171447, 'max' => 498499],
                40 => ['min' => 198505, 'max' => 623672],
                50 => ['min' => 207525, 'max' => 665397],
            ],
            'familia' => [
                30 => ['min' => 239132, 'max' => 664299],
                40 => ['min' => 266190, 'max' => 789472],
                50 => ['min' => 279719, 'max' => 852059],
            ],
        ],
    ],

    'Consalud' => [
        'slug'            => 'consalud',
        'num_planes'      => 472,
        'precio_uf'       => ['min' => 1.16, 'max' => 6.42, 'avg' => 3.02],
        'cobertura'       => ['hosp' => 81, 'amb' => 67],
        'prestadores'     => 53,
        'historia'        => 'Fundada en 1983 por la CChC. 40+ años, líder en seguro de cesantía (1989), bonos electrónicos (2000) y oncológico (2013).',
        'clinicas'        => ['Clínica Las Condes', 'Clínica Alemana', 'Red UC Christus', 'Clínica Santa María', 'Clínica INDISA', 'Hospital del Trabajador', 'Clínica UAndes'],
        'planes_top'      => [
            ['nombre' => 'Select Full Centro 150', 'codigo' => '13-SFC157-26', 'uf' => 6.42, 'tope_anual' => 10000, 'prestadores_plan' => 28, 'hosp' => '80-100%', 'amb' => '60-80%', 'url' => 'https://www.quvi.cl/plan/13-SFC157-26'],
            ['nombre' => 'Select Full 260',        'codigo' => '13-SF267-26', 'uf' => 6.36, 'tope_anual' => 9900, 'prestadores_plan' => 22, 'hosp' => '80-100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/13-SF267-26'],
            ['nombre' => 'Core 10 01',             'codigo' => '13-CORE101-26', 'uf' => 1.16, 'tope_anual' => 3900, 'prestadores_plan' => 3, 'hosp' => '40%', 'amb' => '40%', 'url' => 'https://www.quvi.cl/plan/13-CORE101-26'],
            ['nombre' => 'Select Full Centro 100', 'codigo' => '13-SFC107-26', 'uf' => 4.34, 'tope_anual' => 10000, 'prestadores_plan' => 28, 'hosp' => '80-100%', 'amb' => '60-80%', 'url' => 'https://www.quvi.cl/plan/13-SFC107-26'],
        ],
        'precios'         => [
            'individual' => [
                30 => ['min' =>  77237, 'max' => 292081],
                40 => ['min' =>  91451, 'max' => 370748],
                50 => ['min' =>  96189, 'max' => 396971],
            ],
            'pareja' => [
                30 => ['min' => 154475, 'max' => 584162],
                40 => ['min' => 182903, 'max' => 741496],
                50 => ['min' => 192379, 'max' => 793941],
            ],
            'familia' => [
                30 => ['min' => 212761, 'max' =>  771354],
                40 => ['min' => 241188, 'max' =>  928688],
                50 => ['min' => 255402, 'max' => 1007355],
            ],
        ],
    ],

    'Cruz Blanca' => [
        'slug'            => 'cruz-blanca',
        'num_planes'      => 526,
        'precio_uf'       => ['min' => 1.09, 'max' => 5.50, 'avg' => 3.11],
        'cobertura'       => ['hosp' => 82, 'amb' => 74],
        'prestadores'     => 41,
        'historia'        => 'Constituida en 1985. Pertenece a Bupa UK, con clínicas propias en Chile.',
        'clinicas'        => ['Red UC Christus', 'Clínica Alemana', 'Clínica Las Condes', 'Clínica Santa María', 'Clínica INDISA', 'Clínica UAndes', 'Hospital del Trabajador'],
        'planes_top'      => [
            ['nombre' => 'Solución 1 Regional', 'codigo' => 'SOLR112526', 'uf' => 4.73, 'tope_anual' => 8000, 'prestadores_plan' => 33, 'hosp' => '90-100%', 'amb' => '80-100%', 'url' => 'https://www.quvi.cl/plan/SOLR112526'],
            ['nombre' => 'Solución 2',          'codigo' => 'SOLN214526', 'uf' => 5.50, 'tope_anual' => 8000, 'prestadores_plan' => 14, 'hosp' => '90-100%', 'amb' => '60-100%', 'url' => 'https://www.quvi.cl/plan/SOLN214526'],
            ['nombre' => 'Campus Bupa Max',     'codigo' => 'CMBX001D25', 'uf' => 1.09, 'tope_anual' => 1500, 'prestadores_plan' => 6, 'hosp' => '40%', 'amb' => '50%', 'url' => 'https://www.quvi.cl/plan/CMBX001D25'],
            ['nombre' => 'Solución 2 Regional', 'codigo' => 'SOLR201526', 'uf' => 4.61, 'tope_anual' => 8000, 'prestadores_plan' => 34, 'hosp' => '90-100%', 'amb' => '80-100%', 'url' => 'https://www.quvi.cl/plan/SOLR201526'],
        ],
        'precios'         => [
            'individual' => [
                30 => ['min' =>  84181, 'max' => 362743],
                40 => ['min' =>  97537, 'max' => 459667],
                50 => ['min' => 101989, 'max' => 491975],
            ],
            'pareja' => [
                30 => ['min' => 168362, 'max' =>  725485],
                40 => ['min' => 195075, 'max' =>  919334],
                50 => ['min' => 203979, 'max' =>  983951],
            ],
            'familia' => [
                30 => ['min' => 234735, 'max' =>  958995],
                40 => ['min' => 261447, 'max' => 1152844],
                50 => ['min' => 274804, 'max' => 1249769],
            ],
        ],
    ],

    'Esencial' => [
        'slug'            => 'esencial',
        'num_planes'      => 83,
        'precio_uf'       => ['min' => 1.44, 'max' => 5.70, 'avg' => 2.91],
        'cobertura'       => ['hosp' => 74, 'amb' => 63],
        'prestadores'     => 51,
        'historia'        => 'Respaldo del Grupo Clínica Alemana. Cobertura GES de 90 patologías garantizada.',
        'clinicas'        => ['Clínica Alemana', 'Clínica Santa María', 'Red UC Christus', 'Clínica INDISA (convenio Grupo Alemana)'],
        'planes_top'      => [
            ['nombre' => 'Esencial 100H70A', 'codigo' => 'E100700626', 'uf' => 5.50, 'tope_anual' => 12000, 'prestadores_plan' => 37, 'hosp' => '100%', 'amb' => '70-90%', 'url' => 'https://www.quvi.cl/plan/E100700626'],
            ['nombre' => 'Esencial 90H80A',  'codigo' => 'E90800626', 'uf' => 5.70, 'tope_anual' => 12000, 'prestadores_plan' => 37, 'hosp' => '90-100%', 'amb' => '80-90%', 'url' => 'https://www.quvi.cl/plan/E90800626'],
            ['nombre' => 'Esencial Inicia',  'codigo' => 'IN60400626', 'uf' => 1.44, 'tope_anual' => 6000, 'prestadores_plan' => 9, 'hosp' => '50-60%', 'amb' => '40%', 'url' => 'https://www.quvi.cl/plan/IN60400626'],
            ['nombre' => 'Esencial Santa María', 'codigo' => 'SM70600626', 'uf' => 2.55, 'tope_anual' => 8500, 'prestadores_plan' => 38, 'hosp' => '70%', 'amb' => '60%', 'url' => 'https://www.quvi.cl/plan/SM70600626'],
        ],
        'precios'         => [
            'individual' => [
                30 => ['min' =>  95985, 'max' => 269984],
                40 => ['min' => 113630, 'max' => 339829],
                50 => ['min' => 119512, 'max' => 363110],
            ],
            'pareja' => [
                30 => ['min' => 191971, 'max' => 539968],
                40 => ['min' => 227260, 'max' => 679657],
                50 => ['min' => 239024, 'max' => 726220],
            ],
            'familia' => [
                30 => ['min' => 264429, 'max' => 716826],
                40 => ['min' => 299719, 'max' => 856515],
                50 => ['min' => 317364, 'max' => 926360],
            ],
        ],
    ],

    'Nueva MasVida' => [
        'slug'            => 'nueva-masvida',
        'num_planes'      => 174,
        'precio_uf'       => ['min' => 1.18, 'max' => 5.64, 'avg' => 3.30],
        'cobertura'       => ['hosp' => 90, 'amb' => 73],
        'prestadores'     => 37,
        'historia'        => 'Ex Óptima, fusión con Masvida en 2017. Nexus Company (USA). 275K beneficiarios, 33 sucursales.',
        'clinicas'        => ['Clínica INDISA', 'Clínica Dávila', 'Clínica Las Condes', 'Clínica Santa María', 'IntegraMédica', 'Clínica UAndes', 'Hospital del Trabajador'],
        'planes_top'      => [
            ['nombre' => 'Pleno Plus Sur', 'codigo' => 'PPS23300', 'uf' => 4.81, 'tope_anual' => 10000, 'prestadores_plan' => 18, 'hosp' => '90-100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/PPS23300'],
            ['nombre' => 'Pleno Max',      'codigo' => 'PM260334', 'uf' => 5.64, 'tope_anual' => 10000, 'prestadores_plan' => 13, 'hosp' => '100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/PM260334'],
            ['nombre' => 'Pleno Salud',    'codigo' => 'PS260600', 'uf' => 1.18, 'tope_anual' => 8000, 'prestadores_plan' => 9, 'hosp' => '50-70%', 'amb' => '50-60%', 'url' => 'https://www.quvi.cl/plan/PS260600'],
            ['nombre' => 'Pleno Plus Sur 280', 'codigo' => 'PPS23280', 'uf' => 4.23, 'tope_anual' => 10000, 'prestadores_plan' => 18, 'hosp' => '90-100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/PPS23280'],
        ],
        'precios'         => [
            'individual' => [
                30 => ['min' =>  79763, 'max' => 272700],
                40 => ['min' =>  93645, 'max' => 344463],
                50 => ['min' =>  98272, 'max' => 368384],
            ],
            'pareja' => [
                30 => ['min' => 159526, 'max' => 545399],
                40 => ['min' => 187290, 'max' => 688926],
                50 => ['min' => 196545, 'max' => 736768],
            ],
            'familia' => [
                30 => ['min' => 220779, 'max' => 722415],
                40 => ['min' => 248543, 'max' => 865941],
                50 => ['min' => 262425, 'max' => 937704],
            ],
        ],
    ],

    'Vida Tres' => [
        'slug'            => 'vida-tres',
        'num_planes'      => 230,
        'precio_uf'       => ['min' => 1.29, 'max' => 8.99, 'avg' => 3.30],
        'cobertura'       => ['hosp' => 76, 'amb' => 64],
        'prestadores'     => 43,
        'historia'        => 'Más de 3 décadas en el mercado. Parte del Grupo Banmédica y UnitedHealth Group.',
        'clinicas'        => ['Red Grupo Banmédica', 'Clínica Las Condes', 'Clínica Alemana', 'Clínica Santa María', 'Red UC Christus', 'Clínica INDISA'],
        'planes_top'      => [
            ['nombre' => 'Vanguardia Premium Platinum One', 'codigo' => 'VPRPO26071012', 'uf' => 4.68, 'tope_anual' => 11000, 'prestadores_plan' => 16, 'hosp' => '100%', 'amb' => '80%', 'url' => 'https://www.quvi.cl/plan/VPRPO26071012'],
            ['nombre' => 'Vanguardia Premium Black',        'codigo' => 'VPRB260656', 'uf' => 8.99, 'tope_anual' => 11000, 'prestadores_plan' => 17, 'hosp' => '90-100%', 'amb' => '80-90%', 'url' => 'https://www.quvi.cl/plan/VPRB260656'],
            ['nombre' => 'Vanguardia Plus Gold',            'codigo' => 'VPG260500', 'uf' => 1.29, 'tope_anual' => 7000, 'prestadores_plan' => 6, 'hosp' => '40%', 'amb' => '40%', 'url' => 'https://www.quvi.cl/plan/VPG260500'],
            ['nombre' => 'Vanguardia Plus Reg. Sur',        'codigo' => 'VPS8F260588', 'uf' => 3.71, 'tope_anual' => 7000, 'prestadores_plan' => 25, 'hosp' => '80%', 'amb' => '60%', 'url' => 'https://www.quvi.cl/plan/VPS8F260588'],
        ],
        'precios'         => [
            'individual' => [
                30 => ['min' =>  81771, 'max' => 396276],
                40 => ['min' =>  97578, 'max' => 506435],
                50 => ['min' => 102847, 'max' => 543154],
            ],
            'pareja' => [
                30 => ['min' => 163543, 'max' =>  792552],
                40 => ['min' => 195156, 'max' => 1012869],
                50 => ['min' => 205694, 'max' => 1086308],
            ],
            'familia' => [
                30 => ['min' => 224238, 'max' => 1041951],
                40 => ['min' => 255852, 'max' => 1262267],
                50 => ['min' => 271659, 'max' => 1372426],
            ],
        ],
    ],
];

/**
 * Devuelve la categoría de perfil según cargas.
 * 0 = individual, 1 = pareja, 2+ = familia
 */
function getPerfilCategoria($cargas) {
    if ($cargas == 0) return 'individual';
    if ($cargas == 1) return 'pareja';
    return 'familia';
}

/**
 * Devuelve el bracket de edad más cercano (30, 40, o 50).
 */
function getEdadBracket($edad) {
    if ($edad <= 35) return 30;
    if ($edad <= 50) return 40;
    return 50;
}

/**
 * Obtiene los precios reales para un perfil dado.
 * Retorna array con isapre => [min, max] o null si no hay dato.
 */
function getPreciosPerfil($edad, $cargas) {
    global $ISAPRES;
    $categoria = getPerfilCategoria($cargas);
    $bracket   = getEdadBracket($edad);
    $result = [];
    foreach ($ISAPRES as $nombre => $data) {
        $precio = $data['precios'][$categoria][$bracket] ?? null;
        if ($precio && $precio['min'] !== 'N/A') {
            $result[$nombre] = $precio;
        }
    }
    return $result;
}
