<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

class Index extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule
{
    const BREADCRUMB = 'Rules';

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Kemana_Shippingrestriction::shipping_restriction');
        $resultPage->addBreadcrumb(__(self::BREADCRUMB), __(self::BREADCRUMB));
        $resultPage->getConfig()->getTitle()->prepend(__($this->_srhelper::SHIPPING_RESTRICTIONS));

        return $resultPage;
    }
}
