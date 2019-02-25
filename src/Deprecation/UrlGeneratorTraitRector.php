<?php

namespace Mxr576\Rector\Deprecation;

use PhpParser\Node;
use Rector\Exception\ShouldNotHappenException;
use Rector\NodeTypeResolver\Node\Attribute;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated UrlGeneratorTrait trait.
 *
 * TODO Import Drupal\Core\Url in a refactored class.
 *
 * @see https://github.com/rectorphp/rector/issues/1117
 */
final class UrlGeneratorTraitRector extends AbstractRector
{
    private const TRAIT_NAME = 'Drupal\Core\Routing\UrlGeneratorTrait';

    private const CLASS_NAME = 'Drupal\Core\Url';

    /**
     * Associative array where keys are class FQCNs and values are trait FQCNs.
     *
     * @var string[][]
     */
    private $traitsByClasses = [];

    /**
     * Methods (method names) provided by the deprecated trait.
     *
     * @var string[]
     */
    private $methodsByTrait = [];

    /**
     * UrlGeneratorTraitRector constructor.
     */
    public function __construct()
    {
        $rc = new \ReflectionClass(self::TRAIT_NAME);
        $this->methodsByTrait = array_map(function (\ReflectionMethod $method) {
            return $method->getName();
        }, $rc->getMethods());
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\TraitUse::class,
            Node\Stmt\Return_::class,
            Node\Expr\Assign::class,
            Node\Expr\ArrayItem::class,
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\TraitUse) {
            $rekey = false;
            foreach ($node->traits as $id => $trait) {
                if (self::TRAIT_NAME === (string) $trait) {
                    unset($node->traits[$id]);
                    $rekey = true;
                }
            }
            if ($rekey) {
                if (empty($node->traits)) {
                    $this->removeNode($node);
                } else {
                    $node->traits = array_values($node->traits);
                }
            }
        } elseif ($node instanceof Node\Stmt\Return_) {
            if ($node->expr instanceof Node\Expr\MethodCall) {
                if ($processed = $this->processMethodCall($node->expr)) {
                    $node->expr = $processed;
                }
            }
        } elseif ($node instanceof Node\Expr\Assign) {
            if ($node->expr instanceof Node\Expr\MethodCall) {
                if ($processed = $this->processMethodCall($node->expr)) {
                    $node->expr = $processed;
                }
            }
        } elseif ($node instanceof Node\Expr\ArrayItem) {
            if ($node->value instanceof Node\Expr\MethodCall) {
                if ($processed = $this->processMethodCall($node->value)) {
                    $node->value = $processed;
                }
            }
        } elseif ($node instanceof Node\Expr\MethodCall) {
            if ($processed = $this->processMethodCall($node)) {
                // $this->setUrlGenerator() should not be replaced with $this.
                if ('setUrlGenerator' !== $node->name->name) {
                    $this->addNodeAfterNode($processed, $node);
                }
                $this->removeNode($node);
            }
        }

        return $node;
    }

    public function getDefinition(): RectorDefinition
    {
        // FIXME return $this->decorated->getDefinition();
    }

    /**
     * @param string $class
     *
     * @return string[]
     *   Array of trait FQCNs implemented by a class and its parents.
     */
    private function getTraits(string $class)
    {
        if (!array_key_exists($class, $this->traitsByClasses)) {
            $this->traitsByClasses[$class] = [];
            $rc = new \ReflectionClass($class);
            do {
                $this->traitsByClasses[$class] = array_merge($this->traitsByClasses[$class], array_keys($rc->getTraits()));
            } while ($rc = $rc->getParentClass());
        }

        return $this->traitsByClasses[$class];
    }

    /**
     * Process method calls.
     *
     * @param \PhpParser\Node\Expr\MethodCall$node
     *   Method call that may or may not related to UrlGeneratorTrait trait.
     *
     * @throws \Rector\Exception\ShouldNotHappenException
     *   If method is related to UrlGeneratorTrait but it is not handled by
     *   this method.
     */
    private function processMethodCall(Node\Expr\MethodCall $node): ?Node\Expr
    {
        $result = null;
        $classNode = $node->getAttribute(Attribute::CLASS_NAME);
        // Ignore procedural code because traits can not be used there.
        if (null === $classNode) {
            return $result;
        }
        if (in_array(self::TRAIT_NAME, $this->getTraits($classNode))) {
            $method_name = $node->name->name;
            if (in_array($method_name, $this->methodsByTrait)) {
                if ('redirect' === $method_name) {
                    $urlFromRouteArgs = [
                        $node->args[0],
                    ];
                    if (array_key_exists(1, $node->args)) {
                        $urlFromRouteArgs[] = $node->args[1];
                    }
                    if (array_key_exists(2, $node->args)) {
                        $urlFromRouteArgs[] = $node->args[2];
                    }
                    $urlFromRouteExpr = new Node\Expr\StaticCall(new Node\Name\FullyQualified(self::CLASS_NAME), 'urlFromRoute', $urlFromRouteArgs);
                    $redirectResponseArgs = [$urlFromRouteExpr];
                    if (array_key_exists(3, $node->args)) {
                        $redirectResponseArgs[] = $node->args[3];
                    }
                    $result = new Node\Expr\New_(new Node\Name('Symfony\Component\HttpFoundation\RedirectResponse'), $redirectResponseArgs);
                } elseif ('url' === $method_name) {
                    $result = new Node\Expr\StaticCall(new Node\Name\FullyQualified(self::CLASS_NAME), 'fromRoute', $node->args);
                } elseif ('getUrlGenerator' === $method_name) {
                    $result = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_('url_generator'))]);
                } elseif ('setUrlGenerator' === $method_name) {
                    // It was a fluent setter.
                    $result = new Node\Expr\Variable('this');
                } else {
                    throw new ShouldNotHappenException("Unhandled {$method_name} method from UrlGeneratorTrait trait.");
                }
            }
        }

        return $result;
    }
}
