# Platform icons

This directory contains the icons used to identify the platforms supported by
EG Social Timeline. They are **not** covered by the plugin's own licence, and
they are included solely to identify each platform's content inside the
timeline.

## Where the icons come from

Except where noted below, every icon in this directory is the single-path
glyph published by **Simple Icons**, released under **CC0 1.0 Universal**
(public domain dedication):

- Collection: https://github.com/simple-icons/simple-icons
- Licence: https://creativecommons.org/publicdomain/zero/1.0/

The CC0 dedication covers the drawing as published by Simple Icons. It does
**not** cover the underlying trademarks: each brand remains the property of
its own project, and the marks are used here descriptively, to say which
platform a post came from. Nothing in this plugin implies endorsement by, or
affiliation with, any of these projects.

The icons are not modified in shape. The stylesheet only sets their colour
through `fill: currentColor`, because the Simple Icons glyphs carry no colour
of their own.

## Platform by platform

### Mastodon — `mastodon.svg`

- Drawing: Simple Icons (CC0 1.0).
- Trademark and brand usage: https://joinmastodon.org/trademark
- Official brand assets: https://github.com/mastodon/joinmastodon/tree/main/public/logos

### Pleroma — `pleroma.svg`

Also used for **Akkoma**, which is a fork of Pleroma and shares its API.
Akkoma has no icon of its own in the collection used here; the label shown in
the timeline always names the software actually detected.

- Drawing: Simple Icons (CC0 1.0).
- Project: https://pleroma.social/
- Akkoma: https://akkoma.social/

### Bluesky — `bluesky.svg`

- Drawing: Simple Icons (CC0 1.0).
- Brand guidelines: https://bsky.social/about/support/branding

### Pixelfed — `pixelfed.svg`

- Drawing: Simple Icons (CC0 1.0).
- Official brand assets: https://github.com/pixelfed/brand-assets

### PeerTube — `peertube.svg`

- Drawing: Simple Icons (CC0 1.0).
- The official PeerTube logo is by Framasoft, released under CC BY-SA 4.0:
  https://github.com/Chocobozzz/PeerTube/blob/develop/client/src/assets/images/logo.svg
  https://creativecommons.org/licenses/by-sa/4.0/

### Lemmy — `lemmy.svg`

- Drawing: Simple Icons (CC0 1.0).
- The official Lemmy logo is by Andy Cuccaro (@andycuccaro), released under
  CC BY-SA 4.0: https://github.com/LemmyNet/lemmy-ui
  https://creativecommons.org/licenses/by-sa/4.0/

### Forgejo — `forgejo.svg`

- Drawing: Simple Icons (CC0 1.0).
- Forgejo branding and the logo licence exemption:
  https://codeberg.org/forgejo/meta/src/branch/readme/branding

### RSS and Atom feeds — `rss.svg`

- Drawing: Simple Icons (CC0 1.0).
- The RSS feed icon is a widely used generic mark, not the logo of any single
  project.

## Icons drawn for this plugin

### `listenbrainz.svg`

A plain music note, drawn for EG Social Timeline and distributed under the
plugin's licence, GPL-2.0-or-later. ListenBrainz has no icon in the collection
used here, and this avoids putting the MusicBrainz logo — a different project
of the same family — on ListenBrainz content.

- ListenBrainz: https://listenbrainz.org/
- MetaBrainz brand assets: https://metabrainz.org/

### `generic.svg`

Neutral placeholder used when a platform has no icon of its own. Drawn for EG
Social Timeline and distributed under the plugin's licence, GPL-2.0-or-later.

## Replacing an icon

The files here can be replaced with your own, including a project's official
logo. Keep the same file name, a `viewBox` of `0 0 24 24` and a single path
without a `fill` attribute, so the stylesheet can colour it and adapt it to a
light or dark surface. An SVG that carries its own `fill` values keeps them:
the plugin will not recolour it, and the "single colour" icon style stops
having any effect on it.
