<?php

namespace Kemana\Shippingrestriction\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Kemana\Shippingrestriction\Helper\Data;

class DayTime extends Generic implements TabInterface
{
    const DAYS_LABEL = 'Days of the week';
    const DAYS_NOTE  = 'Leave empty or select all to apply the rule every day';
    const TAB_TITLE  = 'Day time';

    protected $_srhelper;

    /**
     * DayTime constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $helper,
        array $data = []
    )
    {
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

        $fldInfo = $form->addFieldset('daystime', array('legend' => __($this->_srhelper::DAYS_TITLE)));

        $fldInfo->addField('days', 'multiselect', array(
            'label'  => __(self::DAYS_LABEL),
            'name'   => 'days[]',
            'values' => $this->_srhelper->getAllDays(),
            'note'   => __(self::DAYS_NOTE),
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __(self::TAB_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __(self::TAB_TITLE);
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
