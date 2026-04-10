<?php

use Concrete\Core\Area\Area;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Template as PageTemplate;
use Concrete\Core\Page\Type\Type as PageType;

$pageType = PageType::getByHandle('page');
$fullTemplate = PageTemplate::getByHandle('full');
$contentBlockType = BlockType::getByHandle('content');

if (!$pageType || !$fullTemplate || !$contentBlockType) {
    $output->writeln('<error>Missing required page type, template, or content block type.</error>');
    return 1;
}

$site = \Core::make('site')->getSite();
$root = Page::getByID((int) $site->getSiteHomePageID(), 'ACTIVE');
if (!$root || $root->isError()) {
    $output->writeln('<error>Unable to find the site home page.</error>');
    return 1;
}

$joinPath = static function (string $parentPath, string $handle): string {
    return $parentPath === '/' ? '/' . $handle : rtrim($parentPath, '/') . '/' . $handle;
};

$findOrCreatePage = static function (Page $parent, array $definition) use ($pageType, $fullTemplate, $joinPath, $output): Page {
    $path = $joinPath((string) $parent->getCollectionPath(), $definition['handle']);
    $page = Page::getByPath($path, 'ACTIVE');

    if ((!$page || $page->isError()) && !empty($definition['aliases'])) {
        foreach ($definition['aliases'] as $aliasHandle) {
            $aliasPath = $joinPath((string) $parent->getCollectionPath(), $aliasHandle);
            $page = Page::getByPath($aliasPath, 'ACTIVE');
            if ($page && !$page->isError()) {
                break;
            }
        }
    }

    if ($page && !$page->isError()) {
        $update = [];
        if ($page->getCollectionName() !== $definition['name']) {
            $update['cName'] = $definition['name'];
        }
        if (!$page->getCollectionDescription() && !empty($definition['description'])) {
            $update['cDescription'] = $definition['description'];
        }
        if ($page->getPageTemplateID() !== $fullTemplate->getPageTemplateID()) {
            $update['pTemplateID'] = $fullTemplate->getPageTemplateID();
        }
        if ($update !== []) {
            if (!isset($update['cHandle'])) {
                $update['cHandle'] = $definition['handle'];
            }
            $page->update($update);
            $output->writeln(sprintf('Updated page: %s', $page->getCollectionPath()));
            $page = Page::getByID($page->getCollectionID(), 'ACTIVE');
        }

        return $page;
    }

    $page = $parent->add(
        $pageType,
        [
            'cName' => $definition['name'],
            'cHandle' => $definition['handle'],
            'cDescription' => $definition['description'] ?? '',
        ],
        $fullTemplate
    );

    $output->writeln(sprintf('Created page: %s', $page->getCollectionPath()));

    return $page;
};

$seedMainArea = static function (Page $page, string $html) use ($contentBlockType, $output): void {
    $area = new Area('Main');
    if ($area->getTotalBlocksInArea($page) > 0) {
        $output->writeln(sprintf('Skipped content (already populated): %s', $page->getCollectionPath()));
        return;
    }

    $page->addBlock($contentBlockType, $area, ['content' => $html]);
    $output->writeln(sprintf('Seeded content: %s', $page->getCollectionPath()));
};

