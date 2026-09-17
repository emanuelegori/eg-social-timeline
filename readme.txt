=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, bluesky, forgejo, social, timeline
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.8.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Unified chronological timeline of your public activity from Mastodon, Bluesky, PeerTube, Forgejo and Diggita. Zero JavaScript, zero tracking.

== Description ==

EG Social Timeline aggregates in chronological order your public posts from five decentralized platforms and displays them in a single timeline via shortcode.

= Supported platforms =

- Mastodon (and in theory any ActivityPub-compatible instance, untested)
- Bluesky (public API, no authentication required)
- PeerTube (public REST API, videos from your account)
- Forgejo and Gitea (commits from your public repositories)
- Diggita (Italian Lemmy platform)

= Key features =

- Interactive per-platform filters, no JavaScript required
- Image previews for Mastodon and Bluesky posts and PeerTube video thumbnails (optional)
- Per-platform configurable limits for a balanced mix
- Interaction stats: likes, boosts, comments
- Configurable cache (30 minutes - 24 hours)
- Timeline and card backgrounds configurable independently, each with its own color
- Text, borders and icons derived from the contrast of the color you pick, so they stay readable
- Responsive design
- Shortcode with optional limit parameter
- Modular and customizable SVG icons
- Privacy-friendly: public data only, no trackers

= Usage =

Configure the profiles in the settings, then insert the shortcode:

`[eg_social_timeline]`

With a custom limit:

`[eg_social_timeline limit="20"]`

= Privacy =

- Retrieves only public posts from the configured platforms
- No user tracking
- No data sent to third parties
- Local cache in the WordPress database
- No cookies set by the plugin

== Installation ==

1. Upload the files to the `/wp-content/plugins/eg-social-timeline/` directory
2. Activate the plugin from the WordPress Plugins menu
3. Go to Settings → EG Social Timeline
4. Configure at least one social profile
5. Insert `[eg_social_timeline]` into the desired page or post

= Automatic updates =

