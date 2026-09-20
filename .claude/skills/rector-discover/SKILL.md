---
name: rector-discover
description: Lists unimplemented drupal-digests rules ranked by real contrib impact and classified by implementation phase. Regenerates docs/rector-index.yml if absent or stale (>24h), audits docs/implemented-digests.yml for entries recorded as done that are not, and queries the drupal-code-query index for per-rule contrib exposure. Use to find what to work on next, filter by phase, or get a summary count.
argument-hint: "[--phase 1a|1b|1c|2|3|4] [--limit N] [--pending-only] [--no-impact]"
allowed-tools: Bash, Read, mcp__drupal-code-query__get_change_record, mcp__drupal-code-query__lookup_core_symbol, mcp__drupal-code-query__search_code
---

# Rector Discover

Show which drupal-digests deprecation rules still need to be implemented in drupal-rector, grouped by phase.

## Steps

### 1. Ensure the digests repo is available

The canonical path is `repos/drupal-digests` (inside ddev: `/var/www/html/repos/drupal-digests`). Always run the setup script first to clone or update the repositories:

```bash
bash .claude/scripts/setup-repos.sh
```

### 2. Ensure the index is fresh

Update the `docs/rector-index.yml`:

```bash
INDEX="docs/rector-index.yml"
echo "Regenerating rector-index.yml…"
php .claude/scripts/generate-rector-index.php --digests-path=repos/drupal-digests
```

### 3. Read the index

Read `docs/rector-index.yml` completely.

### 3b. Apply the authoritative implemented-digests record

The index matcher is sometimes unstable and marks already-implemented digests as
`pending` (e.g. a rector whose `@see` cites a change-record number ≠ the digest
issue, or code that lives in an open PR not yet merged to main). The hand-maintained
`docs/implemented-digests.yml` is the source of truth for what is actually done, and
it always wins over the index.

Apply the overlay with the script — it computes the post-overlay header counts and
prints the true pending list, so the arithmetic is never done by hand:

```bash
php .claude/scripts/apply-implemented-overlay.php
```

Every issue under `digests:` takes its status from the record, which overrides the
index. `implemented`, `config-only` and `skip` mean **not pending**. `pending` is a
correction the audit forced — an earlier `implemented` claim that turned out to be
false; the digest goes back on the list and the entry survives with its `note` so the
same wrong claim is not filed again.

The script also reports **in flight** issues: pending here, but already recorded as
done on another branch (an active worktree or an open PR). That happens because
`rector-implement` appends to the record on its own branch, so `main` cannot see the
work until it merges. Report these as awaiting merge and **never suggest one as the
next action** — the rule is already written.

Read the record itself when you need an entry's `note`:

```bash
[ -f docs/implemented-digests.yml ] && cat docs/implemented-digests.yml
```

### 3c. Audit that record against the codebase

Step 3b makes `implemented-digests.yml` authoritative, which also makes it the one
file that can silently hide work: an entry claiming `implemented` when nothing was
implemented removes that digest from the pending list permanently, and nothing else
in the pipeline notices. Always run the audit:

```bash
php .claude/scripts/audit-implemented-digests.php
```

It extracts each digest's **target symbols** and checks they appear in `src/`,
`config/` or `tests/`. (Grepping the issue number does not work — rectors
overwhelmingly cite the change-record node instead, and config-only entries cite
nothing.) Exit code is 1 when anything is `MISSING`. Entries at `skip` or `pending`
are not audited — neither one claims the work exists, so neither can be a false claim
of completion.

Verdicts:

| Verdict | Meaning | Action |
|---|---|---|
| `OK` | every target symbol present | none |
| `MISSING` | **none** of the symbols present — recorded as done but is not | verify by hand, then treat as pending and correct the record |
| `PARTIAL` | some symbols present, some not | check each named symbol — often a multi-symbol digest only partly implemented. It can also fire benignly when the digest names a type-guard base class the rector guards differently. Benign is a conclusion you reach by checking, never the default |
| `NO-DIGEST` | digest file removed upstream (the digests repo rebases) | entry can no longer be verified against a source rule; leave as-is unless the rector is also gone |
| `NO-SYMBOLS` | nothing extractable (custom rector with no quoted identifiers) | not verifiable by this method; ignore |

#### How to verify a `MISSING` or `PARTIAL` — read this before dismissing one

The audit searches for symbol names. Confirming or clearing its verdict is a
different question: **does the codebase perform the digest's transform?** Answer it
against the mapping, not the name.

