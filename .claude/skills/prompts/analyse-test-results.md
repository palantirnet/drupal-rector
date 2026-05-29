---
Analyze the rector test log generated after running the rector-live-test. The user wants to know:
1. Which rectors might not be working correctly (failures, errors, unexpected behavior)
2. Which rectors should trigger but don't (missed transformations)

Read the file in chunks (it's large, use offset/limit). Look for:
- Test FAILED or ERROR lines
- Rectors where "0 files changed" when changes were expected
- PHPStan errors or exceptions during rector runs
- Any patterns suggesting a rector ran but didn't apply its transformation
- Any "no changes" results that seem wrong
- Skipped tests or rectors with warnings

Produce a structured report:
- Section 1: Rectors with failures/errors (list rector name + what went wrong)
- Section 2: Rectors that appear to not trigger when they should (list rector name + why you suspect it)
- Section 3: Any other notable issues

Be specific with rector class names and test names where available.

Add a table at the end of the report with key metrics like; rectors with changes, rectors wihtout changes, rectors skipped.
