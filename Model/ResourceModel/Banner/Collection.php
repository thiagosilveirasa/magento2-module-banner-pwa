<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner;

use Magento\Customer\Model\Group;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Model\Banner;

/**
 * Banner Collection
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = BannerInterface::BANNER_ID;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'banner_pwa_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'banner_collection';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        MetadataPool $metadataPool,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            Banner::class,
            \ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner::class
        );
        $this->_map['fields']['store'] = 'bps.store_id';
        $this->_map['fields']['customer_group'] = 'bpcg.customer_group_id';
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
    }

    /**
     * Perform operations after collection load
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad(?string $linkField): void
    {
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['bps' => $this->getTable(BannerInterface::STORE_TABLE_NAME)])
                ->where('bps.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
            $select = $connection->select()
                ->from(['bpcg' => $this->getTable(BannerInterface::CUSTOMER_GROUP_TABLE_NAME)])
                ->where('bpcg.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $customerGroupsData = [];
                foreach ($result as $customerGroupData) {
                    $customerGroupsData[$customerGroupData[$linkField]][] = $customerGroupData['customer_group_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($customerGroupsData[$linkedId])) {
                        continue;
                    }
                    $item->setData(BannerInterface::CUSTOMER_GROUP_ID, $customerGroupsData[$linkedId]);
                }
            }
        }
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }
        if ($field === 'customer_group_id') {
            return $this->addCustomerGroupFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|string|Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter(int|array|string|Store $store, bool $withAdmin = true): void
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        // @phpstan-ignore-next-line
        $this->addFilter('store', ['in' => $store], 'public');
    }

    /**
     * Perform adding filter by customer group
     *
     * @param int|array|string|Group $customerGroup
     * @return void
     */
    protected function performAddCustomerGroupFilter(int|array|string|Group $customerGroup): void
    {
        if ($customerGroup instanceof Group) {
            $customerGroup = [$customerGroup->getId()];
        }

        if (!is_array($customerGroup)) {
            $customerGroup = [$customerGroup];
        }

        // @phpstan-ignore-next-line
        $this->addFilter('customer_group', ['in' => $customerGroup], 'public');
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string|null $linkField
     * @return void
     */
    protected function joinStoreRelationTable(?string $linkField): void
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['bps' => $this->getTable(BannerInterface::STORE_TABLE_NAME)],
                'main_table.' . $linkField . ' = bps.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Join customer group id relation table if there is customer group id filter
     *
     * @param string|null $linkField
     * @return void
     */
    protected function joinCustomerGroupRelationTable(?string $linkField): void
    {
        if ($this->getFilter('customer_group')) {
            $this->getSelect()->join(
                ['bpcg' => $this->getTable(BannerInterface::CUSTOMER_GROUP_TABLE_NAME)],
                'main_table.' . $linkField . ' = bpcg.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);

        $this->performAfterLoad($entityMetadata->getLinkField());

        return parent::_afterLoad();
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql(): Select
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Select::GROUP);

        return $countSelect;
    }

    /**
     * Returns pairs banner_id - title
     *
     * @return array
     */
    public function toOptionIdArray(): array
    {
        $res = [];
        foreach ($this as $item) {
            $id = $item->getData('banner_id');

            $data = [];
            $data['value'] = $id;
            $data['label'] = $item->getData('title');

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Returns pairs banner_id - title
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->_toOptionArray('banner_id', 'title');
    }

    /**
     * Add filter by store
     *
     * @param int|array|string|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter(int|array|string|Store $store, bool $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Add filter by customer group id
     *
     * @param int|array|string|Group $customerGroup
     * @return $this
     */
    public function addCustomerGroupFilter(int|array|string|Group $customerGroup)
    {
        $this->performAddCustomerGroupFilter($customerGroup);

        return $this;
    }
    
    /**
     * Add filter by from date
     *
     * @param string $fromDate
     * @return $this
     */
    public function addFromDateFilter(string $fromDate)
    {
        $this->getSelect()
            ->where(
                'from_date is null or from_date <= ?',
                $fromDate
            );
        return $this;
    }
    
    /**
     * Add filter by to date
     *
     * @param string $toDate
     * @return $this
     */
    public function addToDateFilter(string $toDate)
    {
        $this->getSelect()
            ->where(
                'to_date is null or to_date >= ?',
                $toDate
            );
        return $this;
    }

    /**
     * Join store and customer group relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $this->joinStoreRelationTable($entityMetadata->getLinkField());
        $this->joinCustomerGroupRelationTable($entityMetadata->getLinkField());
    }
}
