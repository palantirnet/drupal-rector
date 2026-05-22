#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Generate a deprecation message coverage map for the upgrade_status module.
 *
 * Joins @see node URLs from rector class docblocks and config file comments
 * with the trigger_error() strings found in repos/drupal-core, plus the
 * git tag when each rector was first introduced.
 *
 * Usage:
 *   php scripts/generate-deprecation-map.php [--php-array] [--debug] [--verify]
 */
$rootDir = dirname(__DIR__);
$phpArray = false;
$debug = false;
$verify = false;

foreach (array_slice($argv, 1) as $arg) {
    match ($arg) {
        '--php-array' => $phpArray = true,
        '--debug' => $debug = true,
        '--verify' => $verify = true,
        '--help', '-h' => exitHelp(),
        default => null,
    };
}

function exitHelp(): never
{
    echo <<<'HELP'
Usage:
  php scripts/generate-deprecation-map.php [--php-array] [--debug] [--verify]

Options:
  --php-array  Output a PHP array keyed by deprecation message string.
  --debug      Print extracted URLs and index sizes to stderr.
  --verify     Run correctness assertions and exit non-zero on failure.
  --help, -h   Show this help.

HELP;
    exit(0);
}

// ============================================================
// U5 helpers: resolve git SHA → earliest containing tag.
// ============================================================

$tagCache = [];

function resolveTag(string $rootDir, string $sha, array &$tagCache): string
{
    if (isset($tagCache[$sha])) {
        return $tagCache[$sha];
    }
    exec(
        sprintf('git -C %s tag --contains %s 2>/dev/null | sort -V | head -1',
            escapeshellarg($rootDir), escapeshellarg($sha)),
        $out
    );
    $tag = trim($out[0] ?? '');
    $result = $tag !== '' ? $tag : 'unreleased';

    return $tagCache[$sha] = $result;
}

function introducedForFile(string $rootDir, string $relPath, array &$tagCache): string
{
    exec(
        sprintf('git -C %s log --diff-filter=A --format="%%H" -- %s 2>/dev/null | tail -1',
            escapeshellarg($rootDir), escapeshellarg($relPath)),
        $out
    );
    $sha = trim($out[0] ?? '');

    return $sha !== '' ? resolveTag($rootDir, $sha, $tagCache) : 'unreleased';
}

function relPath(string $rootDir, string $absPath): string
{
    return ltrim(str_replace($rootDir, '', $absPath), '/');
}

// ============================================================
// U1: Rector source file crawler
// ============================================================

$rectorIndex = [];

$customRectorFiles = array_merge(
    glob("$rootDir/src/Drupal*/Rector/Deprecation/*.php") ?: [],
    glob("$rootDir/src/Drupal*/Rector/Convert/*.php") ?: []
);

foreach ($customRectorFiles as $absPath) {
    $content = file_get_contents($absPath);

    // Derive major version from directory (src/Drupal11/... → 11)
    if (!preg_match('/\/Drupal(\d+)\//', $absPath, $vm)) {
        continue;
    }
    $major = $vm[1];

    // Extract the class-level docblock — the /** ... */ immediately before the class declaration.
    if (!preg_match('/\/\*\*(.*?)\*\/\s*(?:(?:final|abstract|readonly)\s+)*class\s+/s', $content, $dm)) {
        continue;
    }
    $docblock = $dm[1];

    // All @see https://www.drupal.org/node/XXXXXX lines in the docblock.
    if (!preg_match_all('/@see\s+https:\/\/www\.drupal\.org\/node\/(\d+)/', $docblock, $sm)) {
        if ($debug) {
            fwrite(STDERR, 'UNMAPPED (no @see): '.basename($absPath, '.php')."\n");
        }
        continue;
    }

    $nodeIds = $sm[1];
    // Convention: last @see = change record (appears in trigger_error strings),
    // first @see = issue (may equal change_record when there's only one URL).
    $changeRecordId = end($nodeIds);
    $issueId = $nodeIds[0];

    // FQCN from namespace declaration.
    preg_match('/^namespace\s+([\w\\\\]+);/m', $content, $nm);
    $namespace = $nm[1] ?? '';
    $className = basename($absPath, '.php');
    $fqcn = $namespace !== '' ? $namespace.'\\'.$className : $className;
    $relPath = relPath($rootDir, $absPath);

    $rectorIndex[$className] = [
        'class' => $className,
        'fqcn' => $fqcn,
        'file' => $relPath,
        'major' => $major,
        'issue_id' => $issueId,
        'change_record_id' => $changeRecordId,
        'type' => 'custom',
        'introduced' => introducedForFile($rootDir, $relPath, $tagCache),
        'configuration_class' => null,
        'configuration' => [],
        'source_content' => $content,
    ];
}

