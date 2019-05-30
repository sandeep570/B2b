<?php

namespace Cminds\MultiUserAccounts\Model;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Api\Data\SubaccountInterfaceFactory;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount as SubaccountResourceModel;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount\Collection;
use Magento\Customer\Model\Config\Share;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Cminds MultiUserAccounts subaccount model.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 *
 * @method int getId()
 * @method Subaccount setId($id)
 * @method int getCustomerId()
 * @method Subaccount setCustomerId($id)
 * @method int getParentCustomerId()
 * @method Subaccount setParentCustomerId($id)
 * @method int getPermission();
 * @method Subaccount setPermission($permission)
 * @method int getIsActive();
 * @method Subaccount setIsActive($isActive)
 * @method string getCreatedAt()
 * @method Subaccount setCreatedAt($date)
 * @method string getUpdatedAt()
 * @method Subaccount setUpdatedAt($date)
 * @method string getAdditionalInformation()
 * @method Subaccount setAdditionalInformation($additionalInformation)
 */
class Subaccount extends AbstractModel
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'subaccount';

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SubaccountInterfaceFactory
     */
    private $subaccountDataFactory;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Share
     */
    private $configShare;

    /**
     * Object initialization.
     *
     * @param Context                    $context
     * @param Registry                   $registry
     * @param SubaccountResourceModel    $resource
     * @param Collection                 $resourceCollection
     * @param DataObjectProcessor        $dataObjectProcessor
     * @param DataObjectHelper           $dataObjectHelper
     * @param DateTime                   $dateTime
     * @param SubaccountInterfaceFactory $subaccountDataFactory
     * @param StoreManagerInterface      $storeManager
     * @param Share                      $configShare
     * @param array                      $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SubaccountResourceModel $resource,
        Collection $resourceCollection,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DateTime $dateTime,
        SubaccountInterfaceFactory $subaccountDataFactory,
        StoreManagerInterface $storeManager,
        Share $configShare,
        array $data = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->date = $dateTime;
        $this->subaccountDataFactory = $subaccountDataFactory;
        $this->storeManager = $storeManager;
        $this->configShare = $configShare;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Model construct that should be used for object initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        // Set resource model.
        $this->_init('Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount');
    }

    /**
     * Retrieve subaccount model with subaccount data.
     *
     * @return SubaccountInterface
     */
    public function getDataModel()
    {
        $subaccountData = $this->getData();
        $subaccountDataObject = $this->subaccountDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $subaccountDataObject,
            $subaccountData,
            \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface::class
        );

        $subaccountDataObject->setId($this->getId());

        return $subaccountDataObject;
    }

    /**
     * Update subaccount data.
     *
     * @param SubaccountInterface $subaccount
     *
     * @return Subaccount
     */
    public function updateData(SubaccountInterface $subaccount)
    {
        $subaccountDataAttributes = $this->dataObjectProcessor
            ->buildOutputDataArray(
                $subaccount,
                \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface::class
            );
        foreach ($subaccountDataAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        return $this;
    }

    /**
     * Prepare some subaccount data before save.
     *
     * @return Subaccount|AbstractModel
     */
    public function beforeSave()
    {
        $this->setUpdatedAt($this->date->gmtDate());

        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->date->gmtDate());
        }

        $additionalInformation = $this->getAdditionalInformation();
        if (is_array($additionalInformation) === true) {
            $this->setData(
                SubaccountInterface::ADDITIONAL_INFORMATION,
                json_encode($additionalInformation)
            );
        }

        return parent::beforeSave();
    }

    /**
     * Retrieve current store.
     *
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * Load subaccount by customer id.
     *
     * @param int $customerId
     *
     * @return Subaccount
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCustomerId($customerId)
    {
        $this->_getResource()->loadByCustomerId($this, $customerId);

        return $this;
    }

    /**
     * Retrieve customer sharing configuration model.
     *
     * @return Share
     */
    public function getSharingConfig()
    {
        return $this->configShare;
    }
}
