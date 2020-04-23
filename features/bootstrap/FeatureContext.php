<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 *
 * IMPORTANT: This will only work on Linux / MacOS, because it uses commands that we aren't going to rewrite right now such as copying files and folders and looking at the difference between them.
 */
class FeatureContext implements Context {

  /**
   * Temporary folder path.
   *
   * @var string
   */
  protected $temporaryFolderPath = 'features/tmp';

  /**
   * The Drupal Rector path.
   *
   * @var string
   */
  protected $drupalRectorPath = 'drupal-rector/';

  /**
   * The path to the test file or folder.
   *
   * @var string
   */
  protected $testPath;

  /**
   * The original file that will be tested and copied.
   *
   * @var string
   */
  protected $originalFilePath;

  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct() {
  }

  /**
   * @When I run Drupal Rector on the test file/folder
   */
  public function iRunDrupalRectorOnTheTest() {
    $this->iRunDrupalRectorOnThe("$this->temporaryFolderPath/$this->testPath");
  }

  /**
   * @When I run Drupal Rector on the file/folder :path
   */
  public function iRunDrupalRectorOnThe($path) {
    chdir('..');

    $output = NULL;
    $return_value = NULL;
    exec("vendor/bin/rector process $this->drupalRectorPath/$path", $output, $return_value);

    chdir($this->drupalRectorPath);

    if ($return_value !== 0) {
      throw new Exception('Rector did not complete successfully.' . PHP_EOL
        . 'Rector output:' . PHP_EOL
        . join(PHP_EOL, $output)
      );
    }
  }

  /**
   * @Given I create a test copy of the file/folder :path
   */
  public function iCreateATestCopyOfThe($path) {
    $this->testPath = $path;

    // Using Linux / MacOS commands, because they are simple and work well.
    exec("rm -rf $this->temporaryFolderPath/$this->testPath");

    // Make any necessary folders.
    $path_parts = explode('/', $this->testPath);
    if (count($path_parts) > 1) {
      $directory_parts = $path_parts;
      array_pop($directory_parts);
      $directory = implode('/', $directory_parts);

      exec("mkdir -p $this->temporaryFolderPath/$directory");
    }

    exec("cp -R $this->testPath $this->temporaryFolderPath/$this->testPath");
  }

  /**
   * @Then the test file/folder matches :path
   */
  public function theTestMatches($path) {
    // Using Linux / MacOS commands, because they are simple and work well.
    $output = NULL;
    $return_value = NULL;
    /*
     * -r: recursive
     * -u: show the joined context, like git diff
     * -b: ignore whitespace
     * -B: ignore lines that are only whitespace
     */
    exec("diff -rubB $path $this->temporaryFolderPath/$this->testPath", $output, $return_value);

    if ($return_value !== 0) {
      throw new Exception('The test does not match.' . PHP_EOL . PHP_EOL
        . join(PHP_EOL, $output) . PHP_EOL . PHP_EOL
      );
    }
  }
}
