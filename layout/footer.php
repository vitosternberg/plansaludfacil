<?php
/**
 * layout/footer.php
 * Contenido del pie de p���gina y etiquetas de cierre HTML.
 * Ubicaci���n: tu_proyecto_raiz/layout/footer.php
 *
 * [VERSION CONTROL] - Nueva Versi���n: 2025-07-06
 * - Contiene el footer y las etiquetas de cierre `</body>` y `</html>`.
 * - Incluye el JavaScript global para el men��� m���vil.
 */
$trackerScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$trackerHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
$trackerIsLocalHost = strpos($trackerHost, 'localhost') !== false || strpos($trackerHost, '127.0.0.1') !== false;
$trackerOmnilamaBaseUrl = $trackerIsLocalHost
    ? $trackerScheme . '://' . $trackerHost . '/Omniflow'
    : 'https://www.omnilama.cl';
$siteActivityTrackerConfig = [
    'siteKey' => 'plansaludfacil',
    'endpointUrl' => $trackerOmnilamaBaseUrl . '/api/site_activity/track.php',
    'sessionStorageKey' => 'psf_tracking_session_id',
];
?>
<footer class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-6 mt-auto footer-gradient">
    <div class="container mx-auto px-4 text-center text-sm">
        <p>&copy; 2025 Plan Salud Facil. Todos los derechos reservados.</p>
        <div class="mt-2 space-x-4">
            <a href="/nosotros/privacidad" class="hover:text-blue-200 transition">Politica de Privacidad</a>
            <a href="#" class="hover:text-blue-200 transition">Terminos de Servicio</a>
        </div>
    </div>
</footer>

