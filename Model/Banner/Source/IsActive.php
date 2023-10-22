<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Banner\Source;

use Magento\Framework\Data\OptionSourceInterface;
use ThiagoSilveira\BannerPWA\Model\Banner;

/**
 * Is Active options source
 */
class IsActive implements OptionSourceInterface
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
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $availableOptions = $this->banner->getAvailableStatuses();
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
