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
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use Rector\ValueObject\Application\File;
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

    /**
     * Statement types a receiver may be declared as. The concrete classes are
     * listed as well because the interface only matches when Drupal core is
     * autoloadable, whereas a `@var \Drupal\Core\Database\Statement` doc block
     * is matched by name either way.
     */
    private const STATEMENT_CLASSES = [
        'Drupal\Core\Database\StatementInterface',
        'Drupal\Core\Database\Statement',
        'Drupal\Core\Database\StatementWrapperIterator',
    ];

    private const PDO_RETURN_METHODS = ['getClientStatement', 'getClientConnection'];

    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    /**
     * @var \WeakMap<File, array{int, bool}>|null
     */
    private ?\WeakMap $pdoInstantiationByFile = null;

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
        return [MethodCall::class, NullsafeMethodCall::class, ArrayItem::class];
    }

    public function refactor(Node $node): ?Node
    {
        foreach ($this->configuration as $configuration) {
            if (!$this->rectorShouldApplyToDrupalVersion($configuration)) {
                continue;
            }
            if ($this->isInBackwardsCompatibleCall($node)) {
                continue;
            }

            if ($node instanceof ArrayItem) {
                $result = $this->refactorArrayItem($node, $configuration);
            } elseif ($node instanceof MethodCall || $node instanceof NullsafeMethodCall) {
                $result = $this->refactorMethodCall($node, $configuration);
            } else {
                return null;
            }

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        // Everything is handled in refactor(): the backwards compatible wrapper
        // goes around the fetch mode value, not around the whole node.
        return null;
    }

    /**
     * Builds the replacement for a PDO::FETCH_* value, wrapped in a
     * DeprecationHelper::backwardsCompatibleCall() when the project still
     * supports cores older than the introduced version.
     *
     * The wrapper surrounds only the value, never the whole call. That keeps
     * multi-line query chains untouched and keeps FetchAs::* and \PDO::FETCH_*
     * on the same line, which is what Upgrade Status' fetch mode scan expects.
     */
    private function createReplacementValue(Node\Expr $value, VersionedConfigurationInterface $configuration): ?Node\Expr
    {
        $replacement = $this->replacePdoFetchConst($value);
        if ($replacement === null) {
            return null;
        }

        if ($this->supportBackwardsCompatibility($configuration)) {
            return $this->createBcCallOnExpr($value, $replacement, $configuration->getIntroducedVersion());
        }

        return $replacement;
    }

    private function refactorMethodCall(MethodCall|NullsafeMethodCall $node, VersionedConfigurationInterface $configuration): ?Node
    {
        $methodName = $this->getName($node->name);
        if ($methodName === null || !array_key_exists($methodName, self::DRUPAL_FETCH_METHODS)) {
            return null;
        }

        if (!$this->isDrupalStatementReceiver($node->var)) {
            return null;
        }

        $fetchArgIndex = self::DRUPAL_FETCH_METHODS[$methodName];
        $arg = $node->args[$fetchArgIndex] ?? null;
        if (!$arg instanceof Arg) {
            return null;
        }

        $replacement = $this->createReplacementValue($arg->value, $configuration);
        if ($replacement === null) {
            return null;
        }

        // Mutate in place so the format preserving printer only reprints the
        // argument and leaves the rest of the (possibly multi-line) chain alone.
        $arg->value = $replacement;

        return $node;
    }

    /**
     * Whether the receiver of a fetch call is a Drupal database statement.
     *
     * Receivers that PHPStan resolves to StatementInterface qualify. Contrib
     * code regularly produces receivers PHPStan cannot resolve (untyped
     * properties, extend() chains, assignments in conditions), so a receiver of
     * unknown type qualifies too unless there is a sign it is native PDO.
     */
    private function isDrupalStatementReceiver(Node\Expr $receiver): bool
    {
        foreach (self::STATEMENT_CLASSES as $statementClass) {
            if ($this->isObjectType($receiver, new ObjectType($statementClass))) {
                return !$this->isRawPdoChain($receiver, false);
            }
        }

        if (!$this->getType($receiver) instanceof MixedType) {
            // A known type that is not a Drupal statement, native PDOStatement
            // included.
            return false;
        }

        return !$this->isRawPdoChain($receiver, true) && !$this->fileInstantiatesPdo();
    }

    /**
     * Whether the receiver chain is, or derives from, native PDO.
     *
     * Walks the whole chain: getClientStatement()/getClientConnection() anywhere
     * in it hand out a raw PDO object. When $checkNames is set, a variable,
     * property or method named like PDO counts too. That is a heuristic for
     * receivers of unknown type only: a receiver PHPStan resolves to a Drupal
     * statement is never second-guessed by its name.
     */
    private function isRawPdoChain(Node\Expr $receiver, bool $checkNames): bool
    {
        $current = $receiver;
        while (true) {
            if ($current instanceof MethodCall || $current instanceof NullsafeMethodCall) {
                $name = $this->getName($current->name);
                if ($name !== null && (in_array($name, self::PDO_RETURN_METHODS, true) || ($checkNames && stripos($name, 'pdo') !== false))) {
                    return true;
                }
                $current = $current->var;
            } elseif ($current instanceof PropertyFetch || $current instanceof NullsafePropertyFetch) {
                $name = $this->getName($current->name);
                if ($checkNames && $name !== null && stripos($name, 'pdo') !== false) {
                    return true;
                }
                $current = $current->var;
            } elseif ($current instanceof Variable) {
                $name = $this->getName($current);

                return $checkNames && $name !== null && stripos($name, 'pdo') !== false;
            } else {
                return false;
            }
        }
    }

    /**
     * Whether the current file contains `new PDO` / `new \PDO`.
     *
     * Upgrade Status skips such files entirely, since the fetch modes there are
     * genuine PDO calls.
     */
    private function fileInstantiatesPdo(): bool
    {
        $file = $this->getFile();
        $this->pdoInstantiationByFile ??= new \WeakMap();
        $version = $file->getNewStmtsVersion();
        $cached = $this->pdoInstantiationByFile[$file] ?? null;
        if ($cached !== null && $cached[0] === $version) {
            return $cached[1];
        }

        $found = (new NodeFinder())->findFirst(
            $file->getNewStmts(),
            fn (Node $candidate): bool => $candidate instanceof New_
                && $candidate->class instanceof Name
                && $this->isName($candidate->class, 'PDO')
        ) !== null;
        $this->pdoInstantiationByFile[$file] = [$version, $found];

        return $found;
    }

    private function refactorArrayItem(ArrayItem $node, VersionedConfigurationInterface $configuration): ?ArrayItem
    {
        if (!($node->key instanceof String_) || $node->key->value !== 'fetch') {
            return null;
        }
        $replacement = $this->createReplacementValue($node->value, $configuration);
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
