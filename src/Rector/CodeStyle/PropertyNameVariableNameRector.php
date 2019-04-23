<?php

declare(strict_types=1);

namespace Drupal8Rector\Rector\CodeStyle;

use Jawira\CaseConverter\Convert;
use PhpParser\Comment;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\ConfiguredCodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Corrects property names and variables names.
 *
 * @see https://www.drupal.org/node/608152#naming
 */
final class PropertyNameVariableNameRector extends AbstractRector
{
    private $variableNameBlacklist = [
        // PHP super globals should not be renamed.
        'GLOBALS',
        '_SERVER',
        '_REQUEST',
        '_POST',
        '_GET',
        '_FILES',
        '_ENV',
        '_COOKIE',
        '_SESSION',
        // $_entity should not be renamed to $entity sometimes.
        // https://github.com/drupal/core/blob/17557c97a816bc78e70989a86064c562bc27027f/lib/Drupal/Core/Entity/Controller/EntityController.php#L255
        '_entity',
    ];

    /**
     * Array of classes and interfaces that properties should not be renamed.
     *
     * @var string[]
     */
    private $doNotRenameProperties = [];

    /**
     * PropertyNameVariableNameRector constructor.
     *
     * @param string[] $doNotRenameProperties
     *   Array of classes and interfaces that properties should not be renamed.
     */
    public function __construct(array $doNotRenameProperties = [])
    {
        $this->doNotRenameProperties = $doNotRenameProperties;
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\PropertyProperty::class,
            Node\Stmt\ClassMethod::class,
            Node\Expr\PropertyFetch::class,
            Node\Stmt\Function_::class,
            Node\Expr\Variable::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\PropertyProperty) {
            $this->rectorProperty($node);
        } elseif ($node instanceof Node\Expr\PropertyFetch) {
            $this->rectorPropertyFetch($node);
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->rectorClassMethod($node);
        } elseif ($node instanceof Node\Stmt\Function_) {
            $this->rectorFunction($node);
        } elseif ($node instanceof Node\Expr\Variable) {
            $this->rectorVariable($node);
        }

        return $node;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes and unifies property and variable names according to Drupal 8 code style.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class Foo {
    protected $variable_one;
    
    public __constructor(string $inputVariable) {
        $this->variable_one = $inputVariable;
    }
    
    public function doSomething(string $inputVariable) {
        $meaningOfLife = 42;
        $this->variable_one = $inputVariable;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class Foo {
    protected $variableOne;
    
    public __constructor(string $input_variable) {
        $this->variableOne = $input_variable;
    }
    
    public function doSomething(string $input_variable) {
        $meaning_of_life = 42;
        $this->variableOne = $input_variable;
    }
}
CODE_SAMPLE
            ),
            new CodeSample(
                <<<'CODE_SAMPLE'
function my_module_foo(string $inputVariable) {
    $variableOne = 'bar';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
function my_module_foo(string $input_variable) {
    $variable_one = 'bar';
}
CODE_SAMPLE
            ),
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class Foo extend Bar\Baz {
    protected $variable_one;
    
    public __constructor(string $inputVariable) {
        $this->variable_one = $inputVariable;
    }
    
    public function doSomething(string $inputVariable) {
        $meaningOfLife = 42;
        $this->variable_one = $inputVariable;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class Foo extend Bar\Baz {
    protected $variable_one;
    
    public __constructor(string $input_variable) {
        $this->variable_one = $input_variable;
    }
    
    public function doSomething(string $input_variable) {
        $meaning_of_life = 42;
        $this->variable_one = $input_variable;
    }
}
CODE_SAMPLE
                ,
                [
                    '$doNotRenameProperties' => [
                        'Bar\Baz',
                    ],
                ]
            ),
        ]);
    }

    private function rectorProperty(Node\Stmt\PropertyProperty $property): void
    {
        foreach ($this->doNotRenameProperties as $classOrInterface) {
            $classNode = $property->getAttribute(AttributeKey::CLASS_NODE);
            if (null === $classNode || $this->isType($classNode, $classOrInterface)) {
                return;
            }
        }

        $property->name->name = $this->convertToLowerCamelCase($property->name->name);
    }

