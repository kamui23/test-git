<?php

namespace Kemana\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Icube Custom for reindex advancedinventory_index_flat table
 */
class ReindexFlatTable extends Command
{
    protected $stock;
    protected $appState;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Wyomind\AdvancedInventory\Model\Stock $stock
    )
    {
        $this->stock = $stock;
        $this->appState = $appState;
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {

        }
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('icube:advancedinventory:reindex')
             ->setDescription('Reindex advancedinventory_index_flat table');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting reindex</info>');
        $this->stock->reindex();
        $output->writeln('<info>Finish</info>');
    }
}