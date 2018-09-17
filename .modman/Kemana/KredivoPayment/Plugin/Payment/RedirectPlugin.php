<?php

namespace Kemana\KredivoPayment\Plugin\Payment;

use Kemana\KredivoPayment\Helper\Data;
use Kredivo\Payment\Library\Api as Kredivo_Api;
use Kredivo\Payment\Library\Config as Kredivo_Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class RedirectPlugin
{
    /** Default Country Code */
    const COUNTRY_CODE = "IDN";

    /** Default Payment Type */
    const PAYMENT_TYPE = "30_days";

    /** @var ObjectManagerInterface $_objectManager */
    protected $_objectManager;

    /** @var Session $_checkoutSession */
    protected $_checkoutSession;

    /** @var Order $_orderFactory */
    protected $_orderFactory;

    /** @var LoggerInterface $_logger */
    protected $_logger;

    /** @var RedirectInterface $_redirect */
    protected $_redirect;

    /** @var ResponseInterface $_response */
    protected $_response;

    /** @var Data $_helperData */
    protected $_helperData;
    protected $_storeManager;
    protected $_scopeConfig;

    /**
     * RedirectPlugin constructor.
     * @param ObjectManagerInterface $objectManager
     * @param RedirectInterface $redirect
     * @param ResponseInterface $response
     * @param Data $helperData
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        RedirectInterface $redirect,
        ResponseInterface $response,
        Data $helperData)
    {
        $this->_objectManager = $objectManager;
        $this->_redirect = $redirect;
        $this->_response = $response;
        $this->_helperData = $helperData;
        $this->init();
    }

    /**
     * Init required class object
     */
    public function init()
    {
        $this->_checkoutSession = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_orderFactory = $this->_objectManager->create('Magento\Sales\Model\Order');
        $this->_logger = $this->_objectManager->get('Psr\Log\LoggerInterface');
        $this->_scopeConfig = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
    }

    /**
     * @param \Kredivo\Payment\Controller\Payment\Redirect $subject
     * @param callable $callable
     * @return mixed
     * @throws \Exception
     */
    public function aroundExecute(\Kredivo\Payment\Controller\Payment\Redirect $subject, callable $callable)
    {
        //TODO: some actions with order
        if ($this->_checkoutSession->getLastRealOrderId()) {

            /** @var \Magento\Sales\Model\Order $order */
            $orderId = $this->_checkoutSession->getLastRealOrderId();
            $order = $this->_orderFactory->loadByIncrementId($orderId);

            if ($order->getIncrementId()) {
                $items = $order->getAllItems();
                $discount_amount = $order->getDiscountAmount();
                $shipping_amount = $order->getShippingAmount();
                $shipping_tax_amount = $order->getShippingTaxAmount();
                $tax_amount = $order->getTaxAmount();

                $item_details = $this->getItemDetails($items);

                // Include discount into item details
                $this->includeDiscountAmount($discount_amount, $item_details);

                // Include shipping amount into item details
                $this->includeShippingAmount($shipping_amount, $item_details);

                // Include shipping tax amount into item details
                $this->includeShippingTaxAmount($shipping_tax_amount, $item_details);

                // Include tax amount into item details
                $this->includeTaxAmount($tax_amount, $item_details);

                $totalPrice = 0;
                foreach ($item_details as $item) {
                    $totalPrice += $item['price'] * $item['quantity'];
                }

                Kredivo_Config::$is_production = $this->_helperData->getEnvironment();
                Kredivo_Config::$server_key = $this->_helperData->getServerKey();

                // ====================== TRANSACTION DETAIL ======================
                $params_transaction_details = array(
                    'order_id' => strval($orderId),
                    'amount'   => $this->is_string($totalPrice),
                    'items'    => $item_details,
                );

                $params = array();

                // Get customer details
                $this->getCustomerDetails($order, $params);

                // Get billing address
                $this->getBillingAddress($order, $params);

                // Get shipping address
                $this->getShippingAddress($order, $params);

                $payloads = array(
                    "server_key"          => Kredivo_Config::$server_key, //optional
                    "payment_type"        => self::PAYMENT_TYPE,
                    "push_uri"            => $this->_helperData->getNotificationUrl(),
                    "back_to_store_uri"   => $this->_helperData->getResponseUrl(),
                    "order_status_uri"    => $this->_helperData->getStatusUrl(),
                    "customer_details"    => $params['customer'],
                    "billing_address"     => $params['billing_address'],
                    "shipping_address"    => $params['shipping_address'],
                    "transaction_details" => $params_transaction_details,
                );

                try {
                    $redirUrl = Kredivo_Api::get_redirection_url($payloads);
                    $this->_logger->debug('kredivo_debug:' . print_r($payloads, true));
                    $order->setStatus(Order::STATE_PENDING_PAYMENT);
                    $orderResource = $order->getResource();
                    $orderResource->save($order);
                    $this->_redirect->redirect($this->_response, $redirUrl);
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    $this->_logger->critical('kredivo_error:' . print_r($e->getMessage(), true));
                }
            }
        }

        return $callable();
    }

    /**
     * @param $str
     * @return float
     */
    private function is_string($str)
    {
        try {
            return is_string($str) ? floatval($str) : $str;
        } catch (\Exception $e) {
        }

        return $str;
    }

    /**
     * @param $items
     * @return array
     */
    public function getItemDetails(array $items)
    {
        // Loop the items
        /** @var Order\Item $each */
        foreach ($items as $each) {
            $product = $each->getProduct();
            $categories = $product->getCategoryIds();

            // Loop and get first category name from each product item.
            foreach ($categories as $category_id) {
                $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($category_id);
                $category_name = $category->getName();
                break;
            }

            // Define product item to be sent.
            $item = array(
                'name'     => $each->getName(),
                'id'       => $each->getSku(),
                'price'    => $this->is_string($each->getPrice()),
                'quantity' => $this->is_string($each->getQtyToInvoice()),
                'type'     => $category_name,
                'url'      => $each->getProduct()->getProductUrl() // Add this array item.
            );

            // Skip product with quantity 0
            if ($item['quantity'] == 0) {
                continue;
            }

            // Append product item into item details array
            $item_details[] = $item;
        }
        unset($each);

        return $item_details;
    }

    /**
     * @param float $discount_amount
     * @param array $item_details
     */
    public function includeDiscountAmount(float $discount_amount, array &$item_details)
    {
        if ($discount_amount != 0) {
            $couponItem = array(
                'name'     => 'DISCOUNT',
                'id'       => 'discount',
                'price'    => $this->is_string($discount_amount),
                'quantity' => 1,
            );
            $item_details[] = $couponItem;
        }
    }

    /**
     * @param float $shipping_amount
     * @param array $item_details
     */
    public function includeShippingAmount(float $shipping_amount, array &$item_details)
    {
        if ($shipping_amount > 0) {
            $shipping_item = array(
                'name'     => 'Shipping Cost',
                'id'       => 'shippingfee',
                'price'    => $this->is_string($shipping_amount),
                'quantity' => 1,
            );
            $item_details[] = $shipping_item;
        }
    }

    /**
     * @param float $shipping_tax_amount
     * @param array $item_details
     */
    public function includeShippingTaxAmount(float $shipping_tax_amount, array &$item_details)
    {
        if ($shipping_tax_amount > 0) {
            $shipping_tax_item = array(
                'name'     => 'Shipping Tax',
                'id'       => 'additionalfee',
                'price'    => $this->is_string($shipping_tax_amount),
                'quantity' => 1,
            );
            $item_details[] = $shipping_tax_item;
        }
    }

    /**
     * @param float $tax_amount
     * @param array $item_details
     */
    public function includeTaxAmount(float $tax_amount, array &$item_details)
    {
        if ($tax_amount > 0) {
            $tax_item = array(
                'name'     => 'Tax',
                'id'       => 'taxfee',
                'price'    => $this->is_string($tax_amount),
                'quantity' => 1,
            );
            $item_details[] = $tax_item;
        }
    }

    /**
     * @param Order $order
     * @param array $params
     */
    public function getCustomerDetails(Order $order, array &$params)
    {
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        $orderBillingAddress = $order->getBillingAddress();

        $loggedOut = true;
        if ($customerSession->isLoggedIn()) {
            $first_name = $customerSession->getCustomer()->getId();  // get Customer Id
            $last_name = $customerSession->getCustomer()->getName();  // get  Full Name
            $cust_email = $customerSession->getCustomer()->getEmail(); // get Email Name
            // get Customer Group Id
            $loggedOut = false;
        }
        if($loggedOut) {
            $cust_email = $orderBillingAddress->getEmail();
            $first_name = $orderBillingAddress->getFirstname();
            $last_name = $orderBillingAddress->getLastname();
        }

        // ====================== CUSTOMER ======================
        $params['customer'] = array(
            "first_name" => $first_name,
            "last_name"  => $last_name,
            "email"      => $cust_email,
            "phone"      => $orderBillingAddress->getTelephone(),
        );
    }

    /**
     * @param Order $order
     * @param array $params
     */
    public function getBillingAddress(Order $order, array &$params)
    {
        $orderBillingAddress = $order->getBillingAddress();
        // ====================== BILLING ADDRESS ======================

        $params['billing_address'] = array(
            "first_name"   => $orderBillingAddress->getFirstname(),
            "last_name"    => $orderBillingAddress->getLastname(),
            "address"      => implode(" ", $orderBillingAddress->getStreet()),
            "city"         => $orderBillingAddress->getCity(),
            "postal_code"  => $orderBillingAddress->getPostcode(),
            "phone"        => $orderBillingAddress->getTelephone(),
            "country_code" => self::COUNTRY_CODE
            //"country_code"		=> $this->convert_country_code($order_billing_address->getCountry()),
        );
    }

    /**
     * @param Order $order
     * @param array $params
     */
    public function getShippingAddress(Order $order, array &$params)
    {
        $orderShippingAddress = $order->getShippingAddress();
        // ====================== SHIPPING ADDRESS ======================

        $params['shipping_address'] = array(
            "first_name"   => $orderShippingAddress->getFirstname(),
            "last_name"    => $orderShippingAddress->getLastname(),
            "address"      => implode(" ", $orderShippingAddress->getStreet()),
            "city"         => $orderShippingAddress->getCity(),
            "postal_code"  => $orderShippingAddress->getPostcode(),
            "phone"        => $orderShippingAddress->getTelephone(),
            "country_code" => self::COUNTRY_CODE
            //"country_code"		=> $this->convert_country_code($order_billing_address->getCountry()),
        );
    }
}