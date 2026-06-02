<?php

use Drupal\Tests\BrowserTestBase;

// Strings containing ':' that are NOT well-formed `Name: value` HTTP header
// lines must be left alone. The rector only rewrites integer-keyed entries
// where (a) the string contains the conventional `: ` (colon-space)
// separator AND (b) the pre-`:` name matches `[A-Za-z][A-Za-z0-9-]*`.
class DrupalGetHeadersNoChangeTest extends BrowserTestBase {

    // URL-shaped string: separator is `://`, not `: `.
    public function testUrlInHeadersArray() {
        $this->drupalGet('/p', [], ['http://example.com/foo']);
    }

    // Path with query: no colon at all.
    public function testPathWithQuery() {
        $this->drupalGet('/p', [], ['/path?key=value']);
    }

    // Empty name part: contains `: ` but pre-colon segment is empty.
    public function testEmptyNamePart() {
        $this->drupalGet('/p', [], [': bar']);
    }

    // Name contains spaces (not a valid header token).
    public function testNamePartWithSpace() {
        $this->drupalGet('/p', [], ['Not A Header: value']);
    }

    // Colon without the conventional trailing space.
    public function testNoSpaceAfterColon() {
        $this->drupalGet('/p', [], ['Foo:bar']);
    }

    // Compact header form without space — also skipped.
    public function testCompactHeader() {
        $this->drupalGet('/p', [], ['X-Header:value']);
    }

    // Header name starts with a digit — not a valid token start.
    public function testDigitStartedName() {
        $this->drupalGet('/p', [], ['123Header: value']);
    }

    // Header name uses underscore — disallowed by the conservative pattern.
    public function testUnderscoreInName() {
        $this->drupalGet('/p', [], ['X_Custom: value']);
    }

    // Trailing colon with empty value, no space.
    public function testTrailingColonNoSpace() {
        $this->drupalGet('/p', [], ['Foo:']);
    }
}
?>
-----
<?php

use Drupal\Tests\BrowserTestBase;

// Strings containing ':' that are NOT well-formed `Name: value` HTTP header
// lines must be left alone. The rector only rewrites integer-keyed entries
// where (a) the string contains the conventional `: ` (colon-space)
// separator AND (b) the pre-`:` name matches `[A-Za-z][A-Za-z0-9-]*`.
class DrupalGetHeadersNoChangeTest extends BrowserTestBase {

    // URL-shaped string: separator is `://`, not `: `.
    public function testUrlInHeadersArray() {
        $this->drupalGet('/p', [], ['http://example.com/foo']);
    }

    // Path with query: no colon at all.
    public function testPathWithQuery() {
        $this->drupalGet('/p', [], ['/path?key=value']);
    }

    // Empty name part: contains `: ` but pre-colon segment is empty.
    public function testEmptyNamePart() {
        $this->drupalGet('/p', [], [': bar']);
    }

    // Name contains spaces (not a valid header token).
    public function testNamePartWithSpace() {
        $this->drupalGet('/p', [], ['Not A Header: value']);
    }

    // Colon without the conventional trailing space.
    public function testNoSpaceAfterColon() {
        $this->drupalGet('/p', [], ['Foo:bar']);
    }

    // Compact header form without space — also skipped.
    public function testCompactHeader() {
        $this->drupalGet('/p', [], ['X-Header:value']);
    }

    // Header name starts with a digit — not a valid token start.
    public function testDigitStartedName() {
        $this->drupalGet('/p', [], ['123Header: value']);
    }

    // Header name uses underscore — disallowed by the conservative pattern.
    public function testUnderscoreInName() {
        $this->drupalGet('/p', [], ['X_Custom: value']);
    }

    // Trailing colon with empty value, no space.
    public function testTrailingColonNoSpace() {
        $this->drupalGet('/p', [], ['Foo:']);
    }
}
?>
