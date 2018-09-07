<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

use Kemana\Shippingrestriction\Helper\Data;

class Edit extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule
{
    const ERROR = 'This item no longer exists.';

    protected $_ruleFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Data $helper,
        \Kemana\Shippingrestriction\Model\RuleFactory $ruleFactory
    )
    {
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $resultPageFactory, $helper);
        $this->_ruleFactory = $ruleFactory;
        $this->_context = $context;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_ruleFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__(self::ERROR));
                $this->_redirect('kemana_shippingrestriction/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_getSession()->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register($this->_srhelper::RULE_REGISTRY, $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('rule_rule_edit');
        $this->_view->renderLayout();

    }
}
