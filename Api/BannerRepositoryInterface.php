<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Banner CRUD interface.
 *
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * Save banner.
     *
     * @param \ThiagoSilveira\BannerPWA\Api\Data\BannerInterface $banner
     * @return \ThiagoSilveira\BannerPWA\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\BannerInterface $banner): Data\BannerInterface;

    /**
     * Retrieve banner.
     *
     * @param string|int $bannerId
     * @return \ThiagoSilveira\BannerPWA\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(string|int $bannerId): Data\BannerInterface;

    // phpcs:disable Generic.Files.LineLength
    /**
     * Retrieve banners matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \ThiagoSilveira\BannerPWA\Api\Data\BannerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\BannerSearchResultsInterface;
    // phpcs:enable Generic.Files.LineLength

    /**
     * Delete banner.
     *
     * @param \ThiagoSilveira\BannerPWA\Api\Data\BannerInterface $banner
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\BannerInterface $banner): bool;

    /**
     * Delete banner by ID.
     *
     * @param string|int $bannerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(string|int $bannerId): bool;
}
