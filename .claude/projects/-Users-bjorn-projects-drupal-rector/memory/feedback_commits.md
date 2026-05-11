---
name: Commit style
description: Rules about what to include (or not) in git commits and pushes in this project
type: feedback
---

Never add "Co-Authored-By" or any AI attribution to commits.

**Why:** User preference — keep commit history clean.

**How to apply:** Omit any Co-Authored-By trailer from all commit messages.

---

Never push or commit without explicit user instruction.

**Why:** User wants to control when code is committed and pushed. Do not commit after implementing a feature or running tests unless the user explicitly says to commit.

**How to apply:** Complete the implementation and tests, then stop. Do not run `git commit` or `git push` unless the user says so.
