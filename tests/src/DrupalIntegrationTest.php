<?php declare(strict_types=1);

namespace DrupalRector\Tests;

use DrupalRector\Rector\Deprecation\DatetimeDateStorageFormatRector;
use PHPUnit\Framework\TestCase;
use Rector\Core\Bootstrap\RectorConfigsResolver;
use Rector\Core\Console\ConsoleApplication;
use Rector\Core\DependencyInjection\RectorContainerFactory;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class DrupalIntegrationTest extends TestCase {

    public function testIntegration() {
        $callable = static function () {
            $configPath = __DIR__ . '/../config/rector-phpunit.php';
            $_SERVER['argv'] = [
                'foo',
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
            'source' => __DIR__ . '/../../rector_examples/datetime_date_storage_format.php',
            '--dry-run' => true,
            '--output-format' => 'json',
        ], [
            'capture_stderr_separately' => true,
            'decorated' => true,
            'verbosity' => OutputInterface::VERBOSITY_DEBUG
        ]);
        $output = json_decode(ob_get_clean());
        self::assertContains(DatetimeDateStorageFormatRector::class, $output->file_diffs[0]->applied_rectors);
        self::assertContains('rector_examples/datetime_date_storage_format.php', $output->changed_files);
    }

}
