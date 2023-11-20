<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $edit = [
            'aggregator_allowed_html_tags' => '<a>',
            'aggregator_summary_items' => 10,
            'aggregator_clear' => 3600,
            'aggregator_teaser_length' => 200,
            'aggregator_fetcher' => 'aggregator_test_fetcher',
            'aggregator_parser' => 'aggregator_test_parser',
            'aggregator_processors[aggregator_test_processor]' => 'aggregator_test_processor',
        ];
        foreach ($edit as $name => $value) {
            $this->assertFieldByName($name, $value, new FormattableMarkup('"@name" has correct default value.', ['@name' => $name]));
        }
    }
}
?>
-----
<?php

use Drupal\Tests\BrowserTestBase;

class BrowserTestBaseGetMock extends BrowserTestBase {

    /**
     * A simple example using the class property.
     */
    public function example() {
        $edit = [
            'aggregator_allowed_html_tags' => '<a>',
            'aggregator_summary_items' => 10,
            'aggregator_clear' => 3600,
            'aggregator_teaser_length' => 200,
            'aggregator_fetcher' => 'aggregator_test_fetcher',
            'aggregator_parser' => 'aggregator_test_parser',
            'aggregator_processors[aggregator_test_processor]' => 'aggregator_test_processor',
        ];
        foreach ($edit as $name => $value) {
            $this->assertSession()->fieldValueEquals($name, $value);
        }
    }
}
?>
