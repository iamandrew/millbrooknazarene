<?php

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\EntryList;
use Doctrine\ORM\EntityManagerInterface;

$entityManager = app(EntityManagerInterface::class);
$entity = $entityManager->getRepository(Entity::class)->findOneBy(['handle' => 'whats_on_item']);

if (!$entity) {
    $output->writeln('<error>Could not find the What’s On Item Express entity.</error>');
    return 1;
}

$output->writeln(sprintf('ENTITY|%s|%s', $entity->getHandle(), $entity->getName()));

$list = new EntryList($entity);
$list->ignorePermissions();

foreach ($list->getResults() as $entry) {
    if (!$entry instanceof Entry) {
        continue;
    }

    $output->writeln(sprintf(
        'ITEM|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s',
        trim((string) $entry->getAttribute('section')),
        trim((string) $entry->getAttribute('eyebrow')),
        trim((string) $entry->getAttribute('item_title')),
        trim((string) $entry->getAttribute('summary')),
        trim((string) $entry->getAttribute('meta')),
        trim((string) $entry->getAttribute('link_label')),
        trim((string) $entry->getAttribute('link_url')),
        trim((string) $entry->getAttribute('card_style')),
        (bool) $entry->getAttribute('always_on') ? 'always_on' : 'dated',
        format_whats_on_date($entry->getAttribute('start_date')),
        format_whats_on_date($entry->getAttribute('end_date')),
        trim((string) $entry->getAttribute('sort_order'))
    ));
}

return 0;

function format_whats_on_date($value): string
{
    if ($value instanceof DateTimeInterface) {
        return $value->format('Y-m-d H:i:s');
    }

    return trim((string) $value);
}
