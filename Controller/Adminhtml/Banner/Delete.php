<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Banner;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;

/**
 * Admin Action: bannerpwa/banner/delete
 */
class Delete extends AbstractAction implements HttpPostActionInterface
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
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');

        try {
            $bannerId = (int) $this->getRequest()->getParam('banner_id');
            $this->modelRepository->deleteById($bannerId);

            $this->messageManager->addSuccessMessage(
                (string) __('The banner "%1" has been deleted.', $bannerId)
            );
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage((string) __('The banner to delete does not exist.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect;
    }
}
