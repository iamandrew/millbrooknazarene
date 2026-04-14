<?php

$seedMap = [
    'inspect' => __DIR__ . '/inspect_site.php',
    'hero-attributes' => __DIR__ . '/ensure_page_hero_attributes.php',
    'demo-sitemap' => __DIR__ . '/build_demo_sitemap.php',
    'new-here' => __DIR__ . '/build_new_here_page.php',
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
    foreach (['hero-attributes', 'demo-sitemap', 'new-here'] as $key) {
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
