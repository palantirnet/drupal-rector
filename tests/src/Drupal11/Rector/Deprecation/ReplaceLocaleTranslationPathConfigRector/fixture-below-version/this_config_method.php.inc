<?php

class LocaleController
{
    protected function config(string $name): \Drupal\Core\Config\ImmutableConfig
    {
    }

    public function getPath(): ?string
    {
        return $this->config('locale.settings')->get('translation.path');
    }
}
?>
-----
<?php

class LocaleController
{
    protected function config(string $name): \Drupal\Core\Config\ImmutableConfig
    {
    }

    public function getPath(): ?string
    {
        return $this->config('locale.settings')->get('translation.path');
    }
}
?>
