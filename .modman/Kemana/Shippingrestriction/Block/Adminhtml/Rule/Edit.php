<?php

namespace Kemana\Shippingrestriction\Block\Adminhtml\Rule;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    const SAVE_AND_CONTINUE_EDIT = 'Save and Continue Edit';
    const OBJECT_ID              = 'id';
    const CONTROLLER             = 'adminhtml_rule';
    const BLOCK_GROUP            = 'Kemana_Shippingrestriction';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = self::OBJECT_ID;
        $this->_controller = self::CONTROLLER;
        $this->_blockGroup = self::BLOCK_GROUP;

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class'          => 'save',
                'label'          => __(self::SAVE_AND_CONTINUE_EDIT),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );

    }

}
