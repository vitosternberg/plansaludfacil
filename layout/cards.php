<?php
// layout/cards.php

// Incluir los archivos de configuración y funciones de tu blog
require_once 'mi-blog/php/config.php';
require_once 'mi-blog/php/functions.php';

// Obtener las tres primeras publicaciones del blog
$posts = get_all_posts();
$featuredPosts = array_slice($posts, 0, 3);
?>

<section class="mb-8 p-2 px-2">
    <h2 class="text-2xl font-bold mb-4">Primeras Publicaciones del Blog</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mx-auto">
        <?php if (!empty($featuredPosts)) : ?>
            <?php foreach ($featuredPosts as $post) : ?>
                <div class="bg-white shadow-md rounded-md p-6">
                    <?php if (!empty($post['image_url'])) : ?>
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full rounded-md mb-4">
                    <?php endif; ?>
                    <h3 class="text-xl font-bold mb-2">
                        <a href="mi-blog/post.html?id=<?php echo $post['id']; ?>" class="text-blue-600 hover:underline">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </h3>
                    <?php if (!empty($post['subtitle'])) : ?>
                        <p class="text-lg text-gray-600 mb-2"><?php echo htmlspecialchars($post['subtitle']); ?></p>
                    <?php endif; ?>
                    <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <a href="mi-blog/post.html?id=<?php echo $post['id']; ?>" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Leer más
                    </a>
                    <p class="text-gray-500 text-sm mt-2">Publicado el <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-gray-600 col-span-3">No hay publicaciones disponibles en el blog.</p>
        <?php endif; ?>
    </div>
</section>