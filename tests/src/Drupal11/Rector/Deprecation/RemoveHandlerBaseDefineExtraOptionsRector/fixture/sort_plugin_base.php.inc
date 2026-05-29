<?php

use Drupal\views\Plugin\views\sort\SortPluginBase;

class MySort extends SortPluginBase
{
    public function defineExtraOptions(&$option)
    {
        $option['sort_key'] = ['default' => 'ASC'];
    }
}

?>
-----
<?php

use Drupal\views\Plugin\views\sort\SortPluginBase;

class MySort extends SortPluginBase
{
}

?>
