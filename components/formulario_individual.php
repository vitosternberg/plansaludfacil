<?php
// components/formulario_individual.php
?>
<div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 mt-12 scroll-mt-24" id="formulario-contacto">
    <div class="text-center mb-8">
        <h3 class="text-3xl font-extrabold text-gray-900 mb-3">Optimiza tu 7%</h3>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">Tú dinos qué necesitas y nosotros hacemos el trabajo por ti en menos de 2 minutos. Encontramos el plan ideal enfocado en lo que realmente utilizas (como kinesiología, telemedicina o generación de excedentes), optimizando tu presupuesto al máximo.</p>
    </div>

    <form id="form-individual" class="max-w-3xl mx-auto" onsubmit="event.preventDefault(); submitIndividualForm();">
        <input type="hidden" name="tipo_plan" value="individual">
        
        <!-- Honeypot Field (Antispam) -->
        <div style="opacity: 0; position: absolute; top: -9999px; left: -9999px;" aria-hidden="true">
            <label for="url_website_ind">Sitio Web</label>
            <input type="text" name="url_website" id="url_website_ind" tabindex="-1" autocomplete="off">
        </div>

        <!-- Datos Básicos -->
        <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">1. Tus Datos Básicos</h4>
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="Ej. Mateo Silva">
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
                    <input type="number" name="age" value="<?php echo isset($_GET['age']) ? htmlspecialchars($_GET['age']) : ''; ?>" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="35">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Renta Líquida</label>
                    <input type="number" name="income" value="<?php echo isset($_GET['income']) ? htmlspecialchars($_GET['income']) : ''; ?>" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="$2.000.000">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Comuna de Residencia</label>
                <input type="text" name="comuna" list="comunas_list_ind" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white transition-colors" placeholder="Ej. Providencia, Santiago" autocomplete="off">
                <datalist id="comunas_list_ind">
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
                    <option value="Esencial">Isapre Esencial</option>
                    <option value="Nueva Masvida">Isapre Nueva Masvida</option>
                    <option value="Vida Tres">Isapre Vida Tres</option>
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
            <input type="hidden" name="cargas" value="0">
        </div>

        <!-- Prioridades -->
        <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">2. ¿Qué te importa realmente en un plan?</h4>
        <p class="text-sm text-gray-500 mb-4">Selecciona las coberturas clave para que busquemos la Isapre que mejor se adapte a tu estilo de vida.</p>
        <div class="grid md:grid-cols-2 gap-4 mb-8">
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Atención Ambulatoria" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Atención Ambulatoria</span>
                    <span class="block text-xs text-gray-500">Consultas médicas y especialistas</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Hospitalización" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Hospitalización</span>
                    <span class="block text-xs text-gray-500">Cobertura en clínicas y hospitales</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Maternidad" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Maternidad</span>
                    <span class="block text-xs text-gray-500">Prenatal, parto y postnatal</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Kinesiología y Deporte" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Kinesiología / Deporte</span>
                    <span class="block text-xs text-gray-500">Kinesiología y Traumatología</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Telemedicina" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Telemedicina</span>
                    <span class="block text-xs text-gray-500">Atención online y recetas digitales</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Excedentes" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Generar Excedentes</span>
                    <span class="block text-xs text-gray-500">Para farmacias, óptica o dental</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Dental" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Cobertura Dental</span>
                    <span class="block text-xs text-gray-500">Limpiezas, caries y ortodoncia</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Farmacia" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Medicamentos / Farmacia</span>
                    <span class="block text-xs text-gray-500">Descuentos en recetas y medicamentos</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group">
                <input type="checkbox" name="interests[]" value="Salud Mental" class="w-5 h-5 text-blue-600 rounded border-gray-300">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Salud Mental</span>
                    <span class="block text-xs text-gray-500">Psicología y Psiquiatría</span>
                </span>
            </label>
            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition group" id="otro_interes_label">
                <input type="checkbox" name="interests[]" value="Otra" class="w-5 h-5 text-blue-600 rounded border-gray-300" onchange="document.getElementById('otro_interes_group').classList.toggle('hidden')">
                <span class="ml-3">
                    <span class="block font-semibold text-gray-800 group-hover:text-blue-600">Otra</span>
                    <span class="block text-xs text-gray-500">Especifica otra cobertura</span>
                </span>
            </label>
        </div>
        <div id="otro_interes_group" class="hidden transition-all mb-8">
            <input type="text" name="otro_interes_text" placeholder="Indica qué otra cobertura te interesa..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white text-sm">
        </div>

        <!-- Preexistencias -->
        <h4 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">3. Historial de Salud</h4>
        <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-700 mb-2">¿Tienes alguna condición o enfermedad diagnosticada (preexistencia)?</label>
            <div class="flex gap-6 mb-3">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="preexistence" value="si" class="w-5 h-5 text-blue-600" onchange="document.getElementById('preexistence_detail').classList.remove('hidden')">
                    <span class="ml-2 text-gray-700 font-medium">Sí</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="preexistence" value="no" class="w-5 h-5 text-blue-600" onchange="document.getElementById('preexistence_detail').classList.add('hidden')" checked>
                    <span class="ml-2 text-gray-700 font-medium">No</span>
                </label>
            </div>
            <div id="preexistence_detail" class="hidden transition-all mt-3">
                <input type="text" name="preexistence_text" placeholder="De forma totalmente confidencial, indícanos cuál es..." class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#00d2ff] focus:border-[#00d2ff] focus:bg-white text-sm">
            </div>
        </div>

        <div id="form-msg-individual" class="mb-6 hidden text-center p-4 rounded-lg font-medium"></div>

        <button type="submit" class="w-full bg-gradient-to-r from-[#00d2ff] to-[#0284c7] hover:from-[#0284c7] hover:to-[#00d2ff] text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 text-lg flex justify-center items-center gap-2">
            🚀 Solicitar Cotización Individual
        </button>
        <p class="text-center text-xs text-gray-400 mt-4">Tus datos están seguros. No los compartiremos con terceros sin tu autorización.</p>
    </form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
