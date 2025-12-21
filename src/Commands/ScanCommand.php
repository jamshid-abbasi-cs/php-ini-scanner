<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Commands;

use Yousha\PhpIniScanner\Config\DevelopmentRules;
use Yousha\PhpIniScanner\Config\ProductionRules;
use Yousha\PhpIniScanner\Output\ConsoleRenderer;
use Yousha\PhpIniScanner\Scanner\ScannerEngine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ScanCommand extends Command
{
    protected static $defaultName = 'scan';

    private ScannerEngine $scanner;

    private ConsoleRenderer $renderer;

    /**
     * Initializes command with its required engine and renderer dependencies.
     */
    public function __construct()
    {
        $this->scanner = new ScannerEngine();
        $this->renderer = new ConsoleRenderer();
        parent::__construct();
    }

    /**
     * Configures scan command properties, options, and help text.
     */
    protected function configure(): void
    {
        $this->setDescription('Scans a php.ini file for development or production standards.')
            ->addOption('development', 'd', InputOption::VALUE_NONE, 'Check against Development standards.')
            ->addOption('production', 'p', InputOption::VALUE_NONE, 'Check against Production standards.')
            ->addOption('ini-path', 'i', InputOption::VALUE_REQUIRED, 'Path to the php.ini file to scan.');
    }

    /**
     * Executes scanner logic based on provided CLI input.
     *
     * @param InputInterface $input Command input stream.
     * @param OutputInterface $output Command output stream.
     * @return int Command::SUCCESS (0) if all checks pass, otherwise Command::FAILURE (1).
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $isDev = $input->getOption('development');
        $isProd = $input->getOption('production');
        $phpIniPath = $input->getOption('ini-path');

        if (!$isDev && !$isProd) {
            $output->writeln('<error>Error: You must specify a mode: -d or -p</error>');
            return Command::FAILURE;
        }

        if (!$phpIniPath) {
            $phpIniPath = php_ini_loaded_file();
            $output->writeln("<comment>No php.ini provided. Detecting system's default: {$phpIniPath}</comment>");
        }

        if (!$phpIniPath || !file_exists($phpIniPath)) {
            $output->writeln("<error>Error: Valid ini file path required via --ini-path or -i</error>");
            return Command::FAILURE;
        }

        $rules = $isProd ? new ProductionRules() : new DevelopmentRules();

        try {
            $results = $this->scanner->scanFile($phpIniPath, $rules);
            $this->renderer->render($output, $results, $rules->getName());
        } catch (\Exception $exception) {
            $output->writeln('<error>Error: ' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }

        foreach ($results as $result) {
            if ($result['pass'] === false) {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