<script>
    window.psfActivityTracker = (() => {
        const config = <?= json_encode($siteActivityTrackerConfig, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const startedForms = new Set();

        const trimValue = (value, maxLength = 255) => {
            if (typeof value !== 'string') {
                value = value === null || value === undefined ? '' : String(value);
            }
            value = value.trim();
            return value.length > maxLength ? value.slice(0, maxLength) : value;
        };

        const getSessionId = () => {
            try {
                let sessionId = localStorage.getItem(config.sessionStorageKey);
                if (!sessionId) {
                    sessionId = `psf_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
                    localStorage.setItem(config.sessionStorageKey, sessionId);
                }
                return sessionId;
            } catch (error) {
                return `psf_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
            }
        };

        const getPageCategory = () => {
            const path = window.location.pathname;
            if (path.includes('/servicios/')) return 'servicios';
            if (path.includes('/nosotros/')) return 'nosotros';
            if (path.includes('/blog')) return 'blog';
            if (path === '/' || path.endsWith('/plansaludfacil_new') || path.endsWith('/plansaludfacil_new/')) return 'home';
            return 'general';
        };

        const send = async (payload) => {
            try {
                await fetch(config.endpointUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    keepalive: true
                });
            } catch (error) {
                console.error('PSF activity tracking failed:', error);
            }
        };

        const track = (eventType, data = {}, options = {}) => {
            const payload = {
                site_key: config.siteKey,
                session_id: getSessionId(),
                event_type: eventType,
                page_url: trimValue(data.pageUrl || window.location.href, 2000),
                page_path: trimValue(data.pagePath || window.location.pathname, 255),
                referrer_url: trimValue(data.referrerUrl || document.referrer || '', 2000),
                element_type: trimValue(data.elementType || '', 50),
                element_label: trimValue(data.elementLabel || '', 255),
                element_target: trimValue(data.elementTarget || '', 2000),
                metadata: {
                    page_category: getPageCategory(),
                    ...((data.metadata && typeof data.metadata === 'object') ? data.metadata : {})
                }
            };

            if (data.leadSource && data.leadReference) {
                payload.lead_source = trimValue(data.leadSource, 50);
                payload.lead_reference = trimValue(String(data.leadReference), 100);
            }

            if (options.associateSessionWithLead) {
                payload.associate_session_with_lead = true;
            }

            return send(payload);
        };

        const identifyLead = (leadSource, leadReference, metadata = {}) => {
            if (!leadSource || !leadReference) {
                return Promise.resolve();
            }

            return track('lead_identified', {
                leadSource,
                leadReference: String(leadReference),
                metadata
            }, {
                associateSessionWithLead: true
            });
        };

        const trackFormSuccess = (formName, leadSource, leadReference, metadata = {}) => {
            return track('form_success', {
                elementType: 'form',
                elementLabel: formName,
                leadSource,
                leadReference: leadReference ? String(leadReference) : '',
                metadata
            }, {
                associateSessionWithLead: !!(leadSource && leadReference)
            });
        };

        document.addEventListener('DOMContentLoaded', () => {
            track('page_view', {
                metadata: {
                    page_title: document.title,
                    query_string: window.location.search || ''
                }
            });

            document.addEventListener('click', (event) => {
                const target = event.target.closest('a,button');
                if (!target || target.closest('#psl-chat-widget-root')) {
                    return;
                }

                const href = target.getAttribute('href') || '';
                const label = trimValue(
                    target.dataset.trackLabel ||
                    target.getAttribute('aria-label') ||
                    target.getAttribute('title') ||
                    target.textContent ||
                    '',
                    255
                );

                if (label === '' && href === '') {
                    return;
                }

                let eventType = 'cta_click';
                if (target.closest('nav')) {
                    eventType = 'nav_click';
                }
                if (href.indexOf('wa.me') !== -1 || target.getAttribute('onclick') === 'openWspModal()') {
                    eventType = 'whatsapp_cta_click';
                }

                const sectionElement = target.closest('section,[id]');

                track(eventType, {
                    elementType: target.tagName.toLowerCase(),
                    elementLabel: label,
                    elementTarget: href,
                    metadata: {
                        section_id: sectionElement ? (sectionElement.id || '') : ''
                    }
                });
            }, true);

            document.addEventListener('focusin', (event) => {
                const form = event.target.closest('form');
                if (!form) {
                    return;
                }

                const formName = form.id || form.getAttribute('name') || 'formulario';
                if (startedForms.has(formName)) {
                    return;
                }

                startedForms.add(formName);
                track('form_start', {
                    elementType: 'form',
                    elementLabel: formName
                });
            });

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) {
                    return;
                }

                const formName = form.id || form.getAttribute('name') || 'formulario';
                track('form_submit', {
                    elementType: 'form',
                    elementLabel: formName
                });
            }, true);
        });

        return {
            getSessionId,
            track,
            identifyLead,
            trackFormSuccess
        };
    })();

    // [VERSION CONTROL] - L���gica JavaScript para el men��� m���vil - 2025-07-06
    // Estas referencias deben coincidir con los IDs en tu `header_content.php`
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuClose = document.getElementById('menu-close');
    const menuOverlay = document.getElementById('menu-overlay');

    function toggleMobileMenu() {
        if (mobileMenu) {
            mobileMenu.classList.toggle('open'); // Usa la clase 'open' para el transform
        }
        if (menuOverlay) {
            menuOverlay.classList.toggle('open'); // Usa la clase 'open' para la opacidad/visibilidad
        }
        // Controla el scroll del body cuando el men��� est��� abierto
        if (mobileMenu.classList.contains('open')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }

    // Aseg���rate de que los elementos existan antes de a���adir los event listeners
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleMobileMenu);
    }
    if (menuClose) {
        menuClose.addEventListener('click', toggleMobileMenu);
    }
    if (menuOverlay) {
        menuOverlay.addEventListener('click', toggleMobileMenu);
    }

    // [VERSION CONTROL] - Lgica de bsqueda (ejemplo) - 2025-07-06
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                // alert(`Buscando: ${this.value}`); // Descomentar para depuracin
                // Aqu ir la lgica real de bsqueda
            }
        });
    });
