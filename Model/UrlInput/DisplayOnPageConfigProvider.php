<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model\UrlInput;

use Magento\Framework\ObjectManagerInterface;
use Magento\Ui\Model\UrlInput\ConfigInterface;

/**
 * Returns information about allowed display on page
 */
class DisplayOnPageConfigProvider implements ConfigInterface
{
    /**
     * @var array
     */
    private $displayOnPageConfiguration;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * LinksProvider constructor.
     *
     * @param array $displayOnPageConfiguration
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        array $displayOnPageConfiguration,
        ObjectManagerInterface $objectManager
    ) {
        $this->displayOnPageConfiguration = $displayOnPageConfiguration;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        $config = [];
        foreach ($this->displayOnPageConfiguration as $displayOnPageName => $className) {
            $config[$displayOnPageName] = $this->createConfigProvider($className)->getConfig();
        }
        return $config;
    }

    /**
     * Create config provider
     *
     * @param string $instance
     * @return ConfigInterface
     */
    private function createConfigProvider(string $instance): ConfigInterface
    {
        return $this->objectManager->create($instance);
    }
}
