# Changelog

Tutte le modifiche notevoli a questo progetto verranno documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.0.0/), e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

---

## [1.4.0] - 2026-05-02

### Added
- **Integrazione Bluesky** via API pubblica ATP (nessuna autenticazione richiesta)
- Nuovo campo admin: Handle Bluesky (es. `emanuele.bsky.social`, senza @)
- Nuovo campo admin: Max Post Bluesky (default: 10, range: 0-100)
- Funzione `eg_social_timeline_fetch_bluesky()`: recupera post via `app.bsky.feed.getAuthorFeed`
- Filtro piattaforma Bluesky nella timeline (CSS-only, come le altre piattaforme)
- Supporto repost Bluesky rispettando l'opzione "Includi Boost/Repost"
- Statistiche Bluesky: like, repost, risposte
- Link diretto al post originale su bsky.app
- `@ ` iniziale nell'handle viene rimosso automaticamente in fase di sanitizzazione

### Changed
- Validazione "almeno un profilo" aggiornata per includere Bluesky
- Messaggi di errore admin aggiornati per citare Bluesky
- `fetch_all_feeds()`: aggiunta chiamata a Bluesky nella pipeline di fetch
- Default options: aggiunti `bluesky_handle` e `bluesky_limit`

---

## [1.3.1] - 2026-04-06

### Security
- Aggiunto `LIBXML_NONET` al parsing XML del feed RSS Diggita per prevenire XXE (XML External Entity)
- Aggiunta sanitizzazione SVG inline con `wp_kses()` nella funzione `eg_social_timeline_get_icon()` per prevenire XSS da file SVG compromessi
- Aggiunta validazione anti-SSRF sulle URL delle API esterne (Mastodon, Forgejo): nuova funzione `eg_social_timeline_is_public_url()` rifiuta IP privati, riservati e localhost

### Fixed
- Aggiunto `esc_url()` mancante sul link admin nello shortcode quando nessun profilo è configurato

---

## [1.3.0] - 2026-01-11

### Added
- Limiti configurabili per piattaforma nelle impostazioni admin
- Nuovo campo: Max post Mastodon (default: 20, range: 0-100)
- Nuovo campo: Max post Diggita (default: 10, range: 0-100)
- Nuovo campo: Max commit Forgejo (default: 5, range: 0-50)
- Nuova sezione settings "Limiti Post per Piattaforma"
- Valore 0 per nessun limite (comportamento v1.2.x)
- Descrizioni tooltip per ogni campo limite
- Timeline più equilibrata: previene monopolizzazione da singola piattaforma

### Changed
- Logica `eg_social_timeline_fetch_all_feeds()` modificata per rispettare limiti per piattaforma
- `eg_social_timeline_fetch_mastodon()` accetta parametro `$limit`
- `eg_social_timeline_fetch_diggita()` accetta parametro `$limit`
- `eg_social_timeline_fetch_forgejo()` accetta parametro `$limit` (TOTALE commit, non per-repo)
- Forgejo: limite distribuito equamente tra repository pubblici
- Limite totale timeline aumentato: 1-100 (era 1-50)
- Default sanitization per nuovi campi limiti
- Version bump: `1.3.0` in tutte le posizioni richieste

### Fixed
- Forgejo: gestione corretta limite totale commit invece di per-repository
- Forgejo: calcolo `commits_per_repo` per distribuzione equa

---

## [1.2.5] - 2026-01-11

### Fixed
- **CRITICO**: Diggita statistiche parsing con `<br>` tag
  - Problema: `<br>` veniva rimosso da `strip_tags()` senza spazi, "5 points | 3 comments" diventava "5 points|3comments" (parsing falliva)
  - Soluzione: Conversione `<br>` → `\n` PRIMA di `strip_tags()` con `str_replace()`
  - Statistiche ora parsate correttamente: punti e commenti visualizzati
- Forgejo nome repository ora incluso nel content visibile
  - Era: "Commit: Fix bug"
  - Ora: "Commit to eg-social-timeline: Fix bug"
