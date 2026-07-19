<?php
// components/formulario_familia.php
?>
<div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 mt-12 scroll-mt-24" id="formulario-contacto">
    <div class="text-center mb-8">
        <h3 class="text-3xl font-extrabold text-gray-900 mb-3">Protege a tus hijos sin pagar de más</h3>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">Unificamos la salud de tu hogar en el plan con la mejor clínica cerca de tu casa. Evaluamos tu caso de forma imparcial y transparente para que tus urgencias infantiles de madrugada y hospitalizaciones no te dejen en la quiebra.</p>
    </div>

    <form id="form-familia" class="max-w-3xl mx-auto" onsubmit="event.preventDefault(); submitFamiliaForm();">
        <input type="hidden" name="tipo_plan" value="familiar">
        <input type="hidden" name="origen_lead" value="<?php echo (isset($es_monoparental) && $es_monoparental) ? 'monoparental' : 'familiar_biparental'; ?>">
        
        <!-- Honeypot Field (Antispam) -->
        <div style="opacity: 0; position: absolute; top: -9999px; left: -9999px;" aria-hidden="true">
            <label for="url_website">Sitio Web</label>
            <input type="text" name="url_website" id="url_website" tabindex="-1" autocomplete="off">
        </div>

        <!-- Datos Titular -->
        <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">1. Datos del Titular</h4>
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="Ej. Juan Pérez">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="tu@email.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                <input type="tel" name="phone" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="9 1234 5678" pattern="[0-9]{9}">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Edad</label>
                    <input type="number" name="age" value="<?= htmlspecialchars($_GET['age'] ?? '') ?>" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="35">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Renta Líquida</label>
                    <input type="number" name="income" value="<?= htmlspecialchars($_GET['income'] ?? '') ?>" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="$2.000.000">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Comuna de Residencia</label>
                <input type="text" name="comuna" list="comunas_list_fam" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="Ej. Providencia, Santiago" autocomplete="off">
                <datalist id="comunas_list_fam">
                    <?php include 'comunas_options.php'; ?>
                </datalist>            
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sistema de Salud Actual</label>
                <select name="isapre_actual" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors">
                    <option value="">Seleccione su sistema actual</option>
                    <option value="Fonasa">Fonasa</option>
                    <option value="Banmédica">Isapre Banmédica</option>
                    <option value="Colmena">Isapre Colmena</option>
                    <option value="Consalud">Isapre Consalud</option>
                    <option value="Cruz Blanca">Isapre Cruz Blanca</option>
                    <option value="Nueva Masvida">Isapre Nueva Masvida</option>
                    <option value="Vida Tres">Isapre Vida Tres</option>
                    <option value="Esencial">Isapre Esencial</option>
                    <option value="Sin Previsión">No tengo / Sin Previsión</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tipo de plan que busca</label>
                <select name="preferencia_plan" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" required>
                    <option value="">Seleccione preferencia</option>
                    <option value="Plan Abierto (Libre Elección)">Plan Abierto (Libre Elección)</option>
                    <option value="Plan Preferencial (con Clínica)">Plan Preferencial (con Clínica)</option>
                    <option value="Plan Cerrado (solo red de prestadores)">Plan Cerrado (solo red de prestadores)</option>
                </select>
            </div>
        </div>

        <!-- Grupo Familiar -->
        <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">2. Tu Familia</h4>
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">¿Cuántas cargas legales incluirás?</label>
                <select name="cargas" id="fam-cargas" onchange="updateFamCargaEdades()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" required>
                    <option value="">Selecciona cantidad</option>
                    <?php if (isset($es_monoparental) && $es_monoparental): ?>
                        <option value="1">1 carga (Hijo/a)</option>
                        <option value="2">2 cargas (Hijos/as)</option>
                        <option value="3">3 cargas (Hijos/as)</option>
                        <option value="4">4 cargas (Hijos/as)</option>
                        <option value="5">5 cargas (Hijos/as)</option>
                    <?php else: ?>
                        <option value="1">1 carga (Cónyuge o 1 hijo)</option>
                        <option value="2">2 cargas</option>
                        <option value="3">3 cargas</option>
                        <option value="4">4 cargas</option>
                        <option value="5">5 cargas</option>
                        <option value="6">6 cargas</option>
                    <?php endif; ?>
                </select>
            </div>
            </div>
            <div id="fam-carga-edades" class="space-y-2 mb-6" style="display:none"></div>
            <div class="grid md:grid-cols-2 gap-6 mb-6">
            <script>
            function updateFamCargaEdades(){const n=parseInt(document.getElementById('fam-cargas').value)||0;const g=document.getElementById('fam-carga-edades');if(!n){g.style.display='none';g.innerHTML='';return}g.style.display='block';let h='<label class="block text-sm font-semibold text-gray-700 mb-2">Edad de cada carga</label>';for(let i=1;i<=n;i++){let v=(new URLSearchParams(window.location.search).getAll('carga_edad[]')[i-1]||'').slice(0,2);if(parseInt(v)>80)v='80';h+='<input type="number" required maxlength="2" class="fam-carga-edad w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 font-medium outline-none focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] transition-colors" min="0" max="80" value="'+v+'" placeholder="Edad carga '+i+'">'}g.innerHTML=h}
            document.addEventListener('DOMContentLoaded',function(){const p=new URLSearchParams(window.location.search);const c=p.get('cargas');if(c){const s=document.getElementById('fam-cargas');s.value=c;if(parseInt(c)>0)updateFamCargaEdades()}})
            </script>
            <?php if (!isset($es_monoparental) || !$es_monoparental): ?>
            <div class="flex items-end pb-3">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="complementar_renta" value="si" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700 font-semibold">Deseo complementar rentas con mi pareja</span>
                </label>
            </div>
            <?php else: ?>
            <input type="hidden" name="complementar_renta" value="no">
            <?php endif; ?>
        </div>
        
        <p class="text-sm text-gray-500 mb-4">¿Qué coberturas son críticas para tu familia hoy?</p>
        <div class="grid md:grid-cols-2 gap-4 mb-8">
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="needs[]" value="Maternidad" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Maternidad / Parto</span>
                    <span class="block text-xs text-gray-500">Planificando embarazo o recién nacido</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="needs[]" value="Pediatria y Urgencias" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Pediatría y Urgencias</span>
                    <span class="block text-xs text-gray-500">Urgencias infantiles frecuentes</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="needs[]" value="Ortodoncia" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Ortodoncia Dental</span>
                    <span class="block text-xs text-gray-500">Frenillos y tratamientos dentales</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="needs[]" value="Enfermedades Graves" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Alta Cobertura Hospitalaria</span>
                    <span class="block text-xs text-gray-500">Protección ante cirugías graves</span>
                </span>
            </label>
        </div>

        <!-- Preexistencias -->
        <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">3. Historial Médico del Grupo</h4>
        <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-700 mb-2">¿Alguien en el grupo familiar tiene alguna condición preexistente? (Te asesoraremos para que el cambio sea seguro y sin perder coberturas actuales)</label>
            <div class="flex gap-6 mb-3">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="preexistence_fam" value="si" class="w-5 h-5 text-blue-600" onchange="document.getElementById('preexistence_detail_fam').classList.remove('hidden')">
                    <span class="ml-2 text-gray-700 font-medium">Sí</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="preexistence_fam" value="no" class="w-5 h-5 text-blue-600" onchange="document.getElementById('preexistence_detail_fam').classList.add('hidden')" checked>
                    <span class="ml-2 text-gray-700 font-medium">No</span>
                </label>
            </div>
            <div id="preexistence_detail_fam" class="hidden transition-all mt-3">
                <input type="text" name="preexistence_text_fam" placeholder="De forma totalmente confidencial, indícanos brevemente..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white text-sm">
            </div>
        </div>

        <div id="form-msg-familia" class="mb-6 hidden text-center p-4 rounded-lg font-medium"></div>

        <button type="submit" class="w-full bg-gradient-to-r from-[#00d2ff] to-[#0284c7] hover:from-[#0284c7] hover:to-[#00d2ff] text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
            👨‍👩‍👧‍👦 Solicitar Asesoría Familiar Gratuita
        </button>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
