<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated JSONAPI_FILTER_AMONG_* global constants with JsonApiFilter class constants.
 *
 * Deprecated in drupal:11.3.0, removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3495600
 */
final class ReplaceJsonApiFilterConstantsRector extends AbstractRector
{
    private const CONSTANT_MAP = [
        'JSONAPI_FILTER_AMONG_ALL'       => 'AMONG_ALL',
        'JSONAPI_FILTER_AMONG_PUBLISHED' => 'AMONG_PUBLISHED',
        'JSONAPI_FILTER_AMONG_ENABLED'   => 'AMONG_ENABLED',
        'JSONAPI_FILTER_AMONG_OWN'       => 'AMONG_OWN',
    ];

    public function getNodeTypes(): array
    {
        return [Node\Expr\ConstFetch::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Expr\ConstFetch);

        $constName = $this->getName($node);
        if ($constName === null || !isset(self::CONSTANT_MAP[$constName])) {
            return null;
        }

        return new Node\Expr\ClassConstFetch(
            new Node\Name\FullyQualified('Drupal\jsonapi\JsonApiFilter'),
            self::CONSTANT_MAP[$constName]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated JSONAPI_FILTER_AMONG_* global constants with \\Drupal\\jsonapi\\JsonApiFilter::AMONG_* class constants (drupal:11.3.0)', [
            new CodeSample(
                'return [JSONAPI_FILTER_AMONG_ALL => AccessResult::allowed()];',
                'return [\\Drupal\\jsonapi\\JsonApiFilter::AMONG_ALL => AccessResult::allowed()];'
            ),
        ]);
    }
}
