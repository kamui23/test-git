<?php

namespace Kemana\Shippingrestriction\Plugin;

use Magento\Quote\Api\Data\EstimateAddressInterface;

class ShippingMethodManagement
{
    protected $_quoteRepository;
    protected $_converter;
    protected $_totalsCollector;
    protected $_allRules = null;
    protected $_ruleCollection;
    protected $_output   = [];
    protected $_addressRepository;

    /**
     * Constructs a shipping method read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $converter
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Kemana\Shippingrestriction\Model\ResourceModel\Rule\Collection $ruleCollection
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\Cart\ShippingMethodConverter $converter,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Kemana\Shippingrestriction\Model\ResourceModel\Rule\Collection $ruleCollection,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    )
    {
        $this->_quoteRepository = $quoteRepository;
        $this->_converter = $converter;
        $this->_totalsCollector = $totalsCollector;
        $this->_ruleCollection = $ruleCollection;
        $this->_addressRepository = $addressRepository;
    }


    /**
     * @inheritdoc
     */
    public function aroundEstimateByAddress(
        \Mageplaza\Osc\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }

        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion()
        );
    }

    /**
     * @inheritdoc
     */
    public function aroundEstimateByExtendedAddress(
        \Mageplaza\Osc\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }

        return $this->getShippingMethods($quote, $address->getData());
    }

    /**
     * {@inheritDoc}
     */
    public function aroundEstimateByAddressId(
        \Mageplaza\Osc\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $cartId,
        $addressId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        $address = $this->_addressRepository->getById($addressId);

        return $this->getEstimatedRates(
            $quote,
            $address->getCountryId(),
            $address->getPostcode(),
            $address->getRegionId(),
            $address->getRegion()
        );
    }

    /**
     * Get estimated rates
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $country
     * @param string $postcode
     * @param int $regionId
     * @param string $region
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    protected function getEstimatedRates(\Magento\Quote\Model\Quote $quote, $country, $postcode, $regionId, $region)
    {
        $data = [
            EstimateAddressInterface::KEY_COUNTRY_ID => $country,
            EstimateAddressInterface::KEY_POSTCODE   => $postcode,
            EstimateAddressInterface::KEY_REGION_ID  => $regionId,
            EstimateAddressInterface::KEY_REGION     => $region
        ];
        return $this->getShippingMethods($quote, $data);
    }

    /**
     * Get list of available shipping methods
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $addressData
     * @return \Magento\Quote\Api\Data\ShippingMethodInterface[]
     */
    public function getShippingMethods(\Magento\Quote\Model\Quote $quote, array $addressData)
    {
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->addData($addressData);
        $shippingAddress->setCollectShippingRates(true);

        $this->_totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();

        $items = $quote->getAllItems();
        $shippingAddress->setItemsToValidateRestrictions($items);

        $this->validateRestrictions($quote, $shippingAddress, $shippingRates, $items);

        return $this->_output;
    }

    public function getRules($address)
    {
        if (is_null($this->_allRules)) {
            $this->_allRules = $this->_ruleCollection
                ->addAddressFilter($address);

            $this->_allRules->load();

            foreach ($this->_allRules as $rule) {
                $rule->afterLoad();
            }
        }
        return $this->_allRules;
    }

    public function validateRestrictions($quote, $shippingAddress, $shippingRates, $items)
    {
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $rules = $this->getRules($shippingAddress, $items);
                if (count($rules)) {
                    foreach ($rules as $rule) {
                        if (!$rule->restrict($rate)) {
                            $this->_output[] = $this->_converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
                        }//if restrict
                    }//rules
                } else {
                    $this->_output[] = $this->_converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
                }
            }
        }
    }
}