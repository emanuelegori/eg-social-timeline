# EG Social Timeline

[![Versione](https://img.shields.io/badge/Versione-1.2.3-green)](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)
[![Licenza](https://img.shields.io/badge/Licenza-GPL--2.0--or--later-blue.svg)](LICENSE)
[![WordPress](https://img.shields.io/badge/WordPress-5.0+-orange.svg)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net)

Plugin WordPress per mostrare una timeline cronologica unificata delle tue attività social da **Mastodon**, **Diggita** (Lemmy), **Forgejo/Gitea** e **Bluesky**.

---

## 🚀 Caratteristiche

- **Timeline Unificata**: Aggrega post da multiple piattaforme in ordine cronologico
- **Piattaforme Supportate**:
  - 🐘 **Mastodon** (e compatibili ActivityPub)
  - 📰 **Diggita** (Lemmy) con statistiche complete
  - 🦊 **Forgejo/Gitea** (commit repository)
  - 🦋 **Bluesky** (in sviluppo)
- **Filtri Interattivi**: Sistema filtri CSS puro per mostrare/nascondere piattaforme
- **Sistema Icone Modulare**: Icone SVG caricate da file, facilmente personalizzabili
- **Cache Intelligente**: Riduce richieste API con cache configurabile
- **Statistiche Interazioni**: Mostra like, boost e commenti per ogni post
- **Responsive**: Design ottimizzato per desktop, tablet e mobile
- **Dark Mode**: Supporto automatico tema scuro
- **Privacy-Friendly**: Solo dati pubblici, nessun tracking

---

## 📦 Installazione

### Automatica (WordPress)

1. Scarica ultima versione da [Gitea](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline)
2. Vai su **Plugin → Aggiungi nuovo → Carica plugin**
3. Seleziona file ZIP scaricato
4. Clicca **Installa** e poi **Attiva**

### Manuale (FTP/SSH)

```bash
cd wp-content/plugins
git clone https://git.emanuelegori.uno/emanuelegori/eg-social-timeline.git
```

### Git Updater (Consigliato)

Installa [Git Updater](https://git-updater.com/) per aggiornamenti automatici da Gitea.

---

## ⚙️ Configurazione

1. Vai su **Impostazioni → EG Social Timeline**
2. Configura almeno un profilo:
   - **Mastodon**: URL completo profilo (es: `https://mastodon.uno/@emanuelegori`)
   - **Diggita**: Username (senza @) (es: emanuelegori)
   - **Forgejo**: URL istanza (es: `https://git.emanuelegori.uno`)
   - **Forgejo**: Username (es: emanuelegori)
3. Regola impostazioni cache e visualizzazione
4. Salva

---

## 📝 Utilizzo

### Shortcode Base

```
[eg_social_timeline]
```

### Con Limite Personalizzato

```
[eg_social_timeline limit="20"]
```

### Esempio Completo

```
<h2>La mia attività recente</h2>
[eg_social_timeline limit="15"]
```

---

## 🎨 Filtri Piattaforme

Sistema filtri CSS integrato:

```
┌──────────────────────────────────────┐
│ 🔍 Filtra per piattaforma:           │
│ ☑ Mastodon (12) ☑ Diggita (8)      │
│ ☑ Forgejo (5)   ☐ Bluesky (2)      │
└──────────────────────────────────────┘
```

Click checkbox = mostra/nascondi post istantaneamente (zero JavaScript richiesto!)

---

## 🔧 Personalizzazione

### Icone Piattaforme

Le icone sono file SVG in `social-icons/`:

```
social-icons/
├── mastodon.svg
├── diggita.svg
├── forgejo.svg
├── bluesky.svg
└── blog.svg
```

**Per personalizzare:**
1. Sostituisci file SVG con tua icona
2. Mantieni dimensioni 24x24px e `viewBox="0 0 24 24"`
3. Usa `fill="currentColor"` per eredità colore

### CSS Personalizzato

Crea `wp-content/themes/tuo-tema/eg-social-timeline-custom.css`:

```css
/* Cambia colore primario */
.eg-timeline-filters {
    border-color: #YOUR_COLOR;
}

/* Personalizza card post */
.timeline-item {
    background: #YOUR_BG;
}
```

---

## 🛠️ Sviluppo

### Requisiti

- WordPress 6.7+
- PHP 7.4+
- API access alle piattaforme configurate

### Struttura File

```
eg-social-timeline/
├── eg-social-timeline.php    # Plugin principale
├── eg-social-timeline.css     # Stili
├── social-icons/              # Icone SVG
│   ├── mastodon.svg
│   ├── diggita.svg
│   ├── forgejo.svg
│   └── bluesky.svg
├── languages/                 # Traduzioni
├── README.md
├── readme.txt                 # WordPress readme
└── LICENSE
```

### API Utilizzate

- **Mastodon**: `/api/v1/accounts/{id}/statuses`
- **Diggita**: RSS `/feeds/u/{username}.xml` (con parsing statistiche)
- **Forgejo**: `/api/v1/users/{username}/repos` + `/api/v1/repos/{owner}/{repo}/commits`

---

## 📋 Changelog

### [1.2.3] - 2026-01-11

#### Fixed
- **Filtri CSS**: logica invertita corregge bug selezione (ora: nascondi tutto, mostra checked)
- **Diggita parsing**: statistiche estratte correttamente con parsing robusto multi-riga
- **Forgejo link**: ora punta a pagina commits repo invece di singolo commit
- **UI filtri**: ridotto font-size per box filtri più compatto (0.95em → 0.85em)

#### Changed
- CSS: checkbox label padding ridotto (8px → 6px) e icone più piccole (20px → 16px)
- CSS: header filtri font-size ridotto (1.1em → 0.95em)
- Forgejo: URL commits page format `/commits/branch/{branch}` invece di `/commit/{sha}`

### [1.2.2] - 2026-01-11

#### Fixed
- Forgejo: risolto feed vuoto usando API commits diretta
- Diggita: rimosso "submitted by..." dal contenuto
- Diggita: estratte statistiche punti/commenti

#### Changed
- Forgejo: usa `/repos` + `/commits` API
- Diggita: emoji ⭐ (punti) e 💬 (commenti)

### [1.2.1] - 2026-01-11

#### Fixed
- Filtri CSS: checkbox posizionati come siblings degli article

### [1.2.0] - 2026-01-11

#### Added
- Integrazione Forgejo/Gitea
- Sistema icone file-based

### [1.1.0-1.1.2] - 2026-01-10/11
- API Mastodon, filtri CSS, fix vari

### [1.0.0] - 2026-01-10
- Release iniziale MVP

---

## 🤝 Contributi

I contributi sono benvenuti!

1. Fork repository
2. Crea branch feature (`git checkout -b feature/AmazingFeature`)
3. Commit modifiche (`git commit -m 'Add AmazingFeature'`)
4. Push branch (`git push origin feature/AmazingFeature`)
5. Apri Pull Request

---

## 📄 Licenza

Questo progetto è rilasciato sotto licenza **GPL-2.0-or-later**.

Vedi file [LICENSE](LICENSE) per dettagli completi.

---

## 👤 Autore

**Emanuele Gori**

- Website: [emanuelegori.uno](https://emanuelegori.uno)
- Mastodon: [@emanuelegori@mastodon.uno](https://mastodon.uno/@emanuelegori)
- Gitea: [git.emanuelegori.uno](https://git.emanuelegori.uno/emanuelegori)

---

## 🙏 Ringraziamenti

- Community Mastodon per API ben documentate
- Diggita.com per piattaforma Lemmy italiana
- Forgejo/Gitea per eccellente API
- WordPress community

---

## 📞 Supporto

- **Issues**: [Gitea Issues](https://git.emanuelegori.uno/emanuelegori/eg-social-timeline/issues)
- **Documentazione**: Questo README
- **Discussioni**: [Mastodon @emanuelegori](https://mastodon.uno/@emanuelegori)

---

**⭐ Se trovi utile questo plugin, lascia una stella su Gitea!**
