<?php

class TmobLabs_Tappz_Model_Customer_Api extends Mage_Customer_Model_Api_Resource
{
    /**
     * @param $data
     * @return array
     */
    protected function _prepareCustomerData($data)
    {
        $genderAttributeCode = Mage::getStoreConfig('tappz/customer/gender');
        $emailAttributeCode = Mage::getStoreConfig('tappz/customer/email');
        $phoneAttributeCode = Mage::getStoreConfig('tappz/customer/phone');
        $birthDateAttributeCode = Mage::getStoreConfig('tappz/customer/birthDate');
        $result = array();
        $result['entity_id'] = $data->customerId;
        $result['firstname'] = $data->firstName;
        $result['lastname'] = $data->lastName;
        $result['password'] = $data->password;
        $result[$genderAttributeCode] = $data->gender;
        $result['isSubscribed'] = $data->isSubscribed;
        $result[$emailAttributeCode] = $data->email;
        $result[$phoneAttributeCode] = $data->phone;
        $result[$birthDateAttributeCode] = $data->birthDate;
        return $result;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function info($customerId)
    {
        $genderAttributeCode = Mage::getStoreConfig('tappz/customer/gender');
        $emailAttributeCode = Mage::getStoreConfig('tappz/customer/email');
        $phoneAttributeCode = Mage::getStoreConfig('tappz/customer/phone');
        $birthDateAttributeCode = Mage::getStoreConfig('tappz/customer/birthDate');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer) {
            $this->_fault("404.10", "Customer is not found.");
        }
        $result = array();
        $result['customerId'] = "";
        $result['fullName'] = "";
        $result['firstName'] = "";
        $result['lastName'] = "";
        $result['gender'] = "";
        $result['isSubscribed'] = false;
        $result['isAccepted'] = false;
        $result['email'] = "";
        $result['password'] = null;
        $result['phone'] = "";
        $result['birthDate'] = "";
        $result['points'] = 0;
        $result['addresses'] = array();
        $result['giftCheques'] = array();
        $result['customerId'] = $customer->getId();
        $result['fullName'] = $customer->getName();
        $result['firstName'] = $customer->getFirstname() . ($customer->getMiddleName() ? (' ' . $customer->getMiddleName()) : '');
        $result['lastName'] = $customer->getLastName();
        $result['gender'] = $customer->getData($genderAttributeCode);
        $result['isAccepted'] = !$customer->isConfirmationRequired();
        $result['email'] = $customer->getData($emailAttributeCode);
        $result['phone'] = $customer->getData($phoneAttributeCode);
        $result['birthDate'] = $customer->getData($birthDateAttributeCode);
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getData($emailAttributeCode));
        $result['isSubscribed'] = (bool)$subscriber->getId();
        $points = Mage::getModel('enterprise_reward/reward');
        if ($points) {
            $points = $points->setCustomer($customer)
                ->setWebsiteId(Mage::app()->getWebsite()->getId())
                ->loadByCustomer()
                ->getPointsBalance();
        }
        $result['points'] = $points;

        $result['addresses'] = Mage::getSingleton('tappz/customer_address_api')->getList($customer->getId()); /// ??????

        $result['giftCheques'] = array();
        
