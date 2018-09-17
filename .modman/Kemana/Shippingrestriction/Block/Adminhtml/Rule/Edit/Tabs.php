<?php

namespace Kemana\Shippingrestriction\Block\Adminhtml\Rule\Edit;

use Kemana\Shippingrestriction\Helper\Data;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    const TITLE                  = 'Rule Configuration';
    const STORE_CUST_GROUP_BLOCK = 'StoresGroups';
    const DAY_BLOCK              = 'DayTime';
    const TABS_ID                = 'kemana_shippingrestriction_rule_edit_tabs';

    protected $_srhelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = [],
        Data $helper
    )
    {
        $this->_srhelper = $helper;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::TABS_ID);
        $this->setDestElementId($this->_srhelper::FORM_ELEMENT_ID);
        $this->setTitle(__(self::TITLE));
    }

    protected function _beforeToHtml()
    {

        $tabs = array(
            'restrictions'  => ['title' => $this->_srhelper::RESTRICTIONS, 'block' => $this->_srhelper::RESTRICTIONS],
            'stores_groups' => ['title' => $this->_srhelper::STORES_AND_CUST_GROUPS, 'block' => self::STORE_CUST_GROUP_BLOCK],
            'daystime'      => ['title' => $this->_srhelper::DAYS_TITLE, 'block' => self::DAY_BLOCK]
        );

        foreach ($tabs as $code => $data) {

            $this->addTab(
                $code,
                [
                    'label'   => __($data['title']),
                    'title'   => __($data['title']),
                    'content' => $this->getLayout()->createBlock(
                        'Kemana\Shippingrestriction\Block\Adminhtml\Rule\Edit\Tab\\' . $data['block']
                    )->toHtml()
                ]
            );

        }

        return parent::_beforeToHtml();
    }
}
