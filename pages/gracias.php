<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu cotización! | Isapre Ahora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <script>
        setTimeout(function() {
            window.location.href = "index.php";
        }, 20000); // 20 segundos
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #3b82f6;
            opacity: 0.7;
            animation: fall 5s linear infinite;
        }
        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Efecto confetti dinámico -->
    <div id="confetti-container"></div>

    <!-- Tarjeta principal -->
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header con gradiente -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-white/20 p-4 rounded-full">
                    <iconify-icon icon="mdi:check-circle" class="text-white" width="48"></iconify-icon>
                </div>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">¡Cotización Recibida!</h1>
            <p class="text-blue-100 text-lg">Estamos procesando tu solicitud</p>
        </div>
        
        <!-- Contenido -->
        <div class="p-8 md:p-10 text-center">
            <?php if (isset($_GET['id']) && is_numeric($_GET['id'])): ?>
                <div class="mb-6">
                    <p class="text-gray-600 mb-4">Tu solicitud ha sido registrada con éxito</p>
                    <div class="inline-block bg-blue-50 rounded-lg px-6 py-3 mb-4">
                        <p class="text-sm text-gray-500">Número de cotización</p>
                        <p class="text-2xl font-bold text-blue-600"><?= htmlspecialchars($_GET['id']) ?></p>
                    </div>
                    <p class="text-gray-600">Un asesor especializado se contactará contigo en las próximas horas.</p>
                </div>
            <?php else: ?>
                <div class="mb-6">
                    <p class="text-gray-600">Hemos recibido tu solicitud correctamente.</p>
                </div>
            <?php endif; ?>
            
            <!-- Información de contacto -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <iconify-icon icon="mdi:help-circle" class="text-blue-500 mr-2" width="20"></iconify-icon>
                    ¿Necesitas ayuda inmediata?
                </h3>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="tel:+56212345678" class="flex items-center text-gray-700 hover:text-blue-600">
                        <iconify-icon icon="mdi:phone" class="mr-2" width="18"></iconify-icon>
                        +56 9 9233 8601
                    </a>
                    <a href="mailto:contacto@misapre.cl" class="flex items-center text-gray-700 hover:text-blue-600">
                        <iconify-icon icon="mdi:email" class="mr-2" width="18"></iconify-icon>
                        contacto@plansaludfacil.cl
                    </a>
                </div>
            </div>
            
            <!-- Contador y botón -->
            <div class="flex flex-col items-center">
                <p class="text-gray-500 mb-4 flex items-center">
                    <iconify-icon icon="mdi:clock-outline" class="mr-2" width="18"></iconify-icon>
                    Serás redirigido en <span id="countdown" class="font-medium">20</span> segundos
                </p>
                <a href="index.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    Volver al inicio ahora
                    <iconify-icon icon="mdi:arrow-right" class="ml-2" width="10"></iconify-icon>
                </a>
            </div>
        </div>
    </div>

    <!-- Script para el contador y confetti -->
    <script>
        // Contador regresivo
        let seconds = 20;
        const countdownElement = document.getElementById('countdown');
        
        const interval = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
            }
        }, 1000);

        // Efecto confetti
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('confetti-container');
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                confetti.style.animationDelay = Math.random() * 5 + 's';
                confetti.style.backgroundColor = `hsl(${Math.random() * 60 + 200}, 80%, 60%)`;
                container.appendChild(confetti);
            }
        });
    </script>
</body>
</html>