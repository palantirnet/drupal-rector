<?php declare(strict_types=1);

namespace DrupalRector\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

final class DeprecationIndexTest extends TestCase
{

    public function testIsValidYaml(): void
    {
        $contents = file_get_contents(__DIR__ . '/../../deprecation-index.yml');
        self::assertNotFalse($contents);
        try {
            $contents = Yaml::parse($contents);
        } catch (ParseException $exception) {
            $this->fail(
                "The YAML was not valid. This is often caused by unescaped quotes.
        Please use `'` to escape a single quote.\n\n{$exception->getMessage()}"
            );
        }
        self::assertIsArray($contents);
    }

    public function testOnlyAsciiCharacters(): void
    {
        $contents = file_get_contents(__DIR__ . '/../../deprecation-index.yml');
        self::assertNotFalse($contents);
        $result = mb_detect_encoding($contents, 'ASCII', true);
        self::assertNotFalse(
            $result,
            'The file contains non ASCII characters. Please make sure `\` and spaces are only using ASCII characters.'
        );
    }

}
