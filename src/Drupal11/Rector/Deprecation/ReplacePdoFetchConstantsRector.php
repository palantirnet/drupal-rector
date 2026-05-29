<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Converts PDO::FETCH_* constants to FetchAs enum cases in Drupal Database API.
 *
 * Deprecated in drupal:11.2.0 and removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3525077
 * @see https://www.drupal.org/node/3488338
 */
final class ReplacePdoFetchConstantsRector extends AbstractDrupalCoreRector
{
    private const FETCH_MAP = [
        'FETCH_OBJ' => 'Object',
        'FETCH_ASSOC' => 'Associative',
        'FETCH_NUM' => 'List',
        'FETCH_COLUMN' => 'Column',
        'FETCH_CLASS' => 'ClassObject',
    ];

    private const DRUPAL_FETCH_METHODS = [
        'setFetchMode' => 0,
        'fetch' => 0,
        'fetchAll' => 0,
        'fetchAllAssoc' => 1,
    ];

    private const PDO_RETURN_METHODS = ['getClientStatement', 'getClientConnection'];

    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace PDO::FETCH_* constants with FetchAs enum cases in Drupal Database API calls',
            [
                new ConfiguredCodeSample(
                    '$statement->setFetchMode(\\PDO::FETCH_ASSOC);',
                    '$statement->setFetchMode(\\Drupal\\Core\\Database\\Statement\\FetchAs::Associative);',
                    [new DrupalIntroducedVersionConfiguration('11.2.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, ArrayItem::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof ArrayItem) {
            foreach ($this->configuration as $configuration) {
                if (!$this->rectorShouldApplyToDrupalVersion($configuration)) {
                    continue;
                }
                if ($this->isInBackwardsCompatibleCall($node)) {
                    continue;
                }

                $result = $this->refactorArrayItem($node);
                if ($result === null) {
                    return null;
                }

                if ($this->supportBackwardsCompatibility($configuration)) {
                    $cloned = clone $result;
                    $cloned->value = $this->createBcCallOnExpr(
                        $node->value,
                        $result->value,
                        $configuration->getIntroducedVersion()
                    );

                    return $cloned;
                }

                return $result;
            }

            return null;
        }

        return parent::refactor($node);
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if ($node instanceof MethodCall) {
            return $this->refactorMethodCall($node);
        }

        return null;
    }

    private function refactorMethodCall(MethodCall $node): ?MethodCall
    {
        $methodName = $this->getName($node->name);
        if ($methodName === null || !array_key_exists($methodName, self::DRUPAL_FETCH_METHODS)) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Database\StatementInterface'))) {
            return null;
        }

        if ($node->var instanceof MethodCall) {
            $calleeName = $this->getName($node->var->name);
            if ($calleeName !== null && in_array($calleeName, self::PDO_RETURN_METHODS, true)) {
                return null;
            }
        }

        $fetchArgIndex = self::DRUPAL_FETCH_METHODS[$methodName];
        $changed = false;
        $cloned = clone $node;

        foreach ($cloned->args as $index => $arg) {
            if (!$arg instanceof Arg || $index !== $fetchArgIndex) {
                continue;
            }
            $replacement = $this->replacePdoFetchConst($arg->value);
            if ($replacement !== null) {
                $newArg = clone $arg;
                $newArg->value = $replacement;
                $cloned->args[$index] = $newArg;
                $changed = true;
            }
        }

        return $changed ? $cloned : null;
    }

    private function refactorArrayItem(ArrayItem $node): ?ArrayItem
    {
        if ($node->key === null) {
            return null;
        }
        if (!($node->key instanceof String_) || $node->key->value !== 'fetch') {
            return null;
        }
        $replacement = $this->replacePdoFetchConst($node->value);
        if ($replacement === null) {
            return null;
        }
        $cloned = clone $node;
        $cloned->value = $replacement;

        return $cloned;
    }

    private function replacePdoFetchConst(Node $node): ?ClassConstFetch
    {
        if (!$node instanceof ClassConstFetch) {
            return null;
        }
        $className = $this->getName($node->class);
        if ($className !== 'PDO') {
            return null;
        }
        $constName = $this->getName($node->name);
        if ($constName === null || !isset(self::FETCH_MAP[$constName])) {
            return null;
        }

        return new ClassConstFetch(
            new FullyQualified('Drupal\Core\Database\Statement\FetchAs'),
            self::FETCH_MAP[$constName]
        );
    }
}
