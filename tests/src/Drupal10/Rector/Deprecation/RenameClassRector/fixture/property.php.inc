<?php

use Drupal\Tests\field\Traits\EntityReferenceTestTrait;

class EntityReferenceItemTest extends FieldKernelTestBase {

    protected \Drupal\OldClass $class;

}

?>
-----
<?php

use Drupal\Tests\field\Traits\EntityReferenceFieldCreationTrait;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;

class EntityReferenceItemTest extends FieldKernelTestBase {

    protected \Drupal\NewClass $class;

}
