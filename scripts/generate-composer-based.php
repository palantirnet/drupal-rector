<?php

declare(strict_types=1);

/**
 * Generate config/composer-based.php from the per-minor deprecation configs.
 *
 * The composer-based set registers every rule of every per-minor set in a
 * single file, each one bound to the exact `drupal/core` version the
 * deprecation was introduced in. Rector then activates only the rules whose
 * constraint the analysed project's installed core satisfies.
 *
 * The version of each registration is resolved as:
 *
 *   1. the lowest `X.Y.Z` version literal inside the registration call — this
 *      is the "introduced in" argument of the configuration value objects
 *      (DrupalIntroducedVersionConfiguration, FunctionToServiceConfiguration,
 *      ConstantToClassConfiguration, …);
 *   2. otherwise the minor of the config file it lives in, e.g.
 *      `drupal-11.3-deprecations.php` → `11.3.0`.
 *
 * Rule (1) is what makes a registration like
 * `RemovePhpUnitCompatibilityTraitRector` — declared in the 11.4 config but
 * gated to `12.0.0` — end up bound to `>=12.0.0` rather than `>=11.4.0`.
 *
 * The generated file is committed. Re-run this script after adding or changing
 * a rule registration in one of the config/drupal-N directories:
 *
 *   php scripts/generate-composer-based.php [target-file-path]
 *
 * The optional target path is used by the test that asserts the committed file
 * is up to date.
 */
$rootDir = dirname(__DIR__);
$targetFilePath = $argv[1] ?? $rootDir.'/config/composer-based.php';

/**
 * Config files in ascending Drupal version order; the "breaking" config of a
 * minor follows its deprecations config.
 *
 * @return array<array{version: string, filePath: string, label: string}>
 */
$resolveSourceConfigs = static function (string $rootDir): array {
    $sourceConfigs = [];

    foreach (glob($rootDir.'/config/drupal-*/drupal-*.php') as $filePath) {
        $fileName = basename($filePath);
        if (!preg_match('#^drupal-(\d+)\.(\d+)-(deprecations|breaking)\.php$#', $fileName, $matches)) {
            // the aggregated `drupal-N-all-deprecations.php` sets only import the per-minor ones
            continue;
        }

        [, $major, $minor, $kind] = $matches;

        $sourceConfigs[] = [
            'version' => sprintf('%d.%d.0', $major, $minor),
            'filePath' => $filePath,
            'label' => sprintf('Drupal %d.%d%s', $major, $minor, $kind === 'breaking' ? ' (breaking)' : ''),
            'sort' => [(int) $major, (int) $minor, $kind === 'breaking' ? 1 : 0],
        ];
    }

    usort($sourceConfigs, static fn (array $first, array $second): int => $first['sort'] <=> $second['sort']);

    return $sourceConfigs;
};

/**
 * Split the closure body of a config file into top-level statements, keeping
 * the comments that precede each one attached to it.
 *
 * Tokenized rather than line-scanned: the statement bodies hold comments and
 * string literals with unbalanced brackets in them (change record URLs, regular
 * expressions), which a bracket count over raw lines gets wrong.
 *
 * @return array<array{comment: string[], lines: string[]}>
 */
