<?php
$titulo = $titulo ?? 'Preguntas Frecuentes';
$preguntas = $preguntas ?? [];
?>
<section class="faq-section">
    <div class="faq-container">
        <h2><?= htmlspecialchars($titulo) ?></h2>
        <div class="faq-accordion">
            <?php foreach ($preguntas as $pregunta => $respuesta): ?>
                <details class="faq-item">
                    <summary><?= htmlspecialchars($pregunta) ?></summary>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars($respuesta) ?></p>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.faq-section {
    padding: 80px 20px;
    background-color: #ffffff;
    font-family: 'Inter', sans-serif;
}
.faq-container {
    max-width: 800px;
    margin: 0 auto;
}
.faq-container h2 {
    text-align: center;
    font-size: 2.5rem;
    color: #0f172a;
    margin-bottom: 40px;
}
.faq-item {
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.faq-item[open] {
    background: #ffffff;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}
.faq-item summary {
    padding: 20px 25px;
    font-weight: 600;
    font-size: 1.1rem;
    color: #1e293b;
    cursor: pointer;
    list-style: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.faq-item summary::-webkit-details-marker {
    display: none;
}
.faq-item summary::after {
    content: '+';
    font-size: 1.5rem;
    color: #3b82f6;
    transition: transform 0.3s ease;
}
.faq-item[open] summary::after {
    transform: rotate(45deg);
    color: #ef4444;
}
.faq-answer {
    padding: 0 25px 20px 25px;
    color: #475569;
    line-height: 1.6;
    border-top: 1px solid #e2e8f0;
    margin-top: 10px;
    padding-top: 20px;
}
</style>
