<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated editor_load($format_id) with entityTypeManager()->getStorage('editor')->load().
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3447794
 */
final class ReplaceEditorLoadRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof FuncCall);

        if (!$this->isName($node, 'editor_load')) {
            return null;
        }

        $entityTypeManager = $this->nodeFactory->createStaticCall('Drupal', 'entityTypeManager');
        $getStorage = $this->nodeFactory->createMethodCall($entityTypeManager, 'getStorage', [new String_('editor')]);

        return new MethodCall($getStorage, 'load', $node->args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated editor_load($format_id) with entityTypeManager()->getStorage(\'editor\')->load() (drupal:11.2.0)', [
            new CodeSample(
                '$editor = editor_load($format_id);',
                "$editor = \\Drupal::entityTypeManager()->getStorage('editor')->load(\$format_id);"
            ),
        ]);
    }
}