$splitIntoStatements = static function (string $fileContents, string $filePath): array {
    $closureHeader = "return static function (RectorConfig \$rectorConfig): void {\n";

    $bodyStartPosition = strpos($fileContents, $closureHeader);
    if ($bodyStartPosition === false) {
        throw new RuntimeException(sprintf('Unexpected config file shape in "%s"', $filePath));
    }

    $body = substr($fileContents, $bodyStartPosition + strlen($closureHeader));
    $body = rtrim((string) preg_replace('#\};\n$#', '', $body), "\n");

    $openPhpTag = '<?php ';
    $tokens = token_get_all($openPhpTag.$body);

    $statements = [];
    $pendingComment = [];
    $statementStartPosition = null;
    $position = -strlen($openPhpTag);
    $depth = 0;

    foreach ($tokens as $token) {
        $text = is_array($token) ? $token[1] : $token;
        $tokenStartPosition = $position;
        $position += strlen($text);

        if (is_array($token) && $token[0] === T_OPEN_TAG) {
            continue;
        }

        if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
            if ($statementStartPosition === null && $token[0] !== T_WHITESPACE) {
                $pendingComment[] = '    '.rtrim($text);
            }

            // a blank line detaches the comment above it from the next statement
            if ($statementStartPosition === null && $token[0] === T_WHITESPACE && substr_count($text, "\n") > 1) {
                $pendingComment = [];
            }

            continue;
        }

        $statementStartPosition ??= $tokenStartPosition;

        if (in_array($text, ['(', '[', '{'], true)) {
            ++$depth;
            continue;
        }

        if (in_array($text, [')', ']', '}'], true)) {
            --$depth;

            // a block statement, e.g. if/elseif/else, is not terminated by a semicolon
            if ($text !== '}' || $depth !== 0) {
                continue;
            }
        }

        if (($text === ';' || $text === '}') && $depth === 0) {
            $statementText = substr($body, $statementStartPosition, $position - $statementStartPosition);

            $statements[] = [
                'comment' => $pendingComment,
                'lines' => explode("\n", '    '.ltrim($statementText)),
            ];

            $pendingComment = [];
            $statementStartPosition = null;
        }
    }

    if ($statementStartPosition !== null) {
        throw new RuntimeException(sprintf('Unbalanced statement at the end of "%s"', $filePath));
    }

    return $statements;
};

/**
 * The lowest `X.Y.Z` literal in the statement, which is the version the
 * deprecation was introduced in.
 */
$resolveIntroducedVersion = static function (string $statement, string $fallbackVersion): string {
    if (preg_match_all("#'(\d+\.\d+\.\d+)'#", $statement, $matches) === 0) {
        return $fallbackVersion;
    }

    $versions = array_unique($matches[1]);
    usort($versions, 'version_compare');

    return $versions[0];
};

/**
 * Short class name => the imported fully qualified names it stands for across
 * all config files.
 *
 * @return array<string, string[]>
 */
$resolveImportsByShortName = static function (array $sourceConfigs): array {
    $importsByShortName = [];

    foreach ($sourceConfigs as $sourceConfig) {
        foreach (explode("\n", (string) file_get_contents($sourceConfig['filePath'])) as $line) {
            if (preg_match('#^use (.+\\\\(\w+));$#', $line, $matches) !== 1) {
                continue;
            }

            $importsByShortName[$matches[2]][] = $matches[1];
        }
    }

    return array_map(
        static fn (array $classNames): array => array_values(array_unique($classNames)),
        $importsByShortName
    );
};

$sourceConfigs = $resolveSourceConfigs($rootDir);
$importsByShortName = $resolveImportsByShortName($sourceConfigs);

// the same short name is used for two different classes, e.g.
// DrupalRector\Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector and
// DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector; those cannot be
// imported side by side, so they are referenced fully qualified instead
$ambiguousShortNames = array_keys(array_filter(
    $importsByShortName,
    static fn (array $classNames): bool => count($classNames) > 1
));

$useStatements = [];
$sections = [];
$registrationCount = 0;
$skippedSetCalls = [];

