<?php
$items = $items ?? [];
?>
<section class="benefits-section">
    <div class="benefits-container">
        <div class="benefits-grid">
            <?php foreach ($items as $item): ?>
                <div class="benefit-card">
                    <div class="benefit-icon"><?= $item['icono'] ?></div>
                    <h3><?= htmlspecialchars($item['titulo']) ?></h3>
                    <p><?= htmlspecialchars($item['texto']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.benefits-section {
    padding: 80px 20px;
    background-color: #ffffff;
    font-family: 'Inter', sans-serif;
}
.benefits-container {
    max-width: 1200px;
    margin: 0 auto;
}
.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}
.benefit-card {
    text-align: center;
    padding: 30px;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.benefit-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}
.benefit-icon {
    font-size: 3rem;
    margin-bottom: 20px;
}
.benefit-card h3 {
    font-size: 1.4rem;
    color: #1e293b;
    margin-bottom: 15px;
    font-weight: 700;
}
.benefit-card p {
    color: #64748b;
    line-height: 1.6;
}
</style>
