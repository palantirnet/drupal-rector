<?php

// addModule() on an unrelated class is not removed.
/** @var \SomeOtherManager $manager */
$manager->addModule('mymodule', 'modules/mymodule');
$manager->addProfile('standard', 'core/profiles/standard');
?>
-----
<?php

// addModule() on an unrelated class is not removed.
/** @var \SomeOtherManager $manager */
$manager->addModule('mymodule', 'modules/mymodule');
$manager->addProfile('standard', 'core/profiles/standard');
?>
