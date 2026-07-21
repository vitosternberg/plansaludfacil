<?php
/**
 * pages/clasificar.php — Clasificador de Keywords (herramienta interna)
 * Pega palabras clave y las clasifica en: Transaccional, Informativa, Navegación
 * Con filtros por tipo y orden A-Z.
 */
$keywords = [];
$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['keywords'])) {
    $raw = $_POST['keywords'];
    $keywords = array_filter(array_map('trim', explode("\n", $raw)));
    foreach ($keywords as $kw) {
        $lower = mb_strtolower($kw, 'UTF-8');
        $tipo = clasificar($lower);
        $results[] = ['keyword' => $kw, 'tipo' => $tipo];
    }
    // Guardar en DB para reportes
    require_once __DIR__ . '/../omniflow_config.php';
    try {
        $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$db->connect_error) {
            $trans = count(array_filter($results, function($r) { return $r['tipo'] === 'transaccional'; }));
            $info = count(array_filter($results, function($r) { return $r['tipo'] === 'informativa'; }));
            $nav = count(array_filter($results, function($r) { return $r['tipo'] === 'navegacion'; }));
            $stmt = $db->prepare("INSERT INTO analisis_busquedas (total_keywords, total_transaccional, total_informativa, total_navegacion, keywords_input, resultados_json) VALUES (?, ?, ?, ?, ?, ?)");
            $json = json_encode($results, JSON_UNESCAPED_UNICODE);
            $stmt->bind_param("iiiiss", count($results), $trans, $info, $nav, $raw, $json);
            $stmt->execute();
            $stmt->close();
            $db->close();
        }
    } catch (Exception $e) { error_log("clasificar DB: ".$e->getMessage()); }
}

