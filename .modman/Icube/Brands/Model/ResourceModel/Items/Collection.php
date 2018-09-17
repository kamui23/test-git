<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Model\ResourceModel\Items;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    protected $_helper;
    protected $_customer;


    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Icube\Brands\Helper\Data $helper
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Icube\Brands\Helper\Data $helper,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_helper = $helper;
        $this->_customer = $customer;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Icube\Brands\Model\Items', 'Icube\Brands\Model\ResourceModel\Items');
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['customer'] = 'customergroup_table.customer_group_id';
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id' || $field === 'store_ids') {
            return $this->addStoreFilter($condition, true);
        }

        if ($field === 'customer_group_id' || $field === 'customer_group_ids') {
            return $this->addCustomerFilter($condition, true);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    public function getActiveCollection()
    {
        $store = $this->_storeManager->getStore();
        $storeCode = $store->getCode();
        $session = 'customer_' . $storeCode . '_website';
        // if ($storeCode == 'id') {
        //     $session = 'customer_id_website';
        // } else {
        //     $session = 'customer_sg_website';
        // }

        $customer = $this->_customerSession;
        $customerData = $customer->getCustomer();

        $groupIdentify = 0;
        if ($this->_helper->isLoggedIn()) {
            $customer = $this->_customer->load($_SESSION[$session]['customer_id']);
            $groupId = $customer->getGroupId();
            $joinConditions = 'main_table.id = kemana_brands_customer.ib_id';
            $this->getSelect()->join(
                ['kemana_brands_customer'],
                $joinConditions,
                []
            )->where("kemana_brands_customer.customer_group_id=" . $groupId);
            $groupIdentify = $groupId;
        }
        $this->addBrandsFilter($store->getId(), $groupIdentify);
        $this->addActiveFilter();

        // $joinConditions = 'e.entity_id = store_price.product_id';
        //   $collection->addAttributeToSelect('*');
        //   $collection->getSelect()->join(
        //        ['store_price'],
        //        $joinConditions,
        //        []
        //       )->columns("store_price.product_price")
        //         ->where("store_price.store_id=1");
        // echo $this->getSelect()->__toString();die;


        $this->setOrder('name', 'ASC');
        return $this;
    }

    public function getActiveCollectionArray()
    {

        $ids = array();
        $koleksiaktip = $this->getActiveCollection();
        if ($koleksiaktip):
            foreach ($koleksiaktip as $key => $items) {
                $ids[] = $items->getId();
            }
        endif;
        return $ids;

    }

    /**
     * Add store filter to collection
     * @param array|int|\Magento\Store\Model\Store $store
     * @param boolean $withAdmin
     * @return $this
     */
    public function addBrandsFilter($store, $customer, $inout = 'in', $withAdmin = true)
    {
        if ($store === null || $customer === null) {
            return $this;
        }
        if (!$this->getFlag('brands_filter_added')) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $this->_storeId = $store->getId();
                $store = [$store->getId()];
            }
            if ($customer instanceof \Magento\Customer\Model\Group) {
                $this->_customerGroupId = $customer->getId();
                $customer = [$customer->getId()];
            }

            if (!is_array($store)) {
                $this->_storeId = $store;
                $store = [$store];
            }
            if (!is_array($customer)) {
                $this->_customerGroupId = $customer;
                $customer = [$customer];
            }

            if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $store)) {
                return $this;
            }

            if ($withAdmin) {
                $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
                $customer[] = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
            }

            // echo "<pre>";
            // print_r($customer);
            // // exit;
            // var_dump($customer);
            // var_dump($store);die;
//            $this->addFilter('store', [$inout => $store], 'public');
//            $this->addFilter('customer', [$inout => $customer], 'public');
            $this->addFieldToFilter(['store', 'customer'], [[$inout => $store], [$inout => $customer]]);
            // var_dump($this->getData());die;

            // $a = $this->getSelect()->__toString();
            // echo $a ; die;
        }
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function addActiveFilter()
    {
        return $this->addFieldToFilter('is_active', \Icube\Brands\Model\Status::STATUS_ENABLED);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('id');
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['kbs' => $this->getTable('kemana_brands_store')])
                                 ->where('kbs.ib_id IN (?)', $items)->order('kbs.ib_id');
            $selectCustomer = $connection->select()->from(['kbc' => $this->getTable('kemana_brands_customer')])
                                         ->where('kbc.ib_id IN (?)', $items)->order('kbc.ib_id');
            $result = $connection->fetchAll($select);
            $resultCustomer = $connection->fetchAll($selectCustomer);
            foreach ($result as $i => $storeData) {
                $storeRes[$storeData['ib_id']]['store_ids'][] = $storeData['store_id'];
            }
            foreach ($resultCustomer as $i => $customerData) {
                $customerRes[$customerData['ib_id']]['customer_group_ids'][] = $customerData['customer_group_id'];
            }
            if (isset($storeRes) && $storeRes) {
                foreach ($storeRes as $ib_id => $data) {
                    $brands[$ib_id] = $storeRes[$ib_id];
                    $brands[$ib_id]['customer_group_ids'] = $customerRes[$ib_id]['customer_group_ids'];
                }
            }
            // echo "<pre>";
            // print_r($brands);
            // print_r($storeRes);
            // print_r($customerRes);
            // exit;
            if (isset($brands) && $brands) {
                foreach ($this as $item) {
                    $brandsId = $item->getData('id');
                    $isError = true;
                    if (isset($brands[$brandsId])) {
                        $item->setData('store_ids', $brands[$brandsId]['store_ids']);
                        $item->setData('customer_group_ids', $brands[$brandsId]['customer_group_ids']);
                        $isError = false;
                    }

                    if($isError) {
                        $item->setData('store_ids', [\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
                        $item->setData('customer_group_ids', [\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID]);
                    }
                }
            }

            if ($this->_storeId) {
                foreach ($this as $item) {
                    $item->setStoreId($this->_storeId);
                }
            }
        }

        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        // if ($this->getFilter('store')) {
        $this->getSelect()->joinLeft(
            ['store_table' => $this->getTable('kemana_brands_store')],
            'main_table.id = store_table.ib_id',
            []
        )->group(
            'main_table.id'
        );
        // }
        //
        // if($this->getFilter('customer')){
        $this->getSelect()->joinLeft(
            ['customergroup_table' => $this->getTable('kemana_brands_customer')],
            'main_table.id = customergroup_table.ib_id',
            []
        )->group(
            'main_table.id'
        );
        // }
        parent::_renderFiltersBefore();
    }

    /**
     * Retrieve gruped category childs
     * @return array
     */
    public function getGroupedChilds()
    {
        $childs = [];
        if (count($this)) {
            foreach ($this as $item) {
                $childs[$item->getParentId()][] = $item;
            }
        }
        return $childs;
    }

    /**
     * Retrieve tree ordered categories
     * @return array
     */
    public function getTreeOrderedArray()
    {
        $tree = [];
        if ($childs = $this->getGroupedChilds()) {
            $this->_toTree(0, $childs, $tree);
        }
        return $tree;
    }

    /**
     * Auxiliary function to build tree ordered array
     * @return array
     */
    protected function _toTree($itemId, $childs, &$tree)
    {
        if ($itemId) {
            $tree[] = $this->getItemById($itemId);
        }

        if (isset($childs[$itemId])) {
            foreach ($childs[$itemId] as $i) {
                $this->_toTree($i->getId(), $childs, $tree);
            }
        }
    }
}
