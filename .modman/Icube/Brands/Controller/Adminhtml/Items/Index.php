<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Controller\Adminhtml\Items;

class Index extends \Icube\Brands\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('icube::base');
        $resultPage->getConfig()->getTitle()->prepend(__('Icube Brands'));
        $resultPage->addBreadcrumb(__('Icube'), __('Icube'));
        $resultPage->addBreadcrumb(__('Items'), __('Brands'));
        return $resultPage;
    }
}
