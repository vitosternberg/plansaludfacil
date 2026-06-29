<?php
$titulo = $titulo ?? 'Marcas Asociadas';
?>
<section class="brands-section">
    <div class="brands-container">
        <h2><?= htmlspecialchars($titulo) ?></h2>
        <div class="brands-carousel">
            <!-- Simulated Logos -->
            <div class="brand-item">Colmena</div>
            <div class="brand-item">CruzBlanca</div>
            <div class="brand-item">Consalud</div>
            <div class="brand-item">Banmédica</div>
            <div class="brand-item">VidaTres</div>
            <div class="brand-item">Nueva Masvida</div>
        </div>
    </div>
</section>

<style>
.brands-section {
    padding: 60px 20px;
    background-color: #f8fafc;
    text-align: center;
    font-family: 'Inter', sans-serif;
}
.brands-container h2 {
    font-size: 1.5rem;
    color: #64748b;
    margin-bottom: 30px;
    font-weight: 500;
}
.brands-carousel {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
    max-width: 1000px;
    margin: 0 auto;
}
.brand-item {
    font-size: 1.2rem;
    font-weight: bold;
    color: #cbd5e1;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: color 0.3s ease;
}
.brand-item:hover {
    color: #00d2ff;
}
</style>
