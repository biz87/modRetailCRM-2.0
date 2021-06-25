<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;

    $my_fields = array(
        'msDelivery' => array('retailcrm_delivery_code'),
        'msPayment' => array('retailcrm_payment_code'),
        'msOrderStatus' => array('retailcrm_status_code')
    );

    $manager = $modx->getManager();

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx->loadClass('msDelivery');
            $modx->map['msDelivery']['fields']['retailcrm_delivery_code'] = '';
            $modx->map['msDelivery']['fieldMeta']['retailcrm_delivery_code'] = array(
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => true,
            );

            $modx->loadClass('msPayment');
            $modx->map['msPayment']['fields']['retailcrm_payment_code'] = '';
            $modx->map['msPayment']['fieldMeta']['retailcrm_payment_code'] = array(
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => true,
            );

            $modx->loadClass('msOrderStatus');
            $modx->map['msOrderStatus']['fields']['retailcrm_status_code'] = '';
            $modx->map['msOrderStatus']['fieldMeta']['retailcrm_status_code'] = array(
                'dbtype' => 'varchar',
                'precision' => '255',
                'phptype' => 'string',
                'null' => true,
            );

            foreach ($my_fields as $class => $fields) {
                $sql = "SHOW COLUMNS FROM {$modx->getTableName($class)}";
                $q = $modx->prepare($sql);
                $q->execute();
                $base_fields = $q->fetchAll(PDO::FETCH_ASSOC);

                foreach ($fields as $field) {
                    $field_is_exists = false;
                    foreach ($base_fields as $base_field) {
                        if ($base_field['Field'] == $field) {
                            $field_is_exists = true;
                            break;
                        }
                    }

                    if (!$field_is_exists) {
                        $manager->addField($class, $field);
                    }
                }
            }
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            foreach ($my_fields as $class => $fields) {
                foreach ($fields as $field) {
                    $manager->removeField($class, $field);
                }
            }
            break;
    }
}
