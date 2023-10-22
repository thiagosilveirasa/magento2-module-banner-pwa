<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Form\UrlInput;

use Magento\Ui\Model\UrlInput\ConfigInterface;

/**
 * Returns configuration for custom Input type
 */
class Custom implements ConfigInterface
{
    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'label' => __('Custom'),
            'component' => 'Magento_Ui/js/form/element/abstract',
            'template' => 'ui/form/element/input',
            'sortOrder' => 1000,
        ];
    }
}
