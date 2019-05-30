<?php

namespace Cminds\MultiUserAccounts\Block\Manage\Form;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Model\Permission;
use Magento\Customer\Model\Session\Proxy as Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Helper\Address;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Cminds\MultiUserAccounts\Helper\Manage as ManageHelper;

/**
 * Cminds MultiUserAccounts manage form edit block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Edit extends Template
{
    /**
     * Session object.
     *
     * @var Session
     */
    private $session;

    /**
     * Customer address object.
     *
     * @var Address
     */
    private $customerAddressHelper;

    /**
     * Permission object.
     *
     * @var Permission
     */
    private $permission;

    /**
     * Subaccount object.
     *
     * @var SubaccountInterface
     */
    private $subaccount;


    /**
     * Customer Repository object.
     *
     * @var
     */
    private $customerRepository;

    /**
     * @var ManageHelper
     */
    private $manageHelper;

    /**
     * Object initialization.
     *
     * @param Context    $context         Context object.
     * @param Session    $session         Session object.
     * @param Permission $permission      Permission object.
     * @param Address    $address         Customer address object.
     * @param array      $data            Array data.
     */
    public function __construct(
        Context $context,
        Session $session,
        Permission $permission,
        Address $address,
        CustomerRepositoryInterface $customerRepository,
        ManageHelper $manageHelper,

        array $data = []
    ) {
        $this->session = $session;
        $this->permission = $permission;
        $this->customerAddressHelper = $address;
        $this->customerRepository = $customerRepository;
        $this->manageHelper = $manageHelper;

        parent::__construct($context, $data);
    }

    /**
     * Preparing global layout.
     *
     * @return Edit
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $subaccountId = $this->getRequest()->getParam('id');

        if ($subaccountId) {
            $this->pageConfig->getTitle()->set(__('Edit Subaccount'));
        } else {
            $this->pageConfig->getTitle()->set(__('Add Subaccount'));
        }

        return $this;
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecured.
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl(
            'subaccounts/manage/editPost',
            ['_secure' => true]
        );
    }

    /**
     * Retrieve back url.
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('subaccounts/manage/index');
    }

    /**
     * Return the Subaccount data stored in session.
     *
     * @return SubaccountInterface
     */
    public function getSubaccount()
    {
        if ($this->subaccount === null) {
            $this->subaccount = $this->session->getSubaccountFormData(true);
        }

        return $this->subaccount;
    }

    /**
     * Get Customer Entity by id.
     *
     * @param $id
     * @return mixed
     */
    private function getCustomerById($id)
    {
        return $this->customerRepository->getById($id);
    }

    /**
     * Retrive the is active custom customer attribute.
     *
     * @param $customerId
     * @return int
     */
    public function getIsActive($customerId)
    {
        $customer = $this->getCustomerById($customerId);
        $customerActive = $customer->getCustomAttribute('customer_is_active');
        if (isset($customerActive)) {
            $is_active = $customerActive->getValue();
        } else {
            $is_active = 0;
        }

        return $is_active;
    }

    /**
     * Get Customer attr that controlls if subs can be edited.
     *
     * @return boolean
     */
    public function getCanManageSubaccounts()
    {
        return $this->manageHelper->getCanManageSubaccounts();
    }

    /**
     * Return all available permissions.
     *
     * @return array
     */
    public function getPermissions()
    {
        // TODO: Refactoring probably required.
        // For example change permission model to helper
        // to avoid methods aliasing.
        return $this->permission->getPermissions();
    }

    /**
     * Return permission html id by permission code.
     *
     * @param string $permissionCode Permission code.
     *
     * @return string
     */
    public function getPermissionHtmlId($permissionCode)
    {
        // TODO: Refactoring probably required.
        // For example change permission model
        // to helper to avoid methods aliasing.
        return $this->permission->getPermissionId($permissionCode);
    }

    /**
     * Return permission getter by permission code.
     *
     * @param string $permissionCode Permission code.
     *
     * @return string
     */
    public function getPermissionGetter($permissionCode)
    {
        // TODO: Refactoring probably required.
        // For example change permission model
        // to helper to avoid methods aliasing.
        return $this->permission->getPermissionGetter($permissionCode);
    }

    /**
     * Return config value for tax/vat show.
     *
     * @return null|string
     */
    public function showTaxVatConfiguration()
    {
        return $this->customerAddressHelper->getConfig('taxvat_show');
    }

    /**
     * Return logged customer data.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getLoggedCustomerData()
    {
         return $this->session->getCustomerData();
    }
}
