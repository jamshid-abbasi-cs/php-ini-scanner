<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Config;

use Yousha\PhpIniScanner\Contracts\RuleSetInterface;

final class ProductionRules implements RuleSetInterface
{
    /**
     * Returns human-readable name of rule set.
     *
     * @return string The rule set identifier ('Production').
     */
    public function getName(): string
    {
        return 'Production';
    }

    /**
     * Retrieves configuration directives and their target values for production.
     *
     * @return array<string, string> Key-value pairs of directive names and expected values.
     */
    public function getRules(): array
    {
        $rules = [
            // --- Core Error Handling ---
            'error_reporting' => (string) E_ALL, // Error engine.
            'display_errors' => 'Off',
            'log_errors' => 'On',
            'display_startup_errors' => 'Off',
            'html_errors' => 'Off',
            'zend.exception_ignore_args' => 'On', // Crucial for security.

            // --- Resources ---
            'memory_limit' => '512M', // Restrictive default.
            'max_execution_time' => '30',
            'max_input_time' => '60',
            'post_max_size' => '16M',
            'max_input_vars' => '1024', // DoS protection.

            // --- Session ---
            'session.cookie_secure' => '1',
            'session.cookie_httponly' => '1',
            'session.use_strict_mode' => '1',

            // --- Information Disclosure ---
            'expose_php' => 'Off', // Security risk.
            'mail.add_x_header' => 'Off', // Security risk. Hide script paths in emails.

            // --- File/Network ---
            'allow_url_fopen' => 'Off', // Security risk.
            'allow_url_include' => 'Off', // Security risk.
            'mysqli.allow_local_infile' => 'Off', // Security risk. Prevent arbitrary file reads via SQL.

            // --- Performance ---
            'opcache.enable' => '1',
            'opcache.log_verbosity_level' => '2',
            'opcache.validate_timestamps' => '1',

            // --- Other ---
            'fastcgi.logging' => '0',
            'date.timezone' => 'UTC',
        ];

        // PHP 8.0+ JIT.
        if (PHP_VERSION_ID >= 80000) {
            $rules['opcache.jit'] = 'tracing';
            $rules['opcache.jit_buffer_size'] = '128M'; // Need buffer for JIT to work.
        }

        // PHP 8.2+ Log Permissions.
        if (PHP_VERSION_ID >= 80200) {
            $rules['error_log_mode'] = '0600'; // Owner rw only.
        }

        // PHP 8.3+
        if (PHP_VERSION_ID >= 80300) {
            $rules['opcache.record_warnings'] = '0';
        }

        return $rules;
    }
}
