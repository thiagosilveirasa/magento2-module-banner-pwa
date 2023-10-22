<?php

declare(strict_types=1);

namespace ThiagoSilveira\BannerPWA\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use ThiagoSilveira\BannerPWA\Api\BannerRepositoryInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerInterfaceFactory;
use ThiagoSilveira\BannerPWA\Api\Data\BannerSearchResultsInterface;
use ThiagoSilveira\BannerPWA\Api\Data\BannerSearchResultsInterfaceFactory;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner as ResourceBanner;
use ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;

/**
 * Banner Repository Implementation
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BannerRepository implements BannerRepositoryInterface
{
    /**
     * @var ResourceBanner
     */
    protected $resource;

    /**
     * @var BannerFactory
     */
    protected $bannerFactory;

    /**
     * @var BannerCollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * @var BannerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BannerInterfaceFactory
     */
    protected $dataBannerFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param ResourceBanner $resource
     * @param BannerFactory $bannerFactory
     * @param BannerInterfaceFactory $dataBannerFactory
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param BannerSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param HydratorInterface|null $hydrator
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceBanner $resource,
        BannerFactory $bannerFactory,
        BannerInterfaceFactory $dataBannerFactory,
        BannerCollectionFactory $bannerCollectionFactory,
        BannerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        ?CollectionProcessorInterface $collectionProcessor = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->bannerFactory = $bannerFactory;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBannerFactory = $dataBannerFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->hydrator = $hydrator ?? ObjectManager::getInstance()->get(HydratorInterface::class);
    }

    /**
     * Save Banner data
     *
     * @param BannerInterface $banner
     * @return Banner
     * @throws CouldNotSaveException
     */
    public function save(BannerInterface $banner): Banner
    {
        if ($banner->getId() && $banner instanceof Banner && !$banner->getOrigData()) {
            $banner = $this->hydrator->hydrate($this->getById($banner->getId()), $this->hydrator->extract($banner));
        }

        if ($banner->getStoreId() === null) {
            $storeId = $this->storeManager->getStore()->getId();
            $banner->setStoreId([$storeId]);
        }

        try {
            $this->resource->save($banner);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $banner;
    }

    /**
     * Load Banner data by given Banner Identity
     *
     * @param string|int $bannerId
     * @return Banner
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(string|int $bannerId): Banner
    {
        $banner = $this->bannerFactory->create();
        $this->resource->load($banner, $bannerId);
        if (!$banner->getId()) {
            throw new NoSuchEntityException(__('The banner with the "%1" ID doesn\'t exist.', $bannerId));
        }
        return $banner;
    }

    /**
     * Load Banner data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return BannerSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria): BannerSearchResultsInterface
    {
        /** @var \ThiagoSilveira\BannerPWA\Model\ResourceModel\Banner\Collection $collection */
        $collection = $this->bannerCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var BannerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Banner
     *
     * @param BannerInterface $banner
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(BannerInterface $banner): bool
    {
        try {
            /** @var \ThiagoSilveira\BannerPWA\Model\Banner $banner */
            $this->resource->delete($banner);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Banner by given Banner Identity
     *
     * @param string|int $bannerId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(string|int $bannerId): bool
    {
        return $this->delete($this->getById($bannerId));
    }
}
