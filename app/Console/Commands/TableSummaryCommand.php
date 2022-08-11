<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TableSummary
 */
class TableSummaryCommand extends Command
{
    protected $signature = 'table:summary';

    protected $descriptio = 'create sequelize type summary report for unit tests';

    public function handle()
    {
        $this->execute();
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return int|null|void
     */
    public function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $table = new Table($output);
        $table
            ->setHeaders(['ISBN', 'Title', 'Author'])
            ->setRows([
                ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
                ['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'],
                ['960-425-059-0', 'The Lord of the Rings', 'J. R. R. Tolkien'],
                ['80-902734-1-6', 'And Then There Were None', 'Agatha Christie'],
            ]);
        $table->render();
    }
}
