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

Create the scratch configuration in `$RECTOR_DIR/rector.php`. Load the project autoloader with `require_once`, scope `withPaths()` to the target files or directories, and attach the required rule sets (see [Configuration reference](#configuration-reference)):

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

Done when `php -l "$RECTOR_DIR/rector.php"` exits `0` and target paths and rules/sets for the task are configured.

### 3. Dry-run and verify the diff

Execute from the project root so package discovery resolves `./vendor/composer/installed.json`:

```bash
$RECTOR_BIN process \
  --config="$RECTOR_DIR/rector.php" \
  --dry-run \
  --memory-limit=1G \
  --no-progress-bar
```

Rector CLI status codes:
- `2`: Target patterns matched and diff generated (expected success state for pending refactors).
- `0`: No files matched or files already comply with target rules.
- `1`: Configuration or syntax error.

Done when dry-run exits `2` with an AST diff transforming only the intended call sites (or `0` when targets already comply) with `0` fatal errors.

### 4. Apply, clean up, and verify

Apply the transformation and remove scratch configuration files:

```bash
$RECTOR_BIN process \
  --config="$RECTOR_DIR/rector.php" \
  --memory-limit=1G \
  --no-progress-bar

rm -f "$RECTOR_DIR"/*.php
```

Format modified files using the project code beautifier, then run automated checks.

Done when:
- Scratch files (`$RECTOR_DIR/*.php`) are removed and root `composer.json` / `composer.lock` are untouched.
- Only intended file modifications are staged in git.
- All automated QA checks (linting, static analysis, covering tests) pass on modified files with `0` errors.

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
For code that must maintain backward-compatibility with older cores using `DeprecationHelper::backwardsCompatibleCall()`:

```php
use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal11SetList;

$projectRoot = getcwd();

return RectorConfig::configure()
    ->withSets([
        Drupal10SetList::DRUPAL_10,
        Drupal11SetList::DRUPAL_11,
    ])
    ->withFileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine'])
    ->withPHPStanConfigs(array_filter([$projectRoot . '/vendor/mglaman/phpstan-drupal/extension.neon'], 'file_exists'));
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

`refactor()` must be **idempotent**: return the modified node, an array of replacement nodes, or `PhpParser\NodeVisitor::REMOVE_NODE`. Return `null` when a node does not match or is already transformed.

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
