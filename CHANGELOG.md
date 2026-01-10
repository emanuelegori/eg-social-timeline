# Changelog

Tutte le modifiche importanti a questo progetto saranno documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.0.0/),
e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

---

## [1.0.0] - 2025-01-10

### Aggiunto - MVP Fase 1

#### Core Features
- Sistema fetch feed RSS da Mastodon (istanze Fediverso)
- Sistema fetch feed RSS da Diggita (Lemmy)
- Timeline unificata con merge e sort cronologico
- Caching intelligente con WordPress Transients API
- Shortcode `[eg_social_timeline]` con parametro `limit`

#### Admin Panel
- Pannello impostazioni completo in Impostazioni → EG Social Timeline
- Campo URL profilo Mastodon
- Campo username Diggita
- Configurazione numero post (1-50)
- Selezione durata cache (30min-24h)
- Toggle mostra boost/repost
- Bottone svuota cache manuale
- Validazione configurazione obbligatoria
- Banner avviso se nessun profilo configurato

#### Frontend
- Timeline responsive con design moderno
- Card post con header, content, footer
- Icone piattaforme colorate SVG inline
- Gradient personalizzati per piattaforma
- Date relative (es: "2 ore fa")
- Troncamento intelligente testo (200 caratteri)
- Link "Vedi post originale" con hover effect
- Supporto dark mode automatico (`prefers-color-scheme`)
- Design mobile-first responsive

#### Performance
- Zero dipendenze JavaScript
- CSS puro senza framework
- Caching configurabile riduce carico server
- Fetch RSS con timeout 15 secondi
- Gestione errori connessione graceful

#### Developer Experience
- Costanti plugin (VERSION, DIR, URL)
- Debug mode attivabile via costante
- Hook WordPress standard
- Sanitization e validazione robusti
- Text domain i18n pronto (traduzioni prossima versione)
- Compatibilità Git Updater (Gitea Plugin URI)

#### Documentazione
- README.md completo con esempi
- Inline code comments
- Admin help text per ogni campo
- Troubleshooting guide

### Note Tecniche

**Architettura MVP:**
- File singolo PHP (eg-social-timeline.php)
- CSS separato (eg-social-timeline.css)
- Parsing RSS con SimpleXML nativo PHP
- WordPress HTTP API per fetch
- Transients API per caching

**Piattaforme Supportate:**
- Mastodon: Feed RSS standard ActivityPub (`profile.rss`)
- Diggita: Feed RSS standard Lemmy (`/feeds/u/username.xml`)

**Limitazioni Correnti:**
- Nessun supporto Bluesky (Fase 2)
- Nessuna paginazione timeline
- Nessun blocco Gutenberg
- Traduzioni i18n da completare
- Nessun widget WordPress nativo

---

## [Unreleased] - Roadmap Futura

### Fase 2 - Bluesky (In Sviluppo)
- Integrazione API pubblica Bluesky AT Protocol
- Fetch posts via `app.bsky.feed.getAuthorFeed`
- Conversione JSON → formato unificato timeline
- Gestione autenticazione se necessaria
- Icona e colori Bluesky

### Fase 3 - Advanced Features (Pianificato)
- Blocco Gutenberg nativo
- Widget WordPress nativo per sidebar
- Paginazione timeline (load more / infinite scroll)
- Filtri avanzati (hashtag, tipo contenuto, data range)
- Traduzioni complete (italiano, inglese)
- Supporto X/Twitter (se tecnicamente possibile)
- Export timeline (PDF, CSV)
- Admin statistics dashboard

### Miglioramenti Futuri (Backlog)
- Personalizzazione template timeline via filter hooks
- Supporto custom post types per archiviazione
- Integrazione con Fediverse API dirette (oltre RSS)
- Supporto immagini inline nei post
- Lightbox per media attachments
- Emoji rendering corretto
- Thread/conversation view
- User mentions e hashtag cliccabili
- Statistiche engagement (se disponibili via API)

---

## Versioning

Questo progetto segue [Semantic Versioning](https://semver.org/):

- **MAJOR**: Modifiche incompatibili API/breaking changes
- **MINOR**: Nuove funzionalità backward-compatible
- **PATCH**: Bug fixes backward-compatible

Esempio: `1.2.3`
- `1` = Major (breaking changes)
- `2` = Minor (nuove features)
- `3` = Patch (bug fixes)

---

## Release Notes

### v1.0.0 - MVP Ready

Prima release pubblica del plugin. Focus su semplicità, privacy e performance.

**Target utenti:**
- Blogger che vogliono mostrare attività Fediverso
- Siti informativi con presenza Mastodon/Diggita
- Content creators multi-piattaforma
- Sostenitori open source e decentralizzazione

**Non pronto per:**
- Produzioni che richiedono Bluesky
- Siti che necessitano paginazione
- Integrazioni complesse via API

**Testato su:**
- WordPress 6.4+
- PHP 8.0, 8.1, 8.2
- Mastodon 4.x
- Diggita/Lemmy 0.19.x

---

## Contributi

Per contribuire:
1. Controlla [ROADMAP](#unreleased---roadmap-futura)
2. Apri issue per discutere feature
3. Crea PR con modifiche
4. Documenta nel CHANGELOG

---

[Unreleased]: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/compare/v1.0.0...HEAD
[1.0.0]: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/releases/tag/v1.0.0