// ============================================================
// U2: Config file crawler for generic (config-driven) rector entries
// ============================================================

$configFiles = glob("$rootDir/config/drupal-*/*.php") ?: [];
sort($configFiles);

$configIntroducedCache = [];

foreach ($configFiles as $absPath) {
    $base = basename($absPath);

    // Skip aggregate "all" files to avoid double-counting.
    if (str_contains($base, '-all-')) {
        continue;
    }

    // Major version from filename (e.g. drupal-11.4-deprecations.php → 11).
    if (!preg_match('/drupal-(\d+)(?:\.\d+)*-/', $base, $vm)) {
        continue;
    }
    $major = $vm[1];
    $relPath = relPath($rootDir, $absPath);
    $lines = file($absPath, FILE_IGNORE_NEW_LINES);

    $urlBuffer = [];
    $configAccum = null;   // non-null while buffering a ruleWithConfiguration([...]) body
    $configIndexKey = null; // rector index key to attach configuration to

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // Inside a configuration array block: accumulate until ']);'
        if ($configAccum !== null) {
            $configAccum .= $line."\n";
            if (str_contains($trimmed, ']);')) {
                // Extract configuration class name (first 'new XxxClass(' hit)
                $cfgClass = null;
                if (preg_match('/new\s+(\w+)\s*\(/', $configAccum, $cm)) {
                    $cfgClass = $cm[1];
                }
                // Extract one entry per 'new XxxClass(args)' call: keep only string args in order
                $cfgEntries = [];
                if (preg_match_all('/new\s+\w+\s*\(([^)]*)\)/', $configAccum, $calls)) {
                    foreach ($calls[1] as $argsStr) {
                        preg_match_all('/[\'"]([^\'"]*)[\'"]/', $argsStr, $am);
                        $cfgEntries[] = $am[1];
                    }
                }
                if ($configIndexKey !== null && isset($rectorIndex[$configIndexKey])) {
                    // Keep the first non-null class name seen; accumulate entries across
                    // multiple ruleWithConfiguration() blocks that share the same key
                    // (same rector + same CR appearing in several config file sections).
                    $rectorIndex[$configIndexKey]['configuration_class'] ??= $cfgClass;
                    $rectorIndex[$configIndexKey]['configuration'] = array_merge(
                        $rectorIndex[$configIndexKey]['configuration'],
                        $cfgEntries
                    );
                }
                $configAccum = null;
                $configIndexKey = null;
                $urlBuffer = [];
            }
            continue;
        }

        // Buffer every // https://www.drupal.org/node/XXXXXX comment.
        if (preg_match('#^\s*//\s*https://www\.drupal\.org/node/(\d+)#', $line, $cm)) {
            $urlBuffer[] = $cm[1];
            continue;
        }

        // Other comment lines — preserve buffer (descriptive text between URL lines).
        if (str_starts_with($trimmed, '//')) {
            continue;
        }

        // Blank lines — preserve buffer.
        if ($trimmed === '') {
            continue;
        }

        // Code line: check if it's a rule() or ruleWithConfiguration() call.
        if (preg_match('/\$rectorConfig->(rule|ruleWithConfiguration)\s*\(\s*(\w+)::class/', $line, $rm)) {
            $rectorClass = $rm[2];
            $configIndexKey = null;

            if (!empty($urlBuffer)) {
                // Last buffered URL = change record; first = issue.
                $changeRecordId = end($urlBuffer);
                $issueId = $urlBuffer[0];

                // Skip if already indexed as a custom rector (its class docblock takes precedence).
                if (!isset($rectorIndex[$rectorClass])) {
                    $key = $rectorClass.'@'.$changeRecordId;
                    if (!isset($rectorIndex[$key])) {
                        $configIntroducedCache[$relPath] ??= introducedForFile($rootDir, $relPath, $tagCache);
                        $rectorIndex[$key] = [
                            'class' => $rectorClass,
                            'fqcn' => $rectorClass,
                            'file' => $relPath,
                            'major' => $major,
                            'issue_id' => $issueId,
                            'change_record_id' => $changeRecordId,
                            'type' => 'config',
                            'introduced' => $configIntroducedCache[$relPath],
                            'configuration_class' => null,
                            'configuration' => [],
                        ];
                    }
                    $configIndexKey = $rectorClass.'@'.$changeRecordId;
                } else {
                    // Custom rector from U1: attach configuration to its index entry.
                    $configIndexKey = $rectorClass;
                }
            }

            // For ruleWithConfiguration, start buffering the configuration array body.
            if ($rm[1] === 'ruleWithConfiguration') {
                $configAccum = $line."\n";
                continue; // URL buffer reset happens when the block closes
            }
        }

        // Any non-comment code line resets the buffer.
        $urlBuffer = [];
    }
}

