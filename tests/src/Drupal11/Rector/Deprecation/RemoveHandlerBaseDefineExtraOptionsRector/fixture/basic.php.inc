<?php

use Drupal\views\Plugin\views\HandlerBase;

class MyViewsFilter extends HandlerBase
{
    public function defineExtraOptions(&$option)
    {
        $option['my_key'] = ['default' => 'value'];
    }

    public function query()
    {
        // Keep this.
    }
}

?>
-----
<?php

use Drupal\views\Plugin\views\HandlerBase;

class MyViewsFilter extends HandlerBase
{
    public function query()
    {
        // Keep this.
    }
}

?>