- **Read the digest's Before/After block and its actual config** — the
  `RenameClassRector` array, the `*Configuration` entries — and find that same
  mapping in `config/` or `src/`. A symbol that appears only in a docblock, a class
  description or a comment is **not** coverage. `rg <symbol> src/` matching prose is
  the single easiest way to clear a real gap by mistake.
- **Check the cited node.** If the recorded rector's `@see` points at a different
  issue or change record than the digest does, it is almost certainly a different
  transform that happens to touch nearby symbols. Two rules can share a subsystem and
  cover nothing of each other.
- **Believe the digest's own caveats.** They frequently say which neighbouring
  transform is *not* covered; a rector doing exactly that excluded thing is not the
  implementation.
- **Then check `repos/drupal-core`** — the digest may target a core change that was
  later reverted, which makes the gap moot rather than urgent.

Worked example: `#3573954` is recorded as `GetDrupalRootToRootPropertyRector`. That
rector does `getDrupalRoot()` → `$this->root` and cites node/3589047; the digest is a
trait rename (`TestRequirementsTrait` → `DrupalTestCaseTrait`) and says in as many
words that `getDrupalRoot()` calls are *not* rewritten by its rule. The trait name
appears nowhere in `config/`, `src/` or `tests/`. Real gap — but a name-grep across
`src/` hits the rector's own docblock and looks like coverage.

**Report `MISSING` and `PARTIAL` entries alongside the pending list** — they are
undiscovered work, not bookkeeping noise. Do not silently fix the YAML. Once verified,
correct the record by setting `status: pending` with a `note` saying what was actually
recorded and why it was wrong; that restores the digest to the pending list without
losing the history.

### 3d. Rank the pending list by real contrib impact

Skip this step if `$ARGUMENTS` contains `--no-impact`, or if the caller only asked for a
count — it costs roughly one MCP call per pending change record (~30). Do run it whenever
the question is "what should I work on next".

Phase order says how *hard* a rule is, not how much it *matters*. A Phase 1a rule
nobody uses is worth less than a Phase 2 rule sitting in 200 modules, so rank the
pending list by contrib exposure before suggesting what to work on.

First resolve each pending digest to its change record and target symbols:

```bash
php .claude/scripts/resolve-pending-change-records.php
```

Digest files cite only their own issue node, and those are all `project_issue`, never
`changenotice` — so the CR comes from the drupal.org reverse lookup
(`?type=changenotice&field_issues=<issue>`), which the script does and caches. It
prints a de-duplicated list of CR nids at the end.

Then, for each nid, call **`mcp__drupal-code-query__get_change_record`** (`top_projects: 5`)
and read:

- `impact.projects_outstanding` — projects still on the legacy side (the headline number)
- `impact.branches.legacy` / `.migrated` — how far contrib has already moved
- `impact.top_projects[]` — each with `installs`, to weight by reach

Some CRs return `no reviewed change record with nid …` — roughly 4 of 31 on a full run.
**This never means the nid is wrong.** The resolve script finds nids through
`?type=changenotice&field_issues=<issue>`, so every one it prints is a real, published
change record correctly linked to that issue; the message means only that the CR is
outside this MCP's curated subset. Do not go looking for a bad node id, and do not treat
the message as a dead end.

**Always complete the fallback — every rule ends the step with a number.** Call
**`mcp__drupal-code-query__lookup_core_symbol`** on a symbol the script printed and use
`usage.dev_branches.projects` plus `usage.top_projects[].installs`. For a symbol with no
catalog entry at all, `mcp__drupal-code-query__search_code` with `by_repo: true` gives a
project-level count.

This applies to rules you are about to set aside as well. A rule that is **blocked** on a
reverted core change still needs its exposure figure — that number is what says whether
the re-land is worth watching. Skipping the fallback because the rule is not actionable
today is the easiest way to lose it: `#3530640` looks like a dead entry until you fetch
`UserAuthenticationController` and find 14 projects, among them email_registration
(23,433), login_emailusername (15,795) and tfa (15,012).

**Caveat — do not quote a CR's project count as the rule's impact.** A change record
covers every symbol it touches, not just the one the digest targets. The
`template_preprocess_HOOK` CR reports 509 projects across the whole glob, while the
`template_preprocess_block` slice specifically is 2. When a CR bundles several symbols,
use `lookup_core_symbol` for the per-symbol figure.

Group the results into tiers and note anything with **0 outstanding projects** — contrib
has already migrated and the rule is not worth building.

