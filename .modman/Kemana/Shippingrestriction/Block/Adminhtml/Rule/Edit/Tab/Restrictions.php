<?php

namespace Kemana\Shippingrestriction\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Kemana\Shippingrestriction\Model\System\Config\Status;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Kemana\Shippingrestriction\Helper\Data;

class Restrictions extends Generic implements TabInterface
{
    const FIELDSET_LEGEND = 'General';
    const NAME_LABEL      = 'Name';
    const STATUS_LABEL    = 'Status';
    const METHODS_LABEL   = 'Methods';
    const NOTE            = '*Carrier Code - Carrier Title - Carrier Name';
    /**
     * @var \Kemana\Shippingrestriction\Model\System\Config\Status
     */
    protected $_status;

    protected $_srhelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Status $status
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        Data $helper,
        array $data = []
    )
    {
        $this->_status = $status;
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
        $form->setHtmlIdPrefix('rule_');
        $fieldset = $form->addFieldset('apply_in', ['legend' => __(self::FIELDSET_LEGEND)]);
        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __(self::NAME_LABEL),
                'title'    => __(self::NAME_LABEL),
                'required' => true
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'   => 'is_active',
                'label'  => __(self::STATUS_LABEL),
                'title'  => __(self::STATUS_LABEL),
                'values' => $this->_status->toOptionArray()
            ]
        );
        $fieldset->addField(
            'methods',
            'multiselect',
            [
                'name'     => 'methods[]',
                'label'    => __(self::METHODS_LABEL),
                'values'   => $this->_srhelper->getShippingMethods(),
                'required' => true,
                'note'     => self::NOTE
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __($this->_srhelper::RESTRICTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __($this->_srhelper::RESTRICTIONS);
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
