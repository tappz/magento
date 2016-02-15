<?php

class TmobLabs_Tappz_Model_Catalog_Api extends Mage_Catalog_Model_Api_Resource
{

    /**
     * Index service is used for fetch products and banner images for the main page of your application
     * It does not requires any parameter and can be cached
     * based on application configuration on the wizard
     * @see http://document.t-appz.com/swagger/ui/index#!/Api/Api_GetIndex
     *  @return array
     */
    public function getFrontPage()
    {
        $sampleEx['ads'][0]['name'] = null;
        $sampleEx['ads'][0]['image'] ="";
        $sampleEx['ads'][0]['type'] ="";
        $sampleEx['ads'][0]['value'] = "";
        $frontPageCategory1 = Mage::getStoreConfig('tappz/catalog/catalog1');
        if ($frontPageCategory1) {
            $category = $this->getCategory($frontPageCategory1);
            $productList = $this->getProductList(null, $frontPageCategory1, 0, 6, null, null);
            $group = array();
            $group['partName'] = $category['name'];
            $group['products'] = $productList['products'];
            $sampleEx['groups'][] = $group;
        }
        $frontPageCategory2 = Mage::getStoreConfig('tappz/catalog/catalog2');
        if ($frontPageCategory2) {
            $category = $this->getCategory($frontPageCategory2);
            $productList = $this->getProductList(null, $frontPageCategory2, 0, 6, null, null);
            $group = array();
            $group['partName'] = $category['name'];
            $group['products'] = $productList['products'];
            $sampleEx['groups'][] = $group;
        }
        $frontPageCategory3 = Mage::getStoreConfig('tappz/catalog/catalog3');
        if ($frontPageCategory3) {
            $category = $this->getCategory($frontPageCategory3);
            $productList = $this->getProductList(null, $frontPageCategory3, 0, 6, null, null);
            $group = array();
            $group['partName'] = $category['name'];
            $group['products'] = $productList['products'];
            $sampleEx['groups'][] = $group;
        }
        $frontPageCategory4 = Mage::getStoreConfig('tappz/catalog/catalog4');
        if ($frontPageCategory4) {
            $category = $this->getCategory($frontPageCategory4);
            $productList = $this->getProductList(null, $frontPageCategory4, 0, 6, null, null);
            $group = array();
            $group['partName'] = $category['name'];
            $group['products'] = $productList['products'];
            $sampleEx['groups'][] = $group;
        }
        return $sampleEx;
    }
    /**
     * Categories service is used for fetch categories
     * It does not requires any parameter and can be cached
     * based on application configuration on the wizard
     * @return array
     * @see http://document.t-appz.com/swagger/ui/index#!/Api/Api_GetCategories
     */
    public function getCategories()
    {
        $storeId = (int)Mage::getStoreConfig('tappz/general/store');
        if ($storeId <= 0) {
            $storeId = 1;
        }
 
        /** @var Mage_Core_Model_Store $store */
        $store = Mage::getModel('core/store')->load($storeId);

        $rootCategoryId = $store->getRootCategoryId();
        if (!$rootCategoryId) {
            $rootCategoryId = 2;
        }
        $rootCategory = $this->getCategory($rootCategoryId);
        return $rootCategory['children'];
    }

    /**
     * Category service gets a category with category id
     * @param $categoryId
     * @return array
     * @see http://document.t-appz.com/swagger/ui/index#!/Api/Api_GetCategory
     */

    public function getCategory($categoryId)
    {
        $storeId = Mage::getStoreConfig('tappz/general/store');
        $tree = Mage::getResourceSingleton('catalog/category_tree')
            ->load();
        $root = $tree->getNodeById($categoryId);
        if ($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($this->_getStoreId($storeId))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->addIsActiveFilter();
        $tree->addCollectionData($collection, true);
        return $this->categoryToModel($root);
    }

    /**
     * Filled children model
     * @param $temp
     * @param bool $getChildren
     * @return array
     */
    private function categoryToModel($temp, $getChildren = true)
    {
        $category = array();
        $category['id'] = $temp->getId();
        $category['name'] = $temp->getName();
        $category['isRoot'] = $temp->getParentId() == $this->rootCategoryId;
        $category['isLeaf'] = $temp->getChildrenCount() == 0;
        $category['parentCategoryId'] = $temp->getParentId();
        $category['children'] = array();

        if ($getChildren) {
            foreach ($temp->getChildren() as $child) {
                $category['children'][] = $this->categoryToModel($child, false);
            }
        }

        return $category;
    }

    /**
     * Searches products by phrase and category.
     * This method is user for both search and product listing under a category.
     * It is also called with a search and a category id when a category filter selected.
     * @param $phrase
     * @param $categoryId
     * @param $pageNumber
     * @param $pageSize
     * @param $filterQuery
     * @param $sort
     * @return array
     * @see http://document.t-appz.com/swagger/ui/index#!/Api/Api_Search
     */
    public function getProductList($phrase, $categoryId, $pageNumber, $pageSize, $filterQuery, $sort)
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', '1')
            ->addAttributeToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);


