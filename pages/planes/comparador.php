<?php
/**
 * comparador-isapres.php — Comparador con datos reales de queplan.cl
 * Keyword target: "comparador isapres", "comparador planes isapre"
 *
 * Usa core/data_isapres.php como fuente de verdad.
 */
require_once __DIR__ . '/../../omniflow_config.php';
require_once __DIR__ . '/../../core/data_isapres.php';

$page_title       = 'Comparador de Isapres: Precios Reales 2026 | Plan Salud Fácil';
$meta_description = 'Compara isapres con precios reales de queplan.cl. Responde 3 preguntas y ve el precio real de cada isapre para tu perfil.';
$h1               = 'Comparador de Isapres';
$lead             = 'Responde 3 preguntas y te mostramos los precios reales de cada isapre para tu perfil, según datos de queplan.cl (julio 2026).';
$svc_name         = 'Comparador de Isapres';
$svc_description  = 'Compara precios reales de isapre según tu edad y cargas. Datos actualizados de Banmédica, Colmena, Cruz Blanca, Consalud, Esencial, Nueva MasVida y Vida Tres.';
$cta_texto        = 'Quiero una asesoría';
$cta_link         = 'https://wa.me/56952282339';

$breadcrumbs = [
    ['label' => 'Inicio', 'url' => 'BASE_URL/'],
    ['label' => 'Planes', 'url' => 'BASE_URL/planes/'],
    ['label' => 'Comparador', 'url' => '#']
];
foreach ($breadcrumbs as &$bc) { $bc['url'] = str_replace('BASE_URL/', BASE_URL . '/', $bc['url']); } unset($bc);

$toc_items = [
    ['id' => 'comparador', 'label' => 'Cotiza aquí'],
    ['id' => 'resultados', 'label' => 'Tus resultados'],
];

ob_start();
?>

<!-- COTIZADOR -->
<section id="comparador" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28">
<div class="bg-white rounded-2xl shadow-lg p-8 border">
<h2 class="text-xl font-bold text-gray-900 mb-2">Responde 3 preguntas</h2>
<p class="text-gray-500 text-sm mb-6">30 segundos. Precios reales de queplan.cl.</p>

<div class="space-y-6" id="quiz">
    <!-- P1: Renta -->
    <div>
        <label class="block font-semibold text-gray-700 mb-2">1. ¿Cuál es tu renta líquida mensual? (sin puntos)</label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">$</span>
            <input type="text" id="renta-input" maxlength="8" placeholder="Ej: 1500000" 
                   class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 font-medium text-lg outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                   oninput="this.value = this.value.replace(/[^0-9]/g,'')">
        </div>
        <p class="text-xs text-gray-400 mt-1">Tu 7% legal: <span id="siete-pct-label" class="font-semibold text-blue-600">$0</span></p>
    </div>
    <!-- P2: Edad -->
    <div>
        <label class="block font-semibold text-gray-700 mb-2">2. ¿Qué edad tienes? (18–65)</label>
        <input type="number" id="edad-input" min="18" max="65" maxlength="2" placeholder="Ej: 30"
               class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 font-medium text-lg outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
               oninput="if(this.value<18)this.value=18;if(this.value>65)this.value=65">
    </div>
    <!-- P3: Cargas -->
    <div>
        <label class="block font-semibold text-gray-700 mb-2">3. ¿Tienes cargas?</label>
        <select id="cargas-input" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 font-medium text-lg outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" onchange="toggleCargaEdad()">
            <option value="0">No, solo yo</option>
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?> carga<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <!-- P3b: Edad de cada carga (dinámico según cantidad) -->
    <div id="carga-edad-group" style="display:none">
        <label class="block font-semibold text-gray-700 mb-2">Edad de cada carga</label>
        <div id="carga-edad-inputs" class="space-y-2"></div>
    </div>
    <button onclick="calcular()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl transition text-lg">Comparar precios reales</button>
    <script>
        function toggleCargaEdad() {
            const n = parseInt(document.getElementById('cargas-input').value) || 0;
            const g = document.getElementById('carga-edad-group');
            const c = document.getElementById('carga-edad-inputs');
            if (n === 0) { g.style.display = 'none'; c.innerHTML = ''; return; }
            g.style.display = 'block';
            let h = '';
            for (let i = 1; i <= n; i++) {
                h += '<input type="number" maxlength="2" class="carga-edad-val w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 font-medium text-lg outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" min="0" max="80" placeholder="Edad carga ' + i + '">';
            }
            c.innerHTML = h;
        }
    </script>
