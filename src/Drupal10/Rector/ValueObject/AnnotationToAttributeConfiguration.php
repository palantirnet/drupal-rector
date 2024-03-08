<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class AnnotationToAttributeConfiguration implements VersionedConfigurationInterface
{
    private string $introducedVersion;

    private string $removeVersion;

    private string $annotation;

    private string $attributeClass;

    public function __construct(string $introducedVersion, string $removeVersion, string $annotation, string $attributeClass)
    {
        $this->introducedVersion = $introducedVersion;
        $this->removeVersion = $removeVersion;
        $this->annotation = $annotation;
        $this->attributeClass = $attributeClass;
    }

    public function getIntroducedVersion(): string
    {
        return $this->introducedVersion;
    }

    public function getRemoveVersion(): string
    {
        return $this->removeVersion;
    }

    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    public function getAttributeClass(): string
    {
        return $this->attributeClass;
    }
}