</script>
<!-- Modal de WhatsApp -->
<div id="wsp-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <!-- Fondo oscuro -->
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeWspModal()"></div>
    
    <!-- Contenedor del Modal -->
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4 transform transition-all">
        <button onclick="closeWspModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
            <iconify-icon icon="mdi:close" width="24"></iconify-icon>
        </button>
        
        <div class="text-center mb-6">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <iconify-icon icon="mdi:whatsapp" width="32" class="text-green-500"></iconify-icon>
            </div>
            <h3 class="text-2xl font-bold text-gray-800">Habla con un Asesor</h3>
            <p class="text-gray-500 text-sm mt-2">Déjanos tus datos para brindarte una atención más personalizada.</p>
        </div>

        <form id="wsp-form" onsubmit="event.preventDefault(); submitWspForm();">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tu Nombre</label>
                <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-colors" placeholder="Ej. Juan Pérez">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono (9 dígitos)</label>
                <input type="tel" name="phone" required pattern="[0-9]{9}" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-colors" placeholder="9 1234 5678">
            </div>
            
            <div id="wsp-msg" class="hidden mb-4 text-sm font-medium p-3 rounded-lg text-center"></div>

            <button type="submit" id="wsp-submit-btn" class="w-full flex justify-center items-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-colors">
                <iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon>
                Continuar al Chat
            </button>
        </form>
    </div>
</div>

<script>
    function openWspModal() {
        const modal = document.getElementById('wsp-modal');
        modal.classList.remove('hidden');
        if (window.psfActivityTracker) {
            window.psfActivityTracker.track('whatsapp_open', {
                elementType: 'modal',
                elementLabel: 'wsp-modal'
            });
        }
        // Ocultar menú móvil si está abierto
        const mobileMenu = document.getElementById('mobile-menu');
        const menuOverlay = document.getElementById('menu-overlay');
        if (mobileMenu && mobileMenu.classList.contains('open')) {
            mobileMenu.classList.remove('open');
            menuOverlay.classList.remove('open');
            document.body.style.overflow = 'auto';
        }
    }

    function closeWspModal() {
        document.getElementById('wsp-modal').classList.add('hidden');
    }

    async function submitWspForm() {
        const form = document.getElementById('wsp-form');
        const btn = document.getElementById('wsp-submit-btn');
        const msg = document.getElementById('wsp-msg');
        
        const name = form.querySelector('[name="name"]').value;
        const phone = form.querySelector('[name="phone"]').value;

        btn.disabled = true;
        btn.innerHTML = '<iconify-icon icon="mdi:loading" width="20" class="mr-2 animate-spin"></iconify-icon> Conectando...';
        msg.classList.add('hidden');

        try {
            const response = await fetch('<?= BASE_URL ?>/guardar_whatsapp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: name,
                    phone: phone,
                    tracking_session_id: window.psfActivityTracker ? window.psfActivityTracker.getSessionId() : ''
                })
            });
            const data = await response.json();

            if (data.success) {
                if (window.psfActivityTracker) {
                    await window.psfActivityTracker.trackFormSuccess('wsp-form', 'whatsapp_contact', data.contact_id || '', {
                        source: 'footer_modal'
                    });
                }
                // Redirigir a whatsapp real
                const whatsappNumber = '56952282339'; // El nro de la empresa
                const text = encodeURIComponent(`Hola, soy ${name}. Quisiera asesoría para cotizar un plan de Isapre.`);
                window.location.href = `https://wa.me/${whatsappNumber}?text=${text}`;
                
                setTimeout(() => {
                    closeWspModal();
                    form.reset();
                    btn.disabled = false;
                    btn.innerHTML = '<iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon> Continuar al Chat';
                }, 1000);
            } else {
                throw new Error(data.message || 'Error al guardar los datos');
            }
        } catch (error) {
            msg.textContent = error.message;
            msg.className = 'mb-4 text-sm font-medium p-3 rounded-lg text-center bg-red-50 text-red-700';
            btn.disabled = false;
            btn.innerHTML = '<iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon> Continuar al Chat';
        }
    }
</script>
<script src="<?= BASE_URL ?>/js/validaciones.js?v=<?= time() ?>"></script>
</body>
</html>
