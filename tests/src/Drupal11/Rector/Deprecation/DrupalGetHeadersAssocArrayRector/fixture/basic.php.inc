<?php

use Drupal\Tests\BrowserTestBase;

class DrupalGetHeadersTest extends BrowserTestBase {

    /**
     * Tests integer-keyed colon-separated header strings.
     */
    public function testIndexedHeader() {
        $this->drupalGet('/path', [], ['X-Requested-With: XMLHttpRequest']);
    }

    /**
     * Tests null header values.
     */
    public function testNullHeader() {
        $this->drupalGet('', [], ['Accept-Language' => NULL]);
    }

    /**
     * Tests a mix of indexed and null headers in one call.
     */
    public function testMixed() {
        $this->drupalGet('/p', [], ['X-Foo: bar', 'Accept-Language' => NULL, 'X-Already' => 'ok']);
    }

    /**
     * Tests a header value that itself contains a colon (e.g. Authorization).
     */
    public function testColonInValue() {
        $this->drupalGet('/p', [], ['Authorization: Bearer abc:def']);
    }
}
?>
-----
<?php

use Drupal\Tests\BrowserTestBase;

class DrupalGetHeadersTest extends BrowserTestBase {

    /**
     * Tests integer-keyed colon-separated header strings.
     */
    public function testIndexedHeader() {
        $this->drupalGet('/path', [], ['X-Requested-With' => 'XMLHttpRequest']);
    }

    /**
     * Tests null header values.
     */
    public function testNullHeader() {
        $this->drupalGet('', [], ['Accept-Language' => '']);
    }

    /**
     * Tests a mix of indexed and null headers in one call.
     */
    public function testMixed() {
        $this->drupalGet('/p', [], ['X-Foo' => 'bar', 'Accept-Language' => '', 'X-Already' => 'ok']);
    }

    /**
     * Tests a header value that itself contains a colon (e.g. Authorization).
     */
    public function testColonInValue() {
        $this->drupalGet('/p', [], ['Authorization' => 'Bearer abc:def']);
    }
}
?>