- Pulsanti filtri larghezza automatica invece di fissa
  - Problema: larghezza fissa 145px causava altezza disuniforme con testo lungo
  - Soluzione: CSS `width: auto` con `flex: 0 0 auto` per adattamento dinamico
- Icone filtri dimensioni uniformi senza distorsione
  - Rimosso `object-fit: contain` che causava rendering non uniforme

### Changed
- CSS `.filter-checkbox-label`: `width: 145px` → `width: auto`
- CSS `.platform-icon-small svg`: rimosso `object-fit: contain`
- Diggita parsing: usa `str_replace()` per `<br>` prima di `strip_tags()`
- Forgejo content: formato "Commit to {repo}: {message}"

---

## [1.2.4] - 2026-01-11

### Fixed
- Diggita description parsing più robusto con `strip_tags()`
- Forgejo nome repository incluso nel field `content`

### Changed
- Diggita: usa `strip_tags()` invece di regex per pulizia HTML
- Forgejo: content format "Commit to {repo_name}: {message}"

---

## [1.2.3] - 2026-01-11

### Fixed
- **CRITICO**: Filtri CSS logica invertita
  - Checkbox `checked` nascondeva invece di mostrare
  - Selettore corretto: `#filter-X:not(:checked) ~ .timeline-X { display: none; }`
- Forgejo: link corretto a pagina commits repository
  - Era: link a commit singolo (404 se non pubblico)
  - Ora: link a `/commits/branch/{default_branch}`
- UI box filtri compatto e pulito

### Changed
- CSS filtri: selettore `#filter-X:checked` → `#filter-X:not(:checked)`
- Forgejo: URL formato `/{owner}/{repo}/commits/branch/{branch}`
- CSS `.eg-timeline-filters`: padding e margin ottimizzati

---

## [1.2.2] - 2026-01-11

### Fixed
- Forgejo commit link URL encoding branch name
- CSS filtri padding e allineamento

### Changed
- Forgejo: usa `urlencode()` per branch name in URL

---

## [1.2.1] - 2026-01-11

### Fixed
- CSS filtri checkbox posizionamento assoluto fuori viewport
- Icon system SVG file path check

### Changed
- CSS `.filter-checkbox-input`: `position: absolute; left: -9999px;`

---

## [1.2.0] - 2026-01-11

