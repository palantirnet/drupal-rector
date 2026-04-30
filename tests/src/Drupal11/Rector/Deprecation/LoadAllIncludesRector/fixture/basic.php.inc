<?php

$this->moduleHandler->loadAllIncludes('install');
$this->moduleHandler->loadAllIncludes('inc', 'admin');
?>
-----
<?php

foreach ($this->moduleHandler->getModuleList() as $module => $filename) {
    $this->moduleHandler->loadInclude($module, 'install');
}
foreach ($this->moduleHandler->getModuleList() as $module => $filename) {
    $this->moduleHandler->loadInclude($module, 'inc', 'admin');
}
?>
