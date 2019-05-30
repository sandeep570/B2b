<?php

namespace Cminds\MultiUserAccounts\Block\Permission;

use Magento\Framework\View\Element\Template;

/**
 * Cminds MultiUserAccounts permission redirect block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Redirect extends Template
{
    /**
     * Return redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        $uenc = $this->getRequest()->getParam('uenc');
        if ($uenc !== null) {
            $uenc = base64_decode($uenc);
        } else {
            $uenc = $this->_urlBuilder->getUrl();
        }

        return $uenc;
    }
}
