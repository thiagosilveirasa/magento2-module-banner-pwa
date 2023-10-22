<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Resolver\Banner\Filters;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Model\Resolver\Banner\Filters\AddFilterInterface;

/**
 * Adds filters to display on.
 */
class DisplayOnFilter implements AddFilterInterface
{
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(FilterBuilder $filterBuilder)
    {
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @inheritdoc
     */
    public function addFilter(SearchCriteriaBuilder $searchCriteriaBuilder, Filter $filter): void
    {
        /** @var array $value */
        $value = $filter->getValue();

        $filterDisplayOnPage = $this->filterBuilder
            ->setField(BannerInterface::DISPLAY_ON_PAGE)
            ->setValue(strtolower($value['page']))
            ->setConditionType('eq')
            ->create();
        $searchCriteriaBuilder->addFilter($filterDisplayOnPage);

        $filterDisplayOnId = $this->filterBuilder
            ->setField(BannerInterface::DISPLAY_ON_ID)
            ->setValue($value['id'])
            ->setConditionType(is_numeric($value['id']) ? 'finset' : 'eq')
            ->create();
        $searchCriteriaBuilder->addFilter($filterDisplayOnId);
    }
}
