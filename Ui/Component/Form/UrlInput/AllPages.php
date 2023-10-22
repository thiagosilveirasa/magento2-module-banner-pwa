<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Form\UrlInput;

use Magento\Ui\Model\UrlInput\ConfigInterface;

/**
 * Returns configuration for all pages Input type
 */
class AllPages implements ConfigInterface
{
    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'label' => __('All pages'),
            'component' => 'Magento_Ui/js/form/element/abstract',
            'template' => '',
            'sortOrder' => 0,
        ];
    }
}
