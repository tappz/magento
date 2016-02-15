<?php

class TmobLabs_Tappz_Model_System_Config_Store
{
    /**
     * get option array for system config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        $collection = Mage::getModel('core/store_group')->getCollection();

        foreach ($collection as $group) {
            /** @var Mage_Core_Model_Store_Group $group */
            foreach ($group->getStores() as $store) {
                /** @var Mage_Core_Model_Store $store */
                array_push(
                    $options,
                    array(
                        'value' => $store->getId(),
                        'label' => $group->getName() . " : " . $store->getName()
                    )
                );
            }
        }

        return $options;
    }
}
