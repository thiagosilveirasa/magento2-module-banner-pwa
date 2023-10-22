<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Listing\Column\CustomerGroup;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Escaper;

/**
 * Customer Group Options for Banners
 *
 * @api
 */
class Options implements OptionSourceInterface
{
    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var CollectionFactory
     */
    private $customerGroupCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $currentOptions = [];

    /**
     * Constructor
     *
     * @param CollectionFactory $customerGroupCollectionFactory
     * @param Escaper $escaper
     */
    public function __construct(
        CollectionFactory $customerGroupCollectionFactory,
        Escaper $escaper
    ) {
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
        $this->escaper = $escaper;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->currentOptions[] = [
            'label' => __('Select...'),
            'value' => '',
        ];

        $this->generateCurrentOptions();

        $this->options = array_values($this->currentOptions);

        return $this->options;
    }

    /**
     * Sanitize website/store option name
     *
     * @param string $name
     * @return string
     */
    protected function sanitizeName(string $name): string
    {
        $matches = [];
        preg_match('/\$[:]*{(.)*}/', $name ?: '', $matches);
        if (count($matches) > 0) {
            $name = $this->escaper->escapeHtml($this->escaper->escapeJs($name));
        } else {
            $name = $this->escaper->escapeHtml($name);
        }

        return $name;
    }

    /**
     * Generate current options
     *
     * @return void
     */
    protected function generateCurrentOptions(): void
    {
        $customerGroups = $this->customerGroupCollectionFactory->create()
            ->getItems();

        foreach ($customerGroups as $customerGroup) {
            $this->currentOptions[] = [
                'label' => $this->sanitizeName($customerGroup->getCustomerGroupCode()),
                'value' => $customerGroup->getId(),
            ];
        }
    }
}
