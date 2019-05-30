<?php

namespace Cminds\MultiUserAccounts\Console\Command;

use Cminds\MultiUserAccounts\Model\Import\Validator;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cminds MultiUserAccounts import console command.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class GenerateSampleImportFileCommand extends Command
{
    const SHOW_FIELDS_OPTION = 'show-fields';

    const SAMPLE_FILE_PATH = 'var/import/';
    const SAMPLE_FILE_NAME = 'sample_subaccounts_import.csv';

    /**
     * @var Validator
     */
    private $importValidator;

    /**
     * @var Csv
     */
    private $csvProcessor;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * Object constructor.
     *
     * @param Validator     $importValidator
     * @param Csv           $csvProcessor
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Validator $importValidator,
        Csv $csvProcessor,
        DirectoryList $directoryList
    ) {
        $this->importValidator = $importValidator;
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;

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
            ->setName('cminds:multiuseraccounts:generate-sample-import-file')
            ->setDescription('Generate parent accounts and sub-accounts sample import file')
            ->setDefinition([
                new InputOption(
                    self::SHOW_FIELDS_OPTION,
                    '-f',
                    InputOption::VALUE_NONE,
                    'Display available fields'
                ),
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $keys = $this->importValidator->getKeys();

        $showFields = $input->getOption(self::SHOW_FIELDS_OPTION);
        if ($showFields) {
            $output->writeln('<info>Available fields:</info>');
            $output->writeln(implode("\n", $keys));

            return 0;
        }

        $output->writeln('<info>Generating file...</info>');

        $relativePath = self::SAMPLE_FILE_PATH;
        $absolutePath = $this->directoryList->getRoot() . '/' . $relativePath;

        if (@mkdir($absolutePath, 0777, true)
            && !is_dir($absolutePath)
        ) {
            $output->writeln('<error>Unable to create path ' . $relativePath . '</error>');
        }

        $this->csvProcessor
            ->setDelimiter(',')
            ->setEnclosure('"')
            ->saveData($absolutePath . self::SAMPLE_FILE_NAME, [$keys]);

        $output->writeln(
            'File ' . $relativePath . self::SAMPLE_FILE_NAME . ' has been generated.'
        );

        return 0;
    }
}
