<?php
/**
 * Gestión de Incidentes Interna — Plan Salud Fácil
 * Acceso: /incidentes
 */

require_once __DIR__ . '/../omniflow_config.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) die("Error de conexión");
$db->set_charset("utf8mb4");

// ─── ACCIONES POST ───
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    if ($accion === 'crear' && !empty($_POST['titulo'])) {
        $stmt = $db->prepare("INSERT INTO incidentes (titulo, descripcion, categoria, criticidad, origen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $_POST['titulo'], $_POST['descripcion'], $_POST['categoria'], $_POST['criticidad'], $_POST['origen']);
        $stmt->execute(); $msg = '✅ Incidente creado'; $stmt->close();
    } elseif ($accion === 'actualizar' && !empty($_POST['id'])) {
        $stmt = $db->prepare("UPDATE incidentes SET estado=?, responsable=?, resolucion=? WHERE id=?");
        $stmt->bind_param("sssi", $_POST['estado'], $_POST['responsable'], $_POST['resolucion'], $_POST['id']);
        $stmt->execute();
        if ($_POST['estado'] === 'cerrado' || $_POST['estado'] === 'resuelto') {
            $db->query("UPDATE incidentes SET fecha_cierre=NOW() WHERE id=".intval($_POST['id']));
        }
        $msg = '✅ Actualizado'; $stmt->close();
    }
}

// ─── CONSULTA ───
$filtro = $_GET['filtro'] ?? 'abiertos';
$where = match($filtro) {
    'todos' => '1=1',
    'criticos' => "criticidad IN ('alta','critica')",
    default => "estado IN ('abierto','en_progreso')"
};
$result = $db->query("SELECT * FROM incidentes WHERE $where ORDER BY FIELD(estado,'abierto','en_progreso','resuelto','cerrado'), FIELD(criticidad,'critica','alta','media','baja'), fecha_creacion DESC");

// ─── ESTADÍSTICAS ───
$stats = $db->query("SELECT estado, COUNT(*) as c FROM incidentes GROUP BY estado")->fetch_all(MYSQLI_ASSOC);
$total = array_sum(array_column($stats, 'c'));
$abiertos = 0; foreach ($stats as $s) { if (in_array($s['estado'], ['abierto','en_progreso'])) $abiertos += $s['c']; }

$page_title = 'Incidentes | Plan Salud Fácil';
include __DIR__ . '/../layout/plantilla.php';
include __DIR__ . '/../layout/header.php';
?>

