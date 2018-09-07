<?php

namespace Icube\Import\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductCron extends Command
{
    protected $importproduct;

    protected $_objectManager;

    public function __construct(
        $name = null,
        \Magento\Framework\App\ObjectManager $objectManager
    ) {
        parent::__construct($name);
        $this->_objectManager = $objectManager;
    }


    protected function configure()
    {
        $this->setName('icube:productcron:import')
             ->setDescription('Upload product using csv files');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $om = \Magento\Framework\App\ObjectManager::getInstance();
        //$om->get('\Magento\Framework\App\State')->setAreaCode('adminhtml');

        $output->writeln('<info>Starting reading data from csv files</info>');
        /**
         * @var \Magento\Framework\Registry
         */
        $registry = $this->_objectManager->get('\Magento\Framework\Registry');
        $registry->register('isSecureArea', true);
        $this->_objectManager->get('\Icube\Import\Helper\Product')->ImportProductCron();
        $output->writeln(' ');
        $output->writeln('<info>finish</info>');

        return 0;
    }
}