if ($debug) {
    fwrite(STDERR, sprintf("Rector index: %d entries\n", count($rectorIndex)));
    foreach (array_slice($rectorIndex, 0, 5, true) as $key => $e) {
        fwrite(STDERR, sprintf("  %s: issue=%s change_record=%s major=%s\n",
            $key, $e['issue_id'], $e['change_record_id'], $e['major']));
    }
}

// ============================================================
// U3: Drupal core trigger_error index (per major branch)
// ============================================================

$coreDir = "$rootDir/repos/drupal-core";
$coreIndex = [];  // $coreIndex[$major][$nodeId][] = $message

function ensureCoreBranch(string $coreDir, string $major): string
{
    exec(sprintf('git -C %s branch -a 2>/dev/null', escapeshellarg($coreDir)), $rawBranches);

    $localBranches = [];
    foreach ($rawBranches as $b) {
        $b = trim($b, " *\t");
        // Strip remote tracking prefix (remotes/origin/...)
        if (str_starts_with($b, 'remotes/')) {
            $b = preg_replace('@^remotes/[^/]+/@', '', $b);
        }
        $localBranches[] = $b;
    }
    $localBranches = array_unique($localBranches);

    // 1. Prefer exact X.x branch (Drupal 11 development branch style).
    if (in_array($major.'.x', $localBranches, true)) {
        return $major.'.x';
    }

    // 2. Find the highest locally available X.Y.x branch for this major.
    $minorCandidates = [];
    foreach ($localBranches as $b) {
        if (preg_match('/^'.preg_quote($major, '/').'\.(\d+)\.x$/', $b, $vm)) {
            $minorCandidates[(int) $vm[1]] = $b;
        }
    }
    if (!empty($minorCandidates)) {
        krsort($minorCandidates);

        return array_values($minorCandidates)[0];
    }

    // 3. Not found locally: attempt to fetch X.x from origin.
    $branch = $major.'.x';
    fwrite(STDERR, "Fetching $branch from origin (local gitcache)...\n");
    exec(sprintf('git -C %s fetch origin %s 2>&1', escapeshellarg($coreDir), escapeshellarg($branch)), $out, $rc);
    if ($rc !== 0) {
        fwrite(STDERR, "Warning: could not fetch $branch: ".implode("\n", $out)."\n");
    }

    return $branch;
}