<div class="min-h-screen bg-gray-50 py-8">
<div class="max-w-6xl mx-auto px-4">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Incidentes</h1>
            <p class="text-sm text-gray-500 mt-1"><?= $abiertos ?> abiertos de <?= $total ?> total</p>
        </div>
        <button onclick="document.getElementById('form-crear').classList.toggle('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            + Nuevo Incidente
        </button>
    </div>

    <?php if ($msg): ?><div class="bg-green-50 text-green-700 px-4 py-2 rounded-lg mb-4 text-sm"><?= $msg ?></div><?php endif; ?>

    <!-- Formulario para crear -->
    <div id="form-crear" class="hidden bg-white rounded-xl shadow-sm p-6 mb-6 border">
        <h2 class="font-semibold text-gray-800 mb-4">Reportar incidente</h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="accion" value="crear">
            <div class="md:col-span-2"><input name="titulo" required placeholder="Título del incidente" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></div>
            <div class="md:col-span-2"><textarea name="descripcion" rows="2" placeholder="Descripción detallada" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea></div>
            <select name="categoria" class="px-3 py-2 border rounded-lg text-sm"><option value="bug">Bug</option><option value="mejora">Mejora</option><option value="tarea">Tarea</option><option value="riesgo">Riesgo</option></select>
            <select name="criticidad" class="px-3 py-2 border rounded-lg text-sm"><option value="media">Media</option><option value="baja">Baja</option><option value="alta">Alta</option><option value="critica">Crítica</option></select>
            <input name="origen" placeholder="Origen (URL, archivo...)" class="px-3 py-2 border rounded-lg text-sm">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">Crear</button>
        </form>
    </div>

    <!-- Filtros -->
    <div class="flex gap-2 mb-4">
        <?php foreach (['abiertos' => 'Abiertos', 'todos' => 'Todos', 'criticos' => 'Críticos'] as $k => $v): ?>
        <a href="?filtro=<?= $k ?>" class="px-3 py-1 rounded-full text-xs font-medium transition <?= $filtro === $k ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border hover:bg-gray-50' ?>"><?= $v ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Lista de incidentes -->
    <div class="space-y-3">
        <?php while ($inc = $result->fetch_assoc()): 
            $color_estado = match($inc['estado']) { 'abierto' => 'text-red-700 bg-red-50', 'en_progreso' => 'text-amber-700 bg-amber-50', 'resuelto' => 'text-green-700 bg-green-50', 'cerrado' => 'text-gray-500 bg-gray-100' };
            $color_cat = match($inc['categoria']) { 'bug' => '🔴', 'mejora' => '💡', 'tarea' => '📋', 'riesgo' => '⚠️' };
            $color_crit = match($inc['criticidad']) { 'critica' => 'border-l-4 border-red-500', 'alta' => 'border-l-4 border-amber-500', default => 'border-l-4 border-gray-200' };
        ?>
        <div class="bg-white rounded-lg shadow-sm p-4 <?= $color_crit ?> cursor-pointer" onclick="this.querySelector('.detalle').classList.toggle('hidden')">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="font-medium text-gray-800 text-sm"><?= $color_cat ?> <?= htmlspecialchars($inc['titulo']) ?></p>
                    <p class="text-xs text-gray-500 mt-1">#<?= $inc['id'] ?> · <?= $inc['origen'] ? htmlspecialchars($inc['origen']) : '—' ?> · <?= date('d/m/Y', strtotime($inc['fecha_creacion'])) ?></p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs px-2 py-1 rounded-full <?= $color_estado ?>"><?= ucfirst(str_replace('_',' ',$inc['estado'])) ?></span>
                </div>
            </div>
            <?php if ($inc['descripcion']): ?>
            <div class="detalle hidden mt-3 pt-3 border-t text-sm text-gray-600">
                <p><?= nl2br(htmlspecialchars($inc['descripcion'])) ?></p>
                <?php if ($inc['resolucion']): ?><p class="mt-2 text-green-700"><strong>Resolución:</strong> <?= htmlspecialchars($inc['resolucion']) ?></p><?php endif; ?>
                <?php if ($inc['responsable']): ?><p class="text-xs text-gray-400 mt-1">Responsable: <?= htmlspecialchars($inc['responsable']) ?></p><?php endif; ?>
                <?php if ($inc['estado'] !== 'cerrado'): ?>
                <form method="POST" class="mt-3 flex flex-wrap gap-2" onclick="event.stopPropagation()">
                    <input type="hidden" name="accion" value="actualizar">
                    <input type="hidden" name="id" value="<?= $inc['id'] ?>">
                    <select name="estado" class="px-2 py-1 border rounded text-xs">
                        <option <?= $inc['estado']==='abierto'?'selected':'' ?>>abierto</option>
                        <option <?= $inc['estado']==='en_progreso'?'selected':'' ?>>en_progreso</option>
                        <option <?= $inc['estado']==='resuelto'?'selected':'' ?>>resuelto</option>
                        <option <?= $inc['estado']==='cerrado'?'selected':'' ?>>cerrado</option>
                    </select>
                    <input name="responsable" placeholder="Responsable" value="<?= htmlspecialchars($inc['responsable'] ?? '') ?>" class="px-2 py-1 border rounded text-xs w-32">
                    <input name="resolucion" placeholder="Resolución" class="px-2 py-1 border rounded text-xs flex-1 min-w-[150px]">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">Actualizar</button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?><p class="text-gray-400 text-center py-8">No hay incidentes con este filtro.</p><?php endif; ?>
    </div>

</div>
</div>

<?php $db->close(); include __DIR__ . '/../layout/footer.php'; ?>
