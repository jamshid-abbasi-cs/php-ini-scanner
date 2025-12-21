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
            // --- Core ---
            'engine' => 'On',
            'short_open_tag' => 'Off',

            // --- Error Handling ---
            'error_reporting' => 'E_ALL', // Error engine.
            'display_errors' => 'Off',
            'log_errors' => 'On',
            'display_startup_errors' => 'Off',
            'html_errors' => 'Off',
            'zend.exception_ignore_args' => 'On', // Crucial for security.

            // --- Resources ---
            'memory_limit' => '256M', // Restrictive default.
            'max_execution_time' => '30',
            'max_input_time' => '60',
            'post_max_size' => '16M',
            'max_input_vars' => '1024', // DoS protection.

            // --- Session ---
            'session.cookie_secure' => 'On',
            'session.cookie_httponly' => 'On',
            'session.use_strict_mode' => 'On',
            'session.use_trans_sid' => 'Off', // Security risk.

            // --- Information Disclosure ---
            'expose_php' => 'Off', // Security risk.
            'mail.add_x_header' => 'Off', // Security risk. Hide script paths in emails.

            // --- File/Network ---
            'allow_url_fopen' => 'Off', // Security risk.
            'allow_url_include' => 'Off', // Security risk.
            'cgi.fix_pathinfo' => 'Off', // Security risk.
            'mysqli.allow_local_infile' => 'Off', // Security risk. Prevent arbitrary file reads via SQL.

            // --- Performance ---
            'opcache.enable' => 'On',
            'opcache.log_verbosity_level' => '2',
            'opcache.validate_timestamps' => 'On',
            'zlib.output_compression' => 'On',

            // --- Other ---
            'fastcgi.logging' => 'Off',
            'enable_dl' => 'Off',
            'date.timezone' => 'UTC',
        ];

        if (PHP_VERSION_ID >= 80000 && PHP_VERSION_ID <= 80400) {
            $rules['opcache.jit'] = 'disable';
        }

        if (PHP_VERSION_ID >= 80000) {
            $rules['opcache.record_warnings'] = 'On';
            $rules['opcache.jit_buffer_size'] = '128M'; // Need buffer for JIT to work.
            $rules['zend.exception_string_param_max_len'] = '0'; // Unlimited.
        }

        if (PHP_VERSION_ID >= 80100) {
            $rules['opcache.interned_strings_buffer'] = '16'; // Increased from default
        }

        // Log permissions.
        if (PHP_VERSION_ID >= 80200) {
            $rules['error_log_mode'] = '0600'; // Owner RW only.
        }

        if (PHP_VERSION_ID >= 80300) {
            $rules['session.sid_length'] = '48';
        }

        if (PHP_VERSION_ID <= 80400) {
            $rules['report_memleaks'] = 'On';
        }

        return $rules;
    }
}
