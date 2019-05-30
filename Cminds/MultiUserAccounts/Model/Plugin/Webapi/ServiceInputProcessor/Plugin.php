<?php

namespace Cminds\MultiUserAccounts\Model\Plugin\Webapi\ServiceInputProcessor;

use Magento\Framework\Reflection\MethodsMap;

/**
 * Cminds MultiUserAccounts WebApi input processor plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Mateusz Niziołek
 */
class Plugin
{
    /** @var  MethodsMap */
    protected $methodsMap;

    /**
     * Plugin constructor.
     * @param MethodsMap $methodsMap
     */
    public function __construct(
        MethodsMap $methodsMap
    )
    {
        $this->methodsMap = $methodsMap;
    }

    /**
     * Return array of Data accepted by API
     *
     * @return array
     */
    private function getApiAttributeNamesArray()
    {
        $array = [
            "firstname", "lastname", "email", "id", "website_id", "group_id", "prefix","middlename","suffix","dob",
            "taxvat","gender","is_active","supplier_approve","company","city","country_id","region","postcode",
            "telephone","fax","vat_id","street_1","street_2","subaccounts","account_data_modification_permission",
            "account_order_history_view_permission","checkout_order_create_permission","checkout_order_approval_permission",
            "checkout_cart_view_permission","checkout_view_permission","checkout_order_placed_notification_permission",
            "force_usage_parent_company_name_permission","force_usage_parent_vat_permission",
            "force_usage_parent_addresses_permission","password","parent_email","promote","can_manage_subaccounts"
        ];
        return $array;
    }

    /**
     * @param \Magento\Framework\Webapi\ServiceInputProcessor $subject
     * @param $serviceClassName
     * @param $serviceMethodName
     * @param array $inputArray
     * @return array
     */
    public function beforeProcess(
        \Magento\Framework\Webapi\ServiceInputProcessor $subject,
        $serviceClassName,
        $serviceMethodName,
        array $inputArray
    )
    {

        if ($serviceClassName === 'Cminds\MultiUserAccounts\Api\ParentaccountInterface') {
            if ($serviceMethodName === 'create') {
                $inputArray = $this->addCustomerContainer($inputArray);
            }
            if ($serviceMethodName === 'updateById') {
                $inputArray = $this->addCustomerContainer($inputArray);
            }
        }
        if ($serviceClassName === 'Cminds\MultiUserAccounts\Api\SubaccountInterface') {
            if ($serviceMethodName === 'create') {
                $inputArray = $this->addCustomerContainer($inputArray);
            }
            if ($serviceMethodName === 'updateById') {
                $inputArray = $this->addCustomerContainer($inputArray);
            }
        }
        return array($serviceClassName, $serviceMethodName, $inputArray);
    }

    /**
     * Return inputArray data closed in the customer array container
     * @param $inputArray. Source data from API endpoint
     * @return mixed[] with container
     */
    private function addCustomerContainer($inputArray)
    {
        $apiAttributes = $this->getApiAttributeNamesArray();
        $result = [];
        foreach ($inputArray as $name => $item) {
            if (in_array($name, $apiAttributes)) {
                $result['customer'][$name] = $item;
            } else {
                $result[$name] = $item;
            }
        }
        $inputArray = [];
        $inputArray = $result;
        return $inputArray;
    }

}