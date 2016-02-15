<?php

class TmobLabs_Tappz_Model_System_Config_Address
{

    /**
     * get option array for system config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value' => ' ', 'label' => ' ');

        $attributes = Mage::getModel('customer/address')->getAttributes();
        foreach ($attributes as $attribute) {
            if($attribute->getIsVisible()) {
                $options[] = array('value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel());
            }
        }

        return $options;
    }
}
