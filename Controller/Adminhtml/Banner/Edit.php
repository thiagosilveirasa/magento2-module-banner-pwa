<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Banner;

use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Admin Action: bannerpwa/banner/edit
 */
class Edit extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $modelId = (int) $this->getRequest()->getParam('banner_id');
        $model = $this->initModel($modelId);

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $breadcrumbTitle = $model->getBannerId() ?
            (string) __('Edit Banner') : (string) __('New Banner');
         
        $this->initPage($resultPage)
            ->addBreadcrumb((string) __('Banner PWA'), (string) __('Banner PWA'))
            ->addBreadcrumb($breadcrumbTitle, $breadcrumbTitle);

        $title = $model->getBannerId() ?
            (string) __("Edit banner #%1", $model->getBannerId()) : (string) __('New banner');

        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
