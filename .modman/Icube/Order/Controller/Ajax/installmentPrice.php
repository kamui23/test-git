<?php

namespace Icube\Order\Controller\Ajax;

class installmentPrice extends \Magento\Framework\App\Action\Action
{
    protected $_productFactory;
    protected $_jsonHelper;
    protected $_priceHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    )
    {
        $this->_productFactory = $productFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_priceHelper = $priceHelper;
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
        $response = array();

        $params = $this->getRequest()->getParams();
        if ($params['product_id'] != NULL) {
            $product = $this->_productFactory->create()->load($params['product_id']);
            if ($product->getTypeId() == 'simple') {
                $price = $this->_priceHelper->currency($product->getPriceInfo()->getPrice('final_price')->getValue(), true, false);
                $response['0']['price'] = $price;
                $response['0']['product_id'] = $params['product_id'];
            } else {
                $_children = $product->getTypeInstance()->getUsedProducts($product);
                $count = 0;
                foreach ($_children as $child) {
                    $_cicilanSimple = $child->getResource()->getAttribute('price')->getFrontend()->getValue($child);
                    $_prodId = $child->getId();
                    if ($_cicilanSimple) {
                        $price = $this->_priceHelper->currency($_cicilanSimple);
                        $response[$count]['price'] = $price;
                        $response[$count]['product_id'] = $_prodId;
                    }
                    $count++;
                }
            }
        }

        $this->getResponse()->representJson(
            $this->_jsonHelper->jsonEncode($response)
        );
    }
}