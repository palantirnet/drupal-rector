<?php

use Drupal\views\Plugin\views\HandlerBase;

class MyHandlerWithBody extends HandlerBase
{
    public function defineExtraOptions(&$option)
    {
        $option['key_a'] = ['default' => 'value_a'];
        $option['key_b'] = ['default' => 0];
        $option['key_c'] = ['default' => FALSE];
    }
}

?>
-----
<?php

use Drupal\views\Plugin\views\HandlerBase;

class MyHandlerWithBody extends HandlerBase
{
}

?>
