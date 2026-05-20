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
    'whats-on-block' => __DIR__ . '/add_whats_on_block.php',
    'home-whats-on' => __DIR__ . '/add_home_whats_on_block.php',
    'whats-on-express' => __DIR__ . '/migrate_whats_on_to_express.php',
];

$seed = $args[0] ?? null;

if ($seed === null || in_array($seed, ['-h', '--help', 'help'], true)) {
    $output->writeln('Millbrook content seed runner');
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

if ($seed === 'all') {
    foreach (['hero-attributes', 'navigation-attributes', 'demo-sitemap', 'visitor-blueprint', 'new-here', 'rename-labels', 'policies-documents', 'sermons-block', 'whats-on-block', 'home-whats-on', 'whats-on-express'] as $key) {
        $output->writeln(sprintf('<info>Running seed: %s</info>', $key));
        $rc = require $seedMap[$key];
        if (is_numeric($rc) && (int) $rc !== 0) {
            return (int) $rc;
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

$output->writeln(sprintf('<info>Running seed: %s</info>', $seed));
$result = require $seedMap[$seed];

return is_numeric($result) ? (int) $result : 0;
