<?php

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Doctrine\ORM\EntityManagerInterface;

$entityManager = app(EntityManagerInterface::class);
$entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'sermon']);

if (!$entity) {
    $output->writeln('<error>Could not find the Sermon Express entity.</error>');
    return 1;
}

$speakerMap = [
    'Sunday 4 May 2025' => 'David Wilson',
    'Sunday 11 May 2025' => 'Naomi Campbell',
    'Sunday 18 May 2025' => 'Stephen Boyd',
    'Sunday 29 Jun 2025' => 'Rachel McBride',
    'Sunday 14 Sep 2025' => 'Mark Allen',
];

$list = new EntryList($entity);
$list->ignorePermissions();

$updated = 0;

foreach ($list->getResults() as $entry) {
    if (!$entry instanceof Entry) {
        continue;
    }

    $title = trim((string) $entry->getAttribute('sermon_title'));
    if (!isset($speakerMap[$title])) {
        continue;
    }

    $entry->setAttribute('speaker', $speakerMap[$title]);
    $updated++;
    $output->writeln(sprintf('<info>Set speaker for %s: %s</info>', $title, $speakerMap[$title]));
}

$output->writeln(sprintf('<info>Updated %d sermon speaker names.</info>', $updated));

return 0;
