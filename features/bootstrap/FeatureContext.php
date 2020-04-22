<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context {

  /**
   * The path to the test file.
   *
   * @var string
   */
  protected $testFilePath = 'features/tmp/test-file.php';

  /**
   * The test file contents.
   *
   * @var string
   */
  protected $testFile;

  /**
   * The original file that will be tested and copied.
   *
   * @var string
   */
  protected $originalFilePath;

  /**
   * The Drupal Rector path.
   *
   * @var string
   */
  protected $drupalRectorPath = 'drupal-rector/';

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
   * @Given I create a test file from :file
   */
  public function iCreateATestFileFrom($file_path) {
    $this->originalFilePath = $file_path;

    copy ( $this->originalFilePath , $this->testFilePath );
  }

  /**
   * @When I run Drupal Rector on the test file
   */
  public function iRunDrupalRectorOnTheTestFile() {
    chdir('..');

    $output = NULL;
    $return_value = NULL;
    exec('vendor/bin/rector process ' . $this->drupalRectorPath . $this->testFilePath, $output, $return_value);

    chdir($this->drupalRectorPath);

    if ($return_value !== 0) {
      throw new Exception('Rector did not complete successfully.' . PHP_EOL
        . 'Rector output:'
        . PHP_EOL . join(PHP_EOL, $output)
      );
    }
  }

  /**
   * @When I examine the test file
   */
  public function iExamineTheTestFile() {
    $this->testFile = file_get_contents($this->testFilePath);
  }

  /**
   * @Then the test file matches :file_path
   */
  public function theTestFileMatches($file_path) {
    $expected_updated_file = file_get_contents($file_path);

    if (is_null($this->testFile)) {
      $this->iExamineTheTestFile();
    }

    if ($this->testFile !== $expected_updated_file) {
      throw new Exception('The test file did not match the expected updated file.' . PHP_EOL
        . 'Expected:' . PHP_EOL
        . $this->testFile . PHP_EOL
        . 'Actual:' . PHP_EOL
        . $expected_updated_file . PHP_EOL
      );
    }
  }
}
