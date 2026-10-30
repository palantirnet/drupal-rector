# Recipe: FuncCall → `::class` service method (BC-wrapped)

**Use when:** a single deprecated global function is replaced by
`\Drupal::service(SomeClass::class)->method()`, was introduced in Drupal >= 10.1.0,
**and** the replacement requires custom logic: arg-count dispatch to different methods,
chained calls (e.g. `->getFormat()->id()`), or method-on-first-arg mixed with a service call.

For a plain 1-to-1 mapping with no custom logic, use `FunctionToServiceConfiguration(..., true)`
as a config-only entry instead (see `config-only-template.md`).

**Output:** one new custom rector class + test suite (4–5 files).

---

## Step 1 — Extract these values from the digest

| Placeholder | Where to find it |
|---|---|
| `{{ClassName}}` | PHP class name in the digest file |
| `{{functionName}}` | The deprecated function name, e.g. `node_access_grants` |
| `{{serviceClass}}` | Fully-qualified service class, e.g. `Drupal\node\NodeGrantsHelper` |
| `{{methodName}}` | Method to call on the service, e.g. `nodeAccessGrants` |
| `{{introducedVersion}}` | From issue markdown `## Impact` section, e.g. `11.4.0` |
| `{{removedVersion}}` | From issue markdown `## Impact` section, e.g. `13.0.0` |
| `{{majorAfterRemoval}}` | Removal major + 1, e.g. `14` for `13.0.0` — the upper bound (`../version-bounds.md` §2) |
| `{{issueNumber}}` | From filename or `@see` comment, e.g. `2473041` |
| `{{beforeCode}}` | CodeSample "before" snippet, e.g. `node_access_grants($operation, $account);` |
| `{{afterCode}}` | CodeSample "after" snippet (clean, no BC wrapper), e.g. `\Drupal::service(\Drupal\node\NodeGrantsHelper::class)->nodeAccessGrants($operation, $account);` |

---

## Step 2 — Determine the target config file and bound

The per-minor file is named after the introduced minor:
`config/drupal-11/drupal-11.{{X}}-deprecations.php` (11.4.x → `drupal-11.4-deprecations.php`).
The rule is also registered in `config/composer-based.php` (Step 7).

The bound is `>={{introducedVersion}} <{{majorAfterRemoval}}.0.0` — the major **after** removal,
so `removed in drupal:13.0.0` → `<14.0.0` (`../version-bounds.md` §2). This recipe's class is
configurable (BC-wrapped), so the bound lives in `composer-based.php`, not on the class.

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
 * Replaces deprecated {{functionName}}() with the {{serviceClass}} service.
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

        if (!$node->name instanceof Name || $node->name->toString() !== '{{functionName}}') {
            return null;
        }

        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified('{{serviceClass}}'), 'class'))]
        );

        return new MethodCall($serviceCall, '{{methodName}}', $node->args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated {{functionName}}() with \Drupal::service(\{{serviceClass}}::class)->{{methodName}}().',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
{{beforeCode}}
CODE_BEFORE,
                    <<<'CODE_AFTER'
{{afterCode}}
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('{{introducedVersion}}')]
                ),
            ]
        );
    }
}
```

---

## Step 4 — Write the test class

Path: `tests/src/Drupal11/Rector/Deprecation/{{ClassName}}/{{ClassName}}Test.php`

```php
<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Drupal11\Rector\Deprecation\{{ClassName}};

use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Tests\AbstractDrupalRectorTestCase;

