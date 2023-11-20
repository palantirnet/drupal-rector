<?php

function testExample() {
    $modules = ['system'];
    uasort($modules, 'system_sort_by_info_name');
}
?>
-----
<?php

function testExample() {
    $modules = ['system'];
    uasort($modules, [\Drupal\Core\Extension\ExtensionList::class, 'sortByName']);
}
?>