</div>
</div>
</section>

<!-- RESULTADOS -->
<section id="resultados" class="max-w-4xl mx-auto px-4 py-10 scroll-mt-28 hidden">
<h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Tus mejores planes</h2>
<p class="text-gray-500 text-sm mb-6" id="resultados-subtitulo"></p>

<div id="resultados-container" class="space-y-4"></div>
</section>

<script>

// ─── ESTADO ───
let cotizando = false;

// 7% en tiempo real al escribir renta
document.getElementById('renta-input').addEventListener('input', function() {
    const renta = parseInt(this.value) || 0;
    document.getElementById('siete-pct-label').textContent = '$' + Math.round(renta * 0.07).toLocaleString();
});

function getIntereses() {
    const checks = document.querySelectorAll('.interes-check:checked');
    if (checks.length === 0) return ['Hospitalización','Atención Ambulatoria'];
    return Array.from(checks).map(c => c.value);
}

async function calcular() {
    const renta  = parseInt(document.getElementById('renta-input').value) || 0;
    const edad   = parseInt(document.getElementById('edad-input').value) || 0;
    const cargas = parseInt(document.getElementById('cargas-input').value);
    const cargaEdades = Array.from(document.querySelectorAll('.carga-edad-val')).map(el => parseInt(el.value)||10);
    
    if (renta < 100000 || edad < 18 || edad > 65 || isNaN(cargas) || cargas < 0) {
        alert('Completa los campos: renta (mín $100.000), edad (18–65) y cargas');
        return;
    }
    if (cotizando) return;
    cotizando = true;

    const btn = document.querySelector('#quiz button');
    if (btn) { btn.disabled = true; btn.textContent = 'Analizando 2,231 planes...'; }

    const pct7   = Math.round(renta * 0.07);
    const categoria = cargas === 0 ? 'individual' : (cargas === 1 ? 'pareja' : 'familia');
    const edadLabel = edad + ' años';

    const lead = {
        nombre: 'Usuario Comparador',
        edad, renta, cargas,
        uf_value: 38500,
        intereses: getIntereses(),
        edad_cargas: cargas > 0 ? cargaEdades.slice(0, cargas) : []
    };

    const API_URL = '<?php echo rtrim(BASE_URL, '/'); ?>/api/cotizar.php';
    try {
        const resp = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(lead)
        });
        const data = await resp.json();
        if (data.error) throw new Error(data.error);
        renderResultados(data, renta, pct7, edadLabel, categoria, edad, cargas);
    } catch (err) {
        document.getElementById('resultados-container').innerHTML = 
            '<div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center text-red-700">Error al calcular: ' + err.message + '</div>';
        document.getElementById('resultados').classList.remove('hidden');
    } finally {
        cotizando = false;
        if (btn) { btn.disabled = false; btn.textContent = 'Comparar precios reales'; }
    }
}

