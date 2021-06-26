<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;

    $items = array(
        'retailcrm_delivery_code' => array(
            'ru' => 'Символьный код способа доставки в RetailCRM',
            'en' => 'Character code of delivery method in RetailCRM'
        ),
        'retailcrm_payment_code' => array(
            'ru' => 'Символьный код способа оплаты в RetailCRM',
            'en' => 'Character code of payment method in RetailCRM'
        ),
        'retailcrm_status_code' => array(
            'ru' => 'Символьный код статуса заказа в RetailCRM',
            'en' => 'Character order status code in RetailCRM'
        ),
    );

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            foreach ($items as $key => $value) {
                foreach ($value as $language => $lexicon) {
                    $q = $modx->newObject('modLexiconEntry');
                    $data = array(
                        'name' => $key,
                        'value' => $lexicon,
                        'topic' => 'default',
                        'namespace' => 'minishop2',
                        'language' => $language
                    );
                    $q->fromArray($data);
                    $q->save();
                }

            }
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            foreach ($items as $key => $value) {
                foreach ($value as $language => $lexicon) {
                    $modx->removeCollection('modLexiconEntry', array('name' => $key));
                }
            }
            break;
    }
}
