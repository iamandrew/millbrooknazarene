<?php

use Concrete\Core\Express\ObjectManager;

$manager = app(ObjectManager::class);
$entities = $manager->getEntities();

foreach ($entities as $entity) {
    $output->writeln(sprintf('ENTITY|%s|%s', $entity->getHandle(), $entity->getName()));
    foreach ($entity->getAttributes() as $attribute) {
        $output->writeln(sprintf(
            'ATTR|%s|%s|%s|%s',
            $entity->getHandle(),
            $attribute->getAttributeKeyHandle(),
            $attribute->getAttributeKeyDisplayName(),
            $attribute->getAttributeTypeHandle()
        ));
    }
}

return 0;
