# Registration and version bounds

Every Drupal 10+ rule is registered **twice** and bound to the `drupal/core`
versions it applies to. The skills and recipes link here instead of repeating it.
Three custom PHPStan rules (`utils/PHPStan/Rule/`) enforce all of this, so a rule
that skips a step fails `ddev composer phpstan` — fix the registration, never
baseline those errors.

## 1. The two registrations

| File | What goes there |
|---|---|
| `config/drupal-<major>/drupal-<X.Y>-deprecations.php` (or `-breaking.php`) | The plain registration, as before: `rule()` or `ruleWithConfiguration()`. Source of truth for `PHPSTAN_MESSAGES` comments — `scripts/generate-coverage-registry.php` reads only these files. |
| `config/composer-based.php` | The same rule again, under the matching `// Drupal X.Y` (or `// Drupal X.Y (breaking)`) heading, in the same order as the per-minor file. Copy the comment block (`@see` URLs, "deprecated in … removed in …", `PHPSTAN_MESSAGES`) along with it. |

`ComposerBasedSetCoverageRule` fails when a rule is in a Drupal 10+ per-minor
config but missing from `composer-based.php`. Drupal 8/9 rules are deliberately
**not** in `composer-based.php`; never add one there.

A breaking rename (replacement symbol only exists from that minor on, see
`DRUPAL_11X_BREAKING`) goes in the per-minor `-breaking.php` file **and** under
the `(breaking)` heading in `composer-based.php`. The composer-based set can carry
breaking renames safely because it knows the installed core exactly.

## 2. The bound: `>=X.Y.0 <N.0.0`

- **Lower bound** = the version the deprecation was **introduced** in
  (`deprecated in drupal:11.4.0` → `>=11.4.0`).
- **Upper bound** = `<N.0.0` where `N` is the major **after** the one the API is
  removed in (`removed in drupal:13.0.0` → `<14.0.0`). Rector reads source, so a
  codebase can still contain a call that is already gone from core; those are the
  calls most worth rewriting, hence one major of slack.
- **No upper bound** only when core never removes the old form (e.g. a setting
  that is deprecated but has no removal version). Say so in the comment.
- Verify both numbers against `repos/drupal-core` (`@deprecated` / `trigger_error`
  text), not only the issue markdown — change records are often wrong about the
  removal version.

Format is enforced by `BoundRuleConfigurationRule`:
`#^>=(\d+)\.\d+\.\d+(?: <(\d+)\.0\.0)?$#`, upper major later than the lower one.

## 3. Where the bound lives — depends on how the rule is registered

**Configurable rule** (`AbstractDrupalCoreRector`, generic rectors such as
`FunctionToServiceRector`, `RenameClassRector`): the bound goes in
`composer-based.php`, one call **per deprecation** (even for generic rectors with
many entries):

```php
// https://www.drupal.org/node/3578055
// node_access_grants() deprecated in drupal:11.4.0, removed in drupal:13.0.0.
// Replaced by \Drupal\node\NodeGrantsHelper::nodeAccessGrants().
$rectorConfig->ruleWithConfigurationComposerVersionBound(FunctionToServiceRector::class, [
    new FunctionToServiceConfiguration('11.4.0', 'node_access_grants', 'Drupal\node\NodeGrantsHelper', 'nodeAccessGrants', true),
], 'drupal/core', '>=11.4.0 <14.0.0');
```

The per-minor file keeps its plain `ruleWithConfiguration()` — no bound there.

**Plain rule** (`AbstractRector`, registered with `rule()`): the bound goes on the
**rule class**, and both config files use a plain `rule()` call.
`PlainlyRegisteredRuleRule` fails otherwise.

```php
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

final class RemoveViewsRowCacheKeysRector extends AbstractRector implements ComposerPackageConstraintInterface, DocumentedRuleInterface
{
    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('drupal/core', '>=11.4.0 <14.0.0');
    }
```

**Important:** a class bound is filtered **globally** — Rector drops the rule
whenever the installed `drupal/core` does not satisfy it, including when it is
loaded through `Drupal11SetList` or a hand-written config, and including when
`drupal/core` is not installed at all. A set-local bound (configurable rules)
only applies inside `composer-based.php`. No BC-wrapping rule carries a class
bound.

## 4. Tests: two different version mechanisms

| Mechanism | Read by | Test default | Override |
|---|---|---|---|
| `\Drupal::VERSION` (stub `stubs/Drupal/Drupal.php`) | `AbstractDrupalCoreRector` BC wrapping | `11.99.x-dev` | `DrupalRectorSettings::setDrupalVersion()` |
| Installed `drupal/core` (stub `tests/composer-json/drupal-core-<major>.json`) | The class-bound filter above | Picked by test **namespace**: `Tests\Drupal11\…` → `11.99.99`, `Tests\Drupal12\…` → `12.99.99`; anything else → `12.0.0` (`drupal-core-installed.json`) | — (`AbstractDrupalRectorTestCase::provideComposerJsonFilePath()`) |

The pin of the test's namespace must **satisfy** a class-bound rule's bound.
Normally that means the `Drupal<major>` namespace of the lower bound (source and
tests live under the introduced major). A rule bound `>=11.4.0 <13.0.0` tested
outside `Tests\Drupal<major>` gets `12.0.0` — fine — but one bound `<12.0.0`
would be silently filtered there and every fixture would fail as "no change".

## 5. Checking what actually runs

`vendor/bin/rector composer-based --config <file>` lists every bound rule with
whether it is active against the installed `drupal/core`. Use it whenever a rule
produces no change on a real site (live tests, the project_analysis bot) — the
filter is silent in `rector process`.

Known trap: a `drupal.git` **main** checkout installs core as `dev-main`, which
satisfies no numeric bound, so every class-bound rule is dropped there.
`11.x` checkouts install as `11.x-dev` and satisfy every 11.x lower bound.
