<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;

abstract class AssertLegacyTraitBase extends AbstractRector implements ConfigurableRectorInterface
{

    use AddCommentTrait;
    use GetDeclaringSourceTrait;

    protected $comment = '';
    protected $deprecatedMethodName;
    protected $methodName;
    protected $isAssertSessionMethod = true;
    protected $declaringSource = 'Drupal\FunctionalTests\AssertLegacyTrait';

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
        ];
    }

    protected function createAssertSessionMethodCall(string $method, array $args): Node\Expr\MethodCall
    {
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');
        return $this->nodeFactory->createMethodCall($assertSessionNode, $method, $args);
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Expression);
        if (!$node->expr instanceof Node\Expr\MethodCall) {
            return null;
        }
        $methodCall = $this->doRefactor($node->expr, $node);
        if (!$methodCall instanceof Node\Expr\MethodCall) {
            return null;
        }
        $newExpr = new Node\Stmt\Expression($methodCall);
        $comments = $node->getComments();
        $newExpr->setAttribute(AttributeKey::COMMENTS, $comments);
        return $newExpr;
    }

    protected function doRefactor(Node\Expr\MethodCall $node, Node\Stmt\Expression $parentExpr): ?Node
    {
        if ($this->getName($node->name) !== $this->deprecatedMethodName) {
            return null;
        }
        if ($this->getDeclaringSource($node) !== $this->declaringSource) {
            return null;
        }

        if ($this->comment !== '') {
            $this->addDrupalRectorComment($parentExpr, $this->comment);
        }

        $args = $this->processArgs($node->args);
        if ($this->isAssertSessionMethod) {
            return $this->createAssertSessionMethodCall($this->methodName, $args);
        }
        return $this->nodeFactory->createLocalMethodCall($this->methodName, $args);
    }

    protected function processArgs(array $args): array
    {
        return $args;
    }
}

