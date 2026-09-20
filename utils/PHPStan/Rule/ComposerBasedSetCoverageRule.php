<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Rule;

use DrupalRector\PHPStan\Collector\RegisteredRectorClassCollector;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * The composer-based set duplicates the registrations of the per-minor configs
 * on purpose, so this is the guard against the two drifting apart: a rule added
 * to a per-minor config and forgotten here would silently never be picked up by
 * composer-based selection.
 *
 * @implements Rule<CollectedDataNode>
 *
 * @see \DrupalRector\Tests\PHPStan\Rule\ComposerBasedSetCoverageRuleTest
 */
final class ComposerBasedSetCoverageRule implements Rule
{
    /**
     * @var string
     */
    private const PER_MINOR_CONFIG_FILE_NAME_REGEX = '#^drupal-\d+\.\d+-(deprecations|breaking)\.php$#';

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $registeredRectorClasses = $node->get(RegisteredRectorClassCollector::class);

        $composerBasedRectorClasses = [];
        foreach ($registeredRectorClasses as $filePath => $rectorClasses) {
            if (basename($filePath) !== PlainlyRegisteredRuleRule::COMPOSER_BASED_SET_FILE_NAME) {
                continue;
            }

            $composerBasedRectorClasses = array_merge($composerBasedRectorClasses, $rectorClasses);
        }

        // the set file is not part of this run, so there is nothing to compare against
        if ($composerBasedRectorClasses === []) {
            return [];
        }

        $ruleErrors = [];

        foreach ($registeredRectorClasses as $filePath => $rectorClasses) {
            if (preg_match(self::PER_MINOR_CONFIG_FILE_NAME_REGEX, basename($filePath)) !== 1) {
                continue;
            }

            foreach (array_unique(array_filter($rectorClasses)) as $rectorClass) {
                if (in_array($rectorClass, $composerBasedRectorClasses, true)) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(sprintf(
                    '"%s" is missing from the composer-based set, so it is never applied by composer-based selection. Register it in config/%s too.',
                    $rectorClass,
                    PlainlyRegisteredRuleRule::COMPOSER_BASED_SET_FILE_NAME
                ))
                    ->identifier('drupalRector.composerBasedSetCoverage')
                    ->file($filePath)
                    ->line(1)
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
