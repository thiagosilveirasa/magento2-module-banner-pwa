<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;

/**
 * Banner model
 */
class Banner extends AbstractDb
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        ?string $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            BannerInterface::TABLE_NAME,
            BannerInterface::BANNER_ID
        );
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(BannerInterface::class)->getEntityConnection();
    }

    /**
     * Get banner id.
     *
     * @param AbstractModel $object
     * @param int|string $value
     * @param string $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getBannerId(AbstractModel $object, int|string $value, ?string $field = null): bool|int|string
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        if (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * @inheritdoc
     */
    public function load($object, $value, $field = null)
    {
        $bannerId = $this->getBannerId($object, $value, $field);
        if ($bannerId) {
            $this->entityManager->load($object, $bannerId);
        }
        return $this;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds(int $id): array
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['cbs' => $this->getTable(BannerInterface::STORE_TABLE_NAME)], BannerInterface::STORE_ID)
            ->join(
                ['cb' => $this->getMainTable()],
                'cbs.' . $linkField . ' = cb.' . $linkField,
                []
            )
            ->where('cb.' . $entityMetadata->getIdentifierField() . ' = :banner_id');

        return $connection->fetchCol($select, [$entityMetadata->getIdentifierField() => (int) $id]);
    }

    /**
     * Get customer group ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupCustomerGroupIds(int $id): array
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(
                ['bpcg' => $this->getTable(BannerInterface::CUSTOMER_GROUP_TABLE_NAME)],
                BannerInterface::CUSTOMER_GROUP_ID
            )
            ->join(
                ['bp' => $this->getMainTable()],
                'bpcg.' . $linkField . ' = bp.' . $linkField,
                []
            )
            ->where('bp.' . $entityMetadata->getIdentifierField() . ' = :banner_id');

        return $connection->fetchCol($select, [$entityMetadata->getIdentifierField() => (int) $id]);
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        if ($object->hasDataChanges()) {
            $object->setUpdatedAt(null);
        }

        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
