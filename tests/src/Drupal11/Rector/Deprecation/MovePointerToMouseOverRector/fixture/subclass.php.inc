<?php

class MyLayoutTest extends \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest
{
    public function testSomething(): void
    {
        $this->movePointerTo('#my-element');
    }
}
?>
-----
<?php

class MyLayoutTest extends \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest
{
    public function testSomething(): void
    {
        $this->getSession()->getDriver()->mouseOver('.//*[@id="my-element"]');
    }
}
?>
