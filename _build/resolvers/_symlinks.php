<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/modRetailCrm/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/modretailcrm')) {
            $cache->deleteTree(
                $dev . 'assets/components/modretailcrm/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/modretailcrm/', $dev . 'assets/components/modretailcrm');
        }
        if (!is_link($dev . 'core/components/modretailcrm')) {
            $cache->deleteTree(
                $dev . 'core/components/modretailcrm/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/modretailcrm/', $dev . 'core/components/modretailcrm');
        }
    }
}

return true;