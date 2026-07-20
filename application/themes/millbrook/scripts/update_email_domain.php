<?php

$connection = \Database::connection();
$schemaManager = $connection->getSchemaManager();
$tables = array_flip($schemaManager->listTableNames());
$oldDomain = 'millbrooknazarene.co.uk';
$newDomain = 'millbrooknazarene.church';
$targets = [
    'btContent' => ['content'],
    'btContentLocal' => ['content'],
    'btForm' => ['recipientEmail'],
    'btFormLocal' => ['recipientEmail'],
];
$updatedRows = 0;

foreach ($targets as $table => $columns) {
    if (!isset($tables[$table])) {
        continue;
    }

    $availableColumns = $schemaManager->listTableColumns($table);
    foreach ($columns as $column) {
        if (!isset($availableColumns[$column])) {
            continue;
        }

        $quotedTable = $connection->quoteIdentifier($table);
        $quotedColumn = $connection->quoteIdentifier($column);
        $updatedRows += $connection->executeStatement(
            "UPDATE {$quotedTable} SET {$quotedColumn} = REPLACE({$quotedColumn}, ?, ?) WHERE {$quotedColumn} LIKE ?",
            [$oldDomain, $newDomain, '%' . $oldDomain . '%']
        );
    }
}

$output->writeln(sprintf(
    '<info>Updated %d stored content and form record(s) from %s to %s.</info>',
    $updatedRows,
    $oldDomain,
    $newDomain
));

return 0;
