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
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated locale.compare.inc functions with LocaleProjectRepository and LocaleProjectChecker service methods.
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3037031
 * @see https://www.drupal.org/node/3037033
 */
class LocaleCompareIncToServiceRector extends AbstractDrupalCoreRector
{
    private const LOCALE_PROJECT_REPOSITORY = 'Drupal\locale\LocaleProjectRepository';
    private const LOCALE_PROJECT_CHECKER = 'Drupal\locale\LocaleProjectChecker';

    /** @var DrupalIntroducedVersionConfiguration[] */
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

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$node->name instanceof Name) {
            return null;
        }

        return match ($node->name->toString()) {
            'locale_translation_flush_projects' => $this->buildServiceCall(self::LOCALE_PROJECT_REPOSITORY, 'deleteAll', $node->args),
            'locale_translation_build_projects' => $this->buildServiceCall(self::LOCALE_PROJECT_REPOSITORY, 'buildProjects', $node->args),
            'locale_translation_check_projects' => $this->buildCheckerCall('checkProjects', $node->args),
            'locale_translation_check_projects_local' => $this->buildCheckerCall('checkLocalProjects', $node->args),
            default => null,
        };
    }

    /**
     * Builds a checker service call, expanding empty $projects to array_keys(getAll()).
     *
     * @param Arg[] $args
     */
    private function buildCheckerCall(string $method, array $args): MethodCall
    {
        if (count($args) === 0) {
            $getAll = new MethodCall(
                $this->buildDrupalServiceCall(self::LOCALE_PROJECT_REPOSITORY),
                'getAll',
                []
            );
            $args = [new Arg(new FuncCall(new Name('array_keys'), [new Arg($getAll)]))];
        }

        return $this->buildServiceCall(self::LOCALE_PROJECT_CHECKER, $method, $args);
    }

    /** @param Arg[] $args */
    private function buildServiceCall(string $serviceClass, string $method, array $args): MethodCall
    {
        return new MethodCall($this->buildDrupalServiceCall($serviceClass), $method, $args);
    }

    private function buildDrupalServiceCall(string $serviceClass): StaticCall
    {
        return new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new ClassConstFetch(new FullyQualified($serviceClass), 'class'))]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated locale.compare.inc functions with LocaleProjectRepository and LocaleProjectChecker service methods.',
            [
                new ConfiguredCodeSample(
                    'locale_translation_flush_projects();',
                    '\Drupal::service(\Drupal\locale\LocaleProjectRepository::class)->deleteAll();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'locale_translation_build_projects();',
                    '\Drupal::service(\Drupal\locale\LocaleProjectRepository::class)->buildProjects();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'locale_translation_check_projects();',
                    '\Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->checkProjects(array_keys(\Drupal::service(\Drupal\locale\LocaleProjectRepository::class)->getAll()));',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    "locale_translation_check_projects_local(['drupal'], ['de']);",
                    "\Drupal::service(\Drupal\locale\LocaleProjectChecker::class)->checkLocalProjects(['drupal'], ['de']);",
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
