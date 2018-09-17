<?php

namespace Icube\Import\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Unirgy\RapidFlow\Model\Profile;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Product extends AbstractHelper
{
    protected $fileSystem;
    protected $profile;
    protected $_objectManager;
    protected $state;
    protected $skip = array();
    protected $site = array('id', 'sg');

    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state,
        Profile $profile
    )
    {
        $this->fileSystem = $fileSystem;
        $this->profile = $profile;
        $this->state = $state;
        try {
            $this->state->setAreaCode('frontend');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        } catch (\Magento\Framework\Exception\SessionException $e) {
            // intentionally left empty
        }
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function importPrice()
    {
        $logFileName = 'import-price-' . date('Y-m-d-h-i-s');
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $logFileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $profile = $this->profile;
        $arrlocationpath = [$this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('urapidflow/import/price/'), $this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('urapidflow/import/specialprice/')];
        foreach ($this->site as $siteLocation) {
            foreach ($arrlocationpath as $location_path) {
                $location_path = $location_path . $siteLocation . '/';
                $idPrice = $this->getPriceBySiteLocale($siteLocation, 34, 42);
                foreach (glob($location_path . '*.csv') as $filepath) {

                    $filename = str_replace($location_path, "", $filepath);
                    $profile->load($idPrice, 'profile_id');
                    if ($profile->getID()) {
                        $profile->setBaseDir($location_path);
                        $profile->setFilename($filename);
                        $profile->start()->save()->run();
                        $profile->exportExcelReport();
                        $result = "import data from " . $filename . " completed. Run status: " . $profile->getRunStatus();
                        $result .= ". Rows Success: " . $profile->getRowsSuccess() . ". Errors: " . $profile->getNumErrors();
                        $result .= ". Warnings: " . $profile->getNumWarnings();
                        echo $result . '<br/>';
                        $logger->info($result);
                        if (!is_dir($location_path . 'archive/'))
                            mkdir($location_path . 'archive/', 0775, true);
                        if (file_exists($filepath)) {
                            rename($filepath, $location_path . 'archive/' . $filename . date('Y-m-d-h-i-s') . '.csv');
                        }
                    }
                }
            }

        }
        return $logFileName;
    }

    public function importTierPrice()
    {
        $logFileName = 'import-tier-price-' . date('Y-m-d-h-i-s');
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $logFileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $profile = $this->profile;
        $location_path = $this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('urapidflow/import/tierprice/');
        foreach (glob($location_path . '*.csv') as $filepath) {
            $filename = str_replace($location_path, "", $filepath);
            $profile->load('Import Tier Price', 'title');
            if ($profile->getID()) {
                $profile->setBaseDir($location_path);
                $profile->setFilename($filename);
                $profile->start()->save()->run();
                $profile->exportExcelReport();
                $result = "import data from " . $filename . " completed. Run status: " . $profile->getRunStatus();
                $result .= ". Rows Success: " . $profile->getRowsSuccess() . ". Errors: " . $profile->getNumErrors();
                $result .= ". Warnings: " . $profile->getNumWarnings();
                echo $result . '<br/>';
                $logger->info($result);
                if (!is_dir($location_path . 'archive/'))
                    mkdir($location_path . 'archive/', 0775, true);
                if (file_exists($filepath)) {
                    rename($filepath, $location_path . 'archive/' . $filename . date('Y-m-d-h-i-s') . '.csv');
                }
            }
        }
        return $logFileName;
    }

    public function importSpecialPriceCcp()
    {
        $logFileName = 'import-special-price-ccp-' . date('Y-m-d-h-i-s');
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $logFileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $profile = $this->profile;
        foreach ($this->site as $siteLocation) {
            $location_path = $this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('urapidflow/import/specialpriceccp/');
            $location_path = $location_path . $siteLocation . '/';
            $idPrice = $this->getPriceBySiteLocale($siteLocation, 35, 43);
            foreach (glob($location_path . '*.csv') as $filepath) {
                $filename = str_replace($location_path, "", $filepath);
                $profile->load($idPrice, 'profile_id');
                if ($profile->getID()) {
                    $profile->setBaseDir($location_path);
                    $profile->setFilename($filename);
                    $profile->start()->save()->run();
                    $profile->exportExcelReport();
                    $result = "import data from " . $filename . " completed. Run status: " . $profile->getRunStatus();
                    $result .= ". Rows Success: " . $profile->getRowsSuccess() . ". Errors: " . $profile->getNumErrors();
                    $result .= ". Warnings: " . $profile->getNumWarnings();
                    echo $result . '<br/>';
                    $logger->info($result);
                    if (!is_dir($location_path . 'archive/'))
                        mkdir($location_path . 'archive/', 0775, true);
                    if (file_exists($filepath)) {
                        rename($filepath, $location_path . 'archive/' . $filename . date('Y-m-d-h-i-s') . '.csv');
                    }
                }
            }
        }
        return $logFileName;
    }

    public function importProductCron()
    {
        $logFileName = 'importproductcron.log';
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $logFileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $profile = $this->profile;
        $location_path = $this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath('urapidflow/import/product/');
        foreach (glob($location_path . '*.csv') as $filepath) {
            $filename = str_replace($location_path, "", $filepath);
            $profile->load('Import Product Cron', 'title');
            if ($profile->getID()) {
                $profile->setBaseDir($location_path);
                $profile->setFilename($filename);
                $profile->start()->save()->run();
                $profile->exportExcelReport();
                $result = "import data from " . $filename . " completed. Run status: " . $profile->getRunStatus();
                $result .= ". Rows Success: " . $profile->getRowsSuccess() . ". Errors: " . $profile->getNumErrors();
                $result .= ". Warnings: " . $profile->getNumWarnings() . '<br/>';
                echo $result;
                $result .= '<br/>';
                $logger->info($result);
                if (!is_dir($location_path . 'archive/'))
                    mkdir($location_path . 'archive/', 0775, true);
                if (file_exists($filepath)) {
                    rename($filepath, $location_path . 'archive/' . $filename . date('Y-m-d-h-i-s') . '.csv');
                }
            }
        }
    }

    protected function getPriceBySiteLocale($siteLocation, $price1, $price2) {
        if($siteLocation == 'id') {
            return $price1;
        }
        return $price2;
    }
}