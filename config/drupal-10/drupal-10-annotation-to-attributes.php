<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\SystemTimeZonesRector;
use DrupalRector\Drupal10\Rector\Deprecation\WatchdogExceptionRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $source = '\Drupal\Core\Annotation\Mail	\Drupal\Core\Mail\Attribute\Mail
\Drupal\image\Annotation\ImageEffect	\Drupal\image\Attribute\ImageEffect
\Drupal\Core\ImageToolkit\Annotation\ImageToolkitOperation	\Drupal\Core\ImageToolkit\Attribute\ImageToolkitOperation
\Drupal\Core\ImageToolkit\Annotation\ImageToolkit	\Drupal\Core\ImageToolkit\Attribute\ImageToolkit
\Drupal\Core\Annotation\QueueWorker	\Drupal\Core\Queue\Attribute\QueueWorker
\Drupal\rest\Annotation\RestResource	\Drupal\rest\Attribute\RestResource
\Drupal\Core\TypedData\Annotation\DataType	\Drupal\Core\TypedData\Attribute\DataType
\Drupal\language\Annotation\LanguageNegotiation	\Drupal\language\Attribute\LanguageNegotiation
\Drupal\workflows\Annotation\WorkflowType	\Drupal\workflows\Attribute\WorkflowType
\Drupal\Core\Field\Annotation\FieldFormatter	\Drupal\Core\Field\Attribute\FieldFormatter
\Drupal\Core\Field\Annotation\FieldWidget	\Drupal\Core\Field\Attribute\FieldWidget
\Drupal\help\Annotation\HelpSection	\Drupal\help\Attribute\HelpSection
\Drupal\Core\Validation\Annotation\Constraint	\Drupal\Core\Validation\Attribute\Constraint
\Drupal\Core\Archiver\Annotation\Archiver	\Drupal\Core\Archiver\Attribute\Archiver
\Drupal\Core\Condition\Annotation\Condition	\Drupal\Core\Condition\Attribute\Condition
\Drupal\layout_builder\Annotation\SectionStorage	\Drupal\layout_builder\Attribute\SectionStorage
\Drupal\views\Annotation\ViewsArgumentValidator	\Drupal\views\Attribute\ViewsArgumentValidator
\Drupal\search\Annotation\SearchPlugin	\Drupal\search\Attribute\Search
\Drupal\views\Annotation\ViewsQuery	\Drupal\views\Attribute\ViewsQuery
\Drupal\views\Annotation\ViewsAccess	\Drupal\views\Attribute\ViewsAccess
\Drupal\views\Annotation\ViewsArea	\Drupal\views\Attribute\ViewsArea
\Drupal\filter\Annotation\Filter	\Drupal\filter\Attribute\Filter
\Drupal\Core\Entity\Annotation\EntityReferenceSelection	Drupal\Core\Entity\Attribute\EntityReferenceSelection
\Drupal\views\Annotation\ViewsCache	\Drupal\views\Attribute\ViewsCache
\Drupal\views\Annotation\ViewsDisplayExtender	\Drupal\views\Attribute\ViewsDisplayExtender
\Drupal\views\Annotation\ViewsExposedForm	\Drupal\views\Attribute\ViewsExposedForm
\Drupal\views\Annotation\ViewsArgument	\Drupal\views\Attribute\ViewsArgument
\Drupal\views\Annotation\ViewsField	\Drupal\views\Attribute\ViewsField
\Drupal\views\Annotation\ViewsFilter	\Drupal\views\Attribute\ViewsFilter
\Drupal\views\Annotation\ViewsRelationship	\Drupal\views\Attribute\ViewsRelationship
\Drupal\views\Annotation\ViewsSort	\Drupal\views\Attribute\ViewsSort
\Drupal\views\Annotation\ViewsJoin	\Drupal\views\Attribute\ViewsJoin
\Drupal\views\Annotation\ViewsPager	\Drupal\views\Attribute\ViewsPager
\Drupal\views\Annotation\ViewsRow	\Drupal\views\Attribute\ViewsRow
\Drupal\views\Annotation\ViewsDisplay	\Drupal\views\Attribute\ViewsDisplay
\Drupal\views\Annotation\ViewsStyle	\Drupal\views\Attribute\ViewsStyle
\Drupal\views\Annotation\ViewsArgumentDefault	\Drupal\views\Attribute\ViewsArgumentDefault
\Drupal\editor\Annotation\Editor	\Drupal\editor\Attribute\Editor
\Drupal\Core\Display\Annotation\DisplayVariant	\Drupal\Core\Display\Attribute\DisplayVariant
\Drupal\Core\Render\Annotation\FormElement	\Drupal\Core\Render\Attribute\FormElement
\Drupal\Core\Render\Annotation\RenderElement	\Drupal\Core\Render\Attribute\RenderElement';

    $lines = explode("\n", $source);
    $configurations = [];
    foreach ($lines as $line) {
        $parts = explode("\t", $line);

        $annotationParts = explode('\\', $parts[0]);
        $annotation = array_pop($annotationParts);
        $attributeClass = $parts[1];
        $configurations[] = new \DrupalRector\Drupal10\Rector\ValueObject\AnnotationToAttributeConfiguration(
            '10.3.0',
            '12.0.0',
            $annotation,
            $attributeClass
        );
    }

    $source1020 = '\Drupal\Core\Annotation\Action	\Drupal\Core\Action\Attribute\Action
\Drupal\Core\Block\Annotation\Block	\Drupal\Core\Block\Attribute\Block';

    $lines = explode("\n", $source1020);
    $configurations = [];
    foreach ($lines as $line) {
        $parts = explode("\t", $line);

        $annotationParts = explode('\\', $parts[0]);
        $annotation = array_pop($annotationParts);
        $attributeClass = $parts[1];
        $configurations[] = new \DrupalRector\Drupal10\Rector\ValueObject\AnnotationToAttributeConfiguration(
            '10.2.0',
            '12.0.0',
            $annotation,
            $attributeClass
        );
    }

    $rectorConfig->ruleWithConfiguration(\DrupalRector\Drupal10\Rector\Deprecation\AnnotationToAttributeRector::class, $configurations);
};
