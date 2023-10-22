<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Form\UrlInput;

use Magento\Catalog\Ui\Component\Product\Form\Categories\Options;
use Magento\Ui\Model\UrlInput\ConfigInterface;

/**
 * Returns configuration for category Input type
 */
class Category implements ConfigInterface
{
    /**
     * @var \Magento\Catalog\Ui\Component\Product\Form\Categories\Options
     */
    private $options;

    /**
     * @param \Magento\Catalog\Ui\Component\Product\Form\Categories\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'label' => __('Category'),
            'component' => 'Magento_Ui/js/form/element/ui-select',
            'template' => 'ui/grid/filters/elements/ui-select',
            'formElement' => 'select',
            'disableLabel' => true,
            'multiple' => true,
            'chipsEnabled' => false,
            'filterOptions' => true,
            'levelsVisibility' => '1',
            'options' => $this->options->toOptionArray(),
            'sortOrder' => 30,
            'missingValuePlaceholder' => __('Category with ID: %s doesn\'t exist'),
            'isDisplayMissingValuePlaceholder' => true,
            'isRemoveSelectedIcon' => true,
        ];
    }
}
