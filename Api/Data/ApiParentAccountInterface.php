<?php

namespace Cminds\MultiUserAccounts\Api\Data;

interface ApiParentAccountInterface
{
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const GROUP_ID = 'group_id';
    const WEBSITE_ID = 'website_id';
    const PREFIX = 'prefix';
    const MIDDLENAME = 'middlename';
    const SUFFIX = 'suffix';
    const DOB = 'dob';
    const TAXVAT = 'taxvat';
    const GENDER = 'gender';
    const IS_ACTIVE = 'is_active';
    const CAN_MANAGE_SUBACCOUNTS = 'can_manage_subaccounts';
    const SUPPLIER_APPROVE = 'supplier_approve';
    const COMPANY = 'company';
    const CITY = 'city';
    const COUNTRY_ID = 'country_id';
    const REGION = 'region';
    const POSTCODE = 'postcode';
    const TELEPHONE = 'telephone';
    const FAX = 'fax';
    const VAT_ID = 'vat_id';
    const STREET_1 = 'street_1';
    const STREET_2 = 'street_2';
    const ID = 'id';
//    const SUBACCOUNTS = 'subaccounts';

    /**
     * @return $this
     */
    public function setFirstname($firstname);

    /**
     * @return mixed
     */
    public function getFirstname();

    /**
     * @return $this
     */
    public function setLastname($lastname);

    /**
     * @return mixed
     */
    public function getLastname();

    /**
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return mixed
     */
    public function getEmail();

    /**
     * @return $this
     */
    public function setPassword($password);

    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @return $this
     */
    public function setGroupId($groupId);

    /**
     * @return mixed
     */
    public function getGroupId();

    /**
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return mixed
     */
    public function getWebsiteId();

    /**
     * @return $this
     */
    public function setPrefix($prefix);

    /**
     * @return mixed
     */
    public function getPrefix();

    /**
     * @return $this
     */
    public function setMiddlename($middlename);

    /**
     * @return mixed
     */
    public function getMiddlename();

    /**
     * @return $this
     */
    public function setSuffix($suffix);

    /**
     * @return mixed
     */
    public function getSuffix();

    /**
     * @return $this
     */
    public function setDob($dob);

    /**
     * @return mixed
     */
    public function getDob();

    /**
     * @return $this
     */
    public function setTaxvat($taxvat);

    /**
     * @return mixed
     */
    public function getTaxvat();

    /**
     * @return $this
     */
    public function setGender($gender);

    /**
     * @return int
     */
    public function getGender();

    /**
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @return $this
     */
    public function setCanManageSubaccounts($canManageSubaccounts);

    /**
     * @return int
     */
    public function getCanManageSubaccounts();


    /**
     * @return $this
     */
    public function setSupplierApprove($supplierApprove);

    /**
     * @return int
     */
    public function getSupplierApprove();

    /**
     * @return $this
     */
    public function setCompany($company);

    /**
     * @return mixed
     */
    public function getCompany();

    /**
     * @return $this
     */
    public function setCity($city);

    /**
     * @return mixed
     */
    public function getCity();

    /**
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * @return mixed
     */
    public function getCountryId();

    /**
     * @return $this
     */
    public function setRegion($region);

    /**
     * @return mixed
     */
    public function getRegion();

    /**
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * @return mixed
     */
    public function getPostcode();

    /**
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * @return mixed
     */
    public function getTelephone();

    /**
     * @return $this
     */
    public function setFax($fax);

    /**
     * @return mixed
     */
    public function getFax();

    /**
     * @return $this
     */
    public function setVatId($vatId);

    /**
     * @return mixed
     */
    public function getVatId();

    /**
     * @return $this
     */
    public function setStreet1($street1);

    /**
     * @return mixed
     */
    public function getStreet1();

    /**
     * @return $this
     */
    public function setStreet2($street2);

    /**
     * @return mixed
     */
    public function getStreet2();

    /**
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface $subaccount
     */
//    public function getSubaccounts();

    /**
     * Set an extension attributes object.
     *
     * @param \Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface $subaccount
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\ApiParentAccountInterface
     */
//    public function setSubaccounts(\Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface $subaccount);
}