class {{ClassName}}Test extends AbstractDrupalRectorTestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('provideData')]
    public function testAboveVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('99.99.99');
        $this->doTestFile($filePath);
    }

    /** @return \Iterator<array<string>> */
    public static function provideData(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideDataBelowVersion')]
    public function testBelowVersion(string $filePath): void
    {
        static::getContainer()->make(DrupalRectorSettings::class)->setDrupalVersion('1.0.0');
        $this->doTestFile($filePath);
    }

    /** @return \Iterator<array<string>> */
    public static function provideDataBelowVersion(): \Iterator
    {
        return self::yieldFilesFromDirectory(__DIR__.'/fixture-below-version');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__.'/config/configured_rule.php';
    }
}
```

---

## Step 5 — Write the test config

Path: `tests/src/Drupal11/Rector/Deprecation/{{ClassName}}/config/configured_rule.php`

```php
<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\{{ClassName}};
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass({{ClassName}}::class, $rectorConfig, false, [
        new DrupalIntroducedVersionConfiguration('{{introducedVersion}}'),
    ]);
};
```

---

## Step 6 — Write the fixtures

### Main fixture (BC-wrapped output)

Path: `tests/src/Drupal11/Rector/Deprecation/{{ClassName}}/fixture/basic.php.inc`

```
<?php

{{beforeCode}}
?>
-----
<?php

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '{{introducedVersion}}', fn() => {{afterCode}}, fn() => {{beforeCode}});
?>
```

> **Note:** In the "after" section, strip any trailing `;` from `{{beforeCode}}` and `{{afterCode}}`
> when they appear inside the `backwardsCompatibleCall` arguments — the `;` belongs on the
> outer statement only. If `{{beforeCode}}` is wrapped in a variable assignment
> (e.g. `$x = fn();`), the BC call wraps only the right-hand side expression.

### Below-version fixture (no change)

Path: `tests/src/Drupal11/Rector/Deprecation/{{ClassName}}/fixture-below-version/basic.php.inc`

```
<?php

{{beforeCode}}
?>
-----
<?php

{{beforeCode}}
?>
```

---

## Step 7 — Register in the deprecations config

Add to the appropriate config file (`config/drupal-11/drupal-11.{{X}}-deprecations.php`):

```php
use DrupalRector\Drupal11\Rector\Deprecation\{{ClassName}};
// (add to the use block at the top)

// https://www.drupal.org/node/{{issueNumber}}
// {{functionName}}() deprecated in drupal:{{introducedVersion}}, removed in drupal:{{removedVersion}}.
$rectorConfig->ruleWithConfiguration({{ClassName}}::class, [
    new DrupalIntroducedVersionConfiguration('{{introducedVersion}}'),
]);
```

Then register it again in `config/composer-based.php`, under the `// Drupal 11.{{X}}` heading in
the same position, with the same comment block and the bound from Step 2:

```php
use DrupalRector\Drupal11\Rector\Deprecation\{{ClassName}};
// (add to the use block at the top)

// https://www.drupal.org/node/{{issueNumber}}
// {{functionName}}() deprecated in drupal:{{introducedVersion}}, removed in drupal:{{removedVersion}}.
$rectorConfig->ruleWithConfigurationComposerVersionBound({{ClassName}}::class, [
    new DrupalIntroducedVersionConfiguration('{{introducedVersion}}'),
], 'drupal/core', '>={{introducedVersion}} <{{majorAfterRemoval}}.0.0');
```

Skipping this fails PHPStan with `drupalRector.composerBasedSetCoverage`.

---

## Step 8 — Run quality checks

```bash
ddev composer fix-style
ddev composer phpstan
vendor/bin/phpunit tests/src/Drupal11/Rector/Deprecation/{{ClassName}}/
```

All three must pass before committing.

---

## Step 9 — Commit

```bash
git add src/Drupal11/Rector/Deprecation/{{ClassName}}.php \
        tests/src/Drupal11/Rector/Deprecation/{{ClassName}}/ \
        config/drupal-11/drupal-11.{{X}}-deprecations.php \
        config/composer-based.php
git commit -m "feat: add {{ClassName}} for #{{issueNumber}}"
```

Add a `CHANGELOG.md` entry under `[Unreleased] / ### Added` (a separate `docs:` commit is fine).
