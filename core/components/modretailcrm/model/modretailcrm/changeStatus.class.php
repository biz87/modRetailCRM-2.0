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
    public function __construct(modRetailCrm $modretailcrm)
    {
        $this->modretailcrm = $modretailcrm;
        $this->modx = $modretailcrm->modx;
    }

    /**
     * @param int $status_id
     * @param msOrder $order
     */
    public function msOnChangeOrderStatus($status_id, $order)
    {
        if ($status_id === 1) {
            return;
        }

        $syncStatuses = $this->modx->getOption('modretailcrm_sync_statuses');
        if (!empty($syncStatuses)) {
            $syncStatuses = array_map("trim", explode(',', $syncStatuses));
            if (is_array($syncStatuses) && count($syncStatuses) > 0) {
                $msOrderStatus = $this->modx->getObject('msOrderStatus', array('id' => $status_id));
                if ($msOrderStatus) {
                    $retailcrm_status_code = $msOrderStatus->get('retailcrm_status_code');
                    if (!empty($retailcrm_status_code) && in_array($status_id, $syncStatuses)) {
                        $orderData = array();
                        $orderData['externalId'] = str_replace('/', '-', $order->get('num'));
                        $orderData['status'] = $retailcrm_status_code;
                        $response = $this->modretailcrm->request->ordersEdit($orderData, $by = 'externalId');
                        if ($this->modx->getOption('modretailcrm_log')) {
                            $this->modx->log(
                                modX::LOG_LEVEL_ERROR,
                                '[ModRetailCrm] - Результат отправки статуса заказа ' . print_r($response, 1)
                            );
                        }

                        //Если Статус оплачено - меняем статус платежа
                        if ($status_id == 2) {
                            $externalId = $orderData['externalId'];
                            $this->ordersPaymentEdit($order, $externalId);
                        }
                    }
                }
            }
        }
    }


    /**
     * @param msOrder $order
     * @param integer|string $externalId
     */
    public function ordersPaymentEdit($order, $externalId)
    {
        //Ищем номер платежа
        $orders = $this->modretailcrm->request->ordersList(array('externalIds' => [$externalId]), 1, 20);
        if ($this->modx->getOption('modretailcrm_log')) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                '[ModRetailCrm] -  Ищу данные о номере платежа ' . print_r($orders, 1)
            );
        }
        $paymentId = 0;
        if (count($orders['orders']) > 0) {
            foreach ($orders['orders'][0]['payments'] as $payment) {
                $paymentId = $payment['id'];
            }
        }

        if ($paymentId > 0) {
            $payment = array();
            $payment['id'] = $paymentId;
            $payment['payment'] = $order->get('cost');
            $payment['paidAt'] = $order->get('updatedon');
            $payment['status'] = 'paid';

            $paymentEdit = $this->modretailcrm->request->ordersPaymentEdit($payment, 'id');
            if ($this->modx->getOption('modretailcrm_log')) {
                $this->modx->log(
                    modX::LOG_LEVEL_ERROR,
                    '[ModRetailCrm] -  Обновляю статус платежа ' . print_r($paymentEdit, 1)
                );
            }
        }
    }
}
