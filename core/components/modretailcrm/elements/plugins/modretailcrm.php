<?php

/**
 * @var modX  $modx
 * @var array $scriptProperties
 */

if (
    !$modRetailCrm = $modx->getService(
        'modretailcrm',
        'modRetailCrm',
        MODX_CORE_PATH . 'components/modretailcrm/model/modretailcrm/',
        array($modx)
    )
) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[modRetailCrm] - Not found class modRetailCrm');
    return;
}

switch ($modx->event->name) {
    case 'OnUserSave':
        /**
         * @var modUser  $user
         * @var string $mode
         */
        $modRetailCrm->OnUserSave($user, $mode);
        break;
    case 'msOnCreateOrder':
        /**
         * @var msOrder  $msOrder
         */
        $modRetailCrm->msOnCreateOrder($msOrder);
        break;
    case 'msOnChangeOrderStatus':
        /**
         * @var int  $status
         * @var msOrder  $order
         */
        $modRetailCrm->msOnChangeOrderStatus($status, $order);
        break;
    case 'OnMODXInit':
        $modRetailCrm->OnMODXInit();
        break;
    case 'msOnManagerCustomCssJs':
        /**
         * @var string  $page
         */
        if ($page != 'settings') {
            return;
        }
        $modx->controller->addLastJavascript(MODX_ASSETS_URL . 'components/modretailcrm/js/mgr/modretailcrm.js');
        break;
    case 'OnHandleRequest':
        if (!empty($_POST['retailCRM_action'])) {
            $modRetailCrm->OnHandleRequest($_POST);
        }
        break;
}
