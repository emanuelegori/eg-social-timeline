=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, bluesky, forgejo, social, timeline
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.5.2
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Unified chronological timeline of your public activity from Mastodon, Bluesky, Forgejo and Diggita. Zero JavaScript, zero tracking.

== Description ==

EG Social Timeline aggregates in chronological order your public posts from four decentralized platforms and displays them in a single timeline via shortcode.

= Supported platforms =

- Mastodon (and in theory any ActivityPub-compatible instance, untested)
- Bluesky (public API, no authentication required)
- Forgejo and Gitea (commits from your public repositories)
- Diggita (Italian Lemmy platform)

= Key features =

- Interactive per-platform filters, no JavaScript required
- Image previews for Mastodon posts (optional)
- Per-platform configurable limits for a balanced mix
- Interaction stats: likes, boosts, comments
- Configurable cache (30 minutes - 24 hours)
- Responsive design with automatic dark mode support
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
- OR Forgejo username + instance URL
- OR Diggita username

= Advanced configuration =

- Per-platform post limits (avoids one platform monopolizing the timeline)
- Image previews for posts with attachments (Mastodon)
- Text length for each post (50-600 characters, 0 = full text)
- Include or exclude boosts and reposts
- Show or hide interaction stats
- Cache duration from 30 minutes to 24 hours

== Frequently Asked Questions ==

= Which platforms are supported? =

Mastodon (and ActivityPub-compatible instances), Bluesky, Forgejo/Gitea and Diggita.

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

= Can I customize the style? =

Yes. The icons are SVG files you can replace in the `social-icons/` folder. You can also add custom CSS from your theme to change colors and layout.

== Screenshots ==

1. Unified frontend timeline with posts from Mastodon, Bluesky, Forgejo and Diggita
2. Admin settings panel — social profiles configuration
3. Admin settings panel — per-platform post limits

== Changelog ==

= 1.5.2 - 2026-06-02 =
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

= 1.5.2 =
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

This plugin connects to the social platforms the user explicitly configures in the settings. No external service is contacted until the user fills in at least one profile field (Mastodon URL, Bluesky handle, Forgejo username + instance URL, or Diggita username). All requests are HTTP GET requests for public content; no user credentials are sent.

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
- **Data received and stored**: public post metadata (text, date, link, like/repost/reply counts). Cached locally as a WordPress transient.
- Terms of service: https://bsky.social/about/support/tos
- Privacy policy: https://bsky.social/about/support/privacy-policy

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