function buildCoreIndex(string $coreDir, string $major, bool $debug): array
{
    $branch = ensureCoreBranch($coreDir, $major);
    $index = [];   // $index[$nodeId][$message] = true — flattened to arrays at return

    $cmd = sprintf(
        'git -C %s grep -p "_DEPRECATED" %s -- "*.php" "*.module" "*.inc" 2>/dev/null',
        escapeshellarg($coreDir),
        escapeshellarg($branch)
    );
    exec($cmd, $lines);

    $currentFunction = '';
    $currentFile = '';
    $matched = 0;

    foreach ($lines as $line) {
        // Split on the first two colons: BRANCH:PATH:CONTENT (match) or BRANCH:PATH=DECL (context).
        // Context lines always have = after the filepath; match lines have :.
        $colonPos = strpos($line, ':');
        if ($colonPos === false) {
            continue;
        }
        $rest = substr($line, $colonPos + 1);

        // Context line: rest = "filepath=function_decl"
        $eqPos = strpos($rest, '=');
        $coPos = strpos($rest, ':');

        if ($eqPos !== false && ($coPos === false || $eqPos < $coPos)) {
            $currentFile = substr($rest, 0, $eqPos);
            // Extract function name from the declaration after '='.
            $decl = substr($rest, $eqPos + 1);
            if (preg_match('/function\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', $decl, $fm)) {
                $currentFunction = $fm[1];
            }
            continue;
        }

        // Match line: rest = "filepath:content"
        if ($coPos === false) {
            continue;
        }
        $currentFile = substr($rest, 0, $coPos);
        $content = substr($rest, $coPos + 1);

        if (!str_contains($content, '_DEPRECATED')) {
            continue;
        }

        // Skip @see docblock lines (they contain the node ID but are not trigger_error calls).
        if (str_contains($content, '@see ')) {
            continue;
        }

        $message = extractTriggerErrorMessage($content, $currentFunction, $currentFile);
        if ($message === null) {
            continue;
        }

        // The message must end with "See https://www.drupal.org/node/XXXXXX".
        if (!preg_match('/See https:\/\/www\.drupal\.org\/node\/(\d+)/', $message, $nm)) {
            continue;
        }

        $nodeId = $nm[1];
        ++$matched;

        $index[$nodeId][$message] = true;
    }

    if ($debug) {
        fwrite(STDERR, sprintf("Core index [%s]: %d node IDs, %d messages\n",
            $major, count($index), $matched));
    }

    return array_map('array_keys', $index);
}

function classFromFilePath(string $filePath): string
{
    $className = basename($filePath, '.php');

    // core/lib/Drupal/Ns1/.../Class.php → Drupal\Ns1\...\Class
    if (preg_match('#core/lib/(.+)/[^/]+\.php$#', $filePath, $m)) {
        return str_replace('/', '\\', $m[1]).'\\'.$className;
    }

    // core/modules/M/src/[Sub/...]Class.php → Drupal\M[\Sub\...]\Class
    if (preg_match('#core/modules/([^/]+)/src(/.*?)?/[^/]+\.php$#', $filePath, $m)) {
        $sub = !empty($m[2]) ? str_replace('/', '\\', $m[2]) : '';

        return 'Drupal\\'.$m[1].$sub.'\\'.$className;
    }

    return $className;
}

function resolveMethod(string $filePath, string $currentFunction): string
{
    if ($filePath === '' || $currentFunction === '') {
        return '__METHOD__';
    }

    return classFromFilePath($filePath).'::'.$currentFunction;
}

