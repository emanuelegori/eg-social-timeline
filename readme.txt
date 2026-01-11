=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, fediverse, social media, timeline, aggregator, forgejo, gitea, lemmy
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.2.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mostra una timeline unificata delle tue attività social da Mastodon, Diggita (Lemmy), Forgejo/Gitea e Bluesky.

== Description ==

EG Social Timeline è un plugin WordPress che aggrega e mostra in ordine cronologico i tuoi post pubblici da diverse piattaforme social decentralizzate.

= Piattaforme Supportate =

* **Mastodon** e compatibili ActivityPub (con statistiche complete)
* **Diggita** (Lemmy) - piattaforma social italiana
* **Forgejo/Gitea** - attività repository (commit e nuovi repo)
* **Bluesky** (in sviluppo)

= Caratteristiche Principali =

* Timeline cronologica unificata
* Filtri interattivi per piattaforma (CSS puro, no JavaScript)
* Sistema icone modulare con file SVG
* Cache intelligente configurabile
* Statistiche interazioni (like, boost, commenti)
* Design responsive e dark mode
* Privacy-friendly (solo dati pubblici)
* Shortcode semplice: `[eg_social_timeline]`

= Filtri Timeline =

Sistema filtri CSS integrato permette di mostrare/nascondere piattaforme con click su checkbox. Le preferenze NON persistono tra sessioni (design by choice per privacy).

= Icone Personalizzabili =

Le icone delle piattaforme sono file SVG in `social-icons/` facilmente sostituibili. Basta mantenere formato 24x24px con `viewBox="0 0 24 24"`.

= Privacy =

Il plugin richiede SOLO dati pubblici dalle API delle piattaforme. Nessun dato utente viene tracciato o inviato a terze parti.

= Compatibilità =

* WordPress 5.0+
* PHP 7.4+
* Git Updater per aggiornamenti automatici

== Installation ==

1. Carica la cartella `eg-social-timeline` in `/wp-content/plugins/`
2. Attiva il plugin dal menu 'Plugin' di WordPress
3. Vai su Impostazioni → EG Social Timeline
4. Configura almeno un profilo social
5. Inserisci shortcode `[eg_social_timeline]` dove vuoi mostrare la timeline

= Shortcode =

Base:
`[eg_social_timeline]`

Con limite personalizzato:
`[eg_social_timeline limit="20"]`

== Frequently Asked Questions ==

= Quali piattaforme sono supportate? =

Attualmente: Mastodon (e istanze compatibili ActivityPub), Diggita (Lemmy), Forgejo/Gitea. Bluesky è in sviluppo.

= Come funzionano i filtri? =

I filtri usano CSS puro con checkbox HTML. Click su checkbox mostra/nasconde immediatamente i post di quella piattaforma. Non richiede JavaScript e funziona anche con JS disabilitato.

= Posso personalizzare le icone? =

Sì! Le icone sono file SVG in `wp-content/plugins/eg-social-timeline/social-icons/`. Sostituisci il file mantenendo dimensioni 24x24px e `viewBox="0 0 24 24"`.

= Il plugin traccia i miei dati? =

No. Il plugin richiede SOLO dati pubblici dalle API social e non invia nessun dato a terze parti. È completamente privacy-friendly.

= Quanto dura la cache? =

Configurabile da 30 minuti a 24 ore nelle impostazioni. Default: 1 ora. Cache riduce richieste API e migliora performance.

= Posso usare più istanze nello stesso sito? =

Sì, puoi inserire shortcode in più pagine/post. La configurazione è globale (unica per tutto il sito).

= Come aggiorno il plugin? =

Consigliato: installa [Git Updater](https://git-updater.com/) per aggiornamenti automatici da Gitea. Alternativa: scarica manualmente nuova versione e sostituisci file.

= Dove trovo il codice sorgente? =

Repository ufficiale: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline

= Come contribuisco? =

Pull request benvenute su Gitea! Leggi CONTRIBUTING.md nel repository.

== Screenshots ==

1. Timeline unificata con post da Mastodon e Diggita
2. Box filtri piattaforme con checkbox
3. Pannello impostazioni admin
4. Post con statistiche interazioni
5. Design responsive mobile
6. Dark mode automatico

== Changelog ==

= 1.2.1 - 2026-01-11 =
* Fix: Checkbox filtri ora posizionati come siblings degli article per corretto funzionamento CSS
* Fix: Post ora visibili correttamente insieme ai filtri (hotfix v1.2.0)

= 1.2.0 - 2026-01-11 =
* Aggiunta integrazione Forgejo/Gitea (commit e nuovi repository)
* Aggiunto campo settings URL istanza Forgejo configurabile
* Aggiunto supporto filtro Forgejo in timeline
* Fix: sistema icone ora legge da file SVG invece di hardcoded
* Fix: icone modificabili senza toccare codice PHP

= 1.1.2 - 2026-01-11 =
* Fix: URL boost Mastodon non mostrano più JSON activity
* Aggiunto sistema filtri CSS puro con checkbox
* Aggiunto attributo data-platform per filtri
* UX: Più spazio tra post e link "Vedi originale" più visibile

= 1.1.1 - 2026-01-10 =
* Fix: Rimossi post duplicati da timeline

= 1.1.0 - 2026-01-10 =
* Aggiunta API Mastodon v1 (sostituisce RSS)
* Aggiunte statistiche complete (like, boost, commenti) per Mastodon
* Aggiunto sistema icone modulare con file SVG
* Aggiunta cartella social-icons/ per icone personalizzabili
* Cambiato: Migrazione da RSS a API REST per Mastodon

= 1.0.0 - 2026-01-10 =
* Release iniziale MVP
* Supporto Mastodon (RSS)
* Supporto Diggita (RSS)
* Sistema cache configurabile
* Admin settings panel
* Shortcode base

== Upgrade Notice ==

= 1.2.1 =
Hotfix importante! Risolve problema post invisibili in v1.2.0. Aggiornamento immediato raccomandato.

= 1.2.0 =
Nuova integrazione Forgejo/Gitea! Sistema icone migliorato: ora le icone si caricano da file SVG invece di essere hardcoded nel codice.

= 1.1.2 =
Fix importante per URL boost Mastodon. Sistema filtri CSS integrato per mostrare/nascondere piattaforme.

= 1.1.1 =
Fix post duplicati. Aggiornamento raccomandato.

= 1.1.0 =
Migrazione a API Mastodon con statistiche complete! Richiede riconfigurazione URL profilo Mastodon in impostazioni.

= 1.0.0 =
Prima release stabile.

== Additional Info ==

= API Utilizzate =

* Mastodon: `/api/v1/accounts/{id}/statuses`
* Diggita: RSS `/feeds/u/{username}.xml`
* Forgejo: `/api/v1/users/{username}/activities/feeds`

= Credits =

Sviluppato da [Emanuele Gori](https://emanuelegori.uno)

= Supporto =

* Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Issues: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
* Mastodon: @emanuelegori@mastodon.uno

= Licenza =

GPL-2.0-or-later
