<?php
require_once __DIR__ . '/vendor/autoload.php';

class modRetailCrm extends \RetailCrm\ApiClient
{
    public $modx;

    public $orders;

    public $customers;

    private $ready = false;

    /**
     * modRetailCrm constructor.
     * @param modX $modx
     */
    public function __construct(modX $modx)
    {
        $this->modx = $modx;

        $apiKey = $this->getSetting('modretailcrm_apiKey');
        $crmUrl = $this->getSetting('modretailcrm_url');
        $site = $this->getSetting('modretailcrm_siteCode');


        $readyToConnect = $this->readyToConnect($apiKey, $crmUrl, $site);
        if ($readyToConnect) {
            parent::__construct($crmUrl, $apiKey, 'v5', $site);
            $this->loadClasses();
            $this->ready = true;
        }


    }


    /**
     * @param modUser $user
     * @param $mode
     */
    public function onUserSave(modUser $user, $mode)
    {
        if ($this->ready) {
            $this->customers->onUserSave($user, $mode);
        }

    }

    /**
     * @param msOrder $msOrder
     */
    public function msOnCreateOrder(msOrder $msOrder)
    {
        if ($this->ready) {
            $this->orders->msOnCreateOrder($msOrder);
        }
    }

    public function OnMODXInit()
    {
        if (!class_exists('ExtendsMsFields')) {
            require_once dirname(__FILE__) . '/extendsMsFields.class.php';
        }

        $ExtendsMsFields = new ExtendsMsFields($this);

        if (!$ExtendsMsFields || !$ExtendsMsFields instanceof ExtendsMsFields) {
            $this->log('Could not initialize class: "ExtendsMsFields"');
            return false;
        }

        $ExtendsMsFields->OnMODXInit();
    }

    public function msOnChangeOrderStatus($status, $order)
    {
        if (!class_exists('ChangeStatus')) {
            require_once dirname(__FILE__) . '/changeStatus.class.php';
        }

        $ChangeStatus = new ChangeStatus($this);

        if (!$ChangeStatus || !$ChangeStatus instanceof ChangeStatus) {
            $this->log('Could not initialize class: "ChangeStatus"');
            return false;
        }

        $ChangeStatus->msOnChangeOrderStatus($status, $order);
    }

    public function OnHandleRequest($post)
    {
        if (!class_exists('Triggers')) {
            require_once dirname(__FILE__) . '/triggers.class.php';
        }

        $Triggers = new Triggers($this);

        if (!$Triggers || !$Triggers instanceof Triggers) {
            $this->log('Could not initialize class: "Triggers"');
            return false;
        }

        $Triggers->OnHandleRequest($post);
    }


    /**
     * @param string $key
     * @return string|int
     */
    public function getSetting($key = '')
    {
        $setting_value = $this->modx->getOption($key);
        return $setting_value;
    }

    /**
     * @param string $error
     */
    public function log($error = '')
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR, '[modRetailCRM] ' . $error);
    }


    /**
     * Проверка на валидность адреса RetailCRM
     * @param string $crmUrl
     * @return bool
     */
    private function crmUrlIsValid($crmUrl = '')
    {
        if (filter_var($crmUrl, FILTER_VALIDATE_URL)) {
            $pos = strpos($crmUrl, 'retailcrm.ru');

            if ($pos === false) {
                $this->log('modretailcrm_url не является корректным URL адресом');
            }


            return true;
        }

        $this->log('modretailcrm_url не является URL адресом');
        return false;
    }

    /**
     * Проверка на заполненность базовых настроек для подключения к RetailCRM
     * @param string $apiKey
     * @param string $crmUrl
     * @param string $site
     * @return bool
     */
    private function readyToConnect($apiKey = '', $crmUrl = '', $site = '')
    {
        $readyToConnect = false;

        if (!empty($site) && !empty($apiKey) && !empty($crmUrl)) {
            $crmUrlIsValid = $this->crmUrlIsValid($crmUrl);
            if ($crmUrlIsValid) {
                $readyToConnect = true;
            }

            return $readyToConnect;
        }

        $this->log('Не заполнены базовые настройки для подключения к RetailCRM');
        return $readyToConnect;
    }

    /**
     * @return bool
     */
    private function loadClasses()
    {

        $this->loadOrdersClass();

        $this->loadCustomersClass();

        return true;
    }


    private function loadOrdersClass()
    {
        // Default classes
        if (!class_exists('Orders')) {
            require_once dirname(__FILE__) . '/orders.class.php';
        }

        $orders_class = 'Orders';

        // Custom orders class
        $custom_orders_class = $this->modx->getOption('modretailcrm_custom_orders_class');
        if (!empty($custom_orders_class)) {
            $custom_orders_class_decoded = json_decode($custom_orders_class, 1);
            if (is_array($custom_orders_class_decoded) && !empty($custom_orders_class_decoded)) {
                $custom_orders_class_name = $custom_orders_class_decoded[0];
                $custom_orders_class_path = $custom_orders_class_decoded[1];
                if ($custom_orders_class_name != 'Orders') {
                    if (file_exists($custom_orders_class_path)) {
                        require_once $custom_orders_class_path;
                    }

                    if (class_exists($custom_orders_class_name)) {
                        $orders_class = $custom_orders_class_name;
                    }
                }
            }

        }


        $this->orders = new $orders_class($this);

        if (!$this->orders instanceof ordersInterface) {
            $this->log('Could not initialize Orders class: "' . $orders_class . '"');
            return false;
        }
    }


    private function loadCustomersClass()
    {
        // Default classes
        if (!class_exists('Customers')) {
            require_once dirname(__FILE__) . '/customers.class.php';
        }

        $customers_class = 'Customers';

        // Custom customers class
        $custom_customers_class = $this->modx->getOption('modretailcrm_custom_customers_class');
        if (!empty($custom_customers_class)) {
            $custom_customers_class_decoded = json_decode($custom_customers_class, 1);
            if (is_array($custom_customers_class_decoded) && !empty($custom_customers_class_decoded)) {
                $custom_customers_class_name = $custom_customers_class_decoded[0];
                $custom_customers_class_path = $custom_customers_class_decoded[1];
                if ($custom_customers_class_name != 'Customers') {
                    if (file_exists($custom_customers_class_path)) {
                        require_once $custom_customers_class_path;
                    }

                    if (class_exists($custom_customers_class_name)) {
                        $customers_class = $custom_customers_class_name;
                    }
                }
            }

        }


        $this->customers = new $customers_class($this);

        if (!$this->customers instanceof customersInterface) {
            $this->log('Could not initialize Customers class: "' . $customers_class . '"');
            return false;
        }
    }

}
