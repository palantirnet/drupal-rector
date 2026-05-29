<?php

$fallback = filter_fallback_format();
$all = filter_formats();
$for_account = filter_formats($account);
$roles = filter_get_roles_by_format($format);
$formats = filter_get_formats_by_role($rid);
$default_id = filter_default_format();
$default_id_account = filter_default_format($account);
?>
-----
<?php

$fallback = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getFallbackFormatId(), fn() => filter_fallback_format());
$all = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getAllFormats(), fn() => filter_formats());
$for_account = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getFormatsForAccount($account), fn() => filter_formats($account));
$roles = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $format->getRoles(), fn() => filter_get_roles_by_format($format));
$formats = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getFormatsByRole($rid), fn() => filter_get_formats_by_role($rid));
$default_id = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getDefaultFormat()->id(), fn() => filter_default_format());
$default_id_account = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\filter\FilterFormatRepositoryInterface::class)->getDefaultFormat($account)->id(), fn() => filter_default_format($account));
?>
