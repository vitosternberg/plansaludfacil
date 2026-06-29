<?php
$titulo = $titulo ?? 'Últimos Artículos';
?>
<section class="blog-preview-section">
    <div class="blog-preview-container">
        <h2><?= htmlspecialchars($titulo) ?></h2>
        <p class="blog-subtitle">Mantente informado sobre el sistema de salud privado en Chile.</p>
        <div class="blog-grid">
            <!-- Mockup articles -->
            <a href="/blog/guias-isapre/como-cambiarse-de-isapre" class="blog-card">
                <div class="blog-badge">Guías Isapre</div>
                <h3>¿Cómo cambiarse de Isapre sin complicaciones en 2026?</h3>
                <p>Todo lo que necesitas saber sobre el proceso, plazos y documentos requeridos.</p>
                <span class="read-more">Leer artículo</span>
            </a>
            <a href="/blog/salud-familiar/plan-de-salud-familiar" class="blog-card">
                <div class="blog-badge">Salud Familiar</div>
                <h3>La guía definitiva para elegir el mejor Plan de Salud Familiar</h3>
                <p>Protege a tus hijos y cónyuge. Qué coberturas priorizar y cuáles evitar.</p>
                <span class="read-more">Leer artículo</span>
            </a>
            <a href="/blog/planes-por-perfil/planes-de-isapre-para-mujeres" class="blog-card">
                <div class="blog-badge">Perfiles</div>
                <h3>Mejores planes de Isapre para Mujeres con y sin cobertura de parto</h3>
                <p>Descubre las opciones más rentables según tu etapa de vida.</p>
                <span class="read-more">Leer artículo</span>
            </a>
        </div>
    </div>
</section>

<style>
.blog-preview-section {
    padding: 80px 20px;
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
}
.blog-preview-container {
    max-width: 1200px;
    margin: 0 auto;
}
.blog-preview-container h2 {
    text-align: center;
    font-size: 2.5rem;
    color: #0f172a;
    margin-bottom: 10px;
}
.blog-subtitle {
    text-align: center;
    color: #64748b;
    font-size: 1.1rem;
    margin-bottom: 50px;
}
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}
.blog-card {
    background: #ffffff;
    padding: 30px;
    border-radius: 20px;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    transition: transform 0.3s ease;
    border: 1px solid #f1f5f9;
}
.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.08);
}
.blog-badge {
    display: inline-block;
    padding: 6px 12px;
    background: #e2e8f0;
    color: #475569;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.blog-card h3 {
    font-size: 1.25rem;
    color: #1e293b;
    line-height: 1.4;
    margin-bottom: 15px;
}
.blog-card p {
    color: #64748b;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
}
.read-more {
    color: #3b82f6;
    font-weight: 600;
    font-size: 0.9rem;
}
</style>
