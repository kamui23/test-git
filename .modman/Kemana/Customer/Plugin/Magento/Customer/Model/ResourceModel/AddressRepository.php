<?php

namespace Kemana\Customer\Plugin\Magento\Customer\Model\ResourceModel;

use Kemana\Customer\Helper\CityData;
use Magento\Customer\Model\Address as CustomerAddressModel;
use Magento\Framework\Exception\InputException;

class AddressRepository
{
    /** @var CityData $_cityDataHelper */
    protected $_cityDataHelper;

    /** @var \Magento\Customer\Model\AddressFactory $_addressFactory */
    protected $_addressFactory;

    /** @var \Magento\Customer\Model\AddressRegistry $_addressRegistry */
    protected $_addressRegistry;

    /** @var \Magento\Customer\Model\CustomerRegistry $_customerRegistry */
    protected $_customerRegistry;

    /** @var \Magento\Directory\Helper\Data $_directoryData */
    protected $_directoryData;

    /**
     * AddressRepository constructor.
     * @param CityData $cityData
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\AddressRegistry $addressRegistry
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Directory\Helper\Data $directoryData
     */
    public function __construct(
        CityData $cityData,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\AddressRegistry $addressRegistry,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Directory\Helper\Data $directoryData
    )
    {
        $this->_cityDataHelper = $cityData;
        $this->_addressFactory = $addressFactory;
        $this->_addressRegistry = $addressRegistry;
        $this->_customerRegistry = $customerRegistry;
        $this->_directoryData = $directoryData;
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\AddressRepository $subject
     * @param callable $proceed
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws InputException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundSave(
        \Magento\Customer\Model\ResourceModel\AddressRepository $subject,
        callable $proceed,
        \Magento\Customer\Api\Data\AddressInterface $address)
    {
        if (!$this->_cityDataHelper->getGeneralConfig('enable')) {
            $addressModel = null;
            $customerModel = $this->_customerRegistry->retrieve($address->getCustomerId());
            if ($address->getId()) {
                $addressModel = $this->_addressRegistry->retrieve($address->getId());
            }

            if ($addressModel === null) {
                /** @var \Magento\Customer\Model\Address $addressModel */
                $addressModel = $this->_addressFactory->create();
                $addressModel->updateData($address);
                $addressModel->setCustomer($customerModel);
            } else {
                $addressModel->updateData($address);
            }

            $inputException = $this->_validate($addressModel);
            if ($inputException->wasErrorAdded()) {
                throw $inputException;
            }
            $addressModel->save();
            $address->setId($addressModel->getId());
            // Clean up the customer registry since the Address save has a
            // side effect on customer : \Magento\Customer\Model\ResourceModel\Address::_afterSave
            $this->_customerRegistry->remove($address->getCustomerId());
            $this->_addressRegistry->push($addressModel);
            $customerModel->getAddressesCollection()->clear();

            return $addressModel->getDataModel();
        }

        return $proceed($address);
    }

    /**
     * Validate Customer Addresses attribute values.
     *
     * @param CustomerAddressModel $customerAddressModel the model to validate
     * @return InputException
     *
     * @throws \Zend_Validate_Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function _validate(CustomerAddressModel $customerAddressModel)
    {
        $exception = new InputException();
        if ($customerAddressModel->getShouldIgnoreValidation()) {
            return $exception;
        }

        if (!\Zend_Validate::is($customerAddressModel->getFirstname(), 'NotEmpty')) {
            $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'firstname']));
        }

        if (!\Zend_Validate::is($customerAddressModel->getLastname(), 'NotEmpty')) {
            $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'lastname']));
        }

        if (!\Zend_Validate::is($customerAddressModel->getStreetLine(1), 'NotEmpty')) {
            $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'street']));
        }

        if (!\Zend_Validate::is($customerAddressModel->getTelephone(), 'NotEmpty')) {
            $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'telephone']));
        }

        $havingOptionalZip = $this->_directoryData->getCountriesWithOptionalZip();
        if (!in_array($customerAddressModel->getCountryId(), $havingOptionalZip)
            && !\Zend_Validate::is($customerAddressModel->getPostcode(), 'NotEmpty')
        ) {
            $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'postcode']));
        }

        if (!\Zend_Validate::is($customerAddressModel->getCountryId(), 'NotEmpty')) {
            $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'countryId']));
        }

        if ($this->_directoryData->isRegionRequired($customerAddressModel->getCountryId())) {
            $regionCollection = $customerAddressModel->getCountryModel()->getRegionCollection();
            if (!$regionCollection->count() && empty($customerAddressModel->getRegion())) {
                $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'region']));
            } elseif (
                $regionCollection->count()
                && !in_array(
                    $customerAddressModel->getRegionId(),
                    array_column($regionCollection->getData(), 'region_id')
                )
            ) {
                $exception->addError(__('%fieldName is a required field.', ['fieldName' => 'regionId']));
            }
        }
        return $exception;
    }
}
