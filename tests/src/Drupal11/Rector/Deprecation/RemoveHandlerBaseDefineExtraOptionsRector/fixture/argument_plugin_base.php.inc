<?php

use Drupal\views\Plugin\views\argument\ArgumentPluginBase;

class MyArgument extends ArgumentPluginBase
{
    public function defineExtraOptions(&$option)
    {
        $option['argument_key'] = ['default' => ''];
    }

    public function title()
    {
        return 'My Title';
    }
}

?>
-----
<?php

use Drupal\views\Plugin\views\argument\ArgumentPluginBase;

class MyArgument extends ArgumentPluginBase
{
    public function title()
    {
        return 'My Title';
    }
}

?>
