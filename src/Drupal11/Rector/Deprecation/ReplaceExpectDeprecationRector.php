<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces removed Drupal/PHPUnit expectDeprecation*() methods with their PHPUnit 11+ replacements.
 *
 * Drupal's ExpectDeprecationTrait is deprecated in drupal:11.4.0 and removed in
 * drupal:12.0.0. PHPUnit's expectDeprecation() / expectDeprecationMessage() /
 * expectDeprecationMessageMatches() are removed in PHPUnit 11. Migration:
 *
 * - $this->expectDeprecation()         (PHPUnit no-arg form) → removed
 * - $this->expectDeprecation($msg)     (Drupal trait form)   → $this->expectUserDeprecationMessage($msg)
 * - $this->expectDeprecationMessage($msg)        → $this->expectUserDeprecationMessage($msg)
 * - $this->expectDeprecationMessageMatches($p)   → $this->expectUserDeprecationMessageMatches($p)
 *
 * Renames are wrapped in DeprecationHelper::backwardsCompatibleCall() so tests
 * keep passing on both pre-11.4 (where the old methods still exist) and
 * 11.4+ (where the new PHPUnit 11+ replacements must be used).
 *
 * Note: the Drupal trait method internally treats $message as a regex fragment
 * with %A boundaries. Renaming to expectUserDeprecationMessage() switches to
 * exact-match semantics, which matches how Drupal core itself migrated its
 * tests and the typical contrib pattern (literal deprecation message). Tests
 * that intentionally relied on partial matching should be reviewed and
 * switched to expectUserDeprecationMessageMatches() manually.
 *
 * @see https://www.drupal.org/node/3550268
 * @see https://www.drupal.org/node/3545276
 */
final class ReplaceExpectDeprecationRector extends AbstractDrupalCoreRector
{
    /**
     * Verbatim PHPStan deprecation messages this rector covers.
     *
     * Stored in upgrade_status's normalized form (whitespace collapsed,
     * ": in" → ". Deprecated in", leading "\Drupal" stripped) so they can be
     * compared with DeprecationAnalyzer::isRectorCovered() via exact match.
     *
     * Capture method: synthetic probe extending KernelTestBase against
     * Drupal 11.4-dev, then `scripts/normalize-phpstan-message.php`.
     */
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated method expectDeprecation() of class Drupal\KernelTests\KernelTestBase. Deprecated in drupal:11.4.0 and is removed from drupal:12.0.0. Use $this->expectUserDeprecationMessage() or $this->expectUserDeprecationMessageMatches() instead.',
    ];

    private const RENAME_MAP = [
        'expectDeprecation' => 'expectUserDeprecationMessage',
        'expectDeprecationMessage' => 'expectUserDeprecationMessage',
        'expectDeprecationMessageMatches' => 'expectUserDeprecationMessageMatches',
    ];

    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        // Expression targets the 0-arg `expectDeprecation()` REMOVE case;
        // MethodCall handles the renames so the parent class auto-wraps
        // them in DeprecationHelper::backwardsCompatibleCall().
        return [Expression::class, MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): Node|int|null
    {
        if ($node instanceof Expression) {
            return $this->refactorBareCall($node);
        }

        if ($node instanceof MethodCall) {
            return $this->refactorRename($node);
        }

        return null;
    }

    private function refactorBareCall(Expression $node): ?int
    {
        if (!$node->expr instanceof MethodCall) {
            return null;
        }

        $call = $node->expr;
        if (!$this->isThisCall($call)) {
            return null;
        }

        if ($this->getName($call->name) !== 'expectDeprecation') {
            return null;
        }

        if ($call->args !== []) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    private function refactorRename(MethodCall $node): ?MethodCall
    {
        if (!$this->isThisCall($node)) {
            return null;
        }

        $name = $this->getName($node->name);
        if ($name === null || !isset(self::RENAME_MAP[$name])) {
            return null;
        }

        // 0-arg `expectDeprecation()` is handled by the Expression path (REMOVE);
        // skip it here so we don't emit a malformed `expectUserDeprecationMessage()`.
        if ($name === 'expectDeprecation' && $node->args === []) {
            return null;
        }

        return new MethodCall($node->var, new Identifier(self::RENAME_MAP[$name]), $node->args);
    }

    private function isThisCall(MethodCall $node): bool
    {
        return $node->var instanceof Variable && $node->var->name === 'this';
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace removed expectDeprecation*() test methods with PHPUnit 11+ expectUserDeprecationMessage*() equivalents.',
            [
                new ConfiguredCodeSample(
                    <<<'BEFORE'
$this->expectDeprecation();
$this->expectDeprecationMessage('Foo is deprecated');
BEFORE,
                    <<<'AFTER'
$this->expectUserDeprecationMessage('Foo is deprecated');
AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
