<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/** Returns information about selected product by product id. Returns empty array if product don't exist */
class GetSelected extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level.
     */
    public const ADMIN_RESOURCE = 'ThiagoSilveira_Banner::banner';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Search constructor.
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        JsonFactory $jsonFactory,
        CollectionFactory $productCollectionFactory,
        Context $context
    ) {
        $this->resultJsonFactory = $jsonFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Get selected products controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): ResultInterface
    {
        $productIds = $this->getRequest()->getParam('productIds');
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect(ProductInterface::NAME);
        $productCollection->addIdFilter($productIds);

        $options = [];
        /** @var ProductInterface $product */
        foreach ($productCollection->getItems() as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => $product->getName(),
                'is_active' => $product->getStatus(),
                'path' => $product->getSku(),
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($options);
    }
}
