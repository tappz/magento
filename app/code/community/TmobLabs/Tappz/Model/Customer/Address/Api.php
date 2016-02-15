<?php

class TmobLabs_Tappz_Model_Customer_Address_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * @param $addressId
     * @return array
     */
    public function get($addressId)
    {
        $address = Mage::getModel('customer/address')->load($addressId);
        if (!$address) {
            $this->_fault("404.11", "Address is not found.");
        }
        return $this->prepareAddress($address);
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getList($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if (!$customer) {
            $this->_fault("404.10", "Customer is not found.");
        }
        $addresses = array();
        foreach ($customer->getAddresses() as $address) {
            $addresses[] = $this->prepareAddress($address);
        }
        return $addresses;
    }

    /**
     * @param $customerId
     * @param $addressData
     * @return array
     */
    public function create($customerId, $addressData)
    {
        $addressAddressNameAttributeCode = Mage::getStoreConfig('tappz/address/name');
        $addressNameAttributeCode = Mage::getStoreConfig('tappz/address/firstname');
        $addressSurnameAttributeCode = Mage::getStoreConfig('tappz/address/lastname');
        $addressEmailAttributeCode = Mage::getStoreConfig('tappz/address/email');
        $addressStreetAttributeCode = Mage::getStoreConfig('tappz/address/street');
        $addressCountryIdAttributeCode = Mage::getStoreConfig('tappz/address/country_id');
        $addressRegionAttributeCode = Mage::getStoreConfig('tappz/address/region');
        $addressRegionIdAttributeCode = Mage::getStoreConfig('tappz/address/region_id');
        $addressCityAttributeCode = Mage::getStoreConfig('tappz/address/city');
        $addressCityIdAttributeCode = Mage::getStoreConfig('tappz/address/city_id');
        $addressDistrictAttributeCode = Mage::getStoreConfig('tappz/address/district');
        $addressDistrictIdAttributeCode = Mage::getStoreConfig('tappz/address/district_id');
        $addressTownAttributeCode = Mage::getStoreConfig('tappz/address/town');
        $addressTownIdAttributeCode = Mage::getStoreConfig('tappz/address/town_id');
        $addressIsCompanyAttributeCode = Mage::getStoreConfig('tappz/address/isCompany');
        $addressCompanyAttributeCode = Mage::getStoreConfig('tappz/address/company');
        $addressTaxDepartmentAttributeCode = Mage::getStoreConfig('tappz/address/taxDepartment');
        $addressTaxNoAttributeCode = Mage::getStoreConfig('tappz/address/vatNo');
        $addressTelephoneAttributeCode = Mage::getStoreConfig('tappz/address/phone');;
        $addressIdentityNoAttributeCode = Mage::getStoreConfig('tappz/address/idNo');
        $addressPostcodeAttributeCode = Mage::getStoreConfig('tappz/address/postcode');
        $customer = Mage::getModel('customer/customer')
            ->load($customerId);
        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }
        $address = Mage::getModel('customer/address');
        $address->setData($addressAddressNameAttributeCode, $addressData->addressName);
        $address->setData($addressNameAttributeCode, $addressData->name);
        $address->setData($addressSurnameAttributeCode, $addressData->surname);
        $address->setData($addressEmailAttributeCode, $addressData->email);
        $address->setData($addressStreetAttributeCode, $addressData->addressLine);
        $address->setData($addressCountryIdAttributeCode, $addressData->countryCode);
        $address->setData($addressRegionAttributeCode, $addressData->state);
        $address->setData($addressRegionIdAttributeCode, $addressData->stateCode);
        $address->setData($addressCityAttributeCode, $addressData->city);
        $address->setData($addressCityIdAttributeCode, $addressData->cityCode);
        $address->setData($addressDistrictAttributeCode, $addressData->district);
        $address->setData($addressDistrictIdAttributeCode, $addressData->districtCode);
        $address->setData($addressTownAttributeCode, $addressData->town);
        $address->setData($addressTownIdAttributeCode, $addressData->townCode);
        $address->setData($addressIsCompanyAttributeCode, $addressData->corporate);
        $address->setData($addressCompanyAttributeCode, $addressData->companyTitle);
        $address->setData($addressTaxDepartmentAttributeCode, $addressData->taxDepartment);
        $address->setData($addressTaxNoAttributeCode, $addressData->taxNo);
        $address->setData($addressTelephoneAttributeCode, $addressData->phoneNumber);
        $address->setData($addressIdentityNoAttributeCode, $addressData->identityNo);
        $address->setData($addressPostcodeAttributeCode, $addressData->zipCode);
        $address->setCustomer($customer);
        $valid = $address->validate();
        if (is_array($valid)) {
            $this->_fault('invalid_data', implode("\n", $valid));
        }
        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('invalid_data', $e->getMessage());
        }
        return $this->prepareAddress($address);
    }

    /**
     * @param $addressData
     * @return array
     */
    public function update($addressData)
    {
        $addressAddressNameAttributeCode = Mage::getStoreConfig('tappz/address/name');
        $addressNameAttributeCode = Mage::getStoreConfig('tappz/address/firstname');
        $addressSurnameAttributeCode = Mage::getStoreConfig('tappz/address/lastname');
        $addressEmailAttributeCode = Mage::getStoreConfig('tappz/address/email');
        $addressStreetAttributeCode = Mage::getStoreConfig('tappz/address/street');
        $addressCountryIdAttributeCode = Mage::getStoreConfig('tappz/address/country_id');
        $addressRegionAttributeCode = Mage::getStoreConfig('tappz/address/region');
        $addressRegionIdAttributeCode = Mage::getStoreConfig('tappz/address/region_id');
        $addressCityAttributeCode = Mage::getStoreConfig('tappz/address/city');
        $addressCityIdAttributeCode = Mage::getStoreConfig('tappz/address/city_id');
        $addressDistrictAttributeCode = Mage::getStoreConfig('tappz/address/district');
        $addressDistrictIdAttributeCode = Mage::getStoreConfig('tappz/address/district_id');
        $addressTownAttributeCode = Mage::getStoreConfig('tappz/address/town');
        $addressTownIdAttributeCode = Mage::getStoreConfig('tappz/address/town_id');
        $addressIsCompanyAttributeCode = Mage::getStoreConfig('tappz/address/isCompany');
        $addressCompanyAttributeCode = Mage::getStoreConfig('tappz/address/company');
        $addressTaxDepartmentAttributeCode = Mage::getStoreConfig('tappz/address/taxDepartment');
        $addressTaxNoAttributeCode = Mage::getStoreConfig('tappz/address/vatNo');
        $addressTelephoneAttributeCode = Mage::getStoreConfig('tappz/address/phone');;
        $addressIdentityNoAttributeCode = Mage::getStoreConfig('tappz/address/idNo');
        $addressPostcodeAttributeCode = Mage::getStoreConfig('tappz/address/postcode');
        $address = Mage::getModel('customer/address')
            ->load($addressData->id);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }
        $address->setData($addressAddressNameAttributeCode, $addressData->addressName);
        $address->setData($addressNameAttributeCode, $addressData->name);
        $address->setData($addressSurnameAttributeCode, $addressData->surname);
        $address->setData($addressEmailAttributeCode, $addressData->email);
        $address->setData($addressStreetAttributeCode, $addressData->addressLine);
        $address->setData($addressCountryIdAttributeCode, $addressData->countryCode);
        $address->setData($addressRegionAttributeCode, $addressData->state);
        $address->setData($addressRegionIdAttributeCode, $addressData->stateCode);
        $address->setData($addressCityAttributeCode, $addressData->city);
        $address->setData($addressCityIdAttributeCode, $addressData->cityCode);
        $address->setData($addressDistrictAttributeCode, $addressData->district);
        $address->setData($addressDistrictIdAttributeCode, $addressData->districtCode);
        $address->setData($addressTownAttributeCode, $addressData->town);
        $address->setData($addressTownIdAttributeCode, $addressData->townCode);
        $address->setData($addressIsCompanyAttributeCode, $addressData->corporate);
        $address->setData($addressCompanyAttributeCode, $addressData->companyTitle);
        $address->setData($addressTaxDepartmentAttributeCode, $addressData->taxDepartment);
        $address->setData($addressTaxNoAttributeCode, $addressData->taxNo);
        $address->setData($addressTelephoneAttributeCode, $addressData->phoneNumber);
        $address->setData($addressIdentityNoAttributeCode, $addressData->identityNo);
        $address->setData($addressPostcodeAttributeCode, $addressData->zipCode);
        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('invalid_data', $e->getMessage());
        }
        return $this->prepareAddress($address);
    }

    /**
     * @param $address
     * @return mixed
     */
    public function delete($address)
    {
        $address = Mage::getModel('customer/address')
            ->load($address->id);
        if (!$address->getId()) {
            $this->_fault('not_exists');
        }
        try {
            $address->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('invalid_data', $e->getMessage());
        }
        return $address->getId();
    }

    /**
     * @param $address
     * @return array
     */
    protected function prepareAddress($address)
    {
        $addressAddressNameAttributeCode = Mage::getStoreConfig('tappz/address/name');
        $addressNameAttributeCode = Mage::getStoreConfig('tappz/address/firstname');
        $addressSurnameAttributeCode = Mage::getStoreConfig('tappz/address/lastname');
        $addressEmailAttributeCode = Mage::getStoreConfig('tappz/address/email');
        $addressStreetAttributeCode = Mage::getStoreConfig('tappz/address/street');
        $addressCountryIdAttributeCode = Mage::getStoreConfig('tappz/address/country_id');
        $addressRegionAttributeCode = Mage::getStoreConfig('tappz/address/region');
        $addressRegionIdAttributeCode = Mage::getStoreConfig('tappz/address/region_id');
        $addressCityAttributeCode = Mage::getStoreConfig('tappz/address/city');
        $addressCityIdAttributeCode = Mage::getStoreConfig('tappz/address/city_id');
        $addressDistrictAttributeCode = Mage::getStoreConfig('tappz/address/district');
        $addressDistrictIdAttributeCode = Mage::getStoreConfig('tappz/address/district_id');
        $addressTownAttributeCode = Mage::getStoreConfig('tappz/address/town');
        $addressTownIdAttributeCode = Mage::getStoreConfig('tappz/address/town_id');
        $addressIsCompanyAttributeCode = Mage::getStoreConfig('tappz/address/isCompany');
        $addressCompanyAttributeCode = Mage::getStoreConfig('tappz/address/company');
        $addressTaxDepartmentAttributeCode = Mage::getStoreConfig('tappz/address/taxDepartment');
        $addressTaxNoAttributeCode = Mage::getStoreConfig('tappz/address/vatNo');
        $addressTelephoneAttributeCode = Mage::getStoreConfig('tappz/address/phone');;
        $addressIdentityNoAttributeCode = Mage::getStoreConfig('tappz/address/idNo');
        $addressPostcodeAttributeCode = Mage::getStoreConfig('tappz/address/postcode');
        $row = array();
        $row['id'] = '';
        $row['addressName'] = '';
        $row['name'] = '';
        $row['surname'] = '';
        $row['email'] = '';
        $row['addressLine'] = '';
        $row['country'] = '';
        $row['countryCode'] = '';
        $row['state'] = '';
        $row['stateCode'] = '';
        $row['city'] = '';
        $row['cityCode'] = '';
        $row['district'] = '';
        $row['districtCode'] = '';
        $row['town'] = '';
        $row['townCode'] = '';
        $row['corporate'] = '';
        $row['companyTitle'] = '';
        $row['taxDepartment'] = '';
        $row['taxNo'] = '';
        $row['phoneNumber'] = '';
        $row['identityNo'] = '';
        $row['zipCode'] = '';
        $row['id'] = $address->getId();
        $row['addressName'] = $address->getData($addressAddressNameAttributeCode);
        if (!isset($row['addressName'])) {
            $row['addressName'] = $address->getData($addressStreetAttributeCode);
        }
        $row['name'] = $address->getData($addressNameAttributeCode);
        $row['surname'] = $address->getData($addressSurnameAttributeCode);
        $row['email'] = $address->getData($addressEmailAttributeCode);
        $row['addressLine'] = $address->getData($addressStreetAttributeCode);
        $row['country'] = '';
        $row['countryCode'] = '';
        $countryId = $address->getData($addressCountryIdAttributeCode);
        if($countryId){
            $country = Mage::getModel('directory/country')->load($countryId);
            $country->getName(); // Loading name in default locale
            $row['country'] = $country->getName();
            $row['countryCode'] = $country->getId();
        }
        $row['state'] = $address->getData($addressRegionAttributeCode);
        $row['stateCode'] = $address->getData($addressRegionIdAttributeCode);
        $row['city'] = $address->getData($addressCityAttributeCode);
        $row['cityCode'] = $address->getData($addressCityIdAttributeCode);
        $row['district'] = $address->getData($addressDistrictAttributeCode);
        $row['districtCode'] = $address->getData($addressDistrictIdAttributeCode);
        $row['town'] = $address->getData($addressTownAttributeCode);
        $row['townCode'] = $address->getData($addressTownIdAttributeCode);
        $row['corporate'] = $address->getData($addressIsCompanyAttributeCode);
        $row['companyTitle'] = $address->getData($addressCompanyAttributeCode);
        $row['taxDepartment'] = $address->getData($addressTaxDepartmentAttributeCode);
        $row['taxNo'] = $address->getData($addressTaxNoAttributeCode);
        $row['phoneNumber'] = $address->getData($addressTelephoneAttributeCode);
        $row['identityNo'] = $address->getData($addressIdentityNoAttributeCode);
        $row['zipCode'] = $address->getData($addressPostcodeAttributeCode);
        return $row;
    }

    /**
     * @return array
     */
    public function countryList()
    {
        $collection = Mage::getModel('directory/country')->getCollection();
        $result = array();
        foreach ($collection as $country) {
            $country->getName();
            $location = array();
            $location['code'] = $country->getId();
            $location['name'] = $country->getName();
            $result[] = $location;
        }
        return $result;
    }

    /**
     * @param $countryId
     * @return array
     */
    public function stateList($countryId)
    {
        try {
            $country = Mage::getModel('directory/country')->load($countryId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('country_not_exists', $e->getMessage());
        }
        if (!$country->getId()) {
            $this->_fault('country_not_exists');
        }
        $result = array();
        foreach ($country->getRegions() as $region) {
            $region->getName(); // Loading name in default locale
            $location = array();
            $location['code'] = $region->getId();
            $location['name'] = $region->getName();
            $result[] = $location;
        }
        return $result;
    }

    /**
     * @param $stateId
     * @return array
     * todo should be done
     */
    public function cityList($stateId)
    {
        return array();
    }

    /**
     * @param $stateId
     * @return array
     * todo should be done
     */
    public function districtList($stateId)
    {
        return array();
    }

    /**
     * @param $stateId
     * @return array
     * todo should be done
     */
    public function townList($stateId)
    {
        return array();
    }
}