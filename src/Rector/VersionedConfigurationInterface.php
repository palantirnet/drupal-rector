<?php

namespace DrupalRector\Rector;

interface VersionedConfigurationInterface {

    public function getIntroducedVersion(): string;

}
