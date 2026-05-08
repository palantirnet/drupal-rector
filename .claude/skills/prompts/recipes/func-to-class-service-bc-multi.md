# Recipe: Multiple FuncCalls → `::class` service methods (BC-wrapped)

**Use when:** several deprecated global functions all map to methods on the same
`\Drupal::service(SomeClass::class)` and were introduced in Drupal >= 10.1.0.

**Output:** one new custom rector class + test suite (4–5 files).

This is the multi-function variant of `func-to-class-service-bc.md`. Use that recipe instead
when there is only one function to replace.

---

## Step 1 — Extract these values from the digest

**Per-class values:**

| Placeholder | Where to find it |
|---|---|
| `{{ClassName}}` | PHP class name in the digest file |
| `{{serviceClass}}` | Shared service FQCN, e.g. `Drupal\node\NodeAccessRebuild` |
| `{{introducedVersion}}` | From issue markdown `## Impact`, e.g. `11.4.0` |
| `{{removedVersion}}` | From issue markdown `## Impact`, e.g. `13.0.0` |
| `{{issueNumber}}` | From filename or `@see` comment |

**Per-function values** (repeat for each deprecated function):

| Placeholder | Example |
|---|---|
| `{{funcN}}` | `node_access_rebuild` |
| `{{methodN}}` | `rebuild` |
| Arg handling | "forward all args" OR "check count first" (see variants below) |

---

## Step 2 — Determine the target config file

Same lookup as `func-to-class-service-bc.md` Step 2.

---

## Step 3 — Write the rector class

Path: `src/Drupal11/Rector/Deprecation/{{ClassName}}.php`

```php
<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated {{func1}}() (and related functions) with the {{serviceClass}} service.
 *
 * Deprecated in drupal:{{introducedVersion}} and removed in drupal:{{removedVersion}}.
 *
 * @see https://www.drupal.org/node/{{issueNumber}}
 */
class {{ClassName}} extends AbstractDrupalCoreRector
{
    /** @var DrupalIntroducedVersionConfiguration[] */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    DrupalIntroducedVersionConfiguration::class
                ));
            }
        }
        parent::configure($configuration);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$node->name instanceof Name) {
            return null;
        }

        return match ($node->name->toString()) {
            '{{func1}}' => $this->buildServiceCall('{{method1}}', $node->args),
            '{{func2}}' => $this->buildServiceCall('{{method2}}', $node->args),
            // add more cases here
            default => null,
        };
    }

    /** @param \PhpParser\Node\Arg[] $args */
    private function buildServiceCall(string $method, array $args): MethodCall
    {
        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified('{{serviceClass}}'), 'class'))]
        );

        return new MethodCall($serviceCall, $method, $args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated {{func1}}() and related functions with the {{serviceClass}} service.',
            [
                new ConfiguredCodeSample(
                    '{{before1}}',
                    '{{after1}}',
                    [new DrupalIntroducedVersionConfiguration('{{introducedVersion}}')]
                ),
                // add more ConfiguredCodeSample entries for each function
            ]
        );
    }
}
```

### Variant: arg-count dispatch (getter/setter pattern)

When one function behaves differently based on whether args are passed (e.g.
`needs_rebuild()` = getter, `needs_rebuild($value)` = setter), replace the match
arm with an if/else:

```php
'{{funcN}}' => count($node->args) === 0
    ? $this->buildServiceCall('{{getterMethod}}', [])
    : $this->buildServiceCall('{{setterMethod}}', $node->args),
```

---

## Steps 4–9 — Test class, test config, fixtures, registration, quality checks, commit

Follow Steps 4–9 from `func-to-class-service-bc.md` exactly, substituting the
multi-function fixture below for Step 6.

### Fixture — include one representative call per function

`tests/src/Drupal11/Rector/Deprecation/{{ClassName}}/fixture/basic.php.inc`

```
<?php

{{before1}};
{{before2}};

?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '{{introducedVersion}}', fn() => {{after1_no_semi}}, fn() => {{before1_no_semi}});
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '{{introducedVersion}}', fn() => {{after2_no_semi}}, fn() => {{before2_no_semi}});

?>
```

The below-version fixture is identical before and after for all functions (no change).
