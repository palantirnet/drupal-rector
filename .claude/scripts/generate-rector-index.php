#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generates docs/rector-index.yml — single source of truth for D11 rector status.
 *
 * Usage: php .claude/scripts/generate-rector-index.php [--digests-path=PATH]
 *
 * Options:
 *   --digests-path=PATH  Path to drupal-digests repo (default: repos//drupal-digests)
 *   --help               Show this help
 */

const GENERIC_RECTORS = [
    'FunctionToServiceRector'          => '1a',
    'FunctionToFirstArgMethodRector'   => '1a',
    'DrupalServiceRenameRector'        => '1a',
    'FunctionToStaticRector'           => '1b',
    'ClassConstantToClassConstantRector' => '1c',
    'ConstantToClassConstantRector'    => '1c',
    'MethodToMethodWithCheckRector'    => '2',
    'DeprecationHelperRemoveRector'    => '2',
    'FunctionCallRemovalRector'        => '3',
    'RenameClassRector'                => 'unknown',
];

// Files with these prefixes add new capability (constructor args, type hints, DI wiring)
// rather than removing deprecated APIs. Keep them out of scope even when they mention @deprecated.
const FORWARD_COMPAT_PREFIXES = ['add-', 'fix-', 'guard-', 'pass-', 'update-'];

main($argv);

function main(array $argv): void
{
    $repoRoot = dirname(dirname(__DIR__));
    $digestsPath = $repoRoot . '/repos/drupal-digests';

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help') {
            echo file_get_contents(__FILE__);
            exit(0);
        }
        if (str_starts_with($arg, '--digests-path=')) {
            $digestsPath = expandPath(substr($arg, 15));
        }
    }
    $rulesDir = $digestsPath . '/rector/rules';

    if (!is_dir($rulesDir)) {
        fprintf(STDERR, "Error: digests rules directory not found: %s\n", $rulesDir);
        fprintf(STDERR, "Pass --digests-path=PATH to specify the drupal-digests repo location.\n");
        exit(1);
    }

    // Step 1: Build base entries from in-scope digest files.
    $entries = scanDigestFiles($rulesDir);

    // Step 2: Mark implemented custom classes — scan src/Drupal*/Rector/Deprecation dirs, newest first.
    $srcDirs = array_filter(
        glob($repoRoot . '/src/Drupal*/Rector/Deprecation', GLOB_ONLYDIR),
        fn($d) => preg_match('#/Drupal(\d+)/#', $d, $m) && (int) $m[1] >= 10
    );
    rsort($srcDirs);
    $unmatched = [];
    foreach ($srcDirs as $srcDir) {
        $unmatched += scanImplementedClasses($srcDir, $repoRoot, $entries);
    }

    // Build a reverse lookup: shortClassName => {class, files} for all known implemented classes.
    // Used in Step 3 so config files can link pending issues to existing custom rectors.
    $implementedClasses = buildClassMap($entries, $unmatched);

    // Step 3: Mark config-only / implemented entries found in any config/* subdir.
    scanConfigFiles($repoRoot . '/config', $entries, $implementedClasses);

    // Step 4: Add unmatched custom classes (no digest file found).
    foreach ($unmatched as $className => $data) {
        $entries['class_' . $className] = $data;
    }

    // Step 5: Write YAML.
    $outputFile = $repoRoot . '/docs/rector-index.yml';
    writeYaml($entries, $outputFile);

    $counts = countByStatus($entries);
    printf(
        "Wrote %s\n  implemented: %d  config-only: %d  pending: %d\n",
        $outputFile,
        $counts['implemented'],
        $counts['config-only'],
        $counts['pending']
    );
}

function expandPath(string $path): string
{
    if (str_starts_with($path, '~/')) {
        $home = $_SERVER['HOME'] ?? getenv('HOME') ?: '';
        return $home . '/' . substr($path, 2);
    }
    return $path;
}

/**
 * Scans in-scope digest files, returns array keyed by issue number.
 *
 * @return array<string, array<string, mixed>>
 */
