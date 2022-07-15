<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Rector\PostRector\Collector\NodesToAddCollector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class UiHelperTraitDrupalPostFormRector extends AbstractRector
{

    /**
     * @readonly
     * @var \Rector\PostRector\Collector\NodesToAddCollector
     */
    private $nodesToAddCollector;

    public function __construct(NodesToAddCollector $nodesToAddCollector)
    {
        $this->nodesToAddCollector = $nodesToAddCollector;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated UiHelperTrait::drupalPostForm() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$edit = [];
$edit['action'] = 'action_goto_action';
$this->drupalPostForm('admin/config/system/actions', $edit, 'Create');
$edit['action'] = 'action_goto_action_1';
$this->drupalPostForm(null, $edit, 'Edit');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$edit = [];
$edit['action'] = 'action_goto_action';
$this->drupalGet('admin/config/system/actions');
$this->submitForm($edit, 'Create');
$edit['action'] = 'action_goto_action_1';
$this->submitForm($edit, 'Edit');
CODE_AFTER
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @param \PhpParser\Node\Expr\MethodCall $node
     *
     * @return array<int, ?\PhpParser\Node\Arg>
     * @throws \Rector\Core\Exception\ShouldNotHappenException
     */
    private function safeArgDestructure(Node\Expr\MethodCall $node): array
    {
        $count = count($node->args);
        if ($count === 3) {
            [$path, $edit, $button] = $node->args;
            return [$path, $edit, $button, null, null];
        }

        if ($count === 4) {
            [$path, $edit, $button, $options] = $node->args;
            return [$path, $edit, $button, $options, null];
        }

        if ($count === 5) {
            return $node->args;
        }

        throw new ShouldNotHappenException('Unexpected argument count for drupalPostForm');
    }

    public function refactor(Node $node)
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) === 'drupalPostForm') {
            [$path, $edit, $button, $options, $htmlId] = $this->safeArgDestructure($node);

            if ($htmlId === null) {
                $submitFormNode = $this->nodeFactory->createLocalMethodCall('submitForm', [$edit, $button]);
            } else {
                $submitFormNode = $this->nodeFactory->createLocalMethodCall('submitForm', [$edit, $button, $htmlId]);
            }

            $pathValue = $path->value;
            if (!$pathValue instanceof Node\Expr\ConstFetch || strtolower((string) $pathValue->name) !== 'null') {
                if ($options === null) {
                    $drupalGetNode = $this->nodeFactory->createLocalMethodCall('drupalGet', [$path]);
                } else {
                    $drupalGetNode = $this->nodeFactory->createLocalMethodCall('drupalGet', [$path, $options]);
                }
                // We have to use the deprecated `addNodeBeforeNode` due to
                // https://github.com/rectorphp/rector/discussions/6538.
                // @phpstan-ignore-next-line
                $this->nodesToAddCollector->addNodeBeforeNode($drupalGetNode, $node);
            }

            return $submitFormNode;
        }
        return null;
    }
}
