<?php
namespace Icube\Brands\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Status;

class ProductRuleProvider
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    /**
     * @var \Icube\Brands\Model\ResourceModel\Items
     */
    private $brand;

    /**
     * @var \Magento\Framework\App\Cache\Type\Collection
     */
    private $collectionCache;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    protected $_scopeConfig;


    /**
     * Cached restricted product IDs
     *
     * @var array|null
     */
    protected static $restrictedProducts = null;

    /**
     * ProductRuleProvider constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param BrandFactory $brand
     * @param \Magento\Catalog\Model\ProductFactory $productCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\App\Cache\Type\Collection $collectionCache
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Icube\Brands\Model\BrandFactory $brand,
        \Magento\Catalog\Model\ProductFactory $productCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\App\Cache\Type\Collection $collectionCache,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->brand = $brand;
        $this->localeDate = $localeDate;
        $this->collectionCache = $collectionCache;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getBrandsIdsNotActive()
    {
        $brandsActive = $this->brand->create()->getCollection()->getActiveCollectionArray();
        // if()
        // echo "<pre>";
        // var_dump($this->brand->create()->getCollection()->getData());die;
        $brandsNotActive = $this->brand->create()->getCollection()->addFieldToFilter('id', ['nin' => $brandsActive]);

        $productIds = array();

        foreach ($brandsNotActive as $key => $brand) {
            $productIds[] = $brand->getAttributeId();

        }
        return $productIds;
    }

    public function getProductIdsNotActive()
    {
        $listBrands = $this->getBrandsIdsNotActive();

        $productIds = array();
        foreach ($listBrands as $key => $brandID) {
            $collection = $this->_productCollectionFactory->create()->getCollection();
            $collection->addAttributeToSelect('*');
            // var_dump(get_class_methods($collection));
            //     	die;
            $value = $this->_scopeConfig->getValue(
                'icube_brands/config/attribute_name',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $collection->addStoreFilter()->addAttributeToFilter($value, $brandID);
            $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
            $collection->addAttributeToFilter('visibility', array('neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE));
            if ($collection) {
                foreach ($collection->getData() as $key => $data) {
                    // echo "<pre>";
                    // print_r($data['entity_id']);
                    // exit;
                    $productIds[] = $data['entity_id'];
                }
            }

        }
        // var_dump($productIds);die;
        return $productIds;

    }

    /**
     * Get array of restricted product ids for current store, store date and customer group
     *
     * @return array|null
     */
    public function getRestrictedProductIds()
    {
        $productIds = $this->getProductIdsNotActive();

        // exit;
        if (self::$restrictedProducts === null) {
            $store = $this->storeManager->getStore();
            $customerGroupId = $this->customerSession->getCustomerGroupId();
            $customerId = (int)$this->customerSession->getCustomerId();
            $cacheId = __CLASS__ . '_restrictedProducts_store' . $store->getId() .
                'customer_group' . $customerGroupId .
                'customer' . $customerId;

            $productIds = $this->hasCacheData($this->collectionCache->load($cacheId));
            if (!$productIds) {
                $dateTs = $this->localeDate->scopeTimeStamp($store);
                $productIds = $this->getProductIdsNotActive();

                $this->collectionCache->save(
                    serialize($productIds),
                    $cacheId,
                    [],
                    3600 // some rules have data range. we should check data range again after 1 hour
                );
            }
            self::$restrictedProducts = $productIds;
        }

        // var_dump($productuctIds);die(1);
        return self::$restrictedProducts;
    }

    public function getProductIdsActive()
    {
        $productIds = $this->getRestrictedProductIds();
        $collection = $this->_productCollectionFactory->create()->getCollection();
        $collection->addAttributeToFilter('entity_id', ['nin' => $productIds]);
        $ids = $collection->getAllIds();
        return $ids;

    }

    /**
     * check and prepare cache for use
     *
     * @param $cachedData
     *
     * @return bool|mixed
     */
    protected function hasCacheData($cachedData)
    {
        $cachedData = $cachedData ?: @unserialize($cachedData);
        if (is_array($cachedData) && count($cachedData)) {
            return $cachedData;
        }

        return false;
    }

}

?>
