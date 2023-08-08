<?php
if (!isset($argv[1]) || !in_array($argv[1], ['show', 'set'], true)) {
    echo "Usage: php set-rector-dev-dependencies.php [show|set]" . PHP_EOL;
    exit(1);
}
$action = $argv[1];

if ($action === 'set' && !isset($argv[2])) {
    echo "Usage: php set-rector-dev-dependencies.php set [RECTOR_VERSION] [RECTOR_PIN_STRING]" . PHP_EOL;
    exit(1);
}

if (!file_exists(__DIR__ . '/vendor/composer/installed.php')) {
    echo "Please run composer install before running this script" . PHP_EOL;
    exit(1);
}

// Get installed packages
$installedSourceJson = file_get_contents(__DIR__ . '/vendor/composer/installed.json');
$installedSourcePackages = json_decode($installedSourceJson, true);
$installedPackages = [];
foreach ($installedSourcePackages['packages'] as $package) {
    $installedPackages[$package['name']] = $package['version'] . (substr($package['version'], 0, 4) === 'dev-' ? '#' . $package['source']['reference'] : '');
}


// Get installed Rector version
$rectorVersion = $installedPackages['rector/rector'];
echo "Rector version: $rectorVersion" . PHP_EOL;

if ($rectorVersion === 'dev-main') {
    echo "Rector is installed from dev-main, this script only works on a specific tag." . PHP_EOL;
    exit(1);
}

// These packages we need to pin to the same version as Rector
$packagedToPin = [
    "rector/rector-doctrine",
    "rector/rector-downgrade-php",
    "rector/rector-phpunit",
    "rector/rector-symfony",
    "nikic/php-parser",
];

// Get packages installed by Rector in this version
$rectorSourceJson = file_get_contents("https://raw.githubusercontent.com/rectorphp/rector/$rectorVersion/vendor/composer/installed.json");
$rectorSourcePackages = json_decode($rectorSourceJson, true);
$rectorPackages = [];
foreach ($rectorSourcePackages['packages'] as $package) {
    $rectorPackages[$package['name']] = $package['version'] . (substr($package['version'], 0, 4) === 'dev-' ? '#' . $package['source']['reference'] : '');
}

if($action === 'show') {
    echo "Version $rectorVersion of Rector requires the following packages to be pinned:" . PHP_EOL;
    foreach ($rectorPackages as $package => $version) {
        if(isset($installedPackages[$package])) {
            if ($version === $installedPackages[$package]) {
                echo "$package:$installedPackages[$package] (already installed)" . PHP_EOL;
            } else {
                echo "$package:$version (installed: $installedPackages[$package])" . PHP_EOL;
            }
        }
    }
    exit;
}

if ($action === 'set') {
    $pinRectorVersion = $argv[2];
    $pinRectorVersionString = $argv[3];

    // Packages that require a conflict to ping rector
    $conflictPackages = [
        'phpstan/phpstan'
    ];

    $composerJson = json_decode(mb_convert_encoding(file_get_contents(__DIR__ . '/composer.json'), 'UTF-8'), true, 512, JSON_UNESCAPED_UNICODE);
    if (!is_array($composerJson['conflict'])) {
        $composerJson['conflict'] = [];
    }
    $pinnedPackages = [
        'rector/rector' => $pinRectorVersionString,
    ];

    foreach ($conflictPackages as $package) {
        if(isset($rectorPackages[$package])) {
            $pinnedPackages[$package] = '>' . $rectorPackages[$package];
        }
    }

    $composerJson['conflict'] = array_merge($composerJson['conflict'], $pinnedPackages);
    file_put_contents(__DIR__ . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    echo "Updated composer.json, please run composer update to load the correct dependencies." . PHP_EOL;
}
