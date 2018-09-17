<?php

namespace Icube\Order\Observer;

class AutofillPickupaddress implements \Magento\Framework\Event\ObserverInterface
{
    protected $_posFactory    = null;
    protected $_regionFactory = null;
    private   $attributes     = [
        'delivery_pickup', 'store_code'
    ];

    public function __construct(
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory
    )
    {
        $this->_posFactory = $posFactory;
        $this->_regionFactory = $regionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getData('order');
        $quote = $observer->getEvent()->getData('quote');

        foreach ($this->attributes as $attribute) {
            if ($quote->hasData($attribute)) {
                $order->setData($attribute, $quote->getData($attribute));
            }
        }

        $storecode = null;
        if ($quote->getDeliveryPickup() == 'pickup') {
            $storecode = $quote->getStoreCode();
            $pos = $this->_posFactory->create()->load($storecode, 'store_code');
            $region = $this->_regionFactory->create()->loadByCode($pos->getState(), 'ID');
            $address = $quote->getShippingAddress();
            $address->setFirstname($pos->getName())
                    ->setLastname('store')
                    ->setStreet($pos->getAddressLine1() . ' ' . $pos->getAddressLine2())
                    ->setCity($pos->getCity())
                    ->setCompany('Rodalink')
                    ->setPostcode($pos->getPostalCode())
                    ->setTelephone($pos->getMainPhone())
                    ->setCountryId($pos->getCountryCode())
                    ->setRegionId($region->getId())
                    ->setRegion($region->getName())
                    ->save();

            $address = $order->getShippingAddress();
            $address->setFirstname($pos->getName())
                    ->setLastname('store')
                    ->setStreet($pos->getAddressLine1() . ' ' . $pos->getAddressLine2())
                    ->setCity($pos->getCity())
                    ->setCompany('Rodalink')
                    ->setPostcode($pos->getPostalCode())
                    ->setTelephone($pos->getMainPhone())
                    ->setCountryId($pos->getCountryCode())
                    ->setRegion($region->getName())
                    ->save();
        }

    }
}