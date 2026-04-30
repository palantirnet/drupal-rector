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
        // https://www.drupal.org/node/3574661 (Drupal 11.4)
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'HIDDEN',
            'Drupal\comment\CommentingStatus',
            'Hidden',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'CLOSED',
            'Drupal\comment\CommentingStatus',
            'Closed',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'OPEN',
            'Drupal\comment\CommentingStatus',
            'Open',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAYNOT_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Forbidden',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAY_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Allowed',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MUST_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Required',
        ),
        // https://www.drupal.org/node/3575575 (Drupal 10.3)
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_RENAME',
            'Drupal\Core\File\FileExists',
            'Rename',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_REPLACE',
            'Drupal\Core\File\FileExists',
            'Replace',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_ERROR',
            'Drupal\Core\File\FileExists',
            'Error',
        ),
        // https://www.drupal.org/node/3575841 (Drupal 11.2)
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_OK',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'OK',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_WARNING',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Warning',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_ERROR',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Error',
        ),
    ]);
};
