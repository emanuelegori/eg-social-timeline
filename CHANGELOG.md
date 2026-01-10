# Changelog

## [1.1.2] - 2026-01-10

### 🐛 Bug Fix

#### Link Boost Mostrano JSON
- **RISOLTO**: Link a post boostati terminano con `/activity` e mostrano JSON invece della pagina web
- **Causa**: API Mastodon restituisce URL dell'attività di boost invece dell'URL del post originale
- **Fix**: Quando è un boost, usa `reblog.url` (post originale) invece di `status.url` (attività boost)

### 🔧 Dettagli Tecnici

#### Prima (v1.1.1) - URL Sbagliato
```php
'link' => $status['url']  // Per boost: termina con /activity
```

**Risultato**: 
```
https://mastodon.uno/users/emanuelegori/statuses/115838898186338772/activity
                                                                      ^^^^^^^^
                                                                      Mostra JSON!
```

#### Dopo (v1.1.2) - URL Corretto
```php
$post_url = $is_boost ? $content_data['url'] : $status['url'];
'link' => $post_url  // Per boost: URL post originale senza /activity
```

**Risultato**:
```
https://mastodon.uno/@username/115838898186338772
                     ^^^^^^^^^^^^^^^^^^^^^^^^^^
                     Pagina web normale!
```

### 📊 Comportamento

| Tipo Post | v1.1.1 URL | v1.1.2 URL |
|-----------|------------|------------|
| Post originale | `status.url` ✅ | `status.url` ✅ |
| Boost | `status.url` ❌ `/activity` | `reblog.url` ✅ normale |

### 🔄 Impatto

- ✅ Link boost ora aprono pagina web leggibile
- ✅ Utenti possono vedere post, rispondere, likare normalmente
- ✅ Nessun cambio visivo nella timeline (solo link funzionanti)
- ✅ Retrocompatibile: post originali invariati

### 📝 Modifica Codice

**File**: `eg-social-timeline.php`  
**Linea**: ~420 (funzione `eg_social_timeline_fetch_mastodon()`)

```php
// AGGIUNTO: Calcolo URL corretto prima del post array
$post_url = $is_boost ? $content_data['url'] : $status['url'];

// MODIFICATO: Usa $post_url invece di $status['url']
'link' => $post_url,
```

### 🚀 Aggiornamento

```bash
# Backup
cp eg-social-timeline.php{,.v1.1.1.backup}

# Sostituisci file
cp eg-social-timeline-v1.1.2.php eg-social-timeline.php

# Nessuna modifica CSS necessaria

# Commit
git add eg-social-timeline.php
git commit -m "Fix v1.1.2 - Correggi URL boost con /activity"
git push origin main

# Svuota cache WordPress
```

### ✅ Test

Dopo l'aggiornamento:

1. Trova un post con badge "🔁 Boost"
2. Clicca "Vedi post originale →"
3. Verifica che si apra la **pagina web normale** (non JSON)
4. URL NON deve terminare con `/activity`

### 🔗 Esempio Pratico

**Prima (v1.1.1)**:
```
Clic su "Vedi post originale" → 
Browser mostra:
{
  "@context": "https://www.w3.org/ns/activitystreams",
  "type": "Announce",
  ...
}
```
❌ JSON incomprensibile!

**Dopo (v1.1.2)**:
```
Clic su "Vedi post originale" →
Browser mostra normale post Mastodon con:
- Testo completo
- Immagini/media
- Bottoni like/boost/risposta
- Commenti
```
✅ Pagina web leggibile!

### 🎯 Priorità

**Fix Minore ma Importante**

- Non critico (timeline funziona)
- Ma molto fastidioso per utenti che cliccano link
- Raccomandato aggiornamento rapido

### 📋 Compatibilità

- ✅ Compatibile con v1.1.0, v1.1.1
- ✅ Nessun breaking change
- ✅ Shortcode invariato
- ✅ Opzioni admin invariate
- ✅ CSS invariato
- ✅ Database invariato

### 🔮 Note

Questo fix completa la trilogia di hotfix post-v1.1.0:
- v1.1.1: Fix duplicazione contenuto + leggibilità
- v1.1.2: Fix URL boost con `/activity`

La v1.2.0 sarà una release feature completa con Bluesky!

---

## [1.1.1] - 2026-01-10

### 🐛 Bug Fix Critici
*(Changelog v1.1.1 precedente...)*

---

## [1.1.0] - 2026-01-10

### 🚀 Nuove Funzionalità
*(Changelog v1.1.0 precedente...)*
