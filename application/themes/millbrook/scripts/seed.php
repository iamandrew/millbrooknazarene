<?php

$seedMap = [
    'inspect' => __DIR__ . '/inspect_site.php',
    'hero-attributes' => __DIR__ . '/ensure_page_hero_attributes.php',
    'navigation-attributes' => __DIR__ . '/seed_navigation_attributes.php',
    'demo-sitemap' => __DIR__ . '/build_demo_sitemap.php',
    'new-here' => __DIR__ . '/build_new_here_page.php',
    'visitor-blueprint' => __DIR__ . '/build_visitor_blueprint_pages.php',
    'rename-labels' => __DIR__ . '/rename_internal_labels.php',
    'policies-documents' => __DIR__ . '/add_policies_documents_block.php',
    'sermons-block' => __DIR__ . '/add_sermons_block.php',
    'resources' => __DIR__ . '/add_resources_page.php',
    'whats-on-block' => __DIR__ . '/add_whats_on_page.php',
    'whats-on' => __DIR__ . '/add_whats_on_page.php',
    'home-whats-on' => __DIR__ . '/add_home_whats_on_block.php',
    'visit-us' => __DIR__ . '/add_visit_us_page.php',
    'church-life' => __DIR__ . '/add_church_life_page.php',
    'children-families' => __DIR__ . '/add_children_families_page.php',
    'homegroups' => __DIR__ . '/add_homegroups_page.php',
    'mens-ministry' => __DIR__ . '/add_mens_ministry_page.php',
    'about' => __DIR__ . '/add_about_page.php',
    'what-we-believe' => __DIR__ . '/add_what_we_believe_page.php',
    'who-we-are' => __DIR__ . '/add_who_we_are_page.php',
    'contact' => __DIR__ . '/add_contact_page.php',
    'womens-ministry' => __DIR__ . '/add_womens_ministry_page.php',
    'creche' => __DIR__ . '/add_creche_page.php',
    'youth' => __DIR__ . '/add_youth_page.php',
    'giving' => __DIR__ . '/add_giving_page.php',
    'kids-club-2026' => __DIR__ . '/add_kids_club_2026_page.php',
    'whats-on-express' => __DIR__ . '/migrate_whats_on_to_express.php',
    'launch-seo' => __DIR__ . '/set_launch_seo.php',
];

$seedGroups = [
    'deploy' => [
        'hero-attributes',
        'navigation-attributes',
    ],
    'staging-content' => [
        'whats-on-express',
        'home-whats-on',
        'whats-on',
        'visit-us',
        'church-life',
        'children-families',
        'homegroups',
        'mens-ministry',
        'about',
        'what-we-believe',
        'who-we-are',
        'resources',
        'contact',
        'womens-ministry',
        'creche',
        'youth',
        'giving',
        'kids-club-2026',
        'launch-seo',
    ],
];

$seed = $args[0] ?? null;

$runSeed = static function (string $key) use ($seedMap, $output): int {
    if (!isset($seedMap[$key])) {
        $output->writeln(sprintf('<error>Unknown seed "%s"</error>', $key));
        return 1;
    }

    $output->writeln(sprintf('<info>Running seed: %s</info>', $key));
    $rc = require $seedMap[$key];

    return is_numeric($rc) ? (int) $rc : 0;
};

if ($seed === null || in_array($seed, ['-h', '--help', 'help'], true)) {
    $output->writeln('Millbrook content seed runner');
    $output->writeln('');
    $output->writeln('Seed groups:');
    foreach ($seedGroups as $key => $members) {
        $output->writeln(sprintf('  - %s (%s)', $key, implode(', ', $members)));
    }
    $output->writeln('');
    $output->writeln('Available seeds:');
    foreach (array_keys($seedMap) as $key) {
        $output->writeln('  - ' . $key);
    }
    $output->writeln('');
    $output->writeln('Usage:');
    $output->writeln('  php concrete/bin/concrete c5:exec application/themes/millbrook/scripts/seed.php -- <seed>');
    return 0;
}

if (isset($seedGroups[$seed])) {
    foreach ($seedGroups[$seed] as $key) {
        $rc = $runSeed($key);
        if ($rc !== 0) {
            return $rc;
        }
    }

    $output->writeln(sprintf('<info>Completed seed group: %s</info>', $seed));
    return 0;
}

if ($seed === 'all') {
    foreach (['hero-attributes', 'navigation-attributes', 'whats-on-express', 'demo-sitemap', 'visitor-blueprint', 'new-here', 'visit-us', 'church-life', 'children-families', 'whats-on', 'homegroups', 'mens-ministry', 'about', 'what-we-believe', 'resources', 'contact', 'giving', 'kids-club-2026', 'launch-seo', 'rename-labels', 'policies-documents', 'sermons-block', 'home-whats-on'] as $key) {
        $rc = $runSeed($key);
        if ($rc !== 0) {
            return $rc;
        }
    }

    $output->writeln('<info>Completed all content seeds.</info>');
    return 0;
}

if (!isset($seedMap[$seed])) {
    $output->writeln(sprintf('<error>Unknown seed "%s"</error>', $seed));
    $output->writeln('Run with -- help to see available seeds.');
    return 1;
}

return $runSeed($seed);