        return $result;
    }

    /**
     * @param $userName
     * @param $password
     * @return array
     */
    public function login($userName, $password)
    {
        $storeId = Mage::getStoreConfig('tappz/general/store');
        $store = Mage::getModel('core/store')->load($storeId);

        $customer = Mage::getModel('customer/customer')
            ->setStore($store);
        try {
            $customer->authenticate($userName, $password);
        } catch (Exception $e) {
            switch ($e->getCode()) {
                case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                    $this->_fault('invalid_data', "Email is not confirmed.");
                    break;
                case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                    $this->_fault('invalid_data', "Invalid email or password.");
                    break;
                default:
                    $this->_fault('invalid_data', $e->getMessage());
                    break;
            }
        }
        $customer = $customer->loadByEmail($userName);
        return $this->info($customer->getId());
    }

    /**
     * @param $facebookAccessToken
     * @param $facebookUserId
     * todo should be done
     */
    public function facebookLogin($facebookAccessToken, $facebookUserId)
    {
        $storeId = Mage::getStoreConfig('tappz/general/store');
         $store = Mage::getModel('core/store')->load($storeId);
         $curl = curl_init();
         curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
         curl_setopt($curl, CURLOPT_URL, "https://graph.facebook.com/$facebookUserId?access_token=$facebookAccessToken");
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         $result = curl_exec($curl);
         $userInfo =  json_decode($result);
         curl_close($curl);
         $email = $userInfo->email;
         $customerExist = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('email', "$email" )
                ->getFirstItem();
         if(($customerExist['entity_id']) > 0){
                $customer = Mage::getModel('customer/customer')->setStore($store);
               $customer->loadByEmail($email);
            return $this->info($customer->getId());
         }else{
         $password  = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
         $registerCustomer["fullName"] = $userInfo->name;
         $registerCustomer["firstName"]  = $userInfo->first_name;
         $registerCustomer["lastName"] = $userInfo->last_name;
         $registerCustomer["customerId"] = "";
         $registerCustomer["isSubscribed"] = 1;
         $registerCustomer["gender"] =  $userInfo->gender ;
         $registerCustomer["isAccepted"]=$userInfo->verified;
         $registerCustomer[ "email"] = $email;
         $registerCustomer[ "password"] = $password;
         $registerCustomer["phone"] = "";
         $registerCustomer["birthDate"] = $userInfo->birthday ;
         $registerCustomer["points"] = "";
         $registerCustomer[ "addresses"] = array();
         $registerCustomer["giftCheques"] = array();
         return$this->register((object)$registerCustomer);
         }
    }
    /**
     * 
     * @param type $tCustomerData
     * @return type
     */
    public function register($tCustomerData)
    {
        $storeId = Mage::getStoreConfig('tappz/general/store');
        $store = Mage::getModel('core/store')->load($storeId);
        $customerData = $this->_prepareCustomerData($tCustomerData);
        try {
            $customer = Mage::getModel('customer/customer');
            $customer->setData($customerData)
                ->setPassword($customerData['password'])
                ->setStore($store)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('invalid_data', $e->getMessage());
        }
        return $this->info($customer->getId());
    }

    /**
     * @param $tCustomerData
     * @return array
     */
    public function update($tCustomerData)
    {
        $genderAttributeCode = Mage::getStoreConfig('tappz/customer/gender');
        $emailAttributeCode = Mage::getStoreConfig('tappz/customer/email');
        $phoneAttributeCode = Mage::getStoreConfig('tappz/customer/phone');
        $birthDateAttributeCode = Mage::getStoreConfig('tappz/customer/birthDate');
        $customerData = $this->_prepareCustomerData($tCustomerData);
        $customer = Mage::getModel('customer/customer')->load($customerData['entity_id']);
             if (!$customer->getId()) {
            $this->_fault('not_exists');
        }
        try {
            $customer->setData('firstname', $customerData['firstname']);
            $customer->setData('lastname', $customerData['lastname']);
            $customer->setData($genderAttributeCode, $customerData[$genderAttributeCode]);
            $customer->setData($emailAttributeCode, $customerData[$emailAttributeCode]);
            $customer->setData($phoneAttributeCode, $customerData[$phoneAttributeCode]);
            $customer->setData($birthDateAttributeCode, $customerData[$birthDateAttributeCode]);

            if (isset($customerData['isSubscribed']))
                $customer->setIsSubscribed($customerData['isSubscribed'] === 'true' ? true : false);

            $customer->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('invalid_data', $e->getMessage());
        }

        return $this->info($customer->getId());
    }

    /**
     * @param $email
     * @return string
     */
    public function lostPassword($email)
    {
        if (!isset($email) || trim($email) === '') {
            $this->_fault("invalid_data", "Please enter a valid email address.");
        }
        $storeId = Mage::getStoreConfig('tappz/general/store');
        $store = Mage::getModel('core/store')->load($storeId);
        $customer = $customer = Mage::getModel('customer/customer')
            ->setStoreId($storeId)
            ->setWebsiteId($store->getWebsiteId())
            ->loadByEmail($email);
        if (!$customer) {
            $this->_fault("invalid_data", "Customer is not found");
        }
        $customer = $customer->sendPasswordReminderEmail();
        if (!$customer) {
            $this->_fault("invalid_data", "Error occured while sending email");
        }
        return "Your password reminder email has been sent.";
    }

    /**
     * @return mixed
     */
    public function getUserAgreement()
    {
        $agreement = Mage::getStoreConfig('tappz/customer/agreement');
        return $agreement;
    }
}