<?php

namespace Icube\CustomWholesale\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $customerSession;
    private $customerContext;
    private $group;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $customerContext,
        \Magento\Customer\Model\Group $group)
    {
        $this->customerSession = $customerSession;
        $this->customerContext = $customerContext;
        $this->group = $group;
        parent::__construct($context);
    }

    public function isLoggedIn()
    {
        return $this->customerContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getCustomerGroupCode()
    {
        $id = $this->customerSession->getCustomerGroupId();
        $group = $this->group->load($id);
        return $group->getCustomerGroupCode();
    }
}