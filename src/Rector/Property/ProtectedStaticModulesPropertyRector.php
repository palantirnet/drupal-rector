<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Property;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\Privatization\NodeManipulator\VisibilityManipulator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://www.drupal.org/node/2909426
 */
final class ProtectedStaticModulesPropertyRector extends AbstractRector
{
    private $visibilityManipulator;

    public function __construct(VisibilityManipulator $visibilityManipulator)
    {
        $this->visibilityManipulator = $visibilityManipulator;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('"public static $modules" will have its visibility changed to protected.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClassTest {
  public static $modules = [];
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
class SomeClassTest {
  protected static $modules = [];
}
CODE_SAMPLE

            )
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Property::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Property $node
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Property);
        if (!$this->isName($node, 'modules')) {
            return $node;
        }
        if ($node->isPublic()) {
            $this->visibilityManipulator->makeProtected($node);
        }

        return $node;
    }
}
