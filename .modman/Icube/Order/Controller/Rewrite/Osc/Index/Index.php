<?php

namespace Icube\Order\Controller\Rewrite\Osc\Index;

/**
 * Class Index
 * @package Mageplaza\Osc\Controller\Index
 */
class Index extends \Mageplaza\Osc\Controller\Index\Index
{
    public function execute()
    {
        $this->_checkoutHelper = $this->_objectManager->get('Mageplaza\Osc\Helper\Data');
        if (!$this->_checkoutHelper->isEnabled()) {
            $this->messageManager->addError(__('One step checkout is turned off.'));

            return $this->resultRedirectFactory->create()->setPath('checkout');
        }

        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        //ICUBE CUSTOM
        $deliveryPickup = $quote->getDeliveryPickup();
        if ($deliveryPickup == NULL || $deliveryPickup == 'oos') {
            $this->messageManager->addError(__('Stock is not enough or you have not choose delivery/pickup yet.'));
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
        //END OF ICUBE CUSTOM

        $this->_customerSession->regenerateId();
        $this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
        $this->getOnepage()->initCheckout();

        $this->initDefaultMethods($quote);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set($this->_checkoutHelper->getConfig()->getCheckoutTitle());

        return $resultPage;
    }


}
