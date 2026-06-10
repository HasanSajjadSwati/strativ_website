# Strativ WordPress Website — Design Spec

**Date:** 2026-06-10
**Status:** Approved by user

## Overview

A modern, animated, professional marketing website for **Strativ**, a software house / IT service provider (AI tools, POS, CMS, HMS, web & mobile applications). Red-and-black dark theme, 2026-style design. Built as a custom WordPress theme that is Elementor-compatible, running on a local Laragon environment.

## Goals

- Slick, professional, animation-rich dark site that positions Strativ as a premium software partner.
- Full multi-page corporate structure including a filterable project portfolio with detail pages.
- All content editable via WP admin; pages optionally editable with Elementor later.
- Placeholder content throughout, swappable by the user later.

## Environment & Stack

| Layer | Choice |
|---|---|
| Local stack | Laragon (winget install): Apache, PHP 8.x, MySQL |
| Site URL | `http://strativ.test` (Laragon auto vhost) |
| WordPress | Latest, installed via WP-CLI |
| Theme | Custom classic PHP theme `strativ` (not FSE) |
| Data plugin | `strativ-core` companion plugin for CPTs |
| Plugins | Elementor (free), Advanced Custom Fields (free), Contact Form 7 |
| JS/animation | GSAP 3 + ScrollTrigger (bundled locally in theme), vanilla JS, no build step |
| Fonts | Space Grotesk (headings), Inter (body) |

### Elementor compatibility requirements

- `add_theme_support('elementor')` conventions: full-width page template (`template-fullwidth.php`) with no theme containers.
- Content width set via Elementor settings; theme uses standard `the_content()` so Elementor can take over any page.
- Theme styles scoped so Elementor-edited pages don't fight theme CSS.

## Visual Language ("Premium Sleek" base + "Neon Tech" accents)

- **Backgrounds:** near-black `#0b0b0d` base, secondary surface `#121214`, card surface `rgba(255,255,255,0.04)` with 1px `rgba(255,255,255,0.07)` borders.
- **Accent:** red gradient `#c41230 → #ff1e3c`; glow shadows `rgba(255,30,60,0.3-0.6)`.
- **Text:** white headings, `#8a8a92` muted body text.
- **Effects:** floating blurred glow orbs (hero), glassmorphism cards (backdrop-blur), red glow on hover, gradient text on key headlines, sticky glass navbar (blur), thin gradient divider lines.
- **Layout:** generous spacing, refined centered hero, max-width ~1200px containers, consistent section rhythm.
- All colors/spacing defined as CSS custom properties in `:root` (design tokens).

## Architecture

### Theme structure (`wp-content/themes/strativ/`)

```
style.css                 (theme header + tokens)
functions.php             (setup, enqueues, menus, theme supports)
inc/                      (template helpers, ACF field registration, CF7 styling, walker)
assets/css/main.css       (full design system)
assets/js/main.js         (GSAP animations, nav, filters, counters)
assets/js/vendor/         (gsap.min.js, ScrollTrigger.min.js)
header.php / footer.php
front-page.php            (Home)
page-about.php, page-services.php, page-contact.php, page-careers.php  (page templates)
archive-project.php       (Portfolio grid + filters)
single-project.php        (Project detail)
single-career.php         (Job detail)
home.php / single.php     (Blog index / single post)
template-fullwidth.php    (Elementor canvas-style template)
404.php, searchform.php
```

### `strativ-core` plugin

- CPT `project`: supports title, editor, excerpt, thumbnail. Taxonomy `project_category` (AI, POS, CMS, HMS, Web, Mobile, plus E-commerce/SaaS as needed).
- CPT `career`: title, editor, meta (location, type, salary range).
- Registered in plugin (not theme) so data survives theme switches.

### Custom fields (ACF, registered in code)

- Project: client name, year, tech stack (list), live URL, challenge, solution, results, gallery.
- Career: location, employment type, department.

## Pages & Sections

| Page | Sections |
|---|---|
| **Home** (`front-page.php`) | 1. Hero: gradient headline, sub-text, dual CTAs, floating glow orbs, parallax. 2. Trust strip: client logo marquee. 3. Services: 6 glass cards with icons. 4. Featured work: 4 projects. 5. Stats: animated counters (projects, clients, years, team). 6. Process: 4-step timeline. 7. Testimonials. 8. Latest insights: 3 blog cards. 9. CTA banner. |
| **About** | Story hero, mission/values cards (3-4), team grid (placeholder members), stats reprise |
| **Services** | Intro hero + 6 detailed alternating service blocks: AI Solutions, Web & SaaS Development, Mobile Apps, POS Systems, CMS Platforms, HMS/Healthcare Software |
| **Portfolio** | Filterable grid by `project_category` (JS filter, no reload); project cards with image zoom hover |
| **Project detail** | Hero image, meta sidebar (client, year, stack, URL), challenge/solution/results, gallery, prev/next project nav |
| **Blog** | Dark card grid (native posts), single template with styled typography |
| **Careers** | Culture intro, perks, open positions list with detail pages |
| **Contact** | CF7 form styled to theme, info cards (email, phone, address), map placeholder |

Header: sticky glass nav with logo, menu, glowing "Get in touch" CTA; mobile hamburger with slide-in panel.
Footer: 4-column (brand + blurb, quick links, services, contact/social), bottom bar.

## Animations (GSAP + ScrollTrigger)

- Scroll-triggered fade-up reveals with stagger on all card grids.
- Hero: glow orbs slow-float (infinite), headline/CTA entrance timeline, subtle mouse parallax.
- Animated number counters on stats (trigger once on scroll).
- Infinite logo marquee (CSS animation).
- Magnetic hover effect on primary buttons; red glow intensify on card hover.
- Portfolio image zoom + overlay slide on hover; JS category filtering with fade transitions.
- Preloader: brief logo animation, then page fade-in.
- Smooth anchor scrolling; navbar background appears on scroll.
- Respect `prefers-reduced-motion`: disable non-essential animation.

## Placeholder Content

- **Logo:** text-based SVG "STRATIV." (red dot accent).
- **Projects (8):** AI customer-support copilot, AI document-intelligence platform, retail POS suite, restaurant POS, enterprise CMS, hospital HMS, e-commerce platform, fitness mobile app — each with full ACF fields and category.
- **Services (6):** as listed above, each with copy.
- **Blog posts (3):** professional tech articles.
- **Careers (3):** e.g., Senior Full-Stack Engineer, AI/ML Engineer, UI/UX Designer.
- **Images:** generated placeholder images (CSS/SVG gradients or solid placeholders) — no copyrighted assets.

## Error Handling & Edge Cases

- Empty states: portfolio/blog/careers templates show a styled "nothing here yet" message when no posts exist.
- 404 page styled to theme.
- Theme functions guard against missing plugins (ACF `get_field` wrapped; CPT templates check `post_type_exists`).
- Mobile-first responsive: breakpoints ~640/960/1200px; hamburger nav below 960px.

## Testing & Verification

- WP-CLI smoke checks (site up, pages created, CPT counts).
- Playwright (webapp-testing skill) screenshots of every page at desktop + mobile widths to verify layout/animations render.
- Verify Elementor can open a page with the full-width template without breakage.

## Out of Scope

- Real hosting/deployment, real content, SEO plugin configuration, multilingual, e-commerce.
