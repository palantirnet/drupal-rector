<?php declare(strict_types=1);

namespace DrupalRector\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class DeprecationIndexTest extends TestCase {

    public function testIsValidYaml() {
        $contents = file_get_contents(__DIR__ . '/../../deprecation-index.yml');
        self::assertNotFalse($contents);
        $contents = Yaml::parse($contents);
        self::assertIsArray($contents);
    }

    public function testOnlyAsciiCharacters() {
        $contents = file_get_contents(__DIR__ . '/../../deprecation-index.yml');
        self::assertNotFalse($contents);
        $result = mb_detect_encoding($contents, 'ASCII', true);
        self::assertNotFalse(
            $result,
            'The file contains non ASCII characters. Please make sure `\` and spaces are only using ASCII characters.'
        );
    }

}
