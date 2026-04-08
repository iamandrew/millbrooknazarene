<?php
// Attempt to bootstrap Concrete and add three Content blocks into the 'Home Features' area
// Run from project root via CLI:
// php application/themes/millbrook/scripts/add_home_features.php

define('C5_EXECUTE', true);

$dispatcher = __DIR__ . '/../../../../concrete/dispatcher.php';
if (!file_exists($dispatcher)) {
    echo "Cannot find Concrete dispatcher at: $dispatcher\n";
    echo "Run this script from the project root.\n";
    exit(1);
}

// Try to bootstrap Concrete. This may run the runtime; if the environment prevents it, abort gracefully.
try {
    $app = require $dispatcher;
} catch (Throwable $e) {
    echo "Failed to bootstrap Concrete: " . $e->getMessage() . "\n";
    exit(2);
}

// Use Concrete classes to add blocks
use Concrete\Core\Page\Page;
use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;

try {
    $c = Page::getByPath('/');
    if (!$c || $c->isError()) {
        echo "Couldn't find the site root page.\n";
        exit(3);
    }

    $a = Area::getOrCreate($c, 'Home Features');

    $bt = BlockType::getByHandle('content');
    if (!$bt) {
        echo "Content block type not available.\n";
        exit(4);
    }

    $items = [
        ['title' => 'Sunday Worship', 'body' => 'Join us every Sunday at 10:30am for worship and teaching.'],
        ['title' => 'Community Groups', 'body' => 'Connect with a small group for study, prayer, and friendship.'],
        ['title' => 'Serve', 'body' => 'Use your gifts to serve locally and globally. Ministries for all ages.'],
    ];

    foreach ($items as $it) {
        $content = '<h3>' . htmlspecialchars($it['title']) . '</h3><p>' . htmlspecialchars($it['body']) . '</p>';
        $data = ['content' => $content];
        // Attempt to add block to area
        $b = $c->addBlock($bt, $a, $data);
        if ($b) {
            echo "Added block: " . $it['title'] . "\n";
        } else {
            echo "Failed to add block: " . $it['title'] . "\n";
        }
    }

    echo "Done. Refresh the home page to see the new blocks.\n";
} catch (Throwable $e) {
    echo "Error while adding blocks: " . $e->getMessage() . "\n";
    exit(5);
}
