<?php
$titulo = $titulo ?? '¿Listo para cotizar?';
$cta_texto = $cta_texto ?? 'Contactar Asesor';
$cta_link = $cta_link ?? '#';
?>
<section class="cta-footer-section">
    <div class="cta-footer-content">
        <h2><?= htmlspecialchars($titulo) ?></h2>
        <a href="<?= htmlspecialchars($cta_link) ?>" class="btn-whatsapp">
            <span class="icon">💬</span> <?= htmlspecialchars($cta_texto) ?>
        </a>
    </div>
</section>

<style>
.cta-footer-section {
    padding: 100px 20px;
    background: linear-gradient(135deg, #2c5364, #203a43, #0f2027);
    color: white;
    text-align: center;
    font-family: 'Inter', sans-serif;
}
.cta-footer-content {
    max-width: 600px;
    margin: 0 auto;
}
.cta-footer-content h2 {
    font-size: 2.5rem;
    margin-bottom: 40px;
    font-weight: 800;
}
.btn-whatsapp {
    background-color: #25D366;
    color: white;
    padding: 16px 40px;
    border-radius: 30px;
    font-weight: bold;
    text-decoration: none;
    font-size: 1.2rem;
    transition: background-color 0.3s ease, transform 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 20px rgba(37, 211, 102, 0.3);
}
.btn-whatsapp:hover {
    background-color: #1ebe5d;
    transform: translateY(-3px);
    color: white;
}
.btn-whatsapp .icon {
    font-size: 1.4rem;
}
</style>
