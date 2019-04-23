<?php

namespace Drupal8Rector\Rector\Deprecation;

use Drupal8Rector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\Exception\ShouldNotHappenException;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated UrlGeneratorTrait trait.
 */
final class UrlGeneratorTraitRector extends AbstractRector
{
    use TraitsByClassHelperTrait;

    private const REPLACED_TRAIT_FQN = 'Drupal\Core\Routing\UrlGeneratorTrait';

    private const URL_CLASS_FQCN = 'Drupal\Core\Url';

    private const REDIRECT_RESPONSE_FQCN = 'Symfony\Component\HttpFoundation\RedirectResponse';

    /**
     * Cached methods (method names) provided by the deprecated trait.
     *
     * Use getMethodsByTrait() instead.
     *
     * @var string[]|null
     */
    private $_methodsByTrait;

    /**
     * Array of name nodes keyed by classes based on $replaceWithFqn value.
     *
     * @var \PhpParser\Node\Name[]
     */
    private $replacementClassesNames = [];

    /**
     * Whether to replace methods by using FQN or not.
     *
     * @var bool
     */
    private $replaceWithFqn;

    /**
     * Add urlGenerator property to classes that used UrlGeneratorTrait.
     *
     * @var bool
     */
    private $addUrlGeneratorProperty;

