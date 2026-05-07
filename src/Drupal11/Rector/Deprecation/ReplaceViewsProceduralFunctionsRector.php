<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Views procedural functions with OO equivalents.
 *
 * Deprecated in drupal:11.4.0, removed in drupal:13.0.0.
 *
 * - views_view_is_enabled($view)  => $view->status()
 * - views_view_is_disabled($view) => !$view->status()
 * - views_enable_view($view)      => $view->enable()->save()
 * - views_disable_view($view)     => $view->disable()->save()
 * - views_get_view_result(...)    => \Drupal\views\Views::getViewResult(...)
 *
 * @see https://www.drupal.org/node/3572243
 */
final class ReplaceViewsProceduralFunctionsRector extends AbstractDrupalCoreRector
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
        return [Node\Expr\FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        if (!$node->name instanceof Node\Name) {
            return null;
        }

        return match ($node->name->toString()) {
            'views_view_is_enabled' => $this->statusCall($node),
            'views_view_is_disabled' => $this->negatedStatusCall($node),
            'views_enable_view' => $this->enableSaveCall($node),
            'views_disable_view' => $this->disableSaveCall($node),
            'views_get_view_result' => $this->staticGetViewResult($node),
            default => null,
        };
    }

    private function statusCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }

        return new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('status'));
    }

    private function negatedStatusCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }

        return new Node\Expr\BooleanNot(
            new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('status'))
        );
    }

    private function enableSaveCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }
        $enable = new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('enable'));

        return new Node\Expr\MethodCall($enable, new Node\Identifier('save'));
    }

    private function disableSaveCall(Node\Expr\FuncCall $node): ?Node
    {
        if (count($node->args) < 1) {
            return null;
        }
        $disable = new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('disable'));

        return new Node\Expr\MethodCall($disable, new Node\Identifier('save'));
    }

    private function staticGetViewResult(Node\Expr\FuncCall $node): Node\Expr\StaticCall
    {
        return new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal\views\Views'),
            new Node\Identifier('getViewResult'),
            $node->args
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated Views procedural functions with OO equivalents (drupal:11.4.0)', [
            new ConfiguredCodeSample(
                'views_enable_view($view);',
                '$view->enable()->save();',
                [new DrupalIntroducedVersionConfiguration('11.4.0')]
            ),
        ]);
    }
}
