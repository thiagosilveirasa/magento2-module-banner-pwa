<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Listing\Column;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use ThiagoSilveira\BannerPWA\Model\Banner;

/**
 * Class DisplayOnId for Ui component
 */
class DisplayOnId extends Column
{
    /**
     * @var PageCollectionFactory
     */
    private $pageCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var string
     */
    protected $customerGroupKey;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PageCollectionFactory $pageCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PageCollectionFactory $pageCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item): string
    {
        if (empty($item['display_on_page']) || empty($item['display_on_id'])) {
            return '';
        }

        $content = '';
        
        if ($item['display_on_page'] == Banner::DISPLAY_ON_PAGE_CMS_PAGE) {
            $data = $this->pageCollectionFactory->create()
                ->addFieldToFilter('page_id', ['in' => $item['display_on_id']])
                ->getItems();

            foreach ($data as $page) {
                $content .= "{$page->getTitle()} (Id: {$page->getId()})<br/>";
            }
        } elseif ($item['display_on_page'] == Banner::DISPLAY_ON_PAGE_CATEGORY) {
            $data = $this->categoryCollectionFactory->create()
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', ['in' => $item['display_on_id']])
                ->getItems();

            foreach ($data as $category) {
                $content .= "{$category->getName()} (Id: {$category->getId()})<br/>";
            }
        } elseif ($item['display_on_page'] == Banner::DISPLAY_ON_PAGE_PRODUCT) {
            $data = $this->productCollectionFactory->create()
                ->addAttributeToSelect('name')
                ->addFieldToFilter('entity_id', ['in' => $item['display_on_id']])
                ->getItems();

            foreach ($data as $product) {
                $content .= "{$product->getName()} (Id: {$product->getId()})<br/>";
            }
        } else {
            $content .= $item['display_on_id'];
        }
        
        return $content;
    }
}
