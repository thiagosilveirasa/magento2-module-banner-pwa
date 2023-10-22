<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Listing\Column\Store;

/**
 * Store Options for Banners
 */
class Options extends \Magento\Store\Ui\Component\Listing\Column\Store\Options
{
    /**
     * All Store Views value
     */
    public const ALL_STORE_VIEWS = '0';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions['All Store Views']['label'] = __('All Store Views');
        $this->currentOptions['All Store Views']['value'] = self::ALL_STORE_VIEWS;

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
