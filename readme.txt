=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, fediverse, social, timeline, lemmy, forgejo, activitypub
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.4.3
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mostra una timeline cronologica unificata delle tue attività social da Mastodon, Diggita (Lemmy), Forgejo/Gitea e Bluesky.

== Description ==

EG Social Timeline è un plugin WordPress che aggrega e mostra in ordine cronologico i tuoi post pubblici da multiple piattaforme social decentralizzate.

= Caratteristiche Principali =

* Timeline unificata da multiple piattaforme
* Supporto Mastodon (e compatibili ActivityPub)
* Supporto Diggita (Lemmy) con statistiche complete
* Supporto Forgejo/Gitea per commit repository
* **NUOVO v1.4.0**: Integrazione Bluesky via API pubblica ATP
* Limiti configurabili per piattaforma
* Filtri CSS interattivi per mostrare/nascondere piattaforme
* Sistema icone SVG modulare e personalizzabile
* Cache intelligente configurabile
* Statistiche interazioni (like, boost, commenti)
* Design responsive con supporto dark mode
* Privacy-friendly: zero tracking, solo dati pubblici

= Piattaforme Supportate =

* **Mastodon**: Qualsiasi istanza compatibile ActivityPub
* **Diggita**: Piattaforma Lemmy italiana
* **Forgejo/Gitea**: Repository Git self-hosted
* **Bluesky**: API pubblica ATP, nessuna autenticazione richiesta

= NUOVO nella v1.4.0 =

Integrazione completa con **Bluesky** tramite API pubblica ATP:

* Inserisci il tuo handle (es. `emanuele.bsky.social`) nelle impostazioni
* Nessuna autenticazione o token richiesti
* Statistiche complete: like, repost, risposte
* Filtro piattaforma CSS-only nella timeline
* Supporto repost configurabile

= v1.3.0 =

Configura limiti massimi per ciascuna piattaforma nelle impostazioni:

* Max post Mastodon (default: 20, 0 = illimitato)
* Max post Diggita (default: 10, 0 = illimitato)
* Max commit Forgejo (default: 5, 0 = illimitato)
* Max post Bluesky (default: 10, 0 = illimitato)

Questo previene che una piattaforma molto attiva (es: Forgejo con molti commit) monopolizzi tutti gli slot disponibili nella timeline, garantendo un mix equilibrato di contenuti.

= Privacy =

Il plugin rispetta la tua privacy:

* Recupera solo post pubblici
* Nessun tracking degli utenti
* Nessun dato inviato a terze parti
* Cache locale per ridurre richieste esterne

= Utilizzo =

Dopo aver configurato i tuoi profili social nelle impostazioni, inserisci lo shortcode in qualsiasi pagina o articolo:

`[eg_social_timeline]`

Con limite personalizzato:

`[eg_social_timeline limit="50"]`

== Installation ==

1. Carica i file del plugin nella directory `/wp-content/plugins/eg-social-timeline/`
2. Attiva il plugin tramite il menu 'Plugin' in WordPress
3. Vai su Impostazioni → EG Social Timeline
4. Configura almeno un profilo social
5. Configura opzionalmente i limiti per piattaforma
6. Inserisci lo shortcode `[eg_social_timeline]` dove desideri mostrare la timeline

= Configurazione Minima =

