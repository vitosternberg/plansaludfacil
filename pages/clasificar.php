<?php
/**
 * pages/clasificar.php — Clasificador de Keywords (herramienta interna)
 * Pega palabras clave y las clasifica en: Transaccional, Informativa, Navegación
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
}

function clasificar($kw) {
    // Navegación: URLs, nombres de dominio, búsquedas de marca pura
    if (preg_match('/^(www\.|https?:\/\/)/', $kw)) return 'navegacion';
    if (preg_match('/^\w+\.(cl|com|net|org)$/', $kw)) return 'navegacion';
    
    // Navegación: búsqueda de acceso a portales/sitios específicos
    $nav_patterns = [
        '/^(mi |mis |acceder a |ingresar a |portal |sucursal |oficina |fono |teléfono |telefono |llamar a |iniciar sesión|iniciar sesion|login)/',
        '/\b(oficinas?|sucursales?|teléfonos?|telefonos?|dirección|direccion|ubicación|ubicacion)\b/',
    ];
    foreach ($nav_patterns as $p) {
        if (preg_match($p, $kw)) return 'navegacion';
    }
    
    // Transaccional: intención clara de compra/contratación/acción
    $trans_patterns = [
        // Compra directa
        '/\b(comprar|contratar|cotizar|cotiza|precio|cuanto cuesta|cuánto cuesta|valor|costo|presupuesto|tarifa)\b/',
        '/\b(planes?|plan de|seguro|seguros|isapre)\b.*\b(precios?|cotizar|costo|valor|barato|baratos|económico|economico|mejor|comparar)\b/',
        '/\b(afilial|afiliarse|cambiarse|cambio de|traslado|trasladarse)\b/',
        '/\b(requisitos para|tramite|trámite|documentos para|como contratar|cómo contratar)\b/',
        '/\b(pre existencias?|preexistencia|enfermedades pre|declaración de salud|declaracion de salud)\b/',
        // Comparación pre-compra
        '/\b(mejor .{1,30} (isapre|seguro|plan)|cual es mejor|cual elegir|comparativa|comparador)\b/',
        '/\b(que isapre|qué isapre|cual isapre|cuál isapre) (elegir|contratar|conviene|recomiendan)\b/',
        // Precio
        '/\b(precio|costo|valor|tarifa|cuota).{1,20}(isapre|seguro|plan|salud)\b/',
        '/\b(isapre|seguro|plan).{1,20}(precio|costo|valor|tarifa|barato)\b/',
        // Acciones específicas
        '/\b(donde contratar|dónde contratar|donde cotizar|dónde cotizar|solicitar|pedir|obtener)\b/',
        '/\b(como cambiarse|cómo cambiarse|cambio de isapre|cambio de plan)\b/',
        '/\b(afiliar a mi|inscribir a mi|agregar a mi|incluir a mi)\b/',
        '/how much (for|does).*(insurance|health|plan)/',
        '/\b(buy|purchase|get|find|compare|switch).*(insurance|health|plan|coverage)\b/',
        '/\b(planes para|plan para).{1,30}\b/',
        '/\b(seguro para|isapre para).{1,30}\b/',
    ];
    
    foreach ($trans_patterns as $p) {
        if (preg_match($p, $kw)) return 'transaccional';
    }
    
    // Informativo: preguntas, definiciones, aprendizaje
    $info_patterns = [
        '/\b(que es|qué es|que son|qué son|como funciona|cómo funciona|como funcionan|cómo funcionan)\b/',
        '/\b(definición|definicion|concepto|explicación|explicacion|significa|significado)\b/',
        '/\b(diferencia entre|vs |versus|comparativa|comparacion|comparación|ventajas|desventajas)\b/',
        '/\b(ley|legislacion|legislación|normativa|regulacion|regulación|decreto|reforma)\b/',
        '/\b(historia|origen|trayectoria|ranking|top|mejores|peores)\b/',
        '/\b(noticias|novedades|aumento|alza|baja|UF|IPC|prima)\b/',
        '/\b(tipos de|clases de|categorias|categorías)\b/',
        '/\b(ejemplo|caso|experiencia|testimonio|opinión|opinion|review|reseña|resena)\b/',
        // Preguntas simples
        '/^como /',
        '/^cómo /',
        '/^que /',
        '/^qué /',
        '/^cual /',
        '/^cuál /',
        '/^cuando /',
        '/^cuándo /',
        '/^donde /',
        '/^dónde /',
        '/^por que /',
        '/^por qué /',
        '/\?$/',
    ];
    
    foreach ($info_patterns as $p) {
        if (preg_match($p, $kw)) return 'informativa';
    }
    
    // Default: si no matchea nada, es informativa (genérica)
    // Nombres de marca puros: navegacion
    $brands = ['banmédica','banmedica','colmena','consalud','cruz blanca','cruzblanca',
               'esencial','nueva masvida','nuevamasvida','masvida','más vida','mas vida',
               'vida tres','vidatres','fonasa','isapre','isapres'];
    $clean_kw = preg_replace('/[\.\-\s]/', '', $kw);
    foreach ($brands as $brand) {
        $clean_brand = preg_replace('/\s/', '', $brand);
        if ($clean_kw === $clean_brand) return 'navegacion';
    }
    
    return 'informativa';
}

$page_title = 'Clasificador de Keywords | Plan Salud Fácil';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">🔍 Clasificador de Keywords</h1>
        <p class="text-sm text-gray-500 mb-6">Pegá palabras clave (una por línea) y las clasifico en Transaccional, Informativa o Navegación.</p>
        
        <form method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
            <textarea name="keywords" rows="12" class="w-full border border-gray-300 rounded-xl p-4 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Pegá palabras clave, una por línea..."><?= htmlspecialchars(implode("\n", $keywords)) ?></textarea>
            <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition">Clasificar</button>
        </form>
        
        <?php if (!empty($results)): 
            $trans = array_filter($results, fn($r) => $r['tipo'] === 'transaccional');
            $info = array_filter($results, fn($r) => $r['tipo'] === 'informativa');
            $nav = array_filter($results, fn($r) => $r['tipo'] === 'navegacion');
        ?>
            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                    <div class="text-3xl font-extrabold text-green-700"><?= count($trans) ?></div>
                    <div class="text-sm text-green-600">Transaccionales 💰</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                    <div class="text-3xl font-extrabold text-blue-700"><?= count($info) ?></div>
                    <div class="text-sm text-blue-600">Informativas 📚</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center">
                    <div class="text-3xl font-extrabold text-gray-700"><?= count($nav) ?></div>
                    <div class="text-sm text-gray-600">Navegación 🧭</div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Keyword</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700 w-40">Clasificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $r): 
                            $badge = match($r['tipo']) {
                                'transaccional' => 'bg-green-100 text-green-800',
                                'informativa' => 'bg-blue-100 text-blue-800',
                                'navegacion' => 'bg-gray-100 text-gray-800',
                            };
                            $emoji = match($r['tipo']) {
                                'transaccional' => '💰',
                                'informativa' => '📚',
                                'navegacion' => '🧭',
                            };
                        ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2.5 px-4"><?= htmlspecialchars($r['keyword']) ?></td>
                            <td class="py-2.5 px-4"><span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium <?= $badge ?>"><?= $emoji ?> <?= ucfirst($r['tipo']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
