<?php

class TmobLabs_Tappz_Model_System_Config_Product_Attribute
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
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');
        $attributes = $product->getAttributes();

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($attributes as $attribute) {
            foreach ($attribute->getEntityType()->getAttributeCodes() as $code) {
                /** @var Mage_Eav_Model_Entity_Attribute $attributeModel */
                $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode($attribute->getEntityType(), $code);
                $options[] = array('value' => $code, 'label' => $attributeModel->getFrontendLabel());
            }
            break;
        }

        return $options;
    }
}
