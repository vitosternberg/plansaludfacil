<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sección Isapre - Asesoría Profesional (Tonos Tierra)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify/2.0.0/iconify.min.js"></script>
    <style>
        /* Degradado de fondo personalizado en tonos tierra */
        .bg-earthy-gradient {
            background-image: linear-gradient(to right, #6b4c3e, #8c6e5e); /* Tonos marrones más profundos y cálidos */
            /* Alternativa más suave: linear-gradient(to right, #d4b499, #b0917c); */
        }
        /* Color del botón en tonos tierra */
        .btn-terracotta {
            background-color: #cb6d5c; /* Terracota cálido */
            box-shadow: 0 10px 15px -3px rgba(203, 109, 92, 0.3), 0 4px 6px -2px rgba(203, 109, 92, 0.2); /* Sombra con el color del botón */
        }
        .btn-terracotta:hover {
            background-color: #bd5a4b; /* Terracota un poco más oscuro al pasar el ratón */
        }
        /* Colores de texto para contraste en el fondo tierra */
        .text-light-cream {
            color: #f5f5dc; /* Blanco crema */
        }
        .text-soft-brown {
            color: #e0d0c3; /* Marrón muy suave, casi beige */
        }
    </style>
</head>
<body class="font-sans antialiased bg-stone-100"> <section class="bg-earthy-gradient py-16 px-4 md:px-8 lg:px-12 flex items-center justify-center min-h-[400px]">
        <div class="container mx-auto text-center z-10 relative">
            <h1 class="text-light-cream text-3xl font-bold mb-4 md:text-4xl lg:text-5xl drop-shadow-sm">
                ¿Cansado de Pagar de Más en tu Isapre?
            </h1>
            <p class="text-soft-brown text-lg md:text-xl lg:text-2xl mb-6 drop-shadow-sm">
                No contrates una Isapre sin Asesoría Profesional
            </p>
            <p class="text-light-cream text-md md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                En menos de 5 minutos, completas tus datos y nuestros expertos encuentran el mejor plan para ti.
                ¡Sin letra chica ni sorpresas!
            </p>

            <button id="contact-expert-button" class="btn-terracotta text-white font-semibold py-3 px-8 rounded-lg shadow-xl focus:outline-none focus:ring-2 focus:ring-orange-300 transition duration-300 transform hover:scale-105">
                Contacta a un Experto
            </button>

            <div class="flex flex-col md:flex-row justify-center items-center gap-6 md:gap-12 mt-12 text-light-cream text-lg">
                <div class="flex items-center space-x-2">
                    <iconify-icon icon="mdi:check-circle-outline" class="text-green-200 text-2xl"></iconify-icon> <span>Sin costo de asesoría</span>
                </div>
                <div class="flex items-center space-x-2">
                    <iconify-icon icon="mdi:clock-time-three-outline" class="text-amber-200 text-2xl"></iconify-icon> <span>Proceso rápido</span>
                </div>
                <div class="flex items-center space-x-2">
                    <iconify-icon icon="mdi:shield-check-outline" class="text-rose-200 text-2xl"></iconify-icon> <span>Asesoría especializada</span>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contactExpertButton = document.getElementById('contact-expert-button');

            if (contactExpertButton) {
                contactExpertButton.addEventListener('click', () => {
                    console.log('Botón "Contacta a un Experto" clickeado');
                    // Aquí puedes añadir tu lógica de redirección o modal, como en el ejemplo anterior.
                    // Por ejemplo:
                    // window.location.href = '/contacto.html';
                });
            }
        });
    </script>
</body>
</html>