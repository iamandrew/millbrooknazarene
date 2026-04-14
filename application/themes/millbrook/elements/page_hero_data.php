<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\File\File;

$pageTitle = isset($c) && method_exists($c, 'getCollectionName') ? (string) $c->getCollectionName() : 'Millbrook Church';
$pageDescription = isset($c) && method_exists($c, 'getCollectionDescription') ? trim((string) $c->getCollectionDescription()) : '';
$pageHeroImageUrl = '';
$pageHeroImageDisabled = false;

if (isset($c) && method_exists($c, 'getCollectionAttributeValue')) {
    $pageHeroImageDisabled = (bool) $c->getCollectionAttributeValue('disable_hero_image');

    if (!$pageHeroImageDisabled) {
        $attributeValue = $c->getCollectionAttributeValue('hero_image');

        if ($attributeValue instanceof File) {
            $approvedVersion = $attributeValue->getApprovedVersion();
            if ($approvedVersion) {
                $pageHeroImageUrl = $approvedVersion->getURL();
            }
        }
    }
}

if (!$pageHeroImageDisabled && $pageHeroImageUrl === '' && isset($this) && method_exists($this, 'getThemePath')) {
    $pageHeroImageUrl = $this->getThemePath() . '/images/hero.png';
}
