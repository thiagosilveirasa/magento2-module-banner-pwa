<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Form\UrlInput;

use Magento\PageBuilder\Ui\Component\UrlInput\Page\Options;
use Magento\Ui\Model\UrlInput\ConfigInterface;

/**
 * Returns configuration for CMS Page Input type
 */
class Page implements ConfigInterface
{
    /**
     * @var \Magento\PageBuilder\Ui\Component\UrlInput\Page\Options
     */
    private $options;

    /**
     * @param \Magento\PageBuilder\Ui\Component\UrlInput\Page\Options $options
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
            'label' => __('CMS Page'),
            'component' => 'Magento_PageBuilder/js/form/element/page-ui-select',
            'template' => 'ui/grid/filters/elements/ui-select',
            'disableLabel' => true,
            'filterOptions' => true,
            'chipsEnabled' => false,
            'levelsVisibility' => '1',
            'sortOrder' => 45,
            'multiple' => true,
            'closeBtn' => true,
            'options' => $this->options->toOptionArray(),
            'filterPlaceholder' => __('Page Name'),
            'missingValuePlaceholder' => __('Page with ID: %s doesn\'t exist'),
            'isDisplayMissingValuePlaceholder' => true,
            'isRemoveSelectedIcon' => true,
        ];
    }
}
