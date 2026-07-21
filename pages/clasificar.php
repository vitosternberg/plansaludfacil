<?php
$keywords = []; $results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['keywords'])) {
    $raw = $_POST['keywords'];
    $lines = array_unique(array_filter(array_map('trim', explode("\n", $raw)), function($v) { return $v !== ''; }));
    foreach ($lines as $kw) {
        $lower = strtolower($kw);
        $tipo = _clasificar($lower);
        $results[] = ['keyword' => $kw, 'tipo' => $tipo];
    }
    // Guardar en DB (corregido con escape)
    if (true) {
        require_once __DIR__ . '/../omniflow_config.php';
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$db->connect_error) {
            $db->set_charset("utf8mb4");
            $trans = 0; $info = 0; $nav = 0;
            foreach ($results as $r) {
                if ($r['tipo'] === 'transaccional') $trans++;
                elseif ($r['tipo'] === 'informativa') $info++;
                else $nav++;
            }
            $json = json_encode($results, JSON_UNESCAPED_UNICODE);
            $stmt = $db->prepare("INSERT INTO analisis_busquedas (total_keywords, total_transaccional, total_informativa, total_navegacion, keywords_input, resultados_json) VALUES (?, ?, ?, ?, ?, ?)");
            $count = count($results); $stmt->bind_param("iiiiss", $count, $trans, $info, $nav, $raw, $json);
            $stmt->execute(); $stmt->close(); $db->close();
        }
    }
}

function _clasificar($kw) {
    if (strpos($kw, 'www.') === 0 || strpos($kw, 'http') === 0) return 'navegacion';
    $dots = substr_count($kw, '.');
    if ($dots >= 1 && preg_match('/\.cl$|\.com$|\.net$|\.org$/', $kw)) return 'navegacion';
    
    $nav_words = ['mi ','mis ','acceder','ingresar','portal','sucursal','oficina','fono','telefono','teléfono','llamar','iniciar sesion','login','direccion','dirección','ubicacion','ubicación'];
    foreach ($nav_words as $w) if (stripos($kw, $w) !== false) return 'navegacion';
    
    $trans_words = ['comprar','contratar','cotizar','cotiza','precio','cuanto cuesta','cuánto cuesta','valor','costo','presupuesto','tarifa','afiliarse','afiliarme','cambiarse','cambio de','traslado','requisitos para','tramite','trámite','preexistencia','declaracion de salud','declaración de salud','mejor isapre','mejor seguro','mejor plan','cual es mejor','cual elegir','comparador','comparativa','que isapre','cual isapre','conviene','recomiendan','donde contratar','solicitar','pedir plan','obtener plan','como cambiarse','cambio de isapre','cambio de plan','afiliar a mi','inscribir a mi','agregar carga','incluir carga','planes para','plan para','seguro para','isapre para','cuanto sale','cuánto sale'];
    foreach ($trans_words as $w) if (stripos($kw, $w) !== false) return 'transaccional';
    
    $info_words = ['que es','qué es','como funciona','cómo funciona','definicion','definición','concepto','explicacion','explicación','significa','significado','diferencia entre','vs ','versus','ventajas','desventajas','ley','legislacion','legislación','normativa','regulacion','regulación','decreto','reforma','ranking','noticias','tipos de','clases de','categorias','categorías','ejemplo','experiencia','testimonio','opinion','opinión','review','reseña'];
    foreach ($info_words as $w) if (stripos($kw, $w) !== false) return 'informativa';
    
    if (preg_match('/^(como|cómo|que|qué|cual|cuál|cuando|cuándo|donde|dónde|por que|por qué) /', $kw)) return 'informativa';
    if (substr($kw, -1) === '?') return 'informativa';
    
    $brands = ['banmedica','banmédica','colmena','consalud','cruz blanca','cruzblanca','esencial','nueva masvida','nuevamasvida','masvida','mas vida','más vida','vida tres','vidatres','fonasa','isapre','isapres'];
    $clean = str_replace(['.','-',' '], '', $kw);
    foreach ($brands as $b) if ($clean === str_replace(' ', '', $b)) return 'navegacion';
    
    return 'informativa';
}

function _badge($t) { return $t==='transaccional'?'bg-green-100 text-green-800':($t==='informativa'?'bg-blue-100 text-blue-800':'bg-gray-100 text-gray-800'); }
function _emoji($t) { return $t==='transaccional'?'💰':($t==='informativa'?'📚':'🧭'); }
$trans=[];$info=[];$nav=[];
foreach($results as $r){if($r['tipo']==='transaccional')$trans[]=$r;elseif($r['tipo']==='informativa')$info[]=$r;else $nav[]=$r;}
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Clasificador Keywords | PSF</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 min-h-screen font-sans"><div class="max-w-5xl mx-auto px-4 py-8"><h1 class="text-2xl font-bold text-gray-900 mb-2">🔍 Clasificador de Keywords</h1><p class="text-sm text-gray-500 mb-6">Pegá palabras clave (una por línea) para clasificarlas con filtros y orden.</p><form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8"><textarea name="keywords" rows="12" class="w-full border border-gray-300 rounded-xl p-4 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pegá palabras clave..."><?=htmlspecialchars(implode("\n",$keywords))?></textarea><button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition">Clasificar</button></form><?php if(!empty($results)): ?><div class="grid md:grid-cols-3 gap-6"><?php
function _columna($items, $titulo, $emoji, $color) {
    $count = count($items);
    echo '<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"><div class="'.$color.' p-4"><h2 class="font-bold text-lg">'.$emoji.' '.$titulo.' <span class="text-sm font-normal text-white/70">('.$count.')</span></h2></div><div class="divide-y divide-gray-100">';
    foreach ($items as $r) echo '<div class="px-4 py-2 text-sm text-gray-700">'.htmlspecialchars($r['keyword']).'</div>';
    echo '</div></div>';
} _columna($trans, 'Transaccional', '💰', 'bg-green-600');
   _columna($info, 'Informativa', '📚', 'bg-blue-600');
   _columna($nav, 'Navegación', '🧭', 'bg-gray-600');
?></div><?php endif;?></div></body></html>