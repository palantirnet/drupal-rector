<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated node_add_body_field() with $this->createBodyField() from BodyFieldCreationTrait.
 *
 * Deprecated in drupal:11.3.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3489266
 * @see https://www.drupal.org/node/3516778
 */
final class ReplaceNodeAddBodyFieldRector extends AbstractDrupalCoreRector
{
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

    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$this->isName($node, 'node_add_body_field')) {
            return null;
        }

        if (empty($node->args)) {
            return null;
        }

        $typeArg = $node->args[0] instanceof Arg ? $node->args[0]->value : null;
        if ($typeArg === null) {
            return null;
        }

        $idCall = new MethodCall($typeArg, 'id');

        $newArgs = [
            new Arg(new String_('node')),
            new Arg($idCall),
        ];

        if (isset($node->args[1]) && $node->args[1] instanceof Arg) {
            $newArgs[] = new Arg(new String_('body'));
            $newArgs[] = new Arg($node->args[1]->value);
        }

        return new MethodCall(
            new Variable('this'),
            'createBodyField',
            $newArgs
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated node_add_body_field() with $this->createBodyField() (drupal:11.3.0)', [
            new ConfiguredCodeSample(
                'node_add_body_field($nodeType, \'My Body\');',
                '$this->createBodyField(\'node\', $nodeType->id(), \'body\', \'My Body\');',
                [new DrupalIntroducedVersionConfiguration('11.3.0')]
            ),
        ]);
    }
}
