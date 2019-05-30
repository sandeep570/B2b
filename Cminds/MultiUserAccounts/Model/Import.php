<?php

namespace Cminds\MultiUserAccounts\Model;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterfaceFactory;
use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Import\Source\CsvFactory as CsvSourceFactory;
use Cminds\MultiUserAccounts\Model\Import\Source\ApiFactory as ApiSourceFactory;
use Cminds\MultiUserAccounts\Model\Import\SourceInterface;
use Cminds\MultiUserAccounts\Model\Import\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterface;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory as CustomerModelFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Symfony\Component\Console\Output\OutputInterface;
use Cminds\MultiUserAccounts\Model\ResourceModel\SubaccountTransportRepository;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount\CollectionFactory as SubaccountCollectionFactory;


/**
 * Cminds MultiUserAccounts import model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Import
{
    /**
     * Core registry keys.
     */
    const SKIP_CUSTOMER_WELCOME_EMAIL
        = 'cminds_multiuseraccounts_skip_customer_welcome_email';

    /**
     * Source types.
     */
    const SOURCE_CSV = 'csv';
    /**
     *
     */
    const SOURCE_API = 'api';

    /**
     * Environment types.
     */
    const ENVIRONMENT_CLI = 'cli';
    const ENVIRONMENT_API = 'api';

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var CsvSourceFactory
     */
    private $csvSourceFactory;

    /**
     * @var ApiSourceFactory
     */
    private $apiSourceFactory;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var AddressInterfaceFactory
     */
    private $addressFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SubaccountInterfaceFactory
     */
    private $subaccountFactory;

    /**
     * @var SubaccountRepositoryInterface
     */
    private $subaccountRepository;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var DataObjectProcessor
     */
    private $dataProcessor;

    /**
     * @var CustomerModelFactory
     */
    private $customerModelFactory;

    /**
     * @var SourceInterface
     */
    private $sourceProcessor;

    /**
     * @var string
     */
    private $environment = self::ENVIRONMENT_API;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Flag for Update via API
     */
    private $updateFlag = false;

    /**
     * Customer ID passed in the API call
     */
    private $customerId;

    /**
     * @var
     */
    private $parentId;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var SubaccountTransportRepository
     */
    private $subaccountTransportRepository;

    /**
     * @var SubaccountCollectionFactory
     */
    private $subaccountCollectionFactory;


    /**
     * @var bool
     */
    private $wasPromoted = false;

    /**
     * @var bool
     */
    private $wasLinked = false;


    /**
     * @var bool
     */
    public $linkFlag = false;
    /**
     * Object constructor.
     *
     * @param Validator $validator
     * @param CsvSourceFactory $csvSourceFactory
     * @param AccountManagementInterface $accountManagement
     * @param AddressInterfaceFactory $addressFactory
     * @param ResourceConnection $resourceConnection
     * @param CountryFactory $countryFactory
     * @param RegionInterfaceFactory $regionFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CustomerInterfaceFactory $customerFactory
     * @param State $appState
     * @param CustomerRepositoryInterface $customerRepository
     * @param SubaccountInterfaceFactory $subaccountFactory
     * @param SubaccountRepositoryInterface $subaccountRepository
     * @param Permission $permission
     * @param IndexerRegistry $indexerRegistry
     * @param DataObjectProcessor $dataProcessor
     * @param CustomerModelFactory $customerModelFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Validator $validator,
        CsvSourceFactory $csvSourceFactory,
        ApiSourceFactory $apiSourceFactory,
        AccountManagementInterface $accountManagement,
        AddressInterfaceFactory $addressFactory,
        ResourceConnection $resourceConnection,
        CountryFactory $countryFactory,
        RegionInterfaceFactory $regionFactory,
        DataObjectHelper $dataObjectHelper,
        CustomerInterfaceFactory $customerFactory,
        State $appState,
        CustomerRepositoryInterface $customerRepository,
        SubaccountInterfaceFactory $subaccountFactory,
        SubaccountRepositoryInterface $subaccountRepository,
        Permission $permission,
        IndexerRegistry $indexerRegistry,
        DataObjectProcessor $dataProcessor,
        CustomerModelFactory $customerModelFactory,
        Registry $coreRegistry,
        AddressRepositoryInterface $addressRepository,
        SubaccountTransportRepository $subaccountTransportRepository,
        SubaccountCollectionFactory $subaccountCollectionFactory
    )
    {
        $this->validator = $validator;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->apiSourceFactory = $apiSourceFactory;
        $this->accountManagement = $accountManagement;
        $this->addressFactory = $addressFactory;
        $this->resourceConnection = $resourceConnection;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerFactory = $customerFactory;
        $this->appState = $appState;
        $this->customerRepository = $customerRepository;
        $this->subaccountFactory = $subaccountFactory;
        $this->subaccountRepository = $subaccountRepository;
        $this->permission = $permission;
        $this->indexerRegistry = $indexerRegistry;
        $this->dataProcessor = $dataProcessor;
        $this->customerModelFactory = $customerModelFactory;
        $this->coreRegistry = $coreRegistry;
        $this->addressRepository = $addressRepository;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->subaccountCollectionFactory = $subaccountCollectionFactory;

        $this->environment = self::ENVIRONMENT_API;
    }

    /**
     * @param $bool
     * @return $this
     */
    public function setLinkFlag($bool)
    {
        $this->linkFlag = $bool;
        return $this;
    }

    /**
     * @return bool
     */
    public function getWasLinked()
    {
        return $this->wasLinked;
    }

    /**
     * @return bool
     */
    public function getWasPromoted()
    {
        return $this->wasPromoted;
    }

    /**
     * Initialize and retrieve source processor object.
     *
     * @param string $sourceType
     *
     * @return SourceInterface
     * @throws LocalizedException
     */
    public function initSourceProcessor($sourceType)
    {
        switch ($sourceType) {
            case self::SOURCE_CSV:
                $this->sourceProcessor = $this->csvSourceFactory->create();
                break;
            case self::SOURCE_API:
                $this->sourceProcessor = $this->apiSourceFactory->create();
                break;
            default:
                throw new LocalizedException(__('Unhandled source type.'));
        }

        return $this->sourceProcessor;
    }

    /**
     * @param string $environment
     *
     * @return Import
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @param OutputInterface $output
     *
     * @return Import
     */
    public function setOutputStream(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Return bool value depends if import is running in cli.
     *
     * @return bool
     */
    private function isCliEnvironment()
    {
        return $this->environment === self::ENVIRONMENT_CLI;
    }

    /**
     * Retrieve source processor.
     *
     * @return SourceInterface
     * @throws LocalizedException
     */
    private function getSourceProcessor()
    {
        if (!$this->sourceProcessor instanceof SourceInterface) {
            throw new LocalizedException(
                __('Source processor has been not initialized.')
            );
        }

        return $this->sourceProcessor;
    }

    /**
     * @param string $log
     *
     * @return Import
     */
    private function debugLog($log)
    {
        if ($this->isCliEnvironment()) {
            $this->output->writeln($log);
        }

        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function update($id)
    {
        if ($id) {
            $customer = $this->customerRepository->getById($id);
            $updateData = $this->getSourceProcessor()->getAccountsData();
            foreach ($updateData as $array) {
                foreach ($array as $key => $value) {
                    $customer->setData($key, $value);
                }
            }
            $this->customerRepository->save($customer);
        }
        return $this;
    }

    /**
     * Process source data.
     *
     * @return Import
     * @throws \RuntimeException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \DomainException
     * @throws \Exception
     */
    public function process()
    {
        $this->coreRegistry->register(self::SKIP_CUSTOMER_WELCOME_EMAIL, true);

        /** @var array $accountsData */
        $accountsData = $this->getSourceProcessor()->getAccountsData();
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();

        $accountsCount = count($accountsData);
        $accountsCounter = 1;

        try {
            foreach ($accountsData as $accountData) {
                $this->debugLog(sprintf(
                    '[%s/%s] Processing account "%s"...',
                    $accountsCounter,
                    $accountsCount,
                    $accountData['email']
                ));
                ++$accountsCounter;

                $this->processAccountData($accountData);

                if (empty($accountData['subaccounts'])) {
                    continue;
                }

                /** @var array $subaccountsData */
                $subaccountsData = $accountData['subaccounts'];

                $subaccountsCount = count($subaccountsData);
                $subaccountsCounter = 1;

                foreach ($subaccountsData as $subaccountData) {
                    $this->debugLog(sprintf(
                        '> [%s/%s] Processing subaccount "%s"...',
                        $subaccountsCounter,
                        $subaccountsCount,
                        $subaccountData['email']
                    ));
                    ++$subaccountsCounter;

                    $this->processAccountData($subaccountData);
                }
            }

            $connection->commit();
            $this->coreRegistry->unregister(self::SKIP_CUSTOMER_WELCOME_EMAIL);
        } catch (LocalizedException $e) {
            $connection->rollBack();
            $this->coreRegistry->unregister(self::SKIP_CUSTOMER_WELCOME_EMAIL);

            throw $e;
        }

        $this->debugLog('Reindexing customer grid...');

        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();

        return $this;
    }

    /**
     * @param array $accountData
     * @return $this
     * @throws LocalizedException
     */
    private function processAccountData(array $accountData)
    {

        if ($this->updateFlag) {

        }
        $isPromote = false;

        if (isset($accountData['promote']) && $accountData['promote'] == 1) {
            $isPromote = true;
            $accountData['parent_email'] = '';
        }
        $accountData = $this->validator->filterData($accountData);


        $customerData['profile'] = $this
            ->prepareCustomerProfileData($accountData);

        $isEmailAvailable = $this->accountManagement
            ->isEmailAvailable($customerData['profile']['email']);
        if (!$isEmailAvailable) {
            if (!$this->updateFlag) {
                throw new LocalizedException(
                    __(
                        'Account with email "%1" already exists.',
                        $customerData['profile']['email']
                    )
                );
            }
        }
        if ($this->updateFlag) {
            $customer = $this->customerRepository->getById($this->customerId);
            foreach ($accountData as $key => $value) {
                $customer->setData($key, $value);
            }
        } else {
            $customer = $this->customerFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $customer,
                $customerData['profile'],
                '\Magento\Customer\Api\Data\CustomerInterface'
            );
        }
        // delete address if create sub method engaged.
//        if (!isset($this->subId) || $this->subId === null) {
//            $addressKeys = [
//                'street','city','region','postcode','telephone',
//            ];
//            foreach ($addressKeys as $key) {
//                $accountData[$key] = '';
//            }
//        }

        $customerData['address'] = $this
            ->prepareCustomerAddressData($accountData);

        if (!empty($customerData['address'])) {
            $customerData['address']['region_id'] = $this
                ->getRegionId($customerData['address']);

            if ($customerData['address']['region_id'] == null) {
                if (isset($accountData['region'])) {
                    $customerData['address']['region_id'] = $accountData['region'];
                }
            }

            $address = $customerData['address'];
            $regionData = [
                RegionInterface::REGION_ID => $address['region_id'],
                RegionInterface::REGION => !empty($address['region'])
                    ? $address['region']
                    : null,
                RegionInterface::REGION_CODE => !empty($address['region_code'])
                    ? $address['region_code']
                    : null,
            ];

            $region = $this->regionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $region,
                $regionData,
                '\Magento\Customer\Api\Data\RegionInterface'
            );

            $addresses = $this->addressFactory->create();
            unset($customerData['address']['region']);
            $this->dataObjectHelper->populateWithArray(
                $addresses,
                $customerData['address'],
                '\Magento\Customer\Api\Data\AddressInterface'
            );

            $addresses
                ->setRegion($region)
                ->setIsDefaultBilling(true)
                ->setIsDefaultShipping(true);

            $customer->setAddresses([$addresses]);
        }
        if (!$this->updateFlag) {

            $result = $this->appState->emulateAreaCode(
                'frontend',
                [$this->accountManagement, 'createAccount'],
                [$customer, $accountData['password']]
            );
        }
        /** @var array $savedCustomerData */
        $savedCustomerData = $this->dataProcessor
            ->buildOutputDataArray(
                $customer,
                '\Magento\Customer\Api\Data\CustomerInterface'
            );
        if (isset($savedCustomerData['custom_attributes'])) {
            foreach ($savedCustomerData['custom_attributes'] as $customAttribute) {
                $savedCustomerData[$customAttribute['attribute_code']]
                    = $customAttribute['value'];
            }
            unset($savedCustomerData['custom_attributes']);
        }
        if (!$this->updateFlag) {
            $notSavedCustomerData = array_diff($customerData['profile'], $savedCustomerData);
            if (!empty($notSavedCustomerData)) {
                $customerModel = $this->customerModelFactory->create()
                    ->load($result->getId());

                foreach ($notSavedCustomerData as $key => $value) {
                    $customerModel->setData($key, $value);
                }
                $customerModel->save();
            }
            if ($accountData['is_active'] !== null) {
                $customer->setCustomAttribute('customer_is_active', $accountData['is_active']);
            }
            if (isset($data[0]['can_manage_subaccounts']) && $data[0]['can_manage_subaccounts'] !== '') {
                if ($accountData['can_manage_subaccounts'] !== null) {
                    $customer->setCustomAttribute('can_manage_subaccounts', $accountData['can_manage_subaccounts']);
                    $this->customerRepository->save($customer);
                }
            }
        } else {
            // update flag true -->
            if ($accountData['is_active'] !== null) {
                $customer->setCustomAttribute('customer_is_active', $accountData['is_active']);
            }
            if (isset($accountData['parent_email']) && $accountData['parent_email'] == "") {
                $customer->setCustomAttribute('can_manage_subaccounts', 1);
                // TODO need to swap old parent from MutliUserAccounts db table
            }
            if (isset($accountData['can_manage_subaccounts'])) {
                if ($accountData['can_manage_subaccounts'] !== null) {
                    $customer->setCustomAttribute('can_manage_subaccounts', $accountData['can_manage_subaccounts']);
                }
            }


            $this->customerRepository->save($customer);
        }


        if (!empty($accountData['parent_email'])) {
            if (!$this->updateFlag) {
                try {
                    $subaccountParent = $this->customerRepository
                        ->get($accountData['parent_email']);
                } catch (NoSuchEntityException $e) {
                    throw new LocalizedException(
                        __(
                            'Parent account "%1" does not exists.',
                            $accountData['parent_email']
                        )
                    );
                }
                try {
                    $websiteId = null;
                    if (!empty($accountData['website_id'])) {
                        $websiteId = $accountData['website_id'];
                    }

                    $subaccountParent = $this->customerRepository
                        ->get($accountData['parent_email'], $websiteId);
                } catch (NoSuchEntityException $e) {
                    throw new LocalizedException(
                        __(
                            'Parent account "%1" does not exists.',
                            $accountData['parent_email']
                        )
                    );
                }

                /** @var array $subaccountData */
                $subaccountData = $this->prepareSubaccountData($accountData);

                $subaccount = $this->subaccountFactory->create();
                $permissionKeys = $this->permission->getPermissionKeys();
                foreach ($permissionKeys as $permissionKey) {
                    if (!empty($accountData[$permissionKey])) {
                        $setter = $this->permission->getPermissionSetter($permissionKey);
                        $subaccount->{$setter}($accountData[$permissionKey]);
                    }
                }

                $this->permission->recalculatePermission($subaccount);

                $subaccount
                    ->setCustomerId($result->getId())
                    ->setParentCustomerId($subaccountParent->getId())
                    ->setIsActive($subaccountData['is_active']);

                $this->subaccountRepository->save($subaccount);
            } else {
                // update flag true --> parent link
                if (isset($accountData['parent_email']) && $this->linkFlag) {
                    $subaccount = $this->subaccountFactory->create();
                    $subaccountData = $this->prepareSubaccountData($accountData);

                    $subaccountCollection = $this->subaccountCollectionFactory->create();
                    $subaccountCollection->filterByCustomerId($this->customerId);
                    if (count($subaccountCollection) == 0) {
                        $subaccount
                            ->setCustomerId($this->customerId)
                            ->setParentCustomerId($this->parentId)
                            ->setIsActive(1);
                    $this->subaccountRepository->save($subaccount);
                    } else {
                        foreach ($subaccountCollection as $sub) {
                            if ($sub->getCustomerId() == $this->customerId) {
                                $sub->setParentCustomerId($this->parentId);
                                $sub->setIsActive(1);
                                $sub->save();
                            }
                        }
                    }
                    $this->wasLinked = true;
                } else {
                    $subaccountData = $this->prepareSubaccountData($accountData);
                    $subaccount = $this->subaccountRepository->getByCustomerId($this->customerId);


                    $permissionKeys = $this->permission->getPermissionKeys();
                    foreach ($permissionKeys as $permissionKey) {
                        if (array_key_exists($permissionKey, $accountData)) {
                            if ($accountData[$permissionKey] == 0 || $accountData[$permissionKey] == 1) {
                                $setter = $this->permission->getPermissionSetter($permissionKey);
                                $subaccount->{$setter}($accountData[$permissionKey]);
                            }
                        }
                    }

                    $this->permission->recalculatePermission($subaccount);

                    $subaccount
                        ->setCustomerId($this->customerId)
                        ->setParentCustomerId($this->getSourceProcessor()->getParentId())
                        ->setIsActive($subaccountData['is_active']);

                    $this->subaccountRepository->save($subaccount);
                }
            }
        } else {

            if ($isPromote === true) {
                // @todo: promote to parent
                $subaccount = $this->subaccountRepository->getByCustomerId($this->customerId);
                $currentParentId = $subaccount->getParentCustomerId();

                $subaccountCollection = $this->subaccountCollectionFactory->create();
                $subaccountCollection->filterByParentCustomerId($currentParentId);

                foreach ($subaccountCollection as $siblingSubAccount) {
                    if ((int)$siblingSubAccount->getCustomerId() === $this->customerId) {
                        $siblingSubAccount->delete();
                    }
                    $siblingSubAccount->setParentCustomerId($this->customerId);
                    $siblingSubAccount->save();
                }

                $subaccountFromMaster = $this->subaccountFactory->create();
                $subaccountFromMaster
                    ->setCustomerId($currentParentId)
                    ->setParentCustomerId($this->customerId)
                    ->setIsActive(1);

                $this->subaccountRepository->save($subaccountFromMaster);

                $this->wasPromoted = true;
            }
        }
        return $this;
    }

    /**
     * Prepare customer profile data.
     *
     * @param array $sourceData
     *
     * @return array
     */
    private function prepareSubaccountData($sourceData)
    {
        $preparedData = $this->validator->getDefaultSubaccountData();
        foreach ($preparedData as $key => $value) {
            if (isset($sourceData[$key]) && $sourceData[$key] !== '') {
                $preparedData[$key] = $sourceData[$key];
            }
        }

        return $preparedData;
    }

    /**
     * Prepare customer profile data.
     *
     * @param array $sourceData
     *
     * @return array
     */
    private function prepareCustomerProfileData($sourceData)
    {
        $preparedData = $this->validator->getDefaultCustomerProfileData();
        foreach ($preparedData as $key => $value) {
            if (!empty($sourceData[$key])) {
                $preparedData[$key] = $sourceData[$key];
            }
            if ($key === 'is_active') {
                if (isset($sourceData['is_active'])) {
                    $preparedData['is_active'] = $sourceData['is_active'];
                }
            }
        }

        return $preparedData;
    }

    /**
     * Prepare customer address data.
     *
     * @param array $sourceData
     *
     * @return array
     */
    private function prepareCustomerAddressData($sourceData)
    {
        $preparedData = $this->validator->getDefaultCustomerAddressData();

        $preparedData['street'][0] = !empty($sourceData['street_1'])
            ? $sourceData['street_1'] : '';
        $preparedData['street'][1] = !empty($sourceData['street_2'])
            ? $sourceData['street_2'] : '';

//        $preparedData = $this->validator->getDefaultCustomerAddressData();
        foreach ($preparedData as $key => $value) {
            if (!empty($sourceData[$key])) {
                $preparedData[$key] = $sourceData[$key];
            }
        }


        if (!$this->validator->validateCustomerAddressData($preparedData)) {
            return [];
        }

        return $preparedData;
    }

    /**
     * Retrieve region id.
     *
     * @param array $address Address data array.
     *
     * @return int
     */
    private function getRegionId($address)
    {
        $country = $this->countryFactory->create()
            ->loadByCode($address['country_id']);

        return $country->getRegionCollection()
            ->addFieldToFilter(
                'name',
                $address['region']
            )
            ->getFirstItem()
            ->getId();
    }

    /**
     * Set UpdateFlag to true.
     *
     * @return bool
     */
    public function setUpdateFlag()
    {
        $this->updateFlag = true;
        return $this->updateFlag;
    }

    /**
     * Set customer Id for customer that will be edited.
     *
     * @return int
     */
    public function setCustomerId($id)
    {
        $this->customerId = $id;
        return $id;
    }


    /**
     * Set parent Id for customer that will be edited.
     *
     * @return int
     */
    public function setParentId($id)
    {
        $this->parentId = $id;
        return $id;
    }
}
