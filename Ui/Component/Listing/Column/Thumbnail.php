<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use ThiagoSilveira\BannerPWA\Helper\Banner;

/**
 * Class Thumbnail for Ui component
 */
class Thumbnail extends Column
{
    public const NAME = 'thumbnail';

    public const ALT_FIELD = 'title';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Banner $bannerHelper
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected Banner $bannerHelper,
        protected UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_src'] = $this->bannerHelper->getBaseUrl() . '/' . $item['image'];
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: $item['image'];
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'bannerpwa/banner/edit',
                    ['banner_id' => $item['banner_id']]
                );
                $item[$fieldName . '_orig_src'] = $this->bannerHelper->getBaseUrl() . '/' . $item['image'];
            }
        }

        return $dataSource;
    }

    /**
     * Get Alt
     *
     * @param array $row
     * @return string|null
     */
    protected function getAlt(array $row): ?string
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return $row[$altField] ?? null;
    }
}