async function submitIndividualForm() {
    const form = document.getElementById('form-individual');
    const msg = document.getElementById('form-msg-individual');
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    
    // Validaciones unificadas
    const validacion = window.validarFormularioCompleto(form);
    if (!validacion.valido) {
        msg.textContent = validacion.mensaje;
        msg.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
        return;
    }

    // Preparar el mensaje que llegará a Omniflow
    const isapreActual = formData.get('isapre_actual') || 'No especificado';
    let customMessage = `Solicitud Plan Individual:\nComuna: ${formData.get('comuna')}\nSistema Actual: ${isapreActual}\nEdad: ${formData.get('age')}\nRenta: ${formData.get('income')}\nCargas: ${formData.get('cargas')}\nIntereses: `;
    const interests = formData.getAll('interests[]');
    customMessage += interests.length > 0 ? interests.join(', ') : 'Ninguno específico';
    const otroInteres = formData.get('otro_interes_text');
    if (interests.includes('Otra') && otroInteres) {
        customMessage += ` (Otra: ${otroInteres})`;
    }
    customMessage += `\nPreexistencias: ${formData.get('preexistence') === 'si' ? formData.get('preexistence_text') : 'No'}`;
    
    formData.append('message', customMessage);
    formData.append('query_type', 'cotizacion_individual');
    if (window.psfActivityTracker) {
        formData.append('tracking_session_id', window.psfActivityTracker.getSessionId());
    }

    msg.className = 'hidden';

    // Deshabilitar botón y mostrar carga
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="inline-block w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span> Enviando...';
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

    try {
        const response = await fetch('<?= BASE_URL ?>/procesar_formularios.php', { method: 'POST', body: formData });
        const data = await response.json();

        if (data.success) {
            if (window.psfActivityTracker) {
                await window.psfActivityTracker.trackFormSuccess('form-individual', 'formulario', data.message_id || '', {
                    query_type: 'cotizacion_individual'
                });
            }
            setTimeout(() => { 
                const params = new URLSearchParams({
                    id: data.message_id,
                    age: formData.get('age') || '',
                    income: formData.get('income') || '',
                    cargas: formData.get('cargas') || '0',
                    intereses: interests.join(',')
                });
                window.location.href = BASE_URL + '/pages/gracias.php?' + params.toString(); 
            }, 500);
        } else {
            msg.textContent = data.message || 'Error al enviar la solicitud.';
            msg.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
            // Rehabilitar botón en caso de error
            submitBtn.disabled = false;
            submitBtn.innerHTML = '🚀 Solicitar Cotización Individual';
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    } catch (error) {
        msg.textContent = 'Error de conexión. Inténtalo más tarde.';
        msg.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
        // Rehabilitar botón en caso de error
        submitBtn.disabled = false;
        submitBtn.innerHTML = '🚀 Solicitar Cotización Individual';
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
}
</script>
