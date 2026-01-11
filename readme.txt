=== EG Social Timeline ===
Contributors: emanuelegori
Tags: mastodon, fediverse, social media, timeline, aggregator, forgejo, gitea, lemmy
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.2.5
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mostra una timeline unificata delle tue attività social da Mastodon, Diggita (Lemmy), Forgejo/Gitea e Bluesky.

== Description ==

EG Social Timeline è un plugin WordPress che aggrega e mostra in ordine cronologico i tuoi post pubblici da diverse piattaforme social decentralizzate.

= Piattaforme Supportate =

* **Mastodon** e compatibili ActivityPub (con statistiche complete)
* **Diggita** (Lemmy) - piattaforma social italiana con statistiche pulite
* **Forgejo/Gitea** - attività repository (commit via API diretta)

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

Attualmente: Mastodon (e istanze compatibili ActivityPub), Diggita (Lemmy), Forgejo/Gitea.

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

= 1.2.5 - 2026-01-11 =
* Fix: Diggita statistiche ora visualizzate correttamente (parsing <br> → newline)
* Fix: Forgejo nome repository visibile in timeline
* Fix: Pulsanti filtri larghezza automatica (risolve altezza disuniforme)
* Fix: Icone filtri dimensioni uniformi
* Changed: CSS pulsanti filtri senza larghezza fissa
* Changed: Rimosso object-fit da icone per rendering uniforme

= 1.2.4 - 2026-01-11 =
* Fix: Diggita statistiche parsing HTML robusto
* Fix: Forgejo nome repository incluso nel content

= 1.2.3 - 2026-01-11 =
* Fix: Filtri CSS logica invertita
* Fix: Forgejo link pagina commits
* Fix: UI filtri compatta

= 1.2.2 - 2026-01-11 =
* Fix: Forgejo API commits diretta
* Fix: Diggita contenuto pulito

= 1.2.1 - 2026-01-11 =
* Fix: Checkbox filtri posizionati come siblings

= 1.2.0 - 2026-01-11 =
* Aggiunta integrazione Forgejo/Gitea
* Fix: sistema icone file-based

= 1.1.2 - 2026-01-11 =
* Fix: URL boost Mastodon
* Aggiunto sistema filtri CSS

= 1.1.1 - 2026-01-10 =
* Fix: Rimossi post duplicati

= 1.1.0 - 2026-01-10 =
* Aggiunta API Mastodon v1
* Sistema icone modulare

= 1.0.0 - 2026-01-10 =
* Release iniziale MVP

== Upgrade Notice ==

= 1.2.5 =
Fix importante UI! Risolve pulsanti filtri altezza disuniforme e icone Diggita. Aggiornamento raccomandato.

= 1.2.4 =
Fix statistiche Diggita e nome repo Forgejo. Aggiornamento raccomandato.

= 1.2.3 =
Fix critico filtri CSS. Aggiornamento raccomandato.

== Additional Info ==

= API Utilizzate =

* Mastodon: `/api/v1/accounts/{id}/statuses`
* Diggita: RSS `/feeds/u/{username}.xml` (con parsing statistiche)
* Forgejo: `/api/v1/users/{username}/repos` + `/api/v1/repos/{owner}/{repo}/commits`

= Credits =

Sviluppato da [Emanuele Gori](https://emanuelegori.uno)

= Supporto =

* Repository: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
* Issues: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues
* Mastodon: @emanuelegori@mastodon.uno

= Licenza =

GPL-2.0-or-later
