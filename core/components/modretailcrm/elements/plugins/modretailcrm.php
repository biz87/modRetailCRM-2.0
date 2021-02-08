<?php

if (!$modRetailCrm = $modx->getService(
    'modretailcrm',
    'modRetailCrm',
    MODX_CORE_PATH . 'components/modretailcrm/model/modretailcrm/',
    array($modx)
)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[modRetailCrm] - Not found class modRetailCrm');
    return;
}

switch ($modx->event->name) {
    case 'OnUserSave':
        $modRetailCrm->OnUserSave($user, $mode);
        break;
    case 'msOnCreateOrder':
        $modRetailCrm->msOnCreateOrder($msOrder);
        break;
    case 'msOnChangeOrderStatus':
        $modRetailCrm->msOnChangeOrderStatus($status, $order);
        break;
    case 'OnMODXInit':
        $modRetailCrm->OnMODXInit();
        break;
    case 'msOnManagerCustomCssJs':
        if ($page != 'settings') return;
        $modx->controller->addLastJavascript(MODX_ASSETS_URL . 'components/modretailcrm/js/mgr/modretailcrm.js');
        break;
    case 'OnHandleRequest':
        if (!empty($_POST['retailCRM_action'])) {
            $modRetailCrm->OnHandleRequest($_POST);
        }

        break;
}