foreach ($sourceConfigs as $sourceConfig) {
    $fileContents = (string) file_get_contents($sourceConfig['filePath']);

    $fileImports = [];
    foreach (explode("\n", $fileContents) as $line) {
        if (str_starts_with($line, 'use ')) {
            $useStatements[] = $line;
        }

        if (preg_match('#^use (.+\\\\(\w+));$#', $line, $matches) === 1) {
            $fileImports[$matches[2]] = $matches[1];
        }
    }

    /**
     * Rewrite the ambiguous short names of this file to fully qualified ones.
     */
    $qualifyAmbiguousNames = static function (string $statementText) use ($ambiguousShortNames, $fileImports): string {
        foreach ($ambiguousShortNames as $shortName) {
            if (!isset($fileImports[$shortName])) {
                continue;
            }

            $className = $fileImports[$shortName];

            $statementText = (string) preg_replace_callback(
                '#(?<![\\\\\w])'.preg_quote($shortName, '#').'\b#',
                static fn (): string => '\\'.$className,
                $statementText
            );
        }

        return $statementText;
    };

    $sectionLines = [];

    foreach ($splitIntoStatements($fileContents, $sourceConfig['filePath']) as $statement) {
        $statementText = $qualifyAmbiguousNames(implode("\n", $statement['lines']));
        $version = $resolveIntroducedVersion($statementText, $sourceConfig['version']);
        $constraint = '>='.$version;

        if (preg_match('#^\s+\$rectorConfig->sets\(#', $statementText) === 1) {
            // third-party version sets (Symfony, PHPUnit); those packages ship their own
            // composer-based sets, enabled with withComposerBased(symfony: true, phpunit: true)
            $skippedSetCalls[] = $sourceConfig['label'];
            continue;
        }

        if (preg_match('#^\s+\$rectorConfig->singleton\(#', $statementText) === 1) {
            // services the rules depend on; registered once, unconditionally, in the header
            continue;
        }

        if (preg_match('#^\s+(if|elseif|else)\b#', $statementText) === 1) {
            if (!str_contains($statementText, 'TwigSetList') && !str_contains($statementText, 'ShouldNotHappenException')) {
                throw new RuntimeException(sprintf('Unhandled block statement in "%s"', $sourceConfig['filePath']));
            }

            // resolves the Twig set name for the skipped sets() call above
            continue;
        }

        if (preg_match('#^\s+\$\w+ = new #', $statementText) === 1) {
            // a configuration value object referenced by a registration below
            $sectionLines[] = implode("\n", [...$statement['comment'], $statementText]);

            continue;
        }

        if (preg_match('#^(\s+)\$rectorConfig->rule\((\S+)::class\);$#', $statementText, $matches) === 1) {
            $registration = sprintf("%s\$ruleSince(%s::class, '%s');", $matches[1], $matches[2], $constraint);
        } elseif (preg_match('#^(\s+)\$rectorConfig->rules\(\[$#', explode("\n", $statementText)[0], $matches) === 1) {
            $registrations = [];
            foreach (explode("\n", $statementText) as $ruleLine) {
                if (preg_match('#^\s+(\S+)::class,$#', $ruleLine, $ruleMatches) === 1) {
                    $registrations[] = sprintf("%s\$ruleSince(%s::class, '%s');", $matches[1], $ruleMatches[1], $constraint);
                }
            }

            $registration = implode("\n", $registrations);
        } elseif (str_contains($statementText, '$rectorConfig->ruleWithConfiguration(')) {
            $registration = str_replace(
                '$rectorConfig->ruleWithConfiguration(',
                '$rectorConfig->ruleWithConfigurationComposerVersionBound(',
                $statementText
            );

            // append the package and constraint arguments to the closing `]);`
            $registration = preg_replace(
                '#\]\);$#',
                sprintf("], 'drupal/core', '%s');", $constraint),
                $registration
            );
        } else {
            throw new RuntimeException(sprintf('Unhandled statement in "%s": %s', $sourceConfig['filePath'], substr(trim($statementText), 0, 80)));
        }

        ++$registrationCount;

        $sectionLines[] = implode("\n", [...$statement['comment'], $registration]);
    }

    if ($sectionLines === []) {
        continue;
    }

    $sections[] = sprintf(
        "    // ---------------------------------------------------------------------\n    // %s\n    // ---------------------------------------------------------------------\n\n%s",
        $sourceConfig['label'],
        implode("\n\n", $sectionLines)
    );
}

$body = implode("\n\n", $sections);

// keep only the imports the generated body actually uses
$useStatements = array_values(array_unique($useStatements));
sort($useStatements);

