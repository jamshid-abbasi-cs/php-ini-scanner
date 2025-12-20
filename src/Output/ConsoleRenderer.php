<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Output;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleRenderer
{
    /**
     * Renders scan results in a formatted table via CLI.
     *
     * @param OutputInterface $output Command output stream.
     * @param array $results Numerical array of scan result rows.
     * @param string $mode The environment mode name.
     */
    public function render(OutputInterface $output, array $results, string $mode): void
    {
        $output->writeln(['', "<info>Scan result of php.ini for <comment>{$mode}</comment> environment...</info>", '']);
        $table = new Table($output);
        $table->setHeaders(['Configuration', 'Expected', 'Current', 'Status']);
        $passCount = 0;

        foreach ($results as $row) {
            $status = $row['pass'] ? '<info>PASS</info>' : '<error>FAIL</error>';
            if ($row['pass']) {
                $passCount++;
            }

            $table->addRow([
                $row['setting'],
                $row['expected'],
                $row['current'], // Now displays "0" or "1" accurately.
                $status,
            ]);
        }

        $table->render();
        $total = count($results);
        $failed = $total - $passCount;
        $output->writeln(['', "Checked: {$total} | Passed: <info>{$passCount}</info> | Failed: <error>{$failed}</error>", '']);
    }
}
