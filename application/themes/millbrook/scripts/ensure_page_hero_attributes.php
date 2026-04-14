<?php

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Attribute\Type;

$category = Category::getByHandle('collection');

if (!$category) {
    $output->writeln('<error>Could not load the page attribute category.</error>');
    return 1;
}

$controller = $category->getController();

$definitions = [
    [
        'handle' => 'hero_image',
        'name' => 'Hero Image',
        'type' => 'image_file',
    ],
    [
        'handle' => 'disable_hero_image',
        'name' => 'Disable Hero Image',
        'type' => 'boolean',
    ],
];

foreach ($definitions as $definition) {
    $existing = $controller->getAttributeKeyByHandle($definition['handle']);
    if ($existing) {
        $output->writeln(sprintf('<comment>Attribute already exists: %s</comment>', $definition['handle']));
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

    $output->writeln(sprintf('<info>Created page attribute: %s</info>', $definition['handle']));
}

$output->writeln('<info>Page hero attributes are ready.</info>');

return 0;