$usedUseStatements = [];
foreach ($useStatements as $useStatement) {
    preg_match('#^use (?:.*\\\\)?(\w+);$#', $useStatement, $matches);
    if (!isset($matches[1])) {
        continue;
    }

    if (in_array($matches[1], $ambiguousShortNames, true)) {
        continue;
    }

    if (preg_match('#(?<![\\\\\w])'.preg_quote($matches[1], '#').'\b#', $body) === 1) {
        $usedUseStatements[] = $useStatement;
    }
}

$usedUseStatements[] = 'use Composer\Semver\Semver;';
$usedUseStatements[] = 'use DrupalRector\Services\AddCommentService;';
$usedUseStatements[] = 'use Rector\Composer\InstalledPackageResolver;';
$usedUseStatements[] = 'use Rector\Config\RectorConfig;';
$usedUseStatements = array_values(array_unique($usedUseStatements));
sort($usedUseStatements);

$header = <<<'PHP'
    <?php

    declare(strict_types=1);

    __USE_STATEMENTS__

    /**
     * Every drupal-rector rule, bound to the exact `drupal/core` version its
     * deprecation was introduced in.
     *
     * GENERATED FILE — do not edit by hand. Run
     * `php scripts/generate-composer-based.php` after changing a per-minor config
     * in one of the config/drupal-N directories.
     *
     * Instead of picking set lists by hand, this set lets Rector pick the rules from
     * the installed `drupal/core` version: a site on 11.2 gets the rules bound to
     * `>=8.0.0` through `>=11.2.0` and never a later minor's. Because the installed
     * core is known exactly, the otherwise opt-in "breaking" renames (whose
     * replacement symbol only exists from a given minor onward) are safe to include
     * — they cannot fatal on a core that is guaranteed to have the replacement.
     *
     * Usage:
     *
     *     return RectorConfig::configure()
     *         ->withComposerBased(drupal: true);
     *
     * The Symfony and PHPUnit version sets that the per-minor Drupal configs pull in
     * are deliberately not repeated here: those packages ship their own
     * composer-based sets, bound to their own installed version, which is more
     * accurate than inferring them from the Drupal minor. Add
     * `symfony: true, phpunit: true` to the call above to get those too.
     *
     * To look ahead and prepare for a Drupal version you have not installed yet, use
     * the explicit \DrupalRector\Set\Drupal11SetList sets instead — they are not
     * version-bound.
     *
     * @see \DrupalRector\Set\DrupalSetList::COMPOSER_BASED
     */
    return static function (RectorConfig $rectorConfig): void {
        $rectorConfig->singleton(AddCommentService::class, fn (): AddCommentService => new AddCommentService());

        $installedCoreVersion = (new InstalledPackageResolver())->resolvePackageVersion('drupal/core');

        // the bootstrap resolves the Drupal root and the PHPUnit bootstrap file, which
        // only exist when the analysed project actually has Drupal installed
        if ($installedCoreVersion !== null) {
            $rectorConfig->import(__DIR__.'/drupal-bootstrap.php');
        }

        /**
         * Registers a rule that takes no configuration, and so cannot be registered
         * with ruleWithConfigurationComposerVersionBound(), only when the installed
         * `drupal/core` satisfies the constraint.
         *
         * @param class-string<\Rector\Contract\Rector\RectorInterface> $rectorClass
         */
        $ruleSince = static function (string $rectorClass, string $constraint) use ($rectorConfig, $installedCoreVersion): void {
            if ($installedCoreVersion === null) {
                return;
            }

            if (!Semver::satisfies($installedCoreVersion, $constraint)) {
                return;
            }

            $rectorConfig->rule($rectorClass);
        };

    PHP;

$header = str_replace('__USE_STATEMENTS__', implode("\n", $usedUseStatements), $header);

file_put_contents($targetFilePath, $header."\n".$body."\n};\n");

printf(
    'Wrote %s (%d registrations from %d configs, %d third-party sets() calls skipped)%s',
    $targetFilePath,
    $registrationCount,
    count($sourceConfigs),
    count($skippedSetCalls),
    PHP_EOL
);
