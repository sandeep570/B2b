<?php

namespace Cminds\MultiUserAccounts\Console\Command;

use Cminds\MultiUserAccounts\Model\Import;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cminds MultiUserAccounts import console command.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class ImportCommand extends Command
{
    const FILE_ARGUMENT = 'file';

    const DEFAULT_FILE_PATH = 'var/import/';
    const DEFAULT_FILE_NAME = 'subaccounts_import.csv';

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var Import
     */
    private $import;

    /**
     * Object constructor.
     *
     * @param DirectoryList $directoryList
     * @param Import        $import
     */
    public function __construct(
        DirectoryList $directoryList,
        Import $import
    ) {
        $this->directoryList = $directoryList;
        $this->import = $import;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('cminds:multiuseraccounts:import')
            ->setDescription('Import parent account with their subaccounts')
            ->setDefinition([
                new InputArgument(
                    self::FILE_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'File'
                ),
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     * @throws \Exception
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $filePath = $input->getArgument(self::FILE_ARGUMENT);
        if (!$filePath) {
            $filePath = self::DEFAULT_FILE_PATH . self::DEFAULT_FILE_NAME;
        }

        $relativeFilePath = $filePath;
        $absoluteFilePath = $this->directoryList->getRoot() . '/' . $relativeFilePath;

        $output->writeln('<info>Looking for a import file...<info>');

        if (!file_exists($absoluteFilePath)) {
            $output->writeln('<error>File ' . $relativeFilePath . ' was not found</error>');
            $output->writeln(
                '<error>You can point your import file by providing relative '
                . 'path to it as a argument or use default path and name</error>'
            );

            return -1;
        }

        try {
            $this->import
                ->setEnvironment(Import::ENVIRONMENT_CLI)
                ->setOutputStream($output)
                ->initSourceProcessor(Import::SOURCE_CSV)
                ->setFilePath($absoluteFilePath);

            $output->writeln('<info>Import file has been found, processing...<info>');

            $this->import->process();
        } catch (LocalizedException $e) {
            $output->writeln('<error>During import process error has occured.</error>');
            $output->writeln('<error>Details: ' . $e->getMessage() . '</error>');

            return -1;
        }

        $output->writeln('<info>Import has been finished<info>');

        return 0;
    }
}
