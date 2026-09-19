<?php

declare(strict_types=1);

/**
 * Step 3b of the rector-discover skill.
 *
 * Overlays the hand-maintained docs/implemented-digests.yml onto the generated
 * docs/rector-index.yml, prints the post-overlay header counts, and prints the
 * true pending list. Exists so the header arithmetic is computed, not narrated.
 *
 * Also flags pending issues that are already implemented on another branch
 * (open PR or active worktree) so the skill stops recommending in-flight work.
 * Pass --no-inflight to skip that scan.
 */

$root = dirname(__DIR__, 2);
chdir($root);

const INDEX = 'docs/rector-index.yml';
const RECORD = 'docs/implemented-digests.yml';

$noInflight = in_array('--no-inflight', $argv, true);

/**
 * Parses the `digests:` map of an implemented-digests.yml payload.
 *
 * @return array<string, array{status: string, note: bool}>
 */
function parse_record(string $yaml): array
{
    $out = [];
    $cur = null;
    foreach (explode("\n", $yaml) as $line) {
        if (preg_match("/^  '([0-9]+)':/", $line, $m)) {
            $cur = $m[1];
            $out[$cur] = ['status' => '?', 'note' => false];
            continue;
        }
        if ($cur === null) {
            continue;
        }
        if (preg_match('/^    status:\s*(\S+)/', $line, $m)) {
            $out[$cur]['status'] = $m[1];
        } elseif (preg_match('/^    note:/', $line)) {
            $out[$cur]['note'] = true;
        }
    }

    return $out;
}

if (!is_file(INDEX)) {
    fwrite(STDERR, "missing ".INDEX." — run generate-rector-index.php first\n");
    exit(2);
}

// Parse the index.
$entries = [];
$i = -1;
foreach (file(INDEX) as $line) {
    if (preg_match("/^    issue:\s*'?([^'\n]+)'?/", $line, $m)) {
        $entries[] = ['issue' => trim($m[1]), 'status' => '?', 'phase' => 'unknown', 'digest' => '?'];
        $i = count($entries) - 1;
    } elseif ($i >= 0 && preg_match("/^    digest_file:\s*'?([^'\n]+)'?/", $line, $m)) {
        $entries[$i]['digest'] = trim($m[1]);
    } elseif ($i >= 0 && preg_match("/^    phase:\s*'?([^'\n]+)'?/", $line, $m)) {
        $entries[$i]['phase'] = trim($m[1]);
    } elseif ($i >= 0 && preg_match('/^    status:\s*(\S+)/', $line, $m)) {
        $entries[$i]['status'] = trim($m[1]);
    }
}
$generated = '?';
foreach (file(INDEX) as $line) {
    if (preg_match("/^generated:\s*'([^']+)'/", $line, $m)) {
        $generated = $m[1];
        break;
    }
}

$record = is_file(RECORD) ? parse_record((string) file_get_contents(RECORD)) : [];

// A `pending` status in the record is an explicit correction: the entry documents
// why a previous `implemented` claim was wrong, and the digest goes back on the list.
$applied = 0;
$overridden = [];
foreach ($entries as $k => $e) {
    if (!isset($record[$e['issue']])) {
        continue;
    }
    $new = $record[$e['issue']]['status'];
    ++$applied;
    if ($e['status'] !== $new) {
        $overridden[$e['issue']] = $e['status'].' -> '.$new;
    }
    $entries[$k]['status'] = $new;
}

$counts = array_count_values(array_column($entries, 'status'));
foreach (['implemented', 'config-only', 'skip', 'pending'] as $s) {
    $counts[$s] ??= 0;
}

$pending = array_values(array_filter($entries, fn ($e) => $e['status'] === 'pending'));

// In-flight scan: is a pending issue already recorded as done on another branch?
$inflight = [];
if (!$noInflight && $pending !== []) {
    $branches = [];
    exec('git worktree list --porcelain 2>/dev/null', $wt);
    foreach ($wt as $line) {
        if (str_starts_with($line, 'branch refs/heads/')) {
            $branches[] = substr($line, strlen('branch refs/heads/'));
        }
    }
    $ghStatus = 1;
    exec('gh pr list --state open --json headRefName -q ".[].headRefName" 2>/dev/null', $prs, $ghStatus);
    if ($ghStatus === 0) {
        $branches = array_merge($branches, array_filter($prs));
    }
    $branches = array_values(array_unique($branches));
    $here = trim((string) shell_exec('git branch --show-current 2>/dev/null'));

    foreach ($branches as $branch) {
        if ($branch === $here) {
            continue;
        }
        $yaml = shell_exec('git show '.escapeshellarg($branch.':'.RECORD).' 2>/dev/null');
        if (!is_string($yaml) || $yaml === '') {
            continue;
        }
        foreach (parse_record($yaml) as $issue => $meta) {
            // PHP casts numeric-string array keys to int, so normalise before comparing.
            $issue = (string) $issue;
            if (isset($record[$issue]) || $meta['status'] === '?' || $meta['status'] === 'pending') {
                continue;
            }
            foreach ($pending as $p) {
                if ($p['issue'] === $issue) {
                    $inflight[$issue] ??= [];
                    $inflight[$issue][] = $branch;
                }
            }
        }
    }
}

printf("Rector Index — %s  (%d entries from %s applied)\n", $generated, $applied, RECORD);
printf(
    "  implemented: %d   config-only: %d   skip: %d   pending: %d\n",
    $counts['implemented'],
    $counts['config-only'],
    $counts['skip'],
    $counts['pending']
);
if ($overridden !== []) {
    echo "\nOverridden by the record:\n";
    foreach ($overridden as $issue => $change) {
        printf("  #%s  %s\n", $issue, $change);
    }
}
if ($inflight !== []) {
    echo "\nIn flight — recorded as done on another branch, not yet on this one:\n";
    foreach ($inflight as $issue => $branches) {
        printf("  #%s  %s\n", $issue, implode(', ', array_unique($branches)));
    }
    echo "  (do not suggest these as next actions; report them as awaiting merge)\n";
}

$order = ['1a' => 0, '1b' => 1, '1c' => 2, '2' => 3, '3' => 4, '4' => 5, 'unknown' => 6];
usort($pending, function ($a, $b) use ($order) {
    $pa = $order[$a['phase']] ?? 9;
    $pb = $order[$b['phase']] ?? 9;

    return $pa <=> $pb ?: strcmp($a['issue'], $b['issue']);
});
echo "\nPending (".count($pending)."):\n";
foreach ($pending as $e) {
    printf(
        "[Phase %s]\t%s\t%s%s\n",
        $e['phase'],
        $e['issue'],
        $e['digest'],
        isset($inflight[$e['issue']]) ? "\t<- IN FLIGHT" : ''
    );
}
