<?php
/**
 * components/indice_contenido.php
 * Tabla de Contenidos (ToC) con anclas a H2s de la página.
 *
 * Variables esperadas:
 *   $toc_items — Array de ['id' => '...', 'label' => '...']
 */
$toc_items = $toc_items ?? [];
if (empty($toc_items)) return;
?>

<nav aria-label="Tabla de contenidos" class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 md:p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
            <iconify-icon icon="mdi:format-list-bulleted" width="20" class="text-blue-600"></iconify-icon>
            En esta página
        </h2>
        <ol class="space-y-1.5">
            <?php foreach ($toc_items as $i => $item): ?>
            <li class="flex items-start gap-2">
                <span class="text-blue-600 font-bold text-sm min-w-[1.5rem]"><?= $i + 1 ?>.</span>
                <a href="#<?= htmlspecialchars($item['id']) ?>"
                   class="text-gray-600 hover:text-blue-700 transition text-sm md:text-base">
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>
