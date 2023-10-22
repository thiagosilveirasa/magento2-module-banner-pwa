<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Block\Adminhtml\Banner\Edit;

use Magento\Backend\Block\Widget\Context;
use Psr\Log\LoggerInterface;
use ThiagoSilveira\BannerPWA\Api\BannerRepositoryInterface;

/**
 * Adminhtml block: Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var BannerRepositoryInterface
     */
    protected $bannerRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param BannerRepositoryInterface $bannerRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        LoggerInterface $logger
    ) {
        $this->context = $context;
        $this->bannerRepository = $bannerRepository;
        $this->logger = $logger;
    }

    /**
     * Return Banner ID
     *
     * @return int|null
     */
    public function getBannerId(): ?int
    {
        $bannerId = $this->context->getRequest()->getParam('banner_id');
        if ($bannerId) {
            return $this->bannerRepository->getById(
                $this->context->getRequest()->getParam('banner_id')
            )->getBannerId();
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
