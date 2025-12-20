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
     * @param RuleSetInterface $ruleSet The set of standards to validate against.
     * @return array<int, array{setting: string, expected: string, current: string, pass: bool}>
     * @throws \RuntimeException If file is unreadable or parser fails.
     */
    public function scanFile(string $filePath, RuleSetInterface $ruleSet): array
    {
        if (!is_readable($filePath)) {
            throw new \RuntimeException("Cannot read file: {$filePath}");
        }

        // parse_ini_file ignores CLI's active environment settings.
        $fileSettings = parse_ini_file($filePath, false, INI_SCANNER_TYPED);

        if ($fileSettings === false) {
            throw new \RuntimeException("Failed to parse ini file: {$filePath}");
        }

        $results = [];
        $rules = $ruleSet->getRules();

        foreach ($rules as $setting => $expectedValue) {
            // Check if the setting exists in the file, otherwise it's an empty string/null.
            $rawValue = isset($fileSettings[$setting]) ? (string) $fileSettings[$setting] : '';
            $normalizedCurrent = $this->normalizeValue($rawValue);
            $normalizedExpected = $this->normalizeValue($expectedValue);
            $results[] = [
                'setting' => $setting,
                'expected' => $normalizedExpected,
                'current' => $normalizedCurrent,
                'pass' => $normalizedCurrent === $normalizedExpected,
            ];
        }

        return $results;
    }

    /**
     * Normalizes php.ini values into a consistent string format.
     *
     * Converts 'Off', empty strings, and '0' to '0', and 'On' or '1' to '1'.
     *
     * @param string $value The raw configuration value to normalize.
     * @return string Normalized value as '0', '1', or a lowercase string.
     */
    private function normalizeValue(string $value): string
    {
        if ($value === '' || strcasecmp($value, 'Off') === 0 || $value === '0') {
            return '0';
        }

        if (strcasecmp($value, 'On') === 0 || $value === '1') {
            return '1';
        }

        return strtolower($value);
    }
}
