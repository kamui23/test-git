<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Kemana\Shippingrestriction\Helper\Data;

/**
 * Items controller
 */
abstract class Rule extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_resultLayoutFactory = null;

    protected $_rule;

    protected $_srhelper;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Data $helper
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_srhelper = $helper;
    }

    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Kemana_Shippingrestriction::shipping_restriction');
        $resultPage->addBreadcrumb(__($this->_srhelper::SHIPPING_RESTRICTIONS), __($this->_srhelper::SHIPPING_RESTRICTIONS));
        $resultPage->getConfig()->getTitle()->prepend(__($this->_srhelper::SHIPPING_RESTRICTIONS));

        return $resultPage;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Kemana_Shippingrestriction::shipping_restriction');
    }
}
