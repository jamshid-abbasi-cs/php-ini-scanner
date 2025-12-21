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
            // --- Core ---
            'engine' => 'On',
            'short_open_tag' => 'Off',

            // --- Error Handling ---
            'error_reporting' => 'E_ALL', // Error engine.
            'display_errors' => 'On',
            'log_errors' => 'On',
            'display_startup_errors' => 'On',
            'html_errors' => 'Off',
            'zend.exception_ignore_args' => 'Off',

            // --- Resources ---
            'memory_limit' => '128M', // Generous for dev.
            'max_execution_time' => '120', // Longer for debugging.
            'max_input_time' => '60',
            'post_max_size' => '8M',
            'max_input_vars' => '1024', // DoS protection.

            // --- Session ---
            'session.cookie_secure' => 'Off', // Often no HTTPS on localhost.
            'session.cookie_httponly' => 'On',
            'session.use_strict_mode' => 'On',
            'session.use_trans_sid' => 'Off', // Security risk.

            // --- Information Disclosure ---
            'expose_php' => 'On', // Useful to see version in dev.
            'mail.add_x_header' => 'On', // Useful to debug in dev.

            // --- File/Network ---
            'allow_url_fopen' => 'Off', // To match with production envs.
            'allow_url_include' => 'Off', // To match with production envs.
            'cgi.fix_pathinfo' => 'Off', // Security risk.
            'mysqli.allow_local_infile' => 'Off', // Security risk. Prevent arbitrary file reads via SQL.

            // --- Performance ---
            'opcache.enable' => 'Off', // Disable caching for hot reloading.
            'opcache.enable_cli' => 'Off',
            'opcache.log_verbosity_level' => '2',

            // --- Other ---
            'fastcgi.logging' => 'On',
            'enable_dl' => 'Off',
            'date.timezone' => 'UTC',
        ];

        if (PHP_VERSION_ID >= 80000 && PHP_VERSION_ID <= 80400) {
            $rules['opcache.jit'] = 'disable';
        }

        if (PHP_VERSION_ID >= 80000) {
            $rules['opcache.record_warnings'] = 'Off';
            $rules['zend.exception_string_param_max_len'] = '15';
        }

        // Log permissions.
        if (PHP_VERSION_ID >= 80200) {
            $rules['error_log_mode'] = '0600'; // Owner RW only.
        }

        if (PHP_VERSION_ID <= 80300) {
            $rules['session.sid_length'] = '48';
        }

        if (PHP_VERSION_ID <= 80400) {
            $rules['report_memleaks'] = 'On';
        }

        return $rules;
    }
}
