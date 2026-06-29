<?php
/**
 * =======================================================================
 * OMNIFLOW - SCRIPT DE SEGUIMIENTO DE VISITAS HÍBRIDO
 * =======================================================================
 */
require_once __DIR__ . '/../../omniflow_config.php';
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db->connect_error) {
        $db->set_charset("utf8mb4");
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $visited_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $stmt_general = $db->prepare("INSERT INTO log_visitas_generales (ip_address, user_agent, url_visitada) VALUES (?, ?, ?)");
        if ($stmt_general) {
            $stmt_general->bind_param("sss", $ip_address, $user_agent, $visited_url);
            $stmt_general->execute(); 
            $stmt_general->close();
        }

        $lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
        if ($lead_id) {
            $stmt_lead = $db->prepare("INSERT INTO lead_visits (lead_id, url_visitada) VALUES (?, ?)");
            if ($stmt_lead) {
                $stmt_lead->bind_param("is", $lead_id, $visited_url);
                $stmt_lead->execute(); 
                $stmt_lead->close();
            }
        }
        $db->close();
    }
} catch (Exception $e) {
    error_log("Omniflow Tracking Error: " . $e->getMessage());
}

$page_title = 'Nuestra Empresa - Plan Salud Fácil';
$admin_email_display = "contacto@plansaludfacil.cl";

include __DIR__ . '/../../layout/plantilla.php'; 
include __DIR__ . '/../../layout/header.php';
?>

<main class="bg-gray-50 font-sans pb-20">
    <!-- HERO NOSOTROS -->
    <div class="bg-gradient-to-r from-blue-900 to-blue-800 text-white py-16">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Sobre PlanSaludFácil</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto">Conoce cómo estamos transformando la forma en que los chilenos eligen y gestionan su previsión de salud.</p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 pt-12">
        <!-- NOSOTROS CONTENT -->
        <article class="prose prose-lg max-w-none text-gray-700 mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Beneficios de nuestra asesoría: ¿Por qué elegir a PlanSaludFácil?</h2>
            <p class="mb-10 text-lg">Descubre cómo podemos transformar tu experiencia en el sistema de salud chileno. Nuestro equipo de expertos te brinda un servicio personalizado, diseñado para garantizar la optimización de tu presupuesto y darte tranquilidad en cada paso. Deja atrás la burocracia y la complejidad de las Isapres; disfruta de una atención humana, clara y 100% enfocada en tu bienestar.</p>

            <div class="grid gap-8 mb-12">
                <!-- Beneficio 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex gap-6 items-start">
                    <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">🤝</div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 mt-0">Acompañamiento continuo: Antes, durante y después de contratar</h3>
                        <p class="text-gray-600 m-0">No somos simplemente un comparador online; somos tus asesores de confianza a largo plazo. Te guiamos de manera personalizada desde la evaluación inicial y la firma de tu contrato, hasta la resolución de dudas futuras. Nuestro compromiso no termina cuando firmas, asegurándonos de que nunca estés solo frente a tu aseguradora.</p>
                    </div>
                </div>

                <!-- Beneficio 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex gap-6 items-start">
                    <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">⚖️</div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 mt-0">Búsqueda imparcial del plan de salud más conveniente</h3>
                        <p class="text-gray-600 m-0">Analizamos todas las opciones del mercado sin favoritismos comerciales. Nuestro único objetivo es cruzar tus necesidades médicas con la oferta actual para encontrar la opción exacta que maximice tu 7% legal. Te garantizamos la mejor relación precio-cobertura, tanto para ti como para tu familia.</p>
                    </div>
                </div>

                <!-- Beneficio 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex gap-6 items-start">
                    <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-3xl">🔍</div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 mt-0">Transparencia absoluta: Sin letra chica ni sorpresas</h3>
                        <p class="text-gray-600 m-0">Creemos en las relaciones basadas en la honestidad. Te explicamos los topes de cobertura, las restricciones y los beneficios con un lenguaje sencillo, directo y libre de tecnicismos médicos. Con nosotros, las reglas del juego están claras desde el primer minuto, sin cobros ocultos ni falsas promesas.</p>
                    </div>
                </div>
            </div>

            <!-- Llamado a la acción -->
            <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded-r-lg mb-12">
                <p class="text-lg font-bold text-gray-900 mb-2">¿Listo para dejar tu salud en manos de expertos?</p>
                <a href="<?= BASE_URL ?>/servicios/cambio-de-isapre" class="text-green-700 hover:text-green-800 font-bold inline-flex items-center group">
                    Hablemos hoy mismo y encontremos tu plan ideal 
                    <span class="ml-2 transform group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>
        </article>

        <!-- FORMULARIO DE CONTACTO -->
        <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100" id="contacto">
            <h2 class="text-3xl font-bold text-gray-900 mb-4 text-center">Envíanos un Mensaje</h2>
            <p class="text-lg text-gray-600 mb-8 text-center">
                ¿Tienes alguna pregunta o sugerencia? ¡Estamos aquí para ayudarte! Escríbenos o envía un correo a <a href="mailto:<?php echo htmlspecialchars($admin_email_display); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($admin_email_display); ?></a>.
            </p>
            
            <form id="contact-form" onsubmit="event.preventDefault(); submitContactForm();" class="max-w-2xl mx-auto">
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="contact-name" class="block text-sm font-semibold text-gray-700 mb-2">Nombre y Apellido</label>
                        <input type="text" id="contact-name" name="name"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors" required
                               pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios permitidos.">
                        <p id="name-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label for="contact-email" class="block text-sm font-semibold text-gray-700 mb-2">Tu Email</label>
                        <input type="email" id="contact-email" name="email"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors" required>
                        <p id="email-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                </div>
                
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="contact-phone" class="block text-sm font-semibold text-gray-700 mb-2">Teléfono de Contacto</label>
                        <input type="tel" id="contact-phone" name="phone"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors"
                               pattern="[0-9]{9}" title="Por favor, ingresa un número de teléfono de 9 dígitos (solo números)." maxlength="9">
                        <p id="phone-error" class="text-red-500 text-xs mt-1 hidden"></p>
                    </div>
                    <div>
                        <label for="contact-type" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Consulta</label>
                        <select id="contact-type" name="query_type"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors" required>
                            <option value="">Selecciona un tipo</option>
                            <option value="reclamo">Reclamo</option>
                            <option value="asistencia">Asistencia</option>
                            <option value="consulta">Consulta General</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="contact-message" class="block text-sm font-semibold text-gray-700 mb-2">Tu Mensaje</label>
                    <textarea id="contact-message" name="message" rows="5"
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-colors" required></textarea>
                </div>
                
                <div id="contact-form-message" class="mb-6 text-center text-sm font-medium hidden p-4 rounded-lg"></div>
                
                <button type="submit"
                        class="w-full bg-blue-600 text-white px-6 py-4 rounded-lg hover:bg-blue-700 transition duration-200 font-bold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Enviar Mensaje
                </button>
            </form>
        </div>
    </div>
