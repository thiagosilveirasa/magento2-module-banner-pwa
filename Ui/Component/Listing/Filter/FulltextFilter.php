<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Listing\Filter;

use InvalidArgumentException;
use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\Grid\Collection as GridCollection;

/**
 * Full text filter for banner listing data source
 */
class FulltextFilter implements FilterApplierInterface
{
    /**
     * @inheritdoc
     */
    public function apply(Collection $collection, Filter $filter)
    {
        if (!$collection instanceof AbstractDb) {
            throw new InvalidArgumentException('Database collection required.');
        }

        /** @var GridCollection $gridCollection */
        $gridCollection = $collection;
        $value = $filter->getValue() ? trim($filter->getValue()) : '';
        $gridCollection->addFullTextFilter($value);
    }
}
