<?php

// Class whose short name matches a PARENT_SHORT_NAMES entry but is NOT a
// Drupal Views cache plugin — cacheExpire() must NOT be removed.
use SomeOther\Cache\Time;

class MyCache extends Time
{
    protected function cacheExpire($type)
    {
        return 3600;
    }
}
