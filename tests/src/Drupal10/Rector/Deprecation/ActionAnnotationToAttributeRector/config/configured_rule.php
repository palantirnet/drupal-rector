<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\AnnotationToAttributeRector;
use DrupalRector\Drupal10\Rector\ValueObject\AnnotationToAttributeConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AnnotationToAttributeRector::class, $rectorConfig, false, [
        new AnnotationToAttributeConfiguration('10.2.0', '12.0.0', 'Action', 'Drupal\Core\Action\Attribute\Action'),
        new AnnotationToAttributeConfiguration('10.2.0', '12.0.0', 'Block', 'Drupal\Core\Action\Attribute\Block'),
        new AnnotationToAttributeConfiguration('10.2.0', '12.0.0', 'ContentEntityType', 'Drupal\Core\Entity\Attribute\ContentEntityType'),
    ]);
};
