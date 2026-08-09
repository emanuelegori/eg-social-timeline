# EG Social Timeline

[![Versione](https://img.shields.io/badge/Versione-1.7.2-green)](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)
[![Licenza](https://img.shields.io/badge/Licenza-GPL--2.0--or--later-blue.svg)](LICENSE.IT.md)
[![WordPress](https://img.shields.io/badge/WordPress-5.0+-orange.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net)

Plugin WordPress per mostrare una timeline cronologica unificata delle tue attività social da **Mastodon**, **Diggita** (Lemmy), **PeerTube**, **Forgejo/Gitea** e **Bluesky**.

---

## Caratteristiche

- **Timeline Unificata**: Aggrega post da multiple piattaforme in ordine cronologico
- **Piattaforme Supportate**:
  - **Mastodon** (e compatibili ActivityPub)
  - **Diggita** (Lemmy) con statistiche complete
  - **Forgejo/Gitea** (commit repository)
  - **Bluesky** (API pubblica ATP, nessuna autenticazione)
  - **PeerTube** (API REST pubblica, video dal tuo account)
- **Limiti Configurabili per Piattaforma**: Previene che una piattaforma monopolizzi la timeline
- **Filtri Interattivi**: Sistema filtri CSS puro per mostrare/nascondere piattaforme
- **Sistema Icone Modulare**: Icone SVG caricate da file, facilmente personalizzabili
- **Cache Intelligente**: Riduce richieste API con cache configurabile
- **Statistiche Interazioni**: Mostra like, boost e commenti per ogni post
- **Responsive**: Design ottimizzato per desktop, tablet e mobile
- **Dark Mode**: Supporto automatico tema scuro
- **Privacy-Friendly**: Solo dati pubblici, nessun tracking

---

## Screenshot

![EG Social Timeline — frontend](assets/screenshot-1.png)

---

## Installazione

### Automatica (WordPress)

1. Scarica l'ultima versione da [Forgejo](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)
2. Vai su **Plugin → Aggiungi nuovo → Carica plugin**
3. Seleziona file ZIP scaricato
4. Clicca **Installa** e poi **Attiva**

### Manuale (FTP/SSH)

```bash
cd wp-content/plugins
git clone https://git.emanuelegori.uno/emanuelegori/eg-social-timeline.git
```

### EG Forgejo Updater (Consigliato)

Installa [EG Forgejo Updater](https://git.emanuelegori.uno/emanuelegori/eg-forgejo-updater) per aggiornamenti automatici da Forgejo.

---

## Configurazione

1. Vai su **Impostazioni → EG Social Timeline**
2. Configura almeno un profilo:
   - **Mastodon**: URL completo profilo (es: `https://mastodon.uno/@emanuelegori`)
   - **Diggita**: Username (senza @)
   - **Forgejo**: Username + URL istanza (es: `https://git.emanuelegori.uno`)
   - **Bluesky**: Handle (es: `emanuele.bsky.social`, senza @)
   - **PeerTube**: Nome account + URL istanza (es: `emanuelegori` + `https://peertube.uno`)
3. Configura limiti per piattaforma (opzionale):
   - Max post Mastodon (default: 20, 0 = illimitato)
   - Max post Diggita (default: 10, 0 = illimitato)
   - Max commit Forgejo (default: 5, 0 = illimitato)
   - Max post Bluesky (default: 10, 0 = illimitato)
   - Max video PeerTube (default: 5, 0 = illimitato)
4. Regola impostazioni cache e visualizzazione
5. Salva

![Configurazione profili](assets/screenshot-2.png)
![Limiti post per piattaforma](assets/screenshot-3.png)

---

## Utilizzo

### Shortcode Base

```
[eg_social_timeline]
```

### Con Limite Personalizzato

```
[eg_social_timeline limit="20"]
```

### Esempio Completo

```
<h2>La mia attività recente</h2>
[eg_social_timeline limit="50"]
```

---

## Filtri Piattaforme

Sistema filtri CSS integrato:

```
┌──────────────────────────────────────┐
│ Filtra per piattaforma:              │
│ ☑ Mastodon (12) ☑ Diggita (8)       │
│ ☑ Forgejo (5)   ☐ Bluesky (2)       │
└──────────────────────────────────────┘
```

Click checkbox = mostra/nascondi post istantaneamente (zero JavaScript richiesto!)

---

## Personalizzazione

### Icone Piattaforme

Le icone sono file SVG in `social-icons/`:

```
social-icons/
├── mastodon.svg
├── diggita.svg
├── forgejo.svg
├── bluesky.svg
└── blog.svg
```

**Per personalizzare:**
1. Sostituisci file SVG con tua icona
2. Mantieni dimensioni 24x24px e `viewBox="0 0 24 24"`
3. Usa `fill="currentColor"` per eredità colore

### CSS Personalizzato

Crea `wp-content/themes/tuo-tema/eg-social-timeline-custom.css`:

```css
/* Cambia colore primario */
.eg-timeline-filters {
    border-color: #YOUR_COLOR;
}

/* Personalizza card post */
.timeline-item {
    background: #YOUR_BG;
}
```

---

## Sviluppo

### Requisiti

- WordPress 5.0+
- PHP 7.4+
- API access alle piattaforme configurate

### Struttura File

```
eg-social-timeline/
├── eg-social-timeline.php    # Plugin principale
├── eg-social-timeline.css     # Stili
├── social-icons/              # Icone SVG
│   ├── mastodon.svg
│   ├── diggita.svg
│   ├── forgejo.svg
│   └── bluesky.svg
├── languages/                 # Traduzioni
├── README.md
├── readme.txt                 # WordPress readme
└── LICENSE
```

### API Utilizzate

- **Mastodon**: `/api/v1/accounts/{id}/statuses`
- **Diggita**: RSS `/feeds/u/{username}.xml` (con parsing statistiche)
- **Forgejo**: `/api/v1/users/{username}/repos` + `/api/v1/repos/{owner}/{repo}/commits`
- **Bluesky**: `https://public.api.bsky.app/xrpc/app.bsky.feed.getAuthorFeed` (pubblica, nessun token)

---

## Changelog

### [1.7.2] - 2026-07-05

#### Fixed
- Le anteprime immagini ora riempiono la larghezza della card in modo uniforme su tutte le piattaforme — le immagini sorgente piccole (es. le miniature `preview_url` di Mastodon) venivano mostrate alla loro dimensione naturale ridotta mentre quelle più grandi (Bluesky, PeerTube) riempivano la card; aggiunto `width: 100%` a `.post-image img`

### [1.7.1] - 2026-07-05

#### Added
- Anteprime immagini per i post Bluesky, alla pari di Mastodon: viene mostrata la prima immagine del post (embed immagine diretto o quote-post con media) quando l'opzione "Show Image Previews" è attiva, testo alternativo incluso. Le miniature delle card di link esterni sono ignorate di proposito

#### Note
- Nessuna nuova impostazione: riusa il toggle "Show Image Previews" esistente

### [1.7.0] - 2026-07-05

#### Added
- Integrazione PeerTube tramite l'API REST pubblica (`GET /api/v1/accounts/{account}/videos`, senza autenticazione): basta indicare nome account e URL dell'istanza nelle impostazioni
- Anteprime con le miniature dei video PeerTube (rispettano l'opzione "Show Image Previews") ed etichetta dedicata del link "Watch video"
- Limite per piattaforma dei video PeerTube (default 5), filtro di piattaforma, icona e colore brand dedicati

#### Security
- L'URL dell'istanza PeerTube è accettato solo su HTTPS, con validazione anti-SSRF (rifiuta host privati/riservati)

#### Changed
- Rigenerato il `.pot` e allineata la traduzione italiana (`it_IT` .po/.mo) alle nuove stringhe

### [1.6.7] - 2026-06-18

#### Changed
- Tradotte in inglese le ultime 3 stringhe sorgente rimaste in italiano nella sezione "Utilizzo" del pannello admin (il resto del plugin era già in inglese dalla 1.6.6)
- Rigenerato il `.pot` con `wp i18n make-pot` e ri-allineata la traduzione italiana (`it_IT` .po/.mo) al sorgente attuale: aggiunte le stringhe mancanti, corretti i fuzzy errati e tradotta la Description

#### Note
- Nessuna modifica funzionale, al database o alle impostazioni

### [1.6.6] - 2026-06-02

#### Fixed
- Commenti `translators:` aggiunti a tutte le stringhe i18n con placeholder
- `strip_tags()` sostituita con `wp_strip_all_tags()` (×4)
- `date()` sostituita con `gmdate()` per sicurezza sui fusi orari
- `wp_unslash()` + `sanitize_text_field()` aggiunte alla verifica del nonce
- Chiamate `error_log()` marcate con `phpcs:ignore` (già protette da `EG_SOCIAL_TIMELINE_DEBUG`)
- `phpcs:ignore` sull'output delle icone SVG (hardcoded, sanitizzate internamente)
- `esc_html()` aggiunta all'output della costante `EG_SOCIAL_TIMELINE_VERSION`

#### Removed
- `load_plugin_textdomain()` — non necessaria da WP 4.6+ con file `.mo` compilati

#### Changed
- Tag ridotti a 5 (limite Plugin Check)

### [1.6.1] - 2026-06-02

#### Fixed
- Action links ora usano msgid inglesi (`Settings`, `Documentation`) — tradotti correttamente in italiano via `it_IT.mo`
- Aggiunto `rel="noopener noreferrer"` al link Documentazione

### [1.6.0] - 2026-06-02

#### Changed
- **Refactoring i18n completo**: tutte le stringhe PHP ora hanno msgid in inglese (convenzione WordPress)
- `it_IT.po`/`.mo` ricostruiti con traduzioni inglese→italiano corrette
- `en_US.po`/`.mo` rimossi — l'inglese è ora il fallback nativo

### [1.5.3] - 2026-06-02

#### Fixed
- "Filtra per piattaforma:" inserita in `esc_html_e()` e aggiunta a `it_IT.po`/`.mo`

### [1.5.1] - 2026-06-02

#### Fixed
- Commento `translators:` spostato sulla riga immediatamente sopra `esc_html__()` (compliance PHPCS)
- `readme.txt` tradotto in inglese (compliance Plugin Check)

### [1.5.0] - 2026-06-02

#### Fixed
- Tutti i `strip_tags()` sostituiti con `wp_strip_all_tags()`
- `date()` sostituito con `gmdate()` per correttezza timezone
- Verifica nonce usa `wp_unslash()` + `sanitize_text_field()`
- `error_log()` marcati con `phpcs:ignore` (già condizionati da `EG_SOCIAL_TIMELINE_DEBUG`)
- `esc_html()` aggiunto alla costante `EG_SOCIAL_TIMELINE_VERSION`

#### Removed
- `load_plugin_textdomain()` — non necessario da WordPress 4.6+

### [1.4.6] - 2026-05-25

#### Changed
- readme.txt riscritto con struttura più chiara
- Tested up to aggiornato a WordPress 7.0

### [1.4.5] - 2026-05-25

#### Security
- Validazione HTTPS sull'URL istanza Forgejo durante sanitizzazione

#### Added
- Screenshot frontend, configurazione e limiti per piattaforma

### [1.4.4] - 2026-05-24

#### Fixed
- Forgejo: ordinamento repo per `updated_at` lato client con `usort()` — il parametro `sort=recentupdate` non è supportato dall'endpoint `/users/{username}/repos`

### [1.4.3] - 2026-05-24

#### Added
- Supporto anteprime immagini con nuova opzione admin (default: disabilitato)
- Mastodon: estrazione prima immagine da `media_attachments` con `preview_url` e testo alt
- Attributo `loading="lazy"` sulle immagini

#### Fixed
- Forgejo: repo ordinati per ultimo push con `?sort=recentupdate`; interrogati solo i repo necessari (`min(repo_count, limit)`). Prima i repo più recenti venivano esclusi perché l'API restituiva i repo in ordine di creazione

#### Changed
- Struttura post unificata: campi `image_url` e `image_alt` presenti su tutte le piattaforme

### [1.4.2] - 2026-05-24

#### Added
- Traduzioni italiano (`it_IT`) e inglese (`en_US`) con file `.pot`, `.po` e `.mo`

#### Fixed
- Link "Vedi post originale" / "Vedi commit" sempre allineato a destra nel footer, anche senza statistiche

#### Changed
- Cache ID account Mastodon estesa da 24 ore a 30 giorni

### [1.4.1] - 2026-05-02

#### Added
- Lunghezza testo post configurabile da admin (default: 300, range: 50–600, 0 = testo completo senza limiti)

### [1.4.0] - 2026-05-02

#### Added
- Integrazione Bluesky via API pubblica ATP (`app.bsky.feed.getAuthorFeed`)
- Nuovo campo admin: Handle Bluesky (es. `emanuele.bsky.social`, senza @)
- Nuovo campo admin: Max Post Bluesky (default: 10, range: 0-100)
- Filtro piattaforma Bluesky nella timeline (CSS-only)
- Statistiche Bluesky: like, repost, risposte
- Supporto repost rispettando l'opzione "Includi Boost/Repost"
- Rimozione automatica `@` iniziale dall'handle in fase di sanitizzazione

#### Changed
- Validazione "almeno un profilo" estesa a Bluesky
- Messaggi di errore admin aggiornati

### [1.3.1] - 2026-04-06

#### Security
- Aggiunto `LIBXML_NONET` al parsing XML del feed RSS Diggita (anti-XXE)
- Sanitizzazione SVG inline con `wp_kses()` in `eg_social_timeline_get_icon()` (anti-XSS)
- Validazione anti-SSRF sulle URL API esterne: nuova funzione `eg_social_timeline_is_public_url()` rifiuta IP privati, riservati e localhost

#### Fixed
- Aggiunto `esc_url()` mancante su link admin nello shortcode

### [1.3.0] - 2026-01-11

#### Added
- Limiti configurabili per piattaforma nelle impostazioni admin
- Nuovi campi: Max post Mastodon, Max post Diggita, Max commit Forgejo
- Valore 0 = nessun limite (comportamento v1.2.x)
- Timeline più equilibrata: previene monopolizzazione da singola piattaforma

#### Changed
- Logica fetch modificata per rispettare limiti per piattaforma
- Forgejo: limite TOTALE commit invece di per-repo
- Default sensati: Mastodon 20, Diggita 10, Forgejo 5
- Limite totale timeline aumentato: 1-100 (era 1-50)

### [1.2.5] - 2026-01-11

#### Fixed
- Diggita statistiche: `<br>` tag convertiti in `\n` prima di `strip_tags()` per parsing corretto
- Forgejo nome repository: ora visibile in timeline ("Commit to {repo}: {message}")
- Pulsanti filtri: larghezza automatica risolve altezza disuniforme
- Icone filtri: dimensioni uniformi senza distorsione

#### Changed
- CSS: rimossa larghezza fissa pulsanti filtri (era 145px → auto)
- CSS: rimosso `object-fit: contain` da icone per rendering uniforme
- Diggita: parsing usa `str_replace` per `<br>` prima di `strip_tags()`

### [1.2.4] - 2026-01-11

#### Fixed
- Diggita: parsing HTML robusto con `strip_tags()`
- Forgejo: nome repo incluso nel content

### [1.2.3] - 2026-01-11

#### Fixed
- Filtri CSS: logica invertita
- Forgejo: link pagina commits
- UI: box filtri compatto

### [1.2.0-1.2.2] - 2026-01-11
- Integrazione Forgejo, fix vari

### [1.0.0-1.1.2] - 2026-01-10/11
- Release iniziali, API Mastodon

---

## Contributi

I contributi sono benvenuti!

1. Fork repository
2. Crea branch feature (`git checkout -b feature/AmazingFeature`)
3. Commit modifiche (`git commit -m 'Add AmazingFeature'`)
4. Push branch (`git push origin feature/AmazingFeature`)
5. Apri Pull Request

---

## Licenza

Questo progetto è rilasciato sotto licenza **GPL-2.0-or-later**.

Vedi file [LICENSE](LICENSE) per dettagli completi.

---

## Autore

**Emanuele Gori**

- Website: [emanuelegori.uno](https://emanuelegori.uno)
- Mastodon: [@emanuelegori@mastodon.uno](https://mastodon.uno/@emanuelegori)
- Gitea: [git.emanuelegori.uno](https://git.emanuelegori.uno/emanuelegori)

---

## Ringraziamenti

- Community Mastodon per API ben documentate
- Diggita.com per piattaforma Lemmy italiana
- Forgejo/Gitea per eccellente API
- WordPress community

