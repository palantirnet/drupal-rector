<?php

$output = twig_render_template($template_file, $variables);
?>
-----
<?php

$output = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service('Drupal\Core\Template\TwigThemeEngine')->renderTemplate($template_file, $variables), fn() => twig_render_template($template_file, $variables));
?>
