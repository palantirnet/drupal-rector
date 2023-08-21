<?php

namespace DrupalRector\Rector\ValueObject;

class StaticArgumentRenameConfiguration {

    protected string $methodName;

    protected string $fullyQualifiedClassName;

    protected string $newArgument;

    protected string $oldArgument;

    public function __construct(string $oldArgument, string $newArgument, string $fullyQualifiedClassName = 'Drupal', string $methodName = 'service') {
        $this->oldArgument = $oldArgument;
        $this->newArgument = $newArgument;
        $this->fullyQualifiedClassName = $fullyQualifiedClassName;
        $this->methodName = $methodName;
    }

    public function getMethodName(): string {
        return $this->methodName;
    }

    public function getFullyQualifiedClassName(): string {
        return $this->fullyQualifiedClassName;
    }

    public function getNewArgument(): string {
        return $this->newArgument;
    }

    public function getOldArgument(): string {
        return $this->oldArgument;
    }

}
