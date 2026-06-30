<?php
$titulo = $titulo ?? 'Últimos Artículos';
$limite = $limite ?? 3;

// Implementar un caché simple usando archivos
$cache_file = __DIR__ . '/../tmp_blog_cache.json';
$cache_time = 3600; // 1 hora de caché

$posts = [];
$use_mock = false;

// Función auxiliar simple para limpiar HTML
if (!function_exists('clean_excerpt_tags')) {
    function clean_excerpt_tags($string) {
        $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string);
        return trim(strip_tags(html_entity_decode($string)));
    }
}

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    $posts = json_decode(file_get_contents($cache_file), true);
} else {
    // Intentar obtener de la API REST de WordPress
    $url = "https://plansaludfacil.cl/blog_isapre/wp-json/wp/v2/posts?_embed&per_page={$limite}";
    
    // Usar cURL para mayor robustez
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout de 5 segundos para no colgar la home
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200 && $response) {
        $data = json_decode($response, true);
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $item) {
                $category = 'Blog';
                if (isset($item['_embedded']['wp:term'][0][0]['name'])) {
                    $category = $item['_embedded']['wp:term'][0][0]['name'];
                }
                
                $posts[] = [
                    'title' => html_entity_decode($item['title']['rendered']),
                    'excerpt' => clean_excerpt_tags($item['excerpt']['rendered']),
                    'link' => $item['link'],
                    'category' => $category
                ];
            }
            // Guardar en caché
            @file_put_contents($cache_file, json_encode($posts));
        } else {
            $use_mock = true;
        }
    } else {
        // Si falla la API, usar el mock o caché expirado si existe
        if (file_exists($cache_file)) {
            $posts = json_decode(file_get_contents($cache_file), true);
        } else {
            $use_mock = true;
        }
    }
}
?>
<section class="blog-preview-section">
    <div class="blog-preview-container">
        <h2><?= htmlspecialchars($titulo) ?></h2>
        <p class="blog-subtitle">Mantente informado sobre el sistema de salud privado en Chile.</p>
        <div class="blog-grid">
            <?php if (!$use_mock && !empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                <a href="<?= htmlspecialchars($post['link']) ?>" class="blog-card" target="_blank">
                    <div class="blog-badge"><?= htmlspecialchars($post['category']) ?></div>
                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                    <p><?= htmlspecialchars(mb_substr($post['excerpt'], 0, 100)) ?>...</p>
                    <span class="read-more">Leer artículo</span>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Mockup articles (Fallback en caso de que el blog no responda) -->
                <a href="https://plansaludfacil.cl/blog_isapre/" class="blog-card" target="_blank">
                    <div class="blog-badge">Guías Isapre</div>
                    <h3>¿Cómo cambiarse de Isapre sin complicaciones en 2026?</h3>
                    <p>Todo lo que necesitas saber sobre el proceso, plazos y documentos requeridos.</p>
                    <span class="read-more">Leer artículo</span>
                </a>
                <a href="https://plansaludfacil.cl/blog_isapre/" class="blog-card" target="_blank">
                    <div class="blog-badge">Salud Familiar</div>
                    <h3>La guía definitiva para elegir el mejor Plan de Salud Familiar</h3>
                    <p>Protege a tus hijos y cónyuge. Qué coberturas priorizar y cuáles evitar.</p>
                    <span class="read-more">Leer artículo</span>
                </a>
                <a href="https://plansaludfacil.cl/blog_isapre/" class="blog-card" target="_blank">
                    <div class="blog-badge">Perfiles</div>
                    <h3>Mejores planes de Isapre para Mujeres con y sin cobertura de parto</h3>
                    <p>Descubre las opciones más rentables según tu etapa de vida.</p>
                    <span class="read-more">Leer artículo</span>
                </a>
            <?php endif; ?>
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
