<?php

namespace Kemana\Import\Plugin\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Unirgy\RapidFlow\Model\Profile;
use Magento\Framework\App\Helper\Context;

class Product
{
    const SITE_MY                               = 'my';
    const PATH_IMPORT_PRICE                     = 'urapidflow/import/price/';
    const PATH_IMPORT_SPECIALPRICE              = 'urapidflow/import/specialprice/';
    const PATH_IMPORT_SPECIALPRICECCP           = 'urapidflow/import/specialpriceccp/';
    const LOG_FILENAME_PRICE                    = 'import-price-';
    const LOG_FILENAME_SPECIALPRICECCP          = 'import-special-price-ccp-';
    const PATH_LOG                              = '/var/log/';
    const PATH_ARCHIVE                          = 'archive/';
    const DATE_FORMAT                           = 'Y-m-d-h-i-s';
    const URAPIDFLOW_PRICE_PROFILE_ID           = '45';
    const URAPIDFLOW_SPECIALPRICECCP_PROFILE_ID = '46';
    const URAPIDFLOW_TABLE_FIELD                = 'profile_id';
    const FILE_EXTENSION                        = '.csv';
    const FILE_PERMISSION_MODE                  = 755;
    const RESULT_MESSAGE                        = 'import data from %s completed. Run status: %s. Rows Success: %s. Errors: %s. Warnings: %s <br\>';
    const PRICE_KEY                             = 'price';
    const SPECIALPRICECCP_KEY                   = 'specialpriceccp';

    protected $_fileSystem;
    protected $_profile;

    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem $fileSystem,
        Profile $profile
    )
    {
        $this->_fileSystem = $fileSystem;
        $this->_profile = $profile;
    }

    public function aroundImportPrice(\Icube\Import\Helper\Product $subject, callable $callable)
    {
        $proceed = $callable();
        $this->importProcess(self::PRICE_KEY);
        return $proceed;
    }

    public function aroundImportSpecialPriceCcp(\Icube\Import\Helper\Product $subject, callable $callable)
    {
        $proceed = $callable();
        $this->importProcess(self::SPECIALPRICECCP_KEY);
        return $proceed;
    }

    protected function importProcess($importName)
    {
        $logName = $importName === self::PRICE_KEY ? self::LOG_FILENAME_PRICE : self::LOG_FILENAME_SPECIALPRICECCP;
        $profileId = $importName === self::PRICE_KEY ? self::URAPIDFLOW_PRICE_PROFILE_ID : self::URAPIDFLOW_SPECIALPRICECCP_PROFILE_ID;
        $logFileName = $logName . date(self::DATE_FORMAT);
        $writer = new \Zend\Log\Writer\Stream(BP . self::PATH_LOG . $logFileName);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $profile = $this->_profile;
        $arrlocationpath = [
            self::PRICE_KEY           => [
                $this->_fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath(self::PATH_IMPORT_PRICE),
                $this->_fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath(self::PATH_IMPORT_SPECIALPRICE)
            ],
            self::SPECIALPRICECCP_KEY => [
                $this->_fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath(self::PATH_IMPORT_SPECIALPRICECCP)
            ]
        ];
        try {
            foreach ($arrlocationpath[$importName] as $locationPath) {
                $locationPath = $locationPath . self::SITE_MY . '/';
                foreach (glob($locationPath . '*' . self::FILE_EXTENSION) as $filepath) {

                    $filename = str_replace($locationPath, "", $filepath);
                    $profile->load($profileId, self::URAPIDFLOW_TABLE_FIELD);

                    if ($profile->getID()) {
                        $profile->setBaseDir($locationPath);
                        $profile->setFilename($filename);
                        $profile->start()->save()->run();
                        $profile->exportExcelReport();
                        $result = sprintf(
                            self::RESULT_MESSAGE,
                            $filename,
                            $profile->getRunStatus(),
                            $profile->getRowsSuccess(),
                            $profile->getNumErrors(),
                            $profile->getNumWarnings()
                        );
                        echo $result;
                        $logger->info($result);
                        if (!is_dir($locationPath . self::PATH_ARCHIVE)) {
                            mkdir($locationPath . self::PATH_ARCHIVE, self::FILE_PERMISSION_MODE, true);
                        }

                        if (file_exists($filepath)) {
                            rename($filepath, $locationPath . self::PATH_ARCHIVE . $filename . date(self::DATE_FORMAT) . self::FILE_EXTENSION);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $logger->info($e);
        }
        echo $logFileName;
    }

}