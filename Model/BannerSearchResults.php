<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model;

use Magento\Framework\Api\SearchResults;
use ThiagoSilveira\BannerPWA\Api\Data\BannerSearchResultsInterface;

/**
 * Service Data Object with Banner search results.
 */
class BannerSearchResults extends SearchResults implements BannerSearchResultsInterface
{
}
