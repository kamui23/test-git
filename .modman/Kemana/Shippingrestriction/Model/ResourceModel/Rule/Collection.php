<?php

namespace Kemana\Shippingrestriction\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const IS_ACTIVE = 1;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
    )
    {
        $this->_coreDate = $coreDate;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, null, null);
    }


    /**
     * Define resource model
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        $this->_init('Kemana\Shippingrestriction\Model\Rule', 'Kemana\Shippingrestriction\Model\ResourceModel\Rule');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    public function addAddressFilter($address)
    {
        $this->addFieldToFilter('is_active', self::IS_ACTIVE);

        $storeId = $address->getQuote()->getStoreId();
        $storeId = intval($storeId);
        $this->addFieldToFilter('stores',
                                array(
                                    array('like' => "%," . $storeId . ",%"),
                                    array('eq' => "")
                                )
        );

        $groupId = 0;
        if ($address->getQuote()->getCustomerId()) {
            $groupId = $address->getQuote()->getCustomer()->getGroupId();
        }
        $groupId = intval($groupId);
        $this->addFieldToFilter('cust_groups',
                                array(
                                    array('like' => "%," . $groupId . ",%"),
                                    array('eq' => "")
                                )
        );
        $this->addFieldToFilter('days',
                                array(
                                    array('like' => "%," . $this->_coreDate->date('N') . ",%"),
                                    array('eq' => "")
                                )
        );
        return $this;
    }
}
