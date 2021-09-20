<?php

namespace Drupal\Tests\rector_examples\Functional;

use Drupal\Tests\BrowserTestBase;

class BuildXpathQueryTest extends BrowserTestBase {

    public function testExample() {
        $xpath = $this->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
        $fields = $this->xpath($xpath);
    }

}