function extractTriggerErrorMessage(string $content, string $currentFunction, string $currentFile = ''): ?string
{
    // Case 0: __CLASS__ . '::' . __FUNCTION__ . 'rest' — class::method context
    if (preg_match(
        '/\@?trigger_error\s*\(\s*__CLASS__\s*\.\s*[\'"]::[\'"]\s*\.\s*__FUNCTION__\s*\.\s*([\'"])(.+?)\1\s*,\s*E_(?:USER_)?DEPRECATED/s',
        $content, $m
    )) {
        return $currentFunction !== '' ? $currentFunction.$m[2] : $m[2];
    }

    // Case 1: __FUNCTION__ . 'rest' or __FUNCTION__ . "rest"
    if (preg_match(
        '/\@?trigger_error\s*\(\s*__FUNCTION__\s*\.\s*([\'"])(.+?)\1\s*,\s*E_(?:USER_)?DEPRECATED/s',
        $content, $m
    )) {
        if ($currentFunction === '') {
            return null;
        }
        $suffix = $m[2];
        // A trigger_error may concatenate __FUNCTION__ twice, e.g.:
        //   __FUNCTION__ . '() is deprecated ... ' . __FUNCTION__ . '() may also ...'
        // The regex captures the suffix between the first quote pair, which can still
        // contain the literal string  ' . __FUNCTION__ . '  — resolve it here.
        if (str_contains($suffix, '__FUNCTION__')) {
            $suffix = preg_replace("/'\\s*\\.\\s*__FUNCTION__\\s*\\.\\s*'/", $currentFunction, $suffix);
            $suffix = str_replace('__FUNCTION__', $currentFunction, $suffix);
        }

        return $currentFunction.$suffix;
    }

    // Case 2: __METHOD__ . 'rest' — resolve to ClassName::method using file path + function context
    if (preg_match(
        '/\@?trigger_error\s*\(\s*__METHOD__\s*\.\s*([\'"])(.+?)\1\s*,\s*E_(?:USER_)?DEPRECATED/s',
        $content, $m
    )) {
        return resolveMethod($currentFile, $currentFunction).$m[2];
    }

    // Case 3: static string (single or double quote) — may embed magic constants literally
    if (preg_match(
        '/\@?trigger_error\s*\(\s*([\'"])(.+?)\1\s*,\s*E_(?:USER_)?DEPRECATED/s',
        $content, $m
    )) {
        $message = $m[2];
        // Resolve embedded ' . __METHOD__ . ' / ' . __CLASS__ . ' / ' . __FUNCTION__ . ' patterns.
        foreach ([
            '__METHOD__' => resolveMethod($currentFile, $currentFunction),
            '__CLASS__' => classFromFilePath($currentFile),
            '__FUNCTION__' => $currentFunction,
        ] as $magic => $replacement) {
            if ($replacement !== '' && str_contains($message, $magic)) {
                $message = preg_replace(
                    "/'\\s*\\.\\s*".preg_quote($magic, '/')."\\s*\\.\\s*'/",
                    $replacement,
                    $message
                );
                $message = str_replace($magic, $replacement, $message);
            }
        }

        return $message;
    }

    // Case 4: sprintf(format, ...) — URL is embedded in the format string
    if (preg_match(
        '/\@?trigger_error\s*\(\s*sprintf\s*\(\s*([\'"])(.+?)\1/s',
        $content, $m
    )) {
        $message = $m[2];
        if (str_contains($message, '%s') && $currentFunction !== '') {
            // Replace first %s with resolved method name (common pattern: sprintf("Calling %s()...", __METHOD__))
            $method = resolveMethod($currentFile, $currentFunction);
            $message = preg_replace('/%s/', $method, $message, 1);
        }

        return $message;
    }

    return null;
}

// ============================================================
// U3b: Drupal core @deprecated annotation index (per major branch)
// ============================================================

function buildDeprecatedAnnotationIndex(string $coreDir, string $major, bool $debug): array
{
    $branch = ensureCoreBranch($coreDir, $major);
    $index = [];

    // Grep for @deprecated docblocks, capturing 20 lines of context after each match.
    // This is enough to reach the @see URL that appears later in the same docblock.
    $cmd = sprintf(
        'git -C %s grep -A 20 "@deprecated in drupal:" %s -- "*.php" "*.module" "*.inc" 2>/dev/null',
        escapeshellarg($coreDir),
        escapeshellarg($branch)
    );

    $handle = popen($cmd, 'r');
    if ($handle === false) {
        return [];
    }

    $pending = false;  // true = inside a @deprecated docblock, scanning for @see
    $annotation = '';
    $matched = 0;

    while (false !== ($line = fgets($handle))) {
        $line = rtrim($line, "\n\r");

        // Section separator between match groups.
        if ($line === '--') {
            $pending = false;
            $annotation = '';
            continue;
        }

        if (str_contains($line, '@deprecated in drupal:')) {
            $pending = true;
            // Capture the "in drupal:X.Y.0 ..." fragment as the human-readable message.
            $annotation = '@deprecated annotation';
            if (preg_match('/@deprecated (in drupal:[^*\n]+)/', $line, $am)) {
                $annotation = '@deprecated '.rtrim(trim($am[1]), ". \t");
            }
            // @see may appear on the same line as @deprecated (rare, handle anyway).
            if (preg_match('/@see\s+https:\/\/www\.drupal\.org\/node\/(\d+)/', $line, $sm)) {
                $index[$sm[1]][$annotation] = true;
                ++$matched;
                $pending = false;
                $annotation = '';
            }
            continue;
        }

        if (!$pending) {
            continue;
        }

        // End of docblock reached without finding @see.
        if (str_contains($line, '*/')) {
            $pending = false;
            $annotation = '';
            continue;
        }

        if (preg_match('/@see\s+https:\/\/www\.drupal\.org\/node\/(\d+)/', $line, $sm)) {
            $index[$sm[1]][$annotation] = true;
            ++$matched;
            $pending = false;
            $annotation = '';
            continue;
        }

        // Accumulate continuation lines of the @deprecated text (e.g. " *   Use Foo instead.").
        // Stop accumulating at any new docblock tag (@param, @see, etc.).
        if (preg_match('/\*\s+(\S.*)/', $line, $cm) && !str_starts_with(trim($cm[1]), '@')) {
            $annotation = rtrim($annotation, ". \t").' '.trim($cm[1]);
        }
    }

    pclose($handle);

    if ($debug) {
        fwrite(STDERR, sprintf("@deprecated annotation index [%s]: %d node IDs, %d entries\n",
            $major, count($index), $matched));
    }

    return array_map('array_keys', $index);
}

