<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache limpiado correctamente.";
} else {
    echo "OPcache no esta habilitado en este servidor.";
}
