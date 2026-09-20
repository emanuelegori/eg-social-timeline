=== EG Social Timeline ===
Contributors: emanuelegori
Donate link: https://emanuelegori.uno/en/donate/
Tags: mastodon, bluesky, lemmy, forgejo, timeline
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.15.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Chronological timeline of your public activity across the fediverse, Bluesky, ListenBrainz and any RSS feed. Zero JavaScript, zero tracking.

== Description ==

EG Social Timeline aggregates in chronological order your public posts from five decentralized platforms and displays them in a single timeline via shortcode.

= Supported platforms =

- Mastodon, and the Mastodon-compatible Pleroma and Akkoma (public accounts API, no authentication)
- Bluesky (public API, no authentication required)
- Pixelfed (public Atom feed: photos and captions, no interaction counts)
- PeerTube (public REST API, videos from an account or from a channel)
- Forgejo and Gitea (commits from your public repositories)
- Lemmy, on any instance (public user RSS feed; Diggita is one of them)
- ListenBrainz (public API, no token: what you have been listening to)
- Any RSS 2.0 or Atom feed: a blog, a newsletter, a podcast

Every platform is configured the same way: the instance URL plus your username. Bluesky is the exception, because its public API lives at a single address for everyone.

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

1. In WordPress go to Plugins → Add New and search for "EG Social Timeline"
2. Click Install Now, then Activate
3. Go to Settings → EG Social Timeline
4. Configure at least one social profile
5. Insert `[eg_social_timeline]` into the desired page or post

= Installing from a ZIP =

The source code lives at https://git.emanuelegori.uno/emanuelegori/eg-social-timeline. Download a release, then go to Plugins → Add New → Upload Plugin and select the file.

= Minimum configuration =

