<?php declare(strict_types=1);

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/../../vendor/autoload.php';

$data = file_get_contents(__DIR__ . '/../../deprecation-index.yml');
if ($data === false) {
    throw new \RuntimeException('The deprecation-index.yml could not be read.');
}

// Ensure YAML is parseable.
try {
    $parsed_data = Yaml::parse($data);
}
catch (ParseException $exception) {
    throw new \RuntimeException(
        "The YAML was not valid. This is often caused by unescaped quotes.
        Please use `'` to escape a single quote.\n\n{$exception->getMessage()}"
    );
}

# Unicode characters sometimes get added when copy & pasting.
# They are not intended and don't match as easily.
if (mb_detect_encoding($data, 'ASCII', true) === false) {
    throw new \RuntimeException('The file contains non ASCII characters.
        Please make sure `\` and spaces are only using ASCII characters.');
}
