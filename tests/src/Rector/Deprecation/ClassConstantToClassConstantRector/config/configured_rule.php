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
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'FORM_SEPARATE_PAGE',
            'Drupal\comment\FormLocation',
            'SeparatePage',
            '11.4.0',
        ),
        // https://www.drupal.org/node/3151009 (Drupal 9.1)
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_NAME',
            '9.1.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
            'Drupal\Core\Routing\RouteObjectInterface',
            'ROUTE_OBJECT',
            '9.1.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Symfony\Cmf\Component\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
            'Drupal\Core\Routing\RouteObjectInterface',
            'CONTROLLER_NAME',
            '9.1.0',
        ),
        // https://www.drupal.org/node/3574661 (Drupal 11.4)
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'HIDDEN',
            'Drupal\comment\CommentingStatus',
            'Hidden',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'CLOSED',
            'Drupal\comment\CommentingStatus',
            'Closed',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\Plugin\Field\FieldType\CommentItemInterface',
            'OPEN',
            'Drupal\comment\CommentingStatus',
            'Open',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAYNOT_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Forbidden',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MAY_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Allowed',
            '11.4.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\comment\CommentInterface',
            'ANONYMOUS_MUST_CONTACT',
            'Drupal\comment\AnonymousContact',
            'Required',
            '11.4.0',
        ),
        // https://www.drupal.org/node/3575575 (Drupal 10.3)
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_RENAME',
            'Drupal\Core\File\FileExists',
            'Rename',
            '10.3.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_REPLACE',
            'Drupal\Core\File\FileExists',
            'Replace',
            '10.3.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_ERROR',
            'Drupal\Core\File\FileExists',
            'Error',
            '10.3.0',
        ),
        // https://www.drupal.org/node/3575841 (Drupal 11.2)
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_OK',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'OK',
            '11.2.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_WARNING',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Warning',
            '11.2.0',
        ),
        new ClassConstantToClassConstantConfiguration(
            'Drupal\system\SystemManager',
            'REQUIREMENT_ERROR',
            'Drupal\Core\Extension\Requirement\RequirementSeverity',
            'Error',
            '11.2.0',
        ),
    ]);
};