// Build core indices for all major versions referenced by our rector index.
$majorsNeeded = array_unique(array_column($rectorIndex, 'major'));
foreach ($majorsNeeded as $major) {
    $triggerIndex = buildCoreIndex($coreDir, $major, $debug);
    $annotIndex = buildDeprecatedAnnotationIndex($coreDir, $major, $debug);
    // Merge annotation entries alongside trigger_error entries.
    foreach ($annotIndex as $nodeId => $messages) {
        foreach ($messages as $message) {
            if (!in_array($message, $triggerIndex[$nodeId] ?? [], true)) {
                $triggerIndex[$nodeId][] = $message;
            }
        }
    }
    $coreIndex[$major] = $triggerIndex;
}

// ============================================================
// U4: Join rector index with core index and build output rows
// ============================================================

/**
 * Extract the pure snake_case function name from the start of a deprecation message.
 * Returns '' for annotation messages, class::method patterns, or anything unrecognisable.
 * We deliberately only filter on plain function names — FQCN/method messages are rarely
 * the source of shared-CR false positives and are harder to match in source files.
 *
 * Handles three Drupal core trigger_error patterns:
 *   "func_name() is deprecated..."       — normal
 *   "func_name is deprecated..."         — no parens
 *   "func_nameis deprecated..."          — Drupal core typo (missing space+parens)
 */
function extractFunctionName(string $message): string
{
    if (str_starts_with($message, '@deprecated')) {
        return '';
    }
    // Lazy match: stop as soon as we see () OR a lookahead for "is deprecated"
    // (with or without a preceding space/parens).
    if (preg_match('/^([a-z_][a-z0-9_]*?)(?:\(\)|(?=is\s+deprecated)|(?=\s+is\s+deprecated))/', $message, $m)) {
        return $m[1];
    }

    return '';
}

/**
 * Return true if this rector entry is known to handle the given plain function name.
 *
 * Only custom rectors are filtered: their source file is the definitive record of
 * which function(s) they target, so str_contains() is reliable.
 *
 * Config rectors are NOT filtered here because many use omnibus
 * ruleWithConfiguration() blocks that span functions from multiple change records.
 * The CR attached to the block may not match the trigger_error CR for every function
 * listed, so excluding them would silently drop valid coverage rows.
 */
function rectorHandlesFunction(array $entry, string $funcName): bool
{
    if ($entry['type'] !== 'custom') {
        return true;
    }

    return isset($entry['source_content']) && str_contains($entry['source_content'], $funcName);
}

$csvRows = [];

foreach ($rectorIndex as $entry) {
    $major = $entry['major'];
    $changeRecordId = $entry['change_record_id'];
    $messages = $coreIndex[$major][$changeRecordId] ?? [];

    if (empty($messages)) {
        fwrite(STDERR, sprintf("UNMAPPED: %s (change_record: [%s](https://www.drupal.org/node/%s), major: %s, introduced: %s)\n",
            $entry['class'], $changeRecordId, $changeRecordId, $major, $entry['introduced']));
        continue;
    }

    foreach ($messages as $message) {
        // When multiple rectors share a CR, each rector only covers a subset of the
        // deprecation messages for that CR.  Filter out messages whose deprecated
        // function name does not appear in this rector's source or configuration.
        $funcName = extractFunctionName($message);
        if ($funcName !== '' && !rectorHandlesFunction($entry, $funcName)) {
            continue;
        }

        $csvRows[] = [
            'rector_class' => $entry['class'],
            'fqcn' => $entry['fqcn'],
            'source_path' => $entry['file'],
            'issue_node_id' => $entry['issue_id'],
            'change_record_node_id' => $changeRecordId,
            'deprecation_message' => $message,
            'introduced' => $entry['introduced'],
            'configuration_class' => $entry['configuration_class'] ?? null,
            'configuration' => $entry['configuration'] ?? [],
        ];
    }
}

