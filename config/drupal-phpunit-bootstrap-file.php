<?php

/**
 * @file
 *
 * This fixes Drupal testing namespace autoloading and PHPUnit compatibility.
 */

use DrupalFinder\DrupalFinder;
use Rector\Core\Autoloading\BootstrapFilesIncluder;
use Rector\Core\Exception\ShouldNotHappenException;

if (!isset($this) || !$this instanceof BootstrapFilesIncluder) {
    throw new ShouldNotHappenException('The Drupal PHPUnit Bootstrap file could not access the BootstrapFilesIncluder');
}

/** @phpstan-ignore-next-line */
$parameterProvider = $this->parameterProvider;
if ($parameterProvider === NULL) {
    throw new ShouldNotHappenException('We were unable to access the parameter provider from the BootstrapFilesIncluder.');
}

/** @phpstan-ignore-next-line */
$autoloadPaths = $parameterProvider->provideArrayParameter(\Rector\Core\Configuration\Option::AUTOLOAD_PATHS);
if (count($autoloadPaths) === 0) {
    throw new \RuntimeException('No autoload paths were specified.');
}

$drupalFinder = new DrupalFinder();
$drupalFinder->locateRoot($autoloadPaths[0]);
$drupalRoot = $drupalFinder->getDrupalRoot();
$drupalVendorRoot = $drupalFinder->getVendorDir();

if (! (bool) $drupalRoot || ! (bool) $drupalVendorRoot) {
    throw new \RuntimeException("Unable to detect Drupal at $drupalRoot");
}

/**
 * Finds all valid extension directories recursively within a given directory.
 *
 * @param string $scan_directory
 *   The directory that should be recursively scanned.
 *
 * @return array
 *   An associative array of extension directories found within the scanned
 *   directory, keyed by extension name.
 */
function drupal_phpunit_find_extension_directories($scan_directory) {
  $extensions = [];
  $dirs = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($scan_directory, \RecursiveDirectoryIterator::FOLLOW_SYMLINKS));
  foreach ($dirs as $dir) {
    if (strpos($dir->getPathname(), '.info.yml') !== FALSE) {
      // Cut off ".info.yml" from the filename for use as the extension name. We
      // use getRealPath() so that we can scan extensions represented by
      // directory aliases.
      $extensions[substr($dir->getFilename(), 0, -9)] = $dir->getPathInfo()
        ->getRealPath();
    }
  }
  return $extensions;
}

/**
 * Returns directories under which contributed extensions may exist.
 *
 * @param string $root
 *   (optional) Path to the root of the Drupal installation.
 *
 * @return array
 *   An array of directories under which contributed extensions may exist.
 */
function drupal_phpunit_contrib_extension_directory_roots($root) {
  $paths = [
    $root . '/core/modules',
    $root . '/core/profiles',
    $root . '/modules',
    $root . '/profiles',
    $root . '/themes',
  ];
  $sites_path = $root . '/sites';
  // Note this also checks sites/../modules and sites/../profiles.
  foreach (scandir($sites_path) as $site) {
    if ($site[0] === '.' || $site === 'simpletest') {
      continue;
    }
    $path = "$sites_path/$site";
    $paths[] = is_dir("$path/modules") ? realpath("$path/modules") : NULL;
    $paths[] = is_dir("$path/profiles") ? realpath("$path/profiles") : NULL;
    $paths[] = is_dir("$path/themes") ? realpath("$path/themes") : NULL;
  }
  return array_filter(array_filter($paths), 'file_exists');
}

/**
 * Registers the namespace for each extension directory with the autoloader.
 *
 * @param array $dirs
 *   An associative array of extension directories, keyed by extension name.
 *
 * @return array
 *   An associative array of extension directories, keyed by their namespace.
 */
function drupal_phpunit_get_extension_namespaces($dirs) {
  $suite_names = ['Unit', 'Kernel', 'Functional', 'Build', 'FunctionalJavascript'];
  $namespaces = [];
  foreach ($dirs as $extension => $dir) {
    if (is_dir($dir . '/src')) {
      // Register the PSR-4 directory for module-provided classes.
      $namespaces['Drupal\\' . $extension . '\\'][] = $dir . '/src';
    }
    $test_dir = $dir . '/tests/src';
    if (is_dir($test_dir)) {
      foreach ($suite_names as $suite_name) {
        $suite_dir = $test_dir . '/' . $suite_name;
        if (is_dir($suite_dir)) {
          // Register the PSR-4 directory for PHPUnit-based suites.
          $namespaces['Drupal\\Tests\\' . $extension . '\\' . $suite_name . '\\'][] = $suite_dir;
        }
      }
      // Extensions can have a \Drupal\extension\Traits namespace for
      // cross-suite trait code.
      $trait_dir = $test_dir . '/Traits';
      if (is_dir($trait_dir)) {
        $namespaces['Drupal\\Tests\\' . $extension . '\\Traits\\'][] = $trait_dir;
      }
    }
  }
  return $namespaces;
}


/**
 * Populate class loader with additional namespaces for tests.
 *
 * We run this in a function to avoid setting the class loader to a global
 * that can change. This change can cause unpredictable false positives for
 * phpunit's global state change watcher. The class loader can be retrieved from
 * composer at any time by requiring autoload.php.
 */
function drupal_phpunit_populate_class_loader($drupalRoot, $vendorRoot) {

  /** @var \Composer\Autoload\ClassLoader $loader */
  $loader = require $vendorRoot . '/autoload.php';

  // Start with classes in known locations.
  $loader->add('Drupal\\BuildTests', $drupalRoot . '/core/tests');
  $loader->add('Drupal\\Tests', $drupalRoot . '/core/tests');
  $loader->add('Drupal\\TestSite', $drupalRoot . '/core/tests');
  $loader->add('Drupal\\KernelTests', $drupalRoot . '/core/tests');
  $loader->add('Drupal\\FunctionalTests', $drupalRoot . '/core/tests');
  $loader->add('Drupal\\FunctionalJavascriptTests', $drupalRoot . '/core/tests');
  $loader->add('Drupal\\TestTools', $drupalRoot . '/core/tests');

  if (!isset($GLOBALS['namespaces'])) {
    // Scan for arbitrary extension namespaces from core and contrib.
    $extension_roots = drupal_phpunit_contrib_extension_directory_roots($drupalRoot);

    $dirs = array_map('drupal_phpunit_find_extension_directories', $extension_roots);
    $dirs = array_reduce($dirs, 'array_merge', []);
    $GLOBALS['namespaces'] = drupal_phpunit_get_extension_namespaces($dirs);
  }
  foreach ($GLOBALS['namespaces'] as $prefix => $paths) {
    $loader->addPsr4($prefix, $paths);
  }

  return $loader;
}

// Do class loader population.
drupal_phpunit_populate_class_loader($drupalRoot, $drupalVendorRoot);

$autoloader = require $drupalVendorRoot . '/autoload.php';
if ($autoloader instanceof \Composer\Autoload\ClassLoader) {
    if (interface_exists(\PHPUnit\Framework\Test::class)
        && class_exists('Drupal\TestTools\PhpUnitCompatibility\PhpUnit8\ClassWriter')) {
        \Drupal\TestTools\PhpUnitCompatibility\PhpUnit8\ClassWriter::mutateTestBase($autoloader);
    }
}
