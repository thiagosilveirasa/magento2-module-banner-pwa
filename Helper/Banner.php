<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\UrlInterface;

/**
 * Banner Helper
 */
class Banner extends AbstractHelper
{
    /**
     * Banners media directory path.
     *
     * @var string
     */
    protected $subDir = '/bannerpwa';

    /**
     * Image constructor.
     *
     * @param UrlInterface $urlBuilder URL Builder
     */
    public function __construct(
        protected UrlInterface $urlBuilder
    ) {
    }

    /**
     * Get images base url
     */
    public function getBaseUrl(): string
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $this->subDir;
    }
}