// ============================================================
// Output dispatch
// ============================================================

if ($verify) {
    runVerify($csvRows, $coreIndex, $rectorIndex);
} elseif ($phpArray) {
    outputPhpArray($csvRows);
} else {
    outputCsv($csvRows);
}

// ============================================================
// Output: CSV
// ============================================================

function outputCsv(array $rows): void
{
    echo "rector_class,source_path,issue_node_id,change_record_node_id,deprecation_message,introduced\n";
    foreach ($rows as $row) {
        printf(
            '"%s","%s","%s","%s","%s","%s"'."\n",
            csvEsc($row['rector_class']),
            csvEsc($row['source_path']),
            csvEsc($row['issue_node_id']),
            csvEsc($row['change_record_node_id']),
            csvEsc($row['deprecation_message']),
            csvEsc($row['introduced'])
        );
    }
}

function csvEsc(string $s): string
{
    return str_replace('"', '""', $s);
}

// ============================================================
// Output: PHP array
// ============================================================

function phpSqEsc(string $s): string
{
    return str_replace(['\\', "'"], ['\\\\', "\\'"], $s);
}

function outputPhpArray(array $rows): void
{
    echo "<?php\n\nreturn [\n";
    $seen = [];
    foreach ($rows as $row) {
        // When multiple rectors share a CR, every one of them produces a row for
        // every message under that CR.  Custom rectors are indexed first (U1) so
        // the first occurrence of a message key is always the most specific match.
        // Skip later duplicates so the PHP array remains a valid map (one key → one value).
        if (isset($seen[$row['deprecation_message']])) {
            continue;
        }
        $seen[$row['deprecation_message']] = true;
        $msg = phpSqEsc($row['deprecation_message']);
        $cls = phpSqEsc($row['fqcn']);
        $introduced = phpSqEsc($row['introduced']);
        echo "  '$msg' => [\n";
        echo "    'class' => '$cls',\n";
        echo "    'issue' => '{$row['issue_node_id']}',\n";
        echo "    'change_record' => '{$row['change_record_node_id']}',\n";
        echo "    'introduced' => '$introduced',\n";
        if ($row['configuration_class'] !== null) {
            $cfgCls = phpSqEsc($row['configuration_class']);
            echo "    'configuration_class' => '$cfgCls',\n";
            if (!empty($row['configuration'])) {
                echo "    'configuration' => [\n";
                foreach ($row['configuration'] as $args) {
                    $parts = array_map(static fn ($a) => "'".phpSqEsc((string) $a)."'", $args);
                    echo '      ['.implode(', ', $parts)."],\n";
                }
                echo "    ],\n";
            }
        }
        echo "  ],\n";
    }
    echo "];\n";
}

// ============================================================
// U6: --verify mode
// ============================================================

