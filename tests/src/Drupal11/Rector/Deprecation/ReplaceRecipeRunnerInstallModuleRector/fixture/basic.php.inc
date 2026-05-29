<?php

use Drupal\Core\Recipe\RecipeRunner;

RecipeRunner::installModule($module, $recipeConfigStorage, $context);
RecipeRunner::installModules([$module], $recipeConfigStorage, $context);

?>
-----
<?php

use Drupal\Core\Recipe\RecipeRunner;

\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => RecipeRunner::installModules([$module], $recipeConfigStorage, $context), fn() => RecipeRunner::installModule($module, $recipeConfigStorage, $context));
RecipeRunner::installModules([$module], $recipeConfigStorage, $context);

?>