- Mastodon, Pleroma or Akkoma: instance URL + username (e.g. https://mastodon.uno + username)
- OR Lemmy: instance URL + username (e.g. https://diggita.com + username)
- OR Pixelfed: instance URL + username (e.g. https://pixelfed.uno + yourname)
- OR PeerTube: instance URL + account or channel (e.g. https://peertube.uno + yourname)
- OR Forgejo/Gitea: instance URL + username
- OR Bluesky: handle alone (e.g. emanuele.bsky.social)
- OR ListenBrainz: username (the API URL is already filled in)
- OR an RSS/Atom feed: the feed address, with an optional label

You can also paste the full profile address into either field and the plugin splits it: `https://lemmy.ml/u/username`, `https://mastodon.uno/@name`, `https://peertube.tv/c/name@host/videos`, `https://bsky.app/profile/name.bsky.social`.

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
- Filter bar style: full (icon, name and count) or compact (icons only, name and count in the tooltip)
- Per-source display options: boosts, statistics, image previews and text length are set inside each platform's own box, and only where they have an effect
- Text, borders, badges, links and icons are not configured: they are derived from the contrast of the background you choose, and switch to their light variants on a dark surface

== Frequently Asked Questions ==

= Which platforms are supported? =

Mastodon, Pleroma, Akkoma, Bluesky, Pixelfed, PeerTube, Forgejo/Gitea and Lemmy (any instance).

= My PeerTube videos are not showing up. =

On PeerTube videos almost always live in a **channel**, not directly in the account, and the two use different API endpoints. Paste the full address of your channel and the plugin sorts it out: `/c/name` is a channel, `/a/name` an account. If you only type a name, both are tried and the answer is remembered.

A second detail: a channel belongs to the instance that hosts it. An address like `https://peertube.tv/c/name@tube.example/videos` means the channel lives on `tube.example` and peertube.tv only federates it — the plugin then queries the origin, which does not depend on the state of federation.

= Can I add a source that is not a social platform? =

Yes. The "RSS or Atom feed" fields take the address of any feed — your blog, a newsletter, a podcast — and its items join the timeline like any other source, with their own filter. The label you give it is the name shown on the cards; left empty, the feed's own title is used.

= What does the ListenBrainz source show? =

Your recent listens: artist and track, with the date. The public API needs no token. Each card links to the recording on MusicBrainz when the identifier is available, otherwise to your ListenBrainz profile. No interaction counts, because listens do not have any.

= Why does Pixelfed show no likes or comments? =

Because its public Atom feed does not carry them. Pixelfed answers the Mastodon-compatible account lookup but redirects the statuses endpoint to its login page, so the feed is the only way to read a profile without credentials: it gives photos, captions, dates and links, and no counts.

= Does the Mastodon field work with every fediverse server? =

No, and it is worth knowing why. The plugin reads the public accounts API (`/api/v1/accounts/lookup` and `/api/v1/accounts/{id}/statuses`), which Mastodon, Pleroma and Akkoma serve without authentication. GoToSocial and Friendica answer those endpoints with HTTP 401, Misskey and Sharkey use a different API, and Pixelfed answers the lookup but redirects the statuses endpoint to its login page. When you save an instance running one of those, the settings page says so instead of leaving you with a platform that never appears.

= A platform I configured does not show up. Where do I look? =

The settings page, in the "Configured profiles" table. It shows what the plugin sees for each platform — instance, software, account or channel — and what the last fetch returned, with the reason when a platform came back empty: an instance requiring authentication, an account not found, a Lemmy feed requested where that account is not registered. The checks run when you save the settings and can be repeated with the "Verify profiles" button; the page itself never queries the instances while loading.

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

1. The timeline on the site: one chronological feed from every source, with the compact filter bar on top.
2. Cards carry image previews and interaction counts where the source provides them.
3. Settings: one box per platform, with the fields and the display options that apply to it.
4. Settings: how many posts reach the page, how long they stay cached, and the colours of the timeline.

== Changelog ==

= 1.15.4 - 2026-09-20 =
* Changed: the installation instructions now start from the WordPress plugin directory, which is where the plugin is distributed. The release ZIP and the source repository are still documented.
* Removed: the section recommending a third-party updater to receive releases from outside the plugin directory.
* Fixed: the screenshots in README.md are referenced by absolute address, so they show up outside the repository too, and all four are listed instead of three.
* Changed: the Lemmy example address no longer names a real account.


= 1.15.3 - 2026-09-20 =
* Changed: this readme now carries the changelog of the current 1.15 line only. Every earlier entry moved to changelog.txt, which ships with the plugin, as the plugin directory asks.
* Changed: the upgrade notice keeps the two most recent releases instead of every version ever published.

= 1.15.2 - 2026-09-20 =
* Fixed: eight field descriptions still quoted the defaults from before 1.14.1. They now match what the plugin actually does: five items per source, fifty posts on the page, thirty minutes of cache.
* Fixed: the screenshot list is now in the format the plugin directory reads, so the screenshots appear on the plugin page.
* Changed: the plugin directory assets (screenshots, banner, icon) are no longer shipped inside the plugin package.

= 1.15.1 - 2026-09-20 =
* Changed: the footer links now follow the language of the person reading them. The project page and the support page are translatable strings, so a translation can point them at its own localised pages, and a language without one simply stays on English. Nothing in the code tests for a specific locale.

= 1.15.0 - 2026-09-20 =
* Added: the settings page now has a proper footer, built on the WordPress footer slots. On the left, who develops the plugin and the links that matter: documentation, repository, and a way to support the work. On the right, version and licence, where administrators already look for them.
* Added: a donate link on the plugin directory page, pointing at the project's support page.
* Changed: the plugin homepage now opens the project page instead of the code repository, so anyone clicking it lands on documentation rather than on a Git tree.
* Fixed: the repository link in the footer was missing `rel="noopener noreferrer"`, unlike the developer one.

== Upgrade Notice ==

= 1.15.3 =
Documentation only: the changelog in this readme now covers the 1.15 line, with the earlier history moved to changelog.txt. No functional change.

= 1.15.2 =
Eight settings descriptions still quoted the defaults of an older version; they now match what the plugin really does. No functional change.

== External services ==

This plugin connects to the social platforms the user explicitly configures in the settings. No external service is contacted until the user fills in at least one profile (instance URL + username for Mastodon/Pleroma/Akkoma, Lemmy, PeerTube or Forgejo/Gitea, or a Bluesky handle). All requests are HTTP GET requests for public content; no user credentials are sent.

Contacted services are cached locally for a configurable duration (30 minutes to 24 hours, default value depends on the admin setting) to minimize external traffic.

= Mastodon =

- **What**: the Mastodon, Pleroma or Akkoma instance whose URL the user enters in the settings (e.g. `https://mastodon.uno`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered.
- **Endpoints**: `GET /.well-known/nodeinfo` and the linked nodeinfo document (to read which software the instance runs, cached 7 days), `GET /api/v1/accounts/lookup` (account ID lookup, cached 30 days) and `GET /api/v1/accounts/{id}/statuses` (public statuses).
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
- **Endpoints**: `GET /api/v1/accounts/{account}/videos` and `GET /api/v1/video-channels/{channel}/videos` for the public videos of an account or of a channel.
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

= Pixelfed =

- **What**: the Pixelfed instance the user enters in the settings (e.g. `https://pixelfed.uno`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered, plus once when the settings are saved or the "Verify profiles" button is used.
- **Endpoint**: `GET /users/{username}.atom`, the public Atom feed of the profile.
- **Data sent**: only the public username entered in the settings, as part of the URL path.
- **Data received and stored**: public post metadata (caption, date, link, photo URL and alt text if image previews are enabled) parsed from the public Atom feed. Cached locally as a WordPress transient.
- Pixelfed is decentralized: terms of service and privacy policy depend on the specific instance the user chooses and are available on that instance.

= ListenBrainz =

- **What**: the ListenBrainz API the user enters in the settings (by default `https://api.listenbrainz.org`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered, plus once when the settings are saved or the "Verify profiles" button is used.
- **Endpoint**: `GET /1/user/{username}/listens`, the public listens of the account. No token is sent.
- **Data sent**: only the public username entered in the settings, as part of the URL path.
- **Data received and stored**: artist, track, album, listen timestamp and the recording identifier when present, used to link the track page on ListenBrainz. Cached locally as a WordPress transient.
- Privacy policy: https://metabrainz.org/privacy — terms: https://metabrainz.org/social-contract

= RSS or Atom feed =

- **What**: the feed address the user enters in the settings; it can be any site.
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered, plus once when the settings are saved or the "Verify profiles" button is used.
- **Endpoint**: a `GET` request to the address itself.
- **Data sent**: nothing beyond the request for the feed.
- **Data received and stored**: title, link, summary, date and, when present, the item's image. Cached locally as a WordPress transient.
- Terms and privacy policy depend on the site the user chooses.

= Lemmy =

- **What**: the Lemmy instance the user enters in the settings (e.g. `https://diggita.com`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered.
- **Endpoint**: `GET /feeds/u/{username}.xml`, the public user RSS feed. It exists only on the instance where the account is registered.
- **Data sent**: only the public username entered in the settings, as part of the URL path.
- **Data received and stored**: public post metadata (title, link, date, vote and comment counts) parsed from the public RSS feed. Cached locally as a WordPress transient.
- Lemmy is decentralized: terms of service and privacy policy depend on the specific instance the user chooses and are available on that instance. For Diggita: https://www.diggita.com/regolamento and https://www.diggita.com/privacy

== Privacy Policy ==

EG Social Timeline retrieves only public content from the configured platforms. It does not track site visitors, does not send data to third-party services, and does not set cookies. The cache is local to the WordPress database.

== Support ==

* Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Issues: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
