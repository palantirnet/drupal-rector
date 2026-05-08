<?php

$ext = twig_extension();
$output = twig_render_template($template_file, $variables);
?>
-----
<?php

$ext = '.html.twig';
$output = \Drupal::service(\Drupal\Core\Template\TwigThemeEngine::class)->renderTemplate($template_file, $variables);
?>
