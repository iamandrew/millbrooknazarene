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
$entries = array_slice($list->getResults(), 0, 10);

foreach ($entries as $entry) {
    $audio = $entry->getAttribute('audio_file');
    $audioType = is_object($audio) ? get_class($audio) : gettype($audio);
    $audioTitle = '';
    $audioUrl = '';

    if (is_object($audio) && method_exists($audio, 'getApprovedVersion')) {
        $version = $audio->getApprovedVersion();
        if ($version) {
            $audioTitle = (string) $version->getTitle();
            $audioUrl = (string) $version->getURL();
        }
    }

    $date = $entry->getAttribute('date');
    if ($date instanceof DateTimeInterface) {
        $date = $date->format('c');
    } elseif (is_object($date) && method_exists($date, '__toString')) {
        $date = (string) $date;
    } else {
        $date = is_scalar($date) ? (string) $date : gettype($date);
    }

    $output->writeln(sprintf(
        'ENTRY|%s|%s|%s|%s|%s|%s',
        $entry->getID(),
        (string) $entry->getAttribute('sermon_title'),
        (string) $entry->getAttribute('speaker'),
        (string) $date,
        $audioType,
        $audioTitle !== '' ? $audioTitle : $audioUrl
    ));
}

return 0;