Install [EG Forgejo Updater](https://git.emanuelegori.uno/emanuelegori/eg-forgejo-updater) to receive automatic updates directly inside WordPress, exactly like plugins from the official repository.

= Minimum configuration =

- Mastodon profile URL (e.g. https://mastodon.uno/@username)
- OR Bluesky handle (e.g. emanuele.bsky.social)
- OR PeerTube account + instance URL (e.g. yourname + https://peertube.uno)
- OR Forgejo username + instance URL
- OR Diggita username

= Advanced configuration =

- Per-platform post limits (avoids one platform monopolizing the timeline)
- Image previews for posts with attachments (Mastodon and Bluesky) and PeerTube video thumbnails
- Text length for each post (50-600 characters, 0 = full text)
- Include or exclude boosts and reposts
- Show or hide interaction stats
- Cache duration from 30 minutes to 24 hours
- Timeline background: transparent (default), neutral preset, follow the visitor browser, or a custom color
- Card background: neutral preset (default), transparent, follow the visitor browser, or a custom color
- Platform icon style: platform colors, or a single color taken from the post text
- Text, borders, badges, links and icons are not configured: they are derived from the contrast of the background you choose, and switch to their light variants on a dark surface

== Frequently Asked Questions ==

= Which platforms are supported? =

Mastodon (and ActivityPub-compatible instances), Bluesky, PeerTube, Forgejo/Gitea and Diggita.

= Do the platform filters require JavaScript? =

No. They use only CSS with the checkbox/label pattern and work even with JavaScript disabled in the browser.

= Can I use the plugin with a single platform? =

Yes. Just configure at least one profile. Unconfigured platforms are simply ignored.

= How do the per-platform limits work? =

Each platform has a configurable limit on the number of posts included in the timeline. This prevents a very active platform (e.g. Forgejo with many commits) from filling all available slots, ensuring a balanced mix.

= Is data private? =

The plugin retrieves only public content. It does not track site visitors and does not send data to third-party services.

= How does the cache work? =

Posts are temporarily stored to reduce calls to the external APIs. You can configure the duration from 30 minutes to 24 hours. The cache is automatically cleared when settings are saved.

= Why does the timeline look dark on my light theme? =

Up to version 1.7.2 the timeline always followed the `prefers-color-scheme` setting of the visitor browser, so it turned dark even on a light theme. Since 1.8.1 the stylesheet carries no `prefers-color-scheme` rule at all: the timeline goes dark only if you set a background to "Follow the visitor browser", or if both backgrounds are transparent and there is no color to measure.

= I picked a dark card background but the text stayed dark. =

That was a bug in 1.8.0, fixed in 1.8.1: contrast was inferred from a global color scheme instead of being measured on the color of the surface. Text, borders, badges, links and icons now follow the background you actually chose.

= Can I customize the style? =

Yes. Settings → EG Social Timeline → Appearance covers the two backgrounds and the icon style. Every color in the stylesheet comes from a custom property declared on the `.eg-social-timeline` container: two source palettes (`--egst-light-*` and `--egst-dark-*`) and the active tokens that read from them (`--egst-text`, `--egst-card-bg`, `--egst-chip-bg`, …), so a few lines of theme CSS are enough to retheme the whole timeline. The icons are SVG files you can replace in the `social-icons/` folder.

== Screenshots ==

![Unified timeline](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/raw/branch/main/assets/screenshot-1.png)
Unified timeline — chronological feed from Mastodon, Bluesky, Forgejo and Diggita.

![Admin settings — profiles](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/raw/branch/main/assets/screenshot-2.png)
Admin settings — social profiles configuration.

![Admin settings — limits](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/raw/branch/main/assets/screenshot-3.png)
Admin settings — per-platform post limits for a balanced mix.

== Changelog ==

= 1.8.1 - 2026-09-17 =
* Fixed: contrast is now measured on the color you choose instead of being inferred from a color scheme. In 1.8.0 a dark card background left the post text, the borders and the icons dark on dark. The plugin now computes the WCAG contrast ratio of the chosen background and moves text, borders, badges, links and icons to the palette that contrasts more.
* Fixed: the icons on the filter chips read their own tokens, so they no longer follow the card. A dark card used to lighten them while they sat on light chips.
* Changed: simpler Appearance section. The "Color Scheme" setting and the light/dark color pairs are gone; each background has one menu (transparent, neutral preset, follow the visitor browser, custom color) and one color.
* Changed: clearer icon labels, "Platform colors" and "Single color (same as the post text)". The SVG icons are monochrome outlines with no color of their own: the stylesheet paints them.
* Added: "Transparent" card background, for a timeline with no card surfaces — only the border delimits each post, and the shadow is dropped.
* Added: a warning in the settings when the chosen color cannot reach the WCAG AA minimum (4.5:1) with either text palette, showing the measured ratio.
* Changed: settings saved with 1.8.0 are converted on read, so nothing is lost; the database is rewritten on your first save.
* Changed: the stylesheet no longer contains any `prefers-color-scheme` rule. The media query is emitted only when a background follows the browser, or when both are transparent.

= 1.8.0 - 2026-09-17 =
* New "Appearance" settings section: color scheme (always light, always dark or follow the visitor browser), timeline background, card background and platform icon style.
* The timeline background is now configurable independently from the card background, with a separate color for the light and the dark scheme.
* Fixed: platform icons were always rendered black. The Simple Icons SVG files carry no `fill` attribute, so the `color` rules in the stylesheet had no effect and the icons became invisible on dark cards. Icons now use `fill: currentColor`, are colored per platform on the filter chips too, and use lightened brand colors on the dark scheme.
* Changed: the dark theme is no longer forced by the browser. The default is "Always light"; the `prefers-color-scheme` media query only applies when you choose "Follow the visitor browser".
* Changed: every color moved to custom properties (`--egst-*`) declared on the timeline container, so custom CSS can retheme the plugin from a single place.
* Changed: the "no posts available" message is now wrapped in the timeline container and inherits the chosen colors.
* Accessibility: the `<title>` element of the SVG icons is no longer stripped by sanitization, so each icon keeps its accessible name.
* Tested up to WordPress 7.1.

= 1.7.2 - 2026-07-05 =
* Fixed: image previews now fill the card width uniformly across all platforms. Small source images (e.g. Mastodon `preview_url` thumbnails) were rendered at their natural, reduced size while larger ones (Bluesky, PeerTube) filled the card; added `width: 100%` to `.post-image img` so all previews are consistent.

= 1.7.1 - 2026-07-05 =
* Added: image previews for Bluesky posts, on par with Mastodon. The first image of a post (direct image embed or quote-post-with-media) is shown when the "Show Image Previews" option is enabled, including its alt text. External link-card thumbnails are intentionally ignored.
* No new settings: reuses the existing "Show Image Previews" toggle.

= 1.7.0 - 2026-07-05 =
* Added: PeerTube integration via the public REST API (`/api/v1/accounts/{account}/videos`, no authentication). Configure your PeerTube account name and instance URL in the settings.
* Added: PeerTube video thumbnails shown as image previews (respects the "Show Image Previews" option) and a dedicated "Watch video" link label.
* Added: per-platform limit for PeerTube videos (default 5), platform filter, brand icon and color.
* Security: PeerTube instance URL accepted over HTTPS only, with anti-SSRF validation (rejects private/reserved hosts).
* Translation sync: regenerated the `.pot` and aligned the Italian translation (`it_IT` .po/.mo) with the new strings.

= 1.6.7 - 2026-06-18 =
* Fixed: three leftover Italian source strings in the admin "Usage" section are now in English (the rest of the plugin was already English)
* Translation sync: regenerated the `.pot` and re-aligned the Italian translation (`it_IT` .po/.mo) with the current source; added the missing strings and the Italian Description
* No functional change, no database or settings change

= 1.6.6 - 2026-06-02 =
* Fixed: `translators:` comments added to all i18n strings with placeholders
* Fixed: `strip_tags()` replaced with `wp_strip_all_tags()` (×4)
* Fixed: `date()` replaced with `gmdate()` for timezone safety
* Fixed: `wp_unslash()` + `sanitize_text_field()` added to nonce verification
* Fixed: `error_log()` calls marked with `phpcs:ignore` (already gated by `EG_SOCIAL_TIMELINE_DEBUG`)
* Fixed: `phpcs:ignore` on SVG icon output (hardcoded, sanitized internally)
* Fixed: `esc_html()` added to `EG_SOCIAL_TIMELINE_VERSION` constant output
* Removed: `load_plugin_textdomain()` — not needed since WP 4.6+ with compiled `.mo` files
* Changed: tags reduced to 5 (Plugin Check limit)

= 1.4.6 - 2026-05-25 =
* Changed: readme.txt rewritten — clearer structure, removed references to obsolete versions
* Changed: Tested up to bumped to WordPress 7.0
* Changed: EG Forgejo Updater replaces Git Updater in the installation instructions

= 1.4.5 - 2026-05-25 =
* Security: HTTPS validation on the Forgejo instance URL during sanitization
* Added: frontend, configuration and post-limits screenshots

= 1.4.4 - 2026-05-24 =
* Fixed: Forgejo: repo sorting by last push date (client-side sort)

= 1.4.3 - 2026-05-24 =
* Added: Mastodon image previews (admin option, disabled by default)
* Fixed: Forgejo: commits now come from the most recently updated repositories

= 1.4.2 - 2026-05-24 =
* Added: Italian and English translations (.pot, .po, .mo files)
* Fixed: "View original post" link always right-aligned
* Changed: Mastodon account ID cache extended to 30 days

= 1.4.1 - 2026-05-02 =
* Added: post text length configurable from admin (50-600, 0 = full text)

= 1.4.0 - 2026-05-02 =
* Added: Bluesky integration via the public ATP API (no authentication)

= 1.3.1 - 2026-04-06 =
* Security: XXE protection, SVG sanitization, anti-SSRF validation

= 1.3.0 - 2026-01-11 =
* Added: per-platform configurable limits

= 1.2.0 - 2026-01-11 =
* Added: Forgejo/Gitea integration

= 1.1.0 - 2026-01-10 =
* Added: migrated Mastodon to API v1, full statistics

= 1.0.0 - 2026-01-10 =
* Initial release: Mastodon and Diggita

== Upgrade Notice ==

= 1.8.1 =
Fixes 1.8.0: contrast is measured on the color you pick, so a dark card background no longer leaves dark text and dark icons on it. The Appearance section is simpler — no more Color Scheme, one colour per background — and card backgrounds can now be transparent.

= 1.8.0 =
New Appearance section: color scheme, timeline and card backgrounds, icon style. Heads up: the timeline no longer turns dark just because the browser is dark — the default is now "Always light". Also fixes icons that were always black.

= 1.7.2 =
Image previews now fill the card width consistently across all platforms.

= 1.7.1 =
Bluesky posts now show image previews (when the option is enabled), like Mastodon.

= 1.7.0 =
New PeerTube integration. Enter your account name and instance URL in the settings.

= 1.6.6 =
Plugin Check compliance fixes. Update recommended.

= 1.4.5 =
Recommended update: security fix on the Forgejo instance URL.

= 1.4.4 =
Forgejo fix: commits shown now come from the most recently updated repositories.

= 1.4.3 =
Mastodon image previews available (admin option). Important fix for Forgejo commit ordering.

= 1.4.2 =
Italian/English translations. Footer link alignment fix.

= 1.4.1 =
Post text length configurable from admin.

= 1.4.0 =
New Bluesky integration. Enter your handle in the settings.

= 1.3.1 =
Important security fixes. Update recommended.

= 1.3.0 =
Per-platform configurable limits. Backward-compatible with v1.2.x.

== External services ==

This plugin connects to the social platforms the user explicitly configures in the settings. No external service is contacted until the user fills in at least one profile field (Mastodon URL, Bluesky handle, PeerTube account + instance URL, Forgejo username + instance URL, or Diggita username). All requests are HTTP GET requests for public content; no user credentials are sent.

Contacted services are cached locally for a configurable duration (30 minutes to 24 hours, default value depends on the admin setting) to minimize external traffic.

= Mastodon =

- **What**: the Mastodon (or ActivityPub-compatible) instance whose URL the user enters in the settings (e.g. `https://mastodon.uno/@username`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered.
- **Endpoints**: `GET /api/v1/accounts/lookup` (account ID lookup, cached 30 days) and `GET /api/v1/accounts/{id}/statuses` (public statuses).
- **Data sent**: only the public username/handle entered in the settings, as a URL parameter.
- **Data received and stored**: public post metadata (text, date, link, media preview URL and alt text if image previews are enabled, like/boost/reply counts). Cached locally as a WordPress transient.
- Mastodon is decentralized: terms of service and privacy policy depend on the specific instance the user chooses and are available on that instance.

= Bluesky =

- **What**: the Bluesky public API at `https://public.api.bsky.app`.
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered.
- **Endpoint**: `GET https://public.api.bsky.app/xrpc/app.bsky.feed.getAuthorFeed?actor={handle}`.
- **Data sent**: only the public handle entered in the settings (e.g. `username.bsky.social`).
- **Data received and stored**: public post metadata (text, date, link, image preview URL and alt text if image previews are enabled, like/repost/reply counts). Cached locally as a WordPress transient.
- Terms of service: https://bsky.social/about/support/tos
- Privacy policy: https://bsky.social/about/support/privacy-policy

= PeerTube =

- **What**: the PeerTube instance the user enters in the settings (e.g. `https://peertube.uno`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered.
- **Endpoint**: `GET /api/v1/accounts/{account}/videos` for the account's public videos.
- **Data sent**: only the public account name entered in the settings, as part of the URL path.
- **Data received and stored**: public video metadata (title, description, date, link, thumbnail URL and like count). Cached locally as a WordPress transient.
- PeerTube is decentralized federated video hosting: terms of service and privacy policy depend on the specific instance the user chooses and are available on that instance.

= Forgejo / Gitea =

- **What**: the Forgejo or Gitea instance the user enters in the settings (e.g. `https://git.example.com`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered.
- **Endpoints**: `GET /api/v1/users/{username}/repos` and `GET /api/v1/repos/{owner}/{repo}/commits` for the user's public repositories.
- **Data sent**: only the public username entered in the settings, as a URL parameter.
- **Data received and stored**: public commit metadata (message, date, repository name, commit hash and link). Cached locally as a WordPress transient.
- Forgejo and Gitea are open-source git hosting platforms. Their terms of service and privacy policy depend on the specific instance the user chooses and are available on that instance.

= Diggita =

- **What**: the Diggita RSS feed at `https://www.diggita.com`.
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered.
- **Endpoint**: `GET https://www.diggita.com/feeds/u/{username}.xml`.
- **Data sent**: only the public username entered in the settings, as part of the URL path.
- **Data received and stored**: public post metadata (title, link, date, vote and comment counts) parsed from the public RSS feed. Cached locally as a WordPress transient.
- Terms of service: https://www.diggita.com/regolamento
- Privacy policy: https://www.diggita.com/privacy

== Privacy Policy ==

EG Social Timeline retrieves only public content from the configured platforms. It does not track site visitors, does not send data to third-party services, and does not set cookies. The cache is local to the WordPress database.

== Support ==

* Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Issues: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
