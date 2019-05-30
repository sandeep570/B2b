<?php

namespace Cminds\MultiUserAccounts\Block\Plugin\Sales\Order\History;

use Cminds\MultiUserAccounts\Block\Plugin\Sales\Order\Plugin as OrderPlugin;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * Cminds MultiUserAccounts sales order history block plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin extends OrderPlugin
{
    /**
     * Around getOrders plugin.
     *
     * @param BlockInterface $subject Subject object.
     * @param \Closure       $proceed Closure.
     *
     * @return OrderCollection|bool
     */
    public function aroundGetOrders(
        BlockInterface $subject,
        \Closure $proceed
    ) {
        if ($this->moduleConfig->isEnabled() === false) {
            return $proceed();
        }

        if ($this->orders === null) {
            $this->orders = $this
                ->getOrders();
        }

        return $this->orders;
    }
}
