---
name: rector
description: AST refactoring and codemods for PHP. Use for Drupal or PHP upgrades, type-declarations, dead-code removal, symbol renames, or custom AST rules.
---

Run Rector from an isolated directory (`.cache/agent-rector`) so project `composer.json` and `composer.lock` stay untouched.

## Steps

### 1. Provision Rector

Locate the project root dynamically and resolve an existing binary (drupal-rector >= 1.2) or provision an isolated installation:

```bash
# Locate the nearest Composer project root containing vendor/autoload.php
PROJECT_ROOT="$PWD"
while [ "$PROJECT_ROOT" != "/" ] && [ "$PROJECT_ROOT" != "." ]; do
  if [ -f "$PROJECT_ROOT/composer.json" ] && [ -f "$PROJECT_ROOT/vendor/autoload.php" ]; then
    break
  fi
  PROJECT_ROOT="$(dirname "$PROJECT_ROOT")"
done

# Fallback: nearest composer.json (if vendor is not yet installed) or git root
if [ ! -f "$PROJECT_ROOT/composer.json" ]; then
  if git rev-parse --show-toplevel >/dev/null 2>&1; then
    PROJECT_ROOT="$(git rev-parse --show-toplevel)"
  else
    PROJECT_ROOT="$PWD"
  fi
fi

# Always execute from the project root so Rector resolves ./vendor/composer/installed.json
cd "$PROJECT_ROOT"

RECTOR_DIR="${RECTOR_CACHE_DIR:-$PROJECT_ROOT/.cache/agent-rector}"
mkdir -p "$RECTOR_DIR"

if [ -x "vendor/bin/rector" ] && [ -f "vendor/palantirnet/drupal-rector/config/composer-based.php" ]; then
  RECTOR_BIN="vendor/bin/rector"
elif [ -x "$RECTOR_DIR/vendor/bin/rector" ]; then
  RECTOR_BIN="$RECTOR_DIR/vendor/bin/rector"
else
  composer --working-dir="$RECTOR_DIR" init --no-interaction --name="agent/rector-env" >/dev/null 2>&1
  composer --working-dir="$RECTOR_DIR" require --no-interaction --prefer-dist --no-progress rector/rector palantirnet/drupal-rector:^1.2
  RECTOR_BIN="$RECTOR_DIR/vendor/bin/rector"
fi
```

Done when `$RECTOR_BIN --version` exits `0`.

### 2. Write `$RECTOR_DIR/rector.php`

Configure `$RECTOR_DIR/rector.php` with the project autoloader, target paths, and rules (see [Configuration reference](#configuration-reference)):

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

$projectRoot = getcwd();
require_once $projectRoot . '/vendor/autoload.php';

return RectorConfig::configure()
    ->withPaths([
        $projectRoot . '/web/modules/custom',
    ]);
    // Attach sets, configured rules, or custom rules here.
```

Done when `php -l "$RECTOR_DIR/rector.php"` exits `0` and target paths and rules are configured.

### 3. Dry-run and verify the diff

Run from `$PROJECT_ROOT` so Rector resolves `./vendor/composer/installed.json`:

```bash
$RECTOR_BIN process \
  --config="$RECTOR_DIR/rector.php" \
  --dry-run \
  --memory-limit=1G \
  --no-progress-bar
```

Rector exit codes: `2` = diff generated, `0` = no changes needed, `1` = error.

Done when dry-run exits `2` (or `0` if targets already comply) with `0` errors and the diff transforms only the intended call sites.

### 4. Apply, clean up, and verify

```bash
$RECTOR_BIN process \
  --config="$RECTOR_DIR/rector.php" \
  --memory-limit=1G \
  --no-progress-bar

rm -f "$RECTOR_DIR"/*.php
```

Done when `$RECTOR_DIR/*.php` scratch files are removed, `composer.json` and `composer.lock` are untouched, and all automated QA checks (linting, static analysis, covering tests) pass on modified files with `0` errors.

---

## Configuration reference

### PHP version & prepared sets (types, dead code, code quality)

```php
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
    )
```

### Drupal deprecations & upgrade sets

#### Drupal Sites (Custom Code)
For custom modules and themes, fix deprecations for the installed Drupal core:

```php
use DrupalRector\Set\DrupalSetList;

    ->withSets([DrupalSetList::COMPOSER_BASED])
```

#### Contrib Modules (Multi-Version BC Wrapping)
For code that must maintain backward-compatibility with older cores using `DeprecationHelper::backwardsCompatibleCall()`, use the closure format to register `DrupalRectorSettings` with your module's minimum supported core version:

```php
use DrupalRector\Services\DrupalRectorSettings;
use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal11SetList;
use Rector\Config\RectorConfig;

$projectRoot = getcwd();
require_once $projectRoot . '/vendor/autoload.php';

return static function (RectorConfig $rectorConfig) use ($projectRoot): void {
    $rectorConfig->paths([
        $projectRoot . '/web/modules/custom',
    ]);
    $rectorConfig->sets([
        Drupal10SetList::DRUPAL_10,
        Drupal11SetList::DRUPAL_11,
    ]);
    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);

    // Set the lowest core version supported (matches core_version_requirement in .info.yml).
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () => (new DrupalRectorSettings())
        ->enableBackwardCompatibility()
        ->setMinimumCoreVersionSupported('10.3.0')
    );

    $phpstanDrupal = $projectRoot . '/vendor/mglaman/phpstan-drupal/extension.neon';
    if (file_exists($phpstanDrupal)) {
        $rectorConfig->phpstanConfigs([$phpstanDrupal]);
    }
};
```

### Symbol & signature renames

```php
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Renaming\ValueObject\MethodCallRename;

    ->withConfiguredRule(RenameClassRector::class, [
        'App\\OldNamespace\\OldClassName' => 'App\\NewNamespace\\NewClassName',
    ])
    ->withConfiguredRule(RenameMethodRector::class, [
        new MethodCallRename('App\\SomeService', 'oldMethod', 'newMethod'),
    ])
```

### Custom `AbstractRector` rule

When no built-in rule matches the structural pattern, write `$RECTOR_DIR/CustomRule.php` and load it via `require_once __DIR__ . '/CustomRule.php'` and `->withRules([\AgentRector\CustomRule::class])`.

`refactor()` must be **idempotent**: return the modified node, an array of replacement nodes, or `PhpParser\NodeVisitor::REMOVE_NODE` when transforming; return `null` when the node already matches the target state.

```php
<?php

declare(strict_types=1);

namespace AgentRector;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class CustomRule extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Transform custom method call pattern', []);
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'deprecatedCall')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\\my_module\\SomeService'))) {
            return null;
        }

        $node->name = new Identifier('replacementCall');
        return $node;
    }
}
```
