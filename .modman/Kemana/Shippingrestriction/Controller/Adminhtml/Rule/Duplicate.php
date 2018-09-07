<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

use Kemana\Shippingrestriction\Helper\Data;

class Duplicate extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule
{
    const ERROR   = 'Please select a rule to duplicate.';
    const SUCCESS = 'The rule has been duplicated. Please feel free to activate it.';
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

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->messageManager->addError(__(self::ERROR));
            return $this->_redirect('*/*');
        }

        try {
            $model = $this->_ruleFactory->create()->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__(self::ERROR));
                return $this->_redirect('*/*');
            }

            $rule = clone $model;
            $rule->setIsActive(0);
            $rule->setId(null);
            $rule->save();

            $this->messageManager->addSuccess(
                __(self::SUCCESS)
            );
            return $this->_redirect('*/*/edit', array('id' => $rule->getId()));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $this->_redirect('*/*');
        }
    }
}
