<?php
$titulo = $titulo ?? 'Marcas Asociadas';
?>
<section class="brands-section">
    <div class="brands-container">
        <h2 class="text-4xl md:text-6xl font-extrabold text-[#64748b] mb-10 leading-none tracking-tight drop-shadow-sm"><?= htmlspecialchars($titulo) ?></h2>
        <div class="brands-carousel">
            <!-- Isapre Logos -->
            <div class="brand-item"><img src="<?= BASE_URL ?>/img/logo_colmena.svg" alt="Colmena"></div>
            <div class="brand-item"><img src="<?= BASE_URL ?>/img/logo_cruzblanca.svg" alt="CruzBlanca"></div>
            <div class="brand-item"><img src="<?= BASE_URL ?>/img/logo_consalud.svg" alt="Consalud"></div>
            <div class="brand-item"><img src="<?= BASE_URL ?>/img/logo_banmedica.svg" alt="Banmédica"></div>
            <div class="brand-item"><img src="<?= BASE_URL ?>/img/logo_vidatres.svg" alt="VidaTres"></div>
            <div class="brand-item"><img src="<?= BASE_URL ?>/img/logo_masvida.svg" alt="Nueva Masvida"></div>
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

.brands-carousel {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
    max-width: 1000px;
    margin: 0 auto;
}
.brand-item {
    transition: transform 0.3s ease, filter 0.3s ease;
    filter: grayscale(100%) opacity(60%);
    display: flex;
    align-items: center;
}
.brand-item:hover {
    transform: scale(1.05);
    filter: grayscale(0%) opacity(100%);
}
.brand-item img {
    height: 45px;
    width: auto;
}
</style>
