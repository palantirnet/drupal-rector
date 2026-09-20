<?php

declare(strict_types=1);

/**
 * Verifies docs/implemented-digests.yml against the actual codebase.
 *
 * WHY THIS EXISTS
 * ---------------
 * implemented-digests.yml is hand-maintained and, by design, overrides the
 * rector-index matcher (see the rector-discover skill, step 3b). That makes it
 * authoritative — and therefore dangerous: an entry that claims `implemented`
 * when nothing was implemented removes that digest from the pending list
 * permanently, and nothing else in the pipeline notices. This script is the
 * check that catches that drift.
 *
 * HOW IT CHECKS
 * -------------
 * Grepping the issue number does NOT work: rectors overwhelmingly cite the
 * change-record node rather than the issue, and config-only entries cite
 * nothing at all. (Verified: 134 entries, of which only a handful mention their
 * own issue number anywhere.) So instead we extract the digest's *target
 * symbols* — the deprecated function/class/constant names it names — and look
 * for those in src/, config/ and tests/.
 *
 * Searching is done in pure PHP on purpose. `php` in this project routes
 * through `ddev exec`, and ripgrep does not exist inside the web container, so
 * shelling out to `rg` silently returns zero matches and every entry looks
 * broken. Do not "optimise" this into an exec() call.
 *
 * Usage:
 *   php .claude/scripts/audit-implemented-digests.php [--quiet] [--json]
 *
 * Exit codes: 0 = no gaps, 1 = at least one MISSING entry.
 */

$repoRoot = dirname(__DIR__, 2);
$implFile = $repoRoot . '/docs/implemented-digests.yml';
$rulesDir = $repoRoot . '/repos/drupal-digests/rector/rules';

$quiet = in_array('--quiet', $argv, true);
$asJson = in_array('--json', $argv, true);

if (!file_exists($implFile)) {
    fwrite(STDERR, "No docs/implemented-digests.yml — nothing to audit.\n");
    exit(0);
}
if (!is_dir($rulesDir)) {
    fwrite(STDERR, "Missing $rulesDir — run: bash .claude/scripts/setup-repos.sh\n");
    exit(0);
}

/**
 * Generic identifiers that appear in nearly every digest and prove nothing.
 * Matching on these produces false "covered" verdicts.
 */
const STOP_WORDS = [
    'Drupal', 'service', 'class', 'value', 'string', 'array', 'static', 'self',
    'this', 'true', 'false', 'null', 'Rector', 'RectorConfig', 'CODE_SAMPLE',
    'CODE_BEFORE', 'CODE_AFTER', 'method', 'minArgs', 'maxArgs', 'action',
    'langcode', 'options', 'variables', 'entity', 'settings', 'default',
];

/**
 * Placeholder identifiers used in digest code samples (`$my_var`, 'mytemplate').
 * They are illustrative, never real target symbols.
 */
const PLACEHOLDER_PATTERN = '/^(my_?|some_?|example|foo|bar|baz|mymodule|mytheme|mytemplate|custom_)/i';

/**
 * Builds one searchable haystack from the directories a rule can live in.
 */
function buildHaystack(string $repoRoot): string
{
    $haystack = '';
    foreach (['src', 'config', 'tests'] as $dir) {
        $path = $repoRoot . '/' . $dir;
        if (!is_dir($path)) {
            continue;
        }
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isFile()) {
                $haystack .= file_get_contents($file->getPathname()) . "\n";
            }
        }
    }

    return $haystack;
}

/**
 * Extracts candidate target symbols from a digest rule file.
 *
 * Digests come in two shapes: a `RectorConfig::configure()` call listing the
 * symbols, or a full custom rector class holding them in constants. Stripping
 * the leading docblock and taking every single-quoted identifier covers both.
 *
 * A namespaced symbol is reduced to its last TWO segments ("mysql\Merge")
 * rather than the bare class name. Short class names such as Merge, Select or
 * Update occur all over the codebase as ordinary words, so matching on them
 * alone reports a symbol as covered when it is not. Two segments stay specific
 * while still matching both a `use Drupal\…\mysql\Merge;` import and an
 * escaped FQCN string inside a config array.
 *
 * @return string[] needles, deduplicated
 */
function extractSymbols(string $source): array
{
    // Drop the leading docblock — its "Before:"/"After:" samples are full of
    // placeholders that would otherwise be treated as target symbols.
    $body = preg_replace('#^\s*<\?php.*?\*/#s', '', $source, 1) ?? $source;

    preg_match_all("/'([A-Za-z_\\\\][A-Za-z0-9_\\\\]{5,})'/", $body, $m);

    $symbols = [];
    foreach (array_unique($m[1]) as $raw) {
        // Digest sources escape namespace separators inconsistently
        // ('Drupal\\user\\Entity\\Role' vs 'Drupal\user\Entity\Role').
        $normalised = str_replace('\\\\', '\\', $raw);
        $segments = array_values(array_filter(explode('\\', $normalised)));
        if ($segments === []) {
            continue;
        }

        $short = end($segments);
        if ($short === '' || in_array($short, STOP_WORDS, true)) {
            continue;
        }
        if (preg_match(PLACEHOLDER_PATTERN, $short)) {
            continue;
        }

        $needle = count($segments) >= 2
            ? $segments[count($segments) - 2] . '\\' . $short
            : $short;

        $symbols[$needle] = true;
    }

    return array_keys($symbols);
}

