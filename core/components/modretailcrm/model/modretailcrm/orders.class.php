<?php

interface ordersInterface
{
    public function msOnCreateOrder($msOrder);
}

class Orders implements ordersInterface
{
    /** @var modX $modx */
    public $modx;

    /** @var modRetailCrm $modretailcrm */
    public $modretailcrm;

    /** @var pdoFetch $pdo */
    public $pdo;

    public $allow_msoptionsprice = false;
    public $is_mspromocode = false;

    public function __construct(modRetailCrm $modretailcrm)
    {
        $this->modretailcrm = $modretailcrm;
        $this->modx = $modretailcrm->modx;
        $this->pdo = $this->modx->getService('pdoFetch');
    }

    public function msOnCreateOrder($msOrder)
    {
        $packages = $this->modx->getOption('extension_packages');
        $packages = json_decode($packages, true);
        $this->allow_msoptionsprice = $this->modx->getOption('modretailcrm_allow_msoptionsprice');

        foreach ($packages as $package) {
            if (isset($package['mspromocode'])) {
                $this->is_mspromocode = true;
            }
        }

        $order = $this->orderCombine($msOrder);

        $orderData = array();

        $customerData = $this->getCustomer($order);
        if ($customerData) {
            $orderData = array_merge($orderData, $customerData);
        }

        $orderData['externalId'] = str_replace('/', '-', $order['num']);
        //$orderData['externalId'] = $order['product_id']; Желающим идентифицировать заказ по id


        $itemsData = $this->getItemsData($order['products']);
        $orderData['items'] = $itemsData;

        if ($order['weight'] > 0) {
            $orderData['weight'] = $order['weight'];
        }

        $orderData['customerComment'] = $order['address']['comment'];

        $deliveryData = $this->getdeliveryData($order);
        if (!empty($deliveryData)) {
            $orderData['delivery'] = $deliveryData;
        }

        if (!empty($order['payment']['retailcrm_payment_code'])) {
            $orderData['payments'][0]['type'] = $order['payment']['retailcrm_payment_code'];
        }

        $paymentData = $this->getPaymentData($order['payment']);
        if (!empty($paymentData)) {
            $orderData['payments'][0] = $paymentData;
        }

        if (!empty($order['sale'])) {
            $orderData['customFields']['promocode'] = $order['sale']['promocode'];
        }
        if (!empty($order['sale']['discount_amount'])) {
            $orderData['discountManualAmount'] = $order['sale']['discount_amount'];
        }

        if ($this->modx->getOption('modretailcrm_add_crm_number')) {
            $orderData['number'] = $order['num'];
        }

        if ($this->modx->getOption('modretailcrm_log')) {
            $this->modretailcrm->log('Итоговый набор данных ' . print_r($orderData, 1));
        }

        $response = $this->modretailcrm->request->ordersCreate($orderData);

        if ($this->modx->getOption('modretailcrm_log')) {
            $this->modretailcrm->log('Результат отправки заказа ' . print_r($response, 1));
        }

        if ($this->modx->getOption('modretailcrm_rewrite_num')) {
            if ($response->isSuccessful()) {
                $num = $response['order']['number'];
                $msOrder->set('num', $num);
                $msOrder->save();
            }
        }
    }


    /**
     * @param msOrder $msOrder
     * @return mixed
     */
    public function orderCombine($msOrder)
    {
        $order = $msOrder->toArray();
        $order['address'] = $this->pdo->getArray('msOrderAddress', array('id' => $order['address']), array('sortby' => 'id'));
        $order['delivery'] = $this->pdo->getArray('msDelivery', array('id' => $order['delivery']), array('sortby' => 'id'));
        $order['payment'] = $this->pdo->getArray('msPayment', array('id' => $order['payment']), array('sortby' => 'id'));
        $order['profile'] = $this->pdo->getArray('modUserProfile', array('internalKey' => $order['user_id']), array('sortby' => 'id'));
        $order['products'] = $this->pdo->getCollection('msOrderProduct', array('order_id' => $order['id']), array('sortby' => 'id'));
        if ($this->is_mspromocode) {
            $order['sale'] = $this->pdo->getArray('mspcOrder', array('order_id' => $order['id']));
        }

        return $order;
    }

