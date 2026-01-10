=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, fediverse, social, timeline, diggita, lemmy, rss, feed
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mostra una timeline cronologica unificata delle tue attività social da Mastodon, Diggita e Bluesky. Privacy-first, solo feed RSS pubblici.

== Description ==

**EG Social Timeline** è un plugin WordPress che mostra una timeline cronologica unificata delle tue attività sui social network decentralizzati.

= Caratteristiche Principali =

* **Privacy-First** - Usa solo feed RSS pubblici, nessun tracker
* **Fediverso-Native** - Supporto completo Mastodon e Diggita (Lemmy)
* **Timeline Unificata** - Tutti i tuoi post in un unico posto
* **Design Moderno** - Timeline responsive con card e animazioni
* **Caching Intelligente** - Riduce carico server (30min-24h configurabile)
* **Zero Dipendenze** - Nessun JavaScript, solo CSS puro
* **Dark Mode** - Supporto automatico tema scuro browser
* **Open Source** - Codice trasparente GPL-2.0-or-later

= Piattaforme Supportate =

**MVP - Fase 1 (v1.0.0):**
* Mastodon (e tutte le istanze Fediverso compatibili: Pleroma, Pixelfed, ecc.)
* Diggita (piattaforma Lemmy italiana)

**Fase 2 (Prossimamente):**
* Bluesky (AT Protocol)

= Come Funziona =

1. Configura almeno un profilo social (Mastodon o Diggita)
2. Il plugin scarica automaticamente i feed RSS pubblici
3. I post vengono uniti e ordinati cronologicamente
4. La timeline viene cachata per migliori performance
5. Inserisci lo shortcode `[eg_social_timeline]` dove vuoi mostrare la timeline

Semplice, veloce, rispettoso della privacy!

= Utilizzo =

Inserisci lo shortcode nel contenuto di articoli o pagine:

`[eg_social_timeline]`

Limita il numero di post mostrati:

`[eg_social_timeline limit="20"]`

= Configurazione =

Vai in **Impostazioni → EG Social Timeline** e configura:

