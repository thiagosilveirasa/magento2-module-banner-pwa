<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Model\Resolver\DataProvider\Banner as BannerDataProvider;

/**
 * @inheritdoc
 */
class CmsPageBanners implements ResolverInterface
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
        if (!isset($value['page_id'])) {
            throw new LocalizedException(__('"page_id" value should be specified'));
        }

        $pageId = $value['page_id'];

        /** @var \Magento\GraphQl\Model\Query\ContextInterface $context */
        // @phpstan-ignore-next-line
        $customerGroupId = (int) $context->getExtensionAttributes()->getCustomerGroupId();
        // @phpstan-ignore-next-line
        $storeId = (int) $context->getExtensionAttributes()->getStore()->getId();
        $filters = [
            BannerInterface::DISPLAY_ON => [
                'page' => BannerInterface::DISPLAY_ON_PAGE_CMS_PAGE,
                'id' => $pageId,
            ],
        ];
        $bannersResult = $this->bannerDataProvider->getBanners($filters, $customerGroupId, $storeId, []);
        return $bannersResult['ítems'] ?? [];
    }
}
