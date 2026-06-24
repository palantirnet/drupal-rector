<?php

function example(&$variables) {
    template_preprocess_table($variables);
    template_preprocess_tablesort_indicator($variables);
    template_preprocess_item_list($variables);
    template_preprocess_region($variables);
    template_preprocess_maintenance_page($variables);
    template_preprocess_maintenance_task_list($variables);
    template_preprocess_install_page($variables);
    template_preprocess_image($variables);
    template_preprocess_breadcrumb($variables);
    template_preprocess_pager($variables);
    template_preprocess_field($variables);
    template_preprocess_field_multiple_value_form($variables);
    template_preprocess_menu_local_task($variables);
    template_preprocess_menu_local_action($variables);
}
?>
-----
<?php

function example(&$variables) {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessTable($variables);
    }, function () use (&$variables) {
        template_preprocess_table($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessTablesortIndicator($variables);
    }, function () use (&$variables) {
        template_preprocess_tablesort_indicator($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessItemList($variables);
    }, function () use (&$variables) {
        template_preprocess_item_list($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessRegion($variables);
    }, function () use (&$variables) {
        template_preprocess_region($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessMaintenancePage($variables);
    }, function () use (&$variables) {
        template_preprocess_maintenance_page($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessMaintenanceTaskList($variables);
    }, function () use (&$variables) {
        template_preprocess_maintenance_task_list($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ThemePreprocess')->preprocessInstallPage($variables);
    }, function () use (&$variables) {
        template_preprocess_install_page($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Theme\ImagePreprocess')->preprocessImage($variables);
    }, function () use (&$variables) {
        template_preprocess_image($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Breadcrumb\BreadcrumbPreprocess')->preprocessBreadcrumb($variables);
    }, function () use (&$variables) {
        template_preprocess_breadcrumb($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Pager\PagerPreprocess')->preprocessPager($variables);
    }, function () use (&$variables) {
        template_preprocess_pager($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Field\FieldPreprocess')->preprocessField($variables);
    }, function () use (&$variables) {
        template_preprocess_field($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Field\FieldPreprocess')->preprocessFieldMultipleValueForm($variables);
    }, function () use (&$variables) {
        template_preprocess_field_multiple_value_form($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Menu\MenuPreprocess')->preprocessMenuLocalTask($variables);
    }, function () use (&$variables) {
        template_preprocess_menu_local_task($variables);
    });
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', function () use (&$variables) {
        \Drupal::service('Drupal\Core\Menu\MenuPreprocess')->preprocessMenuLocalAction($variables);
    }, function () use (&$variables) {
        template_preprocess_menu_local_action($variables);
    });
}
?>
