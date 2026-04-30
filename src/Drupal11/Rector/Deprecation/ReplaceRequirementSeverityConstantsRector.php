<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated REQUIREMENT_INFO/OK/WARNING/ERROR global constants with
 * RequirementSeverity enum cases.
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3575841
 */
final class ReplaceRequirementSeverityConstantsRector extends AbstractRector
{
    private const CONSTANT_MAP = [
        'REQUIREMENT_INFO'    => 'Info',
        'REQUIREMENT_OK'      => 'OK',
        'REQUIREMENT_WARNING' => 'Warning',
        'REQUIREMENT_ERROR'   => 'Error',
    ];

    public function getNodeTypes(): array
    {
        return [Node\Expr\ConstFetch::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Expr\ConstFetch);

        $name = ltrim($node->name->toString(), '\\');
        if (!isset(self::CONSTANT_MAP[$name])) {
            return null;
        }

        return new Node\Expr\ClassConstFetch(
            new Node\Name\FullyQualified('Drupal\Core\Extension\Requirement\RequirementSeverity'),
            self::CONSTANT_MAP[$name]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated REQUIREMENT_* global constants with RequirementSeverity enum cases (drupal:11.2.0)', [
            new CodeSample(
                "\$requirements['check']['severity'] = REQUIREMENT_ERROR;",
                "\$requirements['check']['severity'] = \\Drupal\\Core\\Extension\\Requirement\\RequirementSeverity::Error;"
            ),
        ]);
    }
}