</main>

<script>
    // Global mobile menu logic - Should be moved to a global JS file later
    document.addEventListener('DOMContentLoaded', () => {
        const toggles = [document.getElementById('menu-toggle'), document.getElementById('menu-close'), document.getElementById('menu-overlay')];
        const menu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('menu-overlay');
        
        toggles.forEach(el => {
            if (el) el.addEventListener('click', () => {
                menu?.classList.toggle('translate-x-0');
                menu?.classList.toggle('-translate-x-full');
                overlay?.classList.toggle('hidden');
            });
        });
    });

    // Contact Form Logic
    const contactForm = document.getElementById('contact-form');
    const contactNameInput = document.getElementById('contact-name');
    const contactEmailInput = document.getElementById('contact-email');
    const contactPhoneInput = document.getElementById('contact-phone');
    const contactFormMessage = document.getElementById('contact-form-message');

    const nameError = document.getElementById('name-error');
    const emailError = document.getElementById('email-error');
    const phoneError = document.getElementById('phone-error');

    function validateName() {
        const val = contactNameInput.value;
        if (val === "" || !/^[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+$/.test(val)) {
            nameError.textContent = 'Solo letras y espacios permitidos.';
            nameError.classList.remove('hidden');
            contactNameInput.classList.add('border-red-500');
            return false;
        }
        nameError.classList.add('hidden');
        contactNameInput.classList.remove('border-red-500');
        return true;
    }

    function validateEmail() {
        const val = contactEmailInput.value;
        if (val === "" || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            emailError.textContent = 'Por favor, ingresa un formato de email válido.';
            emailError.classList.remove('hidden');
            contactEmailInput.classList.add('border-red-500');
            return false;
        }
        emailError.classList.add('hidden');
        contactEmailInput.classList.remove('border-red-500');
        return true;
    }

    function validatePhone() {
        const val = contactPhoneInput.value;
        if (val !== "" && !/^[0-9]{9}$/.test(val)) {
            phoneError.textContent = 'Ingresa un número de 9 dígitos.';
            phoneError.classList.remove('hidden');
            contactPhoneInput.classList.add('border-red-500');
            return false;
        }
        phoneError.classList.add('hidden');
        contactPhoneInput.classList.remove('border-red-500');
        return true;
    }

    contactNameInput.addEventListener('input', validateName);
    contactEmailInput.addEventListener('input', validateEmail);
    contactPhoneInput.addEventListener('input', validatePhone);

    async function submitContactForm() {
        if (!validateName() || !validateEmail() || !validatePhone() || !document.getElementById('contact-type').value || !document.getElementById('contact-message').value.trim()) {
            contactFormMessage.textContent = 'Por favor, corrige los errores en el formulario.';
            contactFormMessage.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
            return;
        }

        const formData = new FormData(contactForm);
        contactFormMessage.className = 'hidden';

        try {
            const response = await fetch('/procesar_contacto.php', { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                contactFormMessage.textContent = data.message;
                contactFormMessage.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-green-50 text-green-700';
                contactForm.reset();
            } else {
                contactFormMessage.textContent = data.message || 'Error al enviar el mensaje.';
                contactFormMessage.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
            }
        } catch (error) {
            contactFormMessage.textContent = 'Error de conexión. Inténtalo más tarde.';
            contactFormMessage.className = 'mb-6 text-center text-sm font-medium p-4 rounded-lg bg-red-50 text-red-700';
        }
    }
</script>

<?php include __DIR__ . '/../../layout/footer.php'; ?>
