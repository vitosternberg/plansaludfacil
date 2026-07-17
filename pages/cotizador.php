<?php
/**
 * Cotizador Interactivo — PlanSaludFácil
 * Sugiere planes en tiempo real según edad, renta, cargas e intereses.
 * URL: /plansaludfacil/pages/cotizador.php
 */
$page_title = 'Cotizador de Planes | Plan Salud Fácil';
require_once __DIR__ . '/../config.php';
include __DIR__ . '/../layout/header.php';
?>

<style>
    .input-focus:focus { box-shadow: 0 0 0 3px rgba(0,210,255,0.3); }
    .score-ring { 
        width: 64px; height: 64px; 
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.2rem;
    }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.12); }
    .loading-dots::after { content: ''; animation: dots 1.5s steps(4,end) infinite; }
    @keyframes dots { 0% { content: ''; } 25% { content: '.'; } 50% { content: '..'; } 75% { content: '...'; } }
    .slide-in { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
<div class="max-w-6xl mx-auto px-4 py-8">

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">
            <span class="bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">Cotizador</span> de Planes de Salud
        </h1>
        <p class="text-gray-500 text-lg">Completá tus datos y te mostramos los mejores planes para vos — entre <strong>2,231 opciones reales</strong></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <!-- ── FORM PANEL ── -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-4">
                <h2 class="font-bold text-lg text-gray-800 mb-5 flex items-center gap-2">
                    <iconify-icon icon="mdi:account-details" class="text-blue-600" width="22"></iconify-icon>
                    Tus Datos
                </h2>

                <!-- Edad -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Edad</label>
                    <div class="flex items-center gap-3">
                        <input type="range" id="edad-slider" min="18" max="80" value="30" 
                               class="flex-1 accent-blue-600 h-2 rounded-lg cursor-pointer">
                        <span id="edad-value" class="w-10 text-center font-bold text-blue-600 text-lg">30</span>
                    </div>
                </div>

                <!-- Renta -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Renta Líquida Mensual</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                        <input type="text" id="renta" value="1.300.000" 
                               class="input-focus w-full pl-8 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 font-medium outline-none transition-colors">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Tu 7% legal: <span id="siete-pct" class="font-semibold text-blue-600">$91.000</span></p>
                </div>

                <!-- Cargas -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cargas</label>
                    <div class="flex gap-2">
                        <?php foreach ([0 => 'Sin cargas', 1 => '1', 2 => '2', 3 => '3+'] as $v => $l): ?>
                        <button type="button" class="carga-btn flex-1 py-2.5 rounded-xl text-sm font-medium border-2 transition-all
                            <?= $v === 0 ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' ?>"
                            data-cargas="<?= $v ?>"><?= $l ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Intereses -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">¿Qué coberturas te importan más?</label>
                    <div class="flex flex-wrap gap-2">
                        <?php 
                        $all_interests = [
                            'Hospitalización' => '🏥', 'Atención Ambulatoria' => '🩺',
                            'Maternidad' => '👶', 'Kinesiología y Deporte' => '🏃',
                            'Telemedicina' => '📱', 'Dental' => '🦷',
                            'Salud Mental' => '🧠', 'Farmacia' => '💊',
                        ];
                        foreach ($all_interests as $int => $emoji): ?>
                        <button type="button" class="interes-btn px-3 py-2 rounded-xl text-sm border-2 transition-all
                            <?= in_array($int, ['Hospitalización','Atención Ambulatoria']) ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' ?>"
                            data-interes="<?= htmlspecialchars($int) ?>"><?= $emoji ?> <?= htmlspecialchars($int) ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cotizar button -->
                <button id="cotizar-btn" onclick="cotizar()" 
                        class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-bold rounded-xl hover:from-blue-700 hover:to-cyan-600 transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-2 text-lg">
                    <iconify-icon icon="mdi:magnify" width="20"></iconify-icon>
                    Buscar Mejores Planes
                </button>

                <p class="text-xs text-gray-400 text-center mt-3">
                    Evaluando 2,231 planes de 7 ISAPREs · Resultados en ~2 segundos
                </p>
            </div>
        </div>

        <!-- ── RESULTS PANEL ── -->
        <div class="lg:col-span-3" id="results-area">
            
            <!-- Empty state -->
            <div id="empty-state" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <iconify-icon icon="mdi:shield-search" class="text-gray-300" width="64"></iconify-icon>
                <h3 class="text-xl font-bold text-gray-700 mt-4 mb-2">Tus recomendaciones aparecerán aquí</h3>
                <p class="text-gray-400">Ajustá tus datos y presioná <strong>"Buscar Mejores Planes"</strong> para ver los planes más afines a tu perfil.</p>
            </div>

            <!-- Loading -->
            <div id="loading-state" class="hidden bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="inline-block w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                <p class="text-gray-500 font-medium">Analizando <span class="loading-dots"></span></p>
                <p class="text-xs text-gray-400 mt-1">Comparando 2,231 planes contra tu perfil</p>
            </div>

            <!-- Results -->
            <div id="results-container" class="hidden space-y-4"></div>

            <!-- Error -->
            <div id="error-state" class="hidden bg-red-50 rounded-2xl border border-red-200 p-8 text-center">
                <iconify-icon icon="mdi:alert-circle" class="text-red-400" width="40"></iconify-icon>
                <p class="text-red-700 font-medium mt-2">No pudimos calcular las recomendaciones</p>
                <p class="text-red-400 text-sm mt-1" id="error-msg"></p>
            </div>
        </div>
    </div>
</div>
</div>

<script>
// ─── State ───
let cotizando = false;
const ISAPRE_COLORS = {
    'Banmédica':    {bg:'bg-blue-600',     ring:'#2563eb'},
    'Colmena':      {bg:'bg-yellow-500',   ring:'#eab308'},
    'Consalud':     {bg:'bg-green-600',    ring:'#16a34a'},
    'Cruz Blanca':  {bg:'bg-indigo-600',   ring:'#4f46e5'},
    'Esencial':     {bg:'bg-purple-600',   ring:'#9333ea'},
    'Nueva Masvida':{bg:'bg-pink-600',     ring:'#db2777'},
    'Vida Tres':    {bg:'bg-red-600',      ring:'#dc2626'},
};

// ─── UI State ───
function showState(id) {
    ['empty-state','loading-state','results-container','error-state'].forEach(s => {
        document.getElementById(s).classList.toggle('hidden', s !== id);
    });
}

// ─── Formatters ───
function fmtCLP(n) { return n.toLocaleString('es-CL'); }
function parseCLP(s) { return parseInt(s.replace(/\D/g,'')) || 500000; }

// ─── Events ───
document.getElementById('edad-slider').addEventListener('input', e => {
    document.getElementById('edad-value').textContent = e.target.value;
});

document.getElementById('renta').addEventListener('input', e => {
    const renta = parseCLP(e.target.value);
    document.getElementById('siete-pct').textContent = '$' + fmtCLP(Math.round(renta * 0.07));
});

document.querySelectorAll('.carga-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.carga-btn').forEach(b => {
            b.classList.remove('border-blue-600','bg-blue-50','text-blue-700');
            b.classList.add('border-gray-200','bg-white','text-gray-600');
        });
        btn.classList.remove('border-gray-200','bg-white','text-gray-600');
        btn.classList.add('border-blue-600','bg-blue-50','text-blue-700');
    });
});

