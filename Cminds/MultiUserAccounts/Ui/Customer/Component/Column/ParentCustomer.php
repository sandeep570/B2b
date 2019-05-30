<?php

namespace Cminds\MultiUserAccounts\Ui\Customer\Component\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Cminds MultiUserAccounts parent customercolumn component.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class ParentCustomer extends Column
{
    /**
     * Url builder object.
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Object initialization.
     *
     * @param ContextInterface   $context Context object.
     * @param UiComponentFactory $uiComponentFactory Ui component factory
     *     object.
     * @param UrlInterface       $urlBuilder Url builder object.
     * @param array              $components Components array.
     * @param array              $data Data array.
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource Data source array.
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
                if (!empty($item['parent_customer_id'])) {
                    $item[$this->getData('name')]['parent_customer'] = [
                        'href' => $this->urlBuilder->getUrl(
                            'customer/*/edit',
                            [
                                'id' => $item['parent_customer_id'],
                                'store' => $storeId,
                            ]
                        ),
                        'label' => sprintf(
                            '%s %s',
                            $item['parent_customer_firstname'],
                            $item['parent_customer_lastname']
                        ),
                        'hidden' => false,
                    ];
                }
            }
        }

        return $dataSource;
    }
}
