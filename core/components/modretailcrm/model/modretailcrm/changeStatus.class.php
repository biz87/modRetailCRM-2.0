<?php

class ChangeStatus
{
    /** @var modX $modx */
    public $modx;

    /** @var modRetailCrm $modretailcrm */
    public $modretailcrm;

    /**
     * Customers constructor.
     * @param modRetailCrm $modretailcrm
     */
    public function __construct(modRetailCrm & $modretailcrm)
    {
        $this->modretailcrm = &$modretailcrm;

        $this->modx = &$modretailcrm->modx;
    }

    public function msOnChangeOrderStatus($status, $order)
    {
        $modx =& $this->modx;

        $modRetailCrm =& $this->modretailcrm;

        if ($status === 1) {
            return;
        }
        $syncStatuses = $modx->getOption('modretailcrm_sync_statuses');
        if (!empty($syncStatuses)) {
            $syncStatuses = array_map("trim", explode(',', $syncStatuses));
            if (is_array($syncStatuses) && count($syncStatuses) > 0) {
                $statusObj = $modx->getObject('msOrderStatus', array('id' => $status));
                if ($statusObj) {
                    $retailcrm_status_code = $statusObj->get('retailcrm_status_code');
                    if (!empty($retailcrm_status_code) && in_array($status, $syncStatuses)) {
                        $orderData = array();
                        $orderData['externalId'] = str_replace('/', '-', $order->num);
                        $orderData['status'] = $retailcrm_status_code;
                        $response = $modRetailCrm->request->ordersEdit($orderData, $by = 'externalId');
                        if ($modx->getOption('modretailcrm_log')) {
                            $modx->log(modX::LOG_LEVEL_ERROR, '[ModRetailCrm] - Результат отправки статуса заказа ' . print_r($response, 1));
                        }

                        //Если Статус оплачено - меняем статус платежа
                        if ($status = 2) {
                            $externalId = $orderData['externalId'];
                            $this->ordersPaymentEdit($order, $externalId);
                        }
                    }
                }
            }
        }
    }


    public function ordersPaymentEdit($order, $externalId)
    {
        $modx =& $this->modx;

        $modRetailCrm =& $this->modretailcrm;


        //Ищем номер платежа
        $orders = $modRetailCrm->request->ordersList(array('externalIds' => [$externalId]), 1, 20);
        if ($modx->getOption('modretailcrm_log')) {
            $modx->log(modX::LOG_LEVEL_ERROR, '[ModRetailCrm] -  Ищу данные о номере платежа ' . print_r($orders, 1));
        }
        $paymentId = 0;
        if (count($orders) > 0) {
            foreach ($orders['orders'][0]['payments'] as $payment) {
                $paymentId = $payment['id'];
            }
        }

        if ($paymentId > 0) {
            $payment = array();
            $payment['id'] = $paymentId;
            $payment['payment'] = $order->cost;
            $payment['paidAt'] = $order->updatedon;
            $payment['status'] = 'paid';

            $paymentEdit = $modRetailCrm->request->ordersPaymentEdit($payment, 'id');
            if ($modx->getOption('modretailcrm_log')) {
                $modx->log(modX::LOG_LEVEL_ERROR, '[ModRetailCrm] -  Обновляю статус платежа ' . print_r($paymentEdit, 1));
            }

        }
    }


}