function renderResultados(data, renta, pct7, edadLabel, categoria, edad, cargas) {
    const recs = data.recomendaciones || [];
    if (recs.length === 0) {
        document.getElementById('resultados-container').innerHTML = '<p class="text-gray-500 text-center py-8">No hay resultados para este perfil.</p>';
        document.getElementById('resultados').classList.remove('hidden');
        return;
    }

    document.getElementById('resultados-subtitulo').textContent =
        `Perfil: ${edadLabel}, ${categoria} · Renta: $${renta.toLocaleString()} · 7% legal: $${pct7.toLocaleString()} · ${data.total_planes_evaluados} planes evaluados`;

    const ISAPRE_COLORS = {
        'Banmédica':'bg-blue-600','Colmena':'bg-yellow-500','Consalud':'bg-green-600',
        'Cruz Blanca':'bg-indigo-600','Esencial':'bg-purple-600','Nueva Masvida':'bg-pink-600','Vida Tres':'bg-red-600'
    };

    // CTA links (antes del loop para que las cards los usen)
    const esFamiliar = cargas > 0;
    const formUrl = esFamiliar 
        ? '<?php echo rtrim(BASE_URL, '/'); ?>/planes/familiares/con-cargas/?age=' + edad + '&income=' + renta + '&cargas=' + cargas
        : '<?php echo rtrim(BASE_URL, '/'); ?>/planes/individuales/adulto/?age=' + edad + '&income=' + renta + '&cargas=0';
    const formLabel = esFamiliar ? 'Plan Familiar' : 'Plan Individual';

    let html = '';
    const labels = ['🥇 Mejor Afinidad', '🥈', '🥉'];

    recs.forEach((rec, i) => {
        const plan = rec;
        const dentro = plan.precio_clp <= pct7;
        const diff = plan.precio_clp - pct7;
        const bg = ISAPRE_COLORS[plan.isapre] || 'bg-gray-600';
        const scoreColor = plan.score >= 80 ? '#16a34a' : plan.score >= 70 ? '#eab308' : '#dc2626';
        const circumference = 2 * Math.PI * 26;
        const dashOffset = circumference * (1 - plan.score / 100);

        html += `
        <div class="bg-white border rounded-xl p-5 shadow-sm hover:shadow-md transition ${i===0 ? 'border-blue-400 ring-2 ring-blue-100' : ''}">
            <div class="flex items-center gap-3 mb-3">
                <span class="${i===0 ? 'bg-blue-600' : i===1 ? 'bg-emerald-500' : 'bg-teal-500'} text-white text-xs font-bold px-3 py-1 rounded-full">${labels[i] || ''}</span>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold text-white ${bg}">${plan.isapre}</span>
                <span class="text-xs text-gray-400">${plan.prestadores} prestadores · ${plan.uf} UF</span>
            </div>
            <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                <div class="flex-1">
                    <p class="font-bold text-gray-900 text-lg">${escHtml(plan.nombre)}</p>
                    <p class="text-gray-500 text-sm">Plan ${categoria} · Código: ${plan.codigo}</p>
                    <div class="flex gap-4 mt-2 text-sm flex-wrap">
                        <span class="text-gray-600">📊 Tope anual: <strong>${plan.tope_anual_uf} UF</strong></span>
                        <span class="text-gray-600">📋 ${plan.prestadores} prestadores</span>
                    </div>
                    ${plan.razones && plan.razones.length > 0 ? `
                    <div class="mt-2 space-y-0.5">
                        ${plan.razones.slice(0, 2).map(r => `<p class="text-xs text-gray-500">✓ ${escHtml(r)}</p>`).join('')}
                    </div>` : ''}
                    <a href="${formUrl}" class="inline-flex items-center gap-1 mt-2 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition">
                        📋 Completar datos para ${formLabel}
                    </a>
                </div>
                <div class="flex items-center gap-4 md:block md:text-right">
                    <div class="relative" style="width:58px;height:58px">
                        <svg width="58" height="58" class="-rotate-90">
                            <circle cx="29" cy="29" r="26" fill="none" stroke="#e5e7eb" stroke-width="4"/>
                            <circle cx="29" cy="29" r="26" fill="none" stroke="${scoreColor}" stroke-width="4"
                                    stroke-dasharray="${circumference}" stroke-dashoffset="${dashOffset}" stroke-linecap="round"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-sm font-extrabold text-gray-800">${plan.score}</span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">${dentro ? 'Dentro de tu 7%' : 'Cotización Adicional'}</p>
                        <p class="text-2xl font-extrabold text-gray-900">$${plan.precio_clp.toLocaleString()}<span class="text-sm font-normal text-gray-400">/mes</span></p>
                        ${dentro 
                            ? '<p class="text-xs text-green-600">✓ Cubierto por tu cotización legal</p>' 
                            : `<p class="text-xs text-amber-600">+$${diff.toLocaleString()} extra (descuento vía empleador)</p>`
                        }
                    </div>
                </div>
            </div>
        </div>`;
    });

    // ── Advertencia si ningún plan cabe en el 7% ──
    const planesDentro = recs.filter(r => r.precio_clp <= pct7).length;
    if (planesDentro === 0) {
        html += `
        <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm">
            <p class="font-bold text-amber-800 mb-2">⚠️ Ninguno de los planes cabe en tu 7% legal</p>
            <p class="text-amber-700 mb-2">Con tu renta de $${renta.toLocaleString()} (7% = $${pct7.toLocaleString()}) y ${cargas} carga(s), todos los planes requieren <strong>Cotización Adicional Voluntaria</strong>. Esta diferencia se descuenta automáticamente de tu sueldo por tu empleador.</p>
            <p class="text-amber-700 text-xs">Opciones: <strong>1)</strong> Aceptar la cotización adicional · <strong>2)</strong> Buscar un plan compensado (dos cotizantes) · <strong>3)</strong> Revisar si tus cargas califican como cotizantes independientes · <strong>4)</strong> Acercarte a la Isapre para renegociar (Ley Corta obliga a ofrecer alternativas).</p>
        </div>`;
    } else if (planesDentro < 3) {
        html += `
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
            💡 Solo ${planesDentro} de 5 planes caben dentro de tu 7%. Los demás requieren <strong>Cotización Adicional Voluntaria</strong> (descuento por empleador). Si querés evitar el pago extra, considerá un plan más económico o revisá tus cargas.
        </div>`;
    }

    // Tabla comparativa
    html += `
    <div class="mt-6 bg-white border rounded-xl overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b">
            <p class="font-semibold text-gray-700 text-sm">Comparativa — 5 planes más afines</p>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 text-xs">
                <tr>
                    <th class="p-3 text-left">Plan</th>
                    <th class="p-3 text-left">ISAPRE</th>
                    <th class="p-3 text-right">UF/mes</th>
                    <th class="p-3 text-right">CLP/mes</th>
                    <th class="p-3 text-right">Prest.</th>
                    <th class="p-3 text-right">Score</th>
                    <th class="p-3">7%</th>
                </tr>
            </thead>
            <tbody class="divide-y">`;
    recs.forEach((rec, i) => {
        const dentro = rec.precio_clp <= pct7;
        html += `
            <tr class="${i < 3 ? 'bg-blue-50/50' : ''}">
                <td class="p-3 font-medium max-w-[240px] truncate" title="${escHtml(rec.nombre)}">${escHtml(rec.nombre)}</td>
                <td class="p-3">${rec.isapre}</td>
                <td class="p-3 text-right">${rec.uf}</td>
                <td class="p-3 text-right">$${rec.precio_clp.toLocaleString()}</td>
                <td class="p-3 text-right">${rec.prestadores}</td>
                <td class="p-3 text-right font-bold">${rec.score}</td>
                <td class="p-3 text-center">${dentro ? '✅' : '+'}</td>
            </tr>`;
    });
    html += `</tbody></table></div>
        <div class="px-6 py-2 bg-gray-50 border-t text-xs text-gray-400">
            Precios en UF × valor UF ($38.500). Tu 7% legal: $${pct7.toLocaleString()}. Motor evalúa 2,231 planes reales de 7 ISAPREs.
        </div>
    </div>`;

    // ── Nota: costo real de cargas varía por edad ──
    if (cargas > 0) {
        html += `
        <div class="mt-4 text-xs text-gray-400 text-center bg-gray-50 rounded-lg py-3 px-4">
            💡 El costo real de cada carga depende de su <strong>edad</strong> (factor etario) y del <strong>GES</strong>. El sexo y tipo de vínculo <strong>no</strong> afectan el precio (Ley 2024). Precios mostrados son estimados con promedio.
        </div>`;
    }

    // ── CTA banner ──
    const formEmoji = esFamiliar ? '👨‍👩‍👧‍👦' : '🧑';

    html += `
    <div class="mt-6 bg-gradient-to-r from-green-600 to-emerald-500 rounded-xl p-6 text-center text-white shadow-lg">
        <p class="text-lg font-bold mb-2">${formEmoji} ¿Querés una cotización real con estos datos?</p>
        <p class="text-green-100 text-sm mb-4">Completá el formulario de ${formLabel} y un ejecutivo te contactará con los planes exactos para tu perfil.</p>
        <a href="${formUrl}" class="inline-flex items-center gap-2 bg-white text-green-700 font-bold px-6 py-3 rounded-xl hover:bg-green-50 transition shadow text-base">
            <iconify-icon icon="mdi:clipboard-text-outline" width="20"></iconify-icon>
            Completar datos para ${formLabel}
        </a>
        <p class="text-green-200 text-xs mt-2">Sin compromiso · Tus datos están seguros</p>
    </div>`;

    document.getElementById('resultados-container').innerHTML = html;
    document.getElementById('resultados').classList.remove('hidden');
    document.getElementById('resultados').scrollIntoView({ behavior: 'smooth' });
}

