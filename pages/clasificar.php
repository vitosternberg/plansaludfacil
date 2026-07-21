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

$trans=[];$info=[];$nav=[];
foreach($results as $r){if($r['tipo']==='transaccional')$trans[]=$r;elseif($r['tipo']==='informativa')$info[]=$r;else $nav[]=$r;}
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Clasificador Keywords | PSF</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 min-h-screen font-sans"><div class="max-w-7xl mx-auto px-4 py-8"><h1 class="text-2xl font-bold text-gray-900 mb-2">🔍 Clasificador de Keywords</h1><p class="text-sm text-gray-500 mb-6">Pegá palabras clave (una por línea) para clasificarlas.</p><form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8"><textarea name="keywords" rows="10" class="w-full border border-gray-300 rounded-xl p-4 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pegá palabras clave..."><?=htmlspecialchars(implode("\n",$keywords))?></textarea><button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition">Clasificar</button></form><?php if(!empty($results)): ?>
<div class="flex gap-2 mb-4 items-center flex-wrap"><span class="text-sm text-gray-500" id="fl">Filtro: Todos</span><button onclick="filterBy('all')" class="text-xs bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-full font-medium">Todos</button><button onclick="filterBy('transaccional')" class="text-xs bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded-full font-medium">💰 Transaccional (<?=count($trans)?>)</button><button onclick="filterBy('informativa')" class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded-full font-medium">📚 Informativa (<?=count($info)?>)</button><button onclick="filterBy('navegacion')" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full font-medium">🧭 Navegación (<?=count($nav)?>)</button><button onclick="sortTable()" class="text-xs bg-white border border-gray-300 hover:bg-gray-100 px-3 py-1 rounded-full font-medium ml-auto">A → Z</button></div>
<div class="grid md:grid-cols-3 gap-4">
<div class="kw-col" data-col="transaccional"><div class="bg-green-600 text-white px-4 py-2.5 rounded-t-xl font-bold text-sm">💰 Transaccional</div><div class="bg-white rounded-b-xl shadow-sm border-x border-b border-gray-200 divide-y divide-gray-100 max-h-96 overflow-y-auto"><?php foreach($trans as $r): ?><div class="kw-text px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50"><?=htmlspecialchars($r['keyword'])?></div><?php endforeach; ?></div></div>
<div class="kw-col" data-col="informativa"><div class="bg-blue-600 text-white px-4 py-2.5 rounded-t-xl font-bold text-sm">📚 Informativa</div><div class="bg-white rounded-b-xl shadow-sm border-x border-b border-gray-200 divide-y divide-gray-100 max-h-96 overflow-y-auto"><?php foreach($info as $r): ?><div class="kw-text px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50"><?=htmlspecialchars($r['keyword'])?></div><?php endforeach; ?></div></div>
<div class="kw-col" data-col="navegacion"><div class="bg-gray-600 text-white px-4 py-2.5 rounded-t-xl font-bold text-sm">🧭 Navegación</div><div class="bg-white rounded-b-xl shadow-sm border-x border-b border-gray-200 divide-y divide-gray-100 max-h-96 overflow-y-auto"><?php foreach($nav as $r): ?><div class="kw-text px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-50"><?=htmlspecialchars($r['keyword'])?></div><?php endforeach; ?></div></div>
</div>
<script>
function filterBy(t){document.querySelectorAll('.kw-col').forEach(function(c){if(t==='all'||c.dataset.col===t){c.style.display=''}else{c.style.display='none'}});document.getElementById('fl').textContent='Filtro: '+(t==='all'?'Todos':t==='transaccional'?'💰 Transaccional':t==='informativa'?'📚 Informativa':'🧭 Navegación')}
var sa=true;function sortTable(){sa=!sa;document.querySelectorAll('.kw-col > div:last-child').forEach(function(col){var items=Array.from(col.querySelectorAll('.kw-text'));items.sort(function(a,b){var ta=a.textContent.trim().toLowerCase();var tb=b.textContent.trim().toLowerCase();return sa?ta.localeCompare(tb):tb.localeCompare(ta)});items.forEach(function(item){col.appendChild(item)})})}
</script>
<?php endif;?></div></body></html>