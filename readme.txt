=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, fediverse, social, timeline, diggita, lemmy, api, statistics
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.1.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Timeline unificata Mastodon/Diggita con statistiche interazioni live. API native, icone modulari, filtro boost funzionante. Privacy-first.

== Description ==

**EG Social Timeline** è un plugin WordPress che mostra una timeline cronologica unificata delle tue attività sui social network decentralizzati con **statistiche interazioni in tempo reale**.

= Novità v1.1.x =

* **API Mastodon Nativa** - Dati completi invece di RSS limitato
* **Statistiche Live** - Conteggi like, boost e risposte in tempo reale
* **Filtro Boost Funzionante** - Mostra/nascondi boost via API
* **Icone Modulari** - File SVG separati facilmente sostituibili
* **Badge Boost** - Indicatore visivo post riboostati
* **Fix Leggibilità** - Nessuna duplicazione contenuto
* **URL Corretti** - Link boost aprono pagina web (non JSON)

= Caratteristiche Principali =

* **API Native & RSS** - Mastodon API per dati completi, RSS per Diggita
* **Statistiche Interazioni** - ❤️ like, 🔁 boost, 💬 risposte (Mastodon)
* **Privacy-First** - Nessun tracker, cookies o servizi esterni
* **Fediverso-Native** - ActivityPub (Mastodon) e Lemmy (Diggita)
* **Timeline Unificata** - Tutti i post in ordine cronologico
* **Filtro Boost** - Mostra/nascondi boost e repost (funzionante!)
* **Icone SVG Modulari** - Facili da sostituire e personalizzare
* **Caching Intelligente** - Riduce carico server (30min-24h)
* **Design Moderno** - Cards responsive, dark mode, statistiche colorate
* **Zero Dipendenze** - Nessun JavaScript esterno
* **Open Source** - Codice trasparente GPL-2.0-or-later

= Piattaforme Supportate =

**Corrente (v1.1.2):**
* **Mastodon** - API nativa con statistiche complete
* **Diggita** - Feed RSS (Lemmy)

**Prossimamente (v1.2.0):**
* **Bluesky** - AT Protocol con statistiche

= Come Funziona =

1. Configura almeno un profilo (Mastodon o Diggita)
2. **Mastodon**: API nativa ottiene post + statistiche (like/boost/risposte)
3. **Diggita**: Feed RSS standard
4. Post uniti, ordinati cronologicamente e cachati
5. Timeline con statistiche colorate e badge boost
6. Inserisci `[eg_social_timeline]` ovunque

**Esempio Output:**

```
🐘 Mastodon  🔁 Boost  2 ore fa
──────────────────────────────
Testo del post social...

❤️ 16  🔁 10  💬 2
Vedi post originale →
```

= Utilizzo =

Shortcode base:
`[eg_social_timeline]`

Con limite personalizzato:
`[eg_social_timeline limit="20"]`

= Configurazione =

**Impostazioni → EG Social Timeline:**

* **URL Profilo Mastodon** - Es: https://mastodon.uno/@username
* **Username Diggita** - Es: emanuelegori (senza @)
* **Numero Post** - 1-50 (default: 10)
* **Durata Cache** - 30min, 1h, 2h, 4h, 8h, 24h
* **Includi Boost** - Mostra/nascondi boost (ora funzionante!)
* **Mostra Statistiche** - Abilita/disabilita conteggi interazioni

Almeno un profilo obbligatorio.

== Installation ==

= Installazione Automatica =

1. WordPress → Plugin → Aggiungi nuovo
2. Cerca "EG Social Timeline"
3. Installa e attiva
4. Impostazioni → EG Social Timeline per configurare

= Installazione Manuale =

1. Scarica ZIP: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
2. WordPress → Plugin → Aggiungi nuovo → Carica plugin
3. Seleziona ZIP, installa e attiva
4. Impostazioni → EG Social Timeline per configurare

= Via FTP =

1. Scarica ed estrai ZIP
2. Carica cartella `eg-social-timeline` in `/wp-content/plugins/`
3. Attiva da pannello Plugin
4. Impostazioni → EG Social Timeline per configurare

