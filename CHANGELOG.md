# Changelog

Tutte le modifiche importanti a questo progetto sono documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.0.0/),
e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

---

## [1.2.0] - 2026-01-11

### Added
- **Integrazione Forgejo/Gitea**: mostra attività repository pubbliche (commit e nuovi repository)
- **Campo settings username Forgejo**: configurabile nelle impostazioni plugin
- **Campo settings URL istanza Forgejo**: supporta istanze personalizzate (default: https://gitea.com)
- **Supporto filtro Forgejo**: integrato nel sistema filtri CSS della timeline
- **Icona Forgejo**: aggiunta icona modulare SVG per Forgejo/Gitea

### Fixed
- **Sistema icone file-based**: le icone ora si caricano da file SVG in `social-icons/` invece di essere hardcoded in array PHP
- **Icone personalizzabili**: modifica file SVG senza toccare codice PHP

### Changed
- **Versione**: 1.1.2 → 1.2.0 (nuova feature Forgejo)
- **Funzione get_icon()**: refactored per leggere da filesystem

---

## [1.1.2] - 2026-01-11

### Fixed
- **URL boost Mastodon**: i boost non puntano più all'endpoint JSON `/activity` ma alla pagina web del post originale
- **Timeline filtri**: integrato sistema filtri CSS puro per mostrare/nascondere piattaforme

### Added
- **Box filtri interattivi**: checkbox CSS per ogni piattaforma con conteggio post
- **Attributo data-platform**: aggiunto a ogni `<article>` per supportare filtri CSS
- **CSS filtri**: styling completo con dark mode e mobile responsive

### Changed
- **UX design**: aumentato spazio tra post (20px → 40px)
- **Link "Vedi originale"**: ridotto da bottone full-width a link inline discreto

---

## [1.1.1] - 2026-01-10

### Fixed
- **Post duplicati**: rimossa duplicazione accidentale di post nella timeline

---

## [1.1.0] - 2026-01-10

### Added
- **API Mastodon v1**: migrazione da RSS a API REST `/api/v1/accounts/{id}/statuses`
- **Statistiche complete**: mostra like (❤️), boost (🔁) e commenti (💬) per post Mastodon
- **Sistema icone modulare**: icone SVG caricate da file invece di hardcoded
- **Cartella social-icons/**: directory dedicata per file SVG icone piattaforme
- **Fallback icone**: icona generica se file non trovato

### Changed
- **Fetch Mastodon**: da RSS feed a API JSON
- **Struttura dati**: standardizzata con campi `favourites_count`, `reblogs_count`, `replies_count`

### Deprecated
- **RSS Mastodon**: sostituito da API (RSS non fornisce statistiche)

---

## [1.0.0] - 2026-01-10

### Added - MVP Release
- **Plugin WordPress**: prima release pubblica
- **Supporto Mastodon**: integrazione RSS per post pubblici
- **Supporto Diggita**: integrazione RSS per post Lemmy
- **Sistema cache**: transient WordPress con durata configurabile (30min - 24h)
- **Admin settings**: pannello impostazioni completo
- **Shortcode base**: `[eg_social_timeline]` e `[eg_social_timeline limit="20"]`
- **Timeline unificata**: ordine cronologico inverso di tutti i post
- **Design responsive**: supporto mobile, tablet, desktop
- **Dark mode**: supporto automatico tema scuro
- **Licenza GPL-2.0-or-later**: software libero
- **README completo**: documentazione e istruzioni
- **readme.txt WordPress**: file standard per directory plugin
- **CHANGELOG.md**: questo file

### Technical
- **Requisiti**: WordPress 5.0+, PHP 7.4+
- **API**: Mastodon RSS, Diggita RSS
- **Cache**: WordPress transient API
- **Sanitization**: `esc_url_raw()`, `sanitize_text_field()`, `wp_kses_post()`
- **i18n ready**: text domain `eg-social-timeline`

---

## [Unreleased]

### Planned
- Integrazione Bluesky API
- Integrazione feed blog RSS
- Statistiche Diggita via Lemmy API
- Opzione localStorage per persistenza filtri
- Widget sidebar
- Gutenberg block

---

## Formato Versioni

Questo progetto segue Semantic Versioning (MAJOR.MINOR.PATCH):

- **MAJOR**: Modifiche incompatibili API
- **MINOR**: Nuove funzionalità compatibili
- **PATCH**: Bug fix compatibili

Esempio:
- `1.0.0` → `1.0.1`: Bug fix (patch)
- `1.0.1` → `1.1.0`: Nuova feature (minor)
- `1.1.0` → `2.0.0`: Breaking change (major)

---

## Link

- **Repository**: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
- **Issues**: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
- **Releases**: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/releases
- **Autore**: [Emanuele Gori](https://emanuelegori.uno)
