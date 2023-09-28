<?php

namespace DrupalRector\Contract;

interface VersionedConfigurationInterface {

    public function getIntroducedVersion(): string;

}
