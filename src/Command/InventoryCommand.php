<?php

namespace PlinioCardoso\InventoryBundle\Command;

use PlinioCardoso\InventoryBundle\Service\StockImportManagement;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'inventory',
    description: 'Update stock data from a CSV file',
)]
class InventoryCommand extends Command
{
    public function __construct(
        private readonly StockImportManagement $stockImportManagement,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'CSV filename')
            ->addOption('directory', null, InputOption::VALUE_REQUIRED, 'Directory where the CSV file is located')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');
        $directory = '/' . $input->getOption('directory');
        $kernel = $this->getApplication()->getKernel();

        $finder = new Finder();
        $finder->files()
            ->in($kernel->getProjectDir() . $directory)
            ->name($file);

        if (!$finder->hasResults()) {
            throw new FileNotFoundException('The specified file does not exist, please check the filename/directory and try again.');
        }

        foreach ($finder as $file) {
            $content = $file->getContents();
            $this->processCsvContent(
                str_getcsv($content, "\n"), $io
            );
        }

        $io->success('Stock data updated successfully!');
        return Command::SUCCESS;
    }

    public function processCsvContent(array $rows, SymfonyStyle $io): void
    {
        $progressBar = $io->createProgressBar(count($rows) - 1);
        $progressBar->start();
        $progressBar->setBarCharacter('<comment>=</comment>');
        $progressBar->setProgressCharacter('|');

        foreach ($rows as $key => $row) {
            if ($key == 0) { continue; }

            $columns = str_getcsv($row);
            $sku = $columns[0];
            $quantity = $columns[1];
            $location = $columns[2];

            try {
                $this->stockImportManagement->createOrUpdateStock($sku, $quantity, $location);
                $progressBar->advance();
            } catch (RuntimeException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $progressBar->finish();
    }
}
