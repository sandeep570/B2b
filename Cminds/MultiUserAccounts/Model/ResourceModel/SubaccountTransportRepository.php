<?php

namespace Cminds\MultiUserAccounts\Model\ResourceModel;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Api\Data\SubaccountInterfaceFactory;
use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterfaceFactory;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Model\SubaccountRegistry;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\CustomerSecure;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Cminds MultiUserAccounts subaccount transport repository resource model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class SubaccountTransportRepository implements SubaccountTransportRepositoryInterface
{
    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * @var SubaccountRepository
     */
    private $subaccountRepository;

    /**
     * @var SubaccountRegistry
     */
    private $subaccountRegistry;

    /**
     * @var SubaccountTransportInterfaceFactory
     */
    private $subaccountTransportDataFactory;

    /**
     * @var SubaccountInterfaceFactory
     */
    private $subaccountDataFactory;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CoreRegistry
     */
    private $coreRegistry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Object initialization.
     *
     * @param CustomerRepository                  $customerRepository
     * @param CustomerRegistry                    $customerRegistry
     * @param CustomerInterfaceFactory            $customerDataFactory
     * @param SubaccountRepository                $subaccountRepository
     * @param SubaccountRegistry                  $subaccountRegistry
     * @param SubaccountTransportInterfaceFactory $subaccountTransportDataFactory
     * @param SubaccountInterfaceFactory          $subaccountDataFactory
     * @param DataObjectProcessor                 $dataObjectProcessor
     * @param DataObjectHelper                    $dataObjectHelper
     * @param CoreRegistry                        $coreRegistry
     * @param StoreManagerInterface               $storeManager
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerRegistry $customerRegistry,
        CustomerInterfaceFactory $customerDataFactory,
        SubaccountRepository $subaccountRepository,
        SubaccountRegistry $subaccountRegistry,
        SubaccountTransportInterfaceFactory $subaccountTransportDataFactory,
        SubaccountInterfaceFactory $subaccountDataFactory,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        CoreRegistry $coreRegistry,
        StoreManagerInterface $storeManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->customerDataFactory = $customerDataFactory;
        $this->subaccountRepository = $subaccountRepository;
        $this->subaccountRegistry = $subaccountRegistry;
        $this->subaccountTransportDataFactory = $subaccountTransportDataFactory;
        $this->subaccountDataFactory = $subaccountDataFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param SubaccountTransportInterface $subaccountTransportDataObject
     *
     * @return SubaccountTransportInterface
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function save(
        SubaccountTransportInterface $subaccountTransportDataObject
    ) {
        $subaccountTransportDataArray = $this->dataObjectProcessor
            ->buildOutputDataArray(
                $subaccountTransportDataObject,
                '\Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface'
            );
        unset($subaccountTransportDataArray[$subaccountTransportDataObject::ID]);

        if ($subaccountTransportDataObject->getCustomerId()) {
            /** @var \Magento\Customer\Model\Customer $customerModel */
            $customerModel = $this->customerRegistry
                ->retrieve($subaccountTransportDataObject->getCustomerId());

            /** @var CustomerInterface $customerDataObject */
            $customerDataObject = $customerModel->getDataModel();
        } else {
            /** @var CustomerInterface $customerDataObject */
            $customerDataObject = $this->customerDataFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $customerDataObject,
            $subaccountTransportDataArray,
            '\Cminds\MultiUserAccounts\Api\Data\CustomerInterface'
        );

        $parentCustomerData = $this->customerRepository
            ->getById($subaccountTransportDataObject->getParentCustomerId());

        $customerDataObject
            ->setWebsiteId($parentCustomerData->getWebsiteId())
            ->setStoreId($parentCustomerData->getStoreId());

        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();
        if ($currentWebsiteId !== $parentCustomerData->getWebsiteId()) {
            $this->storeManager->setCurrentStore($parentCustomerData->getStoreId());
        }

        $savedCustomerDataObject = $this->customerRepository
            ->save($customerDataObject);

        if ($subaccountTransportDataObject->getId()) {
            /** @var Subaccount $subaccountModel */
            $subaccountModel = $this->subaccountRegistry
                ->retrieve($subaccountTransportDataObject->getId());

            /** @var SubaccountInterface $subaccountDataObject */
            $subaccountDataObject = $subaccountModel->getDataModel();
        } else {
            /** @var SubaccountInterface $subaccountDataObject */
            $subaccountDataObject = $this->subaccountDataFactory->create();
        }

        $this->dataObjectHelper->populateWithArray(
            $subaccountDataObject,
            $subaccountTransportDataArray,
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountInterface'
        );

        $subaccountDataObject->setCustomerId($savedCustomerDataObject->getId());
        $savedSubaccountDataObject = $this->subaccountRepository
            ->save($subaccountDataObject);

        $savedSubaccountTransactionDataObject = $this
            ->getById($savedSubaccountDataObject->getId());

        return $savedSubaccountTransactionDataObject;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $customerId
     *
     * @return SubaccountTransportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId)
    {
        /** @var SubaccountInterface $subaccountDataObject */
        $subaccountDataObject = $this->subaccountRepository
            ->getByCustomerId($customerId);

        return $this->getById($subaccountDataObject->getId());
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $subaccountId
     *
     * @return SubaccountTransportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($subaccountId)
    {
        /** @var SubaccountInterface $subaccountDataObject */
        $subaccountDataObject = $this->subaccountRepository
            ->getById($subaccountId);
        $subaccountDataArray = $this->dataObjectProcessor->buildOutputDataArray(
            $subaccountDataObject,
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountInterface'
        );

        /** @var CustomerInterface $customerDataObject */
        $customerDataObject = $this->customerRepository
            ->getById($subaccountDataObject->getCustomerId());
        $customerDataArray = $this->dataObjectProcessor->buildOutputDataArray(
            $customerDataObject,
            '\Cminds\MultiUserAccounts\Api\Data\CustomerInterface'
        );
        unset($customerDataArray['id']);

        /** @var CustomerSecure $customerSecureData */
        $customerSecureDataObject = $this->customerRegistry
            ->retrieveSecureData($subaccountDataObject->getCustomerId());
        $customerSecureDataArray = $this->dataObjectProcessor
            ->buildOutputDataArray(
                $customerSecureDataObject,
                '\Cminds\MultiUserAccounts\Api\Data\CustomerSecureInterface'
            );

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->subaccountTransportDataFactory
            ->create();

        $this->dataObjectHelper->populateWithArray(
            $subaccountTransportDataObject,
            array_merge(
                $subaccountDataArray,
                $customerDataArray,
                $customerSecureDataArray
            ),
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface'
        );

        return $subaccountTransportDataObject;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param SubaccountTransportInterface $subaccountTransportDataObject
     *
     * @return bool
     * @throws \RuntimeException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function delete(
        SubaccountTransportInterface $subaccountTransportDataObject
    ) {
        return $this->deleteById($subaccountTransportDataObject->getId());
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $subaccountId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function deleteById($subaccountId)
    {
        $this->coreRegistry->register('isSecureArea', true);

        /** @var SubaccountInterface $subaccountDataObject */
        $subaccountDataObject = $this->subaccountRepository
            ->getById($subaccountId);

        $this->subaccountRepository
            ->deleteById($subaccountId);
        $this->customerRepository
            ->deleteById($subaccountDataObject->getCustomerId());

        return true;
    }
}
