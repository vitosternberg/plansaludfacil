<?php
/**
 * components/schema_service.php
 * Schema.org JSON-LD: Service (para páginas de planes/cotización).
 *
 * Variables esperadas:
 *   $svc_name          — Nombre del servicio (string)
 *   $svc_description   — Descripción (string)
 *   $svc_url           — URL canónica (string, opcional, usa $_SERVER si no se pasa)
 *   $svc_provider_name — Nombre del proveedor (string, default: 'Plan Salud Fácil')
 *   $svc_category      — Categoría (string, default: 'Planes de ISAPRE')
 */

$svc_name          = $svc_name ?? 'Plan de ISAPRE';
$svc_description   = $svc_description ?? '';
$svc_url           = $svc_url ?? ('https://plansaludfacil.cl' . strtok($_SERVER['REQUEST_URI'] ?? '/', '?'));
$svc_provider_name = $svc_provider_name ?? 'Plan Salud Fácil';
$svc_category      = $svc_category ?? 'Planes de ISAPRE';
?>

<script type="application/ld+json">
<?= json_encode([
    '@context'     => 'https://schema.org',
    '@type'        => 'Service',
    'name'         => $svc_name,
    'description'  => $svc_description,
    'provider'     => [
        '@type' => 'Organization',
        'name'  => $svc_provider_name,
    ],
    'category'     => $svc_category,
    'url'          => $svc_url,
    'areaServed'   => [
        '@type' => 'Country',
        'name'  => 'Chile',
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
