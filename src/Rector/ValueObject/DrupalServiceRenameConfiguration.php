<?php

namespace DrupalRector\Rector\ValueObject;

class DrupalServiceRenameConfiguration {
    protected string $newArgument;

    protected string $oldArgument;

    public function __construct(string $oldArgument, string $newArgument) {
        $this->oldArgument = $oldArgument;
        $this->newArgument = $newArgument;
    }
    public function getNewArgument(): string {
        return $this->newArgument;
    }

    public function getOldArgument(): string {
        return $this->oldArgument;
    }

}
