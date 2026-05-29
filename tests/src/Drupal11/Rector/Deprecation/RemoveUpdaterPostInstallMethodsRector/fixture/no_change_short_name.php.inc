<?php

// Class extends a short name 'Updater' without a FQCN import.
// The rector only matches FQCN extends, so this is NOT transformed.
class MyUpdater extends Updater
{
    public function postInstall()
    {
        return 'noop';
    }
}

?>
-----
<?php

// Class extends a short name 'Updater' without a FQCN import.
// The rector only matches FQCN extends, so this is NOT transformed.
class MyUpdater extends Updater
{
    public function postInstall()
    {
        return 'noop';
    }
}

?>
