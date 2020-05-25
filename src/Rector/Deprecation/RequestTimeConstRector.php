<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;


use PhpParser\Node;

use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

class RequestTimeConstRector extends AbstractRector
{
    protected $deprecatedConstant = 'REQUEST_TIME';

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [Node\Expr\ConstFetch::class];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isName($node->name, $this->deprecatedConstant)) {
            return null;
        }

        $service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'time');
        $method_name = new Node\Identifier('getRequestTime');

        $node = new Node\Expr\MethodCall($service, $method_name);
        return $node;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated REQUEST_TIME calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$request_time = REQUEST_TIME;
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$request_time = \Drupal::time()->getRequestTime();
CODE_AFTER
            )
        ]);
    }

}
