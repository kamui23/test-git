<?php

namespace Icube\LimitCompare\Model\Plugin\Compare;

use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Catalog\Helper\Product\Compare;

class LimitToCompareProducts
{
    const LIMIT_TO_COMPARE_PRODUCTS = 3;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /** @var Compare */
    protected $helper;

    /**
     * RestrictCustomerEmail constructor.
     * @param Compare $helper
     * @param RedirectFactory $redirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        RedirectFactory $redirectFactory,
        Compare $helper,
        ManagerInterface $messageManager
    )
    {
        $this->helper = $helper;
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
    }

    public function aroundExecute(
        \Magento\Catalog\Controller\Product\Compare\Add $subject,
        \Closure $proceed
    )
    {
        $count = $this->helper->getItemCount();
        if ($count >= self::LIMIT_TO_COMPARE_PRODUCTS) {
            $this->messageManager->addErrorMessage(
                'Max compared products is 3 item(s)'
            );

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setRefererOrBaseUrl();
        }

        return $proceed();
    }
}