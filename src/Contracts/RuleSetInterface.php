<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Contracts;

interface RuleSetInterface
{
    /**
     * Returns name of rule set ('Production', 'Development').
     */
    public function getName(): string;

    /**
     * Returns an associative array of php.ini keys and their expected values.
     *
     * @return array<string, string>
     */
    public function getRules(): array;
}
