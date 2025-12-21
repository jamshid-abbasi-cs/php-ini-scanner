<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Scanner;

use Yousha\PhpIniScanner\Contracts\RuleSetInterface;

final class ScannerEngine
{
    /**
     * Parses target ini file and compares its values against provided rule set.
     *
     * @param string $filePath Path to php.ini file to be audited.
     * @param RuleSetInterface $standardRuleSet The set of standards to validate against.
     * @return array<int, array{setting: string, expected: string, current: string, pass: bool}>
     * @throws \RuntimeException If file is unreadable or parser fails.
     */
    public function scanFile(string $filePath, RuleSetInterface $standardRuleSet): array
    {
        if (!is_readable($filePath)) {
            throw new \RuntimeException("Cannot read file: {$filePath}");
        }

        // parse_ini_file ignores CLI's active environment settings.
        //parse_ini_file
        $phpConfigs = parse_ini_file($filePath, false, INI_SCANNER_RAW);

        if ($phpConfigs === false) {
            throw new \RuntimeException("Failed to parse ini file: {$filePath}");
        }

        if ($phpConfigs === []) {
            throw new \RuntimeException('No PHP configuration were found in the file.');
        }

        $results = [];
        $standardRules = $standardRuleSet->getRules();

        foreach ($standardRules as $setting => $expectedValue) {
            // Check if the setting exists in the file, otherwise it's an empty string/null.
            $normalizedCurrentValue = isset($phpConfigs[$setting]) ? $this->normalizeValue($phpConfigs[$setting]) : '<comment>[NOT SET]</comment>';
            $normalizedExpectedValue = $this->normalizeValue($expectedValue);
            $results[] = [
                'setting' => $setting,
                'current' => $normalizedCurrentValue,
                'expected' => $normalizedExpectedValue,
                'pass' => $normalizedCurrentValue === $normalizedExpectedValue,
            ];
        }

        return $results;
    }

    /**
     * Normalizes php.ini values into a consistent string format.
     *
     * @param string $value The raw configuration value to normalize.
     * @return string Normalized value as '0', '1', or a lowercase string.
     */
    private function normalizeValue(string $value): string
    {
        if (in_array($value, ['off', '0', 'false', 'no'], true)) {
            return 'off';
        }

        if (in_array($value, ['on', '1', 'true', 'yes'], true)) {
            return 'on';
        }

        return strtolower($value);
    }
}
