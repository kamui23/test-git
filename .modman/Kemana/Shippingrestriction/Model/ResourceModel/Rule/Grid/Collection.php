<?php

namespace Kemana\Shippingrestriction\Model\ResourceModel\Rule\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Kemana\Shippingrestriction\Model\ResourceModel\Rule\Collection as RuleCollection;

class Collection extends RuleCollection implements SearchResultInterface
{
    protected $_aggregations;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document'
    )
    {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $coreDate,
            $localeDate
        );
        $this->_eventObject = $eventObject;
        $this->_eventPrefix = $eventPrefix;
        $this->setMainTable($mainTable);
        $this->_init($model, $resourceModel);
    }

    public function getAggregations()
    {
        return $this->_aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->_aggregations = $aggregations;
    }


    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    protected function _afterLoad()
    {
        foreach ($this as $item) {
            $item->setMethods(explode(',', $item->getMethods()));
            if ($item->getStores()) {
                $item->setStores(explode(',', $item->getStores()));
            } else {
                $item->setStores(array('all'));
            }
            if ($item->getCustGroups()) {
                $item->setCustGroups(explode(',', $item->getCustGroups()));
            } else {
                $item->setCustGroups(array('all'));
            }
        }
    }
}