function escHtml(s) {
    const div = document.createElement('div');
    div.textContent = s;
    return div.innerHTML;
}

// Pre-fill + auto-ejecutar desde GET params (flujo UX: home → comparador)
(function(){
    const p = new URLSearchParams(window.location.search);
    const renta = p.get('renta'), edad = p.get('edad'), cargas = p.get('cargas');
    const hasData = renta || edad || cargas;
    if(renta){ document.getElementById('renta-input').value = renta; document.getElementById('siete-pct-label').textContent = '$'+Math.round((parseInt(renta)||0)*0.07).toLocaleString(); }
    if(edad) document.getElementById('edad-input').value = edad;
    if(cargas){ document.getElementById('cargas-input').value = cargas; toggleCargaEdad();
        setTimeout(()=>{
            const ages = p.getAll('carga_edad[]');
            document.querySelectorAll('.carga-edad-val').forEach((el,i)=>{ if(ages[i]) el.value = ages[i].slice(0,2); });
            if(hasData) calcular();
        }, 200);
    } else if(hasData) {
        calcular();
    }
})();
</script>
<?php
$secciones_html = ob_get_clean();

$faq_preguntas = [
    '¿Los precios son reales?' => 'Sí. Los precios provienen de queplan.cl, actualizados a julio 2026. Son precios referenciales del plan más económico y más caro de cada isapre para el perfil seleccionado.',
    '¿Qué significa "dentro de tu 7%?"' => 'Indica que el plan más barato de esa isapre calza dentro del 7% legal de tu renta. Si sale "extra", necesitarías una cotización adicional mensual para cubrir ese plan.',
    '¿Puedo contratar directamente desde aquí?' => 'El comparador te muestra los precios reales para que compares. Para contratar, agenda una asesoría gratuita con uno de nuestros ejecutivos que te ayudará con el plan exacto.',
    '¿Por qué no se muestran coberturas detalladas?' => 'Las coberturas exactas dependen del plan específico que elijas. Te mostramos el promedio de hospitalización y ambulatorio de cada isapre, y el mejor plan con sus coberturas reales.',
];
$faq_titulo = 'Preguntas Frecuentes sobre el Comparador';

include __DIR__ . '/../../layout/seo-page.php';
