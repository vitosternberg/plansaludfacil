<body class="bg-gray-50 min-h-screen font-sans">
  <!-- Botón de WhatsApp flotante -->
<div class="fixed bottom-6 right-6 z-50">
    <button id="whatsapp-button" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-all duration-300 transform hover:scale-110">
        <iconify-icon icon="mdi:whatsapp" width="32"></iconify-icon>
    </button>
</div>

<!-- Popup/Modal de WhatsApp -->
<div id="whatsapp-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Contacto por WhatsApp</h3>
            <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                <iconify-icon icon="mdi:close" width="24"></iconify-icon>
            </button>
        </div>
        
        <form id="whatsapp-form" class="space-y-4">
            <div>
                <label for="whatsapp-number" class="block text-sm font-medium text-gray-700 mb-1">Número de WhatsApp*</label>
                <input type="tel" id="whatsapp-number" name="phone" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="Ej: 56912345678">
                <p class="mt-1 text-sm text-red-600 hidden" id="phone-error">Ingrese un número válido (9 dígitos)</p>
            </div>
            
            <div>
                <label for="whatsapp-name" class="block text-sm font-medium text-gray-700 mb-1">Tu Nombre*</label>
                <input type="text" id="whatsapp-name" name="name" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="Ej: Juan Pérez">
            </div>
            
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                <iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon>
                Enviar Mensaje
            </button>
        </form>
    </div>
</div>  
   <script>
document.addEventListener('DOMContentLoaded', function() {
    const whatsappButton = document.getElementById('whatsapp-button');
    const whatsappModal = document.getElementById('whatsapp-modal');
    const closeModal = document.getElementById('close-modal');
    const whatsappForm = document.getElementById('whatsapp-form');
    const phoneInput = document.getElementById('whatsapp-number');
    const phoneError = document.getElementById('phone-error');

    // Validación de teléfono en tiempo real
    phoneInput.addEventListener('input', function() {
        const phone = this.value.replace(/\D/g, '');
        this.value = phone;
        
        if (phone.length === 9) {
            this.classList.remove('border-red-500');
            this.classList.add('border-green-500');
            phoneError.classList.add('hidden');
        } else {
            this.classList.remove('border-green-500');
            this.classList.add('border-red-500');
            phoneError.classList.remove('hidden');
        }
    });

    // Abrir modal
    whatsappButton.addEventListener('click', function() {
        whatsappModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });

    // Cerrar modal
    closeModal.addEventListener('click', function() {
        whatsappModal.classList.add('hidden');
        document.body.style.overflow = '';
    });

    // Enviar formulario
    whatsappForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const phone = phoneInput.value.replace(/\D/g, '');
        const name = document.getElementById('whatsapp-name').value;
        
        if (phone.length !== 9) {
            phoneError.classList.remove('hidden');
            return;
        }

        // Mostrar loader
        const submitBtn = whatsappForm.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<span class="inline-block animate-spin mr-2">↻</span> Procesando...';
        submitBtn.disabled = true;

        // Enviar datos al backend
        fetch('guardar_whatsapp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                phone: phone,
                name: name,
                date: new Date().toISOString()
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al guardar los datos');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Redirigir a WhatsApp
                const message = encodeURIComponent(`Hola soy ${name}, y tengo interés en los planes de Isapre`);
                // Y cámbiala por (con tu número real):
                window.open(`https://wa.me/56952282339?text=${message}`, '_blank'); // Reemplaza 56952282339 con tu número de 52282339 WhatsApp
                //window.open(`https://wa.me/56${phone}?text=${message}`, '_blank');
                
                // Busca esta línea:
// window.open(`https://wa.me/56${phone}?text=${message}`, '_blank');

                
                
                // Cerrar modal
                whatsappModal.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                throw new Error(data.message || 'Error en la respuesta');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error: ' + error.message);
        })
        .finally(() => {
            submitBtn.innerHTML = '<iconify-icon icon="mdi:whatsapp" width="20" class="mr-2"></iconify-icon> Enviar Mensaje';
            submitBtn.disabled = false;
        });
    });
});
</script>