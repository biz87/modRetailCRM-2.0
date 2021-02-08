<?php

class Triggers
{
    /** @var modX $modx */
    public $modx;

    /** @var modRetailCrm $modretailcrm */
    public $modretailcrm;

    public $pdo;

    /**
     * Customers constructor.
     * @param modRetailCrm $modretailcrm
     */
    public function __construct(modRetailCrm & $modretailcrm)
    {
        $this->modretailcrm = &$modretailcrm;

        $this->modx = &$modretailcrm->modx;

        $this->pdo = $this->modx->getService('pdoFetch');
    }

    public function OnHandleRequest($post)
    {
        switch ($post['retailCRM_action']) {
            case 'change_status':
                $status = $post['status'];
                $order_id = $post['order_id'];
                $post_status = trim(filter_var($status,FILTER_SANITIZE_STRING));
                $post_order_id = trim(filter_var($order_id,FILTER_SANITIZE_STRING));

                if(empty($post_status) || empty($post_order_id)){
                    break;
                }

                $statuses = $this->getSyncStatuses();

                foreach ($statuses as $status) {
                    if ($status['retailcrm_status_code'] === $post_status) {
                        if (strpos($post_order_id, '-')) {
                            $order_num = str_replace('-', '/', $post_order_id);
                            $order = $this->modx->getObject('msOrder', array('num' => $order_num));
                        } elseif (strpos($post_order_id, '/')) {
                            $order = $this->modx->getObject('msOrder', array('num' => $post_order_id));
                        } elseif (intval($post_order_id) > 0) {
                            $order = $this->modx->getObject('msOrder', array('id' => intval($post_order_id)));
                        }

                        if ($order) {
                            $order->set('status', $status['id']);
                            $order->save();
                        }
                    }
                }


                break;
        }
    }

    public function getSyncStatuses()
    {
        $syncStatuses = $this->modx->getOption('modretailcrm_sync_statuses');
        $output = [];
        if (!empty($syncStatuses)) {
            $syncStatuses = array_map("trim", explode(',', $syncStatuses));
            if (is_array($syncStatuses) && count($syncStatuses) > 0) {
                $statuses = $this->pdo->getCollection('msOrderStatus', array('id:IN' => $syncStatuses, 'retailcrm_status_code:!=' => ''));
                if(!empty($statuses)){
                    $output = $statuses;
                }
            }
        }

        return $output;
    }


}