<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Advancerate
 * @author       CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Icube\JneTrucking\Model\Config\Backend;

/**
 * Backend model for shipping table rates CSV importing
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Jnetruckingrate extends \Magento\Framework\App\Config\Value
{
    protected $_advancerateFactory;
    protected $_jnetruckingrateFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Icube\JneTrucking\Model\ResourceModel\Carrier\JnetruckingrateFactory $jnetruckingrateFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_jnetruckingrateFactory = $jnetruckingrateFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return \Magento\Framework\App\Config\Value
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave()
    {
        $tableRate = $this->_jnetruckingrateFactory->create();
        $tableRate->uploadAndImport($this);
        return parent::afterSave();
    }
}
