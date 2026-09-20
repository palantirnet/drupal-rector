# Testing the rector-discover changes

A before/after check on the 2026-09-19 additions to this skill (steps 3c and 3d).
The baseline below is a **real run of the old skill**, captured earlier the same day
before the changes existed. Criteria are written down *before* the new run so the
comparison can actually fail.

---

## What changed

| | Old | New |
|---|---|---|
| Verifies `implemented-digests.yml`? | no — trusted blindly | **3c** — `audit-implemented-digests.php` |
| Contrib impact data? | none | **3d** — `resolve-pending-change-records.php` + `drupal-code-query` MCP |
| "Next suggested" chosen by | phase order | impact, phase as tiebreak |
| `allowed-tools` | `Bash, Read` | + 3 `mcp__drupal-code-query__*` |

---

## Baseline — old skill, 2026-09-19 08:24

```
Rector Index — 2026-09-19 08:24:37   (139 entries from implemented-digests.yml applied)
  implemented: 117   config-only: 43   skip: 5   pending: 31
```

Pending grouped by phase: 1a (13), 1b (2), 1c (3), 2 (2), 3 (2), 4 (1), unknown (8).

Next suggested: `replace-user-mail-notify-calls-with-notificationhandler-3539178.php`
— the first Phase 1a entry judged interesting.

What the baseline did **not** do:

- never checked whether the 134 non-skip records were true, so **#3525077 and #3573954
  were invisible** — recorded as implemented, actually absent
- no per-rule contrib numbers
- did not flag that six pending rules have **0 outstanding projects** (contrib already
  migrated) and are not worth building
- did not flag the two rules blocked on reverted core changes

Since that run, `#2025089` was implemented, so the new pending count should be **30**.

---

## Pre-registered success criteria

Judged only against the new run's output. Each is checkable, not a matter of taste.

**Must hit (otherwise the change is not an improvement):**

1. Reports pending as **30**, and `#2025089` is absent from the pending list.
2. Runs the audit and reports **`#3525077` and `#3573954` as MISSING** — the two records
   that claim work that does not exist.
3. "Next suggested" is justified by **a contrib number for that specific rule**
   (`projects_outstanding` / legacy branches / installs), not by phase position.
4. Cites impact figures for **several** pending rules, not just one — see the confound
   below for why "several" matters.

**Should hit (real value, but not disqualifying):**

5. Deprioritises or flags the **0-outstanding** rules: `#2951046`, `#2934063`,
   `#3561302`, `#3488467`, `#3506605`, `#3581816`.
6. Flags `#3590050` and `#3530640` as blocked on reverted core changes.
7. Picks a genuine Tier-1 rule — `#3595652` (202 projects) or `#2012976` (134) should
   outrank `#3539178`, which the old run picked on phase order.

**Watch for regressions — the change is a net loss if:**

8. The skill is now long enough that steps get skipped, or the ~30 MCP calls make the
   run so slow it is abandoned midway.
9. The audit's `PARTIAL` output is treated as findings. Three of the five current
   PARTIALs are benign (`#3410938` `getMaxSeverity`, `#3421202` `WebDriverTestBase`,
   `#3505370` `FilterBase`) — they name a type-guard base class the rector guards
   differently. Reporting those as gaps is noise, and would mean the signal/noise ratio
   needs tightening.
10. Step 3d is skipped entirely despite the question being "what next" — that would mean
    the `--no-impact` guidance reads as too permissive.

### Confound to control for

This project injects prior-session memory at startup, and that memory already contains
`_user_mail_notify (57 projects)` from earlier impact work. A fresh context could recite
that one figure **without running anything**. So criterion 4 requires impact numbers for
*several* rules, and the run should show the two scripts actually executing. One recalled
number proves nothing.

