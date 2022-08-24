<?php

namespace Rector\Tests\DrupalRector\Rector\FuncCall\ModuleLoadInstallRector\Fixture;

class SomeClass
{
    public function run()
    {
        module_load_install('node');
    }
}

?>
-----
<?php

namespace Rector\Tests\DrupalRector\Rector\FuncCall\ModuleLoadInstallRector\Fixture;

class SomeClass
{
    public function run()
    {
        \Drupal::moduleHandler()->loadInclude('node', 'install');
    }
}

?>
