<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Controller\Adminhtml\Items;

class NewAction extends \Icube\Brands\Controller\Adminhtml\Items
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    protected $_attrOptionCollectionFactory;

    protected $registry;

    // public function __construct(
    // 	\Magento\Backend\App\Action\Context $context,
    // 	\Magento\Framework\Registry $registry,
    // 	\Magento\Backend\Model\View\Result\ForwardFactory $ForwardFactory ,
    // 	\Magento\Framework\View\Result\PageFactory $PF,
    // 	\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
    // 	array $data = [])
    // {
    // 	parent::__construct($context, $data);
    // 	$this->_attrOptionCollectionFactory = $attrOptionCollectionFactory;
    // }

    public function execute()
    {
        $totval = 0;
        // $this->_forward('edit');
        $model = $this->_objectManager->create(
            'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
        )->setEntityTypeId(
            \Magento\Catalog\Model\Product::ENTITY
        );

        $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $scopeConfig->getValue(
            'icube_brands/config/attribute_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($value == null) {
            $this->messageManager->addError(__('Cannot re-sync brands. Brand attribute is still empty under Store > Configuration > Catalog > Brand'));
        } else {
            $model->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $value);
            // echo "<pre>";
            // var_dump(get_class_methods($model));

            foreach ($model->getOptions() as $option) {

                $item = $this->_objectManager->create('Icube\Brands\Model\Items');
                if ($option->getValue()) {
                    $id = (int)$option->getValue();
                    if ($id) {
                        $item->load($id, 'attribute_id');
                        if ($id != $item->getId()) {
                            // throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                        }
                    }

                    $data = array(
                        'id'                 => $item->getId(),
                        'name'               => $option->getLabel(),
                        'attribute_id'       => $option->getValue(),
                        'is_active'          => 1,
                        'store_ids'          => $item->getStoreId() ? $item->getStoreId() : (array)0,
                        'customer_group_ids' => $item->getCustomerGroupId() ? $item->getCustomerGroupId() : (array)0,
                    );
                    // echo "<pre>";
                    $item->setData($data);
                    // print_r($item->debug());
                    // die;
                    // try{
                    if ($item->save())
                        $totval++;
                    // }
                    // catch(\Exception $e){
                    // echo "ada error ".$e;
                    // }
                    // var_dump($item->debug());
                    // die;

                }
            }
            // die;
            $this->messageManager->addSuccess(__('All Brands Re-Synced - %1 items', $totval));
        }
        $this->_redirect('icube_brands/*/');

    }
}
