<?php

class TmobLabs_Tappz_Model_System_Config_Payment
{
    /**
     * get option array for system config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $collection = Mage::helper('payment')->getStoreMethods();

        foreach ($collection as $method) {
            /** @var $method Mage_Payment_Model_Method_Abstract */
            array_push(
                $options,
                array(
                    'value' => $method->getCode(),
                    'label' => $method->getTitle()
                )
            );
        }
        return $options;
    }
}
