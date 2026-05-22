#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Check per-rector coverage against real-world .rector.patch files.
 *
 * For each rector, extracts the "before" patterns it transforms and counts
 * how many patch files contain those patterns in their removed (-) lines.
 *
 * Usage (must run with host PHP, not ddev — patches dir is outside the container):
 *   /opt/homebrew/opt/php@8.2/bin/php scripts/check-rector-coverage.php <patches-dir> [--csv]
 *
 * The patches directory can also be provided via the RECTOR_PATCHES_DIR
 * environment variable.
 */
$rootDir = dirname(__DIR__);
$patchesDir = null;
$verbose = false;
$csvMode = false;

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--help' || $arg === '-h') {
        echo <<<'HELP'
Usage:
  php scripts/check-rector-coverage.php <patches-dir> [--csv] [--verbose]

Arguments:
  patches-dir   Directory containing <module>/*.patch files.
                May also be supplied via the RECTOR_PATCHES_DIR env var.

Options:
  --csv         Output results as CSV instead of a human-readable table.
  --verbose, -v Show extra debug output.
  --help, -h    Show this help message.

How it works:
  For each rector in three tiers (config files → custom rector classes → fixture diffs),
  the script extracts the "before" patterns the rector looks for. It then scans the removed
  lines (-) of every .patch file in patches-dir and counts how many distinct modules contain
  each pattern. The final table is sorted by match count descending.

Tiers of pattern extraction:
  1. Config files (config/drupal-*/*.php) — FunctionToService, ConstantToClass, etc.
  2. Custom rector classes (src/Drupal*/Rector/Deprecation/*.php) — match/case/getName patterns.
  3. Fixture diffs (tests/src/Rector/*/fixture/*.php.inc) — fallback for anything not covered above.

HELP;
        exit(0);
    } elseif ($arg === '--verbose' || $arg === '-v') {
        $verbose = true;
    } elseif ($arg === '--csv') {
        $csvMode = true;
    } elseif (!str_starts_with($arg, '-')) {
        $patchesDir = $arg;
    }
}

$patchesDir ??= getenv('RECTOR_PATCHES_DIR') ?: null;

if ($patchesDir === null) {
    fwrite(STDERR, "Error: patches directory is required.\nRun with: php scripts/check-rector-coverage.php <patches-dir>\nOr set RECTOR_PATCHES_DIR.\n");
    exit(1);
}

if (!is_dir($patchesDir)) {
    fwrite(STDERR, "Patches directory not found: $patchesDir\n");
    exit(1);
}

// ============================================================
// Registry: key => ['label', 'patterns', 'version', 'class', 'source', 'configuration']
// ============================================================

$registry = [];

function reg(array &$registry, string $key, string $label, string $pattern, string $version, string $class, string $source, string $configEntry = ''): void
{
    if (!isset($registry[$key])) {
        $registry[$key] = [
            'label' => $label,
            'patterns' => [],
            'version' => $version,
            'class' => $class,
            'source' => $source,
            'configuration' => [],
        ];
    }
    if (!in_array($pattern, $registry[$key]['patterns'], true)) {
        $registry[$key]['patterns'][] = $pattern;
    }
    if ($configEntry !== '' && !in_array($configEntry, $registry[$key]['configuration'], true)) {
        $registry[$key]['configuration'][] = $configEntry;
    }
}

// ============================================================
// TIER 1: Config files
// ============================================================

$configFiles = glob("$rootDir/config/drupal-*/*.php") ?: [];
sort($configFiles);

// Build short-class → FQCN map from use statements in config files.
$fqcnMap = [];
foreach ($configFiles as $f) {
    if (preg_match_all('/^use\s+([\w\\\\]+);/m', file_get_contents($f), $um)) {
        foreach ($um[1] as $fqcn) {
            $short = substr($fqcn, strrpos($fqcn, '\\') + 1);
            $fqcnMap[$short] = $fqcn;
        }
    }
}

foreach ($configFiles as $configFile) {
    $base = basename($configFile);

    // Skip "all" aggregate files to avoid double-counting
    if (str_contains($base, '-all-')) {
        continue;
    }

    // Extract Drupal version from filename: drupal-11.4-deprecations.php → "11.4"
    if (!preg_match('/drupal-(\d+(?:\.\d+)*)-/', $base, $vm)) {
        continue;
    }
    $version = $vm[1];

    $content = file_get_contents($configFile);

    // --- FunctionToServiceConfiguration / FunctionToStaticConfiguration ---
    // Format: new FunctionToServiceConfiguration('version', 'func_name', 'Service\\Class', 'method')
    // The first string is always the version, the second is always the function name.
    if (preg_match_all(
        '/new FunctionTo(Service|Static)Configuration\(\s*[\'"][^\'\"]+[\'"],\s*[\'"]([a-z_][a-zA-Z0-9_]*)[\'"],\s*[\'"]([^\'\"]+)[\'"],\s*[\'"]([^\'\"]+)[\'"]/',
        $content,
        $matches,
        PREG_SET_ORDER
    )) {
        foreach ($matches as $m) {
            $rClass = 'FunctionTo'.$m[1].'Rector';
            $key = $rClass.'@'.$version;
            $configEntry = $m[2].'( → '.$m[3].'::'.$m[4].'()';
            reg($registry, $key, "$rClass [$version]", $m[2].'(', $version, $fqcnMap[$rClass] ?? $rClass, 'config', $configEntry);
        }
    }

    // --- FunctionCallRemovalConfiguration ---
    // Format: new FunctionCallRemovalConfiguration('func_name')  — no version prefix
    if (preg_match_all(
        '/new FunctionCallRemovalConfiguration\(\s*[\'"]([a-z_][a-zA-Z0-9_]*)[\'"]/',
        $content,
        $matches
    )) {
        foreach ($matches[1] as $funcName) {
            $rClass = 'FunctionCallRemovalRector';
            $key = $rClass.'@'.$version;
            $configEntry = $funcName.'( → (removed)';
            reg($registry, $key, "$rClass [$version]", $funcName.'(', $version, $fqcnMap[$rClass] ?? $rClass, 'config', $configEntry);
        }
    }

    // --- FunctionToFirstArgMethodConfiguration ---
    // Old format (D8/D9): ('func_name', 'method')
    // New format (D11+):  ('version', 'func_name', '$arg', 'method')
    if (preg_match_all(
        '/new FunctionToFirstArgMethodConfiguration\(\s*[\'"]([^\'\"]+)[\'"],\s*[\'"]([^\'\"]+)[\'"],\s*(?:[\'"][^\'\"]*[\'"],\s*)?[\'"]([a-zA-Z][a-zA-Z0-9_]*)[\'"]/',
        $content,
        $matches,
        PREG_SET_ORDER
    )) {
        foreach ($matches as $m) {
            // If the first arg looks like a version string (starts with digit), use second as func name
            $funcName = preg_match('/^\d+\.\d+/', $m[1]) ? $m[2] : $m[1];
            $methodArg = $m[3];
            if (preg_match('/^\d/', $funcName)) {
                continue; // skip if still a version string
            }
            $rClass = 'FunctionToFirstArgMethodRector';
            $key = $rClass.'@'.$version;
            $configEntry = $funcName.'($arg) → $arg->'.$methodArg.'()';
            reg($registry, $key, "$rClass [$version]", $funcName.'(', $version, $fqcnMap[$rClass] ?? $rClass, 'config', $configEntry);
        }
    }

    // --- FunctionToEntityTypeStorageConfiguration ---
    // Format: new FunctionToEntityTypeStorageConfiguration('func_name', ...)
    if (preg_match_all(
        '/new FunctionToEntityTypeStorageConfiguration\(\s*[\'"]([a-z_][a-zA-Z0-9_]*)[\'"]/',
        $content,
        $matches
    )) {
        foreach ($matches[1] as $funcName) {
            $rClass = 'FunctionToEntityTypeStorageRector';
            $key = $rClass.'@'.$version;
            reg($registry, $key, "$rClass [$version]", $funcName.'(', $version, $fqcnMap[$rClass] ?? $rClass, 'config');
        }
    }

    // --- ConstantToClassConfiguration ---
    // Format: new ConstantToClassConfiguration('CONST_NAME', 'Target\\Class', 'NEW_CONST')
    if (preg_match_all(
        '/new ConstantToClass(?:Constant)?Configuration\(\s*[\'"]([A-Z_][A-Z0-9_]{3,})[\'"],\s*[\'"]([^\'\"]+)[\'"],\s*[\'"]([^\'\"]+)[\'"]/',
        $content,
        $matches,
        PREG_SET_ORDER
    )) {
        foreach ($matches as $m) {
            $constName = $m[1];
            $rClass = 'ConstantToClassConstantRector';
            $key = $rClass.'@'.$version;
            $shortTarget = substr($m[2], strrpos($m[2], '\\') + 1);
            $configEntry = $constName.' → '.$shortTarget.'::'.$m[3];
            reg($registry, $key, "$rClass [$version]", $constName, $version, $fqcnMap[$rClass] ?? $rClass, 'config', $configEntry);
        }
    }

    // --- ClassConstantToClassConstantConfiguration ---
    // Format: new ClassConstantToClassConstantConfiguration('OldClass', 'OLD_CONST', 'NewClass', 'NEW_CONST')
    if (preg_match_all(
        '/new ClassConstantToClassConstantConfiguration\(\s*[\'"]([^\'\"]+)[\'"],\s*[\'"]([A-Z_][A-Z0-9_]{2,})[\'"],\s*[\'"]([^\'\"]+)[\'"],\s*[\'"]([^\'\"]+)[\'"]/',
        $content,
        $matches,
        PREG_SET_ORDER
    )) {
        foreach ($matches as $m) {
            $oldClass = $m[1];
            $oldConst = $m[2];
            $shortOld = substr($oldClass, strrpos($oldClass, '\\') + 1);
            $shortNew = substr($m[3], strrpos($m[3], '\\') + 1);
            $rClass = 'ClassConstantToClassConstantRector';
            $key = $rClass.'@'.$version;
            $configEntry = $shortOld.'::'.$oldConst.' → '.$shortNew.'::'.$m[4];
            reg($registry, $key, "$rClass [$version]", $shortOld.'::'.$oldConst, $version, $fqcnMap[$rClass] ?? $rClass, 'config', $configEntry);
        }
    }

    // --- MethodToMethodWithCheckConfiguration ---
    // Format: new MethodToMethodWithCheckConfiguration('ClassName', 'old_method', 'new_method')
    if (preg_match_all(
        '/new MethodToMethodWithCheckConfiguration\(\s*[\'"][^\'\"]+[\'"],\s*[\'"]([a-z][a-zA-Z0-9_]{2,})[\'"],\s*[\'"]([a-z][a-zA-Z0-9_]{2,})[\'"]/',
        $content,
        $matches,
        PREG_SET_ORDER
    )) {
        foreach ($matches as $m) {
            $rClass = 'MethodToMethodWithCheckRector';
            $key = $rClass.'@'.$version;
            $configEntry = '->'.$m[1].'() → ->'.$m[2].'()';
            reg($registry, $key, "$rClass [$version]", '->'.$m[1].'(', $version, $fqcnMap[$rClass] ?? $rClass, 'config', $configEntry);
        }
    }

    // --- RenameClassRector ---
    // Format: ['Old\\ClassName' => 'New\\ClassName'] inside ruleWithConfiguration blocks
    if (preg_match_all(
        '/[\'"]([A-Z][a-zA-Z0-9]*(?:\\\\[A-Z][a-zA-Z0-9]+)+)[\'"][\s\n]*=>[\s\n]*[\'"](?:[A-Z][a-zA-Z0-9\\\\]+)[\'"]/',
        $content,
        $matches
    )) {
        foreach ($matches[1] as $oldFqcn) {
            $shortClass = substr($oldFqcn, strrpos($oldFqcn, '\\') + 1);
            if (strlen($shortClass) < 5) {
                continue;
            }
            $key = 'RenameClassRector@'.$version;
            reg($registry, $key, "RenameClassRector [$version]", $shortClass, $version, 'RenameClassRector', 'config');
        }
    }
}

// ============================================================
// TIER 2: Custom rector classes
// ============================================================

// Explicit pattern overrides for rectors where generic AST-based extraction
// produces wrong results — typically because the heuristics pick up internal
// node-accessor names or new service-method names instead of the deprecated
// user-facing function/method calls we actually want to find in patches.
// [] means "skip tracking entirely" (e.g. addition-only rectors where no
// lines are removed and removed-line scanning cannot detect the transformation).
$patternOverrides = [
    // Generic extraction picks up 'stmts' (an AST node accessor) instead of
    // the deprecated method name.
    'LoadAllIncludesRector' => ['loadAllIncludes('],
    // Generic extraction picks up the new service method names ('rebuild',
    // 'needsRebuild', 'setNeedsRebuild') rather than the deprecated procedural
    // function names that will appear in the removed lines of a patch.
    'NodeAccessRebuildFunctionsRector' => ['node_access_rebuild(', 'node_access_needs_rebuild('],
    // This rector only adds parent::setUp()/tearDown() calls — nothing is
    // removed, so removed-line scanning produces only fixture noise. Skip it.
    'ShouldCallParentMethodsRector' => [],
    // Generic extraction picks up the new service method names (getRoles,
    // getAllFormats, getFallbackFormatId, …) from the replacement values in the
    // function-name → service-call map. Only the map keys are the deprecated
    // user-facing functions that appear in removed patch lines.
    'FilterFormatFunctionsToServiceRector' => [
        'filter_fallback_format(',
        'filter_formats(',
        'filter_get_roles_by_format(',
        'filter_get_formats_by_role(',
        'filter_default_format(',
    ],
];

// Classes with an empty override are excluded from all tiers so Tier 3 does
// not accidentally pick them up via fixture diffs.
$skippedClasses = array_keys(array_filter($patternOverrides, fn ($p) => $p === []));

$customRectorFiles = array_merge(
    glob("$rootDir/src/Drupal*/Rector/Deprecation/*.php") ?: [],
    glob("$rootDir/src/Drupal*/Rector/Convert/*.php") ?: []
);

// Build short class name → FQCN map from namespace declarations in rector files.
$customRectorFqcnMap = [];
foreach ($customRectorFiles as $rf) {
    $cn = basename($rf, '.php');
    if (preg_match('/^namespace\s+([\w\\\\]+);/m', file_get_contents($rf), $nm)) {
        $customRectorFqcnMap[$cn] = $nm[1].'\\'.$cn;
    }
}

// Track which custom rector classes are already covered by config entries
$configClasses = array_unique(array_column($registry, 'class'));

foreach ($customRectorFiles as $rectorFile) {
    $className = basename($rectorFile, '.php');

    // Skip if this rector is already represented as a config-driven entry
    // (the generic rectors like FunctionToServiceRector are config-only)
    if (in_array($className, $configClasses, true)) {
        continue;
    }

    $content = file_get_contents($rectorFile);

    // Extract version from the first version string in the class
    preg_match('/\'(\d+\.\d+)\.\d+\'/', $content, $vm);
    $version = $vm[1] ?? '?';

    // Use explicit override if available; skip addition-only rectors entirely.
    if (array_key_exists($className, $patternOverrides)) {
        $patterns = $patternOverrides[$className];
        if (empty($patterns)) {
            continue; // not trackable via removed-line scanning
        }
        $key = $className.'@'.$version;
        if (!isset($registry[$key])) {
            $registry[$key] = [
                'label' => $className.' ['.$version.']',
                'patterns' => $patterns,
                'version' => $version,
                'class' => $customRectorFqcnMap[$className] ?? $className,
                'source' => 'custom-rector',
                'configuration' => [],
            ];
        }
        continue;
    }

    $patterns = [];

    // Extract from match($node->name->toString()) style: 'function_name' => ...
    if (preg_match_all('/match\s*\([^)]+toString\(\)[^)]*\)[^{]*\{([^}]+)\}/s', $content, $blocks)) {
        foreach ($blocks[1] as $block) {
            if (preg_match_all('/[\'"]([a-z_][a-zA-Z0-9_]{4,})[\'"]/', $block, $fm)) {
                foreach ($fm[1] as $name) {
                    $patterns[] = $name.'(';
                }
            }
        }
    }

    // Extract from case 'function_name': style
    if (preg_match_all('/case\s+[\'"]([a-z_][a-zA-Z0-9_]{4,})[\'"]/', $content, $fm)) {
        foreach ($fm[1] as $name) {
            $patterns[] = $name.'(';
        }
    }

    // Extract from getName() === 'function_name' style
    if (preg_match_all('/getName\(\)[^=]*===\s*[\'"]([a-z_][a-zA-Z0-9_]{4,})[\'"]/', $content, $fm)) {
        foreach ($fm[1] as $name) {
            $patterns[] = $name.'(';
        }
    }

    // Extract from string array keys: 'function_name' => SomeClass::method(...)
    if (preg_match_all('/[\'"]([a-z_][a-zA-Z0-9_]{4,})[\'"][\s\n]*=>[\s\n]*(?:new |fn\s*\(|\[|\'|Drupal)/', $content, $fm)) {
        foreach ($fm[1] as $name) {
            $patterns[] = $name.'(';
        }
    }

    $patterns = array_values(array_unique($patterns));
    if (empty($patterns)) {
        continue;
    }

    $key = $className.'@'.$version;
    if (!isset($registry[$key])) {
        $registry[$key] = [
            'label' => $className.' ['.$version.']',
            'patterns' => $patterns,
            'version' => $version,
            'class' => $customRectorFqcnMap[$className] ?? $className,
            'source' => 'custom-rector',
            'configuration' => [],
        ];
    }
}

// ============================================================
// TIER 3: Fixture file diffs (fallback for remaining rectors)
// ============================================================

$fixtureDirs = array_merge(
    glob("$rootDir/tests/src/Rector/*/fixture", GLOB_ONLYDIR) ?: [],
    glob("$rootDir/tests/src/Rector/*/*/fixture", GLOB_ONLYDIR) ?: []
);

// Custom rectors already registered, plus addition-only rectors that are
// intentionally skipped so Tier 3 does not process their fixture diffs.
$registeredClasses = array_unique(array_merge(
    array_column($registry, 'class'),
    $skippedClasses
));

foreach ($fixtureDirs as $fixtureDir) {
    // Skip bc/below-version variant dirs
    if (str_contains($fixtureDir, 'fixture-')) {
        continue;
    }

    // Derive rector name from path
    $parts = explode('/', rtrim($fixtureDir, '/'));
    $fixtureIdx = array_search('fixture', $parts);
    $rectorName = $parts[$fixtureIdx - 1] ?? '';

    // Skip if already covered
    if (in_array($rectorName, $registeredClasses, true)) {
        continue;
    }

    // Skip generic/abstract rectors
    if (in_array($rectorName, ['AbstractDrupalCoreRector', 'FunctionToServiceRector', 'FunctionToStaticRector'], true)) {
        continue;
    }

    $patterns = [];

    foreach (glob("$fixtureDir/*.php.inc") ?: [] as $fixture) {
        $fixtureContent = file_get_contents($fixture);
        $parts = explode("\n-----\n", $fixtureContent, 2);
        if (count($parts) < 2) {
            continue;
        }

        [$before, $after] = $parts;

        // Write temp files and diff to find removed lines
        $tmpBefore = tempnam(sys_get_temp_dir(), 'before_');
        $tmpAfter = tempnam(sys_get_temp_dir(), 'after_');
        file_put_contents($tmpBefore, $before);
        file_put_contents($tmpAfter, $after);

        exec(sprintf('diff %s %s', escapeshellarg($tmpBefore), escapeshellarg($tmpAfter)), $diffLines);

        foreach ($diffLines as $line) {
            if (!str_starts_with($line, '< ')) {
                continue;
            }
            $removed = ltrim(substr($line, 2));

            // Skip trivial lines
            if (
                strlen($removed) < 10
                || in_array($removed, ['<?php', '?>', '{', '}', ''], true)
                || str_starts_with($removed, '//')
                || str_starts_with($removed, '*')
                || str_starts_with($removed, 'namespace ')
                || str_starts_with($removed, 'use ')
            ) {
                continue;
            }

            $patterns[] = trim($removed);
        }

        unlink($tmpBefore);
        unlink($tmpAfter);
    }

    $patterns = array_values(array_unique($patterns));
    if (empty($patterns)) {
        continue;
    }

    // Infer a plausible version from the patterns (look for rector class in src/)
    $version = 'unknown';

    $key = $rectorName.'@fixture';
    if (!isset($registry[$key])) {
        $registry[$key] = [
            'label' => $rectorName.' [fixture]',
            'patterns' => $patterns,
            'version' => $version,
            'class' => $customRectorFqcnMap[$rectorName] ?? $rectorName,
            'source' => 'fixture',
            'configuration' => [],
        ];
    }
}

// ============================================================
// Scan patch files
// ============================================================

$patchFiles = glob("$patchesDir/*/*.patch") ?: [];
$totalPatches = count($patchFiles);

if ($totalPatches === 0) {
    fwrite(STDERR, "No .patch files found in: $patchesDir\n");
    exit(1);
}

// Build a flat pattern → keys lookup for efficiency
$patternIndex = [];
foreach ($registry as $key => $entry) {
    foreach (array_unique($entry['patterns']) as $pattern) {
        $patternIndex[$pattern][] = $key;
    }
}

// $results[key] = [
//   'modules'  => [module => true],          // which modules this rector matched
//   'patterns' => [pattern => moduleCount],  // how many modules each pattern hit
// ]
$results = [];
$processed = 0;
$startTime = microtime(true);

foreach ($patchFiles as $patchFile) {
    $moduleName = basename(dirname($patchFile));
    $patchContent = file_get_contents($patchFile);

    $removedLines = extractPhpRemovedLines($patchContent);
    if (empty($removedLines)) {
        ++$processed;
        continue;
    }

    $removedText = implode("\n", $removedLines);

    foreach ($patternIndex as $pattern => $keys) {
        if (str_contains($removedText, $pattern)) {
            foreach ($keys as $key) {
                $results[$key]['modules'][$moduleName] = true;
                $results[$key]['patterns'][$pattern] = ($results[$key]['patterns'][$pattern] ?? 0) + 1;
            }
        }
    }

    ++$processed;
    if ($processed % 1000 === 0) {
        $elapsed = round(microtime(true) - $startTime, 1);
        fwrite(STDERR, "Processed $processed/$totalPatches patches ({$elapsed}s)...\r");
    }
}

$elapsed = round(microtime(true) - $startTime, 1);
fwrite(STDERR, "Processed $totalPatches patches in {$elapsed}s.          \n");

// ============================================================
// Compile & sort output
// ============================================================

$rows = [];
foreach ($registry as $key => $entry) {
    $res = $results[$key] ?? [];
    $modules = array_keys($res['modules'] ?? []);
    sort($modules);

    // Build per-pattern hit counts; patterns with 0 hits get 0
    $patternHits = [];
    foreach (array_unique($entry['patterns']) as $p) {
        $patternHits[$p] = $res['patterns'][$p] ?? 0;
    }
    arsort($patternHits); // highest hit count first

    $rows[] = [
        'key' => $key,
        'class' => $entry['class'],
        'version' => $entry['version'],
        'label' => $entry['label'],
        'patternCount' => count($patternHits),
        'count' => count($modules),
        'modules' => $modules,
        'source' => $entry['source'],
        'patternHits' => $patternHits,
        'configuration' => $entry['configuration'] ?? [],
    ];
}

// Sort by count descending, then by version, then by class name
usort($rows, static function (array $a, array $b): int {
    if ($b['count'] !== $a['count']) {
        return $b['count'] <=> $a['count'];
    }

    return strnatcmp($a['label'], $b['label']);
});

// ============================================================
// Output
// ============================================================

if ($csvMode) {
    echo "rector,version,source,pattern_count,match_count,matched_patterns,sample_modules\n";
    foreach ($rows as $row) {
        $matchedPatterns = implode(';', array_keys(array_filter($row['patternHits'])));
        $sample = implode(';', array_slice($row['modules'], 0, 20));
        printf(
            '"%s","%s","%s",%d,%d,"%s","%s"'."\n",
            $row['class'],
            $row['version'],
            $row['source'],
            $row['patternCount'],
            $row['count'],
            $matchedPatterns,
            $sample
        );
    }
} else {
    $labelWidth = 52;
    $verWidth = 6;
    $matchWidth = 8;

    printf("%-{$labelWidth}s %-{$verWidth}s %-{$matchWidth}s %s\n",
        'Rector', 'Ver', 'Matches', 'Sample modules (first 5)');
    echo str_repeat('─', 120)."\n";

    foreach ($rows as $row) {
        $label = $row['class'];
        if (strlen($label) > $labelWidth) {
            $label = '…'.substr($label, -($labelWidth - 1));
        }

        if ($row['count'] === 0) {
            $moduleStr = '—';
        } else {
            $sample = array_slice($row['modules'], 0, 5);
            $moduleStr = implode(', ', $sample);
            if ($row['count'] > 5) {
                $moduleStr .= ' … (+'.($row['count'] - 5).' more)';
            }
        }

        printf("%-{$labelWidth}s %-{$verWidth}s %-{$matchWidth}d %s\n",
            $label, $row['version'], $row['count'], $moduleStr);

        // Show "from" patterns with per-pattern hit count
        foreach ($row['patternHits'] as $pattern => $hits) {
            $indicator = $hits > 0 ? '  ✓' : '  ✗';
            $hitStr = $hits > 0 ? sprintf('%5d hits', $hits) : '    0 hits';
            // Truncate long patterns (fixture-sourced full lines)
            $displayPattern = strlen($pattern) > 60 ? substr($pattern, 0, 57).'...' : $pattern;
            printf("     %s  %-62s %s\n", $indicator, $displayPattern, $hitStr);
        }

        foreach ($row['configuration'] as $cfg) {
            printf("     ·  %s\n", $cfg);
        }

        if (!empty($row['patternHits']) || !empty($row['configuration'])) {
            echo "\n";
        }
    }

    $withMatches = count(array_filter($rows, fn ($r) => $r['count'] > 0));
    printf("Patches: %d  |  Rectors tracked: %d  |  With matches: %d  |  Time: %ss\n",
        $totalPatches, count($rows), $withMatches, $elapsed);
}

// ============================================================
// Helper: extract removed lines from .php/.module/.inc hunks only
// ============================================================

function extractPhpRemovedLines(string $patchContent): array
{
    static $phpExtensions = ['php', 'module', 'inc', 'install', 'theme', 'profile', 'engine'];

    $lines = explode("\n", $patchContent);
    $result = [];
    $inPhpHunk = false;

    foreach ($lines as $line) {
        if (str_starts_with($line, 'diff --git ')) {
            $inPhpHunk = false;
            // "diff --git a/path/to/file.php b/path/to/file.php"
            if (preg_match('/diff --git a\/(\S+)/', $line, $m)) {
                $ext = strtolower(pathinfo($m[1], PATHINFO_EXTENSION));
                $inPhpHunk = in_array($ext, $phpExtensions, true);
            }
            continue;
        }

        if (!$inPhpHunk) {
            continue;
        }

        // Collect removed lines (starts with - but NOT ---)
        if (isset($line[0]) && $line[0] === '-' && (!isset($line[1]) || $line[1] !== '-')) {
            $result[] = substr($line, 1);
        }
    }

    return $result;
}
