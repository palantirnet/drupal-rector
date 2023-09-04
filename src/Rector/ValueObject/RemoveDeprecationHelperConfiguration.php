<?php

namespace DrupalRector\Rector\ValueObject;

class RemoveDeprecationHelperConfiguration {

    protected int $majorVersion;

    public function __construct(int $majorVersion) {
        $this->majorVersion = $majorVersion;
    }

    public function getMajorVersion(): int {
        return $this->majorVersion;
    }
}
