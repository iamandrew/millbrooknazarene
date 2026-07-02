<?php
defined('C5_EXECUTE') or die("Access Denied.");

if (!function_exists('millbrook_escape_attr')) {
    function millbrook_escape_attr(string $value): string
    {
        return function_exists('h')
            ? h($value)
            : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('millbrook_theme_asset_url')) {
    function millbrook_theme_asset_url(string $themePath, string $assetPath, bool $version = true): string
    {
        $assetPath = ltrim($assetPath, '/');
        $url = rtrim($themePath, '/') . '/' . $assetPath;

        if (!$version) {
            return $url;
        }

        $filePath = dirname(__DIR__) . '/' . $assetPath;
        if (!is_file($filePath)) {
            return $url;
        }

        $separator = strpos($url, '?') === false ? '?' : '&';

        return $url . $separator . 'v=' . rawurlencode((string) filemtime($filePath));
    }
}

if (!function_exists('millbrook_default_hero_image_url')) {
    function millbrook_default_hero_image_url(string $themePath): string
    {
        return millbrook_theme_asset_url($themePath, 'images/hero-1800.webp');
    }
}

if (!function_exists('millbrook_default_hero_fallback_url')) {
    function millbrook_default_hero_fallback_url(string $themePath): string
    {
        return millbrook_theme_asset_url($themePath, 'images/hero-1800.jpg');
    }
}

if (!function_exists('millbrook_default_hero_image_markup')) {
    function millbrook_default_hero_image_markup(string $themePath): string
    {
        return sprintf(
            '<picture class="hero-image-card__picture"><source srcset="%s" type="image/webp"><img class="hero-image-card__image" src="%s" alt="" width="1800" height="956" loading="eager" decoding="async" fetchpriority="high"></picture>',
            millbrook_escape_attr(millbrook_default_hero_image_url($themePath)),
            millbrook_escape_attr(millbrook_default_hero_fallback_url($themePath))
        );
    }
}

if (!function_exists('millbrook_hero_image_markup')) {
    function millbrook_hero_image_markup(string $imageUrl, bool $isDefaultImage, string $themePath): string
    {
        if ($imageUrl === '') {
            return '';
        }

        if ($isDefaultImage) {
            return millbrook_default_hero_image_markup($themePath);
        }

        return sprintf(
            '<img class="hero-image-card__image" src="%s" alt="" loading="eager" decoding="async" fetchpriority="high">',
            millbrook_escape_attr($imageUrl)
        );
    }
}

if (!function_exists('millbrook_page_uses_default_hero')) {
    function millbrook_page_uses_default_hero($page): bool
    {
        if (!is_object($page) || !method_exists($page, 'getCollectionAttributeValue')) {
            return false;
        }

        if (method_exists($page, 'getCollectionPath') && (string) $page->getCollectionPath() === '/kids-club-2026') {
            return false;
        }

        if ((bool) $page->getCollectionAttributeValue('disable_hero_image')) {
            return false;
        }

        $heroImage = $page->getCollectionAttributeValue('hero_image');
        if (is_object($heroImage)) {
            if (!method_exists($heroImage, 'getApprovedVersion')) {
                return false;
            }

            return !$heroImage->getApprovedVersion();
        }

        return trim((string) $heroImage) === '';
    }
}

if (!function_exists('millbrook_page_uses_sermon_player')) {
    function millbrook_page_uses_sermon_player($page): bool
    {
        if (!is_object($page) || !method_exists($page, 'getCollectionPath')) {
            return false;
        }

        $path = (string) $page->getCollectionPath();

        return $path === '/resources/sermons' || strpos($path, '/resources/sermons/') === 0;
    }
}