= Post-Installazione =

1. Impostazioni → EG Social Timeline
2. Inserisci almeno un profilo (Mastodon o Diggita)
3. Configura opzioni (statistiche, boost, cache)
4. Salva impostazioni
5. Inserisci `[eg_social_timeline]` in articolo/pagina

== Frequently Asked Questions ==

= Quali piattaforme sono supportate? =

**Corrente (v1.1.2):**
* Mastodon (API nativa + statistiche)
* Diggita/Lemmy (RSS)

**Prossime:**
* Bluesky (v1.2.0)

= Cosa sono le statistiche interazioni? =

Per post Mastodon, il plugin mostra:
* ❤️ Preferiti (like)
* 🔁 Boost (reblog)
* 💬 Risposte (replies)

Conteggi in tempo reale tramite API Mastodon.

= Il plugin raccoglie dati personali? =

**NO!** Il plugin:
* Usa solo API pubbliche e RSS
* Non installa tracker o analytics
* Non invia dati a servizi esterni
* Non usa cookie
* Cache locale su server WordPress
* GDPR-compliant al 100%

= Come funziona il filtro boost? =

**v1.1.x**: Funzionante tramite API Mastodon!

Opzione "Includi Boost/Repost":
* **Abilitato**: Mostra post originali + boost
* **Disabilitato**: Solo post originali

Badge visivo "🔁 Boost" su post riboostati.

= Le icone sono personalizzabili? =

**Sì!** (novità v1.1.0)

Icone in file SVG separati (`social-icons/`):
* `mastodon.svg`
* `diggita.svg`
* `bluesky.svg`
* `generic.svg` (fallback)

Sostituisci file SVG per personalizzare!

= Come svuoto la cache? =

Impostazioni → EG Social Timeline → bottone "Svuota Cache Ora"

Cache si svuota automaticamente salvando impostazioni.

= Compatibile con il mio tema? =

Sì! HTML semantico e CSS standard.

Funziona con qualsiasi tema WordPress moderno.

= Supporta Gutenberg? =

**Shortcode**: Usa blocco "Shortcode" di Gutenberg
**Blocco nativo**: Pianificato v1.3.0

Intanto: inserisci `[eg_social_timeline]` nel blocco Shortcode.

= Come aggiorno da v1.0.0? =

