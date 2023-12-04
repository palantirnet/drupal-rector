<?php

use Drupal\Tests\field\Traits\EntityReferenceTestTrait;
use Drupal\KernelTests\AssertContentTrait;

class EntityReferenceItemTest extends FieldKernelTestBase {

    use AssertContentTrait;

    use EntityReferenceTestTrait;
}

?>
-----
<?php

use Drupal\Tests\field\Traits\EntityReferenceFieldCreationTrait;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;

class EntityReferenceItemTest extends FieldKernelTestBase {

    use EntityReferenceFieldCreationTrait;

}

?>
