<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\Base;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use PHPStan\Type\Constant\ConstantStringType;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;

abstract class ExtensionPathBase extends AbstractRector implements ConfigurableRectorInterface
{
    use AddCommentTrait;

    protected $functionName;

    protected $methodName;

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);
        if ($this->getName($node->name) !== $this->functionName) {
            return null;
        }
        $args = $node->getArgs();
        if (count($args) !== 2) {
            $this->addDrupalRectorComment($node, "Invalid call to {$this->functionName}, cannot process.");
            return null;
        }
        [$extensionType, $extensionName] = $args;

        $extensionTypeValueType = $this->nodeTypeResolver->getType($extensionType->value);
        if ($extensionTypeValueType instanceof ConstantStringType) {
            $extensionType = $extensionTypeValueType->getValue();
        }

        if (in_array($extensionType, ['module', 'theme', 'profile', 'theme_engine'], true)) {
            $serviceName = "extension.list.$extensionType";
            $methodArgs = [$extensionName];
        } else {
            $this->addDrupalRectorComment(
                $node,
                'Unsupported extension type encountered, using extension.path.resolver instead of extension.list'
            );
            $serviceName = 'extension.path.resolver';
            $methodArgs = $args;
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_($serviceName))]
        );
        $methodName = new Node\Identifier($this->methodName);

        return new Node\Expr\MethodCall($service, $methodName, $methodArgs);
    }
}
