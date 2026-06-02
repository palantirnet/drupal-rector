<?php

use Drupal\Core\Entity\Entity\EntityFormMode;

function create_short() {
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => '',
        'targetEntityType' => 'user',
    ]);
}

function create_fqcn() {
    \Drupal\Core\Entity\Entity\EntityFormMode::create([
        'id' => 'node.preview',
        'label' => 'Preview',
        'description' => '',
        'targetEntityType' => 'node',
    ]);
}

function create_only_label() {
    EntityFormMode::create([
        'id' => 'taxonomy_term.simple',
        'label' => 'Simple',
        'description' => '',
        'targetEntityType' => 'taxonomy_term',
    ]);
}
?>
-----
<?php

use Drupal\Core\Entity\Entity\EntityFormMode;

function create_short() {
    EntityFormMode::create([
        'id' => 'user.test',
        'label' => 'Test',
        'description' => NULL,
        'targetEntityType' => 'user',
    ]);
}

function create_fqcn() {
    \Drupal\Core\Entity\Entity\EntityFormMode::create([
        'id' => 'node.preview',
        'label' => 'Preview',
        'description' => NULL,
        'targetEntityType' => 'node',
    ]);
}

function create_only_label() {
    EntityFormMode::create([
        'id' => 'taxonomy_term.simple',
        'label' => 'Simple',
        'description' => NULL,
        'targetEntityType' => 'taxonomy_term',
    ]);
}
?>
