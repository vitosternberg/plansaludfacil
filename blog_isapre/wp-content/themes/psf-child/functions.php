<?php
/**
 * Child theme PSF — RRSS en mu-plugins/psf-rrss.php
 */

$psf_rrss_plugin = WP_CONTENT_DIR . '/mu-plugins/psf-rrss.php';
if (is_readable($psf_rrss_plugin)) {
    require_once $psf_rrss_plugin;
}
