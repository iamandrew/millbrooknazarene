<?php

namespace Concrete\Package\MillbrookTurnstile;

use Concrete\Core\Captcha\Library;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected $pkgHandle = 'millbrook_turnstile';
    protected $appVersionRequired = '9.4.0';
    protected $pkgVersion = '1.0.0';

    public function getPackageName()
    {
        return t('Millbrook Turnstile');
    }

    public function getPackageDescription()
    {
        return t('Cloudflare Turnstile validation for Millbrook forms.');
    }

    public function install()
    {
        $package = parent::install();
        $library = Library::getByHandle('turnstile') ?: Library::add('turnstile', 'Cloudflare Turnstile', $package);
        $library->activate();

        return $package;
    }
}
