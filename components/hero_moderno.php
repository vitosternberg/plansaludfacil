<?php
// components/hero_moderno.php — v2026-07-18b
// Variables esperadas (opcionales porque tienen default): $titulo, $subtitulo, $cta_texto, $cta_link
$titulo = $titulo ?? 'Encuentra tu Plan de Salud Ideal';
$titulo_movil = $titulo_movil ?? $titulo;
$subtitulo = $subtitulo ?? 'Asesoría gratuita y experta para elegir la mejor Isapre según tu perfil de salud y familia.';
$subtitulo_movil = $subtitulo_movil ?? $subtitulo;
$cta_texto = $cta_texto ?? 'Cotizar Ahora';
$cta_link = $cta_link ?? '/servicios/cambio-de-isapre#formulario-contacto';
?>
<div class="relative mb-12 md:mb-32">
    <!-- Hero Background with Curved Bottom -->
    <section class="hero-bg relative pt-8 md:pt-24 pb-32 md:pb-48 px-4 text-center rounded-b-[50px] md:rounded-b-[100px] shadow-lg">
        <div class="relative z-10 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-2 md:mb-6 leading-none tracking-tight drop-shadow-xl">
                <span class="hidden md:inline"><?= htmlspecialchars($titulo) ?></span>
                <span class="md:hidden"><?= htmlspecialchars($titulo_movil) ?></span>
            </h1>
            <p class="text-lg md:text-[23px] text-white mb-10 max-w-2xl mx-auto drop-shadow-md font-medium">
                <span class="hidden md:inline"><?= htmlspecialchars($subtitulo) ?></span>
                <span class="md:hidden"><?= htmlspecialchars($subtitulo_movil) ?></span>
            </p>
            
        </div>
    </section>

    <!-- Floating Action Bar (Overlapping) -->
    <div class="relative md:absolute left-0 right-0 -mt-24 md:mt-0 md:-bottom-12 z-20 px-4">
        <form id="hero-cotizador" class="max-w-5xl mx-auto bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] p-4 md:p-6 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4 transition-all relative">
            
            <div class="block md:hidden absolute -top-10 left-1/2 transform -translate-x-1/2 z-30">
                <img src="<?= BASE_URL ?>/img/asesor_movil.jpg" alt="Asesor Plan Salud Fácil" class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover">
            </div>
            
            <div class="flex-1 w-full relative group mt-8 md:mt-0">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Renta Líquida (sin puntos)</label>
                <div class="relative">
                    <input type="text" id="hero-renta" name="renta" placeholder="Ej. 1500000" maxlength="8" oninput="this.value=this.value.replace(/[^0-9]/g,'');document.getElementById('hero-7pct').textContent='$'+Math.round((parseInt(this.value)||0)*0.07).toLocaleString()" class="w-full appearance-none border-2 border-gray-100 bg-gray-50 text-gray-800 font-semibold rounded-xl px-4 py-3 focus:ring-0 focus:border-[#00d2ff] transition-colors">
                    <span class="absolute right-3 bottom-1 text-xs text-gray-400">7% <span id="hero-7pct" class="font-semibold text-blue-600">$0</span></span>
                </div>
            </div>
            
            <div class="hidden md:block w-px h-12 bg-gray-200"></div>
            
            <div class="flex-1 w-full relative group">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Edad (18–65)</label>
                <input type="number" id="hero-edad" name="edad" min="18" max="65" maxlength="2" placeholder="Ej. 30" onchange="if(this.value<18)this.value=18;if(this.value>65)this.value=65" class="w-full border-2 border-gray-100 bg-gray-50 text-gray-800 font-semibold rounded-xl px-4 py-3 focus:ring-0 focus:border-[#00d2ff] transition-colors">
            </div>
            
            <div class="hidden md:block w-px h-12 bg-gray-200"></div>

            <div class="flex-1 w-full relative group">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">¿Tienes Cargas?</label>
                <div class="relative">
                    <select id="hero-cargas" name="cargas" onchange="updateCargaEdadInputs()" class="w-full appearance-none border-2 border-gray-100 bg-gray-50 text-gray-800 font-semibold rounded-xl px-4 py-3 focus:ring-0 focus:border-[#00d2ff] cursor-pointer transition-colors">
                        <option value="0">Soy solo yo</option>
                        <?php for($i=1;$i<=10;$i++): ?><option value="<?=$i?>"><?=$i?> carga<?=$i>1 ? 's' : ''?></option><?php endfor; ?>
                    </select>
                    <iconify-icon icon="mdi:chevron-down" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></iconify-icon>
                </div>
                <div id="hero-carga-edad-group" style="display:none" class="mt-2 space-y-1"></div>
            </div>

            <div class="mt-2 md:mt-0 w-full md:w-auto flex flex-col gap-2">
                <button type="button" onclick="heroCotizar()" class="block w-full text-center bg-gradient-to-r from-[#00d2ff] to-[#0284c7] hover:from-[#0284c7] hover:to-[#00d2ff] text-white font-extrabold py-4 px-8 rounded-xl shadow-lg transition-transform hover:-translate-y-1 whitespace-nowrap">
                    Buscar Planes
                </button>
                <button type="button" onclick="heroCotizarExpress()" class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 px-8 rounded-xl shadow transition whitespace-nowrap text-sm">
                    ⚡ Cotizar Express
                </button>
            </div>
        </form>
        <script>
        const BASE_URL = '<?= BASE_URL ?>';
        function updateCargaEdadInputs(){const n=parseInt(document.getElementById('hero-cargas').value)||0;const g=document.getElementById('hero-carga-edad-group');if(n===0){g.style.display='none';g.innerHTML='';return}g.style.display='block';let h='';for(let i=1;i<=n;i++)h+='<input type=number maxlength=2 class=hero-carga-edad name=carga_edad[] min=0 max=80 placeholder=\"Edad carga '+i+'\" style=\"width:100%;border:2px solid #f3f4f6;background:#f9fafb;border-radius:12px;padding:8px 12px;font-size:13px;font-weight:600;color:#1f2937;margin-bottom:4px\">';g.innerHTML=h}
        function heroCotizar(){const r=document.getElementById('hero-renta').value||'0';const e=document.getElementById('hero-edad').value||'';const c=document.getElementById('hero-cargas').value||'0';const p=new URLSearchParams({age:e,income:r,cargas:c});if(c>0){document.querySelectorAll('.hero-carga-edad').forEach((el,i)=>{if(el.value)p.append('carga_edad[]',el.value)})};const d=c>0?BASE_URL+'/planes/familiares/':BASE_URL+'/planes/individuales/';window.location.href=d+'?'+p.toString()+'#formulario'}
        function heroCotizarExpress(){const r=document.getElementById('hero-renta').value||'0';const e=document.getElementById('hero-edad').value||'';const c=document.getElementById('hero-cargas').value||'0';const p=new URLSearchParams({edad:e,renta:r,cargas:c});if(c>0){document.querySelectorAll('.hero-carga-edad').forEach((el,i)=>{if(el.value)p.append('carga_edad[]',el.value)})};window.location.href=BASE_URL+'/planes/comparador/?'+p.toString()}
        </script>
    </div>
</div>

<style>
.hero-bg {
    /* Mantenemos la imagen original pero le damos un tinte azul moderno alineado a nuestra marca */
    background: linear-gradient(135deg, rgba(2, 132, 199, 0.85) 0%, rgba(0, 210, 255, 0.6) 100%), url('<?= BASE_URL ?>/img/hero_familia.jpg');
    background-size: cover;
    background-position: center 35%;
}
</style>
