<?php

namespace Kemana\FixRequestStore\Plugin\Model;

class StoreRepository
{
    protected $_storeRepository;

    public function __construct(\Magento\Store\Model\StoreRepository $storeRepository)
    {
        $this->_storeRepository = $storeRepository;
    }

    public function beforeGetById(\Magento\Store\Model\StoreRepository $subject, $id)
    {
        $stores = $this->_storeRepository->getList();
        if (!in_array($id, $stores)) $id = 0;
        return [$id];

    }
}