<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated $_SESSION['key'] = $value writes with \Drupal::request()->getSession()->set().
 *
 * Deprecated in drupal:11.2.0.
 *
 * @see https://www.drupal.org/node/3518527
 * @see https://www.drupal.org/node/3518914
 */
final class ReplaceSessionWritesWithRequestSessionRector extends AbstractDrupalCoreRector
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
        return [Assign::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Assign);

        if (!$node->var instanceof ArrayDimFetch) {
            return null;
        }

        $arrayDimFetch = $node->var;

        if (!$arrayDimFetch->var instanceof Variable) {
            return null;
        }

        if ($this->getName($arrayDimFetch->var) !== '_SESSION') {
            return null;
        }

        if ($arrayDimFetch->dim === null) {
            return null;
        }

        $drupalRequest = new StaticCall(
            new FullyQualified('Drupal'),
            'request',
            []
        );

        $getSession = new MethodCall($drupalRequest, 'getSession', []);

        return new MethodCall(
            $getSession,
            'set',
            [
                new Arg($arrayDimFetch->dim),
                new Arg($node->expr),
            ]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated $_SESSION writes with \\Drupal::request()->getSession()->set() (drupal:11.2.0)', [
            new ConfiguredCodeSample(
                '$_SESSION[\'my_key\'] = $value;',
                '\\Drupal::request()->getSession()->set(\'my_key\', $value);',
                [new DrupalIntroducedVersionConfiguration('11.2.0')]
            ),
        ]);
    }
}
