<?php

namespace Mxr576\Rector\FunctionLike;

use PhpParser\Node;
use Rector\Application\AppliedRectorCollector;
use Rector\Application\RemovedFilesCollector;
use Rector\NodeTypeResolver\Application\ClassLikeNodeCollector;
use Rector\NodeTypeResolver\PhpDoc\NodeAnalyzer\DocBlockAnalyzer;
use Rector\Php\Rector\FunctionLike\AbstractTypeDeclarationRector;
use Rector\Php\Rector\FunctionLike\ReturnTypeDeclarationRector;
use Rector\PhpParser\Node\Commander\NodeAddingCommander;
use Rector\PhpParser\Node\Commander\NodeRemovingCommander;
use Rector\PhpParser\Node\Commander\PropertyAddingCommander;
use Rector\PhpParser\Node\Maintainer\ConstFetchMaintainer;
use Rector\PhpParser\Node\Maintainer\FunctionLikeMaintainer;
use Rector\PhpParser\Node\Resolver\NameResolver;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\RectorDefinition\RectorDefinition;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Allows to re-enable return type declaration rector which is disabled by
 * default.
 *
 * @see drupal8-php71.yml
 */
final class ReturnTypeDeclarationRectorProxy extends AbstractTypeDeclarationRector
{
    /**
     * @var \Rector\Php\Rector\FunctionLike\ReturnTypeDeclarationRector
     */
    private $original;

    /**
     * ReturnTypeDeclarationRectorProxy constructor.
     *
     * @param \Rector\NodeTypeResolver\PhpDoc\NodeAnalyzer\DocBlockAnalyzer $docBlockAnalyzer
     * @param \Rector\NodeTypeResolver\Application\ClassLikeNodeCollector $classLikeNodeCollector
     * @param \Rector\PhpParser\Node\Maintainer\FunctionLikeMaintainer $functionLikeMaintainer
     * @param bool $enableObjectType
     */
    public function __construct(DocBlockAnalyzer $docBlockAnalyzer, ClassLikeNodeCollector $classLikeNodeCollector, FunctionLikeMaintainer $functionLikeMaintainer, bool $enableObjectType = false)
    {
        parent::__construct($docBlockAnalyzer, $classLikeNodeCollector, $functionLikeMaintainer, $enableObjectType);
        $this->original = new ReturnTypeDeclarationRector($docBlockAnalyzer, $classLikeNodeCollector, $functionLikeMaintainer, $enableObjectType);
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node): ?Node
    {
        return $this->original->refactor($node);
    }

    public function getDefinition(): RectorDefinition
    {
        return $this->original->getDefinition();
    }

    /**
     * @inheritdoc
     */
    public function setNameResolver(NameResolver $nameResolver): void
    {
        $this->original->setNameResolver($nameResolver);
    }

    /**
     * @inheritdoc
     */
    public function setAbstractRectorDependencies(AppliedRectorCollector $appliedRectorCollector, SymfonyStyle $symfonyStyle, ValueResolver $valueResolver, RemovedFilesCollector $removedFilesCollector): void
    {
        parent::setAbstractRectorDependencies($appliedRectorCollector, $symfonyStyle, $valueResolver, $removedFilesCollector);
        $this->original->setAbstractRectorDependencies($appliedRectorCollector, $symfonyStyle, $valueResolver, $removedFilesCollector);
    }

    /**
     * @inheritdoc
     */
    public function setConstFetchAnalyzer(ConstFetchMaintainer $constFetchMaintainer): void
    {
        parent::setConstFetchAnalyzer($constFetchMaintainer);
        $this->original->setConstFetchAnalyzer($constFetchMaintainer);
    }

    /**
     * @inheritdoc
     */
    public function setRequiredCommanders(NodeRemovingCommander $nodeRemovingCommander, NodeAddingCommander $nodeAddingCommander, PropertyAddingCommander $propertyAddingCommander): void
    {
        parent::setRequiredCommanders($nodeRemovingCommander, $nodeAddingCommander, $propertyAddingCommander);
        $this->original->setRequiredCommanders($nodeRemovingCommander, $nodeAddingCommander, $propertyAddingCommander);
    }
}
