# Changelog

## [1.1.0] - 2026-01-10

### 🎨 Architettura Modulare Icone
- **Nuova cartella `social-icons/`** con file SVG separati
- Icone facilmente sostituibili senza modificare PHP
- Struttura estendibile per nuove piattaforme
- File SVG individuali:
  - `mastodon.svg` - Logo ufficiale Mastodon
  - `diggita.svg` - Icona personalizzata Diggita
  - `lemmy.svg` - Icona personalizzata Lemmy
  - `bluesky.svg` - Logo ufficiale Bluesky
  - `generic.svg` - Fallback per piattaforme sconosciute
- Documentazione completa in `social-icons/README.md`


### 🚀 Nuove Funzionalità

#### API Mastodon Integrata
- **Sostituito feed RSS con API Mastodon nativa** per dati completi
- Supporto completo per boost/reblog (finalmente funzionante!)
- Conteggi in tempo reale: ❤️ like, 🔁 boost, 💬 risposte
- Badge visivo per post boostati
- Filtro boost/repost ora effettivo (API parameter: `exclude_reblogs`)

#### Statistiche Post
- Nuova opzione admin: **"Mostra Statistiche"** per abilitare/disabilitare conteggi
- Visualizzazione conteggi interazioni sotto ogni post
- Colori specifici per tipo interazione:
  - ❤️ Like: rosso (`#e0245e`)
  - 🔁 Boost: verde (`#17bf63`)
  - 💬 Risposte: blu (`#1da1f2`)

#### Icone SVG Migliorate
- Icona Mastodon ufficiale (elefante stilizzato)
- Icona Diggita moderna e pulita
- Icona Bluesky (farfalla) pronta per Fase 2
- Dimensioni uniformi 24x24px

### 🔧 Miglioramenti

#### Performance & Caching
- Cache intelligente Account ID Mastodon (24h)
- Riduzione chiamate API duplicate
- Gestione errori più robusta con debug logging

#### User Experience
- Badge "🔁 Boost" per post riboostati (senza avatar, solo badge)
- Bordo verde laterale su post boostati (visual indicator)
- Tooltip su statistiche (hover mostra "Preferiti", "Boost", "Risposte")
- Dark mode completo per nuove statistiche

### 🐛 Bug Fix
- **RISOLTO:** Filtro boost non funzionava (feed RSS non include boost)
- **RISOLTO:** Opzione "Mostra Boost/Repost" era inutile con RSS
- **RISOLTO:** Icone poco visibili e non uniformi

### 📝 Modifiche Tecniche

#### Da RSS a API
```php
// PRIMA (RSS)
$rss_url = $profile_url . '.rss';
// Solo post originali, nessuna info su boost/like

// DOPO (API)
$api_url = "https://{$instance}/api/v1/accounts/{$id}/statuses";
// Post completi con statistiche e controllo boost
```

#### Nuovi Endpoint API
- `GET /api/v1/accounts/lookup?acct={username}` - Ottiene Account ID
- `GET /api/v1/accounts/{id}/statuses` - Ottiene post con statistiche
- Parameter: `exclude_reblogs` per filtrare boost
- Parameter: `exclude_replies` per filtrare risposte

#### Struttura Dati Post Estesa
```php
array(
    'platform' => 'mastodon',
    'date' => 1234567890,
    'title' => 'Post title',
    'content' => 'Post content',
    'link' => 'https://...',
    'is_boost' => false,
    'favourites_count' => 10,  // NEW
    'reblogs_count' => 6,      // NEW
    'replies_count' => 0       // NEW
)
```

### 🎨 CSS Aggiornamenti

#### Nuovi Stili
- `.post-stats` - Container statistiche
- `.stat-item` - Singola statistica (like/boost/reply)
- `.boost-badge` - Badge "🔁 Boost"
- `.timeline-item.is-boost` - Bordo verde per boost

#### Responsive
- Mobile: gap ridotto statistiche (10px)
- Dark mode: colori statistiche adattati

### 📋 Impostazioni Admin

#### Nuova Opzione
- **Mostra Statistiche**: Toggle per abilitare/disabilitare conteggi interazioni
- Default: `true` (attivo)

#### Opzione Aggiornata
- **Mostra Boost/Repost**: Ora funzionante grazie ad API Mastodon
- Filtra i boost prima del rendering
- API parameter: `exclude_reblogs=true/false`

### 🔄 Compatibilità

#### Breaking Changes
Nessuno! Plugin 100% retrocompatibile:
- Shortcode invariato: `[eg_social_timeline]`
- Opzioni salvate mantenute
- Cache auto-invalidata al primo caricamento

#### Requisiti
- WordPress: 5.0+
- PHP: 7.4+
- Istanze Mastodon con API v1 (tutte)

### 📊 Confronto v1.0.0 vs v1.1.0

| Feature | v1.0.0 (RSS) | v1.1.0 (API) |
|---------|-------------|--------------|
| Post originali | ✅ | ✅ |
| Boost visibili | ❌ | ✅ |
| Filtro boost funzionante | ❌ | ✅ |
| Conteggi like | ❌ | ✅ |
| Conteggi boost | ❌ | ✅ |
| Conteggi risposte | ❌ | ✅ |
| Badge boost | ❌ | ✅ |
| Icone SVG | Basic | Professional |

### 🚧 Limitazioni Note

1. **Diggita/Lemmy**: Ancora su RSS (no statistiche disponibili)
2. **Bluesky**: Non implementato (pianificato Fase 2)
3. **Avatar utenti**: Non visualizzati (scelta design)
4. **Citazioni Mastodon**: Non incluse nel conteggio risposte

### 🔮 Prossimi Passi (v1.2.0)

- [ ] Integrazione Bluesky API
- [ ] Statistiche anche per Diggita (se API disponibile)
- [ ] Filtro per tipo post (solo originali/solo boost)
- [ ] Ordinamento personalizzabile (data/popolarità)

---

## [1.0.1] - 2026-01-10 (Non Rilasciata)

### Modifiche Annullate
- Tentativo fix filtro boost su RSS (non possibile tecnicamente)
- Icone SVG migliorate (implementate in v1.1.0)

---

## [1.0.0] - 2026-01-09

### Rilascio Iniziale
- Feed RSS Mastodon
- Feed RSS Diggita
- Timeline unificata cronologica
- Shortcode `[eg_social_timeline]`
- Admin panel configurazione
- Sistema caching (WordPress Transients)
- CSS responsive con dark mode
- Opzione "Mostra Boost" (non funzionante - fix in v1.1.0)
