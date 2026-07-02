<?php
defined('C5_EXECUTE') or die("Access Denied.");

require_once __DIR__ . '/theme_assets.php';

$pageTitle = isset($c) && method_exists($c, 'getCollectionName') ? (string) $c->getCollectionName() : 'Millbrook Church';
$pageDescription = isset($c) && method_exists($c, 'getCollectionDescription') ? trim((string) $c->getCollectionDescription()) : '';
$pageHeroImageUrl = '';
$pageHeroImageDisabled = false;
$pageHeroImageIsDefault = false;

if (isset($c) && method_exists($c, 'getCollectionAttributeValue')) {
    $pageHeroImageDisabled = (bool) $c->getCollectionAttributeValue('disable_hero_image');

    if (!$pageHeroImageDisabled) {
        $attributeValue = $c->getCollectionAttributeValue('hero_image');

        if (is_object($attributeValue) && method_exists($attributeValue, 'getApprovedVersion')) {
            $approvedVersion = $attributeValue->getApprovedVersion();
            if ($approvedVersion) {
                $pageHeroImageUrl = $approvedVersion->getURL();

                if (method_exists($approvedVersion, 'getFileVersionID')) {
                    $separator = strpos($pageHeroImageUrl, '?') === false ? '?' : '&';
                    $pageHeroImageUrl .= $separator . 'v=' . rawurlencode((string) $approvedVersion->getFileVersionID());
                }
            }
        }
    }
}

if (!$pageHeroImageDisabled && $pageHeroImageUrl === '' && isset($this) && method_exists($this, 'getThemePath')) {
    $pageHeroImageUrl = millbrook_default_hero_image_url($this->getThemePath());
    $pageHeroImageIsDefault = true;
}
