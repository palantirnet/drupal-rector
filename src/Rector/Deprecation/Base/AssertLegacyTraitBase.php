<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;

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
            Node\Expr\MethodCall::class,
        ];
    }

    protected function createAssertSessionMethodCall(string $method, array $args): Node\Expr\MethodCall
    {
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');
        return $this->nodeFactory->createMethodCall($assertSessionNode, $method, $args);
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== $this->deprecatedMethodName) {
            return null;
        }
        if ($this->getDeclaringSource($node) !== $this->declaringSource) {
            return null;
        }

        if ($this->comment !== '') {
            $this->addDrupalRectorComment($node, $this->comment);
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

