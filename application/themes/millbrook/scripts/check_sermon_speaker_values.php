<?php

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\EntryList;
use Doctrine\ORM\EntityManagerInterface;

$entityManager = app(EntityManagerInterface::class);
$entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'sermon']);

if (!$entity) {
    $output->writeln('<error>No sermon entity found.</error>');
    return 1;
}

$list = new EntryList($entity);
$list->ignorePermissions();

foreach ($list->getResults() as $entry) {
    $title = (string) $entry->getAttribute('sermon_title');
    $speaker = (string) $entry->getAttribute('speaker');
    $avo = $entry->getAttributeValueObject('speaker');
    $raw = '';

    if ($avo && method_exists($avo, 'getValueObject')) {
        $valueObject = $avo->getValueObject();
        if (is_object($valueObject) && method_exists($valueObject, 'getValue')) {
            $raw = (string) $valueObject->getValue();
        }
    }

    $output->writeln(sprintf('ENTRY|%s|speaker=%s|raw=%s', $title, $speaker, $raw));
}

return 0;
