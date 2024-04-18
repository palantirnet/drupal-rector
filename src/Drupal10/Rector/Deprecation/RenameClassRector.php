<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Drupal10\Rector\ValueObject\RenameClassRectorConfiguration;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Property;
use Rector\Configuration\RenamedClassesDataCollector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Renaming\NodeManipulator\ClassRenamer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RenameClassRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    /**
     * @readonly
     *
     * @var RenamedClassesDataCollector
     */
    private $renamedClassesDataCollector;
    /**
     * @readonly
     *
     * @var ClassRenamer
     */
    private $classRenamer;

    public function __construct(RenamedClassesDataCollector $renamedClassesDataCollector, ClassRenamer $classRenamer)
    {
        $this->renamedClassesDataCollector = $renamedClassesDataCollector;
        $this->classRenamer = $classRenamer;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [FullyQualified::class, Property::class, FunctionLike::class, Expression::class, ClassLike::class, If_::class];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof RenameClassRectorConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', RenameClassRectorConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        $oldToNewClasses = $this->renamedClassesDataCollector->getOldToNewClasses();
        if ($oldToNewClasses !== []) {
            /** @var \PHPStan\Analyser\Scope $scope */
            $scope = $node->getAttribute(AttributeKey::SCOPE);
            $return = $this->classRenamer->renameNode($node, $oldToNewClasses, $scope);
            if (!is_null($return)) {
                $scope->getFile();

                return $return;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated watchdog_exception(\'update\', $exception) calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
watchdog_exception('update', $exception);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
use \Drupal\Core\Utility\Error;
$logger = \Drupal::logger('update');
Error::logException($logger, $exception);
CODE_AFTER
                ,
                [
                    new DrupalIntroducedVersionConfiguration('10.1.0'),
                ]
            ),
        ]);
    }
}