$impl = yaml_parse_file($implFile);
$digests = $impl['digests'] ?? [];
$haystack = buildHaystack($repoRoot);

$results = [];
foreach ($digests as $id => $entry) {
    $id = (string) $id;
    $status = $entry['status'] ?? 'unknown';
    // 'skip' is a decision not to implement; 'pending' is a claim already disproven
    // by a previous audit run. Neither asserts that the work exists, so neither can
    // be a false claim of completion.
    if ($status === 'skip' || $status === 'pending') {
        continue;
    }

    $digestFile = $entry['digest_file'] ?? '';
    $path = $digestFile !== '' ? $rulesDir . '/' . $digestFile : '';

    if ($path === '' || !file_exists($path)) {
        $results[] = ['id' => $id, 'status' => $status, 'verdict' => 'NO-DIGEST', 'found' => 0, 'total' => 0, 'missing' => []];
        continue;
    }

    $symbols = extractSymbols(file_get_contents($path));
    if ($symbols === []) {
        $results[] = ['id' => $id, 'status' => $status, 'verdict' => 'NO-SYMBOLS', 'found' => 0, 'total' => 0, 'missing' => []];
        continue;
    }

    $missing = [];
    $found = 0;
    foreach ($symbols as $symbol) {
        if (str_contains($haystack, $symbol)) {
            ++$found;
        } else {
            $missing[] = $symbol;
        }
    }

    $verdict = match (true) {
        $found === 0 => 'MISSING',
        $missing !== [] => 'PARTIAL',
        default => 'OK',
    };

    $results[] = [
        'id' => $id,
        'status' => $status,
        'verdict' => $verdict,
        'found' => $found,
        'total' => count($symbols),
        'missing' => $missing,
        'class' => is_array($entry['class'] ?? null) ? implode(',', $entry['class']) : ($entry['class'] ?? ''),
        'digest_file' => $digestFile,
    ];
}

if ($asJson) {
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
    exit(array_any_missing($results) ? 1 : 0);
}

$counts = array_count_values(array_column($results, 'verdict'));
printf(
    "Audited %d entries — OK:%d PARTIAL:%d MISSING:%d NO-SYMBOLS:%d NO-DIGEST:%d\n",
    count($results),
    $counts['OK'] ?? 0,
    $counts['PARTIAL'] ?? 0,
    $counts['MISSING'] ?? 0,
    $counts['NO-SYMBOLS'] ?? 0,
    $counts['NO-DIGEST'] ?? 0
);

$flagged = array_values(array_filter(
    $results,
    static fn (array $r): bool => in_array($r['verdict'], ['MISSING', 'PARTIAL', 'NO-DIGEST'], true)
));

if ($flagged === []) {
    echo "\nNo drift — every recorded digest's target symbols are present.\n";
    exit(0);
}

echo "\n";
foreach ($flagged as $r) {
    if ($r['verdict'] === 'NO-DIGEST') {
        printf("  NO-DIGEST  #%-9s [%s] — digest file no longer in repos/drupal-digests\n", $r['id'], $r['status']);
        continue;
    }
    printf(
        "  %-9s #%-9s [%s] %d/%d symbols%s\n             missing: %s\n",
        $r['verdict'],
        $r['id'],
        $r['status'],
        $r['found'],
        $r['total'],
        $r['class'] !== '' ? '  class=' . $r['class'] : '',
        implode(', ', $r['missing'])
    );
}

if (!$quiet) {
    echo <<<'NOTE'

    MISSING  = recorded as implemented, but none of the digest's target symbols
               appear in src/, config/ or tests/. Treat as still pending and
               verify by hand before trusting it.
    PARTIAL  = some symbols covered, some not. Often a multi-symbol digest where
               only part was implemented — check the missing ones. Placeholder
               names from code samples can also land here; confirm before acting.
    NO-DIGEST= the digest file was removed upstream (the digests repo rebases),
               so the entry can no longer be verified against a source rule.

    NOTE

;
}

exit((($counts['MISSING'] ?? 0) > 0) ? 1 : 0);

function array_any_missing(array $results): bool
{
    foreach ($results as $r) {
        if ($r['verdict'] === 'MISSING') {
            return true;
        }
    }

    return false;
}
