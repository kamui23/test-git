<?php

namespace Icube\Brands\Plugin\Catalog\Model;

class Layer
{
    private $ruleProvider;

    /**
     * @var \Magento\Framework\Registry
     */
    private   $coreRegistry;
    protected $request;
    protected $_helper;

    /**
     * Layer constructor.
     * @param \Icube\Brands\Model\ProductRuleProvider $ruleProvider
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Icube\Brands\Helper\Data $helper
     */
    public function __construct(
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\Http $request,
        \Icube\Brands\Helper\Data $helper
    )
    {
        $this->ruleProvider = $ruleProvider;
        $this->coreRegistry = $coreRegistry;
        $this->request = $request;
        $this->_helper = $helper;
    }

    public function beforePrepareProductCollection($subject, $collection)
    {

        if (!$this->_helper->isEnable()) {
            $q = $this->request->getParam('q');

            $productIds = $this->ruleProvider->getProductIdsActive();
            $category = $this->coreRegistry->registry('current_category');
            if (($productIds && $category) || (isset($q))) {
                $collection->addFieldToFilter('entity_id', ['in' => $productIds]);

            }
            return [$collection];
        }
    }

}
