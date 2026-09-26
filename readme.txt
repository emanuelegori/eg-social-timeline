=== EG Social Timeline ===
Contributors: emanuelegori
Donate link: https://emanuelegori.uno/en/donate/
Tags: mastodon, bluesky, lemmy, forgejo, timeline
Requires at least: 5.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.16.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Chronological timeline of your public activity across the fediverse, Bluesky, ListenBrainz and any RSS feed. Zero JavaScript, zero tracking.

== Description ==

EG Social Timeline aggregates in chronological order your public posts from ten sources and displays them in a single timeline via shortcode.

= Supported platforms =

- Mastodon, and the Mastodon-compatible Pleroma and Akkoma (public accounts API, no authentication)
- GoToSocial (public RSS feed of the profile, which the account owner turns on in the settings: public posts and images, no interaction counts)
- Friendica (public Atom feed of the profile: posts and images, no interaction counts)
- Bluesky (public API, no authentication required)
- Pixelfed (public Atom feed: photos and captions, no interaction counts)
- PeerTube (public REST API, videos from an account or from a channel)
- Forgejo and Gitea (commits from your public repositories)
- Lemmy, on any instance (public user RSS feed)
- ListenBrainz (public API, no token: what you have been listening to)
- Any RSS 2.0 or Atom feed: a blog, a newsletter, a podcast

Every platform is configured the same way: the instance URL plus your username. Bluesky is the exception, because its public API lives at a single address for everyone.

= Key features =

- Interactive per-platform filters, no JavaScript required
- Image previews wherever the source carries one: Mastodon, GoToSocial, Friendica, Bluesky, Pixelfed, PeerTube thumbnails and images found in a feed (optional, per source)
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

You can also paste the full profile address into either field and the plugin splits it: `https://lemmy.example/u/username`, `https://mastodon.example/@name`, `https://peertube.example/c/name@host.example/videos`, `https://bsky.app/profile/name.bsky.social`.

= Advanced configuration =

- Per-platform post limits (avoids one platform monopolizing the timeline)
- Image previews where the source carries one: Mastodon, Bluesky, Pixelfed, PeerTube thumbnails and images found in a feed
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

Mastodon, Pleroma, Akkoma, Bluesky, Pixelfed, PeerTube, Forgejo/Gitea, Lemmy (any instance), ListenBrainz, and any RSS or Atom feed.

= My PeerTube videos are not showing up. =

On PeerTube videos almost always live in a **channel**, not directly in the account, and the two use different API endpoints. Paste the full address of your channel and the plugin sorts it out: `/c/name` is a channel, `/a/name` an account. If you only type a name, both are tried and the answer is remembered.

A second detail: a channel belongs to the instance that hosts it. An address like `https://peertube.example/c/name@origin.example/videos` means the channel lives on `origin.example` and `peertube.example` only federates it — the plugin then queries the origin, which does not depend on the state of federation.

= Can I add a source that is not a social platform? =

Yes. The "RSS or Atom feed" fields take the address of any feed — your blog, a newsletter, a podcast — and its items join the timeline like any other source, with their own filter. The label you give it is the name shown on the cards; left empty, the feed's own title is used.

= What does the ListenBrainz source show? =

Your recent listens: artist and track, with the date. The public API needs no token. Each card links to the recording on ListenBrainz when the identifier is available, otherwise to your ListenBrainz profile. No interaction counts, because listens do not have any.

= Why does Pixelfed show no likes or comments? =

Because its public Atom feed does not carry them. Pixelfed answers the Mastodon-compatible account lookup but redirects the statuses endpoint to its login page, so the feed is the only way to read a profile without credentials: it gives photos, captions, dates and links, and no counts.

= Does the Mastodon field work with every fediverse server? =

No, and it is worth knowing why. The plugin reads the public accounts API (`/api/v1/accounts/lookup` and `/api/v1/accounts/{id}/statuses`), which Mastodon, Pleroma and Akkoma serve without authentication. GoToSocial and Friendica answer those endpoints only to signed-in users, so they have their own sections, which read the public feed of the profile instead. Misskey and Sharkey use a different API and are not supported at the moment. Pixelfed answers the lookup but redirects the statuses endpoint to its login page, and also has its own section. When you save an instance of another software in the Mastodon field, the settings page says so and points to the right section.

