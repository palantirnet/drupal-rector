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
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => self::installModules([$module], $storage, $context), fn() => self::installModule($module, $storage, $context));
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => static::installModules([$module], $storage, $context), fn() => static::installModule($module, $storage, $context));
    }
}

?>
