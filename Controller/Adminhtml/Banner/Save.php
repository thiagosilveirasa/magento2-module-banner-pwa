<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Banner;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use ThiagoSilveira\BannerPWA\Api\BannerRepositoryInterface as BannerRepository;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterfaceFactory as BannerFactory;
use ThiagoSilveira\BannerPWA\Model\Banner;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Admin Action: bannerpwa/banner/save
 */
class Save extends AbstractAction implements HttpPostActionInterface
{
    /**
     * Save constructor.
     *
     * @param Context $context UI Component context
     * @param Registry $coreRegistry Core Registry
     * @param BannerFactory $modelFactory Banner Factory
     * @param BannerRepository $modelRepository Banner Repository
     * @param DataPersistorInterface $dataPersistor Data Persistor
     * @param Filter $filter Action Filter
     * @param CollectionFactory $collectionFactory Banner Collection Factory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        BannerFactory $modelFactory,
        BannerRepository $modelRepository,
        protected DataPersistorInterface $dataPersistor,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $coreRegistry, $modelFactory, $modelRepository, $filter, $collectionFactory);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();
        $data = $request->getPostValue();
        if (empty($data)) {
            return $resultRedirect;
        }

        // Get the banner id (if edit).
        $bannerId = null;
        if (!empty($data['banner_id'])) {
            $bannerId = (int) $data['banner_id'];
        }

        // Load the banner.
        $model = $this->initModel($bannerId);

        // By default, redirect to the edit page of the banner.
        $resultRedirect->setPath('*/*/edit', ['banner_id' => $bannerId]);

        /** @var Banner $model */
        $model->populateFromArray($data);

        // Try to save it.
        try {
            $this->modelRepository->save($model);
            if ($bannerId === null) {
                $resultRedirect->setPath('*/*/edit', ['banner_id' => $model->getBannerId()]);
            }

            // Display success message.
            $this->messageManager->addSuccessMessage((string) __('The banner has been saved.'));
            $this->dataPersistor->clear('banner_pwa');

            // If requested => redirect to the list.
            if ($this->getRequest()->getParam('back') == 'close') {
                $resultRedirect->setPath('*/*/');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('banner_pwa', $data);
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                (string) __('Something went wrong while saving the banner. "%1"', $e->getMessage())
            );
            $this->dataPersistor->set('banner_pwa', $data);
        }

        return $resultRedirect;
    }
}
