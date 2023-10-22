<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Banner\Source;

/**
 * Is active filter source
 */
class IsActiveFilter extends IsActive
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return array_merge([['label' => '', 'value' => '']], parent::toOptionArray());
    }
}
