<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

use Kemana\Shippingrestriction\Helper\Data;

class Save extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule
{
    const SAVED = 'Shipping Restriction has been successfully saved';
    const ERROR = 'Unable to find a record to save';

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
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('rule_id');
        $model = $this->_ruleFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            unset($data['rule']);
            $model->setData($data);  // common fields

            $model->setId($id);
            $session = $this->_getSession();
            try {
                $this->prepareForSave($data, $model);

                $model->save();

                $session->setPageData(false);

                $this->messageManager->addSuccess(__(self::SAVED));

                $notGetBack = true;
                if ($this->getRequest()->getParam('back')) {
                    $notGetBack = false;
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                if($notGetBack) {
                    $this->_redirect('*/*');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $session->setPageData($model->getData());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
            return;
        }

        $this->messageManager->addError(__(self::ERROR));
        $this->_redirect('*/*');
    }

    public function prepareForSave($data, $model)
    {

        $fields = array('stores', 'cust_groups', 'methods', 'days');

        foreach ($fields as $f) {
            $val = isset($data[$f]) ? $data[$f] : [];
            $model->setData($f, '');
            if ($val) {
                $model->setData($f, "," . implode(',', $val) . ",");
            }
        }

        return true;
    }
}
