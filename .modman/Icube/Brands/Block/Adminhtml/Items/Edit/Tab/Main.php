<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Icube\Brands\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var \Magento\CatalogRule\Model\Rule\CustomerGroupsOptionsProvider
     */
    protected $_customerGroup;
    /**
     * @var \Icube\Brands\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\CatalogRule\Model\Rule\CustomerGroupsOptionsProvider $customerGroup,
        \Icube\Brands\Model\Status $status,
        array $data = []
    )
    {
        $this->_systemStore = $systemStore;
        $this->_customerGroup = $customerGroup;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Brand Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Brand Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_icube_brands_items');
        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Brand Name'), 'title' => __('Brand Name'), 'required' => true]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            ['name' => 'sort_order', 'label' => __('Sort Order'), 'title' => __('Sort Order'), 'required' => false]
        );

        $fieldset->addField(
            'category_url',
            'text',
            ['name' => 'category_url', 'label' => __('Category Url'), 'title' => __('Category Url'), 'required' => false]
        );


        $fieldset->addField(
            'logo',
            'image',
            ['name' => 'logo', 'label' => __('logo'), 'title' => __('logo'), 'required' => false, 'disabled' => $isElementDisabled]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label'    => __('Status'),
                'title'    => __('Status'),
                'name'     => 'is_active',
                'required' => true,
                'options'  => $this->_status->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'featured',
            'select',
            [
                'label'    => __('Featured'),
                'title'    => __('Featured'),
                'name'     => 'featured',
                'required' => true,
                'options'  => $this->_status->getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name'     => 'store_ids',
                'label'    => __('Store View'),
                'title'    => __('Store View'),
                'required' => false,
                'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'name'     => 'customer_group_ids',
                'label'    => __('Customer Group'),
                'title'    => __('Customer Group'),
                'required' => true,
                'values'   => $this->_customerGroup->toOptionArray(),
                'disabled' => $isElementDisabled,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
