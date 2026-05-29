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
        return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\Core\Site\Settings::get('locale_translation_path', 'public://translations'), fn() => $this->config('locale.settings')->get('translation.path'));
    }
}
?>
