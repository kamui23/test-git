<?php

namespace Kemana\FixEmailStore\Model\Order\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Framework\Mail\Template\SenderResolverInterface;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    protected $_senderResolver;

    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder,
        SenderResolverInterface $senderResolver
    )
    {
        $this->_senderResolver = $senderResolver;
        parent::__construct(
            $templateContainer,
            $identityContainer,
            $transportBuilder
        );
    }

    /**
     * Configure email template
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function configureEmailTemplate()
    {
        $this->transportBuilder->setTemplateIdentifier($this->templateContainer->getTemplateId());
        $this->transportBuilder->setTemplateOptions($this->templateContainer->getTemplateOptions());
        $this->transportBuilder->setTemplateVars($this->templateContainer->getTemplateVars());
        $emailFrom = $this->_senderResolver->resolve($this->identityContainer->getEmailIdentity(), $this->identityContainer->getStore()->getId());
        $this->transportBuilder->setFrom($emailFrom);
    }
}