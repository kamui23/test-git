<?php

namespace Icube\Order\Controller\Ajax;

class isSaleAble extends \Magento\Framework\App\Action\Action
{
    protected $_productFactory;
    protected $_jsonHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    )
    {
        $this->_productFactory = $productFactory;
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return ($this->getRequest()->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }


    /**
     * Storelist AJAX
     *
     * @return boolean
     */
    public function execute()
    {
        /* activate this when it's live */
        /*if (!$this->isAjax()) {
            return;
        }*/
        $isSaleAble = NULL;

        $params = $this->getRequest()->getParams();
        if ($params['product_id'] != NULL) {
            $product = $this->_productFactory->create()->load($params['product_id']);
            $isSaleAble = $product->isSaleAble();
        }
        $response['isSaleAble'] = $isSaleAble;

        $this->getResponse()->representJson(
            $this->_jsonHelper->jsonEncode($response)
        );
    }
}

?>