**Metodo 1 - Automatico (Raccomandato):**
Installa [Git Updater](https://git-updater.com/)

**Metodo 2 - Manuale:**
Scarica ZIP nuova versione e sostituisci file.

**Breaking Changes**: Nessuno! Retrocompatibile.

Configurazioni salvate in database (non perse).

= Dove trovo supporto? =

* Documentazione: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* CHANGELOG: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/src/branch/main/CHANGELOG.md
* Blog: https://emanuelegori.uno
* Fediverso: @emanuelegori@mastodon.uno

== Screenshots ==

1. Timeline unificata con statistiche interazioni (❤️ 🔁 💬)
2. Badge boost su post riboostati
3. Pannello amministrazione con tutte le opzioni
4. Design responsive su mobile con dark mode
5. Icone SVG colorate per piattaforme
6. Card post con separatori e spaziatura ottimizzata

== Changelog ==

= 1.1.2 (2026-01-10) =
* Fix: URL boost non terminano più con /activity
* Fix: Link boost ora aprono pagina web invece di JSON
* Migliorato: Link puntano sempre a post originale

= 1.1.1 (2026-01-10) =
* Fix: Rimossa duplicazione contenuto (titolo + testo)
* Fix: Migliorata leggibilità generale
* Migliorato: Badge boost ridimensionato (discreto)
* Migliorato: Spaziatura e contrasti
* Migliorato: Dark mode ottimizzato
* Migliorato: Separatore header post

= 1.1.0 (2026-01-10) =
* Novità: API Mastodon nativa (sostituisce RSS)
* Novità: Statistiche interazioni (like/boost/risposte)
* Novità: Filtro boost funzionante via API
* Novità: Badge visivo per post boostati
* Novità: Icone SVG modulari in cartella separata
* Novità: Opzione "Mostra Statistiche" in admin
* Migliorato: Icone professionali (Mastodon, Diggita, Bluesky)
* Migliorato: Performance con cache Account ID (24h)

= 1.0.0 (2026-01-09) =
* Prima release pubblica (MVP)
* Supporto Mastodon RSS feed
* Supporto Diggita/Lemmy RSS feed
* Timeline unificata cronologica
* Caching intelligente configurabile
* Shortcode [eg_social_timeline]
* Admin panel completo
* Design responsive con dark mode
* Zero dipendenze JavaScript

Vedi [CHANGELOG.md](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/src/branch/main/CHANGELOG.md) per dettagli completi.

== Upgrade Notice ==

= 1.1.2 =
Hotfix: Corregge URL boost che mostravano JSON. Aggiornamento raccomandato se usi boost.

= 1.1.1 =
Hotfix critico: Risolve duplicazione contenuto. Aggiornamento altamente raccomandato!

= 1.1.0 =
Grande aggiornamento! API Mastodon + statistiche interazioni. Retrocompatibile, nessun breaking change.

= 1.0.0 =
Prima release pubblica. Nessuna migrazione necessaria.

== Roadmap ==

**v1.2.0 - Bluesky:**
* Integrazione API pubblica Bluesky
* Statistiche Bluesky
* Supporto AT Protocol

**v1.3.0 - Advanced:**
* Blocco Gutenberg nativo
* Visualizzazione media/immagini
* Paginazione timeline
* Filtri hashtag
* Widget WordPress nativo
* Traduzioni complete (IT/EN)

== Privacy Policy ==

EG Social Timeline rispetta completamente la tua privacy:

**Non raccoglie:**
* Dati personali utenti
* Cookie o storage browser
* Analytics o statistiche d'uso

**Usa solo:**
* API pubbliche Mastodon (nessuna autenticazione)
* Feed RSS pubblici Diggita
* Cache locale server WordPress

**Conformità:**
* GDPR-compliant
* No tracker
* No servizi esterni
* Codice open source verificabile

Le statistiche mostrate (like/boost) sono **conteggi anonimi** da API pubbliche, non identificano utenti.

== Credits ==

Sviluppato da [Emanuele Gori](https://emanuelegori.uno)

**Altri plugin dell'autore:**
* [EG Sharebar Fedi](https://git.emanuelegori.uno/emanuelegori/eg-sharebar-fedi) - Barra condivisione Fediverso
* [EG Fediverso Box](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-box) - Box follow Fediverso
* [EG Fediverso Page](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-page) - Pagina educativa Fediverso
* [EG Contact Form Privacy](https://git.emanuelegori.uno/emanuelegori/eg-contact-form-privacy) - Form contatti privacy-first

**Supporta lo sviluppo:**
* ⭐ Stella su Gitea
* 🐘 Follow su Mastodon: @emanuelegori@mastodon.uno
* 📝 Blog post e condivisioni

== Technical Details ==

**Architettura v1.1.x:**

* Mastodon: API nativa `/api/v1/accounts/{id}/statuses`
* Diggita: Feed RSS Lemmy standard
* Cache: WordPress Transients API
* Storage: Nessuno (solo cache temporanea)
* Frontend: CSS puro, zero JavaScript
* Backend: WordPress HTTP API

**Performance:**
* Account ID cache: 24h (riduce lookup)
* Timeline cache: Configurabile (30min-24h)
* Lazy loading: Solo quando shortcode presente
* CSS minificato: <5KB

**Compatibilità:**
* WordPress: 5.0+
* PHP: 7.4, 8.0, 8.1, 8.2, 8.3
* MySQL: 5.7+
* Temi: Tutti i temi moderni
* Multisite: Compatibile

== License ==

GNU General Public License v2.0 or later
https://www.gnu.org/licenses/gpl-2.0.html

Questo programma è software libero; puoi redistribuirlo e/o modificarlo secondo i termini della GNU General Public License come pubblicata dalla Free Software Foundation; versione 2 della Licenza, o (a tua scelta) qualsiasi versione successiva.

Testo completo:
* Inglese: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/src/branch/main/LICENSE.md
* Italiano: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/src/branch/main/LICENSE.IT.md