$tree = [
    [
        'name' => "I'm New",
        'handle' => 'im-new',
        'description' => 'Everything you need to know before your first visit.',
        'content' => <<<'HTML'
<h2>Your First Visit</h2>
<p>Walking into a church for the first time can feel like a big step, so we want to make things as clear and welcoming as possible. Millbrook is a relaxed, multi-generational church where people are invited to come as they are and explore faith at their own pace.</p>
<p>When you arrive, someone will usually be on hand to help you find your way in, answer questions, and point you toward coffee, seating, and children’s spaces if you need them.</p>

<h3>What To Expect</h3>
<ul>
  <li>A friendly welcome and time to settle in</li>
  <li>Worship, prayer, Scripture reading, and Bible teaching</li>
  <li>A service that is accessible whether you know church well or not at all</li>
  <li>Tea, coffee, and a chance to connect afterwards</li>
</ul>

<blockquote>
  <p>You do not need to know what to say, what to wear, or where everything is before you come. We will be glad to help.</p>
</blockquote>

<h2>Families And Children</h2>
<p>Children are very welcome at Millbrook. On a typical Sunday there is space for families to stay together in the service, and there are age-appropriate activities available at different points in the morning.</p>

<h3>Helpful Details</h3>
<ul>
  <li>Sunday worship begins at 11:00am</li>
  <li>We meet in Millbrook Community Centre</li>
  <li>Parking is available nearby</li>
  <li>If you have access needs, please get in touch before you visit</li>
</ul>
HTML,
    ],
    [
        'name' => 'About',
        'handle' => 'about',
        'description' => 'Learn more about the life, beliefs, and purpose of Millbrook Church.',
        'content' => <<<'HTML'
<h2>About Millbrook</h2>
<p>Millbrook Church of the Nazarene is a local congregation seeking to follow Jesus, love one another well, and serve the wider community with humility and hope. We want to be a church where people can belong, grow in faith, and discover practical ways to live out the gospel together.</p>

<h3>Explore This Section</h3>
<ul>
  <li><a href="/about/who-we-are">Who We Are</a></li>
  <li><a href="/about/what-we-believe">What We Believe</a></li>
  <li><a href="/about/faqs">FAQs</a></li>
</ul>
HTML,
        'children' => [
            [
                'name' => 'Who We Are',
                'handle' => 'who-we-are',
                'description' => 'Our story, our heart for community, and the kind of church we hope to be.',
                'content' => <<<'HTML'
<h2>Who We Are</h2>
<p>Millbrook is a local church rooted in Scripture, shaped by worship and prayer, and committed to the life of our community. We want to help people encounter Jesus, grow in faith, and find meaningful ways to belong.</p>
<p>Our church family includes children, students, parents, single adults, and older members of the congregation. We see that mix as a gift, and we want church life to reflect the richness of different ages, experiences, and stories gathered around Christ.</p>

<h3>What Matters To Us</h3>
<ul>
  <li>Faith that is centred on Jesus and grounded in the Bible</li>
  <li>A church culture marked by grace, honesty, and hospitality</li>
  <li>Prayerful dependence on God in both ordinary and difficult seasons</li>
  <li>Service that blesses our neighbours in practical ways</li>
</ul>

<blockquote>
  <p>We want to be the kind of church where people are known, encouraged, and invited to keep taking the next step with Jesus.</p>
</blockquote>
HTML,
            ],
            [
                'name' => 'What We Believe',
                'handle' => 'what-we-believe',
                'description' => 'A clear introduction to the Christian convictions that shape our church.',
                'content' => <<<'HTML'
<h2>What We Believe</h2>
<p>Our beliefs are rooted in the historic Christian faith and shaped by the teaching of Scripture. We believe that God has made himself known through Jesus Christ and invites us into a life of grace, holiness, hope, and love.</p>

<h3>Our Core Convictions</h3>
<ul>
  <li>God is the loving creator and sustainer of all things</li>
  <li>Jesus Christ is Lord and Saviour, fully God and fully human</li>
  <li>The Holy Spirit is present and active in the life of the Church</li>
  <li>The Bible is trustworthy and central to our faith and practice</li>
  <li>God calls us into salvation, discipleship, and holy living</li>
</ul>

<h3>Why This Matters</h3>
<p>We do not hold our beliefs as abstract ideas only. We want them to shape the way we worship, pray, make decisions, care for one another, and serve the world around us.</p>
HTML,
            ],
            [
                'name' => 'FAQs',
                'handle' => 'faqs',
                'description' => 'Common questions about visiting, worship, families, and church life.',
                'content' => <<<'HTML'
<h2>Frequently Asked Questions</h2>

<h3>Do I need to dress up?</h3>
<p>No. Most people come dressed casually, and you are welcome to come as you are.</p>

<h3>Can I come if I am unsure what I believe?</h3>
<p>Absolutely. You do not need to have everything figured out before you visit. Many people are exploring faith, returning to church, or simply curious to learn more.</p>

<h3>What about children?</h3>
<p>Families are very welcome. We aim to create a safe, caring environment for children and young people and to make it easy for parents and carers to participate in church life.</p>

<h3>Will I be singled out as a visitor?</h3>
<p>No. We will be glad to meet you, but you can join in at your own pace.</p>
HTML,
            ],
        ],
    ],
    [
        'name' => 'Ministries',
        'handle' => 'ministries',
        'description' => 'Find groups, gatherings, and ministries for different ages and stages of life.',
        'content' => <<<'HTML'
<h2>Ministries</h2>
<p>Church life happens in many settings through the week, not only on a Sunday. Our ministries are designed to help people build friendships, grow in faith, and serve alongside others.</p>

<h3>Explore Our Ministries</h3>
<ul>
  <li><a href="/ministries/whats-on">What’s On</a></li>
  <li><a href="/ministries/homegroups">Homegroups</a></li>
  <li><a href="/ministries/children">Children</a></li>
  <li><a href="/ministries/cheesy-nachos">Cheesy Nachos</a></li>
  <li><a href="/ministries/mens-ministry">Men’s Ministry</a></li>
  <li><a href="/ministries/womens-ministry">Women’s Ministry</a></li>
  <li><a href="/ministries/creche">Creche</a></li>
</ul>
HTML,
        'children' => [
            [
                'name' => "What's On",
                'handle' => 'whats-on',
                'description' => 'A snapshot of regular gatherings, seasonal events, and upcoming activities.',
                'content' => <<<'HTML'
<h2>What’s On</h2>
<p>Across the month we gather in a range of ways, from Sunday worship and prayer meetings to children’s activities, ministry gatherings, and seasonal church events.</p>

<h3>Regular Rhythms</h3>
<ul>
  <li>Sunday worship each week at 11:00am</li>
  <li>Midweek groups for prayer, learning, and friendship</li>
  <li>Children’s and family activities at different points in the year</li>
  <li>Occasional church-wide meals, special services, and community events</li>
</ul>
HTML,
            ],
            [
                'name' => 'Homegroups',
                'handle' => 'homegroups',
                'description' => 'Smaller communities for Bible study, prayer, care, and encouragement.',
                'content' => <<<'HTML'
<h2>Homegroups</h2>
<p>Homegroups are one of the main ways people build deeper friendships and grow in faith at Millbrook. These smaller gatherings create space to open Scripture together, pray honestly, and support one another through everyday life.</p>

<h3>What Homegroups Offer</h3>
<ul>
  <li>A welcoming setting for conversation and prayer</li>
  <li>Time to explore the Bible together in a practical way</li>
  <li>Friendship, care, and encouragement beyond Sunday</li>
  <li>A natural place to ask questions and get to know others</li>
</ul>
HTML,
            ],
            [
                'name' => 'Children',
                'handle' => 'children',
                'description' => 'A safe, joyful environment where children can learn about Jesus and belong.',
                'content' => <<<'HTML'
<h2>Children</h2>
<p>We want children to know that church is a place where they are seen, valued, and included. Through teaching, worship, activities, and caring relationships, we aim to help children discover who Jesus is in ways they can understand.</p>

<h3>What You Can Expect</h3>
<ul>
  <li>Age-appropriate teaching and activities</li>
  <li>Leaders who care well for children and families</li>
  <li>A warm environment where questions and curiosity are welcome</li>
  <li>Opportunities to connect with other families in the church</li>
</ul>
HTML,
            ],
            [
                'name' => 'Cheesy Nachos',
                'handle' => 'cheesy-nachos',
                'description' => 'A friendly social space for young people to connect, laugh, and explore faith.',
                'content' => <<<'HTML'
<h2>Cheesy Nachos</h2>
<p>Cheesy Nachos is a relaxed space for young people to meet, have fun, and build friendships in a safe and welcoming environment. It is a place where faith conversations can happen naturally alongside games, food, and shared experiences.</p>

<h3>A Typical Gathering</h3>
<ul>
  <li>Games, conversation, and time to hang out</li>
  <li>Short faith-based input or discussion</li>
  <li>A friendly atmosphere where new people can settle in quickly</li>
  <li>Leaders who want to support and encourage young people well</li>
</ul>
HTML,
            ],
            [
                'name' => "Men's Ministry",
                'handle' => 'mens-ministry',
                'aliases' => ['men'],
                'description' => 'A space for men to grow in faith, friendship, and service together.',
                'content' => <<<'HTML'
<h2>Men’s Ministry</h2>
<p>Men’s Ministry creates opportunities for connection, encouragement, and spiritual growth. Through gatherings, conversations, and shared activities, we want men to be strengthened in faith and equipped for everyday discipleship.</p>

<h3>Why It Matters</h3>
<p>It can be difficult to build meaningful friendships and make time for spiritual growth in the middle of work, family life, and responsibility. This ministry creates room for honest conversation, prayer, and mutual encouragement.</p>
HTML,
            ],
            [
                'name' => "Women's Ministry",
                'handle' => 'womens-ministry',
                'aliases' => ['women'],
                'description' => 'Gatherings that encourage friendship, prayer, learning, and spiritual growth.',
                'content' => <<<'HTML'
<h2>Women’s Ministry</h2>
<p>Women’s Ministry offers spaces for women to gather, support one another, and keep growing in faith. Some gatherings are social, some are reflective, and some focus more directly on Bible study and prayer, but each one is intended to strengthen relationships and discipleship.</p>

<h3>What It Looks Like</h3>
<ul>
  <li>Regular opportunities to meet and build friendship</li>
  <li>Times of prayer, conversation, and Bible reflection</li>
  <li>Encouragement for women in different ages and stages of life</li>
  <li>Space to share experiences and walk alongside one another well</li>
</ul>
HTML,
            ],
            [
                'name' => 'Creche',
                'handle' => 'creche',
                'description' => 'Support for families with very young children during Sunday gatherings.',
                'content' => <<<'HTML'
<h2>Creche</h2>
<p>The creche exists to support families with very young children and help Sundays feel accessible for everyone. It provides a safe and caring environment where little ones can settle and parents can feel more able to participate in the service.</p>

<h3>For Families</h3>
<p>If you are visiting with a baby or toddler, please feel free to speak to someone when you arrive and we will help you find your way. We want parents and carers to feel supported rather than pressured.</p>
HTML,
            ],
        ],
    ],
    [
        'name' => 'Resources',
        'handle' => 'resources',
        'description' => 'Listen, read, and download resources to support faith and church life.',
        'content' => <<<'HTML'
<h2>Resources</h2>
<p>This section gathers together practical resources that support worship, discipleship, and everyday church life. Some pages are for the whole church, while others are more specific to teams or families.</p>
HTML,
        'children' => [
            [
                'name' => 'Sermons',
                'handle' => 'sermons',
                'description' => 'Recent teaching and sermon series from Millbrook Church.',
                'content' => <<<'HTML'
<h2>Recent Teaching</h2>
<p>Our sermons aim to open Scripture clearly and help people respond to God in everyday life. This page is a good place to catch up on recent teaching, revisit a series, or share a message with someone else.</p>

<h3>Recent Series</h3>
<ul>
  <li>Following Jesus In Everyday Life</li>
  <li>Psalms For Ordinary Seasons</li>
  <li>Practices Of Grace</li>
  <li>Stories Jesus Told</li>
</ul>
HTML,
            ],
            [
                'name' => 'Rotas',
                'handle' => 'rotas',
                'description' => 'Schedules and serving information for teams across the church.',
                'content' => <<<'HTML'
<h2>Rotas</h2>
<p>Rotas help serving teams stay organised and prepared. This page can be used for rota downloads, reminders, and short notes for volunteers involved in different parts of church life.</p>

<h3>Team Areas</h3>
<ul>
  <li>Welcome and hospitality</li>
  <li>Worship and production</li>
  <li>Children’s teams</li>
  <li>Setup and practical support</li>
</ul>
HTML,
            ],
            [
                'name' => 'Devotionals',
                'handle' => 'devotionals',
                'description' => 'Short reflections and resources to encourage faith through the week.',
                'content' => <<<'HTML'
<h2>Devotionals</h2>
<p>These devotional resources are designed to help individuals and families keep turning to Scripture and prayer throughout the week. Some are seasonal, while others support particular ministries or age groups.</p>
HTML,
                'children' => [
                    [
                        'name' => 'Kids Club at Home',
                        'handle' => 'kids-club-at-home',
                        'description' => 'Faith-at-home resources for children and families.',
                        'content' => <<<'HTML'
<h2>Kids Club At Home</h2>
<p>Kids Club at Home is a simple way for families to keep exploring faith together beyond church gatherings. These resources are designed to be accessible, practical, and easy to use in ordinary family life.</p>

<h3>What You Might Find</h3>
<ul>
  <li>Bible stories and simple discussion prompts</li>
  <li>Printable activities and memory verses</li>
  <li>Prayer ideas for children and parents</li>
  <li>Creative ways to keep learning about Jesus together</li>
</ul>
HTML,
                    ],
                ],
            ],
            [
                'name' => 'Policies',
                'handle' => 'policies',
                'description' => 'Important church policies and safeguarding-related documents.',
                'content' => <<<'HTML'
<h2>Policies</h2>
<p>This page can be used to make important church documents easy to access. Policies help communicate how we seek to care well for people, act responsibly, and create safe environments for ministry.</p>

<h3>Documents You Might Find Here</h3>
<ul>
  <li>Safeguarding policy</li>
  <li>Data protection information</li>
  <li>Health and safety guidance</li>
  <li>Volunteer and team-related policies</li>
</ul>
HTML,
            ],
        ],
    ],
    [
        'name' => 'Giving',
        'handle' => 'giving',
        'description' => 'Why we give and how generosity supports the life of the church.',
        'content' => <<<'HTML'
<h2>Why We Give</h2>
<p>Giving is one of the ways we worship God, invest in the life of the church, and support ministry within the local community. We are grateful for everyone who gives regularly, occasionally, or in response to a particular need.</p>

<h3>What Giving Supports</h3>
<ul>
  <li>Sunday worship and weekly church life</li>
  <li>Children’s, youth, and pastoral ministry</li>
  <li>Resources and support for discipleship</li>
  <li>Mission, outreach, and community engagement</li>
</ul>

<blockquote>
  <p>We want generosity at Millbrook to be thoughtful, transparent, and rooted in worship rather than pressure.</p>
</blockquote>
HTML,
    ],
    [
        'name' => 'Contact',
        'handle' => 'contact',
        'description' => 'Get in touch, ask a question, or find out where and when we meet.',
        'content' => <<<'HTML'
<h2>Get In Touch</h2>
<p>If you have a question about church life, want to plan a visit, or need to contact a ministry leader, we would love to hear from you. This page can help people find the right first step.</p>

<h3>Ways To Reach Us</h3>
<ul>
  <li>Email: info@millbrookchurch.example</li>
  <li>Sunday worship: 11:00am</li>
  <li>Location: Millbrook Community Centre</li>
  <li>Use the contact form for general enquiries</li>
</ul>
HTML,
    ],
    [
        'name' => 'Shipwrecked',
        'handle' => 'shipwrecked',
        'description' => 'A seasonal event page for children, families, and community outreach.',
        'content' => <<<'HTML'
<h2>Shipwrecked</h2>
<p>Shipwrecked is a themed church event designed to welcome children and families into a joyful, imaginative environment where faith can be explored in accessible and memorable ways.</p>

<h3>What The Page Could Include</h3>
<ul>
  <li>Dates, times, and registration details</li>
  <li>An overview of activities and age ranges</li>
  <li>Volunteer information and parent FAQs</li>
  <li>Photos or highlights from previous years</li>
</ul>
HTML,
    ],
    [
        'name' => 'Kingdom Kids',
        'handle' => 'kingdom-kids',
        'description' => 'A dedicated children’s ministry page with a clear family-friendly focus.',
        'content' => <<<'HTML'
<h2>Kingdom Kids</h2>
<p>Kingdom Kids is a space where children can learn about Jesus, build friendships, and enjoy church as a place of belonging. This page can introduce the ministry, explain what happens, and help families feel confident about getting involved.</p>

<h3>Highlights</h3>
<ul>
  <li>Engaging Bible teaching for children</li>
  <li>A warm, safe, and encouraging environment</li>
  <li>Support for families who are new to church</li>
  <li>Fun activities that help faith feel real and memorable</li>
</ul>
HTML,
    ],
    [
        'name' => 'Jobs',
        'handle' => 'jobs',
        'description' => 'Opportunities to serve the church through employed roles and vacancies.',
        'content' => <<<'HTML'
<h2>Jobs</h2>
<p>From time to time, vacancies may open within the life of the church. This page can be used to share current opportunities, application information, and a brief sense of the culture and calling of the role.</p>

<h3>What To Include</h3>
<ul>
  <li>Role description and key responsibilities</li>
  <li>Hours, contract details, and closing date</li>
  <li>Application pack and contact information</li>
  <li>A short summary of the vision behind the role</li>
</ul>
HTML,
    ],
];

$processBranch = static function (Page $parent, array $branch) use (&$processBranch, $findOrCreatePage, $seedMainArea): void {
    $page = $findOrCreatePage($parent, $branch);

    if (!empty($branch['content'])) {
        $seedMainArea($page, $branch['content']);
    }

    foreach ($branch['children'] ?? [] as $child) {
        $processBranch($page, $child);
    }
};

foreach ($tree as $branch) {
    $processBranch($root, $branch);
}

$output->writeln('<info>Demo sitemap build complete.</info>');
