<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Form\UrlInput;

use Magento\Framework\UrlInterface;
use Magento\Ui\Model\UrlInput\ConfigInterface;

/**
 * Returns configuration for product Input type
 */
class Product implements ConfigInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * Product constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'label' => __('Product'),
            'component' => 'ThiagoSilveira_BannerPWA/js/components/product-ui-select',
            'disableLabel' => true,
            'filterOptions' => true,
            'searchOptions' => true,
            'chipsEnabled' => false,
            'levelsVisibility' => '1',
            'options' => [],
            'sortOrder' => 25,
            'multiple' => true,
            'closeBtn' => true,
            'template' => 'ui/grid/filters/elements/ui-select',
            'searchUrl' => $this->urlBuilder->getUrl('catalog/product/search'),
            'filterPlaceholder' => __('Product Name or SKU'),
            'filterRateLimitMethod' => 'notifyWhenChangesStop',
            'isDisplayEmptyPlaceholder' => true,
            'emptyOptionsHtml' => __('Start typing to find products'),
            'missingValuePlaceholder' => __('Product with ID: %s doesn\'t exist'),
            'isDisplayMissingValuePlaceholder' => true,
            'isRemoveSelectedIcon' => true,
            'validationUrl' => $this->urlBuilder->getUrl('bannerpwa/product/getSelected'),
        ];
    }
}