### Added
- **Integrazione Forgejo/Gitea** per commit repository pubblici
- Nuovo campo admin: Username Forgejo/Gitea
- Nuovo campo admin: URL istanza Forgejo/Gitea (default: https://gitea.com)
- Funzione `eg_social_timeline_fetch_forgejo()`: recupera commit via API
- Icona SVG Forgejo in `social-icons/forgejo.svg`
- Link diretti a pagina commits del repository
- Commit mostrati come "Commit to {repo}: {message}"
- Supporto branch default dinamico (main/master)
- Filtro "Forgejo" nella timeline

### Changed
- Admin settings: nuovo campo username Forgejo
- Admin settings: nuovo campo URL istanza Forgejo
- Timeline: supporto piattaforma "forgejo"
- Forgejo commits ordinati cronologicamente con altri post
- Default commit limit: 5 per repository

---

## [1.1.2] - 2026-01-10

### Fixed
- Diggita statistiche parsing più robusto
  - Gestione corretta regex per "X points | Y comments"
  - Fallback a 0 se parsing fallisce
- Diggita content cleaning migliorato

### Changed
- Diggita: regex parsing con case-insensitive flag
- Diggita: rimozione linea "submitted by" più affidabile

---

## [1.1.1] - 2026-01-10

### Fixed
- Icon system SVG loading con `file_get_contents()`
- Fallback icon se file SVG non trovato
- Path check con `file_exists()` prima di load

### Changed
- Funzione `eg_social_timeline_get_icon()`: carica SVG da file
- Icone ora in cartella `social-icons/`
- Fallback: cerchia generica se file mancante

---

## [1.1.0] - 2026-01-10

### Added
- **Migrazione Mastodon da RSS a API v1**
- Endpoint API: `/api/v1/accounts/{id}/statuses`
- Funzione `eg_social_timeline_get_mastodon_account_id()`: lookup account ID
- Statistiche complete: like (favourites), boost (reblogs), risposte (replies)
- Supporto boost/reblog con flag `is_boost`
- Cache account ID Mastodon (24 ore)
- Parametri API: `exclude_replies`, `exclude_reblogs`, `limit`
- Sistema icone modulare con file SVG separati
- Icone piattaforme in cartella `social-icons/`
- Badge "Boost" per post rebloggati

### Changed
- Mastodon: RSS deprecato, ora usa API v1
- Post structure: aggiunto `is_boost` boolean field
- Statistiche: `favourites_count`, `reblogs_count`, `replies_count`
- Frontend: mostra statistiche se `show_stats` enabled
- Diggita: emoji ⭐ per punti, ❤️ per like Mastodon
- Icons: caricati dinamicamente da file SVG

### Deprecated
- Mastodon RSS feed (`/users/{username}.rss`)

---

## [1.0.0] - 2026-01-10

### Added
- Release iniziale MVP (Minimum Viable Product)
- Supporto piattaforme: Mastodon, Diggita (Lemmy)
- Admin settings panel con WordPress Settings API
- Campo: URL profilo Mastodon
- Campo: Username Diggita
- Campo: Limite post da mostrare (1-50)
- Campo: Durata cache (30min - 24h)
- Campo: Includi boost/repost (checkbox)
- Campo: Mostra statistiche (checkbox)
- Shortcode `[eg_social_timeline]` con parametro opzionale `limit`
- Sistema cache con WordPress Transients API
- Fetch Mastodon via RSS (`/users/{username}.rss`)
- Fetch Diggita via RSS (`/feeds/u/{username}.xml`)
- Timeline unificata ordinata cronologicamente
- Design responsive con CSS Grid/Flexbox
- Supporto dark mode automatico con `prefers-color-scheme`
- Statistiche Diggita: punti e commenti
- Truncate intelligente testo post (300 caratteri)
- Date relative ("5 minuti fa", "2 ore fa")
- Link "Vedi post originale" per ogni post
- Admin notice se nessun profilo configurato
- Pulsante "Svuota Cache Ora" in admin
- Plugin action links: Impostazioni, Documentazione
- Internazionalizzazione (i18n) ready
- Licenza GPL-2.0-or-later
- Git Updater compatibility headers

### Technical
- WordPress compatibility: 5.0+
- PHP compatibility: 7.4+
- Zero JavaScript (CSS-only filters)
- Cache key: `eg_social_timeline_cache`
- Option key: `eg_social_timeline_options`
- Text domain: `eg-social-timeline`
- Sanitization: `esc_url_raw()`, `sanitize_text_field()`
- Security: nonce verification, capability checks
- API timeout: 15 secondi
- SSL verification: abilitato

---

## [Unreleased]

### Planned
- Integrazione Bluesky ATP Protocol
- Integrazione RSS blog personale
- Widget WordPress per sidebar
- Gutenberg block
- Template system personalizzabile
- Esportazione timeline (JSON/CSV)
- Filtri avanzati (data range, piattaforma)
- Paginazione AJAX
- Infinite scroll
- Preview post con immagini
- Supporto video embed
- Modalità lista vs griglia
- Temi colore predefiniti

### In Development
- Bluesky AT Protocol integration
- Blog RSS feed support

---

## Note di Versioning

Il progetto segue [Semantic Versioning](https://semver.org/lang/it/):

- **MAJOR** (X.0.0): Breaking changes, incompatibilità retroattiva
- **MINOR** (0.X.0): Nuove feature, retrocompatibile
- **PATCH** (0.0.X): Bug fix, retrocompatibile

---

## Link

- [Repository](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)
- [Issues](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues)
- [Releases](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/releases)

