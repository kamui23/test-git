<?php

namespace Kemana\Shippingrestriction\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Kemana\Shippingrestriction\Helper\Data;
use Magento\Customer\Api\GroupManagementInterface;

class StoresGroups extends Generic implements TabInterface
{

    const APPLY_IN_FIELDSET  = 'Apply In';
    const APPLY_FOR_FIELDSET = 'Apply For';
    const STORES_LABEL       = 'Stores';
    const CUST_GROUP_LABEL   = 'Customer Groups';
    const NOTE               = 'Leave empty or select all to apply the rule to any store';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_converter;

    protected $_srhelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        GroupManagementInterface $customerGroup,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Convert\DataObject $converter,
        Data $helper,
        array $data = []
    )
    {
        $this->_groupManagement = $customerGroup;
        $this->_systemStore = $systemStore;
        $this->_converter = $converter;
        $this->_srhelper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form before rendering HTML
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry($this->_srhelper::RULE_REGISTRY);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fldStore = $form->addFieldset('apply_in', ['legend' => __(self::APPLY_IN_FIELDSET)]);

        $fldStore->addField(
            'stores',
            'multiselect',
            [
                'name'   => 'stores[]',
                'label'  => __(self::STORES_LABEL),
                'values' => $this->_systemStore->getStoreValuesForForm(false, false),
                'note'   => __(self::NOTE),
            ]
        );

        $fldCust = $form->addFieldset('apply_for', array('legend' => __(self::APPLY_FOR_FIELDSET)));
        $fldCust->addField(
            'cust_groups',
            'multiselect',
            [
                'name'   => 'cust_groups[]',
                'label'  => __(self::CUST_GROUP_LABEL),
                'values' => $this->getAllOptions(),
                'note'   => __(self::NOTE),
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getAllOptions()
    {
        $groups = $this->_groupManagement->getLoggedInGroups();
        $groups[] = $this->_groupManagement->getNotLoggedInGroup();
        $options = $this->_converter->toOptionArray($groups, 'id', 'code');

        return $options;
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __($this->_srhelper::STORES_AND_CUST_GROUPS);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __($this->_srhelper::STORES_AND_CUST_GROUPS);
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
}
