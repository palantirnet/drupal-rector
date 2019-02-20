<?php

namespace Mxr576\Rector\CodeStyle;

use Jawira\CaseConverter\Convert;
use PhpParser\Comment;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\Attribute;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Corrects property names and variables names.
 *
 * @see https://www.drupal.org/node/608152#naming
 */
final class PropertyNameVariableNameRector extends AbstractRector
{
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
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            $this->rectorClassMethod($node);
        } elseif ($node instanceof Node\Expr\PropertyFetch) {
            $this->rectorPropertyFetch($node);
        } elseif ($node instanceof Node\Stmt\Function_) {
            $this->rectorFunction($node);
        } elseif ($node instanceof Node\Expr\Variable) {
            $this->rectorVariable($node);
        }

        return $node;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes property names according to Drupal 8 code style.', [
            new CodeSample(
                '$someObject->property_name;',
                '$someObject->propertyName;'
            ),
        ]);
    }

    private function rectorProperty(Node\Stmt\PropertyProperty $property): void
    {
        $property->name = $this->convertToLowerCamelCase($property->name);
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

    private function rectorComment(Comment $comment)
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

        return new Doc($text, $comment->getLine(), $comment->getFilePos(), $comment->getTokenPos());
    }

    private function rectorVariable(Node\Expr\Variable $node)
    {
        if ('this' === $node->name) {
            // rectorPropertyFetch() takes care of this variable
            // (which is actually a class property call).
            return $node;
        }
        // It should be snake_cased because it is a parameter.
        $node->name = $this->convertToSnakeCase($node->name);
    }

    private function rectorPropertyFetch(Node\Expr\PropertyFetch $node)
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
                        return $node;
                    }
                    $isInherited = false;
                    $parentClass = $node->name->getAttribute(Attribute::PARENT_CLASS_NAME);
                    if ($parentClass && 0 === strpos($parentClass, 'Drupal\Core')) {
                        $rc = new \ReflectionClass($parentClass);
                        $isInherited = $rc->hasProperty($node->name->name);
                    }

                    // Inherited snake cased class properties from Drupal 8 entities
                    // should not renamed.
                    if (!$isInherited) {
                        // It should be lowerCamelCased.
                        $node->name = $this->convertToLowerCamelCase($node->name);
                    }
                }
            }
        }
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
