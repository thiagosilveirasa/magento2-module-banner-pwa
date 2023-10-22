<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Api\Data;

/**
 * @api
 */
interface BannerInterface
{
    /**
     * Name of the main Mysql Table
     */
    public const TABLE_NAME = 'banner_pwa';

    /**
     * Name of the banner-pwa-store association table
     */
    public const STORE_TABLE_NAME = 'banner_pwa_store';

     /**
      * Name of the banner-pwa-customer-group association table
      */
    public const CUSTOMER_GROUP_TABLE_NAME = 'banner_pwa_customer_group';

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ID = 'id';
    public const BANNER_ID = 'banner_id';
    public const NAME = 'name';
    public const TITLE = 'title';
    public const IMAGE = 'image';
    public const URL = 'url';
    public const NEWTAB = 'newtab';
    public const POSITION = 'position';
    public const DISPLAY_ON = 'display_on';
    public const DISPLAY_ON_PAGE = 'display_on_page';
    public const DISPLAY_ON_ID = 'display_on_id';
    public const IS_ACTIVE = 'is_active';
    public const SORT_ORDER = 'sort_order';
    public const STORE_ID = 'store_id';
    public const STORES = 'stores';
    public const CUSTOMER_GROUP_ID = 'customer_group_id';
    public const CUSTOMER_GROUPS = 'customer_groups';
    public const FROM_DATE = 'from_date';
    public const TO_DATE = 'to_date';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**#@+
     * Banner's statuses
     */
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 0;
    /**#@-*/

    /**#@+
     * Banner's display on pages
     */
    public const DISPLAY_ON_PAGE_ALL_PAGES = 'all_pages';
    public const DISPLAY_ON_PAGE_PRODUCT = 'product';
    public const DISPLAY_ON_PAGE_CATEGORY = 'category';
    public const DISPLAY_ON_PAGE_CMS_PAGE = 'page';
    public const DISPLAY_ON_PAGE_CUSTOM = 'custom';
    /**#@-*/

    /**
     * Lists types for display on pages
     */
    public const DISPLAY_ON_PAGE_TYPES = [
      self::DISPLAY_ON_PAGE_ALL_PAGES,
      self::DISPLAY_ON_PAGE_PRODUCT,
      self::DISPLAY_ON_PAGE_CATEGORY,
      self::DISPLAY_ON_PAGE_CMS_PAGE,
      self::DISPLAY_ON_PAGE_CUSTOM,
    ];

    /**
     * Get banner Id
     *
     * @return int
     */
    public function getBannerId(): ?int;

    /**
     * Get ID
     *
     * @return string|int|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Get url
     *
     * @return string|null
     */
    public function getUrl();

    /**
     * Is open in new tab
     *
     * @return bool
     */
    public function isOpenInNewtab();

    /**
     * Get position
     *
     * @return string|null
     */
    public function getPosition();

    /**
     * Get display on
     *
     * @return array|null
     */
    public function getDisplayOn();

    /**
     * Get display on page
     *
     * @return string|null
     */
    public function getDisplayOnPage();

    /**
     * Get display on id
     *
     * @return string|null
     */
    public function getDisplayOnId();

    /**
     * Is active
     *
     * @return bool
     */
    public function isActive();

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Get a list of stores the banner will be shown
     *
     * @return int[]
     */
    public function getStores();

    /**
     * Get ids of customer groups that the banner will be shown
     *
     * @return int[]
     */
    public function getCustomerGroups();

    /**
     * Get from date
     *
     * @return string
     */
    public function getFromDate();

    /**
     * Get to date
     *
     * @return string
     */
    public function getToDate();

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set ID
     *
     * @param mixed $id
     * @return BannerInterface
     */
    public function setId($id): BannerInterface;

    /**
     * Set name
     *
     * @param string $name
     * @return BannerInterface
     */
    public function setName(string $name): BannerInterface;

    /**
     * Set title
     *
     * @param string $title
     * @return BannerInterface
     */
    public function setTitle(string $title): BannerInterface;

    /**
     * Set image
     *
     * @param string $image
     * @return BannerInterface
     */
    public function setImage(string $image): BannerInterface;

    /**
     * Set url
     *
     * @param string $url
     * @return BannerInterface
     */
    public function setUrl(string $url): BannerInterface;

    /**
     * Set newtab
     *
     * @param bool $newtab
     * @return BannerInterface
     */
    public function setNewtab(bool $newtab): BannerInterface;

    /**
     * Set position
     *
     * @param string $position
     * @return BannerInterface
     */
    public function setPosition(string $position): BannerInterface;

    /**
     * Set display on
     *
     * @param array $displayOn
     * @return BannerInterface
     */
    public function setDisplayOn(array $displayOn): BannerInterface;

    /**
     * Set display on page
     *
     * @param string $displayOnPage
     * @return BannerInterface
     */
    public function setDisplayOnPage(string $displayOnPage): BannerInterface;

    /**
     * Set display on id
     *
     * @param string $displayOnId
     * @return BannerInterface
     */
    public function setDisplayOnId(string $displayOnId): BannerInterface;

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return BannerInterface
     */
    public function setIsActive(bool $isActive): BannerInterface;

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return BannerInterface
     */
    public function setSortOrder(int $sortOrder): BannerInterface;
    
    /**
     * Set from date
     *
     * @param string $fromDate
     * @return BannerInterface
     */
    public function setFromDate(string $fromDate): BannerInterface;

    /**
     * Set to date
     *
     * @param string $toDate
     * @return BannerInterface
     */
    public function setToDate(string $toDate): BannerInterface;

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return BannerInterface
     */
    public function setCreatedAt(string $createdAt): BannerInterface;

    /**
     * Set updated at
     *
     * @param string|null $updatedAt
     * @return BannerInterface
     */
    public function setUpdatedAt(?string $updatedAt): BannerInterface;

    /**
     * Get image url
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageUrl(): string;
}
