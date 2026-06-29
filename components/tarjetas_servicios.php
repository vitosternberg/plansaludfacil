<?php
$titulo_seccion = $titulo_seccion ?? 'Nuestros Servicios';
$servicios = $servicios ?? [];
?>
<section class="services-section">
    <div class="services-container">
        <h2 class="section-title"><?= htmlspecialchars($titulo_seccion) ?></h2>
        <div class="services-grid">
            <?php foreach ($servicios as $servicio): ?>
                <a href="<?= htmlspecialchars($servicio['link']) ?>" class="service-card">
                    <div class="service-icon"><?= $servicio['icono'] ?? '🌟' ?></div>
                    <div class="service-content">
                        <h3><?= htmlspecialchars($servicio['titulo']) ?></h3>
                        <p><?= htmlspecialchars($servicio['descripcion']) ?></p>
                        <span class="service-link">Ver detalles &rarr;</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.services-section {
    padding: 80px 20px;
    background-color: #f1f5f9;
    font-family: 'Inter', sans-serif;
}
.services-container {
    max-width: 1200px;
    margin: 0 auto;
}
.section-title {
    text-align: center;
    font-size: 2.5rem;
    color: #0f172a;
    margin-bottom: 50px;
    font-weight: 800;
}
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}
.service-card {
    display: flex;
    align-items: flex-start;
    background: white;
    padding: 30px;
    border-radius: 20px;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid transparent;
}
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: #00d2ff;
}
.service-icon {
    font-size: 2.5rem;
    margin-right: 20px;
    background: #e0f2fe;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
}
.service-content h3 {
    font-size: 1.3rem;
    color: #1e293b;
    margin-bottom: 10px;
    margin-top: 0;
}
.service-content p {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.5;
    margin-bottom: 15px;
}
.service-link {
    color: #0284c7;
    font-weight: 600;
    font-size: 0.95rem;
}
</style>
