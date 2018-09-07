<?php

namespace Icube\Order\Block;

class Storelist extends \Magento\Framework\View\Element\Template
{
    protected $_stockFactory = null;
    protected $_posFactory   = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory
    )
    {
        $this->_posFactory = $posFactory;
        parent::__construct($context);
    }

    public function getStoreList()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        $posCol = $this->_posFactory->create()->getCollection();
        $places = array();
        foreach ($posCol as $pos) {
            $places[] = array('id' => $pos->getId(), 'code' => $pos->getStoreCode(), 'name' => $pos->getName());
        }
        return $places;
    }

}
