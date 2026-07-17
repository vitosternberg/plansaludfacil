<?php
/**
 * Tabla interactiva de planes de ISAPRE — datos reales desde QuVi.cl
 * Acceso: /pages/planes/tabla-planes.php
 */

$csv_path = __DIR__ . '/../../adjuntos/planes_isapre.csv';

$planes = [];
if (($handle = fopen($csv_path, 'r')) !== false) {
    $headers = fgetcsv($handle, 0, ',', '"', '');
    while (($row = fgetcsv($handle, 0, ',', '"', '')) !== false) {
        if (count($row) >= 7) {
            $planes[] = [
                'isapre'   => trim($row[0]),
                'codigo'   => trim($row[1]),
                'nombre'   => trim($row[2]),
                'uf'       => trim($row[3]),
                'tope'     => trim($row[4]),
                'prest'    => trim($row[5]),
                'url'      => trim($row[6]),
            ];
        }
    }
    fclose($handle);
}

$total = count($planes);
$isapres = array_unique(array_column($planes, 'isapre'));
sort($isapres);
?>
<!DOCTYPE html>
<html lang="es-CL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes de ISAPRE — PlanSaludFácil</title>
    <link rel="stylesheet" href="/plansaludfacil/css/tailwind.min.css">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; }
        .sticky-top { position: sticky; top: 0; z-index: 10; }
        th { cursor: pointer; user-select: none; white-space: nowrap; }
        th:hover { background: #e2e8f0; }
        th .sort-arrow { opacity: 0.3; }
        th.sorted-asc .sort-arrow, th.sorted-desc .sort-arrow { opacity: 1; }
        tr:hover td { background: #f1f5f9; }
        .fade-in { animation: fadeIn 0.2s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        #loading { display: none; }
        .pagination-btn { min-width: 2.5rem; }
    </style>
</head>
<body class="min-h-screen">

<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Planes de ISAPRE</h1>
        <p class="text-sm text-gray-500 mt-1">
            <?= $total ?> planes · 7 ISAPREs · Datos actualizados julio 2026 desde <a href="https://quvi.cl" class="text-blue-600 underline" target="_blank">QuVi.cl</a>
        </p>
    </div>

    <!-- Controls -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4 flex flex-wrap gap-3 items-center sticky-top">
        <!-- Search -->
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="search" placeholder="Buscar por nombre, código o ISAPRE..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>

        <!-- ISAPRE filter -->
        <select id="filter-isapre" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Todas las ISAPREs</option>
            <?php foreach ($isapres as $is): ?>
                <option value="<?= htmlspecialchars($is) ?>"><?= htmlspecialchars($is) ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Prestadores filter -->
        <select id="filter-prest" class="px-4 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">Cualquier red</option>
            <option value="0-5">0–5 prestadores</option>
            <option value="6-15">6–15 prestadores</option>
            <option value="16-30">16–30 prestadores</option>
            <option value="31+">31+ prestadores</option>
        </select>

        <!-- Rows per page -->
        <select id="per-page" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="25">25</option>
            <option value="50" selected>50</option>
            <option value="100">100</option>
            <option value="250">250</option>
        </select>

        <!-- Count -->
        <span id="result-count" class="text-sm text-gray-500 whitespace-nowrap"></span>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="planes-table">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700" data-sort="isapre">
                            ISAPRE <span class="sort-arrow">▾</span>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700" data-sort="nombre">
                            Nombre del Plan <span class="sort-arrow">▾</span>
                        </th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700" data-sort="codigo">
                            Código <span class="sort-arrow">▾</span>
                        </th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700" data-sort="uf">
                            UF/mes <span class="sort-arrow">▾</span>
                        </th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700" data-sort="tope">
                            Tope Anual UF <span class="sort-arrow">▾</span>
                        </th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700" data-sort="prest">
                            Prestadores <span class="sort-arrow">▾</span>
                        </th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-700">Link</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 flex flex-wrap items-center justify-between gap-3" id="pagination-container">
            <div class="text-sm text-gray-500" id="page-info"></div>
            <div class="flex gap-1" id="pagination-btns"></div>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-xs text-gray-400 mt-4 text-center">
        Datos obtenidos de QuVi.cl · Precios en UF · No constituye asesoría legal o financiera
    </p>
</div>

<script>
// ─── DATA ───
const ALL_PLANES = <?= json_encode($planes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const ISAPRE_COLORS = {
    'Banmédica':    'bg-blue-100 text-blue-800',
    'Colmena':      'bg-yellow-100 text-yellow-800',
    'Consalud':     'bg-green-100 text-green-800',
    'Cruz Blanca':  'bg-indigo-100 text-indigo-800',
    'Esencial':     'bg-purple-100 text-purple-800',
    'Nueva Masvida': 'bg-pink-100 text-pink-800',
    'Vida Tres':    'bg-red-100 text-red-800',
};

// ─── STATE ───
let sortCol = 'uf';
let sortDir = 'asc';
let currentPage = 1;
let perPage = 50;

function parseNum(s) { return parseFloat(s.replace(',', '.')) || 0; }

function filterPlanes() {
    const search = document.getElementById('search').value.toLowerCase();
    const isapre = document.getElementById('filter-isapre').value;
    const prest = document.getElementById('filter-prest').value;

    let filtered = ALL_PLANES;

    if (search) {
        filtered = filtered.filter(p =>
            p.nombre.toLowerCase().includes(search) ||
            p.codigo.toLowerCase().includes(search) ||
            p.isapre.toLowerCase().includes(search)
        );
    }
    if (isapre) {
        filtered = filtered.filter(p => p.isapre === isapre);
    }
    if (prest) {
        const pv = parseNum(ALL_PLANES[0]?.prest || '0');
        filtered = filtered.filter(p => {
            const v = parseInt(p.prest) || 0;
            switch (prest) {
                case '0-5': return v <= 5;
                case '6-15': return v >= 6 && v <= 15;
                case '16-30': return v >= 16 && v <= 30;
                case '31+': return v >= 31;
            }
            return true;
        });
    }

    return filtered;
}

function sortPlanes(planes) {
    return [...planes].sort((a, b) => {
        let va, vb;
        switch (sortCol) {
            case 'isapre': va = a.isapre; vb = b.isapre; break;
            case 'nombre': va = a.nombre; vb = b.nombre; break;
            case 'codigo': va = a.codigo; vb = b.codigo; break;
            case 'uf':      va = parseNum(a.uf); vb = parseNum(b.uf); break;
            case 'tope':    va = parseNum(a.tope); vb = parseNum(b.tope); break;
            case 'prest':   va = parseInt(a.prest) || 0; vb = parseInt(b.prest) || 0; break;
            default: return 0;
        }
        if (va < vb) return sortDir === 'asc' ? -1 : 1;
        if (va > vb) return sortDir === 'asc' ? 1 : -1;
        return 0;
    });
}

function render() {
    const filtered = filterPlanes();
    const sorted = sortPlanes(filtered);
    const total = sorted.length;
    const totalPages = Math.ceil(total / perPage);
    if (currentPage > totalPages) currentPage = Math.max(1, totalPages);

    const start = (currentPage - 1) * perPage;
    const pageItems = sorted.slice(start, start + perPage);

    // Update count
    document.getElementById('result-count').textContent =
        total === ALL_PLANES.length
            ? `Mostrando ${total} planes`
            : `${total} de ${ALL_PLANES.length} planes`;

    // Table body
    const tbody = document.getElementById('tbody');
    let html = '';
    pageItems.forEach(p => {
        const badge = ISAPRE_COLORS[p.isapre] || 'bg-gray-100 text-gray-800';
        html += `
            <tr class="border-b border-gray-100 fade-in">
                <td class="px-4 py-2.5"><span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium ${badge}">${escHtml(p.isapre)}</span></td>
                <td class="px-4 py-2.5 font-medium text-gray-900">${escHtml(p.nombre)}</td>
                <td class="px-4 py-2.5 text-gray-500 font-mono text-xs">${escHtml(p.codigo)}</td>
                <td class="px-4 py-2.5 text-right tabular-nums text-gray-900">${escHtml(p.uf)}</td>
                <td class="px-4 py-2.5 text-right tabular-nums text-gray-600">${escHtml(p.tope)}</td>
                <td class="px-4 py-2.5 text-right tabular-nums text-gray-600">${escHtml(p.prest)}</td>
                <td class="px-4 py-2.5 text-center">
                    <a href="${escHtml(p.url)}" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-xs">QuVi ↗</a>
                </td>
            </tr>`;
    });
    tbody.innerHTML = html;

    // Pagination
    document.getElementById('page-info').textContent =
        total === 0 ? 'Sin resultados' :
        `Mostrando ${start + 1}–${Math.min(start + perPage, total)} de ${total}`;

    const pBtns = document.getElementById('pagination-btns');
    let btnHtml = '';
    if (totalPages > 1) {
        btnHtml += `<button class="pagination-btn px-3 py-1.5 rounded-lg border text-sm ${currentPage === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" ${currentPage === 1 ? 'disabled' : ''} onclick="goPage(${currentPage - 1})">«</button>`;
        for (let p = 1; p <= totalPages; p++) {
            if (totalPages <= 10 || p === 1 || p === totalPages || Math.abs(p - currentPage) <= 2) {
                btnHtml += `<button class="pagination-btn px-3 py-1.5 rounded-lg border text-sm ${p === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 hover:bg-gray-100'}" onclick="goPage(${p})">${p}</button>`;
            } else if (p === 2 || p === totalPages - 1) {
                btnHtml += `<span class="px-1 text-gray-400">…</span>`;
            }
        }
        btnHtml += `<button class="pagination-btn px-3 py-1.5 rounded-lg border text-sm ${currentPage === totalPages ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" ${currentPage === totalPages ? 'disabled' : ''} onclick="goPage(${currentPage + 1})">»</button>`;
    }
    pBtns.innerHTML = btnHtml;

    // Sort header markers
    document.querySelectorAll('th').forEach(th => {
        th.classList.remove('sorted-asc', 'sorted-desc');
        if (th.dataset.sort === sortCol) {
            th.classList.add(sortDir === 'asc' ? 'sorted-asc' : 'sorted-desc');
        }
    });
}

function goPage(p) { currentPage = p; render(); window.scrollTo({ top: 0, behavior: 'smooth' }); }

function escHtml(s) {
    const div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
}

// ─── Events ───
document.getElementById('search').addEventListener('input', debounce(() => { currentPage = 1; render(); }, 200));
document.getElementById('filter-isapre').addEventListener('change', () => { currentPage = 1; render(); });
document.getElementById('filter-prest').addEventListener('change', () => { currentPage = 1; render(); });
document.getElementById('per-page').addEventListener('change', (e) => { perPage = parseInt(e.target.value); currentPage = 1; render(); });

document.querySelectorAll('th').forEach(th => {
    th.addEventListener('click', () => {
        const col = th.dataset.sort;
        if (sortCol === col) {
            sortDir = sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            sortCol = col;
            sortDir = col === 'uf' ? 'asc' : 'asc';
        }
        render();
    });
});

function debounce(fn, ms) { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); }; }

// ─── Init ───
render();
</script>
</body>
</html>
