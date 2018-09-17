<?php

namespace Kemana\Contact\Plugin\Magento\Contact\Block;

use Kemana\Contact\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;

class ContactForm
{
    /** @var Information $_information */
    protected $_information;

    /** @var StoreManagerInterface $_storeManager */
    protected $_storeManager;

    /** @var FilterManager $_filterManager */
    protected $_filterManager;

    /** @var Data $_data */
    protected $_data;

    /**
     * ContactForm constructor.
     * @param Information $information
     * @param StoreManagerInterface $storeManager
     * @param FilterManager $filterManager
     * @param Data $data
     */
    public function __construct(
        Information $information,
        StoreManagerInterface $storeManager,
        FilterManager $filterManager,
        Data $data)
    {
        $this->_information = $information;
        $this->_storeManager = $storeManager;
        $this->_filterManager = $filterManager;
        $this->_data = $data;
    }

    public function beforeToHtml(\Magento\Contact\Block\ContactForm $subject)
    {
        $store = $this->_storeManager->getStore();
        $info = $this->_information->getStoreInformationObject($store);
        $info->setFormattedAddress($this->getFormattedAddress($this->_information->getStoreInformationObject($store)));
        $info->setContactEmail($this->_data->getContactEmail());

        $subject->setData('info', $info);
    }

    /**
     * @param DataObject $storeInfo
     * @param string $type
     * @return string
     */
    public function getFormattedAddress(DataObject $storeInfo, $type = "html")
    {
        $address = $this->_filterManager->template(
            "{{var name}}\n{{var street_line1}}\n{{depend street_line2}}{{var street_line2}}\n{{/depend}}"
            . "{{depend city}}{{var city}}, {{/depend}}{{var region}} {{var postcode}},\n{{var country}}",
            ['variables' => $storeInfo->getData()]
        );

        if ($type == 'html') {
            $address = nl2br($address);
        }
        return $address;
    }
}