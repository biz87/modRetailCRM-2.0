<?php

interface customersInterface
{
    public function OnUserSave(modUser $user, $mode);

    public function createCustomer($data);

    public function editCustomer($data);
}

class Customers implements customersInterface
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
     * @param modUser $user
     * @param $mode
     */
    public function OnUserSave(modUser $user, $mode)
    {
        $modRetailCrm = $this->modretailcrm;

        $modx = $this->modx;


        $profile = $user->getOne('Profile');

        if ($profile) {
            $customer = $this->getCustomerDataFromProfile($profile);

            switch ($mode) {
                case modSystemEvent::MODE_NEW:
                    $this->createCustomer($customer);
                    break;
                case modSystemEvent::MODE_UPD:
                    $this->editCustomer($customer);
                    break;
            }
        }
    }


    /**
     * @param array $data
     */
    public function createCustomer($data = array())
    {
        $response = $this->modretailcrm->request->customersCreate($data);
        $isSuccess = $response->isSuccessful();
        if (!$isSuccess) {
            $this->modretailcrm->log(print_r($response, 1));
        }
    }


    /**
     * @param array $data
     */
    public function editCustomer($data = array())
    {
        $response = $this->modretailcrm->request->customersEdit($data);

        $isSuccess = $response->isSuccessful();
        if (!$isSuccess) {
            $this->modretailcrm->log(print_r($response, 1));
        }
    }


    /**
     * @param  $userProfile
     * @return array
     */
    public function getCustomerDataFromProfile($userProfile)
    {

        $customer = array();
        if(!is_array($userProfile)){
            $userProfile = $userProfile->toArray();
        }

        $customer['externalId'] = $userProfile['internalKey'];
        $customer['firstName'] = $userProfile['fullname'];
        $customer['email'] = $userProfile['email'];
        if (!empty($userProfile['phone'])) {
            $customer['phones'][0]['number'] = $userProfile['phone'];
        }
        if (!empty($userProfile['mobilephone'])) {
            $customer['phones'][1]['number'] = $userProfile['mobilephone'];
        }

        return $customer;
    }
}
