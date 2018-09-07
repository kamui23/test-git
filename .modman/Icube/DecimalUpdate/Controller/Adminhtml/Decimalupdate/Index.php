<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Icube\DecimalUpdate\Controller\Adminhtml\Decimalupdate;

use \Magento\Catalog\Model\Product\Visibility;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    protected $_resourceConnection;
    protected $_scopeConfig;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     */
    const XML_PATH_DATA = 'icube_section/execute/point_customer_attribute_id';

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_logger = $logger;
        $this->_resourceConnection = $resourceConnection;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }


    /**
     * Synchronize
     *
     * @return void
     */
    public function execute()
    {
        $connection = $this->_resourceConnection->getConnection();

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $tablename = $this->_scopeConfig->getValue(self::XML_PATH_DATA, $storeScope);

        $sql = "SELECT CONCAT( 'ALTER TABLE ', TABLE_NAME, ' MODIFY COLUMN ', COLUMN_NAME, ' TIMESTAMP DEFAULT CURRENT_TIMESTAMP' ) AS TABLE_NAME FROM information_schema.columns WHERE table_schema = '" . $tablename . "' AND column_default = '0000-00-00 00:00:00'";
        $result = $connection->fetchAll($sql);

        $sql1 = "SELECT CONCAT( 'ALTER TABLE ', TABLE_NAME, ' MODIFY COLUMN ', COLUMN_NAME, ' decimal(17,4)' ) AS TABLE_NAME FROM information_schema.columns WHERE table_schema = '" . $tablename . "' AND column_type = 'decimal(12,4)'";
        $result1 = $connection->fetchAll($sql1);

        foreach ($result as $key => $value) {
            $result2 = $connection->query($value['TABLE_NAME']);

        }

        foreach ($result1 as $key1 => $value1) {
            $result3 = $connection->query($value1['TABLE_NAME']);
        }

        $statusTxt = $this->getStatusTxt($result, $result1);
        $response['status'] = $statusTxt;

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
        );
    }

    protected function getStatusTxt($result, $result1) {
        if ($result && $result1) {
            return 'success';
        }
        return 'query already run';
    }
}