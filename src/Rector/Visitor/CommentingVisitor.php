<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Visitor;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\NodeTypeResolver\PHPStan\Scope\Contract\NodeVisitor\ScopeResolverNodeVisitorInterface;

class CommentingVisitor extends NodeVisitorAbstract implements ScopeResolverNodeVisitorInterface {

    public const COMMENT_ATTRIBUTE = 'rector_comment';

    public function leaveNode(Node $node)
    {
        if(!($node instanceof Stmt)){
            return null;
        }

        if (!$node->hasAttribute(self::COMMENT_ATTRIBUTE)) {
            return null;
        }

        $node->setAttribute(AttributeKey::COMMENTS, $node->getAttribute(self::COMMENT_ATTRIBUTE));
        return $node;
    }

}
