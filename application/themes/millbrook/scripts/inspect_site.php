<?php

use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Page\Template as PageTemplate;

$printTree = static function (Page $page, int $depth = 0) use (&$printTree): void {
    if ($page->isError()) {
        return;
    }

    $indent = str_repeat('  ', $depth);
    $template = $page->getPageTemplateObject();
    $type = $page->getPageTypeObject();

    printf(
        "%s- [%d] %s | path=%s | type=%s | template=%s\n",
        $indent,
        $page->getCollectionID(),
        $page->getCollectionName(),
        $page->getCollectionPath() ?: '/',
        $type ? $type->getPageTypeHandle() : 'none',
        $template ? $template->getPageTemplateHandle() : 'none'
    );

    foreach ($page->getCollectionChildrenArray(true) as $childId) {
        $child = Page::getByID((int) $childId, 'ACTIVE');
        if ($child->isError()) {
            continue;
        }
        $printTree($child, $depth + 1);
    }
};

echo "PAGE TYPES\n";
foreach (PageType::getList() as $pageType) {
    printf(
        "- %s (%s)\n",
        $pageType->getPageTypeDisplayName('text'),
        $pageType->getPageTypeHandle()
    );
}

echo "\nPAGE TEMPLATES\n";
foreach (PageTemplate::getList() as $template) {
    printf(
        "- %s (%s)\n",
        $template->getPageTemplateDisplayName(),
        $template->getPageTemplateHandle()
    );
}

echo "\nSITE TREE\n";
$site = \Core::make('site')->getSite();
$home = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
$printTree($home);
