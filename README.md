# EG Social Timeline

[![Version](https://img.shields.io/badge/Version-1.8.0-green)](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-blue.svg)](LICENSE.md)
[![WordPress](https://img.shields.io/badge/WordPress-5.0+-orange.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net)

WordPress plugin to display a unified chronological timeline of your social activity from **Mastodon**, **Diggita** (Lemmy), **PeerTube**, **Forgejo/Gitea** and **Bluesky**.

---

## Features

- **Unified Timeline**: aggregates posts from multiple platforms in chronological order
- **Supported platforms**:
  - **Mastodon** (and ActivityPub-compatible servers)
  - **Diggita** (Lemmy) with full statistics
  - **Forgejo/Gitea** (repository commits)
  - **Bluesky** (public ATP API, no authentication required)
  - **PeerTube** (public REST API, videos from your account)
- **Per-platform limits**: prevents a single platform from monopolizing the timeline
- **Interactive filters**: pure-CSS filter system to show/hide platforms
- **Modular icon system**: SVG icons loaded from files, easy to customize
- **Smart cache**: reduces API requests with a configurable cache
- **Interaction stats**: shows likes, boosts and comments for each post
- **Responsive**: design optimized for desktop, tablet and mobile
- **Color scheme**: always light (default), always dark, or follow the visitor browser
- **Configurable backgrounds**: timeline background independent from the card background, with separate light and dark colors
- **Privacy-friendly**: public data only, no tracking

---

## Screenshot

![EG Social Timeline — frontend](assets/screenshot-1.png)

---

## Installation

### Automatic (WordPress)

1. Download the latest release from [Forgejo](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)
2. Go to **Plugins → Add New → Upload Plugin**
3. Select the downloaded ZIP file
4. Click **Install** and then **Activate**

### Manual (FTP/SSH)

```bash
cd wp-content/plugins
git clone https://git.emanuelegori.uno/emanuelegori/eg-social-timeline.git
```

### EG Forgejo Updater (Recommended)

Install [EG Forgejo Updater](https://git.emanuelegori.uno/emanuelegori/eg-forgejo-updater) for automatic updates from Forgejo.

---

## Configuration

1. Go to **Settings → EG Social Timeline**
2. Configure at least one profile:
   - **Mastodon**: full profile URL (e.g. `https://mastodon.uno/@emanuelegori`)
   - **Diggita**: username (without @)
   - **Forgejo**: username + instance URL (e.g. `https://git.emanuelegori.uno`)
   - **Bluesky**: handle (e.g. `emanuele.bsky.social`, without @)
   - **PeerTube**: account name + instance URL (e.g. `emanuelegori` + `https://peertube.uno`)
3. Configure per-platform limits (optional):
   - Max Mastodon posts (default: 20, 0 = unlimited)
   - Max Diggita posts (default: 10, 0 = unlimited)
   - Max Forgejo commits (default: 5, 0 = unlimited)
   - Max Bluesky posts (default: 10, 0 = unlimited)
   - Max PeerTube videos (default: 5, 0 = unlimited)
4. Adjust cache and display settings
5. Adjust the **Appearance** section (optional):
   - Color scheme: always light (default), always dark, or follow the visitor browser
   - Timeline background: transparent (default), neutral preset or custom colors
   - Card background: automatic (default) or custom colors
   - Platform icon style: brand colors (default) or monochrome
6. Save

![Profile configuration](assets/screenshot-2.png)
![Per-platform post limits](assets/screenshot-3.png)

---

## Usage

### Basic shortcode

```
[eg_social_timeline]
```

### With custom limit

```
[eg_social_timeline limit="20"]
```

### Full example

```
<h2>My recent activity</h2>
[eg_social_timeline limit="50"]
```

---

## Platform filters

Built-in CSS filter system:

```
┌──────────────────────────────────────┐
│ Filter by platform:                  │
│ ☑ Mastodon (12) ☑ Diggita (8)       │
│ ☑ Forgejo (5)   ☐ Bluesky (2)       │
└──────────────────────────────────────┘
```

Click a checkbox = posts shown/hidden instantly (zero JavaScript required!)

---

## Customization

### Platform icons

Icons are SVG files inside `social-icons/`:

```
social-icons/
├── mastodon.svg
├── diggita.svg
├── forgejo.svg
├── bluesky.svg
└── blog.svg
```

**To customize:**
1. Replace the SVG file with your icon
2. Keep size at 24x24px and `viewBox="0 0 24 24"`
3. Use `fill="currentColor"` to inherit the surrounding color

### Custom CSS

Every color comes from a custom property declared on the `.eg-social-timeline` container, so you can retheme the whole timeline by redefining the tokens instead of overriding each rule:

```css
/* Retheme the light scheme */
.eg-social-timeline.egst-scheme-light {
    --egst-canvas: #f0f4ff;        /* background behind the cards */
    --egst-card-bg: #ffffff;       /* card background            */
    --egst-card-border: #d8deff;
    --egst-text: #1f2937;          /* post text                  */
    --egst-brand: #6364ff;         /* checkboxes, boost accent   */
    --egst-icon-mastodon: #563acc; /* one token per platform     */
}

/* Same tokens for the dark scheme */
.eg-social-timeline.egst-scheme-dark {
    --egst-canvas: #0b1020;
    --egst-card-bg: #1f2937;
}
```

The full token list is at the top of `eg-social-timeline.css`. The `egst-scheme-auto` class carries the tokens applied when the browser asks for a dark theme.

Plain rules still work, of course:

```css
/* Customize post cards */
.timeline-item {
    background: #YOUR_BG;
}
```

---

## Development

### Requirements

- WordPress 5.0+
- PHP 7.4+
- API access to the configured platforms

### File structure

```
eg-social-timeline/
├── eg-social-timeline.php    # Main plugin
├── eg-social-timeline.css     # Styles
├── social-icons/              # SVG icons
│   ├── mastodon.svg
│   ├── diggita.svg
│   ├── forgejo.svg
│   └── bluesky.svg
├── languages/                 # Translations
├── README.md
├── readme.txt                 # WordPress readme
└── LICENSE
```

### APIs used

- **Mastodon**: `/api/v1/accounts/{id}/statuses`
- **Diggita**: RSS `/feeds/u/{username}.xml` (with stats parsing)
- **Forgejo**: `/api/v1/users/{username}/repos` + `/api/v1/repos/{owner}/{repo}/commits`
- **Bluesky**: `https://public.api.bsky.app/xrpc/app.bsky.feed.getAuthorFeed` (public, no token required)

---

## Changelog

### [1.8.0] - 2026-09-17

#### Added
- New **Appearance** settings section: color scheme (always light / always dark / follow the visitor browser), timeline background, card background and platform icon style.
- The timeline background is configurable independently from the card background, with separate colors for the light and the dark scheme.
- Selectable icon style: brand colors or monochrome (monochrome icons follow the text color, so they turn white on dark cards).

#### Fixed
- Platform icons were always black: the Simple Icons SVG files carry no `fill` attribute, so the `color` rules had no effect and the icons became invisible on dark cards. Icons now use `fill: currentColor`, are colored per platform on the filter chips too, and use lightened brand colors on the dark scheme.

#### Changed
- ⚠️ **The dark theme is no longer forced by the browser.** The default is now "always light": the `prefers-color-scheme` media query applies only when you choose "follow the visitor browser". Pick that option to restore the 1.7.2 behaviour.
- Every color moved to custom properties (`--egst-*`) declared on the `.eg-social-timeline` container, so custom CSS can retheme the plugin from a single place.
- The "no posts available" message is now wrapped in the timeline container and inherits the chosen colors.
- `readme.txt`: `Tested up to` bumped to WordPress 7.1.

#### Accessibility
- The `<title>` element of the SVG icons is no longer stripped by sanitization, so each icon keeps its accessible name.

### [1.7.2] - 2026-07-05

#### Fixed
- Image previews now fill the card width consistently across all platforms — small source images (e.g. Mastodon `preview_url` thumbnails) were rendered at their reduced natural size while larger ones (Bluesky, PeerTube) filled the card; added `width: 100%` to `.post-image img`

### [1.7.1] - 2026-07-05

#### Added
- Image previews for Bluesky posts, on par with Mastodon: the first post image (direct image embed or quote-post with media) is shown when "Show Image Previews" is enabled, alt text included. Thumbnails of external link cards are intentionally ignored

#### Note
- No new settings: reuses the existing "Show Image Previews" toggle

### [1.7.0] - 2026-07-05

#### Added
- PeerTube integration through the public REST API (`GET /api/v1/accounts/{account}/videos`, no authentication required): just set the account name and instance URL in the settings
- Thumbnail previews for PeerTube videos (honouring the "Show Image Previews" option) and a dedicated "Watch video" link label
- Per-platform limit for PeerTube videos (default 5), platform filter, dedicated icon and brand colour

#### Security
- The PeerTube instance URL is accepted over HTTPS only, with anti-SSRF validation (private/reserved hosts rejected)

#### Changed
- Regenerated the `.pot` and realigned the Italian translation (`it_IT` .po/.mo) with the new strings

### [1.6.7] - 2026-06-18

#### Changed
- Translated to English the last 3 source strings still in Italian in the "Usage" section of the admin panel (the rest of the plugin was already in English since 1.6.6)
- Regenerated the `.pot` with `wp i18n make-pot` and realigned the Italian translation (`it_IT` .po/.mo) with the current source: added missing strings, fixed wrong fuzzy entries and translated the Description

#### Note
- No functional, database or settings changes

### [1.6.6] - 2026-06-02

#### Fixed
- translators: comments added to all i18n strings with placeholders
- strip_tags() replaced with wp_strip_all_tags() (×4)
- date() replaced with gmdate() for timezone safety
- wp_unslash() + sanitize_text_field() added to nonce verification
- error_log() calls marked with phpcs:ignore (already gated by EG_SOCIAL_TIMELINE_DEBUG)
- phpcs:ignore on SVG icon output (hardcoded, sanitized internally)
- esc_html() added to EG_SOCIAL_TIMELINE_VERSION constant output

#### Removed
- load_plugin_textdomain() — not needed since WP 4.6+ with compiled .mo files

#### Changed
- tags reduced to 5 (Plugin Check limit)


### [1.6.1] - 2026-06-02

#### Fixed
- Plugin action links now use English msgids (`Settings`, `Documentation`) — correctly translated to Italian via `it_IT.mo`
- Added `rel="noopener noreferrer"` to the Documentation action link

### [1.6.0] - 2026-06-02

#### Changed
- **Full i18n refactor**: all PHP strings now use English msgids (WordPress convention)
- `it_IT.po`/`.mo` rebuilt with proper English→Italian translations
- `en_US.po`/`.mo` removed — English is now the native fallback

### [1.5.3] - 2026-06-02

#### Fixed
- "Filter by platform:" (`Filtra per piattaforma:`) wrapped in `esc_html_e()` and added to `it_IT.po`/`.mo`

### [1.5.1] - 2026-06-02

#### Fixed
- `translators:` comment moved immediately above `esc_html__()` call (PHPCS compliance)
- `readme.txt` translated to English (Plugin Check compliance)

### [1.5.0] - 2026-06-02

#### Fixed
- All `strip_tags()` replaced with `wp_strip_all_tags()`
- `date()` replaced with `gmdate()` for timezone safety
- Nonce verification uses `wp_unslash()` + `sanitize_text_field()`
- `error_log()` calls marked with `phpcs:ignore` (already gated by `EG_SOCIAL_TIMELINE_DEBUG`)
- `esc_html()` added to `EG_SOCIAL_TIMELINE_VERSION` constant output

#### Removed
- `load_plugin_textdomain()` — no longer needed since WP 4.6+

### [1.4.6] - 2026-05-25

#### Changed
- readme.txt rewritten with clearer structure
- Tested up to bumped to WordPress 7.0
- EG Forgejo Updater replaces Git Updater in installation instructions

### [1.4.5] - 2026-05-25

#### Security
- HTTPS validation on the Forgejo instance URL during sanitization

#### Added
- Frontend, configuration and post-limits screenshots

### [1.4.4] - 2026-05-24

#### Fixed
- Forgejo: client-side repo sorting by `updated_at` using `usort()` — the `sort=recentupdate` parameter is not supported by the `/users/{username}/repos` endpoint

### [1.4.3] - 2026-05-24

#### Added
- Image preview support with new admin option (default: disabled)
- Mastodon: extraction of the first image from `media_attachments` with `preview_url` and alt text
- `loading="lazy"` attribute on images

#### Fixed
- Forgejo: repos sorted by last push with `?sort=recentupdate`; only the necessary repos are queried (`min(repo_count, limit)`). Previously the most recent repos were excluded because the API returned repos in creation order

#### Changed
- Unified post structure: `image_url` and `image_alt` fields available across all platforms

### [1.4.2] - 2026-05-24

#### Added
- Italian (`it_IT`) and English (`en_US`) translations with `.pot`, `.po` and `.mo` files

#### Fixed
- "View original post" / "View commit" link always right-aligned in the footer, even without statistics

#### Changed
- Mastodon account ID cache extended from 24 hours to 30 days

### [1.4.1] - 2026-05-02

#### Added
- Configurable post text length from admin (default: 300, range: 50–600, 0 = full text with no limits)

### [1.4.0] - 2026-05-02

#### Added
- Bluesky integration via public ATP API (`app.bsky.feed.getAuthorFeed`)
- New admin field: Bluesky handle (e.g. `emanuele.bsky.social`, without @)
- New admin field: Max Bluesky posts (default: 10, range: 0-100)
- Bluesky platform filter in the timeline (CSS-only)
- Bluesky statistics: likes, reposts, replies
- Repost support, honoring the "Include Boost/Repost" option
- Automatic stripping of the leading `@` from the handle during sanitization

#### Changed
- "At least one profile" validation extended to Bluesky
- Updated admin error messages

### [1.3.1] - 2026-04-06

#### Security
- Added `LIBXML_NONET` to the Diggita RSS feed XML parsing (anti-XXE)
- Inline SVG sanitization with `wp_kses()` in `eg_social_timeline_get_icon()` (anti-XSS)
- Anti-SSRF validation on external API URLs: new function `eg_social_timeline_is_public_url()` rejects private, reserved and localhost IPs

#### Fixed
- Added missing `esc_url()` on admin links inside the shortcode

### [1.3.0] - 2026-01-11

#### Added
- Per-platform configurable limits in admin settings
- New fields: Max Mastodon posts, Max Diggita posts, Max Forgejo commits
- Value 0 = no limit (v1.2.x behavior)
- More balanced timeline: prevents a single platform from monopolizing it

#### Changed
- Fetch logic modified to honor per-platform limits
- Forgejo: TOTAL commit limit instead of per-repo
- Sensible defaults: Mastodon 20, Diggita 10, Forgejo 5
- Total timeline limit raised: 1-100 (was 1-50)

### [1.2.5] - 2026-01-11

#### Fixed
- Diggita stats: `<br>` tags converted to `\n` before `strip_tags()` for correct parsing
- Forgejo repository name: now visible in the timeline ("Commit to {repo}: {message}")
- Filter buttons: automatic width fixes inconsistent heights
- Filter icons: uniform sizes without distortion

#### Changed
- CSS: removed fixed width on filter buttons (was 145px → auto)
- CSS: removed `object-fit: contain` on icons for uniform rendering
- Diggita: parsing uses `str_replace` for `<br>` before `strip_tags()`

### [1.2.4] - 2026-01-11

#### Fixed
- Diggita: robust HTML parsing with `strip_tags()`
- Forgejo: repo name included in the content

### [1.2.3] - 2026-01-11

#### Fixed
- CSS filters: inverted logic
- Forgejo: commits page link
- UI: compact filter box

### [1.2.0-1.2.2] - 2026-01-11
- Forgejo integration, various fixes

### [1.0.0-1.1.2] - 2026-01-10/11
- Initial releases, Mastodon API

---

## Contributing

Contributions are welcome!

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add AmazingFeature'`)
4. Push the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## License

This project is released under the **GPL-2.0-or-later** license.

See the [LICENSE](LICENSE.md) file for full details.

---

## Author

**Emanuele Gori**

- Website: [emanuelegori.uno](https://emanuelegori.uno)
- Mastodon: [@emanuelegori@mastodon.uno](https://mastodon.uno/@emanuelegori)
- Gitea: [git.emanuelegori.uno](https://git.emanuelegori.uno/emanuelegori)

---

## Acknowledgements

- The Mastodon community for the well-documented API
- Diggita.com for the Italian Lemmy platform
- Forgejo/Gitea for the excellent API
- The WordPress community
