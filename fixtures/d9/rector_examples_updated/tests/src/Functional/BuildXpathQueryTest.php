<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class BuildXpathQueryTest extends BrowserTestBase {

    /**
     * {@inheritdoc}
     */
    protected $defaultTheme = 'stark';

    public function testExample() {
        $xpath = $this->assertSession()->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
        $fields = $this->xpath($xpath);
    }

}
