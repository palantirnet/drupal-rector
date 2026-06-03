<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces DRUPAL_DISABLED/OPTIONAL/REQUIRED with CommentPreviewMode enum cases in CommentTestBase::setCommentPreview() calls.
 *
 * Passing an integer (the deprecated global constants are int-valued) to
 * Drupal\Tests\comment\Functional\CommentTestBase::setCommentPreview() was deprecated in drupal:11.3.0
 * and will be removed in drupal:13.0.0. The new CommentPreviewMode enum class only exists on
 * Drupal >= 11.3.0, so the replacement is wrapped in DeprecationHelper::backwardsCompatibleCall().
 *
 * @see https://www.drupal.org/node/3538660
 * @see https://www.drupal.org/node/3538678
 */
final class ReplaceCommentPreviewConstantsRector extends AbstractDrupalCoreRector
{
    // TODO PHPSTAN_MESSAGES ReplaceCommentPreviewConstantsRector: PHPStan emits
    //   no deprecation for the targeted call. The deprecation is triggered at
    //   runtime via @trigger_error inside CommentTestBase::setCommentPreview()
    //   when the $mode parameter is an int, but the method itself is not
    //   @deprecated. The DRUPAL_DISABLED/OPTIONAL/REQUIRED constants in
    //   system.module carry @deprecated docblocks but phpstan-deprecation-rules
    //   does not flag file-scope const usage. No string is available to add to
    //   the coverage registry; upgrade_status will need a different mechanism
    //   (deprecation message text rather than PHPStan) to detect this case.

    /**
     * @var array<string, string>
     */
    private const CONSTANT_TO_ENUM_CASE = [
        'DRUPAL_DISABLED' => 'Disabled',
        'DRUPAL_OPTIONAL' => 'Optional',
        'DRUPAL_REQUIRED' => 'Required',
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
        return [MethodCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if (!$node instanceof MethodCall) {
            return null;
        }

        if (!$this->isName($node->name, 'setCommentPreview')) {
            return null;
        }

        if (!isset($node->args[0]) || !$node->args[0] instanceof Arg) {
            return null;
        }

        $argValue = $node->args[0]->value;
        if (!$argValue instanceof ConstFetch) {
            return null;
        }

        $constName = $this->getName($argValue);
        if ($constName === null || !isset(self::CONSTANT_TO_ENUM_CASE[$constName])) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Tests\comment\Functional\CommentTestBase'))) {
            return null;
        }

        $enumCase = self::CONSTANT_TO_ENUM_CASE[$constName];

        $newArgs = $node->args;
        $newArgs[0] = new Arg(new ClassConstFetch(new FullyQualified('Drupal\comment\CommentPreviewMode'), $enumCase));

        return new MethodCall($node->var, $node->name, $newArgs);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace DRUPAL_DISABLED/OPTIONAL/REQUIRED with CommentPreviewMode enum in CommentTestBase::setCommentPreview() calls.', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->setCommentPreview(DRUPAL_DISABLED);
CODE_BEFORE,
                <<<'CODE_AFTER'
$this->setCommentPreview(\Drupal\comment\CommentPreviewMode::Disabled);
CODE_AFTER,
                [new DrupalIntroducedVersionConfiguration('11.3.0')]
            ),
        ]);
    }
}