The most recent full run of this is written up in `docs/rector-pending-impact.md`; refresh
that file rather than starting a new one.

### 4. Apply filters

If `$ARGUMENTS` contains `--phase X`, show only entries with `phase: 'X'`.
If `$ARGUMENTS` contains `--limit N`, show only the first N entries.
If `$ARGUMENTS` contains `--pending-only`, show only `status: pending` entries (default unless --all is passed).
If `$ARGUMENTS` contains `--no-impact`, skip the contrib-impact ranking (Step 3d) and fall back to phase order.

Issues recorded in `implemented-digests.yml` (Step 3b) are never shown as pending.

### 5. Present results

Print the header **exactly as `apply-implemented-overlay.php` produced it** in Step 3b,
with the audit line from Step 3c appended. Do not recompute or adjust the counts, and
do not show deltas against the raw index — the script already applied the overlay:

```
Rector Index — <timestamp>  (N entries from implemented-digests.yml applied)
  implemented: X   config-only: Y   skip: S   pending: Z
  audit: A missing, B partial   (Step 3c)
```

If the script reported anything **in flight**, list it right after the header so it is
not mistaken for available work:

```
In flight — implemented on another branch, awaiting merge
  #3595652 — feat/module-weight-3595652
```

If Step 3c flagged anything, list it directly under the header, before the pending
phases — a `MISSING` entry is higher-priority than most of the pending list, because
it is work everyone currently believes is already done:

```
Audit findings — recorded as done, not found in the codebase
  [MISSING] #3525077 — mysql/pgsql/sqlite driver query subclass renames
     recorded as: ReplacePdoFetchConstantsRector (handles a different transform)
  [PARTIAL] #3501136 — 16/19 symbols; missing template_preprocess_block
```

Then list pending entries grouped by phase in order: 1a → 1b → 1c → 2 → 3 → 4 → unknown. Eg: `Phase 3 — Remove function call / node removal`

For each pending entry show:
```
[Phase 2] ReplaceSessionManagerDeleteRector — issue [#3577376](https://www.drupal.org/i/3577376)
  Digest: replace-deprecated-sessionmanager-delete-with-3577376.php
```

If all rules are implemented, print:
```
All rules are implemented or have config-only entries. Nothing pending.
```

### 6. Suggest next action

Suggest what to work on next by **impact first** (Step 3d), using phase only to break
ties — a lower phase is cheaper to build, so among similar exposure prefer 1a → 1b → 1c
→ 2 → 3 → 4. Show the number that justifies the pick, and skip anything that is **in
flight** (Step 3b), has 0 outstanding projects, or is blocked on a reverted core change.

```
Next suggested: /rector-implement repos/drupal-digests/rector/rules/<digest_file>
  #<issue> — <N> projects / <M> legacy branches, phase <P>
  Heaviest: <project> (<installs>), <project> (<installs>)
```

If Step 3c reported a `MISSING` entry, surface it here too — it is work believed done
that is not, and usually outranks the pending list.

## Phase Reference

| Phase | Description                                                                     | Generic rector |
|-------|---------------------------------------------------------------------------------|----------------|
| 1a | FuncCall → service call (`fn(...)` ->  `\Drupal::service(...)`                   | `FunctionToServiceRector` |
| 1a | FuncCall → method on first arg (`fn($obj)` → `$obj->method()`)                  | `FunctionToFirstArgMethodRector` |
| 1a | Service ID rename (`\Drupal::service('old')` → `\Drupal::service('new')`)       | `DrupalServiceRenameRector` |
| 1b | FuncCall → static call on class (`fn(...)` -> `Class::method(...)`              | `FunctionToStaticRector` |
| 1c | Class constant → class constant (`OldClass::OLD` → `NewClass::NEW`)             | `ClassConstantToClassConstantRector` |
| 1c | Bare global constant → class constant (`DEPRECATED_CONST` → `\Ns\Class::CONST`) | `ConstantToClassConstantRector` |
| 2 | MethodCall rename with type check (`$obj->old()` → `$obj->new()`)               | `MethodToMethodWithCheckRector` |
| 2 | MethodCall custom transformation                                                | custom `AbstractRector` or `AbstractDrupalCoreRector` |
| 3 | Remove a function call statement with no replacement                            | `FunctionCallRemovalRector` |
| 3 | Node removal (other patterns)                                                   | custom class returning `REMOVE_NODE` |
| 4 | Complex / multi-node                                                            | custom class |
