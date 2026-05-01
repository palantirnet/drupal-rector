<?php

use Drupal\Core\Recipe\RecipeRunner;

class MyRunner extends RecipeRunner
{
    public static function run(string $module, $storage, ?array &$context): void
    {
        self::installModule($module, $storage, $context);
        static::installModule($module, $storage, $context);
    }
}

?>
-----
<?php

use Drupal\Core\Recipe\RecipeRunner;

class MyRunner extends RecipeRunner
{
    public static function run(string $module, $storage, ?array &$context): void
    {
        self::installModules([$module], $storage, $context);
        static::installModules([$module], $storage, $context);
    }
}

?>
