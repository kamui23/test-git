<?php

namespace Kemana\Shippingrestriction\Block\Adminhtml\Rule\Edit;

use Kemana\Shippingrestriction\Helper\Data;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    const TITLE    = 'Rule';
    const FORM_ID  = 'kemana_rule_form';
    const URL_SAVE = 'kemana_shippingrestriction/rule/save';

    protected $_srhelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = [],
        Data $helper
    )
    {
        $this->_srhelper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::FORM_ID);
        $this->setTitle(__(self::TITLE));
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'     => $this->_srhelper::FORM_ELEMENT_ID,
                    'action' => $this->getUrl(self::URL_SAVE),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
