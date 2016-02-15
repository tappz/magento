<?php

class TmobLabs_Tappz_Model_System_Config_Category
{
    protected $categoryList;

    /**
     * get option array for system config
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->categoryList = array();

        $storeId = (int) Mage::getStoreConfig('tappz/general/store');
        if($storeId <= 0){
            $storeId = 1;
        }

        /** @var Mage_Core_Model_Store $store */
        $store = Mage::getModel('core/store')->load($storeId);

        $list = Mage::getModel('catalog/category')
                    ->load($store->getRootCategoryId())
                    ->getChildrenCategories();

        foreach ($list as $cat) {
            /** @var Mage_Catalog_Model_Category $category */
            $category = Mage::getModel('catalog/category')->load($cat->getId());
            $this->_getChildrenText($category, '');
        }

        return $this->categoryList;
    }

    /**
     * @param $category Mage_Catalog_Model_Category
     * @param $text string
     * @return array $array
     */
    protected function _getChildrenText($category, $text)
    {
        $text = $text . '/' . $category->getName();
        $text = trim($text, '/');
        $this->categoryList[] = array('value' => $category->getId(), 'label' => $text);
        foreach ($category->getChildrenCategories() as $cat) {
           $this->_getChildrenText($cat, $text);
        }
    }
}
