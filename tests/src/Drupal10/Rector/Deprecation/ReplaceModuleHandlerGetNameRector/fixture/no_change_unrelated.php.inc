<?php

// getName() on a class unrelated to ModuleHandlerInterface — must not be changed.

class UnrelatedManager {
    public function getName(string $key): string {
        return $key;
    }
}

/** @var \UnrelatedManager $manager */
$name = $manager->getName('mymodule');
