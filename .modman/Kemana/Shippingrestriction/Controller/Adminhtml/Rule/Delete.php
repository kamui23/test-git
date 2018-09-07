<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

use Kemana\Shippingrestriction\Helper\Data;

class Delete extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule
{
    const ERROR_NOT_FOUND = 'Record does not exist';
    const DELETED         = 'Shipping Restriction has been successfully deleted';
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
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->_ruleFactory->create()->load($id);

        if ($id && !$model->getId()) {
            $this->messageManager->addError(__(self::ERROR_NOT_FOUND));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            $this->messageManager->addSuccess(
                __(self::DELETED));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->_redirect('*/*/');

    }
}
