<?php

namespace Icube\Import\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductCron extends Command
{
    protected $importproduct;

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
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        //$om->get('\Magento\Framework\App\State')->setAreaCode('adminhtml');

        $output->writeln('<info>Starting reading data from csv files</info>');
        /**
         * @var \Magento\Framework\Registry
         */
        $registry = $om->get('\Magento\Framework\Registry');
        $registry->register('isSecureArea', true);
        $om->get('\Icube\Import\Helper\Product')->ImportProductCron();
        $output->writeln(' ');
        $output->writeln('<info>finish</info>');

        return 0;
    }
}
