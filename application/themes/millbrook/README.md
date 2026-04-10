Millbrook Theme
=================

Overview
--------
This is the current Concrete CMS theme for Millbrook Church. The homepage structure takes its cue from https://anchortacoma.org/, while the branding, typography, and colour system have been adapted to Millbrook's own identity.

The theme currently includes:
- a transparent, sticky header on the homepage hero
- a full-height image-led hero with a blended brand-gradient overlay
- a structured homepage with story, rhythms, teaching, next steps, ministry, visit, and CTA sections
- editable Concrete areas throughout the homepage, header, and footer
- responsive navigation and mobile menu behaviour

Brand System
------------
Typography:
- Headings and titles use `Syne`
- Body copy uses `Figtree`

Primary brand colours:
- `Midnight Slate`: `#35485e`
- `Legacy Blue`: `#298fc2`
- `Soft Plum`: `#866eaf`
- `Coral`: `#ec5e61`
- `Mist`: `#f5f4f1`
- `Charcoal`: `#2f3438`
- `Soft Lime`: `#c5d32d`

Usage notes:
- `Soft Lime` should be used sparingly, mostly for children's ministry accents
- `Coral` is available as a warm supporting accent and is currently used lightly in the homepage hero overlay
- `Midnight Slate` is still part of the system, but the hero has been tuned to rely more on blue, plum, and coral than on heavy dark overlays

CSS tokens
----------
The main theme variables live in `css/main.css`.

Current key tokens:
- `--brand-primary: #298fc2`
- `--brand-primary-dark: #35485e`
- `--brand-secondary: #866eaf`
- `--brand-coral: #ec5e61`
- `--brand-highlight: #c5d32d`
- `--brand-ink: #2f3438`
- `--surface: #f5f4f1`

Theme files
-----------
Key theme files:
- `home.php` — custom homepage structure and fallback content
- `elements/header.php` — header, utility bar, navigation, mobile toggle
- `elements/footer.php` — footer content and lower navigation/contact blocks
- `elements/hero.php` — default inner-page hero
- `default.php` — default page wrapper
- `view.php` — standard view wrapper
- `page_templates/home_full.php` — full-width homepage template variant
- `page_templates/home_pixel.php` — alternate homepage template variant
- `css/main.css` — typography, colours, layout, sections, responsive styles
- `js/main.js` — mobile menu and sticky/transparent header behaviour

Homepage structure
------------------
The homepage currently follows this sequence:
1. Hero
2. What to Expect
3. About Millbrook
4. Life at Millbrook
5. Plan Your Visit

Homepage editable areas
-----------------------
Fallback content exists in code, but these areas can all be overridden in Concrete:

- `Home Hero Content`
- `Home Vision Intro`
- `Home Vision Content`
- `Home Community Heading`
- `Home Community Intro`
- `Home Community Cards`
- `Home Ministries Heading`
- `Home Ministries Cards`
- `Home Visit Card`
- `Home Contact Card`
- `Home Quick Links Card`

Shared/global editable areas:
- `Top Bar Left`
- `Top Bar Right`
- `Header Actions`
- `Footer - Column 1`
- `Footer - Column 2`
- `Footer - Column 3`
- `Footer - Column 4`

Notes
-----
- The visible "skip to content" button was removed from the visual UI and is now screen-reader-only
- Repeated wheel-mark treatments were removed from the homepage so the logo primarily appears in the header
- The homepage hero background uses the image at `images/hero.png` with a blended gradient overlay rather than a flat colour wash

Getting started
---------------
1. In Concrete CMS, go to `Pages & Themes` -> `Themes` and make sure `Millbrook` is installed and active.
2. Edit the header and footer global areas for navigation, logo, and footer content.
3. Edit the homepage areas listed above to replace fallback copy with final Millbrook content.
4. Adjust `css/main.css` if you want to refine colour balance, spacing, or component styling further.

Content seeding
---------------
For structured, deployable content we use Concrete CLI seed scripts rather than database deploys.

Available seed commands from the project root:
- `npm run seed:help`
- `npm run seed:inspect`
- `npm run seed:demo-sitemap`
- `npm run seed:new-here`
- `npm run seed:all`

What they do:
- `seed:inspect` prints the current sitemap, page types, and templates
- `seed:demo-sitemap` creates/updates the demo sitemap and seeds empty pages
- `seed:new-here` rebuilds the `New Here` page body with Concrete content blocks
- `seed:all` runs the main content seeds in sequence

Deploy workflow
---------------
1. Deploy code via git
2. Run any needed seed command on the target environment
3. Review the updated pages in Concrete

Use seeds for repeatable starter/structured content only.
Use the CMS itself for ongoing editorial/live content changes.

Local development
-----------------
- Local site URL: `http://millbrookchurch.xyz/`
- Local environment: Laravel Herd
