# EG Social Timeline

Plugin WordPress per visualizzare una timeline cronologica unificata delle tue attività social da Mastodon, Diggita e Bluesky. Privacy-first, nessun tracker, solo feed RSS pubblici.

[![Licenza](https://img.shields.io/badge/Licenza-GPL--2.0--or--later-blue)](LICENSE.md)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759B)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4)](https://www.php.net/)
[![Versione](https://img.shields.io/badge/Versione-1.0.0-green)](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)

---

## MVP - Fase 1

Versione corrente: **1.0.0 (MVP)**

Piattaforme supportate:
- Mastodon (e tutte le istanze Fediverso compatibili)
- Diggita (piattaforma Lemmy italiana)

Prossimamente: Bluesky (Fase 2)

---

## Caratteristiche

- Privacy-First - Usa solo feed RSS pubblici, nessun tracker o servizio esterno
- Fediverso-Native - Supporto Mastodon/ActivityPub e Diggita/Lemmy
- Timeline Unificata - Tutti i tuoi post social in ordine cronologico
- Caching Intelligente - Riduci le richieste ai server con cache configurabile (30min-24h)
- Design Moderno - Timeline responsive con card, icone colorate e animazioni smooth
- Dark Mode - Supporto automatico dark mode del browser
- Leggero - Zero dipendenze JavaScript, solo CSS e feed RSS
- Admin Panel Completo - Configurazione semplice da interfaccia WordPress
- Multilingua - Supporto i18n (presto traduzioni IT/EN)
- Open Source - Codice trasparente GPL-2.0-or-later

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

**2. Username Diggita** (opzionale)
- Il tuo username su diggita.com (senza @)
- Esempio: `emanuelegori`
- Diggita è basato su Lemmy (piattaforma Fediverso italiana)

**3. Numero Post da Mostrare** (default: 10)
- Quanti post mostrare nella timeline
- Range: 1-50

**4. Durata Cache** (default: 1 ora)
- Quanto tempo conservare i feed in cache
- Opzioni: 30min, 1h, 2h, 4h, 8h, 24h
- Cache più lunga = meno richieste ai server = migliori performance

**5. Mostra Boost/Repost** (default: no)
- Se abilitato, include anche boost Mastodon e repost
- Se disabilitato, mostra solo post originali

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

### Architettura

1. **Feed RSS Nativi**
   - Mastodon: `https://istanza.social/@username.rss` (standard ActivityPub)
   - Diggita: `https://diggita.com/feeds/u/username.xml` (standard Lemmy)

2. **Fetching**
   - Il plugin usa `wp_remote_get()` (WordPress HTTP API)
   - Parse XML con `simplexml_load_string()`
   - Estrae: data, titolo, contenuto, link

3. **Merging & Sorting**
   - Unisce i post di tutte le piattaforme
   - Ordina per data (più recenti prima)
   - Applica limit configurato

4. **Caching**
   - Salva in WordPress Transients API
   - Durata configurabile (30min-24h)
   - Riduce carico server e migliora performance

5. **Output**
   - HTML semantico con microformats
   - CSS modulare con variabili
   - Dark mode automatico
   - Responsive mobile-first

### Filtri Post

Se "Mostra Boost/Repost" è disabilitato, il plugin filtra:
- Post Mastodon che iniziano con "RT @" o "Boost:"
- Logica boost detection: analizza titolo RSS

---

## Design Timeline

### Card Post

Ogni post è una card con:
- **Header**: Icona piattaforma colorata + nome piattaforma + data relativa ("2 ore fa")
- **Content**: Titolo (se presente) + estratto testo (max 200 caratteri)
- **Footer**: Link "Vedi post originale →" con hover effect

### Colori Piattaforme

- Mastodon: Gradient viola `#6364FF → #563ACC`
- Diggita: Gradient rosso `#FF6B6B → #EE5A6F`
- Bluesky: Gradient blu `#0085FF → #00A8FF` (Fase 2)

### Responsive

- **Desktop**: Timeline centrata 800px, card con ombra e hover lift
- **Mobile**: Full width, stack verticale, touch-friendly
- **Dark Mode**: Supporto automatico `prefers-color-scheme`

### Accessibilità

- Semantic HTML5 (`<article>`, `<header>`, `<time>`)
- ARIA labels appropriati
- Contrasto colori WCAG AA
- Keyboard navigation friendly

---

## Roadmap Sviluppo

### Fase 1 - MVP (Completata)
- Supporto Mastodon RSS
- Supporto Diggita RSS
- Timeline unificata
- Caching system
- Admin panel
- Design responsive

### Fase 2 - Bluesky (Prossima)
- Integrazione API pubblica Bluesky
- Conversione JSON → formato unificato
- Gestione autenticazione (se necessaria)

### Fase 3 - Advanced Features
- Blocco Gutenberg nativo
- Filtri avanzati (hashtag, tipo contenuto)
- Paginazione timeline
- Traduzioni complete (IT/EN)
- Widget nativo WordPress
- Supporto X/Twitter (se possibile con scraping)

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

## Compatibilità Piattaforme

### Mastodon (Testato)
- Mastodon ufficiale
- Istanze Fediverso compatibili:
  - Pleroma (compatibilità teorica)
  - Pixelfed (compatibilità teorica)
  - Misskey (se supporta RSS)
  - Akkoma (compatibilità teorica)
  - GoToSocial (compatibilità teorica)

### Diggita (Testato)
- diggita.com (istanza Lemmy)
- Altre istanze Lemmy italiane (feddit.it, poliversity.it)

### Bluesky (Fase 2)
- API pubblica AT Protocol
- Feed pubblici senza autenticazione

---

## Troubleshooting

### La Timeline Non Appare

1. Verifica che il plugin sia attivo
2. Controlla di aver configurato almeno un profilo in Impostazioni
3. Verifica shortcode: `[eg_social_timeline]` (con underscore!)
4. Svuota cache sito e browser
5. Controlla log errori WordPress

### Banner "Configurazione Richiesta"

Se vedi il banner rosso in admin:
1. Vai in Impostazioni → EG Social Timeline
2. Inserisci almeno un URL Mastodon o username Diggita
3. Salva le impostazioni
4. Il banner scomparirà

### "Nessun Post Disponibile"

Possibili cause:
1. **Feed RSS vuoto**: I profili non hanno post pubblici
2. **Errore connessione**: Verifica che i server siano raggiungibili
3. **URL errato**: Controlla formato URL Mastodon
4. **Cache vecchia**: Clicca "Svuota Cache Ora" in admin

Debug:
1. Attiva `define('EG_SOCIAL_TIMELINE_DEBUG', true);` in `eg-social-timeline.php`
2. Controlla log errori WordPress: `/wp-content/debug.log`

### Post Duplicati o Mancanti

1. Svuota cache plugin (bottone admin)
2. Verifica filtro "Mostra Boost/Repost"
3. Controlla se i feed RSS hanno dati corretti (apri URL RSS in browser)

### Performance Lente

1. Aumenta durata cache (8h o 24h)
2. Riduci numero post mostrati
3. Disabilita "Mostra Boost/Repost" se non necessario

---

## Personalizzazione CSS

### Cambiare Colori Piattaforme

Modifica `eg-social-timeline.css`:

```css
/* Mastodon - Cambia gradient */
.platform-icon.platform-mastodon {
    background: linear-gradient(135deg, #TUO_COLORE1 0%, #TUO_COLORE2 100%);
}

/* Link hover color */
.view-original:hover {
    background: #TUO_COLORE_PRIMARY;
}
```

### Cambiare Font

Aggiungi nel tuo tema:

```css
.eg-social-timeline {
    font-family: 'TUO_FONT', sans-serif;
}
```

### Card più Grandi/Piccole

```css
.timeline-item {
    padding: 30px; /* aumenta */
    font-size: 17px; /* aumenta testo */
}
```

---

## Aggiornamenti

### Manuale

1. Scarica nuova versione ZIP
2. Disattiva plugin in WordPress
3. Elimina vecchia cartella plugin
4. Carica nuova versione
5. Riattiva plugin

Le configurazioni nel database NON vengono perse.

### Automatico (Raccomandato)

Usa [Git Updater](https://git-updater.com/):
- Controllo automatico nuove release
- Notifiche in WordPress → Aggiornamenti
- Click "Aggiorna" per installare
- Configurazioni mantenute

---

## Struttura Repository

```
eg-social-timeline/
├── eg-social-timeline.php     # File principale plugin
├── eg-social-timeline.css     # Stili timeline
├── languages/                 # Traduzioni (prossima versione)
├── README.md                  # Questa documentazione
├── readme.txt                 # Documentazione WordPress standard
├── CHANGELOG.md               # Storico versioni
├── LICENSE.md                 # Licenza GPL v2 (inglese)
├── LICENSE.IT.md              # Licenza GPL v2 (italiano)
├── .gitignore                 # File da ignorare
└── .gitattributes             # Export pulito
```

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
- [CHANGELOG.md](CHANGELOG.md) - Storico versioni
- Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline

### Contatti

- Website: https://emanuelegori.uno
- Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
- Blog: Informatica, libertà digitale e open source
- Fediverso: @emanuelegori@emanuelegori.uno
- Diggita: @emanuelegori (presto)

---

## Autore

**Emanuele Gori**

- Website: [emanuelegori.uno](https://emanuelegori.uno)
- Git: [git.emanuelegori.uno](https://git.emanuelegori.uno/emanuelegori)
- Blog: Privacy, FOSS, Fediverso, Self-hosting
- Fediverso: @emanuelegori@emanuelegori.uno

Altri plugin:
- [eg-sharebar-fedi](https://git.emanuelegori.uno/emanuelegori/eg-sharebar-fedi) - Barra condivisione Fediverso
- [eg-fediverso-box](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-box) - Box follow Fediverso
- [eg-fediverso-page](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-page) - Pagina educativa Fediverso

---

## Statistiche

- **Versione**: 1.0.0 (MVP - Fase 1)
- **Data Release**: Gennaio 2025
- **Licenza**: GPL-2.0-or-later
- **Compatibilità**: WordPress 5.0+ | PHP 7.4+
- **Piattaforme**: Mastodon (testato), Diggita (testato)
- **Roadmap**: Bluesky (Fase 2), Features avanzate (Fase 3)

---

## Privacy & Etica

EG Social Timeline è costruito con attenzione alla privacy:

- Usa solo feed RSS pubblici
- Nessun tracker o analytics
- Nessun dato inviato a servizi esterni
- Nessun cookie o storage browser
- Codice open source verificabile
- Dati cachati solo sul server WordPress
- Conforme GDPR (nessun dato personale raccolto)

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
