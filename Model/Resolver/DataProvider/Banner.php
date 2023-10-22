<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Resolver\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;
use ThiagoSilveira\BannerPWA\Api\BannerRepositoryInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Model\Resolver\Banner\Filters\AddFilterInterface;

/**
 * Banner data provider
 */
class Banner
{
    /**
     * @var BannerRepositoryInterface
     */
    protected $bannerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SearchCriteria
     */
    protected $searchCriteria;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $date;

    /**
     * @var AddFilterInterface[]
     */
    private $additionalFilterPool;

    /**
     * @param BannerRepositoryInterface $bannerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param array $additionalFilterPool
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        TimezoneInterface $date,
        array $additionalFilterPool = []
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->date = $date;
        $this->additionalFilterPool = $additionalFilterPool;
    }

    /**
     * Get banners data
     *
     * @param array $filters
     * @param int $customerGroupId
     * @param int $storeId
     * @param array $args
     * @return array
     * @throws NoSuchEntityException
     */
    public function getBanners(array $filters, int $customerGroupId, int $storeId, array $args): array
    {
        $now = $this->date->date(null, null, false)->format('Y-m-d H:i:s');

        $filters[BannerInterface::STORE_ID] = [$storeId, Store::DEFAULT_STORE_ID];
        $filters[BannerInterface::CUSTOMER_GROUP_ID] = $customerGroupId;
        $filters[BannerInterface::IS_ACTIVE] = true;
        $filters[BannerInterface::FROM_DATE] = $now;
        $filters[BannerInterface::TO_DATE] = $now;

        $this->processFilters($filters);
        $this->addSortOrder(BannerInterface::SORT_ORDER, SortOrder::SORT_ASC);
     
        $pageSize = $args['pageSize'] ?? null;
        $currentPage = $args['currentPage'] ?? 1;

        if ($pageSize) {
            $this->searchCriteriaBuilder->setPageSize($pageSize);
            $this->searchCriteriaBuilder->setCurrentPage($currentPage);
        }

        $bannerResults = $this->bannerRepository
            ->getList($this->getSearchCriteria());

        $banners = [];

        foreach ($bannerResults->getItems() as $banner) {
            $banners[] = [
                BannerInterface::ID => $banner->getBannerId(),
                BannerInterface::NAME => $banner->getName(),
                BannerInterface::DISPLAY_ON_PAGE => $this->sanitizeType($banner->getDisplayOnPage()),
                BannerInterface::DISPLAY_ON_ID => $banner->getDisplayOnId(),
                BannerInterface::POSITION => $banner->getPosition(),
                BannerInterface::TITLE => $banner->getTitle(),
                BannerInterface::IMAGE => $banner->getImageUrl(),
                BannerInterface::URL => $banner->getUrl(),
                BannerInterface::NEWTAB => $banner->isOpenInNewtab(),
                BannerInterface::SORT_ORDER => $banner->getSortOrder(),
            ];
        }

        if (!$pageSize) {
            $pageSize = $bannerResults->getTotalCount();
        }

        $totalPages = $pageSize ? ((int) ceil($bannerResults->getTotalCount() / $pageSize)) : 0;

        return [
            'total_count' => $bannerResults->getTotalCount(),
            'items' => $banners,
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
            ],
        ];
    }

    /**
     * Process a list of filters based on the provided filter matrix and add to filter groups
     *
     * @param array $filters
     * @return void
     */
    protected function processFilters(array $filters): void
    {
        foreach ($filters as $filterName => $filterValue) {
            $conditionType = is_array($filterValue) ? 'in' : 'eq';
            $this->addFilter(
                $this->filterBuilder
                    ->setField($filterName)
                    ->setValue($filterValue)
                    ->setConditionType($conditionType)
                    ->create()
            );
        }
    }

    /**
     * Create a filter group based on the filter array provided and add to the filter groups
     *
     * @param Filter $filter
     * @return void
     */
    protected function addFilter(Filter $filter): void
    {
        if (!empty($this->additionalFilterPool[$filter->getField()])) {
            $this->additionalFilterPool[$filter->getField()]->addFilter($this->searchCriteriaBuilder, $filter);
        } else {
            $this->searchCriteriaBuilder->addFilter($filter);
        }
    }

    /**
     * Add sort order
     *
     * @param string $field
     * @param string $direction
     * @return void
     */
    protected function addSortOrder(string $field, string $direction): void
    {
        $this->searchCriteriaBuilder->addSortOrder($field, $direction);
    }

    /**
     * Get search criteria
     *
     * @return SearchCriteria
     */
    protected function getSearchCriteria(): SearchCriteria
    {
        // @phpstan-ignore-next-line
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
        }
        return $this->searchCriteria;
    }

    /**
     * Sanitize the display on page to fit schema specifications
     *
     * @param string $displayOnPage
     * @return string
     */
    protected function sanitizeType(string $displayOnPage): string
    {
        return strtoupper(str_replace('-', '_', $displayOnPage));
    }
}
