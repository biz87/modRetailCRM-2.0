<?php

class ExtendsMsFields
{
    /** @var modX $modx */
    public $modx;

    /** @var modRetailCrm $modretailcrm */
    public $modretailcrm;

    /**
     * Customers constructor.
     * @param modRetailCrm $modretailcrm
     */
    public function __construct(modRetailCrm $modretailcrm)
    {
        $this->modretailcrm = $modretailcrm;
        $this->modx = $modretailcrm->modx;
    }

    public function OnMODXInit()
    {
        $this->loadMsClasses();
    }

    /**
     *  extends miniShop2 map
     */
    public function loadMsClasses()
    {
        $this->modx->loadClass('msDelivery');
        $this->modx->map['msDelivery']['fields']['retailcrm_delivery_code'] = '';
        $this->modx->map['msDelivery']['fieldMeta']['retailcrm_delivery_code'] = array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => true,
        );

        $this->modx->loadClass('msPayment');
        $this->modx->map['msPayment']['fields']['retailcrm_payment_code'] = '';
        $this->modx->map['msPayment']['fieldMeta']['retailcrm_payment_code'] = array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => true,
        );

        $this->modx->loadClass('msOrderStatus');
        $this->modx->map['msOrderStatus']['fields']['retailcrm_status_code'] = '';
        $this->modx->map['msOrderStatus']['fieldMeta']['retailcrm_status_code'] = array(
            'dbtype' => 'varchar',
            'precision' => '255',
            'phptype' => 'string',
            'null' => true,
        );

    }


}
