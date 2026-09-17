# Changelog

Tutte le modifiche notevoli a questo progetto verranno documentate in questo file.

Il formato è basato su [Keep a Changelog](https://keepachangelog.com/it/1.0.0/), e questo progetto aderisce al [Semantic Versioning](https://semver.org/lang/it/).

---

## [1.9.0] - 2026-09-17

### Changed
- **Tutte le piattaforme si configurano allo stesso modo**: URL dell'istanza + nome utente. Mastodon chiedeva l'URL completo del profilo mentre Forgejo e PeerTube chiedevano due campi separati, senza alcuna ragione tecnica: il codice spezzava quell'URL nelle stesse due informazioni due righe dopo (`preg_match` in `get_mastodon_account_id`).
- **Diggita diventa supporto Lemmy generico.** Il fetcher parlava già Lemmy — `/feeds/u/{username}.xml` è il formato dei feed utente Lemmy, e il feed stesso si presenta come "diggita lemmy social" — ma con il dominio inchiodato nel codice. Ora l'istanza è un'impostazione e funziona qualsiasi istanza Lemmy. Il nome sulle schede viene dal dominio: `diggita.com` resta "Diggita", `lemmy.ml` è "Lemmy", `sh.itjust.works` è "Itjust" (un sottodominio corto viene saltato).
- Le impostazioni delle versioni precedenti vengono convertite alla lettura: l'URL Mastodon viene spezzato, Diggita diventa un'istanza Lemmy, i limiti per piattaforma sono riportati. L'opzione non viene riscritta: il database si allinea al primo salvataggio.
- Il vero logo Lemmy sostituisce un segnaposto disegnato a mano, e si aggiunge l'icona Pleroma (entrambe da Simple Icons, CC0).

### Added
- **Rilevamento del software** di un'istanza del fediverso via `/.well-known/nodeinfo` (in cache 7 giorni): Pleroma e Akkoma ottengono nome e icona propri invece di quelli di Mastodon, che sarebbe come marchiare Forgejo col logo di GitHub. L'href del documento nodeinfo viene accettato solo se punta allo stesso host dell'istanza, altrimenti sarebbe un SSRF servito su richiesta.
- Salvando un'istanza che non espone l'API pubblica arriva una spiegazione invece del silenzio. Misurato sul campo: **Mastodon** e **Pleroma/Akkoma** rispondono al lookup senza autenticazione; **GoToSocial** e **Friendica** rispondono 401; **Misskey** e **Sharkey** usano un'API propria; **Pixelfed** risponde al lookup ma reindirizza gli stati al login.
- **Esito per piattaforma nelle impostazioni**: dopo ogni aggiornamento della cache il plugin registra quanti contenuti ha portato ciascuna piattaforma configurata e mostra un avviso per quelle vuote, con il motivo quando lo conosce (istanza che richiede autenticazione, account non trovato, feed Lemmy chiesto all'istanza sbagliata).

### Fixed
- Il parser del profilo accettava **solo** la forma `/@utente`: un indirizzo legittimo come `/users/utente` non corrispondeva, `fetch_mastodon()` restituiva un array vuoto e Mastodon spariva dalla timeline **senza un avviso**, perché il log stava dietro `EG_SOCIAL_TIMELINE_DEBUG` che è `false` nei pacchetti. Ora riconosce anche `/users/utente` e `@utente@istanza`.
- Nel parser il delimitatore della regex era `#` e la classe conteneva `[^/?#]`: il carattere chiudeva l'espressione in anticipo (`Unknown modifier ']'`) e ogni URL veniva rifiutato. Trovato dai test prima del rilascio.
- Gli URL delle istanze vengono normalizzati e validati in un solo punto (`normalize_instance()`): solo HTTPS, host privati e riservati rifiutati.

## [1.8.1] - 2026-09-17

### Fixed
- **Il contrasto si calcola dal colore, non si deduce dallo schema.** Nella 1.8.0 testo, bordi, badge, link e icone seguivano lo schema colori globale: una scheda nera con schema "sempre chiaro" restava con testo e icone scuri sul nero. Ora il plugin misura il rapporto di contrasto WCAG del colore scelto per la superficie e sposta il primo piano sulla palette che contrasta di più (nessuna soglia arbitraria: si confrontano i due contrasti e vince il maggiore).
- Le icone sui chip dei filtri leggono token propri (`--egst-chip-icon-*`) invece di seguire la scheda: con una scheda scura si schiarivano pur stando su chip chiari.

### Added
- Sfondo delle schede **Trasparente**: la timeline diventa una lista senza superfici, con il solo bordo a delimitare i post (l'ombra viene rimossa, su una superficie trasparente sarebbe un artefatto).
- Avviso nel pannello quando il colore personalizzato non raggiunge il minimo WCAG AA (4.5:1) con nessuna delle due palette di testo, con il rapporto misurato: un viola o un grigio medio non permettono testo leggibile e tacerlo non aiuterebbe.

### Changed
- Sezione Aspetto semplificata: via l'impostazione **Schema colori** e le coppie di colori chiaro/scuro. Ogni superficie ha un solo menu (trasparente · preset neutro · segue il browser del visitatore · colore personalizzato) e un solo colore.
- Etichette dello stile icone riscritte in "Colori delle piattaforme" e "Un solo colore (quello del testo)": le icone SVG sono sagome monocromatiche senza colore proprio, è il foglio di stile a dipingerle. Se un file SVG porta i propri `fill`, quelli vincono e l'impostazione non ha effetto su quella piattaforma.
- Il foglio di stile non contiene più nessuna `@media (prefers-color-scheme: dark)`: i colori sono organizzati in due palette sorgente (`--egst-light-*`, `--egst-dark-*`) e in token attivi che le leggono. La media query viene emessa nel CSS inline solo se una superficie segue il browser, oppure se entrambe sono trasparenti e non c'è alcun colore da misurare.
- Le impostazioni salvate con la 1.8.0 vengono convertite alla lettura (`color_scheme` + coppie di colori → una scelta e un colore per superficie), senza riscrivere l'opzione: il database si allinea al primo salvataggio dal pannello.

## [1.8.0] - 2026-09-17

### Added
- Nuova sezione **Aspetto** nelle impostazioni: schema colori (sempre chiaro / sempre scuro / segue il browser del visitatore), sfondo della timeline, sfondo delle schede e stile delle icone di piattaforma.
- Lo sfondo del contenitore della timeline si imposta in modo indipendente da quello delle schede, con una coppia di colori distinti per schema chiaro e scuro (preset trasparente/neutro oppure colori personalizzati).
- Stile icone selezionabile: colori brand oppure monocromatico (le icone monocromatiche seguono il colore del testo, quindi diventano bianche sulle schede scure).

### Fixed
- Le icone delle piattaforme erano **sempre nere**: i file SVG Simple Icons non hanno l'attributo `fill`, quindi le regole `color:` del foglio di stile restavano inerti e sulle schede scure le icone diventavano invisibili. Aggiunto `fill: currentColor`, colore per piattaforma anche sui chip dei filtri e varianti brand schiarite sullo schema scuro.

### Changed
- ⚠️ **Il tema scuro non è più imposto dal browser.** Il default è ora "sempre chiaro": la `@media (prefers-color-scheme: dark)` si applica solo scegliendo "segue il browser del visitatore". Per riavere il comportamento della 1.7.2 impostare Schema colori = "Segue il browser del visitatore".
- Tutti i colori del CSS passano da custom properties (`--egst-*`) dichiarate sul contenitore `.eg-social-timeline`: un CSS personalizzato può ridefinire il tema in un punto solo invece di inseguire ogni singola regola. I due blocchi di token scuri (classe e media query) sono volutamente duplicati: in CSS puro una `@media` non si può raggruppare con un selettore di classe.
- Il messaggio "nessun post disponibile" è racchiuso nel contenitore della timeline, così eredita i colori scelti.
- `readme.txt`: `Tested up to` allineato a WordPress 7.1.

### Accessibility
- L'elemento `<title>` delle icone SVG non viene più rimosso dalla sanitizzazione kses, così ogni icona conserva il proprio nome accessibile.

## [1.7.2] - 2026-07-05

### Fixed
- Le anteprime immagini ora riempiono la larghezza della card in modo uniforme su tutte le piattaforme. Le immagini sorgente piccole (es. le miniature `preview_url` di Mastodon) venivano mostrate alla loro dimensione naturale ridotta, mentre quelle più grandi (Bluesky, PeerTube) riempivano la card; aggiunto `width: 100%` a `.post-image img` per renderle coerenti.

## [1.7.1] - 2026-07-05

### Added
- Anteprime immagini per i post Bluesky, alla pari di Mastodon: viene mostrata la prima immagine del post (embed immagine diretto o quote-post con media) quando l'opzione "Show Image Previews" è attiva, incluso il testo alternativo. Le miniature delle card di link esterni sono ignorate di proposito.

### Note
- Nessuna nuova impostazione: riusa il toggle "Show Image Previews" esistente.

## [1.7.0] - 2026-07-05

### Added
- Integrazione PeerTube tramite l'API REST pubblica (`GET /api/v1/accounts/{account}/videos`, senza autenticazione): basta indicare nome account e URL dell'istanza nelle impostazioni.
- Anteprime con le miniature dei video PeerTube (rispettano l'opzione "Show Image Previews") ed etichetta dedicata del link "Watch video".
- Limite per piattaforma dei video PeerTube (default 5), filtro di piattaforma, icona e colore brand dedicati.

### Security
- L'URL dell'istanza PeerTube è accettato solo su HTTPS, con validazione anti-SSRF (rifiuta host privati/riservati).

### Changed
- Rigenerato il `.pot` e allineata la traduzione italiana (`it_IT` .po/.mo) alle nuove stringhe.

## [1.6.7] - 2026-06-18

### Changed
- Tradotte in inglese le ultime 3 stringhe sorgente rimaste in italiano nella sezione "Utilizzo" del pannello admin (il resto del plugin era già in inglese dalla 1.6.6).
- Rigenerato il `.pot` con `wp i18n make-pot` e ri-allineata la traduzione italiana (`it_IT` .po/.mo) al sorgente attuale: aggiunte le stringhe mancanti, corretti i fuzzy errati e tradotta la Description.

### Note
- Nessuna modifica funzionale, al database o alle impostazioni.

## [1.6.6] - 2026-06-02

### Fixed
- Commenti `translators:` aggiunti a tutte le stringhe i18n con placeholder.
- `strip_tags()` sostituita con `wp_strip_all_tags()` (×4).
- `date()` sostituita con `gmdate()` per sicurezza sui fusi orari.
- `wp_unslash()` + `sanitize_text_field()` aggiunte alla verifica del nonce.
- Chiamate `error_log()` marcate con `phpcs:ignore` (già protette da `EG_SOCIAL_TIMELINE_DEBUG`).
- `phpcs:ignore` sull'output delle icone SVG (hardcoded, sanitizzate internamente).
- `esc_html()` aggiunta all'output della costante `EG_SOCIAL_TIMELINE_VERSION`.

### Removed
- `load_plugin_textdomain()` — non necessaria da WP 4.6+ con file `.mo` compilati.

### Changed
- Tag ridotti a 5 (limite Plugin Check).

## [1.6.1] - 2026-06-02

### Fixed
- Gli action links ora usano msgid inglesi (`Settings`, `Documentation`), tradotti correttamente in italiano via `it_IT.mo`.
- Aggiunto `rel="noopener noreferrer"` al link Documentazione.

## [1.6.0] - 2026-06-02

### Changed
- **Refactoring i18n completo**: tutte le stringhe PHP ora hanno msgid in inglese (convenzione WordPress).
- `it_IT.po`/`.mo` ricostruiti con traduzioni inglese→italiano corrette.
- `en_US.po`/`.mo` rimossi — l'inglese è ora il fallback nativo.

## [1.5.3] - 2026-06-02

### Fixed
- "Filtra per piattaforma:" inserita in `esc_html_e()` e aggiunta a `it_IT.po`/`.mo`.

## [1.5.1] - 2026-06-02

### Fixed
- Commento `translators:` spostato sulla riga immediatamente sopra `esc_html__()` (compliance PHPCS).
- `readme.txt` tradotto in inglese (compliance Plugin Check).

## [1.5.0] - 2026-06-02

### Fixed
- Tutti i `strip_tags()` sostituiti con `wp_strip_all_tags()`.
- `date()` sostituito con `gmdate()` per correttezza sui fusi orari.
- La verifica del nonce usa `wp_unslash()` + `sanitize_text_field()`.
- Chiamate `error_log()` marcate con `phpcs:ignore` (già condizionate da `EG_SOCIAL_TIMELINE_DEBUG`).
- `esc_html()` aggiunta alla costante `EG_SOCIAL_TIMELINE_VERSION`.

### Removed
- `load_plugin_textdomain()` — non necessaria da WordPress 4.6+.

## [1.4.6] - 2026-05-25

### Changed
- readme.txt riscritto: struttura più chiara, rimossi riferimenti a versioni obsolete, liste con `-` invece di `* **bold**`
- Tested up to aggiornato a WordPress 7.0
- EG Forgejo Updater sostituisce Git Updater nelle istruzioni di installazione

---

## [1.4.5] - 2026-05-25

### Security
- Sanitizzazione `forgejo_instance`: aggiunta validazione HTTPS — URL non HTTPS vengono silenziosamente sostituiti dal default `https://gitea.com`

---

## [1.4.4] - 2026-05-24

### Fixed
- Forgejo: il parametro `sort=recentupdate` non è supportato dall'endpoint `/users/{username}/repos` — sostituito con ordinamento client-side per `updated_at` decrescente tramite `usort()`

---

## [1.4.3] - 2026-05-24

### Added
- Supporto anteprime immagini: nuova opzione admin "Mostra Anteprime Immagini" (default: disabilitato)
- Mastodon: estrazione prima immagine da `media_attachments` con `preview_url` e testo alt
- Post senza testo ma con immagine: mostra solo l'immagine senza area testo vuota
- Attributo `loading="lazy"` sulle immagini per prestazioni

### Fixed
- Forgejo: bug logica fetch repo — i repo venivano restituiti in ordine di creazione, causando l'esclusione dei repo più recentemente aggiornati. Fix: aggiunto `?sort=recentupdate` all'API e limitato il numero di repo interrogati a `min(repo_count, limit)`, garantendo che i commit più recenti provengano sempre dai repo più attivi

### Changed
- Forgejo: array `$public_repos` ora re-indicizzato con `array_values()` dopo il filtro
- Tutte le piattaforme: campi `image_url` e `image_alt` aggiunti alla struttura del post (vuoti per Diggita, Forgejo, Bluesky — pronti per future integrazioni)

---

## [1.4.2] - 2026-05-24

### Added
- Traduzioni italiano (`it_IT`) e inglese (`en_US`) con file `.pot`, `.po` e `.mo`

### Fixed
- Link "Vedi post originale" / "Vedi commit" sempre allineato a destra nel footer del post, anche in assenza di statistiche interazione

### Changed
- Cache ID account Mastodon estesa da 24 ore a 30 giorni (`MONTH_IN_SECONDS`): il dato non cambia mai durante la vita dell'account

---

## [1.4.1] - 2026-05-02

### Added
- Nuovo campo admin: Lunghezza Testo Post (default: 300, range: 50–600, 0 = testo completo)
- Troncamento post configurabile per tutte le piattaforme (Mastodon, Diggita, Forgejo, Bluesky)

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

