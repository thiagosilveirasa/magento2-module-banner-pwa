<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Banner;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Admin Action: bannerpwa/banner/index
 */
class Index extends AbstractAction implements HttpGetActionInterface
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Banners'));

        return $resultPage;
    }
}
