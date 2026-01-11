# Changelog

Tutte le modifiche importanti a questo progetto sono documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.0.0/),
e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

---

## [1.2.4] - 2026-01-11

### Fixed
- **Diggita statistiche visualizzate**: risolto parsing HTML con `strip_tags()` prima dello split per estrarre correttamente punti e commenti
- **Forgejo nome repository visibile**: incluso nome repo nel campo `content` così appare nella timeline

### Changed
- **Diggita parsing robusto**: usa `strip_tags()` per rimuovere TUTTI i tag HTML (inclusi `<a>`, `<br>`, `<p>`) prima di parsare righe
- **Forgejo content format**: da solo messaggio a "Commit to {repo}: {message}" per chiarezza

### Technical
- Diggita: parsing sequenziale riga-per-riga dopo strip HTML invece di regex multipli
- Forgejo: `$full_content = 'Commit to ' . $repo_name . ': ' . $short_message`

---

## [1.2.3] - 2026-01-11

### Fixed
- **Filtri CSS logica invertita**: risolto bug selezione checkbox. Ora logica corretta: nascondi tutto di default, mostra solo piattaforme checked
- **Forgejo link pagina commits**: link ora punta a `/commits/branch/{branch}` invece di singolo commit `/commit/{sha}`
- **UI filtri compatta**: ridotto font-size, padding e dimensioni icone per box filtri più discreto

### Changed
- **CSS filtri**: da `:not(:checked) ~ .timeline-item { display: none }` a `:checked ~ .timeline-item { display: block }`
- **Forgejo URL format**: `{instance}/{owner}/{repo}/commits/branch/{branch}`

---

## [1.2.2] - 2026-01-11

### Fixed
- **Forgejo API affidabile**: risolto feed vuoto usando API commits diretta invece di `/activities/feeds`
- **Diggita contenuto pulito**: rimossa riga "submitted by X to Y"
- **Diggita statistiche separate**: estratti punti e commenti per visualizzazione pulita

### Changed
- **Forgejo implementazione**: usa `/repos` + `/commits` API
- **Diggita emoji**: ⭐ (punti) e 💬 (commenti)

---

## [1.2.1] - 2026-01-11

### Fixed
- **Filtri CSS siblings fix**: checkbox posizionati FUORI dal container come siblings diretti degli `<article>`
- **Post visibili**: risolto bug v1.2.0 che mostrava solo box filtri senza post

---

## [1.2.0] - 2026-01-11

### Added
- **Integrazione Forgejo/Gitea**: mostra attività repository pubbliche
- **Campo settings Forgejo**: username e URL istanza configurabili

### Fixed
- **Sistema icone file-based**: icone caricate da file SVG invece di hardcoded

---

## [1.1.2] - 2026-01-11

### Fixed
- **URL boost Mastodon**: non puntano più a JSON `/activity`

### Added
- **Box filtri interattivi**: checkbox CSS per ogni piattaforma

---

## [1.1.1] - 2026-01-10

### Fixed
- **Post duplicati**: rimossa duplicazione accidentale

---

## [1.1.0] - 2026-01-10

### Added
- **API Mastodon v1**: migrazione da RSS a API REST
- **Statistiche complete**: like, boost, commenti per Mastodon
- **Sistema icone modulare**: SVG da file

### Deprecated
- **RSS Mastodon**: sostituito da API

---

## [1.0.0] - 2026-01-10

### Added - MVP Release
- Plugin WordPress prima release
- Supporto Mastodon e Diggita
- Sistema cache configurabile
- Admin settings panel
- Shortcode base
- Design responsive
- Dark mode

---

## [Unreleased]

### Planned
- Integrazione Bluesky API
- Feed blog RSS
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
