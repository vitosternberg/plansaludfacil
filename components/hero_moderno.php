<?php
// components/hero_moderno.php
// Variables esperadas (opcionales porque tienen default): $titulo, $subtitulo, $cta_texto, $cta_link
$titulo = $titulo ?? 'Encuentra tu Plan de Salud Ideal';
$subtitulo = $subtitulo ?? 'Asesoría gratuita y experta para elegir la mejor Isapre según tu perfil de salud y familia.';
$cta_texto = $cta_texto ?? 'Cotizar Ahora';
$cta_link = $cta_link ?? '/servicios/planes-individuales';
?>
<section class="hero-section">
    <div class="hero-content">
        <h1><?= htmlspecialchars($titulo) ?></h1>
        <p><?= htmlspecialchars($subtitulo) ?></p>
        <a href="<?= htmlspecialchars($cta_link) ?>" class="btn-primary"><?= htmlspecialchars($cta_texto) ?></a>
    </div>
</section>

<style>
/* Estilos modernos tipo Startup Tech / Gradiente Dinámico */
.hero-section {
    background: linear-gradient(135deg, #0f766e 0%, #022c22 100%);
    color: white;
    padding: 140px 20px;
    text-align: center;
    border-radius: 0 0 40px 40px;
    box-shadow: 0 20px 40px rgba(15, 118, 110, 0.15);
    font-family: 'Inter', sans-serif;
    position: relative;
    overflow: hidden;
}

/* Decoración geométrica sutil */
.hero-section::after {
    content: '';
    position: absolute;
    bottom: -50px;
    right: -50px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(0,0,0,0) 70%);
    border-radius: 50%;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}
.hero-content h1 {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 24px;
    line-height: 1.15;
    background: linear-gradient(to right, #ffffff, #a7f3d0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.hero-content p {
    font-size: 1.25rem;
    margin-bottom: 40px;
    color: #e2e8f0;
    font-weight: 400;
    line-height: 1.6;
}
.btn-primary {
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    color: #fff;
    padding: 18px 45px;
    border-radius: 50px;
    font-weight: 700;
    text-decoration: none;
    font-size: 1.1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-block;
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
}
.btn-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(16, 185, 129, 0.5);
    color: white;
}
</style>
