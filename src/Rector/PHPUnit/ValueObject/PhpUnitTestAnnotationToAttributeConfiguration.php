<?php

declare(strict_types=1);

namespace DrupalRector\Rector\PHPUnit\ValueObject;

use DrupalRector\Contract\VersionedConfigurationInterface;

class PhpUnitTestAnnotationToAttributeConfiguration implements VersionedConfigurationInterface
{
    public function __construct(
        private string $introducedVersion,
        private string $removeVersion,
        private string $annotation,
        private string $attributeClass,
    ) {
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
