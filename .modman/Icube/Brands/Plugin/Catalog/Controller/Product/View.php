<?php

namespace Icube\Brands\Plugin\Catalog\Controller\Product;

class View
{
    private $ruleProvider;
    private $resultForwardFactory;
    private $coreRegistry;

    /**
     * View constructor.
     * @param \Icube\Brands\Model\ProductRuleProvider $ruleProvider
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->ruleProvider = $ruleProvider;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->coreRegistry = $coreRegistry;
    }

    public function noProductRedirect($subject)
    {
        $store = $subject->getRequest()->getQuery('store');
        if (isset($store) && !$subject->getResponse()->isRedirect()) {
            $resultRedirect = $subject->resultRedirectFactory->create();
            return $resultRedirect->setPath('');
        } elseif (!$subject->getResponse()->isRedirect()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    public function aroundExecute(\Magento\Catalog\Controller\Product\View $subject, \Closure $proceed)
    {
        $productId = $subject->getRequest()->getParam('id');
        $productIds = $this->ruleProvider->getRestrictedProductIds();
        if (in_array($productId, $productIds)) {
            $this->noProductRedirect($subject);
        }
        return $proceed();
    }
}