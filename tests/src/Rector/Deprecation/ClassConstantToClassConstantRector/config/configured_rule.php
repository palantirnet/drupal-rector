<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\ClassConstantToClassConstantRector;
use DrupalRector\Rector\ValueObject\ClassConstantToClassConstantConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ClassConstantToClassConstantRector::class, $rectorConfig, false, [
        // https://www.drupal.org/node/3550054 (Drupal 11.4)
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_BELOW',
            'Drupal\comment\FormLocation',
            'Below',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_SEPARATE_PAGE',
            'Drupal\comment\FormLocation',
            'SeparatePage',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_NAME',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
        ),
    ]);
};
