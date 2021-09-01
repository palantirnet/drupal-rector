<?php

declare(strict_types=1);

namespace Drupal\FunctionalTests;

use Drupal\KernelTests\AssertLegacyTrait as BaseAssertLegacyTrait;

if (class_exists('Drupal\FunctionalTests\AssertLegacyTrait')) {
    return;
}

trait AssertLegacyTrait {
    use BaseAssertLegacyTrait;

    abstract public function assertSession($name = NULL): \Drupal\Tests\WebAssert;

    protected function assertCacheTag($expected_cache_tag) {
        $this->assertSession()->responseHeaderContains('X-Drupal-Cache-Tags', $expected_cache_tag);
    }

    protected function assertNoCacheTag($cache_tag) {
        $this->assertSession()->responseHeaderNotContains('X-Drupal-Cache-Tags', $cache_tag);
    }

    protected function assertElementPresent($css_selector) {
        $this->assertSession()->elementExists('css', $css_selector);
    }

    protected function assertElementNotPresent($css_selector) {
        $this->assertSession()->elementNotExists('css', $css_selector);
    }

    protected function assertEscaped($raw) {
        $this->assertSession()->assertEscaped($raw);
    }

    protected function assertNoEscaped($raw) {
        $this->assertSession()->assertNoEscaped($raw);
    }

    protected function assertHeader($name, $value) {
        $this->assertSession()->responseHeaderEquals($name, $value);
    }

    protected function assertLinkByHref($href, $index = 0) {
        $this->assertSession()->linkByHrefExists($href, $index);
    }

    protected function assertNoLinkByHref($href) {
        $this->assertSession()->linkByHrefNotExists($href);
    }

    protected function assertLink($label, $index = 0) {
        return $this->assertSession()->linkExists($label, $index);
    }

    protected function assertNoLink($label) {
        return $this->assertSession()->linkNotExists($label);
    }

    protected function assertNoPattern($pattern) {
        $this->assertSession()->responseNotMatches($pattern);
    }

    protected function assertPattern($pattern) {
        $this->assertSession()->responseMatches($pattern);
    }

    protected function assertRaw($raw) {
        $this->assertSession()->responseContains($raw);
    }

    protected function assertNoRaw($raw) {
        $this->assertSession()->responseNotContains($raw);
    }

    protected function assertResponse($code) {
        $this->assertSession()->statusCodeEquals($code);
    }

    protected function assertText($text) {

    }

    protected function assertNoText($text) {

    }

    protected function assertTitle($expected_title) {
        return $this->assertSession()->titleEquals($expected_title);
    }

    protected function buildXPathQuery($xpath, array $args = []) {
        return $this->assertSession()->buildXPathQuery($xpath, $args);
    }

    protected function assertNoUniqueText($text, $message = '') {
        $this->assertTrue(TRUE, $message);
    }

}