async function submitFamiliaForm() {
    const form = document.getElementById('form-familia');
    const msg = document.getElementById('form-msg-familia');
    const formData = new FormData(form);
    
    // Validaciones unificadas
    const validacion = window.validarFormularioCompleto(form);
    if (!validacion.valido) {
        msg.textContent = validacion.mensaje;
        msg.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
        return;
    }

    let customMessage = `Solicitud Plan Familiar:\nComuna: ${formData.get('comuna')}\nSistema Actual: ${formData.get('isapre_actual')}\nEdad: ${formData.get('age')}\nRenta: ${formData.get('income')}\nCargas: ${formData.get('cargas')}\nComplementar Renta: ${formData.get('complementar_renta') ? 'Sí' : 'No'}\nNecesidades: `;
    const needs = formData.getAll('needs[]');
    customMessage += needs.length > 0 ? needs.join(', ') : 'Ninguna específica';
    customMessage += `\nPreexistencias Grupo: ${formData.get('preexistence_fam') === 'si' ? formData.get('preexistence_text_fam') : 'No'}`;
    
    formData.append('message', customMessage);
    formData.append('query_type', 'cotizacion_familiar');
    formData.append('origen_lead', form.querySelector('[name="origen_lead"]').value);
    if (window.psfActivityTracker) {
        formData.append('tracking_session_id', window.psfActivityTracker.getSessionId());
    }

    msg.className = 'hidden';

    try {
        const response = await fetch('<?= BASE_URL ?>/procesar_formularios.php', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            if (window.psfActivityTracker) {
                await window.psfActivityTracker.trackFormSuccess('form-familia', 'formulario', data.message_id || '', {
                    query_type: 'cotizacion_familiar',
                    origen_lead: form.querySelector('[name="origen_lead"]').value
                });
            }
            setTimeout(() => { 
                const needs = formData.getAll('needs[]');
                const params = new URLSearchParams({
                    id: data.message_id,
                    age: formData.get('age') || '',
                    income: formData.get('income') || '',
                    cargas: formData.get('cargas') || '0',
                    intereses: needs.join(',')
                });
                window.location.href = BASE_URL + '/pages/gracias.php?' + params.toString(); 
            }, 500);
        } else {
            msg.textContent = data.message || 'Error al enviar la solicitud.';
            msg.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
        }
    } catch (error) {
        msg.textContent = 'Error de conexión. Inténtalo más tarde.';
        msg.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
    }
}
</script>
