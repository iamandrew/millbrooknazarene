<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$contentBlockType = BlockType::getByHandle('content');

if (!$contentBlockType) {
    $output->writeln('<error>Content block type is not available.</error>');
    return 1;
}

$resourcesContent = require __DIR__ . '/content/resources.php';
$pageType = PageType::getByHandle('page');
$fullTemplate = PageTemplate::getByHandle('full');
$page = Page::getByPath('/resources', 'ACTIVE');

if (!$page instanceof Page || $page->isError()) {
    $site = \Core::make('site')->getSite();
    $root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');

    if (!$root instanceof Page || $root->isError() || !$pageType || !$fullTemplate) {
        $output->writeln('<error>Could not resolve the site home page, page type, or full template.</error>');
        return 1;
    }

    $page = $root->add(
        $pageType,
        [
            'cName' => $resourcesContent['name'],
            'cHandle' => 'resources',
            'cDescription' => $resourcesContent['description'],
        ],
        $fullTemplate
    );

    $output->writeln('<info>Created /resources.</info>');
}

$page->update([
    'cName' => $resourcesContent['name'],
    'cHandle' => 'resources',
    'cDescription' => $resourcesContent['description'],
]);

$resourceChildren = [
    [
        'path' => '/resources/sermons',
        'name' => 'Latest Sermons',
        'handle' => 'sermons',
        'description' => 'Catch up on Sunday sermons and Bible teaching from Millbrook. Whether you missed a gathering or want to revisit a message during the week, you can listen here or continue on Spotify or Apple Podcasts.',
        'content' => '<p>Catch up on Sunday sermons and Bible teaching from Millbrook. Whether you missed a gathering or want to revisit a message during the week, you can listen here or continue on Spotify or Apple Podcasts.</p>',
    ],
    [
        'path' => '/resources/policies',
        'name' => 'Policies',
        'handle' => 'policies',
        'description' => 'Important church policies and safeguarding-related documents.',
        'content' => '<p>You can use the document library on this page to access important church policies, safeguarding information, and other key documents.</p>',
    ],
];

foreach ($resourceChildren as $childPageData) {
    $childPage = Page::getByPath($childPageData['path'], 'ACTIVE');

    if (!$childPage instanceof Page || $childPage->isError()) {
        if (!$pageType || !$fullTemplate) {
            $output->writeln('<error>Could not resolve page type or full template for resource child pages.</error>');
            return 1;
        }

        $childPage = $page->add(
            $pageType,
            [
                'cName' => $childPageData['name'],
                'cHandle' => $childPageData['handle'],
                'cDescription' => $childPageData['description'],
            ],
            $fullTemplate
        );

        $childArea = Area::getOrCreate($childPage, 'Main');
        $childPage->addBlock($contentBlockType, $childArea, ['content' => $childPageData['content']]);
        $output->writeln(sprintf('<info>Created %s.</info>', $childPageData['path']));
        continue;
    }

    $childPage->update([
        'cName' => $childPageData['name'],
        'cHandle' => $childPageData['handle'],
        'cDescription' => $childPageData['description'],
    ]);
}

$area = Area::getOrCreate($page, 'Main');
foreach ($area->getAreaBlocksArray($page) as $block) {
    $block->deleteBlock();
}

$page->addBlock($contentBlockType, $area, ['content' => $resourcesContent['content']]);

$output->writeln('<info>Updated Resources page content.</info>');

return 0;
