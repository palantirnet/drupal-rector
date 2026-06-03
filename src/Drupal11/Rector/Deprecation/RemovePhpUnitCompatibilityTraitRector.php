<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes `use PhpUnitCompatibilityTrait;` from test class declarations.
 *
 * Deliberately gated to **Drupal 12 only**. The trait was a forward-
 * compatibility shim for PHPUnit API differences across versions and is
 * deleted from Drupal core in issue #3582118 — any test class still
 * referencing it fatal-errors at autoload time on Drupal 12+ because the
 * trait class no longer exists.
 *
 * Why the gate matters:
 * - On Drupal 10 the trait may still provide shim methods that the test
 *   actively depends on (e.g. PHPUnit 9/10 signature bridges). Removing
 *   the composition there can silently break tests.
 * - On Drupal 11 the trait is already an empty no-op, so the composition
 *   is harmless but also pointless.
 * - On Drupal 12 the trait class is gone entirely; the composition fatals.
 *
 * Because of those semantics the rector is **off by default** (the stub
 * Drupal version is 11.99.x-dev). It only fires when the consumer
 * explicitly sets the target Drupal version to 12.0.0 or higher via
 * `DrupalRectorSettings::setDrupalVersion('12.0.0')`. Running it earlier
 * would risk breaking Drupal 10 / 11 tests with no upside.
 *
 * A trait composition cannot be BC-wrapped — `use Trait;` inside a class
 * body is structural — so the transformation is deliberately one-way:
 * users opt into the D12-targeted output. The leftover top-of-file
 * `use Drupal\Tests\PhpUnitCompatibilityTrait;` import is harmless on
 * D12 (PHP never resolves an unused alias); cleanup is optional.
 *
 * @see https://www.drupal.org/node/3582118
 */
class RemovePhpUnitCompatibilityTraitRector extends AbstractDrupalCoreRector
{
    private const TRAIT_FQCN = 'Drupal\\Tests\\PhpUnitCompatibilityTrait';

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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove use Drupal\Tests\PhpUnitCompatibilityTrait; from test classes. Gated to Drupal 12 only — the trait class is deleted from core in #3582118 and the rule must not fire on Drupal 10/11 where the trait still exists.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
class ExampleTest extends \PHPUnit\Framework\TestCase
{
    use \Drupal\Tests\PhpUnitCompatibilityTrait;
}
CODE_BEFORE,
                    <<<'CODE_AFTER'
class ExampleTest extends \PHPUnit\Framework\TestCase
{
}
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('12.0.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class, Trait_::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if (!$node instanceof Class_ && !$node instanceof Trait_) {
            return null;
        }

        $hasChanged = false;
        foreach ($node->stmts as $key => $stmt) {
            if (!$stmt instanceof TraitUse) {
                continue;
            }
            foreach ($stmt->traits as $traitKey => $trait) {
                if (!$this->isName($trait, self::TRAIT_FQCN)) {
                    continue;
                }
                unset($stmt->traits[$traitKey]);
                $hasChanged = true;
            }
            if ($stmt->traits === []) {
                unset($node->stmts[$key]);
            }
        }

        return $hasChanged ? $node : null;
    }

    /**
     * Trait composition is a structural change, not an Expr → Expr rewrite,
     * so it cannot be BC-wrapped via DeprecationHelper. Disable BC entirely.
     */
    public function supportBackwardsCompatibility(VersionedConfigurationInterface $configuration): bool
    {
        return false;
    }
}
