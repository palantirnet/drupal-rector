<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces removed DateTimeRangeConstantsInterface constants and
 * datetime_type_field_views_data_helper() with their Drupal 12 equivalents.
 *
 * Deprecated in drupal:11.2.0 and removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3574901
 */
final class ReplaceDateTimeRangeConstantsRector extends AbstractDrupalCoreRector
{
    private const CONSTANTS_INTERFACE = 'Drupal\datetime_range\DateTimeRangeConstantsInterface';
    private const DISPLAY_OPTIONS_ENUM = 'Drupal\datetime_range\DateTimeRangeDisplayOptions';

    private const CONST_MAP = [
        'BOTH' => 'Both',
        'START_DATE' => 'StartDate',
        'END_DATE' => 'EndDate',
    ];

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
            'Replace removed DateTimeRangeConstantsInterface constants and datetime_type_field_views_data_helper() with Drupal 12 equivalents',
            [
                new ConfiguredCodeSample(
                    'DateTimeRangeConstantsInterface::BOTH;',
                    '\\Drupal\\datetime_range\\DateTimeRangeDisplayOptions::Both->value;',
                    [new DrupalIntroducedVersionConfiguration('11.2.0')]
                ),
                new ConfiguredCodeSample(
                    'datetime_type_field_views_data_helper($field_storage, $data, $column);',
                    "\\Drupal::service('datetime.views_helper')->buildViewsData(\$field_storage, \$data, \$column);",
                    [new DrupalIntroducedVersionConfiguration('11.2.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class, FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if ($node instanceof ClassConstFetch) {
            return $this->refactorClassConst($node);
        }
        if ($node instanceof FuncCall) {
            return $this->refactorFuncCall($node);
        }

        return null;
    }

    private function refactorClassConst(ClassConstFetch $node): ?Node
    {
        if (!$node->name instanceof Identifier) {
            return null;
        }
        $constName = $node->name->toString();
        if (!isset(self::CONST_MAP[$constName])) {
            return null;
        }
        if (!$this->isName($node->class, self::CONSTANTS_INTERFACE)) {
            return null;
        }
        $enumCaseFetch = new ClassConstFetch(
            new FullyQualified(self::DISPLAY_OPTIONS_ENUM),
            self::CONST_MAP[$constName]
        );

        return new PropertyFetch($enumCaseFetch, 'value');
    }

    private function refactorFuncCall(FuncCall $node): ?Node
    {
        if (!$node->name instanceof Name) {
            return null;
        }
        if ($node->name->toString() !== 'datetime_type_field_views_data_helper') {
            return null;
        }
        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new String_('datetime.views_helper'))]
        );

        return new MethodCall($serviceCall, 'buildViewsData', $node->args);
    }
}
