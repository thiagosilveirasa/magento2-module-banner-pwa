<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Resolver;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Model\Resolver\DataProvider\Banner as BannerDataProvider;

/**
 * @inheritdoc
 */
class ProductBanners implements ResolverInterface
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
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var Product $product */
        $product = $value['model'];

        /** @var \Magento\GraphQl\Model\Query\ContextInterface $context */
        // @phpstan-ignore-next-line
        $customerGroupId = (int) $context->getExtensionAttributes()->getCustomerGroupId();
        // @phpstan-ignore-next-line
        $storeId = (int) $context->getExtensionAttributes()->getStore()->getId();
        $filters = [
            BannerInterface::DISPLAY_ON => [
                'page' => BannerInterface::DISPLAY_ON_PAGE_PRODUCT,
                'id' => $product->getId(),
            ],
        ];
        $bannersResult = $this->bannerDataProvider->getBanners($filters, $customerGroupId, $storeId, []);
        return $bannersResult['ítems'] ?? [];
    }
}