function runVerify(array $csvRows, array $coreIndex, array $rectorIndex): never
{
    $passes = 0;
    $fails = 0;

    $check = static function (bool $ok, string $desc) use (&$passes, &$fails): void {
        if ($ok) {
            echo "[PASS] $desc\n";
            ++$passes;
        } else {
            echo "[FAIL] $desc\n";
            ++$fails;
        }
    };

    // Index rows by rector class for easy lookup.
    $byClass = [];
    foreach ($csvRows as $row) {
        $byClass[$row['rector_class']][] = $row;
    }

    // A1: CheckMarkupToProcessedTextRector has correct issue and change_record IDs.
    $cmRows = $byClass['CheckMarkupToProcessedTextRector'] ?? [];
    $check(
        !empty($cmRows)
            && ($cmRows[0]['issue_node_id'] ?? '') === '455724'
            && ($cmRows[0]['change_record_node_id'] ?? '') === '3588040',
        'CheckMarkupToProcessedTextRector: issue=455724 https://www.drupal.org/i/455724, change_record=3588040 https://www.drupal.org/i/3588040'
    );

    // A2: CheckMarkupToProcessedTextRector deprecation message starts with expected prefix.
    $cmMsg = $cmRows[0]['deprecation_message'] ?? '';
    $check(
        str_starts_with($cmMsg, 'check_markup() is deprecated in drupal:11.4.0'),
        'CheckMarkupToProcessedTextRector: message starts with "check_markup() is deprecated in drupal:11.4.0"'
    );

    // A3: change_record 3588040 is indexed under major 11 (confirming 11.x branch was used).
    $check(
        isset($coreIndex['11']['3588040']),
        'CheckMarkupToProcessedTextRector: change record 3588040 https://www.drupal.org/i/3588040 sourced from 11.x branch'
    );

    // A4: FilterFormatFunctionsToServiceRector has ≥5 rows for change_record 3035368.
    $ffRows = array_filter(
        $byClass['FilterFormatFunctionsToServiceRector'] ?? [],
        static fn ($r) => $r['change_record_node_id'] === '3035368'
    );
    $check(count($ffRows) >= 5, sprintf(
        'FilterFormatFunctionsToServiceRector: ≥5 rows for change_record=3035368 https://www.drupal.org/i/3035368 (found %d)',
        count($ffRows)
    ));

    // A5: All five expected filter function message prefixes appear.
    $expectedPrefixes = [
        'filter_formats(',
        'filter_fallback_format(',
        'filter_get_roles_by_format(',
        'filter_get_formats_by_role(',
        'filter_default_format(',
    ];
    $foundPrefixes = [];
    foreach ($ffRows as $r) {
        foreach ($expectedPrefixes as $prefix) {
            if (str_starts_with($r['deprecation_message'], $prefix)) {
                $foundPrefixes[$prefix] = true;
            }
        }
    }
    $check(
        count($foundPrefixes) === count($expectedPrefixes),
        sprintf('FilterFormatFunctionsToServiceRector: all 5 expected function message prefixes found (%d/5)',
            count($foundPrefixes))
    );

    // A6: All rows have non-empty introduced value.
    $emptyIntroduced = array_filter($csvRows, static fn ($r) => trim($r['introduced']) === '');
    $check(empty($emptyIntroduced), sprintf(
        'All %d rows have non-empty introduced value',
        count($csvRows)
    ));

    // A7: All rows have non-empty deprecation_message (catches silent join failures).
    $emptyMsg = array_filter($csvRows, static fn ($r) => trim($r['deprecation_message']) === '');
    $check(empty($emptyMsg), sprintf(
        'All %d rows have non-empty deprecation_message',
        count($csvRows)
    ));

    // A8: Total row count ≥ 350 (342 trigger_error rows + annotation entries).
    $check(count($csvRows) >= 350, sprintf('Total rows: %d (≥ 350)', count($csvRows)));

    // A9: No date-shaped introduced values (all should be tags or "unreleased").
    $dateRows = array_filter($csvRows, static fn ($r) => (bool) preg_match('/^\d{4}-\d{2}-\d{2}/', $r['introduced']));
    $check(empty($dateRows), 'No date-shaped introduced values (all are version tags or "unreleased")');

    // A10: A known D10 deprecation is indexed from 10.x branch but absent from 11.x.
    // Uses watchdog_exception() (change_record=2932520, deprecated in 10.1.0, removed in 11.0.0)
    // as the routing test: it must appear in coreIndex[10] but not coreIndex[11].
    $d10InTen = isset($coreIndex['10']['2932520']);
    $d10NotInElev = !isset($coreIndex['11']['2932520']);
    $check(
        $d10InTen && $d10NotInElev,
        'watchdog_exception() (change_record=2932520 https://www.drupal.org/i/2932520): found in 10.x branch index, absent from 11.x (confirms branch routing)'
    );

    $total = $passes + $fails;
    echo "\n$passes/$total checks passed.\n";
    exit($fails > 0 ? 1 : 0);
}
