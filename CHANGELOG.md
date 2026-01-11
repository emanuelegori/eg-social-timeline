# Changelog

Tutte le modifiche importanti a questo progetto sono documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.0.0/),
e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

---

## [1.2.3] - 2026-01-11

### Fixed
- **Filtri CSS logica invertita**: risolto bug selezione checkbox. Ora logica corretta: nascondi tutto di default, mostra solo piattaforme checked
- **Diggita parsing robusto**: statistiche estratte correttamente con split multi-riga invece di regex su stringa unica
- **Forgejo link pagina commits**: link ora punta a `/commits/branch/{branch}` invece di singolo commit `/commit/{sha}`
- **UI filtri compatta**: ridotto font-size, padding e dimensioni icone per box filtri più discreto

### Changed
- **CSS filtri**: da `:not(:checked) ~ .timeline-item { display: none }` a `:checked ~ .timeline-item { display: block }` (logica invertita)
- **Box filtri dimensioni**:
  - Header h3: 1.1em → 0.95em
  - Label: 0.85em font, 6px padding (era 8px)
  - Icone: 16px (erano 20px)
  - Checkbox indicator: 16px (era 20px)
- **Forgejo URL format**: `{instance}/{owner}/{repo}/commits/branch/{branch}` per vedere tutti i commit del repo

### Technical
- Diggita: parsing con `explode("\n")` e `array_shift()` invece di regex multipli
- CSS: default `.timeline-item { display: none !important }` con override `!important` su `:checked`
- Forgejo: usa `urlencode($default_branch)` per branch names con caratteri speciali

---

## [1.2.2] - 2026-01-11

### Fixed
- **Forgejo API affidabile**: risolto feed vuoto su alcune istanze Forgejo usando API commits diretta invece di `/activities/feeds`
- **Diggita contenuto pulito**: rimossa riga "submitted by X to Y" che appariva sopra il testo
- **Diggita statistiche separate**: estratti punti e commenti dal contenuto per visualizzazione pulita

### Changed
- **Forgejo implementazione**: usa `/api/v1/users/{user}/repos` + `/api/v1/repos/{owner}/{repo}/commits` per affidabilità
- **Diggita emoji**: mostra ⭐ (punti) invece di ❤️ e 💬 (commenti) per chiarezza
- **Forgejo link**: personalizzato "Vedi commit" invece di generico "Vedi post originale"

### Added
- **Diggita statistiche visuali**: upvotes e commenti mostrati sotto il post come Mastodon (formato omogeneo)
- **Forgejo multi-repository**: recupera commit da tutti i repository pubblici dell'utente (ultimi 5 per repo)

---

## [1.2.1] - 2026-01-11

### Fixed
- **Filtri CSS siblings fix**: checkbox ora posizionati FUORI dal container come siblings diretti degli `<article>` per corretto funzionamento dei selettori CSS `~`
- **Post visibili**: risolto bug v1.2.0 che mostrava solo box filtri senza post

### Technical
- Checkbox HTML spostati prima del `<div class="eg-timeline-filters">` invece che dentro
- Label rimangono dentro il container e usano attributo `for` per associazione

---

## [1.2.0] - 2026-01-11

### Added
- **Integrazione Forgejo/Gitea**: mostra attività repository pubbliche (commit)
- **Campo settings username Forgejo**: configurabile nelle impostazioni plugin
- **Campo settings URL istanza Forgejo**: supporta istanze personalizzate
- **Supporto filtro Forgejo**: integrato nel sistema filtri CSS della timeline
- **Icona Forgejo**: aggiunta icona modulare SVG

### Fixed
- **Sistema icone file-based**: le icone ora si caricano da file SVG in `social-icons/` invece di essere hardcoded in array PHP

---

## [1.1.2] - 2026-01-11

### Fixed
- **URL boost Mastodon**: i boost non puntano più all'endpoint JSON `/activity` ma alla pagina web del post originale

### Added
- **Box filtri interattivi**: checkbox CSS per ogni piattaforma con conteggio post
- **Attributo data-platform**: aggiunto a ogni `<article>` per supportare filtri CSS

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

### Technical
- **Requisiti**: WordPress 5.0+, PHP 7.4+
- **Cache**: WordPress transient API
- **i18n ready**: text domain `eg-social-timeline`

---

## [Unreleased]

### Planned
- Integrazione Bluesky API
- Integrazione feed blog RSS
- Opzione localStorage per persistenza filtri
- Widget sidebar
- Gutenberg block

---

## Formato Versioni

Questo progetto segue Semantic Versioning (MAJOR.MINOR.PATCH):

- **MAJOR**: Modifiche incompatibili API
- **MINOR**: Nuove funzionalità compatibili
- **PATCH**: Bug fix compatibili

---

## Link

- **Repository**: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
- **Issues**: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
- **Releases**: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/releases
- **Autore**: [Emanuele Gori](https://emanuelegori.uno)