= My GoToSocial posts do not show up. =

GoToSocial keeps the RSS feed of a profile off by default. Turn it on in your account settings (`https://your-instance/settings`), then save the plugin settings again. The feed carries your latest 20 public posts, without replies or boosts. Also make sure the instance address is the server you log in to: when the account domain is different from the server, the plugin looks it up when you save and corrects the address.

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

= 1.16.0 - 2026-09-27 =
* New: GoToSocial and Friendica have their own sections. Their Mastodon-compatible API is closed to visitors, so the plugin reads the public feed of the profile: the RSS feed on GoToSocial, the Atom feed on Friendica. Posts carry their own icon and name, and images when the feed has them.
* New: on GoToSocial the RSS feed is off by default, and the settings page now says so and links the account settings, instead of reporting an empty source. When the account domain differs from the server, the plugin finds the server when you save.
* New: entering a GoToSocial or Friendica instance in the Mastodon field points to the right section.
* Fixed: the FAQ said ListenBrainz cards link to MusicBrainz; they link to the recording on ListenBrainz.

== Upgrade Notice ==

= 1.16.0 =
GoToSocial and Friendica get their own sections, reading the public feed of the profile. On GoToSocial the feed must be turned on in the account settings.

= 1.15.9 =
Corrects the "tested up to" header, which must carry the major version only. No functional change.

= 1.15.8 =
Three cache keys now carry the plugin's full prefix, and the bundled Italian translation moves to translate.wordpress.org. No setting is affected.

= 1.15.7 =
Fixes a real default: on a fresh install, boosts and image previews were switched off although they are documented as on. Existing installs are not affected.

= 1.15.6 =
Documentation only: dead and placeholder links in the readme have been corrected, and the plugin is now tested up to WordPress 7.1.2.

= 1.15.5 =
The compatibility header now declares the current WordPress release, so the "not tested with your version" warning goes away. No functional change.

== External services ==

This plugin connects only to the platforms and feeds the administrator explicitly configures in the settings, one section per source below. No external service is contacted until at least one source has been filled in. All requests are HTTP GET requests for public content; no user credentials are ever sent.

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

= GoToSocial =

- **What**: the GoToSocial instance the user enters in the settings (e.g. `https://gts.example.org`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered, plus once when the settings are saved or the "Verify profiles" button is used.
- **Endpoints**: `GET /@{username}/feed.rss`, the public RSS feed of the profile, which the account owner must turn on. When the settings are saved, also `GET /.well-known/webfinger?resource=acct:{username}@{domain}`, to find the server that hosts the account when its domain is different.
- **Data sent**: only the public username entered in the settings, as part of the URL.
- **Data received and stored**: public post metadata (text, date, link and image URL if image previews are enabled) parsed from the public RSS feed. Cached locally as a WordPress transient.
- GoToSocial is decentralized: terms of service and privacy policy depend on the specific instance the user chooses and are available on that instance.

= Friendica =

- **What**: the Friendica instance the user enters in the settings (e.g. `https://friendica.example.org`).
- **When**: each time the timeline cache expires and a page containing the shortcode is rendered, plus once when the settings are saved or the "Verify profiles" button is used.
- **Endpoint**: `GET /feed/{nickname}/`, the public Atom feed of the profile.
- **Data sent**: only the public nickname entered in the settings, as part of the URL path.
- **Data received and stored**: public post metadata (title, text, date, link, image URL and its description if image previews are enabled) parsed from the public Atom feed. Cached locally as a WordPress transient.
- Friendica is decentralized: terms of service and privacy policy depend on the specific instance the user chooses and are available on that instance.

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
- Lemmy is decentralized: terms of service and privacy policy depend on the specific instance the user chooses and are published by that instance.

== Privacy Policy ==

EG Social Timeline retrieves only public content from the configured platforms. It does not track site visitors, does not send data to third-party services, and does not set cookies. The cache is local to the WordPress database.

== Support ==

* Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Issues: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
