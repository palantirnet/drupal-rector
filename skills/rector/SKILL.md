---
name: rector
description: PHP AST refactoring and codemods. Use for PHP version or Drupal deprecation upgrades, annotation-to-attribute migrations, type-declaration or dead-code sets, cross-file symbol/signature renames, and multi-file structural PHP changes.
---

Run Rector from an isolated directory (`.cache/agent-rector`) so root `composer.json` and `composer.lock` stay untouched.

## Steps

### 1. Provision Rector

Resolve an existing binary or provision an isolated installation in `.cache/agent-rector`:

```bash
RECTOR_DIR="${RECTOR_CACHE_DIR:-.cache/agent-rector}"
mkdir -p "$RECTOR_DIR"

if [ -x "vendor/bin/rector" ]; then
  RECTOR_BIN="vendor/bin/rector"
elif [ -x "$RECTOR_DIR/vendor/bin/rector" ]; then
  RECTOR_BIN="$RECTOR_DIR/vendor/bin/rector"
elif command -v rector >/dev/null 2>&1; then
  RECTOR_BIN="$(command -v rector)"
else
  composer --working-dir="$RECTOR_DIR" init --no-interaction --name="agent/rector-env" >/dev/null 2>&1
  composer --working-dir="$RECTOR_DIR" require --no-interaction --prefer-dist --no-progress rector/rector palantirnet/drupal-rector:^1.1.3
  RECTOR_BIN="$RECTOR_DIR/vendor/bin/rector"
fi
```

Done when `$RECTOR_BIN --version` exits `0`.

### 2. Write `.cache/agent-rector/rector.php`

Point Rector at the project autoloader so it resolves Drupal and project types, scope `withPaths()` to the target directories, and attach the sets or rules for your task (see [Configuration reference](#configuration-reference)):

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

$projectRoot = dirname(__DIR__, 2);

return RectorConfig::configure()
    ->withAutoloadPaths([$projectRoot . '/vendor/autoload.php'])
    ->withPaths([$projectRoot . '/web/modules/custom']);
    // Attach set, configured rule, or custom rule here.
```

### 3. Dry-run and verify the diff

Preview the AST diff before touching working files:

```bash
$RECTOR_BIN process \
  --config=.cache/agent-rector/rector.php \
  --dry-run \
  --memory-limit=1G \
  --no-progress-bar
```

Refine `.cache/agent-rector/rector.php` (or your custom rule) until the dry-run exits `0` and the diff transforms every target call site and nothing else.

### 4. Apply, clean up, and verify

Apply the transformation and remove the scratch config files (keeping `.cache/agent-rector/vendor` cached for future runs):

```bash
$RECTOR_BIN process \
  --config=.cache/agent-rector/rector.php \
  --memory-limit=1G \
  --no-progress-bar

rm -f .cache/agent-rector/*.php
```

Rector's AST printer reformats modified nodes without project style rules. Format the modified files using the project's code beautifier, then run the project's automated QA checks (linting, static analysis, and covering tests) as defined by the runtime environment.

Done when:
- `.cache/agent-rector/*.php` scratch files are removed and `composer.json` / `composer.lock` are untouched.
- All automated QA checks pass on the modified paths with `0` errors.

## Configuration reference

Chain the branch-specific builder methods onto `RectorConfig::configure()` in `.cache/agent-rector/rector.php`.

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

```php
use DrupalRector\Set\DrupalSetList;
use DrupalRector\Set\DrupalSetProvider;

    ->withSetProviders(DrupalSetProvider::class)
    ->withComposerBased(drupal: true)
    ->withSets([
        DrupalSetList::DRUPAL_10,
    ])
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

When no built-in rule matches the structural pattern, write `.cache/agent-rector/CustomRule.php` and load it in `rector.php` with `require_once __DIR__ . '/CustomRule.php'` and `->withRules([\AgentRector\CustomRule::class])`.

`refactor()` must be **idempotent**: return `null` when a node does not match or is already in its target form, otherwise Rector re-transforms the node in an infinite loop.

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
