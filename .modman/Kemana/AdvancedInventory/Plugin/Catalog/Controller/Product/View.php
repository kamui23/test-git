<?php

namespace Kemana\AdvancedInventory\Plugin\Catalog\Controller\Product;

class View
{
    protected $_storeManager;
    protected $_customer;
    protected $_helper;
    protected $_pointOfSale;
    protected $_productRepository;
    protected $_stock;
    protected $resultForwardFactory;
    protected $coreRegistry;

    public function __construct(
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customer,
        \Kemana\AdvancedInventory\Helper\Data $helper,
        \Wyomind\PointOfSale\Model\PointOfSale $pointOfSale,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Wyomind\AdvancedInventory\Model\Stock $stock
    )
    {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_customer = $customer;
        $this->_helper = $helper;
        $this->_pointOfSale = $pointOfSale;
        $this->_productRepository = $productRepository;
        $this->_stock = $stock;
    }

    public function noProductRedirect($subject)
    {
        $store = $subject->getRequest()->getQuery('store');
        if (isset($store) && !$subject->getResponse()->isRedirect()) {
            $resultRedirect = $subject->resultRedirectFactory->create();
            return $resultRedirect->setPath('');
        } elseif (!$subject->getResponse()->isRedirect()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    public function aroundExecute(\Magento\Catalog\Controller\Product\View $subject, \Closure $proceed)
    {
        $productId = $subject->getRequest()->getParam('id');
        $product = $this->_productRepository->getById($productId);
        $typeId = $product->getTypeId();
        $storeId = $this->_storeManager->getStore()->getId();

        $placesCollection = $this->_pointOfSale->getPlacesByStoreId($storeId);
        $arrPlaces = $placesCollection->getAllIds();
        $access = false;
        if ($typeId == 'configurable') {
            if ($this->_helper->isLoggedIn()) {
                $storeCode = $this->_storeManager->getStore()->getCode();
                $session = 'customer_' . $storeCode . '_website';
                $customer = $this->_customer->load($_SESSION[$session]['customer_id']);
                $groupId = $customer->getGroupId();
            } else {
                $groupId = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
            }
            $arrayConfiguable = json_decode($this->_helper->getConfigurableProductInStock($storeId, $groupId));
            if (in_array($productId, $arrayConfiguable)) {
                return $proceed();
            } else {
                $this->noProductRedirect($subject);
            }

        } else {
            foreach ($arrPlaces as $value) {
                $stockData = $this->_stock->getStockByProductIdAndPlaceId($productId, $value);
                if ($stockData['quantity_in_stock']) {
                    $access = true;
                    break;
                }
            }
            if (!$access) {
                $this->noProductRedirect($subject);
            } else {
                return $proceed();
            }

        }
    }
}