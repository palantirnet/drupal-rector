<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated string $values argument in BlockContentTestBase::createBlockContentType() with an array.
 *
 * Deprecated in drupal:11.1.0 and removed in drupal:12.0.0. Callers must now
 * pass an explicit array such as ['id' => 'basic'] instead of a plain string.
 *
 * @see https://www.drupal.org/node/3196937
 * @see https://www.drupal.org/node/3473739
 */
class BlockContentTestBaseStringToArrayRector extends AbstractDrupalCoreRector
{
    /** @var DrupalIntroducedVersionConfiguration[] */
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
            "Replace deprecated string \$values in BlockContentTestBase::createBlockContentType() with an ['id' => ...] array",
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
$this->createBlockContentType('basic', TRUE);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$this->createBlockContentType(['id' => 'basic'], TRUE);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.1.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if (!$node instanceof MethodCall) {
            return null;
        }

        if (!$this->isName($node->name, 'createBlockContentType')) {
            return null;
        }

        if (count($node->args) === 0) {
            return null;
        }

        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }

        if (!$firstArg->value instanceof String_) {
            return null;
        }

        // Skip InlineBlockTestBase::createBlockContentType($id, $label) — two string args.
        if (isset($node->args[1])) {
            $secondArg = $node->args[1];
            if ($secondArg instanceof Arg && $secondArg->value instanceof String_) {
                return null;
            }
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Tests\block_content\Traits\BlockContentCreationTrait'))) {
            return null;
        }

        // Clone to avoid mutating the original node — required for correct BC wrapping.
        $clonedNode = clone $node;
        $clonedArg = clone $firstArg;
        $clonedArg->value = new Array_([new ArrayItem($firstArg->value, new String_('id'))]);
        $clonedNode->args[0] = $clonedArg;

        return $clonedNode;
    }
}
