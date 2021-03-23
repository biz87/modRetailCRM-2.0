<?php

return [
    'apiKey'       => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'area_modretailcrm_auth',
    ),
    'siteCode'     => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'area_modretailcrm_auth',
    ),
    'url' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'area_modretailcrm_auth',
    ),


    'log' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modretailcrm_main',
    ),
    'sync_statuses' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'modretailcrm_main',
    ),
    'rewrite_num' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modretailcrm_main',
    ),
    'add_crm_number' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'modretailcrm_main',
    ),


    'allow_msoptionsprice' => array(
        'xtype' => 'combo-boolean',
        'value' => false,
        'area'  => 'area_modretailcrm_components',
    ),


    'custom_orders_class' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area'  => 'area_modretailcrm_classes',
    ),
    'custom_customers_class' => array(
        'xtype' => 'textfield',
        'value' => '',
        'area'  => 'area_modretailcrm_classes',
    ),

];
