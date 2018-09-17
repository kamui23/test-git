<?php

namespace Icube\Order\Block\Adminhtml\StoreDashboard;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
    protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    protected $authSession;

    protected $_adminposFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Icube\Order\Model\Config\Status $status
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Icube\Order\Model\AdminPosFactory $adminpos
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Icube\Order\Model\Config\Status $status,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Icube\Order\Model\AdminPosFactory $adminpos,
        array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        $this->_status = $status;
        $this->authSession = $authSession;
        $this->_adminposFactory = $adminpos;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);

    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        try {

            //get store-admin permission
            $user = $this->getCurrentUser();
            $adminpos = $this->_adminposFactory->create()->load($user->getId(), 'user_id');

            $collection = $this->_collectionFactory->create();
            $collection->getSelect()
                       ->joinLeft(array('i' => 'sales_order_item'), 'i.order_id = main_table.entity_id ', array('item_id', 'sku', 'name', 'qty_ordered', 'i.store_code'));
            $collection->getSelect()
                       ->joinLeft(array('s' => 'sales_shipment'), 's.order_id = main_table.entity_id ', array('shipment_eid' => 's.entity_id', 'shipment_id' => 's.increment_id'))
                       ->joinLeft(array('si' => 'sales_shipment_item'), 'si.parent_id = s.entity_id', array('order_item_id'));
            $collection->addFieldToFilter('delivery_pickup', array(array('like' => '%pickup%'), array('like' => '%mixed%')));
            $collection->addFieldToFilter('i.store_code', array('neq' => NULL));
            $collection->getSelect()->group('i.item_id');
            $collection->setOrder('main_table.increment_id', 'DESC');

            if ($adminpos->getId()) {
                if ($adminpos->getStoreCode() == NULL) {
                    $collection->addFieldToFilter('i.store_code', array('eq' => NULL));
                } else if ($adminpos->getStoreCode() != 'all') {
                    $collection->addFieldToFilter('i.store_code', array('eq' => $adminpos->getStoreCode()));
                }
            }

            $this->setCollection($collection);

            parent::_prepareCollection();

            return $this;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    // protected function _addColumnFilterToCollection($column)
    // {
    //     if ($this->getCollection()) {
    //         if ($column->getId() == 'websites') {
    //             $this->getCollection()->joinField(
    //                 'websites',
    //                 'catalog_product_website',
    //                 'website_id',
    //                 'product_id=entity_id',
    //                 null,
    //                 'left'
    //             );
    //         }
    //     }
    //     return parent::_addColumnFilterToCollection($column);
    // }


    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header'           => __('ID'),
                'type'             => 'text',
                'index'            => 'increment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'filter_index'     => 'main_table.increment_id',
                'renderer'         => '\Icube\Order\Block\Adminhtml\Column\Renderer\StoreDashboard\Order'
            ]
        );
        $this->addColumn(
            'store_code',
            [
                'header'       => __('Store Code'),
                'type'         => 'text',
                'index'        => 'store_code',
                'filter_index' => 'i.store_code'
            ]
        );
        $this->addColumn(
            'purchase_date',
            [
                'header'       => __('Purchase Date'),
                'type'         => 'datetime',
                'index'        => 'created_at',
                'class'        => 'date',
                'filter_index' => 'main_table.created_at'
            ]
        );
        // $this->addColumn(
        //     'shipping_method',
        //     [
        //         'header' => __('Shipping Method'),
        //         'type' => 'text',
        //         'index' => 'shipping_method',
        //         'class' => 'ship-method'
        //     ]
        // );
        $this->addColumn(
            'customer_email',
            [
                'header'       => __('Customer Email'),
                'type'         => 'text',
                'index'        => 'customer_email',
                'class'        => 'email',
                'filter_index' => 'main_table.customer_email',
                'renderer'     => '\Icube\Order\Block\Adminhtml\Column\Renderer\StoreDashboard\Customer'
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header'       => __('SKU'),
                'type'         => 'text',
                'index'        => 'sku',
                'class'        => 'sku',
                'filter_index' => 'i.sku'
            ]
        );
        $this->addColumn(
            'product_name',
            [
                'header'       => __('Product Name'),
                'type'         => 'text',
                'index'        => 'name',
                'class'        => 'name',
                'filter_index' => 'i.name'
            ]
        );
        $this->addColumn(
            'qty',
            [
                'header'       => __('Qty'),
                'type'         => 'text',
                'index'        => 'qty_ordered',
                'class'        => 'qty',
                'filter_index' => 'i.qty_ordered'
            ]
        );
        // $this->addColumn(
        //     'purchase_point',
        //     [
        //         'header' => __('Store Name'),
        //         'type' => 'longtext',
        //         'index' => 'store_name',
        //         'class' => 'store name'
        //     ]
        // );
        $this->addColumn(
            'shipment_id',
            [
                'header'           => __('Shipment ID'),
                'type'             => 'text',
                'index'            => 'shipment_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'filter_index'     => 's.increment_id',
                'renderer'         => '\Icube\Order\Block\Adminhtml\Column\Renderer\StoreDashboard\Shipment'
            ]
        );
        $this->addColumn(
            'status',
            [
                'header'       => __('Status'),
                'type'         => 'options',
                'index'        => 'status',
                'class'        => 'store name',
                'filter_index' => 'main_table.status',
                'options'      => $this->_status->toOptionArray(),
            ]
        );
        /*{{CedAddGridColumn}}*/

        // var_dump($this->_status->toOptionArray());

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    // protected function _prepareMassaction()
    // {
    //     $this->setMassactionIdField('increment_id');
    //     $this->setMassactionIdFieldOnlyIndexValue(true);
    //     $this->getMassactionBlock()->setFormFieldName('id');

    //     $this->getMassactionBlock()->addItem(
    //         'receiptid',
    //         array(
    //             'label' => __('Set Receipt ID'),
    //             'url' => $this->getUrl('storedashboard/*/massReceipt'),
    //             'additional'   => array(
    //                 'receipt_id'    => array(
    //                    'name'     => 'receipt_id',
    //                    'type'     => 'text',
    //                    'class'    => 'required-entry',
    //                    'label'    => __('Receipt ID')
    //                 )
    //             )
    //         )
    //     );
    //     $this->getMassactionBlock()->addItem(
    //         'nopo',
    //         array(
    //             'label' => __('Set No PO'),
    //             'url' => $this->getUrl('storedashboard/*/massNopo'),
    //             'additional'   => array(
    //                 'receipt_id'    => array(
    //                    'name'     => 'no_po',
    //                    'type'     => 'text',
    //                    'class'    => 'required-entry',
    //                    'label'    => __('No PO')
    //                 )
    //             )
    //         )
    //     );
    //     $this->getMassactionBlock()->addItem(
    //         'readypickup',
    //         array(
    //             'label' => __('Siap Diambil'),
    //             'url' => $this->getUrl('storedashboard/*/massReady'),
    //             'confirm' => __('Are you sure?')
    //         )
    //     );
    //      $this->getMassactionBlock()->addItem(
    //         'pickedup',
    //         array(
    //             'label' => __('Sudah Diambil'),
    //             'url' => $this->getUrl('storedashboard/*/massPicked'),
    //             'confirm' => __('Are you sure?')
    //         )
    //     );
    //     $this->getMassactionBlock()->addItem(
    //         'waitingcwh',
    //         array(
    //             'label' => __('Tunggu CWH'),
    //             'url' => $this->getUrl('storedashboard/*/massWaitingCwh'),
    //             'confirm' => __('Are you sure?')
    //         )
    //     );
    //     $this->getMassactionBlock()->addItem(
    //         'itemnotcompleted',
    //         array(
    //             'label' => __('Set Barang Tidak Lengkap'),
    //             'url' => $this->getUrl('storedashboard/*/massItemNotCompleted'),
    //             'confirm' => __('Are you sure?')
    //         )
    //     );
    //     $this->getMassactionBlock()->addItem(
    //         'awb',
    //         array(
    //             'label' => __('Set AWB/DO'),
    //             'url' => $this->getUrl('storedashboard/*/massAwb'),
    //             'additional'   => array(
    //                 'receipt_id'    => array(
    //                    'name'     => 'awbdo',
    //                    'type'     => 'text',
    //                    'class'    => 'required-entry',
    //                    'label'    => __('AWB/DO')
    //                 )
    //             )
    //         )
    //     );
    //     return $this;
    // }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('storedashboard/*/index', ['_current' => true]);
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    // public function getRowUrl($row)
    // {
    //     return $this->getUrl(
    //         'storedashboard/storedashboard/view',
    //         ['store' => $this->getRequest()->getParam('store'), 'order_id' => $row->getEntityId()]
    //     );
    // }
}
