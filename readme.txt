=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, bluesky, fediverse, forgejo, social, timeline, activitypub, privacy
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.4.6
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Timeline cronologica unificata delle tue attività pubbliche da Mastodon, Bluesky, Forgejo e Diggita. Zero JavaScript, zero tracking.

== Description ==

EG Social Timeline aggrega in ordine cronologico i tuoi post pubblici da quattro piattaforme decentralizzate e li mostra in un'unica timeline tramite shortcode.

= Piattaforme supportate =

- Mastodon (e teoricamente qualsiasi istanza compatibile ActivityPub, non testata)
- Bluesky (API pubblica, nessuna autenticazione richiesta)
- Forgejo e Gitea (commit dai tuoi repository pubblici)
- Diggita (piattaforma Lemmy italiana)

= Caratteristiche principali =

- Filtri per piattaforma interattivi, senza JavaScript
- Anteprime immagini per i post Mastodon (opzionale)
- Limiti configurabili per piattaforma, per un mix equilibrato
- Statistiche interazioni: like, boost, commenti
- Cache configurabile (30 minuti - 24 ore)
- Design responsive con supporto dark mode automatico
- Shortcode con parametro limit opzionale
- Icone SVG modulari e personalizzabili
- Privacy-friendly: solo dati pubblici, nessun tracker

= Utilizzo =

Configura i profili nelle impostazioni, poi inserisci lo shortcode:

`[eg_social_timeline]`

Con limite personalizzato:

`[eg_social_timeline limit="20"]`

= Privacy =

- Recupera solo post pubblici dalle piattaforme configurate
- Nessun tracking degli utenti
- Nessun dato inviato a terze parti
- Cache locale su database WordPress
- Nessun cookie impostato dal plugin

== Installation ==

1. Carica i file nella directory `/wp-content/plugins/eg-social-timeline/`
2. Attiva il plugin dal menu Plugin di WordPress
3. Vai su Impostazioni → EG Social Timeline
4. Configura almeno un profilo social
5. Inserisci `[eg_social_timeline]` nella pagina o articolo desiderato

= Aggiornamenti automatici =

Installa [EG Forgejo Updater](https://git.emanuelegori.uno/emanuelegori/eg-forgejo-updater) per ricevere gli aggiornamenti automatici direttamente da WordPress, esattamente come i plugin del repository ufficiale.

= Configurazione minima =

- URL profilo Mastodon (es: https://mastodon.uno/@username)
- OPPURE Handle Bluesky (es: emanuele.bsky.social)
- OPPURE Username Forgejo + URL istanza
- OPPURE Username Diggita

= Configurazione avanzata =

- Limiti post per piattaforma (evita monopolizzazione)
- Anteprime immagini per post con allegati (Mastodon)
- Lunghezza testo per ogni post (50-600 caratteri, 0 = completo)
- Includi o escludi boost e repost
- Mostra o nascondi statistiche interazioni
- Durata cache da 30 minuti a 24 ore

== Frequently Asked Questions ==

= Quali piattaforme sono supportate? =

Mastodon (e istanze compatibili ActivityPub), Bluesky, Forgejo/Gitea e Diggita.

= I filtri piattaforma richiedono JavaScript? =

No. Usano esclusivamente CSS con il pattern checkbox/label, funzionano anche con JavaScript disabilitato nel browser.

= Posso usare il plugin con una sola piattaforma? =

Sì. Basta configurare almeno un profilo. Le piattaforme non configurate vengono semplicemente ignorate.

= Come funzionano i limiti per piattaforma? =

Ogni piattaforma ha un limite configurabile di post da includere nella timeline. Questo evita che una piattaforma molto attiva (es: Forgejo con molti commit) occupi tutti gli slot disponibili, garantendo un mix equilibrato.

= I dati sono privati? =

Il plugin recupera solo contenuti pubblici. Non traccia gli utenti del sito né invia dati a servizi terzi.

= Come funziona la cache? =

I post vengono salvati temporaneamente per ridurre le chiamate alle API esterne. Puoi configurare la durata da 30 minuti a 24 ore. La cache viene svuotata automaticamente al salvataggio delle impostazioni.

= Posso personalizzare lo stile? =

Sì. Le icone sono file SVG sostituibili nella cartella `social-icons/`. Puoi aggiungere CSS personalizzato dal tuo tema per modificare colori e layout.

== Screenshots ==

1. Timeline frontend unificata con post da Mastodon, Bluesky, Forgejo e Diggita
2. Pannello impostazioni admin — configurazione profili social
3. Pannello impostazioni admin — limiti post per piattaforma

== Changelog ==

= 1.4.6 - 2026-05-25 =
* Changed: readme.txt riscritto — struttura più chiara, rimossi riferimenti a versioni obsolete
* Changed: Tested up to aggiornato a WordPress 7.0
* Changed: EG Forgejo Updater al posto di Git Updater nelle istruzioni di installazione

= 1.4.5 - 2026-05-25 =
* Security: validazione HTTPS su URL istanza Forgejo in sanitizzazione
* Added: screenshot frontend, configurazione e limiti post

= 1.4.4 - 2026-05-24 =
* Fixed: Forgejo: ordinamento repo per data ultimo push (sort lato client)

= 1.4.3 - 2026-05-24 =
* Added: anteprime immagini Mastodon (opzione admin, default disabilitato)
* Fixed: Forgejo: i commit provengono ora dai repo più recentemente aggiornati

= 1.4.2 - 2026-05-24 =
* Added: traduzioni italiano e inglese (file .pot, .po, .mo)
* Fixed: link "Vedi post originale" sempre allineato a destra
* Changed: cache ID account Mastodon estesa a 30 giorni

= 1.4.1 - 2026-05-02 =
* Added: lunghezza testo post configurabile da admin (50-600, 0 = completo)

= 1.4.0 - 2026-05-02 =
* Added: integrazione Bluesky via API pubblica ATP (nessuna autenticazione)

= 1.3.1 - 2026-04-06 =
* Security: protezione XXE, sanitizzazione SVG, validazione anti-SSRF

= 1.3.0 - 2026-01-11 =
* Added: limiti configurabili per piattaforma

= 1.2.0 - 2026-01-11 =
* Added: integrazione Forgejo/Gitea

= 1.1.0 - 2026-01-10 =
* Added: migrazione Mastodon a API v1, statistiche complete

= 1.0.0 - 2026-01-10 =
* Release iniziale: Mastodon e Diggita

== Upgrade Notice ==

= 1.4.6 =
Documentazione aggiornata. Nessuna modifica al codice.

= 1.4.5 =
Aggiornamento consigliato: fix sicurezza su URL istanza Forgejo.

= 1.4.4 =
Fix Forgejo: i commit mostrati ora provengono dai repo più recentemente aggiornati.

= 1.4.3 =
Anteprime immagini Mastodon disponibili (opzione admin). Fix importante per l'ordinamento commit Forgejo.

= 1.4.2 =
Traduzioni italiano/inglese. Fix allineamento link footer.

= 1.4.1 =
Lunghezza testo post configurabile da admin.

= 1.4.0 =
Nuova integrazione Bluesky. Inserisci il tuo handle nelle impostazioni.

= 1.3.1 =
Fix sicurezza importanti. Aggiornamento consigliato.

= 1.3.0 =
Limiti configurabili per piattaforma. Retrocompatibile con v1.2.x.

== Privacy Policy ==

EG Social Timeline recupera solo contenuti pubblici dalle piattaforme configurate. Non traccia gli utenti del sito, non invia dati a servizi terzi, non imposta cookie. La cache è locale nel database WordPress.

== Support ==

* Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Issues: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