function clasificar($kw) {
    if (preg_match('/^(www\.|https?:\/\/)/', $kw)) return 'navegacion';
    if (preg_match('/^\w+\.(cl|com|net|org)$/', $kw)) return 'navegacion';
    $nav = ['/^(mi |mis |acceder a |ingresar a |portal |sucursal |oficina |fono |teléfono |telefono |llamar a |iniciar sesión|iniciar sesion|login)/','/\b(oficinas?|sucursales?|teléfonos?|telefonos?|dirección|direccion|ubicación|ubicacion)\b/'];
    foreach ($nav as $p) if (preg_match($p,$kw)) return 'navegacion';
    $trans = ['/\b(comprar|contratar|cotizar|cotiza|precio|cuanto cuesta|cuánto cuesta|valor|costo|presupuesto|tarifa)\b/','/\b(planes?|plan de|seguro|seguros|isapre)\b.*\b(precios?|cotizar|costo|valor|barato|baratos|económico|economico|mejor|comparar)\b/','/\b(afilial|afiliarse|cambiarse|cambio de|traslado|trasladarse)\b/','/\b(requisitos para|tramite|trámite|documentos para|como contratar|cómo contratar)\b/','/\b(pre existencias?|preexistencia|enfermedades pre|declaración de salud|declaracion de salud)\b/','/\b(mejor .{1,30} (isapre|seguro|plan)|cual es mejor|cual elegir|comparativa|comparador)\b/','/\b(que isapre|qué isapre|cual isapre|cuál isapre) (elegir|contratar|conviene|recomiendan)\b/','/\b(precio|costo|valor|tarifa|cuota).{1,20}(isapre|seguro|plan|salud)\b/','/\b(isapre|seguro|plan).{1,20}(precio|costo|valor|tarifa|barato)\b/','/\b(donde contratar|dónde contratar|donde cotizar|dónde cotizar|solicitar|pedir|obtener)\b/','/\b(como cambiarse|cómo cambiarse|cambio de isapre|cambio de plan)\b/','/\b(afiliar a mi|inscribir a mi|agregar a mi|incluir a mi)\b/','/how much (for|does).*(insurance|health|plan)/','/\b(buy|purchase|get|find|compare|switch).*(insurance|health|plan|coverage)\b/','/\b(planes para|plan para).{1,30}\b/','/\b(seguro para|isapre para).{1,30}\b/'];
    foreach ($trans as $p) if (preg_match($p,$kw)) return 'transaccional';
    $info = ['/\b(que es|qué es|que son|qué son|como funciona|cómo funciona|como funcionan|cómo funcionan)\b/','/\b(definición|definicion|concepto|explicación|explicacion|significa|significado)\b/','/\b(diferencia entre|vs |versus|comparativa|comparacion|comparación|ventajas|desventajas)\b/','/\b(ley|legislacion|legislación|normativa|regulacion|regulación|decreto|reforma)\b/','/\b(historia|origen|trayectoria|ranking|top)\b/','/\b(noticias|novedades|aumento|alza|baja|UF|IPC|prima)\b/','/\b(tipos de|clases de|categorias|categorías)\b/','/\b(ejemplo|caso|experiencia|testimonio|opinión|opinion|review|reseña|resena)\b/','/^como /','/^cómo /','/^que /','/^qué /','/^cual /','/^cuál /','/^cuando /','/^cuándo /','/^donde /','/^dónde /','/^por que /','/^por qué /','/\?$/'];
    foreach ($info as $p) if (preg_match($p,$kw)) return 'informativa';
    $brands = ['banmédica','banmedica','colmena','consalud','cruz blanca','cruzblanca','esencial','nueva masvida','nuevamasvida','masvida','más vida','mas vida','vida tres','vidatres','fonasa','isapre','isapres'];
    $clean_kw = preg_replace('/[\.\-\s]/', '', $kw);
    foreach ($brands as $brand) { $clean_brand = preg_replace('/\s/', '', $brand); if ($clean_kw === $clean_brand) return 'navegacion'; }
    return 'informativa';
}
$page_title = 'Clasificador de Keywords | Plan Salud Fácil';
?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title><?=$page_title?></title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-gray-100 min-h-screen font-sans"><div class="max-w-5xl mx-auto px-4 py-8"><h1 class="text-2xl font-bold text-gray-900 mb-2">🔍 Clasificador de Keywords</h1><p class="text-sm text-gray-500 mb-6">Pegá palabras clave (una por línea) y clasificalas con filtros y orden.</p><form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8"><textarea name="keywords" rows="12" class="w-full border border-gray-300 rounded-xl p-4 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pegá palabras clave, una por línea..."><?=htmlspecialchars(implode("\n",$keywords))?></textarea><button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition">Clasificar</button></form><?php if(!empty($results)): $trans=array_filter($results,function($r){return $r['tipo']==='transaccional';});$info=array_filter($results,function($r){return $r['tipo']==='informativa';});$nav=array_filter($results,function($r){return $r['tipo']==='navegacion';});?><div class="grid md:grid-cols-3 gap-4 mb-4"><button onclick="filterBy('transaccional')" class="bg-green-50 border border-green-200 rounded-xl p-4 text-center hover:bg-green-100 transition"><div class="text-3xl font-extrabold text-green-700"><?=count($trans)?></div><div class="text-sm text-green-600">Transaccionales 💰</div></button><button onclick="filterBy('informativa')" class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center hover:bg-blue-100 transition"><div class="text-3xl font-extrabold text-blue-700"><?=count($info)?></div><div class="text-sm text-blue-600">Informativas 📚</div></button><button onclick="filterBy('navegacion')" class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center hover:bg-gray-100 transition"><div class="text-3xl font-extrabold text-gray-700"><?=count($nav)?></div><div class="text-sm text-gray-600">Navegación 🧭</div></button></div><div class="flex gap-2 mb-4 items-center"><span class="text-sm text-gray-500" id="filterLabel">Mostrando: Todos (<?=count($results)?>)</span><button onclick="filterBy('all')" class="text-xs bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-full font-medium">Todos</button><button onclick="sortTable()" class="text-xs bg-white border border-gray-300 hover:bg-gray-100 px-3 py-1 rounded-full font-medium">A → Z</button></div><div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"><table class="w-full text-sm" id="kwTable"><thead class="bg-gray-50 border-b border-gray-200"><tr><th class="text-left py-3 px-4 font-semibold text-gray-700 cursor-pointer hover:text-blue-600" onclick="sortTable()">Keyword ↕</th><th class="text-left py-3 px-4 font-semibold text-gray-700 w-40">Clasificación</th></tr></thead><tbody><?php foreach($results as $r): $badge=$r['tipo']==='transaccional'?'bg-green-100 text-green-800':($r['tipo']==='informativa'?'bg-blue-100 text-blue-800':'bg-gray-100 text-gray-800');$emoji=$r['tipo']==='transaccional'?'💰':($r['tipo']==='informativa'?'📚':'🧭');?><tr class="border-b border-gray-100 hover:bg-gray-50 kw-row" data-tipo="<?=$r['tipo']?>"><td class="kw-text py-2.5 px-4"><?=htmlspecialchars($r['keyword'])?></td><td class="py-2.5 px-4"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium <?=$badge?>"><?=$emoji?> <?=ucfirst($r['tipo'])?></span></td></tr><?php endforeach;?></tbody></table></div><script>let sortAsc=true;let currentFilter='all';function filterBy(tipo){currentFilter=tipo;const rows=document.querySelectorAll('.kw-row');let count=0;rows.forEach(row=>{if(tipo==='all'||row.dataset.tipo===tipo){row.style.display='';count++}else{row.style.display='none'}});const labels={all:'Todos',transaccional:'💰 Transaccionales',informativa:'📚 Informativas',navegacion:'🧭 Navegación'};document.getElementById('filterLabel').textContent='Mostrando: '+labels[tipo]+' ('+count+')'}function sortTable(){sortAsc=!sortAsc;const tbody=document.querySelector('#kwTable tbody');const rows=Array.from(tbody.querySelectorAll('.kw-row'));rows.sort((a,b)=>{const ta=a.querySelector('.kw-text').textContent.trim().toLowerCase();const tb=b.querySelector('.kw-text').textContent.trim().toLowerCase();return sortAsc?ta.localeCompare(tb):tb.localeCompare(ta)});rows.forEach(row=>tbody.appendChild(row));document.querySelector('#kwTable th').textContent='Keyword '+(sortAsc?'A→Z':'Z→A')}</script><?php endif;?></div></body></html>
