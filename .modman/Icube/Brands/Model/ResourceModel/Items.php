<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Model\ResourceModel;

use Magento\Cms\Model\Page as CmsPage;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\EntityManager;

class Items extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store model
     *
     * @var null|Store
     */
    protected $_store = null;

    /**
     * Customer Group model
     *
     * @var null|$_customerGroup
     */
    protected $_customerGroup = null;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param DateTime $dateTime
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        DateTime $dateTime,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('icube_brands_items', 'id');
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $storeIds = $this->lookupStoreIds($object->getId());
            $object->setData('store_ids', $storeIds);

            $customerGroup = $this->lookupCustomerGroup($object->getId());
            $object->setData('customer_group_ids', $customerGroup);
        }

        return parent::_afterLoad($object);
    }


    /**
     * Assign Fees to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStoreIds = $this->lookupStoreIds($object->getId());
        $newStoreIds = (array)$object->getStoreIds();
        if (!$newStoreIds) {
            $newStoreIds = [0];
        }

        $oldCustIds = $this->lookupCustomerGroup($object->getId());
        $newCustIds = (array)$object->getCustomerGroupIds();
        if (!$newCustIds) {
            $newCustIds = [0];
        }

        $this->_updateLinks($object, $newStoreIds, $oldStoreIds, 'kemana_brands_store', 'store_id');

        $this->_updateLinks($object, $newCustIds, $oldCustIds, 'kemana_brands_customer', 'customer_group_id');

        return parent::_afterSave($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $pfId
     * @return array
     */
    public function lookupStoreIds($pfId)
    {
        return $this->_lookupIds($pfId, 'kemana_brands_store', 'store_id');
    }

    /**
     * Get customer group ids to which specified item is assigned
     *
     * @param int $pfId
     * @return array
     */
    public function lookupCustomerGroup($pfId)
    {
        return $this->_lookupIds($pfId, 'kemana_brands_customer', 'customer_group_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $pfId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($pfId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'ib_id = ?',
            (int)$pfId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * @param AbstractModel $object
     * @param array $newRelatedIds
     * @param array $oldRelatedIds
     * @param $tableName
     * @param $field
     * @param array $rowData
     */
    protected function _updateLinks(
        \Magento\Framework\Model\AbstractModel $object,
        Array $newRelatedIds,
        Array $oldRelatedIds,
        $tableName,
        $field,
        $rowData = []
    )
    {
        $table = $this->getTable($tableName);

        $insert = $newRelatedIds;
        $delete = $oldRelatedIds;

        if ($delete) {
            $where = ['ib_id = ?' => (int)$object->getId(), $field . ' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(['ib_id' => (int)$object->getId(), $field => $id],
                                      (isset($rowData[$id]) && is_array($rowData[$id])) ? $rowData[$id] : []
                );
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Set store model
     *
     * @param Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    public function getActiveMatchesSelect()
    {
        echo "<pre>CEK";
        print_r($this->getBrandNotActive());
        exit;
        if (is_string($date)) {
            $date = strtotime($date);
        }

        $select = $this->getConnection()->select()
                       ->from(['product' => 'catalog_product_entity'])
                       ->joinLeft(
                           ['entity_int' => $this->getTable('catalog_product_entity_int')],
                           'product.entity_id = store_table.entity_id',
                           []
                       )->joinLeft(
                ['ibi' => $this->getTable('icube_brands_items')],
                'product.id = store_table.ib_id',
                []
            )->where('store_id = 0 or store_id = ?', $storeId)
                       ->where('customer_group_enabled = 0 or customer_group_id = ?', $customerGroupId)
                       ->where('from_time = 0 or from_time < ?', $date)
                       ->where('to_time = 0 or to_time > ?', $date)
                       ->order(['priority DESC', 'price_action DESC']);

        if ($customerId !== null) {
            $select->joinInner(
                ['customer' => $this->getCustomerMatchesTable()],
                'customer.rule_id = product.rule_id',
                ['customer_id' => 'customer.customer_id']
            )
                   ->where('customer_id = ?', $customerId);
        }

        if ($productId) {
            $select->where('product_id = ?', $productId);
        }
        return $select;
    }

}
