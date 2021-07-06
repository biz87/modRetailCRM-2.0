<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $setup_fields = [
                'apikey',
                'siteCode',
                'url'
            ];

            foreach ($setup_fields as $field) {
                if (!empty($options[$field])) {
                    $setting = $modx->getObject('modSystemSetting', array('key' => 'modretailcrm_' . $field));
                    if ($setting) {
                        $setting->set('value', $options[$field]);
                        $setting->save();
                    }
                }
            }
            break;
    }
}