* URL profilo Mastodon (es: https://mastodon.uno/@username)
* OPPURE Username Diggita
* OPPURE Username Forgejo + URL istanza
* OPPURE Handle Bluesky (es: emanuele.bsky.social)

= Configurazione Avanzata =

* Limiti post per piattaforma (previene monopolizzazione)
* Durata cache (da 30 minuti a 24 ore)
* Includi/escludi boost e repost
* Mostra/nascondi statistiche interazioni

== Frequently Asked Questions ==

= Quali piattaforme sono supportate? =

Mastodon (e compatibili ActivityPub), Diggita (Lemmy), Forgejo/Gitea, Bluesky.

= Come funzionano i limiti per piattaforma (v1.3.0)? =

I limiti configurabili prevengono che una piattaforma molto attiva (es: Forgejo con 50+ commit) occupi tutti gli slot disponibili. Esempio:
- Limite Forgejo: 5 commit
- Limite Mastodon: 20 post
- Limite Diggita: 10 post
- Totale disponibili: 35 post (poi tagliati al limite totale, es: 30)

Questo garantisce un mix equilibrato nella timeline.

= Posso usare il plugin senza account Mastodon? =

Sì! Puoi configurare solo Diggita o solo Forgejo se preferisci.

= I dati sono privati? =

Il plugin recupera solo post pubblici dalle piattaforme configurate. Non traccia utenti né invia dati a terze parti.

= Come funziona la cache? =

Il plugin salva temporaneamente i post recuperati per ridurre richieste alle API. Puoi configurare la durata da 30 minuti a 24 ore.

= Posso personalizzare lo stile? =

Sì! Le icone sono file SVG sostituibili e puoi aggiungere CSS personalizzato per modificare colori e layout.

= I filtri piattaforme richiedono JavaScript? =

No! Usano solo CSS con checkbox, funzionano anche con JavaScript disabilitato.

== Screenshots ==

1. Timeline unificata con post da multiple piattaforme
2. Filtri interattivi per mostrare/nascondere piattaforme
3. Pannello impostazioni admin con limiti configurabili (v1.3.0)
4. Vista mobile responsive
5. Supporto dark mode automatico

== Changelog ==

= 1.4.3 - 2026-05-24 =
* Added: Supporto anteprime immagini (opzione admin, default disabilitato)
* Added: Mastodon: estrazione prima immagine allegata con preview_url e testo alt
* Fixed: Forgejo: repo ordinati per ultimo push (sort=recentupdate), fix esclusione repo recenti
* Changed: Struttura post unificata con campi image_url e image_alt su tutte le piattaforme

= 1.4.2 - 2026-05-24 =
* Added: Traduzioni italiano e inglese (file .pot, .po, .mo)
* Fixed: Link "Vedi post originale" / "Vedi commit" sempre a destra anche senza statistiche
* Changed: Cache ID account Mastodon estesa a 30 giorni

= 1.4.1 - 2026-05-02 =
* Added: Lunghezza testo post configurabile da admin (default: 300, range: 50-600, 0 = testo completo)

= 1.4.0 - 2026-05-02 =
* Added: Integrazione Bluesky via API pubblica ATP (nessuna autenticazione richiesta)
* Added: Nuovo campo admin: Handle Bluesky (es. emanuele.bsky.social)
* Added: Nuovo campo admin: Max Post Bluesky (default: 10, range: 0-100)
* Added: Filtro piattaforma Bluesky nella timeline
* Added: Statistiche Bluesky: like, repost, risposte
* Changed: Validazione "almeno un profilo" estesa a Bluesky

= 1.3.1 - 2026-04-06 =
* Security: Aggiunto LIBXML_NONET al parsing XML Diggita (anti-XXE)
* Security: Sanitizzazione SVG inline con wp_kses() (anti-XSS)
* Security: Validazione anti-SSRF sulle URL API esterne (rifiuta IP privati/riservati)
* Fixed: Aggiunto esc_url() mancante su link admin nello shortcode

= 1.3.0 - 2026-01-11 =
* Added: Limiti configurabili per piattaforma nelle impostazioni admin
* Added: Nuovi campi: Max post Mastodon, Max post Diggita, Max commit Forgejo
* Added: Valore 0 = nessun limite (comportamento v1.2.x)
* Added: Timeline più equilibrata previene monopolizzazione da singola piattaforma
* Changed: Logica fetch modificata per rispettare limiti per piattaforma
* Changed: Forgejo limite TOTALE commit invece di per-repo
* Changed: Default sensati: Mastodon 20, Diggita 10, Forgejo 5
* Changed: Limite totale timeline aumentato: 1-100 (era 1-50)

= 1.2.5 - 2026-01-11 =
* Fixed: Diggita statistiche `<br>` → `\n` parsing corretto
* Fixed: Forgejo nome repository visibile in timeline
* Fixed: Pulsanti filtri larghezza automatica (altezza uniforme)
* Fixed: Icone filtri dimensioni uniformi senza distorsione

= 1.2.4 - 2026-01-11 =
* Fixed: Diggita parsing HTML robusto
* Fixed: Forgejo nome repo incluso nel content

= 1.2.3 - 2026-01-11 =
* Fixed: Filtri CSS logica invertita
* Fixed: Forgejo link pagina commits
* Fixed: UI box filtri compatto

= 1.2.2 - 2026-01-11 =
* Fixed: Forgejo commit link URL encoding

= 1.2.1 - 2026-01-11 =
* Fixed: CSS filtri checkbox posizionamento

= 1.2.0 - 2026-01-11 =
* Added: Integrazione Forgejo/Gitea commit repository
* Added: Link diretti a pagina commits Forgejo

= 1.1.2 - 2026-01-10 =
* Fixed: Diggita statistiche parsing robusto

= 1.1.1 - 2026-01-10 =
* Fixed: Icon system SVG loading

= 1.1.0 - 2026-01-10 =
* Added: Migrazione Mastodon da RSS a API v1
* Added: Statistiche complete (like, boost, commenti)
* Added: Sistema icone modulare SVG
* Deprecated: RSS Mastodon sostituito da API

= 1.0.0 - 2026-01-10 =
* Release iniziale MVP
* Added: Supporto Mastodon e Diggita
* Added: Sistema cache configurabile
* Added: Admin settings panel
* Added: Shortcode base
* Added: Design responsive e dark mode

== Upgrade Notice ==

= 1.4.3 =
Anteprime immagini Mastodon (opzione admin). Fix importante Forgejo: i commit mostrati ora provengono dai repo più recentemente aggiornati.

= 1.4.2 =
Aggiunte traduzioni italiano/inglese. Fix allineamento link footer. Cache ID Mastodon estesa a 30 giorni.

= 1.4.1 =
Lunghezza testo post ora configurabile da admin (50-600 caratteri, 0 = testo completo).

= 1.4.0 =
Nuova integrazione Bluesky via API pubblica ATP. Inserisci il tuo handle nelle impostazioni per aggiungere i post Bluesky alla timeline. Nessuna autenticazione richiesta.

= 1.3.1 =
Fix sicurezza: protezione XXE su parsing RSS, sanitizzazione SVG inline, validazione anti-SSRF su URL API. Aggiornamento consigliato.

= 1.3.0 =
Nuova funzionalità: limiti configurabili per piattaforma. Previene che Forgejo o altre piattaforme attive monopolizzino la timeline. Default applicati automaticamente (20/10/5), retrocompatibile con v1.2.x.

= 1.2.5 =
Fix importanti per statistiche Diggita e UI filtri. Aggiornamento consigliato.

= 1.2.0 =
Nuova integrazione Forgejo/Gitea. Mostra commit repository nella timeline.

= 1.1.0 =
Migrazione Mastodon a API v1. Statistiche complete ora disponibili.

== Privacy Policy ==

EG Social Timeline:
* Recupera solo post pubblici dalle piattaforme configurate
* Non traccia utenti del sito
* Non invia dati a servizi terzi
* Cache locale su database WordPress
* Nessun cookie impostato dal plugin

== Support ==

* Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Issues: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
* Documentazione: https://emanuelegori.uno
