# Changelog

Tutte le modifiche importanti a questo progetto sono documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.0.0/),
e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

---

## [1.2.5] - 2026-01-11

### Fixed
- **Diggita statistiche visualizzate**: `<br>` tag HTML convertiti in newline (`\n`) prima di `strip_tags()` per parsing corretto delle righe
- **Forgejo nome repository visibile**: incluso nel campo `content` così appare nella timeline come "Commit to {repo}: {message}"
- **Pulsanti filtri altezza uniforme**: rimossa larghezza fissa (min/max-width) per evitare text wrapping che causava altezze diverse
- **Icone filtri dimensioni uniformi**: rimosso `object-fit: contain` che causava rendering disuniforme tra diverse icone SVG

### Changed
- **CSS pulsanti filtri**: da larghezza fissa 145px/160px a larghezza automatica basata su contenuto
- **CSS icone filtri**: da `width/height + object-fit: contain` a solo `width/height` per rendering naturale
- **Diggita parsing**: usa `str_replace(['<br>', '<br/>', '<br />'], "\n", $description)` prima di `strip_tags()` per preservare struttura righe

### Technical
- Diggita: conversione `<br>` → `\n` prima di strip HTML garantisce che `explode("\n")` funzioni correttamente
- CSS: larghezza automatica evita text wrapping che aumenta altezza pulsanti
- SVG: rendering naturale senza `object-fit` mantiene dimensioni uniformi tra icone diverse

---

## [1.2.4] - 2026-01-11

### Fixed
- **Diggita statistiche**: parsing HTML robusto con `strip_tags()` prima dello split
- **Forgejo nome repository**: ora visibile nella timeline ("Commit to {repo}: {message}")

### Changed
- Diggita: parsing robusto riga-per-riga dopo rimozione tag HTML
- Forgejo: nome repository incluso nel campo `content` per visualizzazione

---

## [1.2.3] - 2026-01-11

### Fixed
- **Filtri CSS logica invertita**: risolto bug selezione checkbox (nascondi tutto, mostra checked)
- **Forgejo link pagina commits**: ora punta a `/commits/branch/{branch}` invece di singolo commit
- **UI filtri compatta**: ridotto font-size, padding e dimensioni icone

### Changed
- **CSS filtri**: da `:not(:checked)` a `:checked` (logica più affidabile)
- **Forgejo URL format**: `{instance}/{owner}/{repo}/commits/branch/{branch}`

---

## [1.2.2] - 2026-01-11

### Fixed
- **Forgejo API affidabile**: risolto feed vuoto usando API commits diretta
- **Diggita contenuto pulito**: rimossa riga "submitted by X to Y"
- **Diggita statistiche separate**: estratti punti e commenti

### Changed
- **Forgejo implementazione**: usa `/repos` + `/commits` API
- **Diggita emoji**: ⭐ (punti) e 💬 (commenti)

---

## [1.2.1] - 2026-01-11

### Fixed
- **Filtri CSS siblings fix**: checkbox posizionati FUORI dal container
- **Post visibili**: risolto bug v1.2.0

---

## [1.2.0] - 2026-01-11

### Added
- **Integrazione Forgejo/Gitea**: mostra attività repository pubbliche
- **Campo settings Forgejo**: username e URL istanza configurabili

### Fixed
- **Sistema icone file-based**: icone caricate da file SVG

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
- **Statistiche complete**: like, boost, commenti
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