document.querySelectorAll('.interes-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.classList.toggle('border-blue-600');
        btn.classList.toggle('bg-blue-50');
        btn.classList.toggle('text-blue-700');
        btn.classList.toggle('border-gray-200');
        btn.classList.toggle('bg-white');
        btn.classList.toggle('text-gray-600');
    });
});

// ─── Cotizar ───
async function cotizar() {
    if (cotizando) return;
    cotizando = true;
    const btn = document.getElementById('cotizar-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span> Analizando...';

    showState('loading-state');

    const edad = parseInt(document.getElementById('edad-slider').value);
    const renta = parseCLP(document.getElementById('renta').value);
    const cargas = parseInt(document.querySelector('.carga-btn.border-blue-600')?.dataset?.cargas || 0);
    const intereses = Array.from(document.querySelectorAll('.interes-btn.border-blue-600')).map(b => b.dataset.interes);

    const lead = {
        nombre: 'Usuario Cotizador',
        edad, renta, cargas,
        uf_value: 38500,
        intereses: intereses.length > 0 ? intereses : ['Hospitalización', 'Atención Ambulatoria']
    };

    const API_URL = '<?php echo rtrim(BASE_URL, '/'); ?>/api/cotizar.php';
    try {
        const resp = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(lead)
        });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        if (data.error) throw new Error(data.error);
        renderResults(data);
        showState('results-container');
    } catch (err) {
        document.getElementById('error-msg').textContent = err.message;
        showState('error-state');
    } finally {
        cotizando = false;
        btn.disabled = false;
        btn.innerHTML = '<iconify-icon icon="mdi:refresh" width="20"></iconify-icon> Actualizar Resultados';
    }
}

