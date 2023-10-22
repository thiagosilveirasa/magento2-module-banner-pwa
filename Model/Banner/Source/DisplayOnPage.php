<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Banner\Source;

use Magento\Framework\Data\OptionSourceInterface;
use ThiagoSilveira\BannerPWA\Model\Banner;

/**
 * Display On Page option source
 */
class DisplayOnPage implements OptionSourceInterface
{
    /**
     * @var \ThiagoSilveira\BannerPWA\Model\Banner
     */
    protected $banner;

    /**
     * Constructor
     *
     * @param \ThiagoSilveira\BannerPWA\Model\Banner $banner
     */
    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $availableOptions = $this->banner->getDisplayOnPagesAvailable();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
