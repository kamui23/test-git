<?php

namespace Kemana\Contact\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\Store;

class Data extends AbstractHelper
{
    /** Contact email config path */
    const CONTACT_EMAIL_PATH = "contact/email/recipient_email";

    /** @var Store $_store */
    protected $_store;

    public function __construct(Context $context, Store $store)
    {
        parent::__construct($context);
        $this->_store = $store;
    }

    public function getContactEmail()
    {
        return $this->_store->getConfig(self::CONTACT_EMAIL_PATH);
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        $this->_logger = $logger;
    }
}