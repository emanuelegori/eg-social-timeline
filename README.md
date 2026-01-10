# EG Social Timeline

Plugin WordPress per visualizzare una timeline cronologica unificata delle tue attività social da Mastodon, Diggita e Bluesky con statistiche interazioni in tempo reale. Privacy-first, nessun tracker, API native e feed RSS.

[![Licenza](https://img.shields.io/badge/Licenza-GPL--2.0--or--later-blue)](LICENSE.md)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759B)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4)](https://www.php.net/)
[![Versione](https://img.shields.io/badge/Versione-1.1.2-green)](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)

---

## Versione Corrente: 1.1.2

**Novità v1.1.x:**
- ✅ API Mastodon nativa (sostituisce RSS)
- ✅ Statistiche interazioni: ❤️ like, 🔁 boost, 💬 risposte
- ✅ Filtro boost funzionante
- ✅ Icone SVG modulari in cartella separata
- ✅ Fix duplicazione contenuto
- ✅ Fix URL boost con `/activity`

Piattaforme supportate:
- **Mastodon** (API nativa + statistiche)
- **Diggita** (feed RSS Lemmy)

Prossimamente: **Bluesky** (Fase 2)

---

## Caratteristiche

- **API Native** - Mastodon API per dati completi, RSS per Diggita
- **Statistiche Live** - Conteggi like, boost e risposte in tempo reale (Mastodon)
- **Privacy-First** - Nessun tracker, cookies o servizi esterni
- **Fediverso-Native** - Supporto ActivityPub (Mastodon) e Lemmy (Diggita)
- **Timeline Unificata** - Tutti i tuoi post social in ordine cronologico
- **Filtro Boost** - Mostra/nascondi boost e repost (funzionante via API)
- **Icone Modulari** - File SVG separati facilmente sostituibili
- **Caching Intelligente** - Riduci richieste server (30min-24h configurabile)
- **Design Moderno** - Cards responsive, dark mode, animazioni smooth
- **Leggero** - Zero dipendenze JavaScript, solo CSS
- **Admin Completo** - Configurazione semplice da WordPress
- **Multilingua** - Supporto i18n (traduzioni IT/EN in arrivo)
- **Open Source** - GPL-2.0-or-later, codice verificabile

---

## Installazione

### Via WordPress (Caricamento ZIP)

1. Scarica l'ultima versione: [eg-social-timeline.zip](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/archive/main.zip)
2. Vai in WordPress → Plugin → Aggiungi nuovo → Carica plugin
3. Seleziona il file ZIP scaricato
4. Clicca "Installa ora" → "Attiva plugin"
5. Vai in Impostazioni → EG Social Timeline e configura almeno un profilo

### Via FTP

1. Scarica l'ultima versione: [eg-social-timeline.zip](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/archive/main.zip)
2. Estrai la cartella `eg-social-timeline`
3. Carica in `/wp-content/plugins/` via FTP
4. Attiva il plugin dal pannello WordPress
5. Vai in Impostazioni → EG Social Timeline e configura almeno un profilo

### Via Git Updater (Raccomandato)

Per aggiornamenti automatici:
1. Installa [Git Updater](https://git-updater.com/) (gratuito)
2. Installa EG Social Timeline normalmente
3. Git Updater controllerà automaticamente nuove versioni

---

## Configurazione

### Pannello Amministrazione

Posizione: **Impostazioni → EG Social Timeline**

Almeno un profilo social è obbligatorio.

#### Campi Disponibili

**1. URL Profilo Mastodon** (opzionale)
- URL completo del tuo profilo pubblico Mastodon
- Formato: `https://istanza.social/@username`
- Esempio: `https://mastodon.uno/@emanuelegori`
- Funziona con qualsiasi istanza Fediverso compatibile (Mastodon, Pleroma, Pixelfed, ecc.)
- **Nuovo v1.1**: Usa API nativa per statistiche complete

**2. Username Diggita** (opzionale)
- Il tuo username su diggita.com (senza @)
- Esempio: `emanuelegori`
- Diggita è basato su Lemmy (piattaforma Fediverso italiana)

**3. Numero Post da Mostrare** (default: 10)
- Quanti post mostrare nella timeline
- Range: 1-50

**4. Durata Cache** (default: 1 ora)
- Quanto tempo conservare i dati in cache
- Opzioni: 30min, 1h, 2h, 4h, 8h, 24h
- Cache più lunga = meno richieste = migliori performance

**5. Includi Boost/Repost** (default: no)
- Se abilitato, include anche boost Mastodon
- Se disabilitato, mostra solo post originali
- **Nuovo v1.1**: Ora funzionante grazie ad API Mastodon!

**6. Mostra Statistiche** (default: sì) **NUOVO v1.1**
- Visualizza conteggi like/boost/risposte sotto ogni post
- Mostra: ❤️ preferiti, 🔁 boost, 💬 risposte
- Solo per Mastodon (API), non disponibile per Diggita (RSS)

#### Svuota Cache Manualmente

Dal pannello admin puoi forzare l'aggiornamento immediato cliccando "Svuota Cache Ora".

La cache si svuota automaticamente anche salvando le impostazioni.

---

## Utilizzo

### Shortcode (Raccomandato)

Inserisci nel contenuto di articoli/pagine:

```
[eg_social_timeline]
```

Limita numero post (sovrascrive impostazione admin):

```
[eg_social_timeline limit="20"]
```

### Esempi Pratici

**Sidebar Widget (con plugin shortcode):**
```
[eg_social_timeline limit="5"]
```

**Pagina "Le Mie Attività":**
```
[eg_social_timeline limit="30"]
```

**Footer Tema (via PHP):**
```php
<?php echo do_shortcode('[eg_social_timeline limit="10"]'); ?>
```

---

## Come Funziona

### Architettura v1.1.x

#### Mastodon - API Nativa
1. **Account Lookup**: `GET /api/v1/accounts/lookup?acct=username`
   - Ottiene Account ID da username
   - Cache ID per 24h (riduce chiamate)

2. **Fetch Statuses**: `GET /api/v1/accounts/{id}/statuses`
   - Parametri: `exclude_reblogs`, `exclude_replies`, `limit`
   - Restituisce JSON con post completi e statistiche

3. **Dati Estratti**:
   - Contenuto post (testo HTML → plain text)
   - Data pubblicazione
   - URL post originale
   - Conteggi: favourites, reblogs, replies
   - Flag boost (is_boost)

#### Diggita - Feed RSS
1. **Fetch RSS**: `https://diggita.com/feeds/u/username.xml`
2. **Parse XML** con `simplexml_load_string()`
3. **Estrae**: data, titolo, contenuto, link

#### Merging & Output
1. Unisce post da tutte le piattaforme
2. Ordina per data (più recenti prima)
3. Applica filtri (boost, limit)
4. Caching in WordPress Transients
5. Render HTML con statistiche

### Icone SVG Modulari **NUOVO v1.1**

Le icone sono in file SVG separati nella cartella `social-icons/`:
- `mastodon.svg` - Logo ufficiale Mastodon
- `diggita.svg` - Icona personalizzata Diggita
- `lemmy.svg` - Icona Lemmy
- `bluesky.svg` - Logo Bluesky (Fase 2)
- `generic.svg` - Fallback

Vantaggi:
- Facile sostituire icone (modifica file SVG)
- Aggiungere piattaforme (aggiungi file + mapping)
- Codice PHP pulito (nessun SVG inline)

---

## Design Timeline v1.1

### Card Post

Ogni post è una card con:
- **Header**: Icona SVG piattaforma + nome + badge boost (se boost) + data relativa
- **Content**: Testo completo (max 300 caratteri)
- **Stats** (Mastodon): ❤️ like, 🔁 boost, 💬 risposte
- **Footer**: Link "Vedi post originale →"

### Statistiche Interazioni **NUOVO**

```
❤️ 16    🔁 10    💬  2
```

- Colori specifici per tipo (rosso/verde/blu)
- Tooltip hover con descrizione
- Dark mode support
- Disabilitabili da admin

### Colori Piattaforme

- Mastodon: Gradient viola `#6364FF → #563ACC`
- Diggita: Gradient rosso `#FF6B6B → #EE5A6F`
- Bluesky: Gradient blu `#0085FF → #00A8FF` (Fase 2)

### Badge Boost **NUOVO**

Post boostati mostrano badge discreto:
```
🔁 Boost
```
- Verde chiaro
- Bordo laterale verde sulla card
- Nessun avatar (privacy-first)

---

## Changelog

### v1.1.2 (2026-01-10) - Hotfix URL Boost
- **Fix**: URL boost non terminano più con `/activity`
- **Fix**: Link boost ora aprono pagina web invece di JSON
- Migliorato: Link puntano sempre a post originale

### v1.1.1 (2026-01-10) - Hotfix Leggibilità
- **Fix**: Rimossa duplicazione contenuto (titolo + testo)
- **Fix**: Migliorata leggibilità generale
- Migliorato: Badge boost ridimensionato (discreto)
- Migliorato: Spaziatura e contrasti
- Migliorato: Dark mode ottimizzato

### v1.1.0 (2026-01-10) - API Mastodon + Statistiche
- **Novità**: API Mastodon nativa (sostituisce RSS)
- **Novità**: Statistiche interazioni (like/boost/risposte)
- **Novità**: Filtro boost funzionante
- **Novità**: Badge visivo per boost
- **Novità**: Icone SVG modulari in cartella separata
- **Novità**: Opzione "Mostra Statistiche"
- Migliorato: Icone professionali (Mastodon, Diggita, Bluesky)
- Performance: Cache Account ID (24h)

### v1.0.0 (2026-01-09) - MVP Iniziale
- Prima release pubblica
- Supporto Mastodon via RSS
- Supporto Diggita via RSS
- Timeline unificata
- Sistema caching
- Admin panel
- Design responsive

---

## Roadmap Sviluppo

### ✅ Fase 1 - MVP (Completata v1.0.0)
- Supporto Mastodon RSS
- Supporto Diggita RSS
- Timeline unificata
- Caching system
- Admin panel
- Design responsive

### ✅ Fase 1.1 - API & Stats (Completata v1.1.2)
- API Mastodon nativa
- Statistiche interazioni
- Filtro boost funzionante
- Icone SVG modulari
- Fix UI e leggibilità

### 🔜 Fase 2 - Bluesky (Prossima v1.2.0)
- Integrazione API pubblica Bluesky
- Statistiche Bluesky
- Icona farfalla attiva
- Conversione JSON → formato unificato

### 📅 Fase 3 - Advanced Features (v1.3.0+)
- Blocco Gutenberg nativo
- Visualizzazione media/immagini
- Filtri avanzati (hashtag, tipo contenuto)
- Paginazione timeline
- Traduzioni complete (IT/EN)
- Widget nativo WordPress
- Supporto X/Twitter (se possibile)

---

## Requisiti

- **WordPress**: 5.0 o superiore
- **PHP**: 7.4 o superiore
- **Profili Social**: Almeno uno tra Mastodon o Diggita

### Dipendenze WordPress

Il plugin usa funzioni core WordPress:
- WordPress HTTP API (`wp_remote_get`)
- Transients API (caching)
- Settings API (admin)
- Shortcode API

Nessuna libreria esterna richiesta!

---

## Struttura Repository

```
eg-social-timeline/
├── eg-social-timeline.php     # File principale plugin
├── eg-social-timeline.css     # Stili timeline
├── social-icons/              # Icone SVG modulari (v1.1+)
│   ├── mastodon.svg
│   ├── diggita.svg
│   ├── lemmy.svg
│   ├── bluesky.svg
│   ├── generic.svg
│   └── README.md
├── languages/                 # Traduzioni (prossima versione)
├── README.md                  # Questa documentazione
├── readme.txt                 # Documentazione WordPress standard
├── CHANGELOG.md               # Storico versioni dettagliato
├── LICENSE.md                 # Licenza GPL v2 (inglese)
├── LICENSE.IT.md              # Licenza GPL v2 (italiano)
├── .gitignore                 # File da ignorare
└── .gitattributes             # Export pulito
```

---

## Troubleshooting

### La Timeline Non Appare

1. Verifica che il plugin sia attivo
2. Controlla di aver configurato almeno un profilo in Impostazioni
3. Verifica shortcode: `[eg_social_timeline]` (con underscore!)
4. Svuota cache plugin: Impostazioni → "Svuota Cache Ora"
5. Svuota cache sito e browser
6. Controlla log errori WordPress

### Statistiche Non Visibili (Mastodon)

1. Verifica opzione "Mostra Statistiche" sia abilitata
2. Svuota cache plugin
3. Solo Mastodon supporta statistiche (API)
4. Diggita usa RSS (nessuna statistica disponibile)

### Link Boost Mostrano JSON

**Risolto in v1.1.2!** Aggiorna alla versione più recente.

### Testo Post Duplicato

**Risolto in v1.1.1!** Aggiorna alla versione più recente.

### "Could not get Mastodon account ID"

1. Verifica URL profilo formato: `https://istanza.social/@username`
2. Controlla che il profilo sia pubblico
3. Verifica connessione server → istanza Mastodon
4. Svuota cache plugin

### "Nessun Post Disponibile"

Possibili cause:
1. **Nessun post pubblico**: Profili senza contenuti
2. **Errore connessione**: Server non raggiungibili
3. **URL errato**: Controlla formato URL Mastodon
4. **Cache vecchia**: Clicca "Svuota Cache Ora"

Debug:
1. Attiva `define('EG_SOCIAL_TIMELINE_DEBUG', true);` in `eg-social-timeline.php`
2. Controlla log errori WordPress: `/wp-content/debug.log`

---

## Aggiornamenti

### Da v1.0.0 a v1.1.x

**Breaking Changes**: Nessuno! Completamente retrocompatibile.

**Novità**:
- API Mastodon (automatico, nessuna config)
- Statistiche (disabilitabili da admin)
- Icone modulari (automatiche)

**Procedura**:
1. Backup database WordPress (precauzione)
2. Aggiorna plugin (ZIP, FTP o Git Updater)
3. Svuota cache in Impostazioni
4. Verifica timeline funzioni
5. Enjoy new features! 🎉

---

## Licenza

Questo plugin è rilasciato sotto licenza **GPL-2.0-or-later**.

Questo programma è software libero; puoi redistribuirlo e/o modificarlo secondo i termini della GNU General Public License come pubblicata dalla Free Software Foundation; versione 2 della Licenza, o (a tua scelta) qualsiasi versione successiva.

Testo completo licenza:
- Inglese (ufficiale): [LICENSE.md](LICENSE.md)
- Italiano (traduzione): [LICENSE.IT.md](LICENSE.IT.md)
- Online: https://www.gnu.org/licenses/gpl-2.0.html

---

## Supporto

### Documentazione

- [README.md](README.md) - Questa documentazione
- [CHANGELOG.md](CHANGELOG.md) - Storico versioni dettagliato
- Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline

### Contatti

- Website: https://emanuelegori.uno
- Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
- Blog: Informatica, libertà digitale e open source
- Fediverso: @emanuelegori@mastodon.uno
- Diggita: @emanuelegori

---

## Autore

**Emanuele Gori**

- Website: [emanuelegori.uno](https://emanuelegori.uno)
- Git: [git.emanuelegori.uno](https://git.emanuelegori.uno/emanuelegori)
- Blog: Privacy, FOSS, Fediverso, Self-hosting
- Fediverso: @emanuelegori@mastodon.uno

Altri plugin:
- [eg-sharebar-fedi](https://git.emanuelegori.uno/emanuelegori/eg-sharebar-fedi) - Barra condivisione Fediverso
- [eg-fediverso-box](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-box) - Box follow Fediverso
- [eg-fediverso-page](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-page) - Pagina educativa Fediverso
- [eg-contact-form-privacy](https://git.emanuelegori.uno/emanuelegori/eg-contact-form-privacy) - Form contatti privacy-first

---

## Statistiche

- **Versione**: 1.1.2 (Stable)
- **Data Release**: 10 Gennaio 2025
- **Licenza**: GPL-2.0-or-later
- **Compatibilità**: WordPress 5.0+ | PHP 7.4+
- **Piattaforme**: Mastodon (API), Diggita (RSS)
- **Roadmap**: Bluesky (v1.2.0), Features avanzate (v1.3.0+)

---

## Privacy & Etica

EG Social Timeline è costruito con attenzione alla privacy:

- API pubbliche e feed RSS (nessuna autenticazione)
- Nessun tracker o analytics
- Nessun dato inviato a servizi esterni
- Nessun cookie o storage browser
- Codice open source verificabile
- Dati cachati solo sul server WordPress
- Conforme GDPR (nessun dato personale raccolto)
- Statistiche anonime (conteggi, non utenti)

---

## Contributi

Il progetto è open source! Contributi benvenuti:

1. Fork del repository
2. Crea branch feature (`git checkout -b feature/amazing`)
3. Commit modifiche (`git commit -m 'Add amazing feature'`)
4. Push al branch (`git push origin feature/amazing`)
5. Apri Pull Request su Gitea

---

Creato con ❤️ da Emanuele Gori  
**Libertà digitale e Fediverso, sempre!**