    /**
     * @param array $order
     * @return array
     */
    public function getCustomer($order = array())
    {
        $output = array();

        //Проверяю наличие пользователя в базе CRM
        $user_response = $this->modretailcrm->request->customersGet($order['user_id'], 'externalId');

        if ($this->modx->getOption('modretailcrm_log')) {
            $this->modretailcrm->log('Ищем клиента в базе RetailCRM ' . print_r($user_response, 1));
        }

        if ($user_response->getStatusCode() == 404) {
            $customer_profile = $this->pdo->getArray('modUserProfile', array('internalKey' => $order['user_id']));

            $data = $this->modretailcrm->customers->getCustomerDataFromProfile($customer_profile);
            $this->modretailcrm->customers->createCustomer($data);
        }

        $output['customer']['externalId'] = $order['user_id'];
        $output['firstName'] = !empty($order['address']['receiver']) ? $order['address']['receiver'] : $order['profile']['fullname'];
        $output['phone'] = !empty($order['address']['phone']) ? $order['address']['phone'] : $order['profile']['phone'];
        $output['email'] = $order['profile']['email'];

        $tmpName = explode(' ', $output['firstName']);
        if (count($tmpName) == 3) {
            $output['lastName'] = $tmpName[0];
            $output['firstName'] = $tmpName[1];
            $output['patronymic'] = $tmpName[2];
        }

        return $output;
    }


    /**
     * @param array $products
     * @return array
     */
    public function getItemsData($products = array())
    {
        if (empty($products)) {
            $this->modretailcrm->log('Не удалось получить список товаров в заказе');
        }

        $itemsData = array();

        foreach ($products as $key => $product) {
            // Возможность  получить  модификацию msOptionsPrice
            //$modification = $modx->getObject('msopModification', $product['options']['modification']);

            $itemsData[$key]['initialPrice'] = $product['price'];
            $itemsData[$key]['productName'] = $product['name'];
            $itemsData[$key]['quantity'] = $product['count'];

            if ($this->allow_msoptionsprice && !empty($product['options']['modification'])) {
                $modification_id = 'mod-' . $product['product_id'] . '-' . $product['options']['modification'];
                $itemsData[$key]['offer']['externalId'] = $modification_id;
            } else {
                $itemsData[$key]['offer']['externalId'] = $product['product_id'];
            }

            foreach ($product['options'] as $k => $v) {
                if (!empty($v)) {
                    switch ($k) {
                        case 'modifications':
                        case 'modification':
                            // if($allow_msoptionsprice){
                            //     foreach($v as $mod){
                            //         $modification = $modx->getObject('msopModification', $mod);
                            //         $orderData['items'][$key]['properties'][] = array('name' => $k, 'value' => $modification->name);
                            //     }

                            // }
                            break;
                        case 'size':
                            $itemsData[$key]['properties'][] = array('name' => 'Размер', 'value' => $v);
                            break;
                        case 'color':
                            $itemsData[$key]['properties'][] = array('name' => 'Цвет', 'value' => $v);
                            break;
                        default:
                            $itemsData[$key]['properties'][] = array('name' => $k, 'value' => $v);
                    }


                }
            }
        }
        return $itemsData;
    }

    /**
     * @param array $address
     * @return array
     */
    public function getDeliveryAddressData($address = array())
    {
        if (empty($address)) {
            $this->modretailcrm->log('Не удалось получить адрес заказа');
        }

        $deliveryAddressData = array();

        $fields = array(
            'index' => 'Индекс',
            'country' => 'Страна',
            'region' => 'Регион',
            'city' => 'Город',
            'metro' => 'Метро',
            'street' => 'Улица',
            'building' => 'Дом',
            'room' => 'Квартира\офис'
        );
        $addressText = '';
        foreach ($fields as $field => $comment) {
            if (!empty($address[$field])) {
                $addressText .= $comment . ':' . $address[$field] . ' 
                ';
                if ($field == 'room') {
                    $deliveryAddressData['flat'] = $address[$field];
                } else {
                    $deliveryAddressData[$field] = $address[$field];
                }

            }
        }

        $deliveryAddressData['text'] = $addressText;

        return $deliveryAddressData;
    }

    /**
     * @param array $order
     * @return array
     */
    public function getDeliveryData($order = array())
    {
        $deliveryData = array();

        $deliveryData['cost'] = $order['delivery_cost'];

        if (!empty($order['delivery']['retailcrm_delivery_code'])) {
            $deliveryData['code'] = $order['delivery']['retailcrm_delivery_code'];
        }

        $deliveryAddressData = $this->getDeliveryAddressData($order['address']);
        $deliveryData['address'] = $deliveryAddressData;

        return $deliveryData;
    }

    /**
     * @param array $payment
     * @return array
     */
    public function getPaymentData($payment = array())
    {
        if (empty($payment)) {
            $this->modretailcrm->log('Не удалось получить информацию об оплате');
        }

        $paymentData = array();

        if (!empty($order['payment']['retailcrm_payment_code'])) {
            $paymentData['type'] = $payment['retailcrm_payment_code'];
        }

        return $paymentData;
    }
}