// ─── Render ───
function renderResults(data) {
    const container = document.getElementById('results-container');
    const recs = data.recomendaciones || [];
    const sietePct = data.cotizacion_legal_7pct;

    if (recs.length === 0) {
        container.innerHTML = '<div class="bg-white rounded-2xl shadow-sm border p-8 text-center text-gray-500">No se encontraron planes para tu perfil.</div>';
        return;
    }

    let html = '';

    recs.forEach((rec, i) => {
        const plan = rec;
        const colors = ISAPRE_COLORS[plan.isapre] || {bg:'bg-gray-600', ring:'#6b7280'};
        const scoreColor = plan.score >= 80 ? '#16a34a' : plan.score >= 70 ? '#eab308' : '#dc2626';
        
        // Calculate ring dasharray for score circle
        const circumference = 2 * Math.PI * 28;
        const dashOffset = circumference * (1 - plan.score / 100);

        html += `
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 card-hover transition-all slide-in" style="animation-delay: ${i * 0.1}s">
            <div class="flex flex-col sm:flex-row gap-4">
                <!-- ISAPRE badge + score ring -->
                <div class="flex items-center gap-3 sm:w-48">
                    <div class="relative score-ring flex-shrink-0">
                        <svg width="64" height="64" class="absolute inset-0 -rotate-90">
                            <circle cx="32" cy="32" r="28" fill="none" stroke="#e5e7eb" stroke-width="5"/>
                            <circle cx="32" cy="32" r="28" fill="none" stroke="${scoreColor}" stroke-width="5"
                                    stroke-dasharray="${circumference}" stroke-dashoffset="${dashOffset}"
                                    stroke-linecap="round"/>
                        </svg>
                        <span class="relative z-10 text-gray-800">${plan.score}</span>
                    </div>
                    <div>
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold text-white ${colors.bg}">${plan.isapre}</span>
                        <p class="text-xs text-gray-400 mt-1">${plan.prestadores} prestadores</p>
                    </div>
                </div>

                <!-- Plan info -->
                <div class="flex-1">
                    <h4 class="font-bold text-gray-900 text-lg leading-tight">${escHtml(plan.nombre)}</h4>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-xs text-gray-500">
                        <span>📋 Código: ${plan.codigo}</span>
                        <span>💰 ${plan.uf} UF</span>
                        <span>📊 Tope: ${plan.tope_anual_uf} UF</span>
                    </div>
                    ${plan.razones && plan.razones.length > 0 ? `
                    <div class="mt-2 space-y-1">
                        ${plan.razones.slice(0, 3).map(r => `<p class="text-xs text-gray-600 flex items-start gap-1"><span class="text-green-500 flex-shrink-0">✓</span> ${escHtml(r)}</p>`).join('')}
                    </div>` : ''}
                </div>

                <!-- Price -->
                <div class="sm:text-right flex sm:block items-center gap-2 sm:gap-0">
                    <p class="text-2xl font-extrabold text-gray-900">$${fmtCLP(plan.precio_clp)}</p>
                    <p class="text-xs text-gray-400">/mes</p>
                    ${plan.precio_clp <= sietePct ? 
                        '<span class="inline-block mt-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">✓ Cubierto por tu 7%</span>' :
                        '<span class="inline-block mt-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">+$' + fmtCLP(plan.precio_clp - sietePct) + ' adicional</span>'
                    }
                    <a href="${plan.url}" target="_blank" class="block mt-2 text-xs text-blue-600 hover:underline">Ver en QuVi ↗</a>
                </div>
            </div>
            ${i === 0 ? '<div class="mt-3 pt-3 border-t border-gray-100"><span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600">🏆 Plan Más Afín a tu Perfil</span></div>' : ''}
        </div>`;
    });

    container.innerHTML = html;
}

function escHtml(s) {
    const div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
}

// ─── Auto-run on load ───
document.addEventListener('DOMContentLoaded', () => { cotizar(); });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
