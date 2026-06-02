<?php

use Drupal\Core\Entity\Entity\EntityFormMode;
use Drupal\Core\Entity\Entity\EntityViewMode;

function unrelated_class_create() {
    // EntityViewMode::create() is a different class — must not be transformed.
    EntityViewMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => '',
        'targetEntityType' => 'user',
    ]);
}

function unrelated_method() {
    // Same class, different method — must not be transformed.
    EntityFormMode::load('user.test');
}

function nonempty_description() {
    // Description has a real value — must not be transformed.
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => 'A real description.',
        'targetEntityType' => 'user',
    ]);
}

function description_already_null() {
    // Already migrated — must not be re-touched.
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => NULL,
        'targetEntityType' => 'user',
    ]);
}

function unrelated_key() {
    // Empty-string elsewhere in the array — must not be transformed.
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => '',
        'targetEntityType' => 'user',
    ]);
}
?>
-----
<?php

use Drupal\Core\Entity\Entity\EntityFormMode;
use Drupal\Core\Entity\Entity\EntityViewMode;

function unrelated_class_create() {
    // EntityViewMode::create() is a different class — must not be transformed.
    EntityViewMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => '',
        'targetEntityType' => 'user',
    ]);
}

function unrelated_method() {
    // Same class, different method — must not be transformed.
    EntityFormMode::load('user.test');
}

function nonempty_description() {
    // Description has a real value — must not be transformed.
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => 'A real description.',
        'targetEntityType' => 'user',
    ]);
}

function description_already_null() {
    // Already migrated — must not be re-touched.
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => NULL,
        'targetEntityType' => 'user',
    ]);
}

function unrelated_key() {
    // Empty-string elsewhere in the array — must not be transformed.
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => '',
        'targetEntityType' => 'user',
    ]);
}
?>