function scanDigestFiles(string $rulesDir): array
{
    $entries = [];

    foreach (glob($rulesDir . '/*.php') as $file) {
        $filename = basename($file);

        $issueNumber = extractIssueNumber($filename);
        if ($issueNumber === null) {
            continue;
        }

        $content = file_get_contents($file);

        if (!isDeprecationDigest($filename, $content)) {
            continue;
        }

        $phase = classifyPhaseFromDigest($content, $filename);

        $entries[$issueNumber] = [
            'issue'       => $issueNumber,
            'digest_file' => $filename,
            'phase'       => $phase,
            'status'      => 'pending',
            'class'       => null,
            'files'       => [],
        ];
    }

    ksort($entries);
    return $entries;
}

function extractIssueNumber(string $filename): ?string
{
    // Last hyphen-separated numeric group before .php
    if (preg_match('/-(\d+)\.php$/', $filename, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Returns true when a digest file documents a Drupal deprecation or removal.
 *
 * Two-pass check:
 * 1. Canonical prefixes (replace-deprecated-*, remove-deprecated-*, replace-removed-*,
 *    strip-removed-*) are always in scope — this includes Twig/library deprecations
 *    that don't use the "@deprecated drupal:" marker.
 * 2. Non-canonical prefixes are included when the file content contains
 *    "@deprecated drupal:" or "deprecated in drupal:" — the standard Drupal core
 *    notation. This catches files like "rename-deprecated-*", "replace-filesysteminterface-*",
 *    "remove-overrides-of-deprecated-*", etc.
 *
 * Exception: files with forward-compatibility prefixes (add new constructor args,
 * fix type signatures, etc.) are excluded even when they mention a deprecated signature.
 */
function isDeprecationDigest(string $filename, string $content): bool
{
    // Always in scope: canonical deprecation/removal/rename prefixes.
    // rename- is included unconditionally because renaming a class or hook is inherently
    // a breaking API change — there is no "forward-compat" meaning for a rename.
    if (preg_match('/^(replace-deprecated|remove-deprecated|replace-removed|strip-removed|rename-)/', $filename)) {
        return true;
    }

    // Never in scope: forward-compatibility files (add args, fix signatures, etc.).
    foreach (FORWARD_COMPAT_PREFIXES as $prefix) {
        if (str_starts_with($filename, $prefix)) {
            return false;
        }
    }

    // Also in scope: non-canonical prefix but explicitly Drupal-deprecated APIs.
    return str_contains($content, '@deprecated drupal:')
        || str_contains($content, 'deprecated in drupal:');
}

/**
 * Classifies phase from digest file content by inspecting getNodeTypes().
 */
function classifyPhaseFromDigest(string $content, string $filename): string
{
    if (!preg_match('/getNodeTypes\s*\(\)[^{]*\{[^}]*return\s*\[([^\]]+)\]/s', $content, $matches)) {
        // No getNodeTypes found — fall back on filename prefix.
        return classifyPhaseFromFilename($filename);
    }

    $nodeTypes = $matches[1];

    $hasFuncCall      = str_contains($nodeTypes, 'FuncCall');
    $hasMethodCall    = str_contains($nodeTypes, 'MethodCall') || str_contains($nodeTypes, 'NullsafeMethodCall');
    $hasStaticCall    = str_contains($nodeTypes, 'StaticCall');
    $hasClassConst    = str_contains($nodeTypes, 'ClassConstFetch') || str_contains($nodeTypes, 'ConstFetch');
    $hasExpression    = str_contains($nodeTypes, 'Expression');
    $hasRemoveNode    = str_contains($content, 'REMOVE_NODE');

    // Phase 3: removes the node entirely.
    if ($hasRemoveNode || ($hasExpression && !$hasFuncCall && !$hasMethodCall && !$hasStaticCall)) {
        return '3';
    }

    // Pure FuncCall — Phase 1a or 1b.
    if ($hasFuncCall && !$hasMethodCall && !$hasStaticCall && !$hasClassConst) {
        return classifyFuncCallSubPhase($content);
    }

    // ClassConstFetch / ConstFetch — Phase 1c.
    if ($hasClassConst && !$hasFuncCall && !$hasMethodCall && !$hasStaticCall) {
        return '1c';
    }

    // MethodCall/NullsafeMethodCall — Phase 2.
    if ($hasMethodCall && !$hasFuncCall && !$hasStaticCall && !$hasClassConst && !$hasExpression) {
        return '2';
    }

    // StaticCall — Phase 2 (custom transformation of a typed static call, same complexity as MethodCall).
    if ($hasStaticCall && !$hasFuncCall && !$hasMethodCall && !$hasClassConst && !$hasExpression) {
        return '2';
    }

    // Multiple or ambiguous — Phase 4.
    return '4';
}

function classifyFuncCallSubPhase(string $content): string
{
    // Service call pattern: ::service( or ->service( or \Drupal::service.
    if (preg_match('/\\\\?Drupal::service\b|->service\s*\(/i', $content)) {
        return '1a';
    }
    // Static call pattern: SomeClass::method().
    if (str_contains($content, '::')) {
        return '1b';
    }
    return '1a';
}

function classifyPhaseFromFilename(string $filename): string
{
    if (str_starts_with($filename, 'remove-deprecated') || str_starts_with($filename, 'strip-removed')) {
        return '3';
    }
    return 'unknown';
}

/**
 * Scans custom rector classes, updates entries in-place, returns unmatched classes.
 *
 * @param  array<string, array<string, mixed>> $entries
 * @return array<string, array<string, mixed>>
 */
function scanImplementedClasses(string $srcDir, string $repoRoot, array &$entries): array
{
    $unmatched = [];

    $relSrcDir  = ltrim(str_replace($repoRoot, '', $srcDir), '/');
    $relTestDir = preg_replace('#^src/(Drupal\d+)#', 'tests/src/$1', $relSrcDir);

    foreach (glob($srcDir . '/*.php') as $file) {
        $content = file_get_contents($file);

        if (!preg_match('/class\s+(\w+)\s+extends/', $content, $classMatch)) {
            continue;
        }
        $className = $classMatch[1];

        $issueNumber = extractIssueFromSeeUrl($content);

        $phase = classifyPhaseFromClass($content);

        $relFile = $relSrcDir . '/' . basename($file);
        $files   = [$relFile];

        // Add test file path if directory exists.
        $testDir = $repoRoot . '/' . $relTestDir . '/' . $className;
        if (is_dir($testDir)) {
            $files[] = $relTestDir . '/' . $className . '/' . $className . 'Test.php';
        }

        if ($issueNumber !== null && isset($entries[$issueNumber])) {
            $existing = $entries[$issueNumber];
            $entries[$issueNumber]['status'] = 'implemented';
            $entries[$issueNumber]['phase']  = $existing['phase'] === 'unknown' ? $phase : $existing['phase'];

            // Support multiple classes per issue (rare but occurs).
            if ($existing['status'] === 'implemented' && !empty($existing['class'])) {
                $prev = is_array($existing['class']) ? $existing['class'] : [$existing['class']];
                $entries[$issueNumber]['class'] = array_values(array_unique(array_merge($prev, [$className])));
                $entries[$issueNumber]['files'] = array_values(array_unique(array_merge($existing['files'], $files)));
            } else {
                $entries[$issueNumber]['class'] = $className;
                $entries[$issueNumber]['files'] = $files;
            }
        } else {
            // No matching digest entry — store for later.
            $unmatched[$className] = [
                'issue'       => $issueNumber ?? 'unknown',
                'digest_file' => null,
                'phase'       => $phase,
                'status'      => 'implemented',
                'class'       => $className,
                'files'       => $files,
            ];
        }
    }

    return $unmatched;
}

function extractIssueFromSeeUrl(string $content): ?string
{
    if (preg_match('#@see\s+https?://www\.drupal\.org/node/(\d+)#i', $content, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Classifies phase from a rector class file by inspecting getNodeTypes().
 */
function classifyPhaseFromClass(string $content): string
{
    $isAbstractDrupalCore = str_contains($content, 'AbstractDrupalCoreRector');
    $hasRemoveNode = str_contains($content, 'REMOVE_NODE');

    if (!preg_match('/getNodeTypes\s*\(\)[^{]*\{[^}]*return\s*\[([^\]]+)\]/s', $content, $matches)) {
        return 'unknown';
    }

    $nodeTypes = $matches[1];
    $hasMethodCall = str_contains($nodeTypes, 'MethodCall') || str_contains($nodeTypes, 'NullsafeMethodCall');
    $hasExpression = str_contains($nodeTypes, 'Expression');

    if ($hasRemoveNode || $hasExpression) {
        return '3';
    }

    if ($hasMethodCall) {
        return $isAbstractDrupalCore ? '2' : '2';
    }

    return '4';
}

/**
 * Builds a map of shortClassName => {class, files} for all currently-implemented entries
 * plus unmatched classes. Used to resolve custom rectors referenced in config files.
 *
 * @param  array<string, array<string, mixed>> $entries
 * @param  array<string, array<string, mixed>> $unmatched
 * @return array<string, array<string, mixed>>
 */
function buildClassMap(array $entries, array $unmatched): array
{
    $map = [];

    foreach ($entries as $entry) {
        if ($entry['status'] !== 'implemented') {
            continue;
        }
        foreach ((array) $entry['class'] as $cls) {
            if ($cls !== null) {
                $map[$cls] = ['class' => $cls, 'files' => $entry['files']];
            }
        }
    }

    foreach ($unmatched as $className => $data) {
        $map[$className] = ['class' => $className, 'files' => $data['files']];
    }

    return $map;
}

/**
 * Scans all config/* subdirectories and marks config-only / implemented entries.
 *
 * @param array<string, array<string, mixed>> $entries
 * @param array<string, array<string, mixed>> $implementedClasses
 */
function scanConfigFiles(string $configRoot, array &$entries, array $implementedClasses): void
{
    foreach (glob($configRoot . '/*/', GLOB_ONLYDIR) as $configDir) {
        $dirName = basename(rtrim($configDir, '/'));
        foreach (glob($configDir . '*.php') as $configFile) {
            // Skip aggregate "all deprecations" bundle files.
            if (preg_match('/drupal-\d+-all-deprecations\.php$/', basename($configFile))) {
                continue;
            }
            $content = file_get_contents($configFile);
            $relFile  = 'config/' . $dirName . '/' . basename($configFile);
            parseConfigBlock($content, $relFile, $entries, $implementedClasses);
        }
    }
}

/**
 * Parses a config file, associating issue URL comments with rector class calls.
 *
 * Generic rectors (FunctionToServiceRector etc.) → config-only.
 * Custom rectors already found in src/ → implemented (linked via $implementedClasses).
 *
 * @param array<string, array<string, mixed>> $entries
 * @param array<string, array<string, mixed>> $implementedClasses  shortClassName => {class, files}
 */
function parseConfigBlock(string $content, string $relFile, array &$entries, array $implementedClasses): void
{
    $lines = explode("\n", $content);
    $pendingIssues = [];

    foreach ($lines as $line) {
        $trimmed = ltrim($line);

        // Collect issue URL comments.
        if (preg_match('#//\s+https?://www\.drupal\.org/node/(\d+)#', $trimmed, $m)) {
            $pendingIssues[] = $m[1];
            continue;
        }

        // On ruleWithConfiguration(...) or rule(...), process the pending issues.
        $isRuleCall = preg_match('/\$rectorConfig->ruleWithConfiguration\s*\(\s*([A-Za-z0-9_\\\\]+)::class/', $trimmed, $m)
            || preg_match('/\$rectorConfig->rule\s*\(\s*([A-Za-z0-9_\\\\]+)::class/', $trimmed, $m);

        if ($isRuleCall) {
            $shortClass = extractShortClassName($m[1]);

            if (isset(GENERIC_RECTORS[$shortClass])) {
                // Generic rector — mark as config-only.
                $phase = GENERIC_RECTORS[$shortClass];
                foreach ($pendingIssues as $issue) {
                    if (isset($entries[$issue]) && $entries[$issue]['status'] === 'pending') {
                        $entries[$issue]['status'] = 'config-only';
                        $entries[$issue]['phase']  = $entries[$issue]['phase'] === 'unknown' ? $phase : $entries[$issue]['phase'];
                        if (!in_array($relFile, $entries[$issue]['files'])) {
                            $entries[$issue]['files'][] = $relFile;
                        }
                    }
                }
            } elseif (isset($implementedClasses[$shortClass])) {
                // Custom rector already implemented in src/ — mark as implemented.
                $classData = $implementedClasses[$shortClass];
                foreach ($pendingIssues as $issue) {
                    if (isset($entries[$issue]) && $entries[$issue]['status'] === 'pending') {
                        $entries[$issue]['status'] = 'implemented';
                        $entries[$issue]['class']  = $classData['class'];
                        $entries[$issue]['files']  = $classData['files'];
                    }
                }
            }

            $pendingIssues = [];
            continue;
        }

        // Non-comment, non-ruleWithConfiguration line — clear pending if it's not blank/whitespace.
        if ($trimmed !== '' && !str_starts_with($trimmed, '//') && !str_starts_with($trimmed, '*')) {
            // Keep pending issues through blank lines and continuation comments.
            // Clear only when we hit something substantive that's NOT a rector call.
            if (!str_starts_with($trimmed, 'new ') && !str_starts_with($trimmed, ']);')) {
                $pendingIssues = [];
            }
        }
    }
}

function extractShortClassName(string $fqcn): string
{
    $parts = explode('\\', $fqcn);
    return end($parts);
}

/**
 * Writes the index as YAML.
 *
 * @param array<string, array<string, mixed>> $entries
 */
function writeYaml(array $entries, string $outputFile): void
{
    $counts = countByStatus($entries);

    $out = "# Generated by scripts/generate-rector-index.php — do not edit manually.\n";
    $out .= "# Re-generate with: php scripts/generate-rector-index.php\n";
    $out .= sprintf("generated: '%s'\n", date('Y-m-d H:i:s'));
    $out .= "counts:\n";
    $out .= sprintf("  implemented: %d\n", $counts['implemented']);
    $out .= sprintf("  config-only: %d\n", $counts['config-only']);
    $out .= sprintf("  pending: %d\n", $counts['pending']);
    $out .= "entries:\n";

    foreach ($entries as $key => $entry) {
        $out .= sprintf("  '%s':\n", $key);
        $out .= sprintf("    issue: '%s'\n", $entry['issue']);
        $out .= sprintf("    digest_file: %s\n", $entry['digest_file'] === null ? 'null' : "'" . $entry['digest_file'] . "'");
        $out .= sprintf("    phase: '%s'\n", $entry['phase']);
        $out .= sprintf("    status: %s\n", $entry['status']);
        if ($entry['class'] === null) {
            $out .= "    class: null\n";
        } elseif (is_array($entry['class'])) {
            $out .= "    class:\n";
            foreach ($entry['class'] as $cls) {
                $out .= sprintf("      - %s\n", $cls);
            }
        } else {
            $out .= sprintf("    class: %s\n", $entry['class']);
        }

        if (empty($entry['files'])) {
            $out .= "    files: []\n";
        } else {
            $out .= "    files:\n";
            foreach ($entry['files'] as $file) {
                $out .= sprintf("      - %s\n", $file);
            }
        }
    }

    file_put_contents($outputFile, $out);
}

/**
 * @param  array<string, array<string, mixed>> $entries
 * @return array<string, int>
 */
function countByStatus(array $entries): array
{
    $counts = ['implemented' => 0, 'config-only' => 0, 'pending' => 0, 'unknown' => 0];
    foreach ($entries as $entry) {
        $status = $entry['status'];
        $counts[$status] = ($counts[$status] ?? 0) + 1;
    }
    return $counts;
}