        if (!empty($filter)) {
            foreach ($filter as $f) {
                if (isset($f->selected)) {
                    $collection->addAttributeToSelect($f->id)
                        ->addAttributeToFilter($f->id, $f->selected->id);
                }
            }
        }

        if (!empty($phrase))
            $collection->addAttributeToFilter('name', array('like' => "%$phrase%"));

        if (!empty($categoryId)) {
            $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left');
            $collection->addAttributeToFilter('category_id', array('eq' => $categoryId));
        }
        
        $total = $collection->getSize();
        if (!empty($sort)) {
            $sortArr = explode("-", $sort);
            $collection->addAttributeToSort($sortArr[0], $sortArr[1]);
        }
         $pageSize = 5;
        empty($pageNumber) ?    $pageNumber = 1 :  $pageNumber +=1;

        if (empty($pageSize))
            $pageSize = 6;

        $collection->setPage($pageNumber, $pageSize);

        $result = array();
        $result['total'] = null;
        $result['filters'] = array();
        $result['sortList'] = array();
        $result['products'] = array();
        $result['total'] = $total;
        $result['sortList'][] = array('id' => 'name-asc', 'name' => 'Name (Ascending)');
        $result['sortList'][] = array('id' => 'name-desc', 'name' => 'Name (Descending)');
        $result['sortList'][] = array('id' => 'price-asc', 'name' => 'Price (Ascending)');
        $result['sortList'][] = array('id' => 'price-desc', 'name' => 'Price (Descending)');
        if (!empty($collection)) {
            foreach ($collection as $_product) {
                $result['products'][] = $this->getProduct($_product->getId());
            }
        }
        return $result;
    }

    /**
     * Product service get a product with product id
     * @param $productId
     * @return array
     * @see http://document.t-appz.com/swagger/ui/index#!/Api/Api_ProductById
     */
    public function getProduct($productId)
    {
        $storeId = (int)Mage::getStoreConfig('tappz/general/store');
        if ($storeId <= 0) {
            $storeId = 1;
        }
       $store = Mage::getModel('core/store')->load($storeId);
        $product = Mage::getModel('catalog/product')->load($productId);
        $productInfo = array();
        $productInfo['id'] = null;
        $productInfo['productName'] = null;
        $productInfo['listPrice'] = array();
        $productInfo['listPrice']['amount'] = null;
        $productInfo['listPrice']['amountDefaultCurrency'] = null;
        $productInfo['listPrice']['currency'] = null;
        $productInfo['strikeoutPrice'] = array();
        $productInfo['strikeoutPrice']['amount'] = null;
        $productInfo['strikeoutPrice']['amountDefaultCurrency'] = null;
        $productInfo['strikeoutPrice']['currency'] = null;
        $productInfo['picture'] = null;
        $productInfo['pictures'] = array();
        $productInfo['productDetailUrl'] = null;
        $productInfo['additionalDetail'] = null;
        $productInfo['inStock'] = true;
        $productInfo['isShipmentFree'] = false;
        $productInfo['isCampaign'] = false;
        $productInfo['headline'] = null;
        $productInfo['productUrl'] = null;
        $productInfo['variants'] = array();
        $productInfo['shipmentInformation'] = null;
        $productInfo['actions'] = array();
        $productInfo['id'] = $product->getId();
        $productInfo['productName'] = $product->getName();
        $specialPrice = sprintf("%0.2f", $product->getData('special_price'));
        $listPrice = sprintf("%0.2f", $product->getPrice());
        $productInfo['listPrice'] = array();
        $productInfo['listPrice']['amount'] = $specialPrice > 0 ? $specialPrice : $listPrice;
        $productInfo['listPrice']['amountDefaultCurrency'] = null;
        $productInfo['listPrice']['currency'] = $store->getCurrentCurrencyCode();
        $productInfo['strikeoutPrice'] = array();
        $productInfo['strikeoutPrice']['amount'] = $specialPrice > 0 ? $listPrice : 0;
        $productInfo['strikeoutPrice']['amountDefaultCurrency'] = '';
        $productInfo['strikeoutPrice']['currency'] = $store->getCurrentCurrencyCode();
        if ($specialPrice > 0) {
            $actionDiscount = array();
            $actionDiscount['id'] = 'discount';
            $actionDiscount['name'] = sprintf("%d%%", (($listPrice - $specialPrice) / $listPrice) * 100);
            $productInfo['actions'][] = $actionDiscount;
        }
        $productInfo['picture'] = $product->getImageUrl();
        $mediaGallery = $product->getMediaGalleryImages();
        if (count($mediaGallery) > 0) {
            foreach ($mediaGallery as $mediaGal) {
                $picture['url'] = $mediaGal->getUrl();
                $productInfo['pictures'][] = $picture;
            }
        } else {
            $productInfo['pictures'][0]['url'] = $productInfo['picture'];
        }
        $productInfo['productDetailUrl'] = '<p>' . $product->getDescription() . '</p>';
        $isInStock = false;
        $stockStatus = Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($product)
            ->getIsInStock();
        if ($stockStatus == 1)
            $isInStock = true;
        $productInfo['inStock'] = $isInStock;
        $productAttributeCodeIsShippingFree = Mage::getStoreConfig('tappz/catalog/productAttributeCodeShippingInfo');
        $productInfo['isShipmentFree'] = $product->getData($productAttributeCodeIsShippingFree);
        $productInfo['productUrl'] = $product->getProductUrl();
        if ($productInfo['isShipmentFree']) {
            $action = array();
            $action['id'] = 'popper';
            $action['name'] = 'Free Shipping';
            $productInfo['actions'][] = $action;
        }
        $productType = $product->getTypeId();;
        if ($productType == 'configurable') {
            $instanceConf = $product->getTypeInstance();
            $configurableAttributesData = $instanceConf->getConfigurableAttributesAsArray();
            foreach ($configurableAttributesData as $dt => $val) {
                $group = array();
                $group['id'] = null;
                $group['name'] = null;
                $group['selected'] = null;
                $group['values'] = array();
                $group['id'] = $val['attribute_code'];
                $group['name'] = $val['label'];
                if ($val['attribute_code'] == 'beden')
                    $productInfo['additionalDetail'] = "<a href=\"http://annelutfen.com/skin/frontend/purple/purple/images/olcu_tablosu_gri.png\">Ölçü tablosu</a>";
                foreach ($val['values'] as $vv) {
                    $groupValue = array();
                    $groupValue['id'] = null;
                    $groupValue['name'] = null;
                    $groupValue['id'] = $vv['label'];
                    $groupValue['name'] = $vv['value_index'];
                    $group['values'][] = $groupValue;
                }
                $productInfo['variants'][] = $group;
            }
        }
        $productAttributeCodeShippingInfo = Mage::getStoreConfig('tappz/catalog/productAttributeCodeShippingInfo');
        $productInfo['shipmentInformation'] = $product->getData($productAttributeCodeShippingInfo);
        return $productInfo;
    }
    /**
     * Product related  service get  related products  with product id
     * @param $productId
     * @return array
     * @see http://document.t-appz.com/swagger/ui/index#!/Api/Api_RelatedProducts
     */
    public function getRelatedProducts($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $link = $product->getLinkInstance()
            ->setLinkTypeId(Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED);
        $collection = $link
            ->getProductCollection()
            ->setIsStrongMode()
            ->setProduct($product);
        $arData = array();
        if (!empty($collection)) {
            foreach ($collection as $_product):
                array_push($arData, $this->getProduct($_product->getId()));
            endforeach;
        }
        return $arData;
    }
    /**
     * Get child product id by parent Product Id
     * @param $parentProductId
     * @param $attributeList
     * @return int
     */
    public function getChildProductId($parentProductId, $attributeList)
    {
        $subProductIds = Mage::getModel('catalog/product_type_configurable')
            ->getChildrenIds($parentProductId);
        $subProducts = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('entity_id', $subProductIds);
        foreach ($attributeList as $attribute) {
            $attributeCode = $attribute->id;
            $attributeValueIndex = $attribute->values[0]->id;
            $subProducts->addAttributeToFilter($attributeCode, $attributeValueIndex);
        }
        $product = null;
        if ($subProducts->getSize() > 0) {
            $product = $subProducts->getFirstItem();
        }
        return $product == null ? 0 : $product->getId();
    }
}