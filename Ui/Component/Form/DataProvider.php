<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Ui\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use ThiagoSilveira\BannerPWA\Model\Banner;
use ThiagoSilveira\BannerPWA\Model\Banner\Image\FileInfo;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Class Form DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * DataProvider constructor.
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param string $name Component Name
     * @param string $primaryFieldName Primary Field Name
     * @param string $requestFieldName  Request Field Name
     * @param CollectionFactory $collectionFactory Collection Factory
     * @param RequestInterface $request HTTP Request
     * @param FileInfo $fileInfo File Info helper
     * @param PoolInterface $modifierPool Modifier Pool
     * @param array $meta Component Meta
     * @param array $data Component Data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        protected RequestInterface $request,
        protected FileInfo $fileInfo,
        private PoolInterface $modifierPool,
        array $meta = [],
        array $data = []
    ) {
        $this->collection    = $collectionFactory->create();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $requestId = $this->request->getParam($this->requestFieldName);
        /** @var Banner $banner */
        $banner = $this->collection->addFieldToFilter($this->requestFieldName, $requestId)->getFirstItem();

        if ($banner->getId()) {
            $data = $this->convertValues($banner, $banner->getData());
            $this->data[$banner->getId()] = $data;
        }

        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $this->meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifierPool->getModifiersInstances() as $modifier) {
            $this->meta = $modifier->modifyMeta($this->meta);
        }

        return $this->meta;
    }

    /**
     * Converts category image data to acceptable for rendering format
     *
     * @param Banner $banner Banner
     * @param array $data Banner Data
     * @return array
     */
    private function convertValues(Banner $banner, array $data): array
    {
        $fileName = $banner->getData('image');

        if ($fileName && $this->fileInfo->isExist($fileName)) {
            $stat = $this->fileInfo->getStat($fileName);
            $mime = $this->fileInfo->getMimeType($fileName);

            unset($data['image']);
            $data['image'][0]['name'] = basename($fileName);

            $data['image'][0]['url'] = $banner->getImageUrl();
            if ($this->fileInfo->isBeginsWithMediaDirectoryPath($fileName)) {
                $data['image'][0]['url'] = $fileName;
            }

            $data['image'][0]['size'] = $stat['size'] ?? 0;
            $data['image'][0]['type'] = $mime;
        }

        $data['display_on'] = $banner->getDisplayOn();

        return $data;
    }
}
