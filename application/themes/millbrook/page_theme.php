<?php
namespace Application\Theme\Millbrook;

use Concrete\Core\Page\Theme\BedrockThemeTrait;
use Concrete\Core\Page\Theme\Theme;

class PageTheme extends Theme
{
    use BedrockThemeTrait;

    public function getThemeName()
    {
        return t('Millbrook');
    }

    public function getThemeDescription()
    {
        return t('A clean and modern theme for Millbrook Church.');
    }
}
