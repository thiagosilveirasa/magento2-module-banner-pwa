<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use ThiagoSilveira\BannerPWA\Api\BannerRepositoryInterface as BannerRepository;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface as Banner;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterfaceFactory as BannerFactory;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Abstract Admin action for banner
 */
abstract class AbstractAction extends Action
{
    /**
     * Authorization level.
     */
    public const ADMIN_RESOURCE = 'ThiagoSilveira_Banner::banner';

    /**
     * AbstractAction constructor.
     *
     * @param Context $context UI Component context
     * @param Registry $coreRegistry Core Registry
     * @param BannerFactory $modelFactory Banner Factory
     * @param BannerRepository $modelRepository Banner Repository
     * @param Filter $filter Action Filter
     * @param CollectionFactory $collectionFactory Banner Collection Factory
     */
    public function __construct(
        Context $context,
        protected Registry $coreRegistry,
        protected BannerFactory $modelFactory,
        protected BannerRepository $modelRepository,
        protected Filter $filter,
        protected CollectionFactory $collectionFactory
    ) {

        parent::__construct($context);
    }

    /**
     * Init the current model.
     *
     * @param string|int|null $bannerId Banner ID
     * @throws NotFoundException
     */
    protected function initModel(string|int|null $bannerId): Banner
    {
        /** @var \ThiagoSilveira\BannerPWA\Model\Banner $model */
        $model = $this->modelFactory->create();

        // Initial checking.
        if ($bannerId) {
            try {
                $model = $this->modelRepository->getById($bannerId);
            } catch (NoSuchEntityException $e) {
                throw new NotFoundException(__('This banner does not exist.'));
            }
        }

        // Register model to use later in blocks.
        $this->coreRegistry->register('banner_pwa', $model);

        return $model;
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage(
        Page $resultPage
    ): Page {
        $resultPage->setActiveMenu('ThiagoSilveira_BannerPWA::banner_pwa')
            ->addBreadcrumb(__('ThiagoSilveira'), __('ThiagoSilveira'))
            ->addBreadcrumb(__('Banners'), __('Banners'));
        return $resultPage;
    }
}
