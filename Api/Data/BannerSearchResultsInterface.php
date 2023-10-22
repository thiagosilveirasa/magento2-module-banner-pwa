<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;

/**
 * Interface for cms banner search results.
 *
 * @api
 */
interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get banners list.
     *
     * @return BannerInterface[]
     */
    public function getItems();

    /**
     * Set banners list.
     *
     * @param BannerInterface[]|\Magento\Framework\DataObject[] $items
     * @return $this
     */
    public function setItems(array $items);
}
