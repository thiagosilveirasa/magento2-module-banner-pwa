<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class BannerCustomerGroupFilter implements CustomFilterInterface
{
    /**
     * Apply custom customer group filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        /** @var \ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\Collection $collection */
        $collection->addCustomerGroupFilter($filter->getValue());

        return true;
    }
}
