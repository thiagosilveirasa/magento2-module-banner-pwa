<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Banner;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 *  Admin Action: bannerpwa/banner/massDelete
 */
class MassDelete extends AbstractAction implements HttpPostActionInterface
{
    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @throws NotFoundException
     */
    public function execute(): ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $banner) {
            $this->modelRepository->deleteById((int) $banner->getId());
        }

        $this->messageManager->addSuccessMessage(
            (string) __('A total of %1 banner(s) have been deleted.', $collectionSize)
        );

        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
