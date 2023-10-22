<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ThiagoSilveira\BannerPWA\Model\Resolver\DataProvider\Banner as BannerDataProvider;

/**
 * Banners field resolver, used for GraphQL request processing
 */
class Banners implements ResolverInterface
{
    /**
     * @var BannerDataProvider
     */
    private $bannerDataProvider;

    /**
     * @param BannerDataProvider $bannerDataProvider
     */
    public function __construct(
        BannerDataProvider $bannerDataProvider
    ) {
        $this->bannerDataProvider = $bannerDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        /** @var \Magento\GraphQl\Model\Query\ContextInterface $context */
        // @phpstan-ignore-next-line
        $customerGroupId = (int) $context->getExtensionAttributes()->getCustomerGroupId();
        // @phpstan-ignore-next-line
        $storeId = (int) $context->getExtensionAttributes()->getStore()->getId();
        $filters = $this->getBannerFilters($args);
        return $this->bannerDataProvider
            ->getBanners($filters, $customerGroupId, $storeId, $args);
    }

    /**
     * Get banner filters
     *
     * @param array $args
     * @return array
     */
    private function getBannerFilters(array $args): array
    {
        return isset($args['filters']) && is_array($args['filters'])
            ? $args['filters']
            : [];
    }
}
