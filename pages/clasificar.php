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
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Clasificador Keywords | PSF</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 min-h-screen font-sans"><div class="max-w-5xl mx-auto px-4 py-8"><h1 class="text-2xl font-bold text-gray-900 mb-2">🔍 Clasificador de Keywords</h1><p class="text-sm text-gray-500 mb-6">Pegá palabras clave (una por línea) para clasificarlas con filtros y orden.</p><form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8"><textarea name="keywords" rows="12" class="w-full border border-gray-300 rounded-xl p-4 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pegá palabras clave..."><?=htmlspecialchars(implode("\n",$keywords))?></textarea><button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition">Clasificar</button></form><?php if(!empty($results)): ?><div class="grid md:grid-cols-3 gap-4 mb-4"><button onclick="filterBy('transaccional')" class="bg-green-50 border border-green-200 rounded-xl p-4 text-center hover:bg-green-100 transition"><div class="text-3xl font-extrabold text-green-700"><?=count($trans)?></div><div class="text-sm text-green-600">💰 Transaccional</div></button><button onclick="filterBy('informativa')" class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center hover:bg-blue-100 transition"><div class="text-3xl font-extrabold text-blue-700"><?=count($info)?></div><div class="text-sm text-blue-600">📚 Informativa</div></button><button onclick="filterBy('navegacion')" class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center hover:bg-gray-100 transition"><div class="text-3xl font-extrabold text-gray-700"><?=count($nav)?></div><div class="text-sm text-gray-600">🧭 Navegación</div></button></div><div class="flex gap-2 mb-4 items-center"><span class="text-sm text-gray-500" id="fl">Mostrando: Todos (<?=count($results)?>)</span><button onclick="filterBy('all')" class="text-xs bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-full font-medium">Todos</button><button onclick="sortTable()" class="text-xs bg-white border border-gray-300 hover:bg-gray-100 px-3 py-1 rounded-full font-medium">A → Z</button></div><div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"><table class="w-full text-sm" id="kt"><thead class="bg-gray-50 border-b border-gray-200"><tr><th class="text-left py-3 px-4 font-semibold text-gray-700 cursor-pointer hover:text-blue-600" onclick="sortTable()">Keyword ↕</th><th class="text-left py-3 px-4 font-semibold text-gray-700 w-40">Clasificación</th></tr></thead><tbody><?php foreach($results as $r): ?><tr class="border-b border-gray-100 hover:bg-gray-50 kw-row" data-tipo="<?=$r['tipo']?>"><td class="kw-text py-2.5 px-4"><?=htmlspecialchars($r['keyword'])?></td><td class="py-2.5 px-4"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium <?=_badge($r['tipo'])?>"><?=_emoji($r['tipo'])?> <?=ucfirst($r['tipo'])?></span></td></tr><?php endforeach;?></tbody></table></div><script>let sa=true;function filterBy(t){const r=document.querySelectorAll('.kw-row');let c=0;r.forEach(r=>{if(t==='all'||r.dataset.tipo===t){r.style.display='';c++}else{r.style.display='none'}});document.getElementById('fl').textContent='Mostrando: '+(t==='all'?'Todos':t==='transaccional'?'💰 Transaccionales':t==='informativa'?'📚 Informativas':'🧭 Navegación')+' ('+c+')'}function sortTable(){sa=!sa;const tb=document.querySelector('#kt tbody');const rs=Array.from(tb.querySelectorAll('.kw-row'));rs.sort((a,b)=>{const ta=a.querySelector('.kw-text').textContent.trim().toLowerCase();const tb=b.querySelector('.kw-text').textContent.trim().toLowerCase();return sa?ta.localeCompare(tb):tb.localeCompare(ta)});rs.forEach(r=>tb.appendChild(r))}</script><?php endif;?></div></body></html>