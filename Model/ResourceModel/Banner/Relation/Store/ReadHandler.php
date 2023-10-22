<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner;

/**
 * Class ReadHandler for store relation
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
            $stores = $this->resourceBanner->lookupStoreIds((int) $entity->getId());
            $entity->setData('store_id', $stores);
            $entity->setData('stores', $stores);
        }
        return $entity;
    }
}
