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

Leave the Rector version to Composer: drupal-rector 1.2 requires `rector/rector` `^2.8.4`, so the unversioned `rector/rector` resolves to a release it supports. The project's own install is used only when it is drupal-rector 1.2 or newer (the check for `config/composer-based.php`); 1.1.x has no `DrupalSetList`. A globally installed `rector` is not used, because it cannot load drupal-rector.

### 2. Write `.cache/agent-rector/rector.php`

Load the project autoloader so Rector resolves Drupal and project types, scope `withPaths()` to the target directories, and attach the sets or rules for your task (see [Configuration reference](#configuration-reference)):

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

$projectRoot = dirname(__DIR__, 2);
require_once $projectRoot . '/vendor/autoload.php';

return RectorConfig::configure()
    ->withPaths([$projectRoot . '/web/modules/custom']);
    // Attach set, configured rule, or custom rule here.
```

Keep the `require_once`. Rector loads `./vendor/autoload.php` itself only when the project has no `vendor/rector/rector`. On a project that ships its own Rector the isolated binary skips it, `Composer\InstalledVersions` sees the isolated vendor without `drupal/core`, and the run fails with `Package "drupal/core" is not installed`. `withAutoloadPaths()` does not fix that: it only feeds files to static reflection. `withBootstrapFiles()` replaces the bootstrap file the Drupal sets register, and rules then miss call sites such as `\Drupal::service('renderer')->…`. `dirname(__DIR__, 2)` assumes the default `.cache/agent-rector`; adjust it if `RECTOR_CACHE_DIR` is set elsewhere.

### 3. Dry-run and verify the diff

Preview the AST diff before touching working files. Run from the project root: Rector reads installed package versions from `./vendor/composer/installed.json` in the working directory, and run from `.cache/agent-rector` it sees no `drupal/core` and silently skips every version-bound Drupal rule.

```bash
$RECTOR_BIN process \
  --config=.cache/agent-rector/rector.php \
  --dry-run \
  --memory-limit=1G \
  --no-progress-bar
```

Refine `.cache/agent-rector/rector.php` (or your custom rule) until the dry-run reports no errors and the diff transforms every target call site and nothing else. A dry-run exits `2` when it would change files, `1` on errors, and `0` when there is nothing to change.

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
- `.cache/` is not staged or committed (add it to `.gitignore` if the project does not ignore it).
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

The project root must be a Drupal site with `drupal/core` in `vendor/`; a standalone module checkout has to sit inside one.

For a site, fix what is deprecated on the installed `drupal/core`. Every rule is bound to the core version its deprecation landed in, backward-compatibility wrapping is off, and the set adds Drupal's file extensions and phpstan-drupal by itself:

```php
use DrupalRector\Set\DrupalSetList;

    ->withSets([DrupalSetList::COMPOSER_BASED])
```

For a contrib module or theme that must keep working on older cores, list the major sets. Rewrites are wrapped in `DeprecationHelper::backwardsCompatibleCall()`. These sets add neither Drupal's file extensions nor phpstan-drupal, and without phpstan-drupal calls on `\Drupal::service()` results are missed. Run against the newest core the module supports: rules are skipped on a core older than their deprecation.

```php
use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal11SetList;

    ->withSets([
        Drupal10SetList::DRUPAL_10,
        Drupal11SetList::DRUPAL_11,
    ])
    ->withFileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine'])
    ->withPHPStanConfigs(array_filter([$projectRoot . '/vendor/mglaman/phpstan-drupal/extension.neon'], 'file_exists'))
```

BC wrapping assumes the lowest supported core is `10.1.0`. To change it, `DrupalRectorSettings` has to be registered with `$rectorConfig->singleton()`, which the `configure()` builder does not offer: write the scratch config in the closure form (`return static function (RectorConfig $rectorConfig): void { ... };`) as drupal-rector's `rector.contrib.php` does, and call `setMinimumCoreVersionSupported()` there. This has no effect with the composer-based set, which registers its own settings.

`withComposerBased(drupal: true)` loads the same composer-based set, but adds nothing instead of failing when drupal-rector is missing or older than 1.2; prefer `withSets()`. Do not use a bare `withComposerBased()` on a Drupal project: it also loads Rector's Symfony, PHPUnit and Twig sets, which follow the installed versions of those packages, not Drupal's. `withSetProviders(DrupalSetProvider::class)` is ignored since Rector 2.6.5.

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

`refactor()` must be **idempotent**: return `null` when a node does not match or is already in its target form. Rector re-runs all rules on a changed file until it stops changing, so a rule that matches its own output loops forever.

Return the node when you changed it. Mutating the node and returning `null` drops the change. Return only `null`, a node, a non-empty array of nodes, or `PhpParser\NodeVisitor::REMOVE_NODE`; `[]` or any other int throws.

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
