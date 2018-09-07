<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Kemana\Shippingrestriction\Controller\Adminhtml\Rule;
use Magento\Framework\View\Result\PageFactory;
use Kemana\Shippingrestriction\Helper\Data;

abstract class AbstractMassAction extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule
{
    protected $_filter;
    protected $_collectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        PageFactory $resultPageFactory,
        Data $helper,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Kemana\Shippingrestriction\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $resultPageFactory, $helper);
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());

            $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }

        return $this->_redirect('*/*');
    }

    abstract protected function massAction($collection);
}
