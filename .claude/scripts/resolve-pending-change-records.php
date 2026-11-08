<?php

declare(strict_types=1);

/**
 * Resolves each pending digest to its Drupal.org change record + target symbols,
 * so the impact of the pending list can be queried from the drupal-code-query
 * index and the roadmap ordered by real contrib exposure instead of phase.
 *
 * WHY THE REVERSE LOOKUP
 * ----------------------
 * Digest rule files cite only their own issue node, and every one of those is a
 * `project_issue`, not a `changenotice` — so there is no change record to read
 * straight out of the digest. The drupal.org API supports the reverse direction:
 *
 *   /api-d7/node.json?type=changenotice&field_issues=<issue-id>
 *
 * which lists the change record(s) naming that issue. Measured over the whole
 * pending list, this resolved a CR for every single entry.
 *
 * Responses are cached under .claude/cache/drupal-issues/ (gitignored), sharing
 * the plain <node>.json files with generate-rector-index.php and adding
 * rev-<issue>.json for the reverse lookups.
 *
 * Usage:
 *   php .claude/scripts/resolve-pending-change-records.php [--json]
 *
 * Feed the resulting nids to mcp__drupal-code-query__get_change_record. For the
 * handful of CRs missing from that index's reviewed set, fall back to
 * mcp__drupal-code-query__lookup_core_symbol on the symbols printed here.
 */

$repoRoot = dirname(__DIR__, 2);
$indexFile = $repoRoot . '/docs/rector-index.yml';
$implFile = $repoRoot . '/docs/implemented-digests.yml';
$rulesDir = $repoRoot . '/repos/drupal-digests/rector/rules';
$cacheDir = $repoRoot . '/.claude/cache/drupal-issues';

$asJson = in_array('--json', $argv, true);

if (!file_exists($indexFile)) {
    fwrite(STDERR, "Missing docs/rector-index.yml — run generate-rector-index.php first.\n");
    exit(1);
}
if (!is_dir($cacheDir) && !mkdir($cacheDir, 0755, true) && !is_dir($cacheDir)) {
    fwrite(STDERR, "Could not create $cacheDir\n");
    exit(1);
}

const USER_AGENT = 'drupal-rector-index (+https://github.com/palantirnet/drupal-rector)';

function fetchCached(string $url, string $cacheFile): ?array
{
    if (!file_exists($cacheFile)) {
        $ctx = stream_context_create(['http' => [
            'user_agent' => USER_AGENT,
            'timeout' => 20,
            'ignore_errors' => true,
        ]]);
        $json = @file_get_contents($url, false, $ctx);
        if ($json === false || $json === '') {
            return null;
        }
        file_put_contents($cacheFile, $json);
        usleep(300_000); // be polite to drupal.org
    } else {
        $json = file_get_contents($cacheFile);
    }

    $data = json_decode($json, true);

    return is_array($data) ? $data : null;
}

function loadNode(string $id, string $cacheDir): ?array
{
    return fetchCached("https://www.drupal.org/api-d7/node/$id.json", "$cacheDir/$id.json");
}

/**
 * Change records naming this issue in their field_issues.
 *
 * @return array<int, array{nid: string, title: string}>
 */
function changeRecordsForIssue(string $issueId, string $cacheDir): array
{
    $data = fetchCached(
        "https://www.drupal.org/api-d7/node.json?type=changenotice&field_issues=$issueId",
        "$cacheDir/rev-$issueId.json"
    );

    $out = [];
    foreach (($data['list'] ?? []) as $node) {
        $out[] = ['nid' => (string) $node['nid'], 'title' => $node['title'] ?? ''];
    }

    return $out;
}

/** @return string[] */
function targetSymbols(string $path): array
{
    if (!file_exists($path)) {
        return [];
    }
    $src = file_get_contents($path);
    $body = preg_replace('#^\s*<\?php.*?\*/#s', '', $src, 1) ?? $src;
    preg_match_all("/'([A-Za-z_\\\\][A-Za-z0-9_\\\\]{5,})'/", $body, $m);

    $skip = ['Drupal', 'service', 'class', 'CODE_SAMPLE', 'value', 'method', 'action'];
    $symbols = [];
    foreach (array_unique($m[1]) as $raw) {
        $normalised = str_replace('\\\\', '\\', $raw);
        $short = str_contains($normalised, '\\')
            ? substr($normalised, strrpos($normalised, '\\') + 1)
            : $normalised;
        if (in_array($short, $skip, true) || preg_match('/^(my_?|some_?|example|foo|mymodule)/i', $short)) {
            continue;
        }
        $symbols[$normalised] = true;
    }

    return array_slice(array_keys($symbols), 0, 8);
}

$index = yaml_parse_file($indexFile);
$overrides = file_exists($implFile) ? (yaml_parse_file($implFile)['digests'] ?? []) : [];

$rows = [];
foreach ($index['entries'] as $id => $entry) {
    $id = (string) $id;
    $status = isset($overrides[$id]) ? $overrides[$id]['status'] : $entry['status'];
    if ($status !== 'pending') {
        continue;
    }

    $digestFile = $entry['digest_file'] ?? '';
    $path = $rulesDir . '/' . $digestFile;

    $self = loadNode($id, $cacheDir);
    $selfType = $self['type'] ?? 'unknown';

    $crs = [];
    // A digest occasionally cites a CR directly; prefer that when present.
    if (file_exists($path)) {
        preg_match_all('#drupal\.org/(?:node|i)/(\d+)#', file_get_contents($path), $m);
        foreach (array_unique($m[1]) as $cited) {
            if ($cited === $id) {
                continue;
            }
            $node = loadNode($cited, $cacheDir);
            if (($node['type'] ?? '') === 'changenotice') {
                $crs[$cited] = $node['title'] ?? '';
            }
        }
    }
    if ($self !== null && $selfType === 'changenotice') {
        $crs[$id] = $self['title'] ?? '';
    }
    // The normal path: reverse-lookup the CR that names this issue.
    if ($crs === [] && $selfType === 'project_issue') {
        foreach (changeRecordsForIssue($id, $cacheDir) as $cr) {
            $crs[$cr['nid']] = $cr['title'];
        }
    }

    $rows[] = [
        'issue' => $id,
        'phase' => (string) ($entry['phase'] ?? 'unknown'),
        'digest_file' => $digestFile,
        'change_records' => $crs,
        'symbols' => targetSymbols($path),
    ];
}

if ($asJson) {
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
    exit(0);
}

printf("Resolved %d pending digests\n\n", count($rows));
foreach ($rows as $row) {
    printf("#%-9s [phase %s]  %s\n", $row['issue'], $row['phase'], $row['digest_file']);
    if ($row['change_records'] === []) {
        echo "   CR:      none found — use lookup_core_symbol on the symbols below\n";
    } else {
        foreach ($row['change_records'] as $nid => $title) {
            printf("   CR %-9s %s\n", $nid, $title);
        }
    }
    if ($row['symbols'] !== []) {
        printf("   symbols: %s\n", implode(', ', $row['symbols']));
    }
    echo "\n";
}

$nids = [];
foreach ($rows as $row) {
    foreach (array_keys($row['change_records']) as $nid) {
        $nids[$nid] = true;
    }
}
printf("Distinct change records to query: %d\n%s\n", count($nids), implode(' ', array_keys($nids)));
