<?php

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Page\Page;

$category = Category::getByHandle('collection');

if (!$category) {
    $output->writeln('<error>Could not load the page attribute category.</error>');
    return 1;
}

$controller = $category->getController();

$definitions = [
    [
        'handle' => 'nav_show_in_menu',
        'name' => 'Show In Primary Menu',
        'type' => 'boolean',
    ],
    [
        'handle' => 'nav_show_in_quick_links',
        'name' => 'Show In Quick Links',
        'type' => 'boolean',
    ],
    [
        'handle' => 'nav_label',
        'name' => 'Navigation Label',
        'type' => 'text',
    ],
    [
        'handle' => 'nav_quick_link_order',
        'name' => 'Quick Link Order',
        'type' => 'number',
    ],
    [
        'handle' => 'nav_show_in_footer',
        'name' => 'Show In Footer Links',
        'type' => 'boolean',
    ],
    [
        'handle' => 'nav_footer_order',
        'name' => 'Footer Link Order',
        'type' => 'number',
    ],
];

$attributeKeys = [];

foreach ($definitions as $definition) {
    $existing = $controller->getAttributeKeyByHandle($definition['handle']);
    if ($existing) {
        $attributeKeys[$definition['handle']] = $existing;
        continue;
    }

    $type = Type::getByHandle($definition['type']);
    if (!$type) {
        $output->writeln(sprintf('<error>Could not find attribute type: %s</error>', $definition['type']));
        return 1;
    }

    $key = $controller->createAttributeKey();
    $key->setAttributeKeyHandle($definition['handle']);
    $key->setAttributeKeyName($definition['name']);
    $controller->add($type, $key);
    $attributeKeys[$definition['handle']] = $controller->getAttributeKeyByHandle($definition['handle']);
    $output->writeln(sprintf('<info>Created page attribute: %s</info>', $definition['handle']));
}

foreach ($definitions as $definition) {
    if (!isset($attributeKeys[$definition['handle']]) || !$attributeKeys[$definition['handle']]) {
        $attributeKeys[$definition['handle']] = $controller->getAttributeKeyByHandle($definition['handle']);
    }
}

$pageMap = [
    '/about' => ['nav_show_in_menu' => true, 'nav_label' => 'About'],
    '/about/who-we-are' => ['nav_show_in_menu' => true, 'nav_label' => 'Who We Are'],
    '/about/what-we-believe' => ['nav_show_in_menu' => true, 'nav_label' => 'What We Believe'],
    '/community' => ['nav_show_in_menu' => true, 'nav_label' => 'Church Life'],
    '/community/children' => ['nav_show_in_menu' => true, 'nav_label' => 'Children & Families'],
    '/community/homegroups' => ['nav_show_in_menu' => true, 'nav_label' => 'Homegroups'],
    '/community/mens-ministry' => ['nav_show_in_menu' => true, 'nav_label' => 'Men'],
    '/community/womens-ministry' => ['nav_show_in_menu' => true, 'nav_label' => 'Women'],
    '/community/creche' => ['nav_show_in_menu' => true, 'nav_label' => 'Creche'],
    '/community/cheesy-nachos' => ['nav_show_in_menu' => true, 'nav_label' => 'Youth'],
    '/resources' => ['nav_show_in_menu' => true, 'nav_label' => 'Resources'],
    '/resources/sermons' => ['nav_show_in_menu' => true, 'nav_show_in_quick_links' => true, 'nav_quick_link_order' => 3, 'nav_show_in_footer' => true, 'nav_footer_order' => 3, 'nav_label' => 'Latest Sermons'],
    '/resources/policies' => ['nav_show_in_menu' => true, 'nav_label' => 'Policies'],
    '/visit-us' => ['nav_show_in_quick_links' => true, 'nav_quick_link_order' => 1, 'nav_show_in_footer' => true, 'nav_footer_order' => 1, 'nav_label' => 'Visit Us?'],
    '/community/whats-on' => ['nav_show_in_quick_links' => true, 'nav_quick_link_order' => 2, 'nav_show_in_footer' => true, 'nav_footer_order' => 2, 'nav_label' => "What's On"],
    '/contact' => ['nav_show_in_quick_links' => true, 'nav_quick_link_order' => 4, 'nav_show_in_footer' => true, 'nav_footer_order' => 4, 'nav_label' => 'Contact'],
    '/giving' => ['nav_show_in_footer' => true, 'nav_footer_order' => 5, 'nav_label' => 'Giving'],
];

foreach ($pageMap as $path => $attributes) {
    $page = Page::getByPath($path, 'ACTIVE');
    if (!$page instanceof Page || $page->isError()) {
        $output->writeln(sprintf('<comment>Skipped missing page: %s</comment>', $path));
        continue;
    }

    foreach ($attributes as $handle => $value) {
        if (!isset($attributeKeys[$handle])) {
            $output->writeln(sprintf('<comment>Skipped missing attribute key: %s</comment>', $handle));
            continue;
        }

        $page->setAttribute($attributeKeys[$handle], $value);
    }

    $output->writeln(sprintf('Updated navigation attributes: %s', $page->getCollectionPath()));
}

$output->writeln('<info>Navigation attributes are ready.</info>');

return 0;
