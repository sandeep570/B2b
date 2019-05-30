<?php

namespace Cminds\MultiUserAccounts\Model\Data;

use Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class ApiSubAccount extends AbstractSimpleObject implements ApiSubAccountInterface
{
    /**
     * @return $this
     */
    public function setParentEmail($email)
    {
        $this->setData(self::PARENT_EMAIL, $email);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentEmail()
    {
        return $this->_get(self::PARENT_EMAIL);
    }

    /**
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->setData(self::FIRSTNAME, $firstname);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->_get(self::FIRSTNAME);
    }

    /**
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->setData(self::LASTNAME, $lastname);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->_get(self::LASTNAME);
    }

    /**
     * @return $this
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL, $email);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * @return $this
     */
    public function setPassword($password)
    {
        $this->setData(self::PASSWORD, $password);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->_get(self::PASSWORD);
    }

    /**
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->setData(self::GROUP_ID, $groupId);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->setData(self::WEBSITE_ID, $websiteId);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebsiteId()
    {
        return $this->_get(self::WEBSITE_ID);
    }

    /**
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->setData(self::PREFIX, $prefix);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->_get(self::PREFIX);
    }

    /**
     * @return $this
     */
    public function setMiddlename($middlename)
    {
        $this->setData(self::MIDDLENAME, $middlename);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMiddlename()
    {
        return $this->_get(self::MIDDLENAME);
    }

    /**
     * @return $this
     */
    public function setSuffix($suffix)
    {
        $this->setData(self::SUFFIX, $suffix);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuffix()
    {
        return $this->_get(self::SUFFIX);
    }

    /**
     * @return $this
     */
    public function setDob($dob)
    {
        $this->setData(self::DOB, $dob);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDob()
    {
        return $this->_get(self::DOB);
    }

    /**
     * @return $this
     */
    public function setTaxvat($taxvat)
    {
        $this->setData(self::TAXVAT, $taxvat);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaxvat()
    {
        return $this->_get(self::TAXVAT);
    }

    /**
     * @return $this
     */
    public function setGender($gender)
    {
        $this->setData(self::GENDER, $gender);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->_get(self::GENDER);
    }

    /**
     * @return $this
     */
    public function setIsActive($is_active)
    {
        $this->setData(self::IS_ACTIVE, $is_active);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * @return $this
     */
    public function setSupplierApprove($supplier_approve)
    {
        $this->setData(self::SUPPLIER_APPROVE, $supplier_approve);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSupplierApprove()
    {
        return $this->_get(self::SUPPLIER_APPROVE);
    }

    /**
     * @return $this
     */
    public function setCompany($company)
    {
        $this->setData(self::COMPANY, $company);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->_get(self::COMPANY);
    }

    /**
     * @return $this
     */
    public function setCity($city)
    {
        $this->setData(self::CITY, $city);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->_get(self::CITY);
    }

    /**
     * @return $this
     */
    public function setCountryId($country_id)
    {
        $this->setData(self::COUNTRY_ID, $country_id);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountryId()
    {
        return $this->_get(self::COUNTRY_ID);
    }

    /**
     * @return $this
     */
    public function setRegion($region)
    {
        $this->setData(self::REGION, $region);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->_get(self::REGION);
    }

    /**
     * @return $this
     */
    public function setPostcode($postcode)
    {
        $this->setData(self::POSTCODE, $postcode);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostcode()
    {
        return $this->_get(self::POSTCODE);
    }

    /**
     * @return $this
     */
    public function setTelephone($telephone)
    {
        $this->setData(self::TELEPHONE, $telephone);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->_get(self::TELEPHONE);
    }

    /**
     * @return $this
     */
    public function setFax($fax)
    {
        $this->setData(self::FAX, $fax);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->_get(self::FAX);
    }

    /**
     * @return $this
     */
    public function setVatId($vatId)
    {
        $this->setData(self::VAT_ID, $vatId);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVatId()
    {
        return $this->_get(self::VAT_ID);
    }

    /**
     * @return $this
     */
    public function setStreet1($street1)
    {
        $this->setData(self::STREET_1, $street1);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStreet1()
    {
        return $this->_get(self::STREET_1);
    }

    /**
     * @return $this
     */
    public function setStreet2($street2)
    {
        $this->setData(self::STREET_2, $street2);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStreet2()
    {
        return $this->_get(self::STREET_2);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountDataModificationPermission()
    {
        return $this->_get(self::ACCOUNT_DATA_MODIFY_PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountDataModificationPermission($permission)
    {
        $this->setData(self::ACCOUNT_DATA_MODIFY_PERMISSION, $permission);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountOrderHistoryViewPermission()
    {
        return $this->_get(self::ACCOUNT_ORDER_HISTORY_VIEW_PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountOrderHistoryViewPermission($permission)
    {
        $this->setData(
            self::ACCOUNT_ORDER_HISTORY_VIEW_PERMISSION,
            $permission
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutOrderCreatePermission()
    {
        return $this->_get(self::CHECKOUT_ORDER_CREATE_PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutOrderCreatePermission($permission)
    {
        $this->setData(self::CHECKOUT_ORDER_CREATE_PERMISSION, $permission);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutOrderApprovalPermission()
    {
        return $this->_get(self::CHECKOUT_ORDER_APPROVAL_PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutOrderApprovalPermission($permission)
    {
        $this->setData(self::CHECKOUT_ORDER_APPROVAL_PERMISSION, $permission);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutCartViewPermission()
    {
        return $this->_get(self::CHECKOUT_CART_VIEW_PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutCartViewPermission($permission)
    {
        $this->setData(self::CHECKOUT_CART_VIEW_PERMISSION, $permission);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutViewPermission()
    {
        return $this->_get(self::CHECKOUT_VIEW_PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutViewPermission($permission)
    {
        $this->setData(self::CHECKOUT_VIEW_PERMISSION, $permission);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckoutOrderPlacedNotificationPermission()
    {
        return $this->_get(
            self::CHECKOUT_ORDER_PLACED_NOTIFICATION_PERMISSION
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutOrderPlacedNotificationPermission($permission)
    {
        $this->setData(
            self::CHECKOUT_ORDER_PLACED_NOTIFICATION_PERMISSION,
            $permission
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getForceUsageParentCompanyNamePermission()
    {
        return $this->_get(
            self::FORCE_USAGE_PARENT_COMPANY_NAME_PERMISSION
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setForceUsageParentCompanyNamePermission($permission)
    {
        $this->setData(
            self::FORCE_USAGE_PARENT_COMPANY_NAME_PERMISSION,
            $permission
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getForceUsageParentVatPermission()
    {
        return $this->_get(
            self::FORCE_USAGE_PARENT_VAT_PERMISSION
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setForceUsageParentVatPermission($permission)
    {
        $this->setData(
            self::FORCE_USAGE_PARENT_VAT_PERMISSION,
            $permission
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getForceUsageParentAddressesPermission()
    {
        return $this->_get(
            self::FORCE_USAGE_PARENT_ADDRESSES_PERMISSION
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setForceUsageParentAddressesPermission($permission)
    {
        $this->setData(
            self::FORCE_USAGE_PARENT_ADDRESSES_PERMISSION,
            $permission
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission()
    {
        return $this->_get(self::PERMISSION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($permission)
    {
        $this->setData(self::PERMISSION, $permission);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPromote($promote)
    {
        $this->setData("promote", $promote);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromote()
    {
        $this->_get("promote");
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function setDowngrade($parent)
    {
        $this->setData("downgrade", $parent);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDowngrade()
    {
        $this->_get("downgrade");
        return $this;
    }

}