    /**
     * UrlGeneratorTraitRector constructor.
     *
     * @param bool $replaceWithFqn
     *   Whether to replace depreocated methods with fully qualified method
     *   names or not. If it is false this rector adds new imports to all
     *   classes that used the replaced trait - even if the trait method was
     *   in use in the class. An external tool (for example PHPCBF) should
     *   optimize and remove unnecessary imports
     * @param bool $addUrlGeneratorProperty
     *   Add urlGenerator property to classes that used UrlGeneratorTrait.
     *   Disabled by default because it requires more deep clean-up.
     */
    public function __construct(bool $replaceWithFqn = false, bool $addUrlGeneratorProperty = false)
    {
        $this->replacementClassesNames = [
            self::URL_CLASS_FQCN => $replaceWithFqn ? new Node\Name\FullyQualified(self::URL_CLASS_FQCN) : new Node\Name('Url'),
            self::REDIRECT_RESPONSE_FQCN => $replaceWithFqn ? new Node\Name\FullyQualified(self::REDIRECT_RESPONSE_FQCN) : new Node\Name('RedirectResponse'),
        ];
        $this->replaceWithFqn = $replaceWithFqn;
        $this->addUrlGeneratorProperty = $addUrlGeneratorProperty;
    }

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Namespace_::class,
            Node\Stmt\Class_::class,
            Node\Stmt\TraitUse::class,
            Node\Stmt\Return_::class,
            Node\Stmt\Expression::class,
            Node\Expr\Assign::class,
            Node\Expr\ArrayItem::class,
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\Namespace_ && !$this->replaceWithFqn) {
            $classNode = null;
            $urlClassExists = false;
            $responseClassExists = false;
            // Probably the last stmt is the class.
            foreach (array_reverse($node->stmts) as $stmt) {
                // Exit from loop as early as we can.
                if ($classNode && $urlClassExists && $responseClassExists) {
                    break;
                }

                if ($stmt instanceof Node\Stmt\Use_) {
                    foreach ($stmt->uses as $use) {
                        if (self::URL_CLASS_FQCN === (string) $use->name) {
                            $urlClassExists = true;
                        } elseif (self::REDIRECT_RESPONSE_FQCN === (string) $use->name) {
                            $responseClassExists = true;
                        }
                    }
                } elseif ($stmt instanceof Node\Stmt\Class_) {
                    $classNode = $stmt;
                }
            }
            // Ignore interfaces, etc.
            if ($classNode && $this->isTraitInUse((string) $classNode->namespacedName)) {
                // This adds these namespaces to all files even if no method
                // is called from these classes. An external tool should
                // optimize and remove created unnecessary imports.
                if (!$urlClassExists) {
                    array_unshift($node->stmts, new Node\Stmt\Use_([new Node\Stmt\UseUse(new Node\Name\FullyQualified(self::URL_CLASS_FQCN))]));
                }
                if (!$responseClassExists) {
                    array_unshift($node->stmts, new Node\Stmt\Use_([new Node\Stmt\UseUse(new Node\Name\FullyQualified(self::REDIRECT_RESPONSE_FQCN))]));
                }
            }
        } elseif ($node instanceof Node\Stmt\Class_ && $this->addUrlGeneratorProperty) {
            if ($this->isTraitInUse($node->namespacedName)) {
                $hasUrlGeneratorProperty = false;
                $firstPropertyPosition = null;
                foreach ($node->stmts as $i => $stmt) {
                    if ($stmt instanceof Node\Stmt\Property) {
                        if (null === $firstPropertyPosition) {
                            $firstPropertyPosition = $i;
                        }
                        foreach ($stmt->props as $property) {
                            if ('urlGenerator' === (string) $property->name) {
                                $hasUrlGeneratorProperty = true;
                                break 2;
                            }
                        }
                    }
                }

                if (!$hasUrlGeneratorProperty) {
                    $urlGeneratorProperty = new Node\Stmt\Property(Node\Stmt\Class_::MODIFIER_PROTECTED, [new Node\Stmt\PropertyProperty(new Node\VarLikeIdentifier('urlGenerator'))]);
                    if (null === $firstPropertyPosition) {
                        array_unshift($node->stmts, $urlGeneratorProperty);
                    } else {
                        $node->stmts = array_merge(array_slice($node->stmts, 0, $firstPropertyPosition), [$urlGeneratorProperty], array_slice($node->stmts, $firstPropertyPosition));
                    }
                }
            }
        } elseif ($node instanceof Node\Stmt\TraitUse) {
            $rekey = false;
            foreach ($node->traits as $id => $trait) {
                if (self::REPLACED_TRAIT_FQN === (string) $trait) {
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
        } elseif ($node instanceof Node\Stmt\Return_ && null !== $node->expr) {
            $node->expr = $this->refactor($node->expr);
        } elseif ($node instanceof Node\Stmt\Expression) {
            $node->expr = $this->refactor($node->expr);
        } elseif ($node instanceof Node\Expr\Assign) {
            $node->expr = $this->refactor($node->expr);
        } elseif ($node instanceof Node\Expr\ArrayItem && null !== $node->value) {
            $node->value = $this->refactor($node->value);
        }
        // Ignore non-trivial identifiers, like when method name is created with concatenation.
        // @see https://git.drupalcode.org/project/features/blob/8.x-3.8/modules/features_ui/src/Form/FeaturesEditForm.php#L643
        elseif ($node instanceof Node\Expr\MethodCall && $node->name instanceof Node\Identifier) {
            // Sanity check, single "$this->setUrlGenerator()" should be
            // removed.
            $parentNode = $node->getAttribute(AttributeKey::PARENT_NODE);
            if ('setUrlGenerator' === $node->name->name && $parentNode instanceof Node\Stmt\Expression && $parentNode->expr === $node) {
                $this->removeNode($node);
            } elseif ($processed = $this->processMethodCall($node)) {
                return $processed;
            }
        }

        return $node;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition(sprintf('Removes usages of deprecated %s trait', self::REPLACED_TRAIT_FQN));
    }

    private function getMethodsByTrait(): array
    {
        if (null === $this->_methodsByTrait) {
            $rc = new \ReflectionClass(self::REPLACED_TRAIT_FQN);
            $this->_methodsByTrait = array_map(function (\ReflectionMethod $method) {
                return $method->getName();
            }, $rc->getMethods());
        }

        return $this->_methodsByTrait;
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
        $className = $node->getAttribute(AttributeKey::CLASS_NAME);
        // Ignore procedural code because traits can not be used there.
        if (null === $className) {
            return $result;
        }
        if ($this->isTraitInUse($className)) {
            $method_name = $node->name->name;
            if (in_array($method_name, $this->getMethodsByTrait())) {
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
                    $urlFromRouteExpr = new Node\Expr\StaticCall($this->replacementClassesNames[self::URL_CLASS_FQCN], 'fromRoute', $urlFromRouteArgs);
                    $redirectResponseArgs = [$urlFromRouteExpr];
                    if (array_key_exists(3, $node->args)) {
                        $redirectResponseArgs[] = $node->args[3];
                    }
                    $result = new Node\Expr\New_($this->replacementClassesNames[self::REDIRECT_RESPONSE_FQCN], $redirectResponseArgs);
                } elseif ('url' === $method_name) {
                    $result = new Node\Expr\StaticCall($this->replacementClassesNames[self::URL_CLASS_FQCN], 'fromRoute', $node->args);
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

    /**
     * @param string $fqcn
     *
     * @return bool
     */
    private function isTraitInUse(string $fqcn): bool
    {
        return in_array(self::REPLACED_TRAIT_FQN, $this->getTraitsByClass($fqcn));
    }
}
