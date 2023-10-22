<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\Relation\CustomerGroup;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner;

/**
 * Class SaveHandler for customer group relation
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Banner
     */
    protected $resourceBanner;

    /**
     * @param MetadataPool $metadataPool
     * @param Banner $resourceBanner
     */
    public function __construct(
        MetadataPool $metadataPool,
        Banner $resourceBanner
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceBanner = $resourceBanner;
    }

    /**
     * @inheritdoc
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldCustomerGroups = $this->resourceBanner->lookupCustomerGroupIds((int) $entity->getId());
        $newCustomerGroups = (array) $entity->getCustomerGroups();

        $table = $this->resourceBanner->getTable(BannerInterface::CUSTOMER_GROUP_TABLE_NAME);

        $delete = array_diff($oldCustomerGroups, $newCustomerGroups);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int) $entity->getData($linkField),
                'customer_group_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newCustomerGroups, $oldCustomerGroups);
        if ($insert) {
            $data = [];
            foreach ($insert as $customerGroupId) {
                $data[] = [
                    $linkField => (int) $entity->getData($linkField),
                    'customer_group_id' => (int) $customerGroupId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
