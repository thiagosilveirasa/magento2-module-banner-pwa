<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Banner\Image;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use ThiagoSilveira\BannerPWA\Model\ImageUploader;

/**
 * Admin Action: bannerpwa/banner_image/upload
 */
class Upload extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level.
     */
    public const ADMIN_RESOURCE = 'ThiagoSilveira_Banner::banner';

    /**
     * Class Upload for image uploader
     *
     * @var \ThiagoSilveira\BannerPWA\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Upload constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \ThiagoSilveira\BannerPWA\Model\ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('ThiagoSilveira_BannerPWA::banner');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            // @phpstan-ignore-next-line
            $imageFile = $this->getRequest()->getFiles($imageId);
            if (empty($imageFile)) {
                throw new NotFoundException(__('No file for upload.'));
            }

            $imageName = $imageFile['name'];
            $newImageName = null;

            if ($this->imageUploader->checkFileNameExistInBasePath($imageName)) {
                $newImageName = $this->imageUploader->getNewFileNameForBasePath($imageName);
            }
            $result = $this->imageUploader->saveFileToTmpDir($imageId, $newImageName);
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
}
