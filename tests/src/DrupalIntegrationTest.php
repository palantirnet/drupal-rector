<?php declare(strict_types=1);

namespace DrupalRector\Tests;

use DrupalRector\Rector\Deprecation\DatetimeDateStorageFormatRector;
use DrupalRector\Rector\Deprecation\DrupalSetMessageRector;
use DrupalRector\Rector\Deprecation\FileCreateDirectoryRector;
use PHPUnit\Framework\TestCase;
use Rector\Core\Bootstrap\RectorConfigsResolver;
use Rector\Core\Console\ConsoleApplication;
use Rector\Core\DependencyInjection\RectorContainerFactory;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class DrupalIntegrationTest extends TestCase {

    protected function setUp(): void
    {
        parent::setUp();
        if (!file_exists(__DIR__ . '/../fixtures/drupal/composer.json')) {
            self::markTestSkipped(<<<HEREDOC
Run the following commands to run the integration test, from the project root.

composer create-project drupal/recommended-project:^8.9 tests/fixtures/drupal
cd tests/fixtures/drupal
composer require --dev drupal/core-dev:^8.9
HEREDOC
);
        }
    }

    /**
     * @dataProvider integrationData
     */
    public function testIntegration(string $source, array $applied_rules)
    {
        $callable = static function () {
            $configPath = __DIR__ . '/../config/rector-phpunit.php';
            $_SERVER['argv'] = [
                'process',
                '--config=' . $configPath
            ];
            $rectorConfigsResolver = new RectorConfigsResolver();
            return $rectorConfigsResolver->provide();
        };
        $bootstrapConfigs = $callable();

        $rectorContainerFactory = new RectorContainerFactory();
        $container = $rectorContainerFactory->createFromBootstrapConfigs($bootstrapConfigs);

        /** @var ConsoleApplication $application */
        $application = $container->get(ConsoleApplication::class);
        $command = $application->find('process');
        $commandTester = new CommandTester($command);

        ob_start();
        $commandTester->execute([
            'source' => $source,
            '--dry-run' => true,
            '--output-format' => 'json',
        ], [
            'capture_stderr_separately' => true,
            'decorated' => true,
            'verbosity' => OutputInterface::VERBOSITY_DEBUG
        ]);
        $output = json_decode(ob_get_clean());
        self::assertObjectNotHasAttribute('errors', $output, var_export($output, true));
        self::assertObjectHasAttribute('file_diffs', $output);
        self::assertEquals($applied_rules, $output->file_diffs[0]->applied_rectors);
    }

    public function integrationData(): \Generator {
        yield [
            __DIR__ . '/../../rector_examples/datetime_date_storage_format.php',
            [DatetimeDateStorageFormatRector::class]
        ];
        yield [
            __DIR__ . '/../../rector_examples/src/FileCreateDirectoryNoUseStatement.php',
            [FileCreateDirectoryRector::class]
        ];
        yield [
            __DIR__ . '/../../rector_examples/drupal_set_message.php',
            [DrupalSetMessageRector::class]
        ];
        yield [
            __DIR__ . '/../../rector_examples/test/src/Functional/BrowserTestBaseGetMock.php',
            [DrupalSetMessageRector::class]
        ];
    }

}