* URL profilo Mastodon (es: https://mastodon.uno/@username)
* Username Diggita (es: emanuelegori)
* Numero post da mostrare (1-50)
* Durata cache (30min, 1h, 2h, 4h, 8h, 24h)
* Mostra/nascondi boost e repost

Almeno un profilo social è obbligatorio.

== Installation ==

= Installazione Automatica =

1. Vai in WordPress → Plugin → Aggiungi nuovo
2. Cerca "EG Social Timeline"
3. Clicca "Installa ora" e poi "Attiva"
4. Vai in Impostazioni → EG Social Timeline per configurare

= Installazione Manuale =

1. Scarica il file ZIP del plugin
2. Vai in WordPress → Plugin → Aggiungi nuovo → Carica plugin
3. Seleziona il file ZIP e clicca "Installa ora"
4. Attiva il plugin
5. Vai in Impostazioni → EG Social Timeline per configurare

= Via FTP =

1. Scarica e estrai il file ZIP
2. Carica la cartella `eg-social-timeline` in `/wp-content/plugins/`
3. Attiva il plugin dal pannello Plugin di WordPress
4. Vai in Impostazioni → EG Social Timeline per configurare

= Configurazione Iniziale =

Dopo l'installazione:

1. Vai in Impostazioni → EG Social Timeline
2. Inserisci almeno un profilo social (Mastodon o Diggita)
3. Salva le impostazioni
4. Inserisci `[eg_social_timeline]` dove vuoi la timeline

== Frequently Asked Questions ==

= Quali piattaforme sono supportate? =

Versione corrente (v1.0.0 - MVP):
* Mastodon e tutte le istanze Fediverso compatibili (Pleroma, Pixelfed, Misskey, ecc.)
* Diggita (e altre istanze Lemmy)

Prossimamente: Bluesky (Fase 2)

= Il plugin raccoglie dati personali? =

No! Il plugin:
* Usa solo feed RSS pubblici
* Non installa tracker o analytics
* Non invia dati a servizi esterni
* Non usa cookie
* È completamente GDPR-compliant

Tutti i dati sono cachati localmente sul tuo server WordPress.

= Come funziona il caching? =

Il plugin salva i feed RSS nella cache di WordPress (Transients API) per ridurre le richieste ai server esterni. Puoi configurare la durata da 30 minuti a 24 ore.

Per svuotare la cache manualmente: Impostazioni → EG Social Timeline → "Svuota Cache Ora"

= Posso nascondere i boost e repost? =

Sì! In Impostazioni → EG Social Timeline trovi l'opzione "Mostra Boost/Repost". Se disabilitata, la timeline mostrerà solo i tuoi post originali.

= Come personalizzo il design? =

Il plugin usa CSS standard. Puoi sovrascrivere gli stili nel CSS del tuo tema targeting le classi `.eg-social-timeline`.

Esempio nel file CSS del tema:

`.timeline-item {
    border-color: #tuocolore;
}`

= Il plugin è compatibile con il mio tema? =

Sì! Il plugin usa HTML semantico e CSS standard, quindi è compatibile con qualsiasi tema WordPress moderno.

= Supporta Gutenberg? =

Al momento (v1.0.0) usa solo shortcode. Il blocco Gutenberg nativo è pianificato per la Fase 3.

Puoi comunque usare il blocco "Shortcode" di Gutenberg e inserire `[eg_social_timeline]`.

= Come aggiorno il plugin? =

**Metodo 1 - Manuale:**
Scarica la nuova versione e sostituisci i file (le configurazioni sono salvate nel database).

**Metodo 2 - Automatico (Raccomandato):**
Installa [Git Updater](https://git-updater.com/) che controlla automaticamente nuove versioni dal repository Gitea.

= Dove trovo supporto? =

* Documentazione completa: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Blog: https://emanuelegori.uno
* Fediverso: @emanuelegori@emanuelegori.uno

== Screenshots ==

1. Timeline unificata con post da Mastodon e Diggita
2. Pannello amministrazione con tutte le opzioni
3. Design responsive su mobile
4. Card post con icone colorate
5. Dark mode automatico

== Changelog ==

= 1.0.0 - 2025-01-10 =
* Prima release pubblica (MVP - Fase 1)
* Supporto Mastodon RSS feed
* Supporto Diggita/Lemmy RSS feed
* Timeline unificata cronologica
* Caching intelligente configurabile
* Shortcode `[eg_social_timeline]`
* Admin panel completo
* Design responsive con dark mode
* Zero dipendenze JavaScript

Vedi [CHANGELOG.md](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/src/branch/main/CHANGELOG.md) per dettagli completi.

== Upgrade Notice ==

= 1.0.0 =
Prima release pubblica. Nessuna migrazione necessaria.

== Roadmap ==

**Fase 2 - Bluesky:**
* Integrazione API pubblica Bluesky
* Supporto AT Protocol

**Fase 3 - Advanced:**
* Blocco Gutenberg nativo
* Paginazione timeline
* Filtri avanzati
* Widget WordPress nativo
* Traduzioni complete (IT/EN)

== Privacy Policy ==

EG Social Timeline non raccoglie, trasmette o memorizza alcun dato personale. Il plugin:

* Usa solo feed RSS pubblici
* Non installa cookie
* Non usa analytics o tracker
* Non invia dati a server esterni
* Cache i feed localmente sul server WordPress
* È completamente GDPR-compliant

== Credits ==

Sviluppato da [Emanuele Gori](https://emanuelegori.uno)

Altri plugin dell'autore:
* [EG Sharebar Fedi](https://git.emanuelegori.uno/emanuelegori/eg-sharebar-fedi) - Barra condivisione Fediverso
* [EG Fediverso Box](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-box) - Box follow Fediverso
* [EG Fediverso Page](https://git.emanuelegori.uno/emanuelegori/eg-fediverso-page) - Pagina educativa Fediverso

== License ==

Questo plugin è rilasciato sotto licenza GPL-2.0-or-later.

GNU General Public License v2.0 or later
https://www.gnu.org/licenses/gpl-2.0.html

Questo programma è software libero; puoi redistribuirlo e/o modificarlo secondo i termini della GNU General Public License come pubblicata dalla Free Software Foundation; versione 2 della Licenza, o (a tua scelta) qualsiasi versione successiva.
