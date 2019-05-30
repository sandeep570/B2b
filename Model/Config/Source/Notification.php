<?php

namespace Cminds\MultiUserAccounts\Model\Config\Source;

use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Framework\Option\ArrayInterface;

/**
 * Cminds MultiUserAccounts notification source config model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Notification implements ArrayInterface
{
    /**
     * Options getter.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => ModuleConfig::NOTIFICATION_MAIN_ACCOUNT,
                'label' => __('Main Account'),
            ],
            [
                'value' => ModuleConfig::NOTIFICATION_SUBACCOUNT,
                'label' => __('Subaccounts'),
            ],
            [
                'value' => ModuleConfig::NOTIFICATION_BOTH,
                'label' => __('Both'),
            ],
        ];
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        $optionsArray = $this->toOptionArray();

        foreach ($optionsArray as $optionData) {
            $array[$optionData['value']] = $optionData['label'];
        }

        return $array;
    }
}
