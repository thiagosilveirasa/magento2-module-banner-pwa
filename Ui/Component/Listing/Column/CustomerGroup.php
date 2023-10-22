<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Listing\Column;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class CustomerGroup for Ui component
 */
class CustomerGroup extends Column
{
    /**
     * @var CollectionFactory
     */
    private $customerGroupCollectionFactory;

    /**
     * @var string
     */
    protected $customerGroupKey;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CollectionFactory $customerGroupCollectionFactory
     * @param array $components
     * @param array $data
     * @param string $customerGroupKey
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CollectionFactory $customerGroupCollectionFactory,
        array $components = [],
        array $data = [],
        string $customerGroupKey = 'customer_group_id'
    ) {
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->customerGroupKey = $customerGroupKey;
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
        $content = '';
        if (!empty($item[$this->customerGroupKey])) {
            $origCustomerGroup = $item[$this->customerGroupKey];
        }

        if (empty($origCustomerGroup)) {
            return '';
        }
        if (!is_array($origCustomerGroup)) {
            $origCustomerGroup = [$origCustomerGroup];
        }

        $data = $this->customerGroupCollectionFactory->create()
            ->addFieldToFilter('customer_group_id', ['in' => $origCustomerGroup])
            ->getItems();

        foreach ($data as $customerGroup) {
            $content .= $customerGroup->getCustomerGroupCode() . "<br/>";
        }

        return $content;
    }
}
