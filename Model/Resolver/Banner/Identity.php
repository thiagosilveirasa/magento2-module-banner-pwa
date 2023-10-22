<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Resolver\Banner;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Model\Banner;

/**
 * Identity for resolved Banner
 */
class Identity implements IdentityInterface
{
    /** @var string */
    private $cacheTag = Banner::CACHE_TAG;

    /**
     * Get banner identities from resolved data
     *
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        $identities = [$this->cacheTag];
        $items = $resolvedData['items'] ?? $resolvedData;
        foreach ($items as $item) {
            if (is_array($item) && !empty($item[BannerInterface::ID])) {
                $identities[] = sprintf('%s_%s', $this->cacheTag, $item[BannerInterface::ID]);
                $identities[] = sprintf('%s_%s', $this->cacheTag, $item[BannerInterface::DISPLAY_ON_PAGE]);
                $identities[] = sprintf('%s_%s', $this->cacheTag, $item[BannerInterface::POSITION]);
            }
        }
        return $identities;
    }
}
