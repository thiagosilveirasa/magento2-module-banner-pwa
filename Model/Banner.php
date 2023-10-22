<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\BannerImageUpload;
use ThiagoSilveira\BannerPWA\Model\Banner\Image\FileInfo;
use ThiagoSilveira\BannerPWA\Model\ImageUploader;

/**
 * Banner Model Implementation
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Banner extends AbstractModel implements BannerInterface, IdentityInterface
{
    /**
     * @var FileInfo
     */
    protected FileInfo $fileInfo;

    /**
     * @var WriteInterface
     */
    protected WriteInterface $mediaDirectory;

    /**
     * Banner cache tag
     */
    public const CACHE_TAG = 'banner_pwa';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'banner_pwa';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager Store Manager
     * @param Filesystem $filesystem FileSystem Helper
     * @param Json $json
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param ImageUploader $imageUploader Image uploader
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        protected StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        private Json $json,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        private ?ImageUploader $imageUploader = null,
        array $data = []
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner::class);
    }

    /**
     * After save
     *
     * @return $this
     */
    public function afterSave()
    {
        $imageName = $this->getData(self::IMAGE);
        $path = $this->getImageUploader()->getFilePath($this->imageUploader->getBaseTmpPath(), $imageName);

        if ($this->mediaDirectory->isExist($path)) {
            $this->getImageUploader()->moveFileFromTmp($imageName);
        }

        return parent::afterSave();
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities(): array
    {
        return [
            self::CACHE_TAG . '_' . $this->getId(),
            self::CACHE_TAG . '_' . $this->getDisplayOnPage(),
            self::CACHE_TAG . '_' . $this->getPosition(),
        ];
    }

    /**
     * Get banner id.
     */
    public function getBannerId(): ?int
    {
        return (int) $this->getId();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Get url
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->getData(self::URL);
    }

    /**
     * Is open in new tab
     *
     * @return bool
     */
    public function isOpenInNewtab(): bool
    {
        return (bool) $this->getData(self::NEWTAB);
    }

    /**
     * Get position
     *
     * @return string|null
     */
    public function getPosition(): ?string
    {
        return $this->getData(self::POSITION);
    }

    /**
     * Get display on
     *
     * @return array|null
     */
    public function getDisplayOn(): ?array
    {
        $displayOn = $this->getData(self::DISPLAY_ON);
        return $displayOn ? $this->json->unserialize($displayOn) : null;
    }

    /**
     * Get display on page
     *
     * @return string|null
     */
    public function getDisplayOnPage(): ?string
    {
        return $this->getData(self::DISPLAY_ON_PAGE);
    }

    /**
     * Get display on id
     *
     * @return string|null
     */
    public function getDisplayOnId(): ?string
    {
        return $this->getData(self::DISPLAY_ON_ID);
    }

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder(): ?int
    {
        return (int) $this->getData(self::SORT_ORDER);
    }
    
    /**
     * Get a list of stores the banner will be shown
     *
     * @return int[]
     */
    public function getStores(): array
    {
        return $this->hasData(self::STORES)
            ? $this->getData(self::STORES)
            : $this->getData(self::STORE_ID);
    }

    /**
     * Get ids of customer groups that the banner will be shown
     *
     * @return int[]
     */
    public function getCustomerGroups(): array
    {
        return $this->hasData(self::CUSTOMER_GROUPS)
            ? $this->getData(self::CUSTOMER_GROUPS)
            : $this->getData(self::CUSTOMER_GROUP_ID);
    }

    /**
     * Get from date
     *
     * @return string
     */
    public function getFromDate(): string
    {
        return (string) $this->getData(self::FROM_DATE);
    }

    /**
     * Get to date
     *
     * @return string
     */
    public function getToDate(): string
    {
        return (string) $this->getData(self::TO_DATE);
    }

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set ID
     *
     * @param mixed $id
     * @return BannerInterface
     */
    public function setId($id): BannerInterface
    {
        return $this->setData(self::BANNER_ID, $id);
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BannerInterface
     */
    public function setName(string $name): BannerInterface
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return BannerInterface
     */
    public function setTitle(string $title): BannerInterface
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set image
     *
     * @param string $image
     * @return BannerInterface
     */
    public function setImage(string $image): BannerInterface
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set url
     *
     * @param string $url
     * @return BannerInterface
     */
    public function setUrl(string $url): BannerInterface
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * Set newtab
     *
     * @param bool $newtab
     * @return BannerInterface
     */
    public function setNewtab(bool $newtab): BannerInterface
    {
        return $this->setData(self::NEWTAB, (bool) $newtab);
    }

    /**
     * Set position
     *
     * @param string $position
     * @return BannerInterface
     */
    public function setPosition(string $position): BannerInterface
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * Set display on
     *
     * @param array $displayOn
     * @return BannerInterface
     */
    public function setDisplayOn(array $displayOn): BannerInterface
    {
        return $this->setData(self::DISPLAY_ON, $this->json->serialize($displayOn));
    }

    /**
     * Set display on page
     *
     * @param string $displayOnPage
     * @return BannerInterface
     */
    public function setDisplayOnPage(string $displayOnPage): BannerInterface
    {
        return $this->setData(self::DISPLAY_ON_PAGE, $displayOnPage);
    }

    /**
     * Set display on id
     *
     * @param string $displayOnId
     * @return BannerInterface
     */
    public function setDisplayOnId(string $displayOnId): BannerInterface
    {
        return $this->setData(self::DISPLAY_ON_ID, $displayOnId);
    }

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return BannerInterface
     */
    public function setIsActive(bool $isActive): BannerInterface
    {
        return $this->setData(self::IS_ACTIVE, (bool) $isActive);
    }

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return BannerInterface
     */
    public function setSortOrder(int $sortOrder): BannerInterface
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set from date
     *
     * @param string $fromDate
     * @return BannerInterface
     */
    public function setFromDate(string $fromDate): BannerInterface
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    /**
     * Set to date
     *
     * @param string $toDate
     * @return BannerInterface
     */
    public function setToDate(string $toDate): BannerInterface
    {
        return $this->setData(self::TO_DATE, $toDate);
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return BannerInterface
     */
    public function setCreatedAt(string $createdAt): BannerInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set updated at
     *
     * @param string|null $updatedAt
     * @return BannerInterface
     */
    public function setUpdatedAt(?string $updatedAt): BannerInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Populate from array
     *
     * @param array $values Form values
     */
    public function populateFromArray(array $values): void
    {
        $displayOn = null;
        $displayOnPage = null;
        $displayOnId = null;
        
        if (isset($values['display_on'])) {
            $displayOn = $values['display_on'];
            $displayOnPage = $displayOn['type'] ?? null;
            $displayOnValues = $displayOn[$displayOnPage];
            foreach (self::DISPLAY_ON_PAGE_TYPES as $type) {
                unset($displayOn[$type]);
            }
            $displayOn[$displayOnPage] = $displayOnValues;
            $displayOnId = isset($displayOn[$displayOnPage])
                ? (
                    is_array($displayOn[$displayOnPage])
                        ? implode(',', array_values($displayOn[$displayOnPage]))
                        : $displayOn[$displayOnPage]
                )
                : null;
        }

        if (!isset($values['store_id'])) {
            $storeId = $this->storeManager->getStore()->getId();
            $values['store_id'] = [$storeId];
        }
        
        $this->setData(self::NAME, (string) $values['name']);
        $this->setData(self::TITLE, (string) $values['title']);
        $this->setData(self::IMAGE, $values['image'][0]['name']);
        $this->setData(self::URL, (string) $values['url']);
        $this->setData(self::NEWTAB, (bool) $values['newtab']);
        $this->setData(self::POSITION, (string) $values['position']);
        $this->setData(self::DISPLAY_ON, (string) $this->json->serialize($displayOn));
        $this->setData(self::DISPLAY_ON_PAGE, $displayOnPage);
        $this->setData(self::DISPLAY_ON_ID, $displayOnId);
        $this->setData(self::IS_ACTIVE, (bool) $values['is_active']);
        $this->setData(self::SORT_ORDER, (int) $values['sort_order']);
        $this->setData(self::FROM_DATE, ($values['from_date'] ? (string) $values['from_date'] : null));
        $this->setData(self::TO_DATE, ($values['to_date'] ? (string) $values['to_date'] : null));
        $this->setData(self::STORE_ID, $values['store_id']);
        $this->setData(self::STORES, $values['store_id']);
        $this->setData(self::CUSTOMER_GROUP_ID, $values['customer_group_id']);
        $this->setData(self::CUSTOMER_GROUPS, $values['customer_group_id']);
    }

    /**
     * Get image url
     *
     * @return string
     * @throws LocalizedException
     */
    public function getImageUrl(): string
    {
        $url = '';
        $image = $this->getData(self::IMAGE);
        if ($image) {
            if (is_string($image)) {
                /** @var Store $store */
                $store = $this->storeManager->getStore();

                $isRelativeUrl = substr($image, 0, 1) === '/';

                $mediaBaseUrl = $store->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                );

                $url = $mediaBaseUrl
                    . ltrim(FileInfo::ENTITY_MEDIA_PATH, '/')
                    . '/'
                    . $image;

                if ($isRelativeUrl) {
                    $url = $image;
                }
            }
        }

        return $url;
    }

    /**
     * Prepare banner's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses(): array
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Prepare banner's display on pages available.
     *
     * @return array
     */
    public function getDisplayOnPagesAvailable(): array
    {
        return [
            self::DISPLAY_ON_PAGE_ALL_PAGES => __('All Pages'),
            self::DISPLAY_ON_PAGE_PRODUCT => __('Products'),
            self::DISPLAY_ON_PAGE_CATEGORY => __('Categories'),
            self::DISPLAY_ON_PAGE_CMS_PAGE => __('CMS Page'),
            self::DISPLAY_ON_PAGE_CUSTOM => __('Custom'),
        ];
    }

    /**
     * Get image uploader
     */
    private function getImageUploader(): ImageUploader
    {
        if ($this->imageUploader === null) {
            $this->imageUploader = ObjectManager::getInstance()->get(
                BannerImageUpload::class // @phpstan-ignore-line
            );
        }

        return $this->imageUploader;
    }
}
