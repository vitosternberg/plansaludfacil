<?php
$titulo = $titulo ?? 'Marcas Asociadas';
$marcas = [
    ['nombre' => 'Colmena', 'archivo' => 'colmena.png'],
    ['nombre' => 'Cruz Blanca', 'archivo' => 'cruzblanca.png'],
    ['nombre' => 'Consalud', 'archivo' => 'consalud.png'],
    ['nombre' => 'Banmédica', 'archivo' => 'banmedica.png'],
    ['nombre' => 'Vida Tres', 'archivo' => 'vidatres.png'],
    ['nombre' => 'Nueva Masvida', 'archivo' => 'nuevamasvida.png'],
];
$logo_base_url = (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . '/img';
$logo_base_path = dirname(__DIR__) . '/img/';
?>
<section class="brands-section">
    <div class="brands-container">
        <h2 class="text-4xl md:text-6xl font-extrabold text-[#64748b] mb-10 leading-none tracking-tight drop-shadow-sm"><?= htmlspecialchars($titulo) ?></h2>
        <div class="brands-carousel">
            <?php foreach ($marcas as $marca): ?>
                <?php
                $logo_path = $logo_base_path . $marca['archivo'];
                $logo_url = $logo_base_url . '/' . rawurlencode($marca['archivo']);
                $tiene_logo = is_file($logo_path);
                ?>
                <div class="brand-item" aria-label="<?= htmlspecialchars($marca['nombre']) ?>">
                    <span class="brand-item__shine"></span>
                    <?php if ($tiene_logo): ?>
                        <img
                            class="brand-item__logo"
                            src="<?= htmlspecialchars($logo_url) ?>"
                            alt="<?= htmlspecialchars($marca['nombre']) ?>"
                            loading="lazy">
                    <?php else: ?>
                        <span class="brand-item__label"><?= htmlspecialchars($marca['nombre']) ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
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
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto;
}
.brand-item {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 180px;
    min-height: 92px;
    padding: 18px 24px;
    border-radius: 18px;
    background: #ffffff;
    border: 1px solid rgba(2, 132, 199, 0.12);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.brand-item:hover {
    transform: translateY(-4px);
    border-color: rgba(0, 210, 255, 0.4);
    box-shadow: 0 18px 36px rgba(2, 132, 199, 0.14);
}
.brand-item__shine {
    position: absolute;
    inset: 0 auto auto 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #00d2ff 0%, #0284c7 100%);
}
.brand-item__label {
    font-size: 1.08rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #0284c7;
}
.brand-item__logo {
    max-width: 150px;
    max-height: 44px;
    width: auto;
    height: auto;
    object-fit: contain;
}

@media (max-width: 640px) {
    .brands-section {
        padding: 48px 16px;
    }

    .brands-carousel {
        justify-content: flex-start;
        flex-wrap: nowrap;
        gap: 14px;
        overflow-x: auto;
        padding: 4px 2px 12px;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
    }

    .brand-item {
        width: 78%;
        min-width: 78%;
        scroll-snap-align: center;
        flex-shrink: 0;
    }

    .brands-carousel::-webkit-scrollbar {
        height: 6px;
    }

    .brands-carousel::-webkit-scrollbar-thumb {
        background: rgba(2, 132, 199, 0.28);
        border-radius: 999px;
    }
}
</style>