    private function rectorPropertyFetch(Node\Expr\PropertyFetch $node): void
    {
        // Do not rename field name properties that calls magic getters, also
        // ignore variables that are not variables,
        // ex.: $entities[$id] = $cache[$cid]->data; (PhpParser\Node\Expr\ArrayDimFetch)
        if ($node->var instanceof Node\Expr\Variable && 'this' === $node->var->name) {
            // Ignore cases when name is not an identifier, ex.:
            // $foo->bar()->{BAZ} = 'foobarbaz'; // ConstFetch
            if ($node->name instanceof Node\Identifier) {
                if ($node->var instanceof Node\Expr\MethodCall) {
                    // Magic getter, should be snake_case.
                    $node->name = $this->convertToSnakeCase($node->name);
                } else {
                    if ($node->name instanceof Node\Expr\MethodCall) {
                        // Ignore method names.
                        return;
                    }
                    foreach ($this->doNotRenameProperties as $classOrInterface) {
                        if ($this->isType($node, $classOrInterface)) {
                            return;
                        }
                    }
                    $isInherited = false;
                    $parentClass = $node->name->getAttribute(AttributeKey::PARENT_CLASS_NAME);
                    if ($parentClass) {
                        $rc = new \ReflectionClass($parentClass);
                        $isInherited = $rc->hasProperty($node->name->name);
                    }

                    // Inherited snake cased properties from parent classes
                    // (especially from Drupal core parent classes)
                    // should not renamed.
                    if (!$isInherited) {
                        // It should be lowerCamelCased.
                        $node->name->name = $this->convertToLowerCamelCase($node->name->name);
                    }
                }
            }
        }
    }

    private function rectorClassMethod(Node\Stmt\ClassMethod $node): void
    {
        $comments = $node->getAttribute('comments') ?? [];
        $cleanComments = [];
        /** @var \PhpParser\Comment\Doc $comment */
        foreach ($comments as $id => $comment) {
            $cleanComments[$id] = $this->rectorComment($comment);
        }
        $node->setAttribute('comments', $cleanComments);
    }

    private function rectorFunction(Node\Stmt\Function_ $node): void
    {
        $comments = $node->getAttribute('comments') ?? [];
        $cleanComments = [];
        /** @var \PhpParser\Comment\Doc $comment */
        foreach ($comments as $id => $comment) {
            $cleanComments[$id] = $this->rectorComment($comment);
        }
        $node->setAttribute('comments', $cleanComments);
    }

    private function rectorComment(Comment $comment): Comment
    {
        $matches = [];
        preg_match_all('/\$[a-zA-z0-9-_]+/', $comment->getText(), $matches);
        $matches = reset($matches);
        if (empty($matches)) {
            return $comment;
        }

        $text = $comment->getText();
        $matches = array_combine($matches, $matches);
        $matches = array_map(function (string $var_name) {
            return $this->convertToSnakeCase($var_name);
        }, $matches);
        foreach ($matches as $old_var_name => $new_var_name) {
            $text = str_replace($old_var_name, $new_var_name, $text);
        }

        $rc = new \ReflectionClass(get_class($comment));

        return $rc->newInstance($text, $comment->getLine(), $comment->getFilePos(), $comment->getTokenPos());
    }

    private function rectorVariable(Node\Expr\Variable $node): void
    {
        if (in_array($node->name, $this->variableNameBlacklist)) {
            return;
        }
        if ('this' === $node->name) {
            // rectorPropertyFetch() takes care of this variable
            // (which is actually a class property call).
            return;
        }
        // It should be snake_cased because it is a parameter.
        $node->name = $this->convertToSnakeCase($node->name);
    }

    private function convertToLowerCamelCase(string $string): string
    {
        $converter = new Convert($string);

        return $converter->toCamel();
    }

    private function convertToSnakeCase(string $string): string
    {
        $converter = new Convert($string);

        return $converter->toSnake();
    }
}
