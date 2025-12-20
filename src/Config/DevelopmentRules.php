<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Config;

use Yousha\PhpIniScanner\Contracts\RuleSetInterface;

final class DevelopmentRules implements RuleSetInterface
{
    /**
     * Returns human-readable name of rule set.
     *
     * @return string The rule set identifier ('Development').
     */
    public function getName(): string
    {
        return 'Development';
    }

    /**
     * Retrieves configuration directives and their target values for development.
     *
     * @return array<string, string> Key-value pairs of directive names and expected values.
     */
    public function getRules(): array
    {
        $rules = [
            // --- Core Error Handling ---
            'error_reporting' => (string) E_ALL, // Error engine.
            'display_errors' => 'On',
            'log_errors' => 'On',
            'display_startup_errors' => 'On',
            'html_errors' => 'Off',
            'zend.exception_ignore_args' => 'Off',
            'report_memleaks' => 'On',

            // --- Resources ---
            'memory_limit' => '128M', // Generous for dev.
            'max_execution_time' => '120', // Longer for debugging.
            'max_input_time' => '60',
            'post_max_size' => '8M',
            'max_input_vars' => '1024', // DoS protection.

            // --- Session ---
            'session.cookie_secure' => '0', // Often no HTTPS on localhost.
            'session.cookie_httponly' => '1',
            'session.use_strict_mode' => '1',

            // --- Information Disclosure ---
            'expose_php' => 'On', // Useful to see version in dev.
            'mail.add_x_header' => 'On', // Useful to debug in dev.

            // --- File/Network ---
            'allow_url_fopen' => '0', // To match with production envs.
            'allow_url_include' => '0', // To match with production envs.
            'mysqli.allow_local_infile' => 'Off', // Security risk. Prevent arbitrary file reads via SQL.

            // --- Performance ---
            'opcache.enable' => '0', // Disable caching for hot reloading.
            'opcache.enable_cli' => '0',
            'opcache.log_verbosity_level' => '2',

            // --- Other ---
            'fastcgi.logging' => '0',
            'date.timezone' => 'UTC',
        ];

        // PHP 8.0+
        if (PHP_VERSION_ID >= 80000) {
            $rules['opcache.jit'] = 'disable';
        }

        // PHP 8.2+ log permissions.
        if (PHP_VERSION_ID >= 80200) {
            $rules['error_log_mode'] = '0600'; // Owner rw only.
        }

        // PHP 8.3+
        if (PHP_VERSION_ID >= 80300) {
            $rules['opcache.record_warnings'] = '1';
        }

        return $rules;
    }
}