> **`/clear` is not enough.** The claude-mem `SessionStart` hook re-injects prior
> observations into *every* new session in this repo, and after the first scored run
> those observations contain the answer key almost verbatim — the pending count 37→30,
> both MISSING ids, `#3595652 (202 projects)`, `#3539178 at 58 projects`. The 15:15
> re-run was contaminated exactly this way. **Disable the claude-mem SessionStart hook
> before a scoring run**, and score from the transcript's tool calls rather than from the
> output file alone, since a file cannot show whether a number was computed or recalled.

---

## The prompt

Paste this into a **new context** in this repo. It is deliberately neutral — it says
nothing about audits, impact or what changed, so the skill has to produce that on its own.

```
/rector-discover

When you're done, also save your full final answer verbatim to
docs/_discover-run-new.md so it can be reviewed.
```

Then come back here and say it's done — the output file is enough to score against the
criteria above.

---

## Scoring

Compare `docs/_discover-run-new.md` against the baseline and walk criteria 1–10 in order,
marking each hit/miss with the evidence from the file. State an overall verdict of
**improved / no better / regressed**, and if any of 8–10 fired, say what to change rather
than defending the current shape.

`docs/_discover-run-new.md` is a scratch artifact — delete it once scored.

---

## Result — scored 2026-09-19

**Verdict: improved.** Criteria 1–5 and 7 hit; 8 and 10 did not fire. Criterion 6 is
stale — `#3590050` has re-landed in core, and both new-skill runs said so instead of
repeating "blocked". Baseline picked `#3539178` (58 projects) on phase order; the new
skill picks a Tier-1 rule with 2–3.5× the reach, flags six 0-outstanding rules that were
previously invisible, and surfaced two false `implemented` records.

**Criterion 9 fired, inverted.** The risk is not over-reporting benign PARTIALs as gaps;
it is dismissing real ones as benign. Two runs of the same new skill disagreed three
times and the later run was wrong each time:

- `#3573954` — called an audit false positive because `rg` matched the trait names in a
  docblock. It is a real gap.
- `#3410938` — called benign. `SystemManager::getMaxSeverity()` is genuinely uncovered.
- header counts — deltas invented instead of computed (`117 (+3)` vs. the correct
  `implemented 118, config-only 43, skip 5`).

A fourth miss surfaced on review: of the four CRs that returned `no reviewed change
record` (3539363, 3035565, 3595589, 3552724), the later run completed the
`lookup_core_symbol` fallback for three and **skipped 3552724 entirely**, declaring
`#3530640` blocked from memory. The conclusion was right — core's
`539a0b893f5 Revert "task: #3530640 …"` confirms it — but the unfetched number was
material: 14 projects, email_registration (23,433), login_emailusername (15,795), tfa
(15,012). All four nids are real published `changenotice` nodes correctly linked to
their issues, so the message never indicates a bad nid.

Both runs also listed `#3595652` as pending while it sat implemented in a worktree with
an open PR, and the earlier run recommended it as the top next action.

**Fixes applied to the skill the same day:**

1. Step 3c gained a *How to verify* section — check the digest's mapping, not the symbol
   name; a docblock mention is not coverage; a rector citing a different node is not the
   implementation. Carries `#3573954` as a worked example.
2. Step 3b is now `.claude/scripts/apply-implemented-overlay.php`, which computes the
   header counts; Step 5 says to print them verbatim rather than doing arithmetic.
3. That script also scans worktree and open-PR branches for record entries missing
   locally and marks those issues `IN FLIGHT`; Steps 3b and 6 exclude them from
   suggestions.
4. `implemented-digests.yml` gained a `pending` status so a disproven `implemented` claim
   returns to the list while keeping a `note` explaining the error. `#3525077` and
   `#3573954` were corrected this way.
5. Step 3d now states that an unreviewed CR never means a bad nid (the resolve script
   filters on `type=changenotice`), requires the fallback to be completed so every rule
   ends the step with a number, and says blocked rules need their exposure figure too —
   with `#3530640` as the cautionary example.

Re-scoring these changes needs a run with the memory hook off.
