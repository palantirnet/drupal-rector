<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated user_load_by_name() and user_load_by_mail() functions
 * with the equivalent entity storage loadByProperties() lookup.
 *
 * The deprecated functions return a single user object, or FALSE when no match
 * is found. loadByProperties() returns an array keyed by user ID, so the result
 * is normalised with array_values(...)[0] ?? FALSE to preserve the original
 * single-object-or-FALSE return contract.
 *
 * @see https://www.drupal.org/node/3555936
 */
class UserLoadByNameAndMailRector extends AbstractRector
{
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated function user_load_by_name(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::entityTypeManager()->getStorage(\'user\')->loadByProperties() instead.',
        'Call to deprecated function user_load_by_mail(). Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Use Drupal::entityTypeManager()->getStorage(\'user\')->loadByProperties() instead.',
    ];

    /**
     * Maps each deprecated function to the user entity property it queries.
     *
     * @var array<string, string>
     */
    private const PROPERTY_MAP = [
        'user_load_by_name' => 'name',
        'user_load_by_mail' => 'mail',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replaces deprecated user_load_by_name() and user_load_by_mail() with entity storage loadByProperties()',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$account = user_load_by_name($name);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$account = array_values(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => $name]))[0] ?? FALSE;
CODE_AFTER
                ),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        $functionName = $this->getName($node->name);
        if ($functionName === null || !isset(self::PROPERTY_MAP[$functionName])) {
            return null;
        }

        $args = $node->getArgs();
        if (!isset($args[0])) {
            return null;
        }

        $property = self::PROPERTY_MAP[$functionName];

        $entityTypeManager = $this->nodeFactory->createStaticCall('Drupal', 'entityTypeManager');
        $storage = $this->nodeFactory->createMethodCall($entityTypeManager, 'getStorage', $this->nodeFactory->createArgs(['user']));
        $loadByProperties = $this->nodeFactory->createMethodCall($storage, 'loadByProperties', $this->nodeFactory->createArgs([
            $this->nodeFactory->createArray([$property => $args[0]]),
        ]));

        $firstUser = new Node\Expr\ArrayDimFetch(
            $this->nodeFactory->createFuncCall('array_values', [$loadByProperties]),
            new Node\Scalar\Int_(0)
        );

        return new Node\Expr\BinaryOp\Coalesce($firstUser, new Node\Expr\ConstFetch(new Node\Name('FALSE')));
    }
}
