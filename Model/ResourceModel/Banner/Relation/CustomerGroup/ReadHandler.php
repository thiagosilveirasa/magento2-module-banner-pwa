<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\Relation\CustomerGroup;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner;

/**
 * Class ReadHandler for customer group relation
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Banner
     */
    protected $resourceBanner;

    /**
     * @param Banner $resourceBanner
     */
    public function __construct(
        Banner $resourceBanner
    ) {
        $this->resourceBanner = $resourceBanner;
    }

    /**
     * @inheritdoc
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $customerGroups = $this->resourceBanner->lookupCustomerGroupIds((int) $entity->getId());
            $entity->setData('customer_group_id', $customerGroups);
            $entity->setData('customer_groups', $customerGroups);
        }
        return $entity;
    }
}
