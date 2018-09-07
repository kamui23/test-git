<?php

namespace Kemana\FixSearchCategoryByMenu\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_helperMenu;
    protected $_resourceCon;
    protected $_storeManager;
    protected $_customer;
    protected $_modelMenu;

    public function __construct(\Ves\Megamenu\Model\Menu $modelMenu, \Ves\Megamenu\Helper\Data $helperMenu, \Magento\Framework\App\ResourceConnection $resourceCon, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Model\Customer $customer)
    {
        $this->_modelMenu = $modelMenu;
        $this->_helperMenu = $helperMenu;
        $this->_resourceCon = $resourceCon;
        $this->_storeManager = $storeManager;
        $this->_customer = $customer;
    }

    public function getMenuCategories()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $storeCode = $this->_storeManager->getStore()->getCode();
        $session = 'customer_' . $storeCode . '_website';
        if ($this->isLoggedIn()) {
            $customer = $this->_customer->load($_SESSION[$session]['customer_id']);
            $groupId = $customer->getGroupId();
        } else {
            $groupId = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        }

        $connection = $this->_resourceCon->getConnection();
        $select = $connection->select()->from(
            ['cb' => $this->_resourceCon->getTableName('ves_megamenu_menu')]
        )->join(
            ['cbs' => $this->_resourceCon->getTableName('ves_megamenu_menu_store')],
            'cb.menu_id = cbs.menu_id',
            []
        )->join(
            ['cgi' => $this->_resourceCon->getTableName('ves_megamenu_menu_customergroup')],
            'cb.menu_id = cgi.menu_id',
            []
        )->where(
            'cb.status = ?',
            1
        )->where(
            'cbs.store_id = ?',
            $storeId
        )->where(
            'cgi.customer_group_id = ?',
            $groupId
        );
        $resultSql = $connection->fetchRow($select);
        $categories = [];
        if ($resultSql) {
            $menu = $this->_modelMenu->load($resultSql['menu_id']);
            $menuCat = $this->_helperMenu->getMenuCategories();

            $menuItems = $menu->getData('menuItems');

            foreach ($menuItems as $item) {
                if (isset($item['link_type']) && $item['link_type'] == 'category_link' && isset($item['category']) && !in_array($item['category'], $categories)) {
                    $categories[] = $item['category'];
                }
            }
        }
        return $categories;
    }

    public function isLoggedIn()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $objectManager->get('Magento\Framework\App\Http\Context');
        return $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

}