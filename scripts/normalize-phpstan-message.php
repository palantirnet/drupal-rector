<?php

declare(strict_types=1);

/**
 * Normalize a raw PHPStan deprecation message into the form
 * upgrade_status's DeprecationAnalyzer::categorizeMessage() compares against.
 *
 * upgrade_status applies these three transforms before reaching
 * isRectorCovered():
 *
 *   1. trim + collapse whitespace runs to a single space
 *   2. ":\s+(in|as of)"  →  ". Deprecated \1"
 *   3. "Use \Drupal..."  →  "Use Drupal..."  (strip leading backslash)
 *
 * Any message we store in a PHPSTAN_MESSAGES const (or feed into the
 * upgrade_status hardcoded list) must be normalized the same way, or the
 * exact in_array() lookup will silently miss.
 *
 * Reference: web/modules/contrib/upgrade_status/src/DeprecationAnalyzer.php
 * lines 600-604 (in any installed upgrade_status >= 4.x).
 *
 * Usage:
 *   php scripts/normalize-phpstan-message.php "<raw message>"
 *   echo "<raw>" | php scripts/normalize-phpstan-message.php
 */
function normalizePhpstanMessage(string $error): string
{
    // 1. trim + collapse runs of whitespace
    $error = preg_replace('!\s+!', ' ', trim($error));
    // 2. ": in" / ": as of"  →  ". Deprecated in" / ". Deprecated as of"
    $error = preg_replace('!:\s+(in|as of)!', '. Deprecated \1', $error);
    // 3. "Use \Drupal..."  →  "Use Drupal..."
    $error = preg_replace('!(u|U)se \\\\Drupal!', '\1se Drupal', $error);

    return $error;
}

if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    $input = $argv[1] ?? stream_get_contents(STDIN);
    if ($input === false || $input === '') {
        fwrite(STDERR, "Usage: php scripts/normalize-phpstan-message.php \"<raw message>\"\n");
        fwrite(STDERR, "       echo \"<raw>\" | php scripts/normalize-phpstan-message.php\n");
        exit(2);
    }
    echo normalizePhpstanMessage($input).PHP_EOL;
}
