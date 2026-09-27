<?php
/**
 * Plugin Name: EG Social Timeline
 * Plugin URI: https://emanuelegori.uno/en/plugins/eg-social-timeline/
 * Description: Unified chronological timeline of your public activity from Mastodon, GoToSocial, Friendica, Bluesky, Pixelfed, PeerTube, Forgejo, Lemmy, ListenBrainz and any RSS or Atom feed. Zero JavaScript, zero tracking.
 * Version: 1.17.0
 * Author: Emanuele Gori
 * Author URI: https://emanuelegori.uno
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: eg-social-timeline
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * Gitea Plugin URI: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
 * Primary Branch: main
 */

/*
Copyright (C) 2025  Emanuele Gori

Questo programma è software libero; puoi redistribuirlo e/o
modificarlo secondo i termini della GNU General Public License
come pubblicata dalla Free Software Foundation; versione 2 della
Licenza, o (a tua scelta) qualsiasi versione successiva.

Questo programma è distribuito nella speranza che sia utile,
ma SENZA ALCUNA GARANZIA; senza neppure la garanzia implicita
di COMMERCIABILITÀ o IDONEITÀ PER UN PARTICOLARE SCOPO.
Vedi la Licenza Pubblica Generale GNU per maggiori dettagli.

Dovresti aver ricevuto una copia della Licenza Pubblica Generale GNU
insieme a questo programma; in caso contrario, visita:
https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;

// Constants
define('EG_SOCIAL_TIMELINE_VERSION', '1.17.0');
define('EG_SOCIAL_TIMELINE_DIR', plugin_dir_path(__FILE__));
define('EG_SOCIAL_TIMELINE_URL', plugin_dir_url(__FILE__));
define('EG_SOCIAL_TIMELINE_DEBUG', false);
define('EG_SOCIAL_TIMELINE_BLUESKY_SERVICE', 'https://public.api.bsky.app');
define('EG_SOCIAL_TIMELINE_LISTENBRAINZ_API', 'https://api.listenbrainz.org');

// L'indirizzo del codice non cambia con la lingua, gli altri si': stanno in
// eg_social_timeline_project_urls(), qui sotto.
define('EG_SOCIAL_TIMELINE_REPO_URL', 'https://git.emanuelegori.uno/emanuelegori/eg-social-timeline');

// Admin menu
add_action('admin_menu', 'eg_social_timeline_admin_menu');

function eg_social_timeline_admin_menu() {
    add_options_page(
        __('EG Social Timeline', 'eg-social-timeline'),
        __('EG Social Timeline', 'eg-social-timeline'),
        'manage_options',
        'eg-social-timeline',
        'eg_social_timeline_settings_page'
    );
}

/**
 * Descrizione delle piattaforme: un posto solo da cui nascono le sezioni del
 * pannello, i campi e le caselle "Cosa mostrare".
 *
 * `display` elenca soltanto le opzioni che hanno effetto su quella fonte: i
 * boost esistono su Mastodon e Bluesky, le statistiche dove i contatori
 * arrivano davvero, le anteprime dove il fetcher produce un'immagine.
 *
 * @return array Definizione per slug di piattaforma.
 */
function eg_social_timeline_platforms() {
    return array(
        'mastodon' => array(
            'title'   => __('Mastodon / Pleroma / Akkoma', 'eg-social-timeline'),
            'intro'   => __('Public accounts API, no token needed.', 'eg-social-timeline'),
            'fields'  => array(
                'mastodon_instance' => __('Instance URL', 'eg-social-timeline'),
                'mastodon_username' => __('Username', 'eg-social-timeline'),
                'mastodon_limit'    => __('Max posts', 'eg-social-timeline'),
            ),
            'display' => array('boosts', 'stats', 'images'),
        ),
        'gotosocial' => array(
            'title'   => __('GoToSocial', 'eg-social-timeline'),
            'intro'   => __('Public RSS feed of the profile, off by default: turn it on in your account settings. Public posts only, no replies or boosts.', 'eg-social-timeline'),
            'fields'  => array(
                'gotosocial_instance' => __('Instance URL', 'eg-social-timeline'),
                'gotosocial_username' => __('Username', 'eg-social-timeline'),
                'gotosocial_limit'    => __('Max posts', 'eg-social-timeline'),
            ),
            'display' => array('images'),
        ),
        'friendica' => array(
            'title'   => __('Friendica', 'eg-social-timeline'),
            'intro'   => __('Public Atom feed of the profile: posts without replies, no interaction counts.', 'eg-social-timeline'),
            'fields'  => array(
                'friendica_instance' => __('Instance URL', 'eg-social-timeline'),
                'friendica_username' => __('Username', 'eg-social-timeline'),
                'friendica_limit'    => __('Max posts', 'eg-social-timeline'),
            ),
            'display' => array('images'),
        ),
        'bluesky' => array(
            'title'   => __('Bluesky', 'eg-social-timeline'),
            'intro'   => __('Not federated: the public endpoint is the same for everyone.', 'eg-social-timeline'),
            'fields'  => array(
                'bluesky_instance' => __('Service', 'eg-social-timeline'),
                'bluesky_handle'   => __('Handle', 'eg-social-timeline'),
                'bluesky_limit'    => __('Max posts', 'eg-social-timeline'),
            ),
            'display' => array('boosts', 'stats', 'images'),
        ),
        'lemmy' => array(
            'title'   => __('Lemmy', 'eg-social-timeline'),
            'intro'   => __('Public user feed, on the instance where your account is registered.', 'eg-social-timeline'),
            'fields'  => array(
                'lemmy_instance' => __('Instance URL', 'eg-social-timeline'),
                'lemmy_username' => __('Username', 'eg-social-timeline'),
                'lemmy_limit'    => __('Max posts', 'eg-social-timeline'),
            ),
            'display' => array('stats'),
        ),
        'pixelfed' => array(
            'title'   => __('Pixelfed', 'eg-social-timeline'),
            'intro'   => __('Public Atom feed: photos and captions, no interaction counts.', 'eg-social-timeline'),
            'fields'  => array(
                'pixelfed_instance' => __('Instance URL', 'eg-social-timeline'),
                'pixelfed_username' => __('Username', 'eg-social-timeline'),
                'pixelfed_limit'    => __('Max posts', 'eg-social-timeline'),
            ),
            'display' => array('images'),
        ),
        'peertube' => array(
            'title'   => __('PeerTube', 'eg-social-timeline'),
            'intro'   => __('Public API: videos from an account or from a channel.', 'eg-social-timeline'),
            'fields'  => array(
                'peertube_instance' => __('Instance URL', 'eg-social-timeline'),
                'peertube_username' => __('Account or channel', 'eg-social-timeline'),
                'peertube_limit'    => __('Max videos', 'eg-social-timeline'),
            ),
            'display' => array('stats', 'images'),
        ),
        'forgejo' => array(
            'title'   => __('Forgejo / Gitea', 'eg-social-timeline'),
            'intro'   => __('Commits from the public repositories of this account.', 'eg-social-timeline'),
            'fields'  => array(
                'forgejo_instance' => __('Instance URL', 'eg-social-timeline'),
                'forgejo_username' => __('Username', 'eg-social-timeline'),
                'forgejo_limit'    => __('Max commits', 'eg-social-timeline'),
            ),
            'display' => array(),
        ),
        'listenbrainz' => array(
            'title'   => __('ListenBrainz', 'eg-social-timeline'),
            'intro'   => __('Public API, no token: artist and track with the listen date.', 'eg-social-timeline'),
            'fields'  => array(
                'listenbrainz_instance' => __('API URL', 'eg-social-timeline'),
                'listenbrainz_username' => __('Username', 'eg-social-timeline'),
                'listenbrainz_limit'    => __('Max listens', 'eg-social-timeline'),
            ),
            'display' => array(),
        ),
        'rss' => array(
            'title'   => __('RSS or Atom feed', 'eg-social-timeline'),
            'intro'   => __('A blog, a newsletter, a podcast: anything with a feed.', 'eg-social-timeline'),
            'fields'  => array(
                'rss_url'   => __('Feed URL', 'eg-social-timeline'),
                'rss_label' => __('Label', 'eg-social-timeline'),
                'rss_limit' => __('Max items', 'eg-social-timeline'),
            ),
            'display' => array('images'),
        ),
    );
}

/**
 * Valore di una casella "Cosa mostrare" per una piattaforma.
 *
 * Ordine: la scelta salvata per quella piattaforma, poi la vecchia opzione
 * globale (così chi aggiorna non vede cambiare nulla), infine il default
 * delle installazioni nuove, che è tutto attivo.
 *
 * @param string $platform Slug della piattaforma.
 * @param string $key      'boosts', 'stats' oppure 'images'.
 * @return bool
 */
function eg_social_timeline_display_option($platform, $key) {
    $options = get_option('eg_social_timeline_options');

    if (!is_array($options)) {
        $options = array();
    }

    $own = $platform . '_show_' . $key;

    if (array_key_exists($own, $options)) {
        return !empty($options[$own]);
    }

    $legacy = array(
        'boosts' => 'show_boosts',
        'stats'  => 'show_stats',
        'images' => 'show_images',
    );

    if (isset($legacy[$key]) && array_key_exists($legacy[$key], $options)) {
        return !empty($options[$legacy[$key]]);
    }

    return true;
}

/**
 * Lunghezza del testo per una piattaforma, con lo stesso ordine di ripiego.
 *
 * @param string $platform Slug della piattaforma.
 * @return int Caratteri, 0 per nessun limite.
 */
function eg_social_timeline_truncate_option($platform) {
    $options = get_option('eg_social_timeline_options');

    if (!is_array($options)) {
        $options = array();
    }

    $own = $platform . '_truncate';

    if (isset($options[$own]) && '' !== $options[$own]) {
        $value = intval($options[$own]);

        return (0 === $value) ? 0 : max(50, min(600, $value));
    }

    if (isset($options['truncate_length'])) {
        $value = intval($options['truncate_length']);

        return (0 === $value) ? 0 : max(50, min(600, $value));
    }

    return 300;
}

// Settings API
add_action('admin_init', 'eg_social_timeline_register_settings');

function eg_social_timeline_register_settings() {
    register_setting(
        'eg_social_timeline_settings',
        'eg_social_timeline_options',
        array(
            'sanitize_callback' => 'eg_social_timeline_sanitize_options',
            'default' => array(
                'mastodon_instance' => '',
                'mastodon_username' => '',
                'lemmy_instance' => '',
                'lemmy_username' => '',
                'forgejo_username' => '',
                'forgejo_instance' => 'https://gitea.com',
                'post_limit' => 50,
                'cache_duration' => 1800,
                // Le tre chiavi globali show_boosts/show_stats/show_images non
                // stanno qui di proposito: display_option() le usa come ripiego
                // per chi aggiorna da prima della 1.14.0, e trovarle fra i
                // default spegneva boost e anteprime sulle installazioni nuove,
                // che invece devono partire con tutto attivo.
                'mastodon_limit' => 5,
                'lemmy_limit' => 5,
                'forgejo_limit' => 5,
                'bluesky_handle' => '',
                'bluesky_limit' => 5,
                'peertube_username' => '',
                'peertube_instance' => '',
                'peertube_type' => 'auto',
                'pixelfed_username' => '',
                'pixelfed_instance' => '',
                'pixelfed_limit' => 5,
                'gotosocial_username' => '',
                'gotosocial_instance' => '',
                'gotosocial_limit' => 5,
                'friendica_username' => '',
                'friendica_instance' => '',
                'friendica_limit' => 5,
                'listenbrainz_username' => '',
                'listenbrainz_instance' => EG_SOCIAL_TIMELINE_LISTENBRAINZ_API,
                'listenbrainz_limit' => 5,
                'rss_url' => '',
                'rss_label' => '',
                'rss_limit' => 5,
                'peertube_limit' => 5,
                'truncate_length' => 300,
                'icon_style' => 'brand',
                'filters_style' => 'full',
                'layout' => 'list',
                'show_diagnostics' => false,
                'canvas_bg' => 'none',
                'canvas_bg_color' => '#f3f4f6',
                'card_bg' => 'neutral',
                'card_bg_color' => '#ffffff'
            )
        )
    );
    
    // Un riquadro per piattaforma: icona nel titolo, campi propri, e soltanto
    // le caselle che hanno effetto su quella fonte. L'ordine dei campi dentro
    // la sezione e' l'ordine di registrazione.
    foreach (eg_social_timeline_platforms() as $slug => $platform) {
        $section = 'eg_social_timeline_' . $slug . '_section';

        add_settings_section(
            $section,
            '<span class="egst-section-icon platform-' . esc_attr($slug) . '">'
                . eg_social_timeline_get_icon($slug) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG sanitizzato internamente dalla funzione
                . '</span>' . esc_html($platform['title']),
            'eg_social_timeline_platform_section_callback',
            'eg-social-timeline'
        );

        foreach ($platform['fields'] as $field => $label) {
            add_settings_field(
                'eg_social_timeline_' . $field,
                $label,
                'eg_social_timeline_' . $field . '_callback',
                'eg-social-timeline',
                $section
            );
        }

        if (!empty($platform['display'])) {
            add_settings_field(
                'eg_social_timeline_' . $slug . '_display',
                __('What to show', 'eg-social-timeline'),
                'eg_social_timeline_display_field_callback',
                'eg-social-timeline',
                $section,
                array('platform' => $slug)
            );
        }

        add_settings_field(
            'eg_social_timeline_' . $slug . '_truncate',
            __('Text length', 'eg-social-timeline'),
            'eg_social_timeline_truncate_field_callback',
            'eg-social-timeline',
            $section,
            array('platform' => $slug)
        );
    }

    add_settings_section(
        'eg_social_timeline_diagnostics_section',
        __('Diagnostics', 'eg-social-timeline'),
        '__return_false',
        'eg-social-timeline'
    );

    add_settings_field(
        'eg_social_timeline_show_diagnostics',
        __('Configured profiles table', 'eg-social-timeline'),
        'eg_social_timeline_show_diagnostics_callback',
        'eg-social-timeline',
        'eg_social_timeline_diagnostics_section'
    );

    add_settings_section(
        'eg_social_timeline_limits_section',
        __('Timeline', 'eg-social-timeline'),
        'eg_social_timeline_limits_section_callback',
        'eg-social-timeline'
    );
    
    
    






    add_settings_field(
        'eg_social_timeline_post_limit',
        __('Number of Posts to Show', 'eg-social-timeline'),
        'eg_social_timeline_post_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_limits_section'
    );
    
    add_settings_field(
        'eg_social_timeline_cache_duration',
        __('Cache Duration', 'eg-social-timeline'),
        'eg_social_timeline_cache_duration_callback',
        'eg-social-timeline',
        'eg_social_timeline_limits_section'
    );
    
    



    add_settings_section(
        'eg_social_timeline_appearance_section',
        __('Appearance', 'eg-social-timeline'),
        'eg_social_timeline_appearance_section_callback',
        'eg-social-timeline'
    );

    add_settings_field(
        'eg_social_timeline_layout',
        __('Layout', 'eg-social-timeline'),
        'eg_social_timeline_layout_callback',
        'eg-social-timeline',
        'eg_social_timeline_appearance_section'
    );

    add_settings_field(
        'eg_social_timeline_canvas_bg',
        __('Timeline Background', 'eg-social-timeline'),
        'eg_social_timeline_canvas_bg_callback',
        'eg-social-timeline',
        'eg_social_timeline_appearance_section'
    );

    add_settings_field(
        'eg_social_timeline_card_bg',
        __('Card Background', 'eg-social-timeline'),
        'eg_social_timeline_card_bg_callback',
        'eg-social-timeline',
        'eg_social_timeline_appearance_section'
    );

    add_settings_field(
        'eg_social_timeline_filters_style',
        __('Filter Bar Style', 'eg-social-timeline'),
        'eg_social_timeline_filters_style_callback',
        'eg-social-timeline',
        'eg_social_timeline_appearance_section'
    );

    add_settings_field(
        'eg_social_timeline_icon_style',
        __('Platform Icon Style', 'eg-social-timeline'),
        'eg_social_timeline_icon_style_callback',
        'eg-social-timeline',
        'eg_social_timeline_appearance_section'
    );
}

// Settings callbacks
/**
 * Riga introduttiva di un riquadro di piattaforma.
 *
 * @param array $args Argomenti della sezione, da cui si ricava lo slug.
 */
function eg_social_timeline_platform_section_callback($args) {
    $slug = str_replace(array('eg_social_timeline_', '_section'), '', $args['id']);
    $platforms = eg_social_timeline_platforms();

    if (!empty($platforms[$slug]['intro'])) {
        echo '<p class="description">' . esc_html($platforms[$slug]['intro']) . '</p>';
    }
}

/**
 * Caselle "Cosa mostrare" di una piattaforma.
 *
 * @param array $args Contiene lo slug della piattaforma.
 */
function eg_social_timeline_display_field_callback($args) {
    $slug = $args['platform'];
    $platforms = eg_social_timeline_platforms();
    $available = isset($platforms[$slug]['display']) ? $platforms[$slug]['display'] : array();

    if (empty($available)) {
        return;
    }

    $labels = array(
        'boosts' => ('bluesky' === $slug)
            ? __('Include reposted posts', 'eg-social-timeline')
            : __('Include boosted posts', 'eg-social-timeline'),
        'stats'  => ('lemmy' === $slug)
            ? __('Points and comments', 'eg-social-timeline')
            : __('Interaction counts', 'eg-social-timeline'),
        'images' => __('Image previews', 'eg-social-timeline'),
    );

    foreach ($available as $key) {
        $name = $slug . '_show_' . $key;
        ?>
        <label style="margin-right: 18px;">
            <input type="checkbox"
                   name="eg_social_timeline_options[<?php echo esc_attr($name); ?>]"
                   value="1"
                   <?php checked(eg_social_timeline_display_option($slug, $key), true); ?>>
            <?php echo esc_html($labels[$key]); ?>
        </label>
        <?php
    }

}

/**
 * Lunghezza del testo per una piattaforma.
 *
 * @param array $args Contiene lo slug della piattaforma.
 */
function eg_social_timeline_truncate_field_callback($args) {
    $slug = $args['platform'];
    ?>
    <input type="number"
           name="eg_social_timeline_options[<?php echo esc_attr($slug . '_truncate'); ?>]"
           value="<?php echo esc_attr(eg_social_timeline_truncate_option($slug)); ?>"
           min="0"
           max="600"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Characters shown per item (50–600). 0 shows the full text.', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_limits_section_callback() {
    echo '<p>' . esc_html__('How the timeline is assembled: how many items reach the page in total, and how long they stay cached. What each source shows — boosts, statistics, previews, text length — is set inside its own box above.', 'eg-social-timeline') . '</p>';
}

/**
 * Normalizza l'URL di un'istanza in "https://host".
 *
 * Solo HTTPS: su HTTP le richieste sarebbero in chiaro. Il controllo
 * anti-SSRF rifiuta host privati o riservati.
 *
 * @param string $url URL, anche nella forma "host" senza schema.
 * @return string URL normalizzato, stringa vuota se non valido.
 */
function eg_social_timeline_normalize_instance($url) {
    $url = trim((string) $url);

    if ('' === $url) {
        return '';
    }

    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }

    $host = wp_parse_url($url, PHP_URL_HOST);

    if (empty($host)) {
        return '';
    }

    $normalized = 'https://' . strtolower($host);

    if (!eg_social_timeline_is_public_url($normalized)) {
        return '';
    }

    return $normalized;
}

/**
 * Estrae istanza e utente da un profilo del fediverso.
 *
 * Accetta l'URL del profilo (/@utente oppure /users/utente) e la forma
 * @utente@istanza. Serve per migrare il campo unico usato fino alla 1.8.1.
 *
 * @param string $input Profilo in una delle forme accettate.
 * @return array|false array con instance e username, false se non riconosciuto.
 */
function eg_social_timeline_parse_fediverse_profile($input) {
    $input = trim((string) $input);

    if ('' === $input) {
        return false;
    }

    if (preg_match('~^https?://([^/]+)/(?:@|users/)([^/?#]+)~i', $input, $matches)) {
        $instance = eg_social_timeline_normalize_instance($matches[1]);
        $username = $matches[2];
    } elseif (preg_match('~^@?([^@/\s]+)@([^@/\s]+)$~', $input, $matches)) {
        $instance = eg_social_timeline_normalize_instance($matches[2]);
        $username = $matches[1];
    } else {
        return false;
    }

    if ('' === $instance || '' === $username) {
        return false;
    }

    return array(
        'instance' => $instance,
        'username' => $username,
    );
}

/**
 * Estrae istanza, nome e tipo da un indirizzo di profilo.
 *
 * Accetta l'indirizzo che si copia dal browser, l'handle federato
 * nome@istanza e, per PeerTube, distingue canali da account guardando il
 * percorso: /c/ e /video-channels/ sono canali, /a/ e /accounts/ account.
 *
 * Quando il nome porta con se' la propria origine (canali remoti visti da
 * un'altra istanza, es. s3nnet@tube.tchncs.de su peertube.tv) vince quella:
 * interrogare il server di origine non dipende dallo stato della federazione.
 *
 * @param string $platform Slug della piattaforma.
 * @param string $value    Indirizzo o handle incollato dall'utente.
 * @return array|false array con instance, username e type; false se non riconosciuto.
 */
function eg_social_timeline_extract_profile($platform, $value) {
    $value = trim((string) $value);

    if ('' === $value) {
        return false;
    }

    $patterns = array(
        'peertube' => array(
            '~^https?://([^/]+)/(?:c|video-channels)/([^/?#]+)~i' => 'channel',
            '~^https?://([^/]+)/(?:a|accounts)/([^/?#]+)~i'       => 'account',
        ),
        'mastodon' => array(
            '~^https?://([^/]+)/(?:@|users/)([^/?#]+)~i' => '',
        ),
        // Anche l'indirizzo del feed (/@nome/feed.rss) finisce qui.
        'gotosocial' => array(
            '~^https?://([^/]+)/(?:@|users/)([^/?#]+)~i' => '',
        ),
        'friendica' => array(
            '~^https?://([^/]+)/(?:profile|feed|channel)/([^/?#]+)~i' => '',
        ),
        'lemmy' => array(
            '~^https?://([^/]+)/u/([^/?#]+)~i' => '',
        ),
        'pixelfed' => array(
            '~^https?://([^/]+)/users/([^/?#]+)\.atom~i' => '',
            '~^https?://([^/]+)/@([^/?#]+)~i'            => '',
            '~^https?://([^/]+)/p/([^/?#]+)/~i'          => '',
            '~^https?://([^/]+)/([^/?#]+)~i'             => '',
        ),
        'forgejo' => array(
            '~^https?://([^/]+)/([^/?#]+)~i' => '',
        ),
        'bluesky' => array(
            '~^https?://[^/]+/profile/([^/?#]+)~i' => '',
        ),
    );

    $instance = '';
    $username = '';
    $type = '';

    if (isset($patterns[$platform])) {
        foreach ($patterns[$platform] as $pattern => $kind) {
            if (!preg_match($pattern, $value, $matches)) {
                continue;
            }

            if ('bluesky' === $platform) {
                // L'handle contiene gia' il proprio dominio, non c'e' istanza.
                return array(
                    'instance' => EG_SOCIAL_TIMELINE_BLUESKY_SERVICE,
                    'username' => $matches[1],
                    'type'     => '',
                );
            }

            $instance = eg_social_timeline_normalize_instance($matches[1]);
            $username = $matches[2];
            $type = $kind;
            break;
        }
    }

    // Handle federato, senza indirizzo completo.
    if ('' === $username && 'bluesky' !== $platform && preg_match('~^@?([^@/\s]+)@([^@/\s]+)$~', $value, $matches)) {
        $instance = eg_social_timeline_normalize_instance($matches[2]);
        $username = $matches[1];
    }

    if ('' === $username) {
        return false;
    }

    // Il nome porta la propria origine: quella vince sull'istanza dell'indirizzo.
    if (false !== strpos($username, '@')) {
        $parts = explode('@', $username, 2);
        $origin = eg_social_timeline_normalize_instance($parts[1]);

        if ('' !== $origin) {
            $username = $parts[0];
            $instance = $origin;
        }
    }

    if ('' === $instance) {
        return false;
    }

    return array(
        'instance' => $instance,
        'username' => $username,
        'type'     => $type,
    );
}

/**
 * Profili configurati, normalizzati e con i limiti per piattaforma.
 *
 * Migra alla lettura lo schema delle versioni precedenti: il campo unico
 * "mastodon_url" viene spezzato in istanza + utente, Diggita diventa una
 * normale istanza Lemmy e "peertube_handle" prende il nome delle altre.
 * L'opzione non viene riscritta: il database si allinea al primo salvataggio.
 *
 * @return array Profili per slug di piattaforma.
 */
function eg_social_timeline_profiles() {
    $options = get_option('eg_social_timeline_options');

    if (!is_array($options)) {
        $options = array();
    }

    $get = function ($key, $default = '') use ($options) {
        return (isset($options[$key]) && '' !== $options[$key]) ? $options[$key] : $default;
    };

    // Famiglia Mastodon: fino alla 1.8.1 un solo campo con l'URL del profilo.
    $mastodon_instance = eg_social_timeline_normalize_instance($get('mastodon_instance'));
    $mastodon_username = sanitize_text_field($get('mastodon_username'));

    if (('' === $mastodon_instance || '' === $mastodon_username) && '' !== $get('mastodon_url')) {
        $parsed = eg_social_timeline_parse_fediverse_profile($get('mastodon_url'));

        if ($parsed) {
            $mastodon_instance = $parsed['instance'];
            $mastodon_username = $parsed['username'];
        }
    }

    // Lemmy: Diggita era il dominio inchiodato nel fetcher.
    $lemmy_username = sanitize_text_field($get('lemmy_username', $get('diggita_username')));
    $lemmy_instance = eg_social_timeline_normalize_instance($get('lemmy_instance'));

    if ('' === $lemmy_instance && '' !== $lemmy_username) {
        $lemmy_instance = 'https://diggita.com';
    }

    $limit = function ($key, $default, $legacy_key = '') use ($options) {
        if (isset($options[$key])) {
            return max(0, intval($options[$key]));
        }

        if ('' !== $legacy_key && isset($options[$legacy_key])) {
            return max(0, intval($options[$legacy_key]));
        }

        return $default;
    };

    return array(
        'mastodon' => array(
            'instance' => $mastodon_instance,
            'username' => ltrim($mastodon_username, '@'),
            'limit'    => $limit('mastodon_limit', 5),
        ),
        'lemmy' => array(
            'instance' => $lemmy_instance,
            'username' => ltrim($lemmy_username, '@'),
            'limit'    => $limit('lemmy_limit', 5, 'diggita_limit'),
        ),
        'forgejo' => array(
            'instance' => eg_social_timeline_normalize_instance($get('forgejo_instance', 'https://gitea.com')),
            'username' => sanitize_text_field($get('forgejo_username')),
            'limit'    => $limit('forgejo_limit', 5),
        ),
        'peertube' => array(
            'instance' => eg_social_timeline_normalize_instance($get('peertube_instance')),
            'username' => ltrim(sanitize_text_field($get('peertube_username', $get('peertube_handle'))), '@'),
            'limit'    => $limit('peertube_limit', 5),
            'type'     => in_array($get('peertube_type', 'auto'), array('account', 'channel', 'auto'), true) ? $get('peertube_type', 'auto') : 'auto',
        ),
        'pixelfed' => array(
            'instance' => eg_social_timeline_normalize_instance($get('pixelfed_instance')),
            'username' => ltrim(sanitize_text_field($get('pixelfed_username')), '@'),
            'limit'    => $limit('pixelfed_limit', 5),
        ),
        'gotosocial' => array(
            'instance' => eg_social_timeline_normalize_instance($get('gotosocial_instance')),
            'username' => ltrim(sanitize_text_field($get('gotosocial_username')), '@'),
            'limit'    => $limit('gotosocial_limit', 5),
        ),
        'friendica' => array(
            'instance' => eg_social_timeline_normalize_instance($get('friendica_instance')),
            'username' => ltrim(sanitize_text_field($get('friendica_username')), '@'),
            'limit'    => $limit('friendica_limit', 5),
        ),
        'bluesky' => array(
            'instance' => EG_SOCIAL_TIMELINE_BLUESKY_SERVICE,
            'username' => ltrim(sanitize_text_field($get('bluesky_handle')), '@'),
            'limit'    => $limit('bluesky_limit', 5),
        ),
        'listenbrainz' => array(
            'instance' => eg_social_timeline_normalize_instance($get('listenbrainz_instance', EG_SOCIAL_TIMELINE_LISTENBRAINZ_API)),
            'username' => ltrim(sanitize_text_field($get('listenbrainz_username')), '@'),
            'limit'    => $limit('listenbrainz_limit', 5),
        ),
        'rss' => array(
            // Il feed ha un indirizzo completo, non una coppia istanza+utente.
            'instance' => '',
            'username' => '',
            'url'      => esc_url_raw($get('rss_url')),
            'label'    => sanitize_text_field($get('rss_label')),
            'limit'    => $limit('rss_limit', 5),
        ),
    );
}

/**
 * Indica se almeno una piattaforma e' configurata.
 *
 * @return bool
 */
function eg_social_timeline_has_profiles() {
    foreach (eg_social_timeline_profiles() as $slug => $profile) {
        if ('rss' === $slug) {
            if ('' !== $profile['url']) {
                return true;
            }

            continue;
        }

        if ('' === $profile['username']) {
            continue;
        }

        // Le piattaforme federate servono a poco senza l'istanza.
        if (in_array($slug, array('mastodon', 'gotosocial', 'friendica', 'lemmy', 'forgejo', 'peertube', 'pixelfed', 'listenbrainz'), true) && '' === $profile['instance']) {
            continue;
        }

        return true;
    }

    return false;
}

/**
 * Nome da mostrare per un'istanza Lemmy: la prima etichetta del dominio.
 *
 * diggita.com diventa "Diggita", lemmy.ml diventa "Lemmy", feddit.it
 * diventa "Feddit": l'istanza dice piu' del nome del software.
 *
 * @param string $instance URL dell'istanza.
 * @return string
 */
function eg_social_timeline_lemmy_label($instance) {
    $host = wp_parse_url($instance, PHP_URL_HOST);

    if (empty($host)) {
        return 'Lemmy';
    }

    $parts = explode('.', preg_replace('~^www\.~', '', strtolower($host)));

    // Con un sottodominio corto il nome vero e' quello dopo: sh.itjust.works
    // e' "Itjust", non "Sh".
    if (count($parts) >= 3 && strlen($parts[0]) <= 3) {
        array_shift($parts);
    }

    return ucfirst(isset($parts[0]) ? $parts[0] : 'lemmy');
}

/**
 * Software di un'istanza, letto da nodeinfo e messo in cache.
 *
 * L'href di nodeinfo arriva dal server remoto, quindi viene accettato solo
 * se punta allo stesso host dell'istanza: altrimenti sarebbe un SSRF servito
 * su richiesta.
 *
 * @param string $instance URL dell'istanza.
 * @return string Nome del software in minuscolo, stringa vuota se sconosciuto.
 */
function eg_social_timeline_detect_software($instance) {
    $instance = eg_social_timeline_normalize_instance($instance);

    if ('' === $instance) {
        return '';
    }

    $cache_key = 'eg_social_timeline_software_' . md5($instance);
    $cached = get_transient($cache_key);

    if (false !== $cached) {
        return $cached;
    }

    $software = '';
    $response = wp_remote_get($instance . '/.well-known/nodeinfo', array('timeout' => 10, 'sslverify' => true, 'reject_unsafe_urls' => true));

    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($data['links']) && is_array($data['links'])) {
            $document = end($data['links']);
            $href = isset($document['href']) ? $document['href'] : '';
            $same_host = $href && wp_parse_url($href, PHP_URL_HOST) === wp_parse_url($instance, PHP_URL_HOST);

            if ($same_host && eg_social_timeline_is_public_url($href)) {
                $document_response = wp_remote_get($href, array('timeout' => 10, 'sslverify' => true, 'reject_unsafe_urls' => true));

                if (!is_wp_error($document_response)) {
                    $document_data = json_decode(wp_remote_retrieve_body($document_response), true);

                    if (!empty($document_data['software']['name'])) {
                        $software = sanitize_key($document_data['software']['name']);
                    }
                }
            }
        }
    }

    // In cache anche il risultato vuoto, per non ripetere la scoperta a ogni
    // fetch quando l'istanza non espone nodeinfo.
    set_transient($cache_key, $software, $software ? WEEK_IN_SECONDS : HOUR_IN_SECONDS);

    return $software;
}

/**
 * Software della famiglia Mastodon che non espongono l'API senza login,
 * con il motivo da mostrare in Impostazioni.
 *
 * @return array Coppie software => motivo.
 */
function eg_social_timeline_unsupported_software() {
    return array(
        'gotosocial' => __('Its Mastodon-compatible API requires a login: use the GoToSocial fields instead, which read the public feed.', 'eg-social-timeline'),
        'friendica'  => __('Its Mastodon-compatible API requires a login: use the Friendica fields instead, which read the public feed.', 'eg-social-timeline'),
        'misskey'    => __('Misskey uses its own API, not the Mastodon one.', 'eg-social-timeline'),
        'sharkey'    => __('Sharkey uses its own API, not the Mastodon one.', 'eg-social-timeline'),
        'firefish'   => __('Firefish uses its own API, not the Mastodon one.', 'eg-social-timeline'),
        'iceshrimp'  => __('Iceshrimp uses its own API, not the Mastodon one.', 'eg-social-timeline'),
        'pixelfed'   => __('Pixelfed answers the account lookup but redirects the statuses endpoint to the login page.', 'eg-social-timeline'),
        'lemmy'      => __('This is a Lemmy instance: use the Lemmy fields instead.', 'eg-social-timeline'),
        'peertube'   => __('This is a PeerTube instance: use the PeerTube fields instead.', 'eg-social-timeline'),
    );
}

/**
 * Nome da mostrare per un software della famiglia Mastodon.
 *
 * @param string $software Nome del software da nodeinfo.
 * @return string
 */
function eg_social_timeline_fediverse_label($software) {
    $labels = array(
        'mastodon'   => 'Mastodon',
        'pleroma'    => 'Pleroma',
        'akkoma'     => 'Akkoma',
        'gotosocial' => 'GoToSocial',
        'friendica'  => 'Friendica',
    );

    return isset($labels[$software]) ? $labels[$software] : 'Mastodon';
}

// Campi dei profili

/**
 * Stampa un campo di testo della sezione profili.
 *
 * @param string $key         Chiave dell'opzione.
 * @param string $value       Valore corrente.
 * @param string $placeholder Esempio mostrato nel campo.
 * @param string $description Testo sotto il campo.
 * @param bool   $readonly    true per un valore non modificabile.
 */
function eg_social_timeline_profile_field($key, $value, $placeholder, $description, $readonly = false) {
    ?>
    <input type="text"
           id="eg_social_timeline_<?php echo esc_attr($key); ?>"
           name="eg_social_timeline_options[<?php echo esc_attr($key); ?>]"
           value="<?php echo esc_attr($value); ?>"
           placeholder="<?php echo esc_attr($placeholder); ?>"
           class="regular-text"
           <?php echo $readonly ? 'readonly' : ''; ?>>
    <p class="description"><?php echo esc_html($description); ?></p>
    <?php
}

function eg_social_timeline_mastodon_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['mastodon_instance']) ? $options['mastodon_instance'] : eg_social_timeline_profiles()['mastodon']['instance'];

    eg_social_timeline_profile_field(
        'mastodon_instance',
        $value,
        'https://mastodon.uno',
        __('The server where your account lives, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_mastodon_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['mastodon_username']) ? $options['mastodon_username'] : eg_social_timeline_profiles()['mastodon']['username'];

    eg_social_timeline_profile_field(
        'mastodon_username',
        $value,
        'emanuelegori',
        __('Username, or paste your full profile address.', 'eg-social-timeline')
    );
}

function eg_social_timeline_lemmy_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['lemmy_instance']) ? $options['lemmy_instance'] : eg_social_timeline_profiles()['lemmy']['instance'];

    eg_social_timeline_profile_field(
        'lemmy_instance',
        $value,
        'https://diggita.com',
        __('Your Lemmy instance, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_lemmy_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['lemmy_username']) ? $options['lemmy_username'] : eg_social_timeline_profiles()['lemmy']['username'];

    eg_social_timeline_profile_field(
        'lemmy_username',
        $value,
        'emanuelegori',
        __('Username, or paste your full profile address. Cards are labelled after the instance.', 'eg-social-timeline')
    );
}

function eg_social_timeline_forgejo_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['forgejo_instance']) ? $options['forgejo_instance'] : 'https://gitea.com';

    eg_social_timeline_profile_field(
        'forgejo_instance',
        $value,
        'https://gitea.com',
        __('The instance hosting your repositories, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_forgejo_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['forgejo_username']) ? $options['forgejo_username'] : '';

    eg_social_timeline_profile_field(
        'forgejo_username',
        $value,
        'emanuelegori',
        __('Commits come from the public repositories of this account.', 'eg-social-timeline')
    );
}

function eg_social_timeline_peertube_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['peertube_instance']) ? $options['peertube_instance'] : '';

    eg_social_timeline_profile_field(
        'peertube_instance',
        $value,
        'https://peertube.uno',
        __('The instance hosting your videos, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_peertube_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['peertube_username']) ? $options['peertube_username'] : eg_social_timeline_profiles()['peertube']['username'];

    eg_social_timeline_profile_field(
        'peertube_username',
        $value,
        'emanuelegori',
        __('Account or channel name, or paste the full address.', 'eg-social-timeline')
    );
}

function eg_social_timeline_pixelfed_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['pixelfed_instance']) ? $options['pixelfed_instance'] : eg_social_timeline_profiles()['pixelfed']['instance'];

    eg_social_timeline_profile_field(
        'pixelfed_instance',
        $value,
        'https://pixelfed.uno',
        __('The instance hosting your photos, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_pixelfed_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['pixelfed_username']) ? $options['pixelfed_username'] : eg_social_timeline_profiles()['pixelfed']['username'];

    eg_social_timeline_profile_field(
        'pixelfed_username',
        $value,
        'emanuelegori',
        __('Username, or paste your full profile address.', 'eg-social-timeline')
    );
}

function eg_social_timeline_gotosocial_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['gotosocial_instance']) ? $options['gotosocial_instance'] : eg_social_timeline_profiles()['gotosocial']['instance'];

    eg_social_timeline_profile_field(
        'gotosocial_instance',
        $value,
        'https://gts.example.org',
        __('The server hosting your account, HTTPS only: the address you log in to.', 'eg-social-timeline')
    );
}

function eg_social_timeline_gotosocial_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['gotosocial_username']) ? $options['gotosocial_username'] : eg_social_timeline_profiles()['gotosocial']['username'];

    eg_social_timeline_profile_field(
        'gotosocial_username',
        $value,
        'emanuelegori',
        __('Username, or paste your full profile address.', 'eg-social-timeline')
    );
}

function eg_social_timeline_friendica_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['friendica_instance']) ? $options['friendica_instance'] : eg_social_timeline_profiles()['friendica']['instance'];

    eg_social_timeline_profile_field(
        'friendica_instance',
        $value,
        'https://friendica.example.org',
        __('The server hosting your account, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_friendica_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['friendica_username']) ? $options['friendica_username'] : eg_social_timeline_profiles()['friendica']['username'];

    eg_social_timeline_profile_field(
        'friendica_username',
        $value,
        'emanuelegori',
        __('Nickname, or paste your full profile address.', 'eg-social-timeline')
    );
}

function eg_social_timeline_show_diagnostics_callback() {
    $options = get_option('eg_social_timeline_options');
    $show = !empty($options['show_diagnostics']);
    ?>
    <label>
        <input type="checkbox"
               id="eg_social_timeline_show_diagnostics"
               name="eg_social_timeline_options[show_diagnostics]"
               value="1"
               <?php checked($show, 1); ?>>
        <?php esc_html_e('Show the "Configured profiles" table on this page', 'eg-social-timeline'); ?>
    </label>
    <p class="description">
        <?php esc_html_e('Off by default, to keep the page short. A short warning appears anyway when a configured platform returns nothing, so a problem is never silent. Turn this on to see what the plugin sees for each platform and to use the "Verify profiles" button.', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_listenbrainz_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['listenbrainz_instance']) ? $options['listenbrainz_instance'] : EG_SOCIAL_TIMELINE_LISTENBRAINZ_API;

    eg_social_timeline_profile_field(
        'listenbrainz_instance',
        $value,
        EG_SOCIAL_TIMELINE_LISTENBRAINZ_API,
        __('Change it only if you run your own instance.', 'eg-social-timeline')
    );
}

function eg_social_timeline_listenbrainz_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['listenbrainz_username']) ? $options['listenbrainz_username'] : '';

    eg_social_timeline_profile_field(
        'listenbrainz_username',
        $value,
        'emanuelegori',
        __('Your ListenBrainz username.', 'eg-social-timeline')
    );
}

function eg_social_timeline_rss_url_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['rss_url']) ? $options['rss_url'] : '';

    eg_social_timeline_profile_field(
        'rss_url',
        $value,
        'https://example.com/feed/',
        __('Address of an RSS or Atom feed, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_rss_label_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['rss_label']) ? $options['rss_label'] : '';

    eg_social_timeline_profile_field(
        'rss_label',
        $value,
        __('My blog', 'eg-social-timeline'),
        __('Name shown on the cards. Left empty, the feed title is used.', 'eg-social-timeline')
    );
}

function eg_social_timeline_bluesky_instance_callback() {
    eg_social_timeline_profile_field(
        'bluesky_instance',
        EG_SOCIAL_TIMELINE_BLUESKY_SERVICE,
        '',
        __('The public endpoint, the same for everyone.', 'eg-social-timeline'),
        true
    );
}

function eg_social_timeline_bluesky_handle_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['bluesky_handle']) ? $options['bluesky_handle'] : '';

    eg_social_timeline_profile_field(
        'bluesky_handle',
        $value,
        'emanuele.bsky.social',
        __('Your full handle, without the leading @.', 'eg-social-timeline')
    );
}

function eg_social_timeline_bluesky_limit_callback() {
    $options = get_option('eg_social_timeline_options');
    $limit = isset($options['bluesky_limit']) ? $options['bluesky_limit'] : 10;
    ?>
    <input type="number"
           id="eg_social_timeline_bluesky_limit"
           name="eg_social_timeline_options[bluesky_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of Bluesky posts to fetch (0 = unlimited). Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_mastodon_limit_callback() {
    $options = get_option('eg_social_timeline_options');
    $limit = isset($options['mastodon_limit']) ? $options['mastodon_limit'] : 20;
    ?>
    <input type="number" 
           id="eg_social_timeline_mastodon_limit" 
           name="eg_social_timeline_options[mastodon_limit]" 
           value="<?php echo esc_attr($limit); ?>" 
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of Mastodon posts to fetch (0 = unlimited). Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_lemmy_limit_callback() {
    $limit = eg_social_timeline_profiles()['lemmy']['limit'];
    ?>
    <input type="number"
           id="eg_social_timeline_lemmy_limit"
           name="eg_social_timeline_options[lemmy_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of Lemmy posts included in the timeline. 0 = no limit. Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_forgejo_limit_callback() {
    $options = get_option('eg_social_timeline_options');
    $limit = isset($options['forgejo_limit']) ? $options['forgejo_limit'] : 5;
    ?>
    <input type="number" 
           id="eg_social_timeline_forgejo_limit" 
           name="eg_social_timeline_options[forgejo_limit]" 
           value="<?php echo esc_attr($limit); ?>" 
           min="0"
           max="50"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum total number of Forgejo commits to fetch (0 = unlimited). Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_peertube_limit_callback() {
    $options = get_option('eg_social_timeline_options');
    $limit = isset($options['peertube_limit']) ? $options['peertube_limit'] : 5;
    ?>
    <input type="number"
           id="eg_social_timeline_peertube_limit"
           name="eg_social_timeline_options[peertube_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of PeerTube videos to fetch (0 = unlimited). Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_pixelfed_limit_callback() {
    $limit = eg_social_timeline_profiles()['pixelfed']['limit'];
    ?>
    <input type="number"
           id="eg_social_timeline_pixelfed_limit"
           name="eg_social_timeline_options[pixelfed_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of Pixelfed posts included in the timeline. 0 = no limit. Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_gotosocial_limit_callback() {
    $limit = eg_social_timeline_profiles()['gotosocial']['limit'];
    ?>
    <input type="number"
           id="eg_social_timeline_gotosocial_limit"
           name="eg_social_timeline_options[gotosocial_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="20"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of GoToSocial posts included in the timeline. The feed carries the latest 20. 0 = no limit. Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_friendica_limit_callback() {
    $limit = eg_social_timeline_profiles()['friendica']['limit'];
    ?>
    <input type="number"
           id="eg_social_timeline_friendica_limit"
           name="eg_social_timeline_options[friendica_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of Friendica posts included in the timeline. 0 = no limit. Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_listenbrainz_limit_callback() {
    $limit = eg_social_timeline_profiles()['listenbrainz']['limit'];
    ?>
    <input type="number"
           id="eg_social_timeline_listenbrainz_limit"
           name="eg_social_timeline_options[listenbrainz_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of listens included in the timeline. 0 = no limit. Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_rss_limit_callback() {
    $limit = eg_social_timeline_profiles()['rss']['limit'];
    ?>
    <input type="number"
           id="eg_social_timeline_rss_limit"
           name="eg_social_timeline_options[rss_limit]"
           value="<?php echo esc_attr($limit); ?>"
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of feed items included in the timeline. 0 = no limit. Default: 5', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_post_limit_callback() {
    $options = get_option('eg_social_timeline_options');
    $limit = isset($options['post_limit']) ? $options['post_limit'] : 50;
    ?>
    <input type="number" 
           id="eg_social_timeline_post_limit" 
           name="eg_social_timeline_options[post_limit]" 
           value="<?php echo esc_attr($limit); ?>" 
           min="1"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of posts to show in the timeline (1–100). Default: 50', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_cache_duration_callback() {
    $options = get_option('eg_social_timeline_options');
    $duration = isset($options['cache_duration']) ? $options['cache_duration'] : 1800;
    ?>
    <select id="eg_social_timeline_cache_duration" 
            name="eg_social_timeline_options[cache_duration]">
        <option value="1800" <?php selected($duration, 1800); ?>>30 <?php esc_html_e('minutes', 'eg-social-timeline'); ?></option>
        <option value="3600" <?php selected($duration, 3600); ?>>1 <?php esc_html_e('hour', 'eg-social-timeline'); ?></option>
        <option value="7200" <?php selected($duration, 7200); ?>>2 <?php esc_html_e('hours', 'eg-social-timeline'); ?></option>
        <option value="14400" <?php selected($duration, 14400); ?>>4 <?php esc_html_e('hours', 'eg-social-timeline'); ?></option>
        <option value="28800" <?php selected($duration, 28800); ?>>8 <?php esc_html_e('hours', 'eg-social-timeline'); ?></option>
        <option value="86400" <?php selected($duration, 86400); ?>>24 <?php esc_html_e('hours', 'eg-social-timeline'); ?></option>
    </select>
    <p class="description">
        <?php esc_html_e('Feed cache duration. Longer cache = fewer requests to servers. Default: 30 minutes', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_appearance_section_callback() {
    echo '<p>' . esc_html__('Colors of the timeline. Choose a background for the timeline and one for the cards: text, borders and icons are derived from the color you pick, measuring its contrast, so they stay readable on any surface.', 'eg-social-timeline') . '</p>';
}

/**
 * Impostazioni aspetto normalizzate, con migrazione dal modello della 1.8.0.
 *
 * La 1.8.0 aveva uno schema colori globale piu' una coppia di colori
 * (chiaro/scuro) per superficie. Qui quel modello viene ridotto a quello
 * attuale — una scelta e un colore per superficie — leggendo le vecchie
 * chiavi senza riscrivere l'opzione: il DB si allinea al primo salvataggio.
 *
 * @return array Impostazioni con chiavi canvas_bg, canvas_bg_color, card_bg,
 *               card_bg_color, icon_style.
 */
function eg_social_timeline_appearance_settings() {
    $options = get_option('eg_social_timeline_options');

    if (!is_array($options)) {
        $options = array();
    }

    $legacy_scheme = isset($options['color_scheme']) ? $options['color_scheme'] : '';
    $legacy = in_array($legacy_scheme, array('light', 'dark', 'auto'), true);

    // Sfondo della timeline.
    $canvas = isset($options['canvas_bg']) ? $options['canvas_bg'] : 'none';
    $canvas_color = isset($options['canvas_bg_color']) ? sanitize_hex_color($options['canvas_bg_color']) : null;

    if ($legacy) {
        if (!$canvas_color) {
            $legacy_key = ('dark' === $legacy_scheme) ? 'canvas_bg_dark' : 'canvas_bg_light';
            $canvas_color = isset($options[$legacy_key]) ? sanitize_hex_color($options[$legacy_key]) : null;
        }

        // Il preset neutro cambiava con lo schema: con schema "auto" equivale
        // alla nuova voce "segue il browser".
        if ('neutral' === $canvas && 'auto' === $legacy_scheme) {
            $canvas = 'auto';
        }
    }

    if (!in_array($canvas, array('none', 'neutral', 'auto', 'custom'), true)) {
        $canvas = 'none';
    }

    // Sfondo delle schede.
    $card = isset($options['card_bg']) ? $options['card_bg'] : 'neutral';
    $card_color = isset($options['card_bg_color']) ? sanitize_hex_color($options['card_bg_color']) : null;

    if ($legacy) {
        if (!$card_color) {
            $legacy_key = ('dark' === $legacy_scheme) ? 'card_bg_dark' : 'card_bg_light';
            $card_color = isset($options[$legacy_key]) ? sanitize_hex_color($options[$legacy_key]) : null;
        }

        // "Automatico" seguiva lo schema globale: diventa "segue il browser"
        // oppure il colore fisso corrispondente allo schema scelto allora.
        if ('auto' === $card) {
            if ('auto' === $legacy_scheme) {
                $card = 'auto';
            } elseif ('dark' === $legacy_scheme) {
                $card = 'custom';
                $card_color = '#1f2937';
            } else {
                $card = 'neutral';
            }
        }
    }

    if (!in_array($card, array('neutral', 'none', 'auto', 'custom'), true)) {
        $card = 'neutral';
    }

    $icon_style = isset($options['icon_style']) ? $options['icon_style'] : 'brand';

    if (!in_array($icon_style, array('brand', 'mono'), true)) {
        $icon_style = 'brand';
    }

    $filters_style = isset($options['filters_style']) ? $options['filters_style'] : 'full';

    if (!in_array($filters_style, array('full', 'compact'), true)) {
        $filters_style = 'full';
    }

    $layout = isset($options['layout']) ? $options['layout'] : 'list';

    if (!in_array($layout, array('list', 'grid'), true)) {
        $layout = 'list';
    }

    return array(
        'canvas_bg'       => $canvas,
        'canvas_bg_color' => $canvas_color ? $canvas_color : '#f3f4f6',
        'card_bg'         => $card,
        'card_bg_color'   => $card_color ? $card_color : '#ffffff',
        'icon_style'      => $icon_style,
        'filters_style'   => $filters_style,
        'layout'          => $layout,
    );
}

/**
 * Stampa il color picker di un'opzione aspetto.
 *
 * @param string $key   Chiave dell'opzione.
 * @param string $value Colore corrente.
 */
function eg_social_timeline_color_field($key, $value) {
    ?>
    <p>
        <label for="eg_social_timeline_<?php echo esc_attr($key); ?>">
            <?php esc_html_e('Custom color:', 'eg-social-timeline'); ?>
        </label>
        <input type="color"
               id="eg_social_timeline_<?php echo esc_attr($key); ?>"
               name="eg_social_timeline_options[<?php echo esc_attr($key); ?>]"
               value="<?php echo esc_attr($value); ?>">
    </p>
    <?php
}

/**
 * Stampa il select di un'opzione aspetto.
 *
 * @param string $key     Chiave dell'opzione.
 * @param string $current Valore corrente.
 * @param array  $choices Coppie valore => etichetta.
 */
function eg_social_timeline_select_field($key, $current, $choices) {
    ?>
    <select id="eg_social_timeline_<?php echo esc_attr($key); ?>"
            name="eg_social_timeline_options[<?php echo esc_attr($key); ?>]">
        <?php foreach ($choices as $value => $label): ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($current, $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

/**
 * Avviso se il colore scelto non permette testo leggibile.
 *
 * Con un colore di mezzo (un viola o un grigio medio) nessuna delle due
 * palette di primo piano raggiunge il minimo WCAG AA: meglio dirlo invece di
 * lasciare all'utente una scheda dal testo illeggibile.
 *
 * @param string $hex Colore scelto.
 */
function eg_social_timeline_contrast_notice($hex) {
    $best = max(
        eg_social_timeline_contrast_ratio($hex, '#374151'),
        eg_social_timeline_contrast_ratio($hex, '#d1d5db')
    );

    if ($best >= 4.5) {
        return;
    }
    ?>
    <p class="description" style="color: #b32d2e;">
        <?php
        printf(
            /* translators: %s: rapporto di contrasto raggiunto, es. 2.9 */
            esc_html__('Careful: with this color the text reaches a contrast ratio of only %s:1, below the 4.5:1 required by WCAG AA. Pick a clearly lighter or darker color.', 'eg-social-timeline'),
            esc_html(number_format_i18n($best, 1))
        );
        ?>
    </p>
    <?php
}

function eg_social_timeline_canvas_bg_callback() {
    $settings = eg_social_timeline_appearance_settings();

    eg_social_timeline_select_field('canvas_bg', $settings['canvas_bg'], array(
        'none'    => __('Transparent (theme background)', 'eg-social-timeline'),
        'neutral' => __('Neutral preset', 'eg-social-timeline'),
        'auto'    => __('Follow the visitor browser', 'eg-social-timeline'),
        'custom'  => __('Custom color', 'eg-social-timeline'),
    ));
    ?>
    <p class="description">
        <?php esc_html_e('Background behind the cards. Set it apart from the card background to make the cards stand out. "Follow the visitor browser" uses a light grey or a very dark grey according to the prefers-color-scheme setting. Default: Transparent', 'eg-social-timeline'); ?>
    </p>
    <?php
    eg_social_timeline_color_field('canvas_bg_color', $settings['canvas_bg_color']);
    ?>
    <p class="description">
        <?php esc_html_e('The color above applies only with "Custom color".', 'eg-social-timeline'); ?>
    </p>
    <?php
    if ('custom' === $settings['canvas_bg']) {
        eg_social_timeline_contrast_notice($settings['canvas_bg_color']);
    }
}

function eg_social_timeline_card_bg_callback() {
    $settings = eg_social_timeline_appearance_settings();

    eg_social_timeline_select_field('card_bg', $settings['card_bg'], array(
        'neutral' => __('Neutral preset (white)', 'eg-social-timeline'),
        'none'    => __('Transparent', 'eg-social-timeline'),
        'auto'    => __('Follow the visitor browser', 'eg-social-timeline'),
        'custom'  => __('Custom color', 'eg-social-timeline'),
    ));
    ?>
    <p class="description">
        <?php esc_html_e('Background of each post card. "Transparent" drops the card surface and its shadow, leaving only the border. Default: Neutral preset', 'eg-social-timeline'); ?>
    </p>
    <?php
    eg_social_timeline_color_field('card_bg_color', $settings['card_bg_color']);
    ?>
    <p class="description">
        <?php esc_html_e('The color above applies only with "Custom color". Pick a dark one and the post text, the stats and the icons switch to their light variants on their own.', 'eg-social-timeline'); ?>
    </p>
    <?php
    if ('custom' === $settings['card_bg']) {
        eg_social_timeline_contrast_notice($settings['card_bg_color']);
    }
}

function eg_social_timeline_layout_callback() {
    $settings = eg_social_timeline_appearance_settings();

    eg_social_timeline_select_field('layout', $settings['layout'], array(
        'list' => __('List: one card under the other', 'eg-social-timeline'),
        'grid' => __('Grid: cards side by side', 'eg-social-timeline'),
    ));
    ?>
    <p class="description">
        <?php
        echo wp_kses(
            sprintf(
                /* translators: %s: esempio di shortcode con l'attributo layout */
                __('The grid fits as many columns as the space allows, and one on phones; images are cropped to 16:9 and shorter texts read better. A single page can override this setting: %s. Default: List', 'eg-social-timeline'),
                '<code>[eg_social_timeline layout="grid"]</code>'
            ),
            array('code' => array())
        );
        ?>
    </p>
    <?php
}

function eg_social_timeline_filters_style_callback() {
    $settings = eg_social_timeline_appearance_settings();

    eg_social_timeline_select_field('filters_style', $settings['filters_style'], array(
        'full'    => __('Full: icon, name and count', 'eg-social-timeline'),
        'compact' => __('Compact: icons only', 'eg-social-timeline'),
    ));
    ?>
    <p class="description">
        <?php esc_html_e('The compact bar keeps only the icons: a platform included is in colour, one filtered out turns grey. Name and count move to the tooltip, and stay readable by screen readers. Default: Full', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_icon_style_callback() {
    $settings = eg_social_timeline_appearance_settings();

    eg_social_timeline_select_field('icon_style', $settings['icon_style'], array(
        'brand' => __('Platform colors', 'eg-social-timeline'),
        'mono'  => __('Single color (same as the post text)', 'eg-social-timeline'),
    ));
    ?>
    <p class="description">
        <?php esc_html_e('The SVG icons are monochrome outlines and carry no color of their own: the stylesheet paints them. Platform colors use the official color of each platform, lightened when the surface is dark. A single color follows the post text, so the icons turn white on a dark card. Default: Platform colors', 'eg-social-timeline'); ?>
    </p>
    <?php
}

// Sanitization
function eg_social_timeline_sanitize_options($input) {
    $output = array();
    
    // Profili: istanza + utente per ogni piattaforma federata. Un indirizzo
    // completo incollato in uno dei due campi viene riconosciuto e distribuito
    // su entrambi, tipo del profilo compreso.
    $platform_defaults = array(
        'mastodon' => '',
        'lemmy'    => '',
        'forgejo'  => 'https://gitea.com',
        'peertube' => '',
        'pixelfed' => '',
        'gotosocial' => '',
        'friendica' => '',
        'listenbrainz' => EG_SOCIAL_TIMELINE_LISTENBRAINZ_API,
    );

    $extracted = array();

    foreach ($platform_defaults as $platform => $fallback) {
        $raw_username = isset($input[$platform . '_username']) ? trim((string) $input[$platform . '_username']) : '';
        $raw_instance = isset($input[$platform . '_instance']) ? trim((string) $input[$platform . '_instance']) : '';

        foreach (array($raw_username, $raw_instance) as $candidate) {
            if ('' === $candidate) {
                continue;
            }

            $parsed = eg_social_timeline_extract_profile($platform, $candidate);

            if ($parsed) {
                $extracted[$platform] = $parsed;
                break;
            }
        }

        if (isset($extracted[$platform])) {
            $output[$platform . '_instance'] = $extracted[$platform]['instance'];
            $output[$platform . '_username'] = sanitize_text_field($extracted[$platform]['username']);
            continue;
        }

        $instance = eg_social_timeline_normalize_instance($raw_instance);

        if ('' === $instance && '' !== $raw_instance) {
            add_settings_error(
                'eg_social_timeline_options',
                'invalid_instance_' . $platform,
                '<strong>' . __('Error:', 'eg-social-timeline') . '</strong> ' .
                sprintf(
                    /* translators: %s: valore inserito per l'istanza */
                    __('"%s" is not a usable instance address. Use an HTTPS address of a public server.', 'eg-social-timeline'),
                    esc_html($raw_instance)
                ),
                'error'
            );
        }

        $output[$platform . '_instance'] = ('' !== $instance) ? $instance : $fallback;
        $output[$platform . '_username'] = sanitize_text_field(ltrim($raw_username, '@'));
    }

    // Feed esterno: un indirizzo completo, con la sua etichetta.
    $raw_feed = isset($input['rss_url']) ? trim((string) $input['rss_url']) : '';
    $feed_url = ('' !== $raw_feed) ? esc_url_raw($raw_feed) : '';

    if ('' !== $feed_url && !eg_social_timeline_is_public_url($feed_url)) {
        add_settings_error(
            'eg_social_timeline_options',
            'invalid_feed_url',
            '<strong>' . __('Error:', 'eg-social-timeline') . '</strong> ' .
            sprintf(
                /* translators: %s: indirizzo inserito per il feed */
                __('"%s" is not a usable feed address. Use an HTTPS address of a public server.', 'eg-social-timeline'),
                esc_html($raw_feed)
            ),
            'error'
        );

        $feed_url = '';
    }

    $output['rss_url'] = $feed_url;
    $output['rss_label'] = isset($input['rss_label']) ? sanitize_text_field($input['rss_label']) : '';

    // Bluesky non ha istanza: l'handle porta il proprio dominio.
    $raw_bluesky = isset($input['bluesky_handle']) ? trim((string) $input['bluesky_handle']) : '';
    $parsed_bluesky = eg_social_timeline_extract_profile('bluesky', $raw_bluesky);
    $output['bluesky_handle'] = sanitize_text_field(ltrim($parsed_bluesky ? $parsed_bluesky['username'] : $raw_bluesky, '@'));

    // GoToSocial: il dominio dell'account puo' non essere il server, e il feed
    // esiste solo sul server. Una richiesta al salvataggio, non a ogni recupero.
    if ('' !== $output['gotosocial_instance'] && '' !== $output['gotosocial_username']) {
        $server = eg_social_timeline_resolve_gotosocial_host($output['gotosocial_instance'], $output['gotosocial_username']);

        if ('' !== $server && $server !== $output['gotosocial_instance']) {
            add_settings_error(
                'eg_social_timeline_options',
                'gotosocial_server',
                sprintf(
                    /* translators: 1: dominio dell'account, 2: server che lo ospita */
                    __('GoToSocial: the account on %1$s is hosted on %2$s, so the instance address now points there.', 'eg-social-timeline'),
                    esc_html(wp_parse_url($output['gotosocial_instance'], PHP_URL_HOST)),
                    esc_html(wp_parse_url($server, PHP_URL_HOST))
                ),
                'info'
            );

            $output['gotosocial_instance'] = $server;
        }
    }

    // Tipo del profilo PeerTube: noto se l'indirizzo lo diceva, altrimenti si
    // scopre al primo recupero. Se il profilo non cambia si conserva quello
    // gia' trovato, per non ripetere la scoperta a ogni salvataggio.
    $output['peertube_type'] = (isset($extracted['peertube']['type']) && in_array($extracted['peertube']['type'], array('account', 'channel'), true))
        ? $extracted['peertube']['type']
        : 'auto';

    if ('auto' === $output['peertube_type']) {
        $previous = get_option('eg_social_timeline_options');

        if (
            is_array($previous)
            && !empty($previous['peertube_type'])
            && isset($previous['peertube_username'], $previous['peertube_instance'])
            && $previous['peertube_username'] === $output['peertube_username']
            && $previous['peertube_instance'] === $output['peertube_instance']
        ) {
            $output['peertube_type'] = $previous['peertube_type'];
        }
    }

    $configured = false;

    foreach (array_keys($platform_defaults) as $platform) {
        if ('' !== $output[$platform . '_username'] && '' !== $output[$platform . '_instance']) {
            $configured = true;
        }
    }

    if ('' !== $output['bluesky_handle'] || '' !== $output['rss_url']) {
        $configured = true;
    }

    if (!$configured) {
        add_settings_error(
            'eg_social_timeline_options',
            'no_profiles',
            '<strong>' . __('Error:', 'eg-social-timeline') . '</strong> ' .
            __('You must configure at least one profile: a username together with its instance URL, or a Bluesky handle.', 'eg-social-timeline'),
            'error'
        );

        $old_options = get_option('eg_social_timeline_options');
        return $old_options ? $old_options : array();
    }

    // Se il server della famiglia Mastodon non espone l'API pubblica, dirlo
    // subito: altrimenti la piattaforma sparirebbe dalla timeline in silenzio.
    if ('' !== $output['mastodon_instance'] && '' !== $output['mastodon_username']) {
        $software = eg_social_timeline_detect_software($output['mastodon_instance']);
        $unsupported = eg_social_timeline_unsupported_software();

        if (isset($unsupported[$software])) {
            add_settings_error(
                'eg_social_timeline_options',
                'unsupported_software',
                '<strong>' . __('Warning:', 'eg-social-timeline') . '</strong> ' .
                sprintf(
                    /* translators: 1: nome del software rilevato, 2: motivo per cui non e' supportato */
                    __('%1$s detected on that instance. %2$s', 'eg-social-timeline'),
                    esc_html(in_array($software, array('gotosocial', 'friendica'), true) ? eg_social_timeline_fediverse_label($software) : ucfirst($software)),
                    esc_html($unsupported[$software])
                ),
                'warning'
            );
        }
    }

    $limit = isset($input['post_limit']) ? intval($input['post_limit']) : 50;
    $output['post_limit'] = max(1, min(100, $limit));
    
    $mastodon_limit = isset($input['mastodon_limit']) ? intval($input['mastodon_limit']) : 20;
    $output['mastodon_limit'] = max(0, min(100, $mastodon_limit));
    
    $lemmy_limit = isset($input['lemmy_limit']) ? intval($input['lemmy_limit']) : 10;
    $output['lemmy_limit'] = max(0, min(100, $lemmy_limit));
    
    $forgejo_limit = isset($input['forgejo_limit']) ? intval($input['forgejo_limit']) : 5;
    $output['forgejo_limit'] = max(0, min(50, $forgejo_limit));

    $bluesky_limit = isset($input['bluesky_limit']) ? intval($input['bluesky_limit']) : 10;
    $output['bluesky_limit'] = max(0, min(100, $bluesky_limit));

    $peertube_limit = isset($input['peertube_limit']) ? intval($input['peertube_limit']) : 5;
    $output['peertube_limit'] = max(0, min(100, $peertube_limit));

    $pixelfed_limit = isset($input['pixelfed_limit']) ? intval($input['pixelfed_limit']) : 10;
    $output['pixelfed_limit'] = max(0, min(100, $pixelfed_limit));

    // Il feed di GoToSocial porta gli ultimi 20 post: oltre non c'e' nulla.
    $gotosocial_limit = isset($input['gotosocial_limit']) ? intval($input['gotosocial_limit']) : 5;
    $output['gotosocial_limit'] = max(0, min(20, $gotosocial_limit));

    $friendica_limit = isset($input['friendica_limit']) ? intval($input['friendica_limit']) : 5;
    $output['friendica_limit'] = max(0, min(100, $friendica_limit));

    $listenbrainz_limit = isset($input['listenbrainz_limit']) ? intval($input['listenbrainz_limit']) : 10;
    $output['listenbrainz_limit'] = max(0, min(100, $listenbrainz_limit));

    $rss_limit = isset($input['rss_limit']) ? intval($input['rss_limit']) : 10;
    $output['rss_limit'] = max(0, min(100, $rss_limit));

    $duration = isset($input['cache_duration']) ? intval($input['cache_duration']) : 1800;
    $output['cache_duration'] = in_array($duration, array(1800, 3600, 7200, 14400, 28800, 86400)) ? $duration : 3600;
    
    // Cosa mostrare e lunghezza del testo, una scelta per piattaforma. Le
    // vecchie chiavi globali non vengono riscritte: restano nel database come
    // ripiego per chi aggiorna e non ha ancora salvato.
    foreach (eg_social_timeline_platforms() as $slug => $platform) {
        foreach ((array) $platform['display'] as $key) {
            $field = $slug . '_show_' . $key;
            $output[$field] = isset($input[$field]) ? true : false;
        }

        $truncate_field = $slug . '_truncate';
        $truncate = isset($input[$truncate_field]) ? intval($input[$truncate_field]) : 300;
        $output[$truncate_field] = (0 === $truncate) ? 0 : max(50, min(600, $truncate));
    }


    // Aspetto
    $canvas_bg = isset($input['canvas_bg']) ? sanitize_key($input['canvas_bg']) : 'none';
    $output['canvas_bg'] = in_array($canvas_bg, array('none', 'neutral', 'auto', 'custom'), true) ? $canvas_bg : 'none';

    $card_bg = isset($input['card_bg']) ? sanitize_key($input['card_bg']) : 'neutral';
    $output['card_bg'] = in_array($card_bg, array('neutral', 'none', 'auto', 'custom'), true) ? $card_bg : 'neutral';

    $icon_style = isset($input['icon_style']) ? sanitize_key($input['icon_style']) : 'brand';
    $output['icon_style'] = in_array($icon_style, array('brand', 'mono'), true) ? $icon_style : 'brand';

    $filters_style = isset($input['filters_style']) ? sanitize_key($input['filters_style']) : 'full';
    $output['filters_style'] = in_array($filters_style, array('full', 'compact'), true) ? $filters_style : 'full';

    $layout = isset($input['layout']) ? sanitize_key($input['layout']) : 'list';
    $output['layout'] = in_array($layout, array('list', 'grid'), true) ? $layout : 'list';

    $output['show_diagnostics'] = isset($input['show_diagnostics']) ? true : false;

    $colors = array(
        'canvas_bg_color' => '#f3f4f6',
        'card_bg_color'   => '#ffffff',
    );

    foreach ($colors as $key => $fallback) {
        $color = isset($input[$key]) ? sanitize_hex_color($input[$key]) : '';
        $output[$key] = $color ? $color : $fallback;
    }

    delete_transient('eg_social_timeline_cache');
    
    add_settings_error(
        'eg_social_timeline_options',
        'settings_updated',
        __('Settings saved successfully! Cache cleared.', 'eg-social-timeline'),
        'success'
    );
    
    return $output;
}

/**
 * Registro degli esiti dei fetcher nella richiesta corrente.
 *
 * Serve a non far scomparire una piattaforma in silenzio: i motivi raccolti
 * qui finiscono nell'opzione di stato e vengono mostrati in Impostazioni.
 *
 * @param string|null $platform Slug della piattaforma, null per leggere.
 * @param string|null $message  Motivo del fallimento.
 * @return array Esiti raccolti.
 */
function eg_social_timeline_record_issue($platform = null, $message = null) {
    static $issues = array();

    if (null === $platform) {
        return $issues;
    }

    $issues[$platform] = $message;

    return $issues;
}

// Manutenzione al cambio di versione
add_action('plugins_loaded', 'eg_social_timeline_maybe_upgrade');

/**
 * Svuota la cache quando la versione del plugin cambia.
 *
 * Nella 1.9.0 lo slug "diggita" diventa "lemmy": i post rimasti in cache
 * avrebbero uno slug senza regole CSS e, con i filtri che partono da
 * display:none, resterebbero invisibili fino alla scadenza del transient.
 */
function eg_social_timeline_maybe_upgrade() {
    $stored = get_option('eg_social_timeline_version');

    if (EG_SOCIAL_TIMELINE_VERSION === $stored) {
        return;
    }

    // La cache va rifatta (gli slug possono essere cambiati), ma l'esito
    // dell'ultimo recupero si conserva: porta la sua data, e cancellarlo
    // lascerebbe la tabella muta proprio quando si controlla l'aggiornamento.
    delete_transient('eg_social_timeline_cache');
    update_option('eg_social_timeline_version', EG_SOCIAL_TIMELINE_VERSION, false);
}

// Admin notices
add_action('admin_notices', 'eg_social_timeline_admin_notice');

function eg_social_timeline_admin_notice() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'settings_page_eg-social-timeline') {
        return;
    }
    
    $options = get_option('eg_social_timeline_options');
    
    if (!eg_social_timeline_has_profiles()) {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e('EG Social Timeline requires configuration!', 'eg-social-timeline'); ?></strong><br>
                <?php esc_html_e('The plugin is active but no social profile has been configured.', 'eg-social-timeline'); ?><br>
                <a href="<?php echo esc_url(admin_url('options-general.php?page=eg-social-timeline')); ?>" class="button button-primary" style="margin-top: 10px;">
                    <?php esc_html_e('Configure now', 'eg-social-timeline'); ?>
                </a>
            </p>
        </div>
        <?php
    }
}

// Settings page
/**
 * Stato di un riquadro di piattaforma: cosa scrivere accanto al titolo e se
 * aprirlo.
 *
 * I riquadri partono chiusi; si apre da solo soltanto quello che chiede
 * attenzione: profilo a meta', errore al salvataggio, verifica non riuscita
 * o ultimo recupero senza contenuti. Un recupero mai avvenuto non conta.
 *
 * @param string $slug        Slug della piattaforma.
 * @param array  $rows        Esito di eg_social_timeline_profile_rows().
 * @param array  $errors      Codici degli errori di salvataggio correnti.
 * @return array array con open (bool), kind (empty|ok|warn) e text.
 */
function eg_social_timeline_box_state($slug, $rows, $errors) {
    $profiles = eg_social_timeline_profiles();
    $status = get_option('eg_social_timeline_status');
    $diagnostics = get_option('eg_social_timeline_diagnostics');

    if (!isset($rows[$slug])) {
        return array('open' => false, 'kind' => 'empty', 'text' => __('Not configured', 'eg-social-timeline'));
    }

    if ('' !== $rows[$slug]) {
        return array('open' => true, 'kind' => 'warn', 'text' => __('Incomplete profile', 'eg-social-timeline'));
    }

    $own_errors = array('invalid_instance_' . $slug);

    if ('mastodon' === $slug) {
        $own_errors[] = 'unsupported_software';
    }

    if ('rss' === $slug) {
        $own_errors[] = 'invalid_feed_url';
    }

    if (array_intersect($own_errors, $errors)) {
        return array('open' => true, 'kind' => 'warn', 'text' => __('Check the message at the top of the page', 'eg-social-timeline'));
    }

    if (isset($diagnostics['platforms'][$slug]['ok']) && !$diagnostics['platforms'][$slug]['ok']) {
        return array('open' => true, 'kind' => 'warn', 'text' => __('The last check found a problem', 'eg-social-timeline'));
    }

    if (isset($status['platforms'][$slug]) && empty($status['platforms'][$slug]['count'])) {
        return array('open' => true, 'kind' => 'warn', 'text' => __('Nothing on the last refresh', 'eg-social-timeline'));
    }

    $profile = $profiles[$slug];

    if ('rss' === $slug) {
        $text = wp_parse_url($profile['url'], PHP_URL_HOST) . ('' !== $profile['label'] ? ' · ' . $profile['label'] : '');
    } elseif ('bluesky' === $slug) {
        $text = $profile['username'];
    } else {
        $text = wp_parse_url($profile['instance'], PHP_URL_HOST) . ' · ' . $profile['username'];
    }

    return array('open' => false, 'kind' => 'ok', 'text' => $text);
}

/**
 * Stampa le sezioni della pagina, come do_settings_sections(), ma con i
 * riquadri delle piattaforme dentro un <details>: si aprono e si chiudono
 * senza JavaScript, e i campi di un riquadro chiuso vengono inviati lo stesso.
 */
function eg_social_timeline_do_settings_sections() {
    global $wp_settings_sections, $wp_settings_fields;

    $page = 'eg-social-timeline';

    if (empty($wp_settings_sections[$page])) {
        return;
    }

    $platforms = eg_social_timeline_platforms();
    $rows = eg_social_timeline_profile_rows();
    $errors = wp_list_pluck(get_settings_errors('eg_social_timeline_options'), 'code');

    foreach ((array) $wp_settings_sections[$page] as $section) {
        $slug = str_replace(array('eg_social_timeline_', '_section'), '', $section['id']);
        $has_fields = !empty($wp_settings_fields[$page][$section['id']]);

        if (isset($platforms[$slug])) {
            $state = eg_social_timeline_box_state($slug, $rows, $errors);
            ?>
            <details class="egst-box egst-box-<?php echo esc_attr($state['kind']); ?>"<?php echo $state['open'] ? ' open' : ''; ?>>
                <summary>
                    <?php echo $section['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- titolo composto in register_settings da icona sanitizzata e testo escapato ?>
                    <span class="egst-box-state"><?php echo esc_html(('ok' === $state['kind'] ? '✓ ' : ('warn' === $state['kind'] ? '⚠ ' : '')) . $state['text']); ?></span>
                </summary>
                <?php
                if ($section['callback']) {
                    call_user_func($section['callback'], $section);
                }

                if ($has_fields) {
                    echo '<table class="form-table" role="presentation">';
                    do_settings_fields($page, $section['id']);
                    echo '</table>';
                }
                ?>
            </details>
            <?php
            continue;
        }

        if ($section['title']) {
            echo '<h2>' . $section['title'] . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- come do_settings_sections(): titoli registrati dal plugin, gia' escapati
        }

        if ($section['callback']) {
            call_user_func($section['callback'], $section);
        }

        if ($has_fields) {
            echo '<table class="form-table" role="presentation">';
            do_settings_fields($page, $section['id']);
            echo '</table>';
        }
    }
}

function eg_social_timeline_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php eg_social_timeline_render_diagnostics(); ?>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('eg_social_timeline_settings');
            eg_social_timeline_do_settings_sections();
            submit_button(__('Save Settings', 'eg-social-timeline'));
            ?>
        </form>
        
        <hr>
        
        <h2><?php esc_html_e('Usage', 'eg-social-timeline'); ?></h2>
        <p><?php esc_html_e('Once configured, you can insert the timeline in your posts using:', 'eg-social-timeline'); ?></p>
        
        <h3><?php esc_html_e('Shortcode', 'eg-social-timeline'); ?></h3>
        <p><?php esc_html_e('Insert in the article content:', 'eg-social-timeline'); ?></p>
        <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #6364FF;"><code>[eg_social_timeline]</code></pre>
        
        <p><?php esc_html_e('Optional: limit the number of posts:', 'eg-social-timeline'); ?></p>
        <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #6364FF;"><code>[eg_social_timeline limit="20"]</code></pre>
        
        <h3><?php esc_html_e('Flush Cache Manually', 'eg-social-timeline'); ?></h3>
        <p><?php esc_html_e('To force an immediate feed refresh:', 'eg-social-timeline'); ?></p>
        <form method="post" style="display: inline;">
            <?php wp_nonce_field('eg_social_timeline_clear_cache', 'eg_social_timeline_nonce'); ?>
            <input type="hidden" name="eg_social_timeline_clear_cache" value="1">
            <button type="submit" class="button"><?php esc_html_e('Flush Cache Now', 'eg-social-timeline'); ?></button>
        </form>
    </div>
    <?php
}

// Footer del pannello impostazioni
add_action('current_screen', 'eg_social_timeline_admin_footer_hooks');

/**
 * Aggancia il footer agli slot nativi di WordPress, e solo sulla pagina del
 * plugin: a sinistra chi lo sviluppa e i link del progetto, a destra la
 * versione, dove l'amministratore e' abituato a cercarla.
 *
 * `update_footer` va a priorita' 20 perche' a 10 c'e' gia' core_update_footer.
 *
 * @param WP_Screen $screen Schermata corrente.
 * @return void
 */
function eg_social_timeline_admin_footer_hooks($screen) {
    if (!isset($screen->id) || 'settings_page_eg-social-timeline' !== $screen->id) {
        return;
    }

    add_filter('admin_footer_text', 'eg_social_timeline_admin_footer_text');
    add_filter('update_footer', 'eg_social_timeline_admin_footer_version', 20);
}

/**
 * Indirizzi del progetto, nella lingua di chi guarda.
 *
 * Sono stringhe traducibili invece che costanti: chi traduce il plugin nella
 * propria lingua puo' puntarle alle pagine localizzate, e chi non ne ha lascia
 * il msgid e resta sull'inglese. E' il modo in cui WordPress stesso gestisce i
 * link alla propria documentazione, e non tratta nessuna lingua come caso
 * speciale: niente controlli sul locale dentro al codice.
 *
 * In amministrazione conta la lingua scelta dall'utente, non quella del sito:
 * un pannello in italiano mostra le pagine italiane, tutti gli altri l'inglese.
 *
 * @return array<string, string> Indirizzi per chiave.
 */
function eg_social_timeline_project_urls() {
    return array(
        /* translators: indirizzo del sito dell'autore. Tradurre con la versione localizzata della home, se esiste; altrimenti lasciare invariato. */
        'author' => __('https://emanuelegori.uno/en/', 'eg-social-timeline'),
        /* translators: indirizzo della pagina del progetto. Tradurre con la versione localizzata, se esiste; altrimenti lasciare invariato. */
        'docs'   => __('https://emanuelegori.uno/en/plugins/eg-social-timeline/', 'eg-social-timeline'),
        /* translators: indirizzo della pagina per sostenere il progetto. Tradurre con la versione localizzata, se esiste; altrimenti lasciare invariato. */
        'donate' => __('https://emanuelegori.uno/en/donate/', 'eg-social-timeline'),
        'repo'   => EG_SOCIAL_TIMELINE_REPO_URL,
    );
}

/**
 * Lato sinistro del footer: autore e link del progetto.
 *
 * Il testo passa da wp_kses invece che da esc_html perche' contiene i link:
 * cosi' una traduzione che si portasse dietro altro HTML non lo vedrebbe
 * comunque arrivare in pagina.
 *
 * @param string $text Testo predefinito di WordPress.
 * @return string Footer del plugin.
 */
function eg_social_timeline_admin_footer_text($text) {
    $urls = eg_social_timeline_project_urls();

    $author = sprintf(
        '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
        esc_url($urls['author']),
        'Emanuele Gori'
    );

    $links = array(
        sprintf(
            '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
            esc_url($urls['docs']),
            esc_html__('Documentation', 'eg-social-timeline')
        ),
        sprintf(
            '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
            esc_url($urls['repo']),
            esc_html__('Repository', 'eg-social-timeline')
        ),
        sprintf(
            '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
            esc_url($urls['donate']),
            esc_html__('Support the project', 'eg-social-timeline')
        ),
    );

    $credit = sprintf(
        /* translators: %s: link HTML al sito dello sviluppatore */
        __('Developed with ❤️ and maintained by %s', 'eg-social-timeline'),
        $author
    );

    return wp_kses(
        $credit . ' &middot; ' . implode(' &middot; ', $links),
        array(
            'a' => array(
                'href'   => array(),
                'target' => array(),
                'rel'    => array(),
            ),
        )
    );
}

/**
 * Lato destro del footer: versione del plugin e licenza, al posto della
 * versione di WordPress.
 *
 * @param string $text Testo predefinito di WordPress.
 * @return string Versione e licenza.
 */
function eg_social_timeline_admin_footer_version($text) {
    $version = sprintf(
        /* translators: %s: numero versione plugin */
        esc_html__('EG Social Timeline v%s', 'eg-social-timeline'),
        esc_html(EG_SOCIAL_TIMELINE_VERSION)
    );

    return $version . ' &middot; ' . esc_html__('License GPL-2.0-or-later', 'eg-social-timeline');
}

// Stile del pannello impostazioni
add_action('admin_enqueue_scripts', 'eg_social_timeline_admin_styles');

/**
 * Righe alternate nelle tabelle delle impostazioni.
 *
 * La pagina ha una trentina di campi in colonna e WordPress non alterna gli
 * sfondi: con le righe tutte uguali si perde il filo fra etichetta e campo.
 *
 * @param string $hook Identificatore della schermata corrente.
 */
function eg_social_timeline_admin_styles($hook) {
    if ('settings_page_eg-social-timeline' !== $hook) {
        return;
    }

    $icons = '';

    foreach (array(
        'mastodon' => '#6364FF',
        'bluesky' => '#0085FF',
        'lemmy' => '#1A1A1A',
        'pixelfed' => '#4F46E5',
        'gotosocial' => '#C76C33',
        'friendica' => '#1872A2',
        'peertube' => '#F1680D',
        'forgejo' => '#609926',
        'listenbrainz' => '#A13D7A',
        'rss' => '#E07B39',
    ) as $slug => $color) {
        $icons .= sprintf('.settings_page_eg-social-timeline .egst-section-icon.platform-%s { color: %s; }', $slug, $color);
    }

    $css = '
/* Ogni sezione e il suo campo formano un riquadro: WordPress non avvolge
   titolo e tabella in un contenitore, quindi il bordo si compone unendo
   l\'h2 alla form-table che lo segue. */
.settings_page_eg-social-timeline .form-table tr:nth-child(even) {
    background: #f6f7f7;
}
.settings_page_eg-social-timeline h2 {
    margin: 26px 0 0;
    padding: 12px 16px;
    background: #fff;
    border: 1px solid #c3c4c7;
    border-bottom: 0;
    border-radius: 6px 6px 0 0;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.settings_page_eg-social-timeline h2 + p.description,
.settings_page_eg-social-timeline h2 + p {
    margin: 0;
    padding: 10px 16px;
    background: #fff;
    border-left: 1px solid #c3c4c7;
    border-right: 1px solid #c3c4c7;
    color: #50575e;
}
.settings_page_eg-social-timeline .form-table {
    margin-top: 0;
    padding: 0 16px 6px;
    background: #fff;
    border: 1px solid #c3c4c7;
    border-top: 0;
    border-radius: 0 0 6px 6px;
}
/* Riquadri delle piattaforme: <details> che si apre senza JavaScript. */
.settings_page_eg-social-timeline details.egst-box {
    margin: 14px 0 0;
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 6px;
}
.settings_page_eg-social-timeline details.egst-box > summary {
    list-style: none;
    cursor: pointer;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 600;
    color: #1d2327;
}
.settings_page_eg-social-timeline details.egst-box > summary::-webkit-details-marker {
    display: none;
}
.settings_page_eg-social-timeline details.egst-box > summary::before {
    content: "";
    border-left: 6px solid #50575e;
    border-top: 5px solid transparent;
    border-bottom: 5px solid transparent;
    transition: transform 0.15s;
}
.settings_page_eg-social-timeline details.egst-box[open] > summary::before {
    transform: rotate(90deg);
}
.settings_page_eg-social-timeline details.egst-box[open] > summary {
    border-bottom: 1px solid #dcdcde;
}
.settings_page_eg-social-timeline details.egst-box > summary:focus-visible {
    outline: 2px solid #2271b1;
    outline-offset: -2px;
    border-radius: 6px;
}
.settings_page_eg-social-timeline .egst-box-state {
    margin-left: auto;
    font-size: 13px;
    font-weight: 400;
    color: #646970;
}
.settings_page_eg-social-timeline .egst-box-ok .egst-box-state {
    color: #00712e;
}
.settings_page_eg-social-timeline .egst-box-warn .egst-box-state {
    color: #8a5a00;
    font-weight: 600;
}
.settings_page_eg-social-timeline details.egst-box > p.description {
    margin: 0;
    padding: 10px 16px 0;
    color: #50575e;
}
.settings_page_eg-social-timeline details.egst-box .form-table {
    border: 0;
    border-radius: 0 0 6px 6px;
}
.settings_page_eg-social-timeline .egst-section-icon svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
    display: block;
}
' . $icons . '
.settings_page_eg-social-timeline .form-table th,
.settings_page_eg-social-timeline .form-table td {
    padding-left: 14px;
    padding-right: 14px;
}
.settings_page_eg-social-timeline .form-table th {
    width: 250px;
}
/* Il riquadro di un colore personalizzato si vede anche sul grigio. */
.settings_page_eg-social-timeline input[type="color"] {
    border: 1px solid #8c8f94;
    border-radius: 4px;
    padding: 2px;
    background: #fff;
}';

    wp_add_inline_style('common', $css);
}

// Handle manual cache clear
add_action('admin_init', 'eg_social_timeline_handle_cache_clear');

function eg_social_timeline_handle_cache_clear() {
    if (!isset($_POST['eg_social_timeline_clear_cache'])) {
        return;
    }
    
    if (!current_user_can('manage_options')) {
        return;
    }
    
    if (!isset($_POST['eg_social_timeline_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['eg_social_timeline_nonce'])), 'eg_social_timeline_clear_cache')) {
        return;
    }
    
    delete_transient('eg_social_timeline_cache');

    // Un recupero subito, cosi' la cache e la tabella degli esiti tornano
    // piene senza dover aspettare che qualcuno apra la pagina della timeline.
    $posts = eg_social_timeline_fetch_all_feeds();

    add_settings_error(
        'eg_social_timeline_options',
        'cache_cleared',
        sprintf(
            /* translators: %d: numero di contenuti recuperati */
            _n('Cache flushed and rebuilt: %d item retrieved.', 'Cache flushed and rebuilt: %d items retrieved.', count($posts), 'eg-social-timeline'),
            count($posts)
        ),
        'success'
    );
    
    set_transient('eg_social_timeline_admin_notice', true, 5);
}

// Validate that a URL points to a public host (anti-SSRF)
function eg_social_timeline_is_public_url($url) {
    $host = wp_parse_url($url, PHP_URL_HOST);
    if (empty($host)) {
        return false;
    }

    // Reject IP addresses (IPv4 and IPv6)
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return false;
    }

    // Reject localhost and common internal hostnames
    $blocked = array('localhost', 'localhost.localdomain', 'ip6-localhost');
    if (in_array(strtolower($host), $blocked, true)) {
        return false;
    }

    // Resolve hostname and reject private/reserved IPs
    $ip = gethostbyname($host);
    if ($ip === $host) {
        return false; // DNS resolution failed
    }
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return false;
    }

    return true;
}

// Get Mastodon Account ID from profile URL
function eg_social_timeline_get_mastodon_account_id($instance, $username) {
    $instance = eg_social_timeline_normalize_instance($instance);
    $username = ltrim(trim((string) $username), '@');

    if ('' === $instance || '' === $username) {
        return false;
    }

    $cache_key = 'eg_social_timeline_mastodon_id_' . md5($instance . '/' . $username);
    $cached_id = get_transient($cache_key);

    if ($cached_id !== false) {
        return $cached_id;
    }

    $api_url = $instance . '/api/v1/accounts/lookup?acct=' . rawurlencode($username);

    $response = wp_remote_get($api_url, array(
        'timeout' => 10,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response)) {
        eg_social_timeline_record_issue('mastodon', $response->get_error_message());

        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline: Mastodon account lookup error - ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }

        return false;
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    // 401 e 403 sono il caso tipico di GoToSocial e Friendica, o di
    // un'istanza Mastodon che ha chiuso l'API a chi non ha fatto login.
    if (401 === $code || 403 === $code) {
        eg_social_timeline_record_issue(
            'mastodon',
            __('The instance requires authentication for the accounts API.', 'eg-social-timeline')
        );

        return false;
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (!isset($data['id'])) {
        eg_social_timeline_record_issue(
            'mastodon',
            sprintf(
                /* translators: 1: nome utente cercato, 2: codice di stato HTTP */
                __('Account "%1$s" not found on that instance (HTTP %2$d).', 'eg-social-timeline'),
                $username,
                $code
            )
        );

        return false;
    }

    set_transient($cache_key, $data['id'], MONTH_IN_SECONDS);

    return $data['id'];
}

function eg_social_timeline_fetch_mastodon($username, $instance, $limit = 0) {
    $instance = eg_social_timeline_normalize_instance($instance);

    if (empty($username) || '' === $instance) {
        return array();
    }

    $account_id = eg_social_timeline_get_mastodon_account_id($instance, $username);

    if (!$account_id) {
        return array();
    }

    $software = eg_social_timeline_detect_software($instance);
    $label = eg_social_timeline_fediverse_label($software);

    $show_boosts = eg_social_timeline_display_option('mastodon', 'boosts');

    // Use configured limit or default to 40
    $api_limit = ($limit > 0) ? min($limit, 40) : 40;
    
    $api_url = $instance . '/api/v1/accounts/' . rawurlencode($account_id) . '/statuses';
    
    $params = array(
        'limit' => $api_limit,
        'exclude_replies' => 'true',
        'exclude_reblogs' => $show_boosts ? 'false' : 'true'
    );
    
    $api_url .= '?' . http_build_query($params);
    
    $response = wp_remote_get($api_url, array(
        'timeout' => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));
    
    if (is_wp_error($response)) {
        eg_social_timeline_record_issue('mastodon', $response->get_error_message());

        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Mastodon API Error: ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return array();
    }
    
    $body = wp_remote_retrieve_body($response);
    $statuses = json_decode($body, true);
    
    if (!is_array($statuses)) {
        return array();
    }
    
    $posts = array();
    
    foreach ($statuses as $status) {
        $is_boost = !empty($status['reblog']);
        $content_data = $is_boost ? $status['reblog'] : $status;
        
        $content = wp_strip_all_tags($content_data['content']);
        
        $title_parts = explode("\n", $content);
        $title = !empty($title_parts[0]) ? $title_parts[0] : '';
        
        $post_url = $is_boost ? $content_data["url"] : $status["url"];
        
        // Extract first image attachment
        $image_url = '';
        $image_alt = '';
        if (!empty($content_data['media_attachments'])) {
            foreach ($content_data['media_attachments'] as $attachment) {
                if (isset($attachment['type']) && $attachment['type'] === 'image') {
                    $image_url = isset($attachment['preview_url']) ? $attachment['preview_url'] : ($attachment['url'] ?? '');
                    $image_alt = isset($attachment['description']) ? $attachment['description'] : '';
                    break;
                }
            }
        }

        $posts[] = array(
            'platform' => 'mastodon',
            'platform_label' => $label,
            'software' => $software,
            'date' => strtotime($status['created_at']),
            'title' => $title,
            'content' => $content,
            'link' => $post_url,
            'is_boost' => $is_boost,
            'image_url' => $image_url,
            'image_alt' => $image_alt,
            'favourites_count' => isset($content_data['favourites_count']) ? intval($content_data['favourites_count']) : 0,
            'reblogs_count' => isset($content_data['reblogs_count']) ? intval($content_data['reblogs_count']) : 0,
            'replies_count' => isset($content_data['replies_count']) ? intval($content_data['replies_count']) : 0
        );
    }
    
    return $posts;
}

// Fetch del feed RSS utente di Lemmy, con le statistiche nella description
function eg_social_timeline_fetch_lemmy($username, $instance, $limit = 0) {
    $instance = eg_social_timeline_normalize_instance($instance);

    if (empty($username) || '' === $instance) {
        return array();
    }

    // Formato dei feed Lemmy, uguale su ogni istanza. Funziona solo per gli
    // utenti locali: su un'istanza che li federa risponde 400.
    $rss_url = $instance . '/feeds/u/' . rawurlencode($username) . '.xml';

    $response = wp_remote_get($rss_url, array(
        'timeout' => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response)) {
        eg_social_timeline_record_issue('lemmy', $response->get_error_message());

        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Lemmy Error: ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return array();
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    if (200 !== $code) {
        eg_social_timeline_record_issue(
            'lemmy',
            sprintf(
                /* translators: 1: nome utente, 2: codice di stato HTTP */
                __('The instance did not return the feed of "%1$s" (HTTP %2$d). On Lemmy the user feed exists only on the instance where the account is registered.', 'eg-social-timeline'),
                $username,
                $code
            )
        );

        return array();
    }

    $body = wp_remote_retrieve_body($response);

    if (empty($body)) {
        return array();
    }

    $label = eg_social_timeline_lemmy_label($instance);
    
    // Un feed malformato non deve riempire il log del sito di warning: gli
    // errori di libxml restano interni e il fallimento si gestisce qui.
    $previous_errors = libxml_use_internal_errors(true);
    $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous_errors);

    if ($xml === false) {
        return array();
    }
    
    $posts = array();
    $count = 0;
    
    foreach ($xml->channel->item as $item) {
        // Apply limit if set
        if ($limit > 0 && $count >= $limit) {
            break;
        }
        
        $pubDate = (string) $item->pubDate;
        $timestamp = strtotime($pubDate);
        
        // Parse description: convert <br> to newlines first
        $description = (string) $item->description;
        $description = str_replace(['<br>', '<br/>', '<br />'], "\n", $description);
        
        // Remove all HTML tags
        $clean_text = wp_strip_all_tags($description);
        
        // Split into lines and remove empty ones
        $lines = explode("\n", $clean_text);
        $lines = array_filter(array_map('trim', $lines));
        
        $points = 0;
        $comments = 0;
        $content_lines = array();
        
        foreach ($lines as $line) {
            // Skip "submitted by X to Y" line
            if (stripos($line, 'submitted by') !== false) {
                continue;
            }
            
            // Parse statistics line: "X points | Y comments"
            if (preg_match('/^(\d+)\s+points?\s+\|\s+(\d+)\s+comments?/i', $line, $matches)) {
                $points = intval($matches[1]);
                $comments = intval($matches[2]);
                continue;
            }
            
            // All other lines are content
            $content_lines[] = $line;
        }
        
        $clean_content = trim(implode("\n", $content_lines));
        
        $posts[] = array(
            'platform' => 'lemmy',
            'platform_label' => $label,
            'date' => $timestamp,
            'title' => (string) $item->title,
            'content' => $clean_content,
            'link' => (string) $item->link,
            'is_boost' => false,
            'image_url' => '',
            'image_alt' => '',
            'favourites_count' => $points,
            'reblogs_count' => 0,
            'replies_count' => $comments
        );
        
        $count++;
    }
    
    return $posts;
}

// Fetch Forgejo/Gitea commits via direct API
function eg_social_timeline_fetch_forgejo($username, $instance_url, $limit = 0) {
    if (empty($username) || empty($instance_url)) {
        return array();
    }

    $instance_url = rtrim($instance_url, '/');

    if (!eg_social_timeline_is_public_url($instance_url)) {
        return array();
    }
    
    // Step 1: Get list of public repositories
    $repos_url = $instance_url . '/api/v1/users/' . sanitize_text_field($username) . '/repos'
        . '?limit=50';

    $repos_response = wp_remote_get($repos_url, array(
        'timeout' => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($repos_response)) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Forgejo Repos Error: ' . $repos_response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return array();
    }

    $repos_body = wp_remote_retrieve_body($repos_response);
    $repositories = json_decode($repos_body, true);

    if (!is_array($repositories)) {
        return array();
    }

    // Filter to public repos only and sort by updated_at descending
    $public_repos = array_values(array_filter($repositories, function($repo) {
        return empty($repo['private']);
    }));

    usort($public_repos, function($a, $b) {
        return strtotime($b['updated_at']) - strtotime($a['updated_at']);
    });

    if (empty($public_repos)) {
        return array();
    }

    // Calculate commits per repo based on total limit
    $repo_count = count($public_repos);

    if ($limit > 0) {
        $commits_per_repo = max(1, intval(ceil($limit / min($repo_count, $limit))));
        $total_limit = $limit;
        // Only query the most recently updated repos we actually need
        $public_repos = array_slice($public_repos, 0, min($repo_count, $limit));
    } else {
        // No limit: default 5 per repo, query all
        $commits_per_repo = 5;
        $total_limit = PHP_INT_MAX;
    }

    $all_commits = array();
    $total_fetched = 0;

    // Step 2: Get commits from each selected repository
    foreach ($public_repos as $repo) {
        if ($total_fetched >= $total_limit) {
            break;
        }
        
        $repo_name = $repo['name'];
        $repo_full_name = $repo['full_name'];
        $default_branch = isset($repo['default_branch']) ? $repo['default_branch'] : 'main';
        
        // Calculate how many commits to fetch from this repo
        $remaining = $total_limit - $total_fetched;
        $fetch_limit = min($commits_per_repo, $remaining);
        
        $commits_url = $instance_url . '/api/v1/repos/' . $repo_full_name . '/commits';
        $commits_url .= '?limit=' . $fetch_limit . '&sha=' . urlencode($default_branch);
        
        $commits_response = wp_remote_get($commits_url, array(
            'timeout' => 10,
            'sslverify' => true,
            // I redirect non vengono rivalidati senza questo flag: la
            // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
            'reject_unsafe_urls' => true,
        ));
        
        if (is_wp_error($commits_response)) {
            if (EG_SOCIAL_TIMELINE_DEBUG) {
                error_log('EG Social Timeline Forgejo Commits Error for ' . $repo_name . ': ' . $commits_response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            }
            continue;
        }
        
        $commits_body = wp_remote_retrieve_body($commits_response);
        $commits = json_decode($commits_body, true);
        
        if (!is_array($commits)) {
            continue;
        }
        
        // Process each commit
        foreach ($commits as $commit) {
            if ($total_fetched >= $total_limit) {
                break 2;
            }
            
            $commit_message = isset($commit['commit']['message']) ? $commit['commit']['message'] : '';
            $commit_date = isset($commit['commit']['committer']['date']) ? $commit['commit']['committer']['date'] : '';
            
            if (empty($commit_date) || empty($commit_message)) {
                continue;
            }
            
            // Extract first line of commit message
            $message_lines = explode("\n", $commit_message);
            $short_message = trim($message_lines[0]);
            
            // Link to commits page of the repository
            $commits_page_url = $instance_url . '/' . $repo_full_name . '/commits/branch/' . urlencode($default_branch);
            
            // Include repo name in content so it's visible in timeline
            $full_content = 'Commit to ' . $repo_name . ': ' . $short_message;
            
            $all_commits[] = array(
                'platform' => 'forgejo',
                'date' => strtotime($commit_date),
                'title' => 'Commit to ' . $repo_name,
                'content' => $full_content,
                'link' => $commits_page_url,
                'is_boost' => false,
                'image_url' => '',
                'image_alt' => '',
                'favourites_count' => 0,
                'reblogs_count' => 0,
                'replies_count' => 0
            );
            
            $total_fetched++;
        }
    }
    
    return $all_commits;
}

// Fetch Bluesky posts via public ATP API (no auth required)
function eg_social_timeline_fetch_bluesky($handle, $limit = 0) {
    if (empty($handle)) {
        return array();
    }

    $handle = ltrim($handle, '@');
    $api_limit = ($limit > 0) ? min($limit, 100) : 100;

    $api_url = 'https://public.api.bsky.app/xrpc/app.bsky.feed.getAuthorFeed?' . http_build_query(array(
        'actor'  => $handle,
        'limit'  => $api_limit,
        'filter' => 'posts_no_replies',
    ));

    $response = wp_remote_get($api_url, array(
        'timeout'  => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response)) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Bluesky Error: ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return array();
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($data['feed']) || !is_array($data['feed'])) {
        return array();
    }

    $options = get_option('eg_social_timeline_options');
    $show_boosts = eg_social_timeline_display_option('bluesky', 'boosts');

    $posts = array();

    foreach ($data['feed'] as $item) {
        $is_repost = isset($item['reason']['$type']) && $item['reason']['$type'] === 'app.bsky.feed.defs#reasonRepost';

        if ($is_repost && !$show_boosts) {
            continue;
        }

        $post = $item['post'] ?? null;
        if (empty($post) || !is_array($post)) {
            continue;
        }
        $record = $post['record'] ?? array();

        $content = isset($record['text']) ? sanitize_text_field($record['text']) : '';
        if (empty($content)) {
            continue;
        }

        $created_at = isset($record['createdAt']) ? $record['createdAt'] : ($post['indexedAt'] ?? '');
        $timestamp = $created_at ? strtotime($created_at) : 0;
        if (!$timestamp) {
            continue;
        }

        // Build post URL from URI: at://did:.../app.bsky.feed.post/{rkey}
        $uri = $post['uri'] ?? '';
        $rkey = $uri ? basename($uri) : '';
        $post_url = ($rkey && !empty($post['author']['handle']))
            ? 'https://bsky.app/profile/' . rawurlencode($post['author']['handle']) . '/post/' . rawurlencode($rkey)
            : 'https://bsky.app/profile/' . rawurlencode($handle);

        $title_parts = explode("\n", $content);
        $title = trim($title_parts[0]);

        // Extract first image from the embed (parity with Mastodon media previews).
        // Handles direct image embeds and quote-post-with-media; ignores external link cards.
        $image_url = '';
        $image_alt = '';
        $embed = isset($post['embed']) && is_array($post['embed']) ? $post['embed'] : array();
        $embed_type = isset($embed['$type']) ? $embed['$type'] : '';
        $images = array();
        if ($embed_type === 'app.bsky.embed.images#view' && !empty($embed['images'])) {
            $images = $embed['images'];
        } elseif ($embed_type === 'app.bsky.embed.recordWithMedia#view'
            && isset($embed['media']['$type']) && $embed['media']['$type'] === 'app.bsky.embed.images#view'
            && !empty($embed['media']['images'])) {
            $images = $embed['media']['images'];
        }
        if (!empty($images) && is_array($images) && isset($images[0]) && is_array($images[0])) {
            $first = $images[0];
            $image_url = isset($first['thumb']) ? esc_url_raw($first['thumb']) : (isset($first['fullsize']) ? esc_url_raw($first['fullsize']) : '');
            $image_alt = isset($first['alt']) ? sanitize_text_field($first['alt']) : '';
        }

        $posts[] = array(
            'platform'        => 'bluesky',
            'date'            => $timestamp,
            'title'           => $title,
            'content'         => $content,
            'link'            => $post_url,
            'is_boost'        => $is_repost,
            'image_url'       => $image_url,
            'image_alt'       => $image_alt,
            'favourites_count' => intval($post['likeCount'] ?? 0),
            'reblogs_count'   => intval($post['repostCount'] ?? 0),
            'replies_count'   => intval($post['replyCount'] ?? 0),
        );
    }

    return $posts;
}

// Fetch PeerTube videos via the public REST API (no authentication)
function eg_social_timeline_fetch_peertube($username, $instance, $limit = 0, $type = 'auto') {
    $instance = eg_social_timeline_normalize_instance($instance);
    $username = ltrim(trim((string) $username), '@');

    if ('' === $username || '' === $instance) {
        return array();
    }

    $api_limit = ($limit > 0) ? min($limit, 100) : 100;

    // Su PeerTube i video stanno quasi sempre in un canale, non nell'account:
    // l'indirizzo /c/nome e' un canale, /a/nome un account. Quando il tipo non
    // e' noto si provano entrambi e l'esito resta in cache.
    $cache_key = 'eg_social_timeline_pt_kind_' . md5($instance . '/' . $username);

    if (!in_array($type, array('account', 'channel'), true)) {
        $cached_kind = get_transient($cache_key);
        $type = $cached_kind ? $cached_kind : 'auto';
    }

    $endpoints = array(
        'account' => '/api/v1/accounts/',
        'channel' => '/api/v1/video-channels/',
    );

    $kinds = ('auto' === $type) ? array('account', 'channel') : array($type);
    $data = null;
    $found_kind = '';
    $last_code = 0;

    foreach ($kinds as $kind) {
        $api_url = $instance . $endpoints[$kind] . rawurlencode($username) . '/videos?' . http_build_query(array(
            'count' => $api_limit,
            'sort'  => '-publishedAt',
        ));

        $response = wp_remote_get($api_url, array(
            'timeout'  => 15,
            'sslverify' => true,
            // I redirect non vengono rivalidati senza questo flag: la
            // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
            'reject_unsafe_urls' => true,
        ));

        if (is_wp_error($response)) {
            eg_social_timeline_record_issue('peertube', $response->get_error_message());

            if (EG_SOCIAL_TIMELINE_DEBUG) {
                error_log('EG Social Timeline PeerTube Error: ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            }

            return array();
        }

        $last_code = (int) wp_remote_retrieve_response_code($response);

        if (200 !== $last_code) {
            continue;
        }

        $decoded = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $data = $decoded;
            $found_kind = $kind;
            break;
        }
    }

    if (null === $data) {
        eg_social_timeline_record_issue(
            'peertube',
            sprintf(
                /* translators: 1: nome cercato, 2: host dell'istanza, 3: codice di stato HTTP */
                __('Neither an account nor a channel named "%1$s" exists on %2$s (HTTP %3$d). On PeerTube videos usually live in a channel: from an address like /c/name@host use that name and the host it belongs to.', 'eg-social-timeline'),
                $username,
                wp_parse_url($instance, PHP_URL_HOST),
                $last_code
            )
        );

        return array();
    }

    set_transient($cache_key, $found_kind, MONTH_IN_SECONDS);

    if (empty($data['data'])) {
        return array();
    }

    $base = $instance;
    $posts = array();

    foreach ($data['data'] as $video) {
        if (!is_array($video)) {
            continue;
        }

        $name = isset($video['name']) ? sanitize_text_field($video['name']) : '';
        if (empty($name)) {
            continue;
        }

        $published = isset($video['publishedAt']) ? $video['publishedAt'] : '';
        $timestamp = $published ? strtotime($published) : 0;
        if (!$timestamp) {
            continue;
        }

        $video_url = !empty($video['url']) ? esc_url_raw($video['url']) : '';
        if (empty($video_url) && !empty($video['uuid'])) {
            $video_url = $base . '/videos/watch/' . rawurlencode($video['uuid']);
        }

        // Thumbnail path is relative to the instance (e.g. /lazy-static/thumbnails/xxx.jpg)
        $image_url = '';
        if (!empty($video['thumbnailPath'])) {
            $image_url = $base . '/' . ltrim($video['thumbnailPath'], '/');
        }

        $description = isset($video['description']) ? sanitize_text_field($video['description']) : '';
        $content = $description !== '' ? $name . "\n\n" . $description : $name;

        $posts[] = array(
            'platform'        => 'peertube',
            'platform_label'  => 'PeerTube',
            'date'            => $timestamp,
            'title'           => $name,
            'content'         => $content,
            'link'            => $video_url,
            'is_boost'        => false,
            'image_url'       => $image_url,
            'image_alt'       => $name,
            'favourites_count' => intval($video['likes'] ?? 0),
            'reblogs_count'   => 0,
            'replies_count'   => 0,
        );
    }

    return $posts;
}

/**
 * Fetch dei post Pixelfed dal feed Atom pubblico del profilo.
 *
 * L'API Mastodon di Pixelfed risponde al lookup dell'account ma rimanda
 * l'endpoint degli stati alla pagina di login, quindi la via pubblica e' il
 * feed Atom. Porta foto e didascalie ma nessun conteggio di interazioni.
 *
 * Il parser e' dedicato: Atom usa feed/entry con namespace media, non il
 * channel/item dell'RSS 2.0 usato per Lemmy.
 *
 * @param string $username Nome utente.
 * @param string $instance URL dell'istanza.
 * @param int    $limit    Numero massimo di post, 0 per nessun limite.
 * @return array Post normalizzati.
 */
function eg_social_timeline_fetch_pixelfed($username, $instance, $limit = 0) {
    $instance = eg_social_timeline_normalize_instance($instance);
    $username = ltrim(trim((string) $username), '@');

    if ('' === $username || '' === $instance) {
        return array();
    }

    $feed_url = $instance . '/users/' . rawurlencode($username) . '.atom';

    $response = wp_remote_get($feed_url, array(
        'timeout'  => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response)) {
        eg_social_timeline_record_issue('pixelfed', $response->get_error_message());

        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Pixelfed Error: ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }

        return array();
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    if (200 !== $code) {
        eg_social_timeline_record_issue(
            'pixelfed',
            sprintf(
                /* translators: 1: nome utente, 2: codice di stato HTTP */
                __('The instance did not return the Atom feed of "%1$s" (HTTP %2$d).', 'eg-social-timeline'),
                $username,
                $code
            )
        );

        return array();
    }

    $body = wp_remote_retrieve_body($response);

    if (empty($body)) {
        return array();
    }

    // Un feed malformato non deve riempire il log del sito di warning: gli
    // errori di libxml restano interni e il fallimento si gestisce qui.
    $previous_errors = libxml_use_internal_errors(true);
    $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous_errors);

    if (false === $xml || !isset($xml->entry)) {
        eg_social_timeline_record_issue(
            'pixelfed',
            __('The Atom feed could not be read. The instance may have changed its format.', 'eg-social-timeline')
        );

        return array();
    }

    $posts = array();
    $count = 0;

    foreach ($xml->entry as $entry) {
        if ($limit > 0 && $count >= $limit) {
            break;
        }

        $timestamp = strtotime((string) $entry->updated);

        if (!$timestamp) {
            continue;
        }

        // Il link della pagina del post: rel="alternate", o il primo link.
        $link = '';

        foreach ($entry->link as $candidate) {
            $attributes = $candidate->attributes();
            $rel = isset($attributes['rel']) ? (string) $attributes['rel'] : 'alternate';

            if ('alternate' === $rel && isset($attributes['href'])) {
                $link = (string) $attributes['href'];
                break;
            }
        }

        if ('' === $link) {
            $link = (string) $entry->id;
        }

        $title = sanitize_text_field((string) $entry->title);
        $summary = trim(wp_strip_all_tags((string) $entry->summary));
        $content_html = (string) $entry->content;
        $content = ('' !== $summary) ? $summary : $title;

        // L'immagine sta in media:content; in mancanza si prende quella del
        // contenuto HTML, dove Pixelfed mette la galleria del post.
        $image_url = '';
        $image_alt = $title;
        $media = $entry->children('http://search.yahoo.com/mrss/');

        if (isset($media->content)) {
            $media_attributes = $media->content->attributes();

            if (isset($media_attributes['url'])) {
                $image_url = esc_url_raw((string) $media_attributes['url']);
            }
        }

        if ('' === $image_url && preg_match('~<img[^>]+src="([^"]+)"~i', $content_html, $matches)) {
            $image_url = esc_url_raw($matches[1]);
        }

        if (preg_match('~<img[^>]+alt="([^"]*)"~i', $content_html, $matches) && '' !== $matches[1]) {
            $image_alt = sanitize_text_field($matches[1]);
        }

        $posts[] = array(
            'platform'         => 'pixelfed',
            'platform_label'   => 'Pixelfed',
            'date'             => $timestamp,
            'title'            => $title,
            'content'          => $content,
            'link'             => esc_url_raw($link),
            'is_boost'         => false,
            'image_url'        => $image_url,
            'image_alt'        => $image_alt,
            'favourites_count' => 0,
            'reblogs_count'    => 0,
            'replies_count'    => 0,
        );

        $count++;
    }

    return $posts;
}

/**
 * Testo semplice da un frammento HTML di un feed.
 *
 * I paragrafi e gli a capo diventano ritorni a capo prima di togliere i tag,
 * altrimenti le frasi di due paragrafi si incollano. Le entita' vengono
 * decodificate qui perche' l'output passa gia' da esc_html().
 *
 * @param string $html Frammento HTML.
 * @return string
 */
function eg_social_timeline_feed_text($html) {
    $html = preg_replace('~</p>|<br\s*/?>~i', "\n", (string) $html);

    return trim(html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

/**
 * Scarica e interpreta il feed di un profilo.
 *
 * @param string $platform Slug, per registrare gli errori.
 * @param string $feed_url Indirizzo del feed.
 * @return array array con code e xml (SimpleXMLElement o false).
 */
function eg_social_timeline_get_profile_feed($platform, $feed_url) {
    $response = wp_remote_get($feed_url, array(
        'timeout'  => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response)) {
        eg_social_timeline_record_issue($platform, $response->get_error_message());

        return array('code' => 0, 'xml' => false);
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    if (200 !== $code) {
        return array('code' => $code, 'xml' => false);
    }

    // Un feed malformato non deve riempire il log del sito di warning: gli
    // errori di libxml restano interni e il fallimento si gestisce qui.
    $previous_errors = libxml_use_internal_errors(true);
    $xml = simplexml_load_string(wp_remote_retrieve_body($response), 'SimpleXMLElement', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous_errors);

    if (false === $xml) {
        eg_social_timeline_record_issue(
            $platform,
            __('The feed could not be read. The instance may have changed its format.', 'eg-social-timeline')
        );
    }

    return array('code' => $code, 'xml' => $xml);
}

/**
 * Server che ospita davvero un account GoToSocial.
 *
 * GoToSocial permette un dominio dell'account diverso dal server: l'account
 * @gotosocial@superseriousbusiness.org vive su gts.superseriousbusiness.org,
 * e il feed esiste solo li'. Il webfinger del dominio lo dice nel link "self".
 *
 * @param string $instance URL scritto dall'utente.
 * @param string $username Nome utente.
 * @return string URL del server reale, stringa vuota se non determinabile.
 */
function eg_social_timeline_resolve_gotosocial_host($instance, $username) {
    $host = wp_parse_url($instance, PHP_URL_HOST);

    if (empty($host) || '' === $username) {
        return '';
    }

    $response = wp_remote_get($instance . '/.well-known/webfinger?resource=' . rawurlencode('acct:' . $username . '@' . $host), array(
        'timeout'  => 10,
        'sslverify' => true,
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response) || 200 !== (int) wp_remote_retrieve_response_code($response)) {
        return '';
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($data['links']) || !is_array($data['links'])) {
        return '';
    }

    foreach ($data['links'] as $link) {
        if (isset($link['rel'], $link['href']) && 'self' === $link['rel']) {
            return eg_social_timeline_normalize_instance(wp_parse_url($link['href'], PHP_URL_HOST));
        }
    }

    return '';
}

/**
 * Fetch dei post GoToSocial dal feed RSS pubblico del profilo.
 *
 * L'API compatibile con Mastodon di GoToSocial chiede l'accesso, mentre il
 * feed /@nome/feed.rss e' pubblico: e' spento di default e l'utente lo
 * accende dalle impostazioni del proprio account. Porta gli ultimi 20 post
 * pubblici, senza risposte ne' boost, e nessun conteggio.
 *
 * Il testo sta in content:encoded: la description e' un riassunto troncato
 * che comincia con "@utente made a new post". Le immagini arrivano come
 * enclosure, senza testo alternativo.
 *
 * Risposte misurate: 404 feed spento, 500 account inesistente.
 *
 * @param string $username Nome utente.
 * @param string $instance URL dell'istanza.
 * @param int    $limit    Numero massimo di post, 0 per nessun limite.
 * @return array Post normalizzati.
 */
function eg_social_timeline_fetch_gotosocial($username, $instance, $limit = 0) {
    $instance = eg_social_timeline_normalize_instance($instance);
    $username = ltrim(trim((string) $username), '@');

    if ('' === $username || '' === $instance) {
        return array();
    }

    $feed = eg_social_timeline_get_profile_feed('gotosocial', $instance . '/@' . rawurlencode($username) . '/feed.rss');

    if (404 === $feed['code']) {
        eg_social_timeline_record_issue(
            'gotosocial',
            sprintf(
                /* translators: 1: nome utente, 2: indirizzo delle impostazioni dell'account */
                __('The RSS feed of "%1$s" is turned off. GoToSocial keeps it off by default: turn it on in the account settings (%2$s).', 'eg-social-timeline'),
                $username,
                $instance . '/settings'
            )
        );

        return array();
    }

    if (200 !== $feed['code'] && 0 !== $feed['code']) {
        eg_social_timeline_record_issue(
            'gotosocial',
            sprintf(
                /* translators: 1: nome utente, 2: codice di stato HTTP */
                __('The instance did not return the feed of "%1$s" (HTTP %2$d). A 500 usually means the account does not exist on this instance.', 'eg-social-timeline'),
                $username,
                $feed['code']
            )
        );

        return array();
    }

    if (false === $feed['xml'] || !isset($feed['xml']->channel->item)) {
        return array();
    }

    $posts = array();
    $count = 0;

    foreach ($feed['xml']->channel->item as $item) {
        if ($limit > 0 && $count >= $limit) {
            break;
        }

        $timestamp = strtotime((string) $item->pubDate);

        if (!$timestamp) {
            continue;
        }

        $encoded = $item->children('http://purl.org/rss/1.0/modules/content/');
        $text = isset($encoded->encoded) ? eg_social_timeline_feed_text((string) $encoded->encoded) : '';

        if ('' === $text) {
            $text = eg_social_timeline_feed_text((string) $item->description);
        }

        $image_url = '';

        if (isset($item->enclosure)) {
            $enclosure = $item->enclosure->attributes();
            $type = isset($enclosure['type']) ? (string) $enclosure['type'] : '';

            if (isset($enclosure['url']) && 0 === strpos($type, 'image/')) {
                $image_url = esc_url_raw((string) $enclosure['url']);
            }
        }

        $title = strtok($text, "\n");

        $posts[] = array(
            'platform'         => 'gotosocial',
            'platform_label'   => 'GoToSocial',
            'date'             => $timestamp,
            'title'            => (false !== $title) ? $title : '',
            'content'          => $text,
            'link'             => esc_url_raw((string) $item->link),
            'is_boost'         => false,
            'image_url'        => $image_url,
            // Il feed non porta la descrizione dell'immagine.
            'image_alt'        => '',
            'favourites_count' => 0,
            'reblogs_count'    => 0,
            'replies_count'    => 0,
        );

        $count++;
    }

    return $posts;
}

/**
 * Fetch dei post Friendica dal feed Atom pubblico del profilo.
 *
 * /feed/{nome}/ porta i post del profilo senza le risposte, che stanno in
 * /feed/{nome}/activity. L'API compatibile con Mastodon chiede l'accesso.
 * Le immagini sono link rel="enclosure", con la descrizione nel title.
 *
 * @param string $username Nickname.
 * @param string $instance URL dell'istanza.
 * @param int    $limit    Numero massimo di post, 0 per nessun limite.
 * @return array Post normalizzati.
 */
function eg_social_timeline_fetch_friendica($username, $instance, $limit = 0) {
    $instance = eg_social_timeline_normalize_instance($instance);
    $username = ltrim(trim((string) $username), '@');

    if ('' === $username || '' === $instance) {
        return array();
    }

    $feed = eg_social_timeline_get_profile_feed('friendica', $instance . '/feed/' . rawurlencode($username) . '/');

    if (200 !== $feed['code'] && 0 !== $feed['code']) {
        eg_social_timeline_record_issue(
            'friendica',
            sprintf(
                /* translators: 1: nickname, 2: codice di stato HTTP */
                __('The instance did not return the feed of "%1$s" (HTTP %2$d). Check the nickname: it is the name in the profile address.', 'eg-social-timeline'),
                $username,
                $feed['code']
            )
        );

        return array();
    }

    if (false === $feed['xml'] || !isset($feed['xml']->entry)) {
        return array();
    }

    $posts = array();
    $count = 0;

    foreach ($feed['xml']->entry as $entry) {
        if ($limit > 0 && $count >= $limit) {
            break;
        }

        $timestamp = strtotime((string) ($entry->published ? $entry->published : $entry->updated));

        if (!$timestamp) {
            continue;
        }

        $link = '';
        $image_url = '';
        $image_alt = '';

        foreach ($entry->link as $candidate) {
            $attributes = $candidate->attributes();
            $rel = isset($attributes['rel']) ? (string) $attributes['rel'] : 'alternate';
            $type = isset($attributes['type']) ? (string) $attributes['type'] : '';

            if ('alternate' === $rel && '' === $link && isset($attributes['href'])) {
                $link = (string) $attributes['href'];
            } elseif ('enclosure' === $rel && '' === $image_url && 0 === strpos($type, 'image/') && isset($attributes['href'])) {
                $image_url = esc_url_raw((string) $attributes['href']);
                $image_alt = isset($attributes['title']) ? sanitize_text_field((string) $attributes['title']) : '';
            }
        }

        if ('' === $link) {
            $link = (string) $entry->id;
        }

        $title = sanitize_text_field((string) $entry->title);
        $text = eg_social_timeline_feed_text((string) $entry->content);

        $posts[] = array(
            'platform'         => 'friendica',
            'platform_label'   => 'Friendica',
            'date'             => $timestamp,
            'title'            => $title,
            // Un post con titolo e corpo li mostra entrambi, come la fonte RSS.
            'content'          => ('' === $text) ? $title : (('' !== $title && 0 !== strpos($text, $title)) ? $title . "\n\n" . $text : $text),
            'link'             => esc_url_raw($link),
            'is_boost'         => false,
            'image_url'        => $image_url,
            'image_alt'        => $image_alt,
            'favourites_count' => 0,
            'reblogs_count'    => 0,
            'replies_count'    => 0,
        );

        $count++;
    }

    return $posts;
}

/**
 * Fetch di un feed RSS 2.0 o Atom qualsiasi.
 *
 * Un solo parser per i due formati: cambiano i nomi degli elementi, non la
 * sostanza (titolo, link, testo, data, eventuale immagine).
 *
 * @param string $feed_url Indirizzo del feed.
 * @param string $label    Nome da mostrare; vuoto usa il titolo del feed.
 * @param int    $limit    Numero massimo di elementi, 0 per nessun limite.
 * @return array Post normalizzati.
 */
function eg_social_timeline_fetch_rss($feed_url, $label = '', $limit = 0) {
    $feed_url = trim((string) $feed_url);

    if ('' === $feed_url) {
        return array();
    }

    // Un indirizzo che non si puo' interrogare va detto: e' l'unico campo del
    // feed, e senza messaggio la fonte sparirebbe in silenzio.
    if (!eg_social_timeline_is_public_url($feed_url)) {
        eg_social_timeline_record_issue(
            'rss',
            __('The feed address cannot be used: it must be an HTTPS address of a public, resolvable host.', 'eg-social-timeline')
        );

        return array();
    }

    $response = wp_remote_get($feed_url, array(
        'timeout'  => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response)) {
        eg_social_timeline_record_issue('rss', $response->get_error_message());

        return array();
    }

    $code = (int) wp_remote_retrieve_response_code($response);

    if (200 !== $code) {
        eg_social_timeline_record_issue(
            'rss',
            sprintf(
                /* translators: %d: codice di stato HTTP */
                __('The feed did not answer (HTTP %d).', 'eg-social-timeline'),
                $code
            )
        );

        return array();
    }

    $body = wp_remote_retrieve_body($response);

    if (empty($body)) {
        return array();
    }

    // Un feed malformato non deve riempire il log del sito di warning: gli
    // errori di libxml restano interni e il fallimento si gestisce qui.
    $previous_errors = libxml_use_internal_errors(true);
    $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous_errors);

    if (false === $xml) {
        eg_social_timeline_record_issue(
            'rss',
            __('The feed could not be read: it is not valid RSS or Atom.', 'eg-social-timeline')
        );

        return array();
    }

    $is_atom = isset($xml->entry);
    $items = $is_atom ? $xml->entry : (isset($xml->channel->item) ? $xml->channel->item : null);

    if (null === $items) {
        eg_social_timeline_record_issue(
            'rss',
            __('The feed has no items: neither channel/item nor feed/entry found.', 'eg-social-timeline')
        );

        return array();
    }

    // Senza etichetta si usa il titolo del feed, e in mancanza il dominio.
    if ('' === $label) {
        $feed_title = $is_atom ? (string) $xml->title : (string) $xml->channel->title;
        $label = ('' !== trim($feed_title))
            ? sanitize_text_field($feed_title)
            : (string) wp_parse_url($feed_url, PHP_URL_HOST);
    }

    $posts = array();
    $count = 0;

    foreach ($items as $item) {
        if ($limit > 0 && $count >= $limit) {
            break;
        }

        if ($is_atom) {
            $date_raw = (string) ($item->published ? $item->published : $item->updated);
            $link = '';

            foreach ($item->link as $candidate) {
                $attributes = $candidate->attributes();
                $rel = isset($attributes['rel']) ? (string) $attributes['rel'] : 'alternate';

                if ('alternate' === $rel && isset($attributes['href'])) {
                    $link = (string) $attributes['href'];
                    break;
                }
            }

            $summary = (string) ($item->summary ? $item->summary : $item->content);
        } else {
            $date_raw = (string) $item->pubDate;
            $link = (string) $item->link;
            $summary = (string) $item->description;
        }

        $timestamp = $date_raw ? strtotime($date_raw) : 0;

        if (!$timestamp) {
            continue;
        }

        $title = sanitize_text_field((string) $item->title);
        $text = trim(wp_strip_all_tags($summary));
        $content = ('' !== $text) ? $text : $title;

        // Immagine: enclosure dell'RSS, media:content, o la prima del testo.
        $image_url = '';

        if (isset($item->enclosure)) {
            $enclosure = $item->enclosure->attributes();
            $type = isset($enclosure['type']) ? (string) $enclosure['type'] : '';

            if (isset($enclosure['url']) && (0 === strpos($type, 'image/') || '' === $type)) {
                $image_url = esc_url_raw((string) $enclosure['url']);
            }
        }

        if ('' === $image_url) {
            $media = $item->children('http://search.yahoo.com/mrss/');

            if (isset($media->content)) {
                $media_attributes = $media->content->attributes();

                if (isset($media_attributes['url'])) {
                    $image_url = esc_url_raw((string) $media_attributes['url']);
                }
            }
        }

        if ('' === $image_url && preg_match('~<img[^>]+src="([^"]+)"~i', $summary, $matches)) {
            $image_url = esc_url_raw($matches[1]);
        }

        $posts[] = array(
            'platform'         => 'rss',
            'platform_label'   => $label,
            'date'             => $timestamp,
            'title'            => $title,
            'content'          => ('' !== $title && 0 !== strpos($content, $title)) ? $title . "\n\n" . $content : $content,
            'link'             => esc_url_raw($link),
            'is_boost'         => false,
            'image_url'        => $image_url,
            'image_alt'        => $title,
            'favourites_count' => 0,
            'reblogs_count'    => 0,
            'replies_count'    => 0,
        );

        $count++;
    }

    return $posts;
}

/**
 * Fetch degli ascolti da ListenBrainz.
 *
 * API pubblica, nessun token: /1/user/{utente}/listens. Il link punta alla
 * registrazione su MusicBrainz quando l'mbid c'e', altrimenti al profilo.
 *
 * @param string $username Nome utente.
 * @param string $instance URL dell'API.
 * @param int    $limit    Numero massimo di ascolti, 0 per il valore di default.
 * @return array Post normalizzati.
 */
function eg_social_timeline_fetch_listenbrainz($username, $instance, $limit = 0) {
    $instance = eg_social_timeline_normalize_instance('' !== $instance ? $instance : EG_SOCIAL_TIMELINE_LISTENBRAINZ_API);
    $username = ltrim(trim((string) $username), '@');

    if ('' === $username || '' === $instance) {
        return array();
    }

    $count = ($limit > 0) ? min($limit, 100) : 25;

    $api_url = $instance . '/1/user/' . rawurlencode($username) . '/listens?' . http_build_query(array(
        'count' => $count,
    ));

    $response = wp_remote_get($api_url, array(
        'timeout'  => 15,
        'sslverify' => true,
        // I redirect non vengono rivalidati senza questo flag: la
        // verifica anti-SSRF dell'URL iniziale coprirebbe solo il primo salto.
        'reject_unsafe_urls' => true,
    ));

    if (is_wp_error($response)) {
        eg_social_timeline_record_issue('listenbrainz', $response->get_error_message());

        return array();
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (200 !== $code || !isset($data['payload']['listens'])) {
        eg_social_timeline_record_issue(
            'listenbrainz',
            sprintf(
                /* translators: 1: nome utente, 2: codice di stato HTTP */
                __('No listens returned for "%1$s" (HTTP %2$d).', 'eg-social-timeline'),
                $username,
                $code
            )
        );

        return array();
    }

    // Il sito sta sotto l'host dell'API senza il prefisso api.
    $site = 'https://' . preg_replace('~^api\.~', '', (string) wp_parse_url($instance, PHP_URL_HOST));
    $profile_url = $site . '/user/' . rawurlencode($username) . '/';

    $posts = array();

    foreach ($data['payload']['listens'] as $listen) {
        $timestamp = isset($listen['listened_at']) ? intval($listen['listened_at']) : 0;
        $meta = isset($listen['track_metadata']) ? $listen['track_metadata'] : array();

        if (!$timestamp || empty($meta['track_name'])) {
            continue;
        }

        $artist = isset($meta['artist_name']) ? sanitize_text_field($meta['artist_name']) : '';
        $track = sanitize_text_field($meta['track_name']);
        $album = isset($meta['release_name']) ? sanitize_text_field($meta['release_name']) : '';

        $mbid = '';

        if (!empty($meta['mbid_mapping']['recording_mbid'])) {
            $mbid = $meta['mbid_mapping']['recording_mbid'];
        } elseif (!empty($meta['additional_info']['recording_mbid'])) {
            $mbid = $meta['additional_info']['recording_mbid'];
        }

        $title = ('' !== $artist) ? $artist . ' — ' . $track : $track;
        $content = ('' !== $album)
            ? sprintf(
                /* translators: 1: artista e titolo del brano, 2: nome dell'album */
                __('%1$s (from %2$s)', 'eg-social-timeline'),
                $title,
                $album
            )
            : $title;

        $posts[] = array(
            'platform'         => 'listenbrainz',
            'platform_label'   => 'ListenBrainz',
            'date'             => $timestamp,
            'title'            => $title,
            'content'          => $content,
            // Pagina del brano su ListenBrainz; senza identificatore si torna
            // al profilo, che e' sempre valido.
            'link'             => $mbid ? $site . '/track/' . rawurlencode($mbid) : $profile_url,
            'is_boost'         => false,
            'image_url'        => '',
            'image_alt'        => '',
            'favourites_count' => 0,
            'reblogs_count'    => 0,
            'replies_count'    => 0,
        );
    }

    return $posts;
}

// Fetch and merge all feeds
function eg_social_timeline_fetch_all_feeds() {
    $cached = get_transient('eg_social_timeline_cache');

    if ($cached !== false) {
        return $cached;
    }

    $options   = get_option('eg_social_timeline_options');
    $profiles  = eg_social_timeline_profiles();
    $all_posts = array();
    $fetched   = array();

    // Piattaforme federate: tutte con la stessa firma (utente, istanza, limite).
    $fetchers = array(
        'mastodon' => 'eg_social_timeline_fetch_mastodon',
        'lemmy'    => 'eg_social_timeline_fetch_lemmy',
        'forgejo'  => 'eg_social_timeline_fetch_forgejo',
        'peertube' => 'eg_social_timeline_fetch_peertube',
        'pixelfed' => 'eg_social_timeline_fetch_pixelfed',
        'gotosocial' => 'eg_social_timeline_fetch_gotosocial',
        'friendica' => 'eg_social_timeline_fetch_friendica',
        'listenbrainz' => 'eg_social_timeline_fetch_listenbrainz',
    );

    foreach ($fetchers as $platform => $fetcher) {
        $profile = $profiles[$platform];

        if ('' === $profile['username'] || '' === $profile['instance']) {
            continue;
        }

        $arguments = array($profile['username'], $profile['instance'], $profile['limit']);

        // Solo PeerTube distingue account e canali.
        if ('peertube' === $platform) {
            $arguments[] = isset($profile['type']) ? $profile['type'] : 'auto';
        }

        $posts = call_user_func_array($fetcher, $arguments);
        $all_posts = array_merge($all_posts, $posts);
        $fetched[$platform] = count($posts);
    }

    // Bluesky non e' federato: un solo endpoint pubblico per tutti.
    if ('' !== $profiles['bluesky']['username']) {
        $posts = eg_social_timeline_fetch_bluesky($profiles['bluesky']['username'], $profiles['bluesky']['limit']);
        $all_posts = array_merge($all_posts, $posts);
        $fetched['bluesky'] = count($posts);
    }

    // Il feed esterno ha un indirizzo suo, non una coppia istanza + utente.
    if ('' !== $profiles['rss']['url']) {
        $posts = eg_social_timeline_fetch_rss($profiles['rss']['url'], $profiles['rss']['label'], $profiles['rss']['limit']);
        $all_posts = array_merge($all_posts, $posts);
        $fetched['rss'] = count($posts);
    }

    usort($all_posts, function($a, $b) {
        return $b['date'] - $a['date'];
    });

    $cache_duration = isset($options['cache_duration']) ? intval($options['cache_duration']) : 1800;
    set_transient('eg_social_timeline_cache', $all_posts, $cache_duration);

    // Esito per piattaforma, mostrato in Impostazioni: una piattaforma
    // configurata che non porta nulla e' un problema da vedere, non da
    // scoprire guardando la timeline.
    $issues = eg_social_timeline_record_issue();
    $report = array('time' => time(), 'platforms' => array());

    foreach ($fetched as $platform => $count) {
        $report['platforms'][$platform] = array(
            'count' => $count,
            'issue' => isset($issues[$platform]) ? $issues[$platform] : '',
        );
    }

    update_option('eg_social_timeline_status', $report, false);

    return $all_posts;
}

/**
 * Verifica i profili configurati interrogando le istanze.
 *
 * Una richiesta leggera per piattaforma, il cui esito viene salvato: la
 * pagina delle impostazioni mostra il risultato registrato e non interroga
 * nulla al caricamento, altrimenti aprirla costerebbe cinque o sei richieste
 * HTTP e sarebbe ostaggio dei timeout.
 *
 * @return array Esito per piattaforma.
 */
function eg_social_timeline_verify_profiles() {
    $profiles = eg_social_timeline_profiles();
    $report = array('time' => time(), 'platforms' => array());

    $get = function ($url) {
        $response = wp_remote_get($url, array('timeout' => 10, 'sslverify' => true, 'reject_unsafe_urls' => true));

        if (is_wp_error($response)) {
            return array('code' => 0, 'body' => '', 'error' => $response->get_error_message());
        }

        return array(
            'code'  => (int) wp_remote_retrieve_response_code($response),
            'body'  => wp_remote_retrieve_body($response),
            'error' => '',
        );
    };

    // Famiglia Mastodon: software dell'istanza e account.
    $profile = $profiles['mastodon'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $software = eg_social_timeline_detect_software($profile['instance']);
        $unsupported = eg_social_timeline_unsupported_software();
        $result = $get($profile['instance'] . '/api/v1/accounts/lookup?acct=' . rawurlencode($profile['username']));
        $data = json_decode($result['body'], true);

        if (isset($unsupported[$software])) {
            $report['platforms']['mastodon'] = array(
                'ok'     => false,
                'detail' => eg_social_timeline_fediverse_label($software),
                'note'   => $unsupported[$software],
            );
        } elseif (!empty($data['id'])) {
            $report['platforms']['mastodon'] = array(
                'ok'     => true,
                'detail' => sprintf('%s · %s · %s', wp_parse_url($profile['instance'], PHP_URL_HOST), $software ? eg_social_timeline_fediverse_label($software) : __('unknown software', 'eg-social-timeline'), $profile['username']),
                'note'   => '',
            );
        } else {
            $report['platforms']['mastodon'] = array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['instance'], PHP_URL_HOST),
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: 1: nome utente, 2: codice di stato HTTP */
                    __('Account "%1$s" not found (HTTP %2$d).', 'eg-social-timeline'),
                    $profile['username'],
                    $result['code']
                ),
            );
        }
    }

    // Lemmy: feed RSS dell'utente.
    $profile = $profiles['lemmy'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $result = $get($profile['instance'] . '/feeds/u/' . rawurlencode($profile['username']) . '.xml');
        $items = ('' !== $result['body']) ? substr_count($result['body'], '<item>') : 0;

        $report['platforms']['lemmy'] = (200 === $result['code'])
            ? array(
                'ok'     => true,
                'detail' => sprintf('%s · %s · %s', wp_parse_url($profile['instance'], PHP_URL_HOST), eg_social_timeline_lemmy_label($profile['instance']), $profile['username']),
                'note'   => sprintf(
                    /* translators: %d: numero di elementi nel feed */
                    _n('%d item in the feed', '%d items in the feed', $items, 'eg-social-timeline'),
                    $items
                ),
            )
            : array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['instance'], PHP_URL_HOST),
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: 1: nome utente, 2: codice di stato HTTP */
                    __('No feed for "%1$s" on this instance (HTTP %2$d). The Lemmy user feed exists only where the account is registered.', 'eg-social-timeline'),
                    $profile['username'],
                    $result['code']
                ),
            );
    }

    // Forgejo/Gitea: esistenza dell'utente.
    $profile = $profiles['forgejo'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $result = $get($profile['instance'] . '/api/v1/users/' . rawurlencode($profile['username']));

        $report['platforms']['forgejo'] = (200 === $result['code'])
            ? array('ok' => true, 'detail' => sprintf('%s · %s', wp_parse_url($profile['instance'], PHP_URL_HOST), $profile['username']), 'note' => '')
            : array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['instance'], PHP_URL_HOST),
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: 1: nome utente, 2: codice di stato HTTP */
                    __('User "%1$s" not found (HTTP %2$d).', 'eg-social-timeline'),
                    $profile['username'],
                    $result['code']
                ),
            );
    }

    // PeerTube: account oppure canale.
    $profile = $profiles['peertube'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $kinds = array(
            'account' => '/api/v1/accounts/',
            'channel' => '/api/v1/video-channels/',
        );

        $found = '';
        $total = 0;
        $last = array('code' => 0, 'error' => '');

        foreach ($kinds as $kind => $path) {
            $result = $get($profile['instance'] . $path . rawurlencode($profile['username']) . '/videos?count=1');
            $last = $result;

            if (200 === $result['code']) {
                $data = json_decode($result['body'], true);
                $found = $kind;
                $total = isset($data['total']) ? intval($data['total']) : 0;
                break;
            }
        }

        if ('' !== $found) {
            set_transient('eg_social_timeline_pt_kind_' . md5($profile['instance'] . '/' . $profile['username']), $found, MONTH_IN_SECONDS);

            $report['platforms']['peertube'] = array(
                'ok'     => true,
                'detail' => sprintf(
                    '%s · %s · %s',
                    wp_parse_url($profile['instance'], PHP_URL_HOST),
                    ('channel' === $found) ? __('channel', 'eg-social-timeline') : __('account', 'eg-social-timeline'),
                    $profile['username']
                ),
                'note'   => sprintf(
                    /* translators: %d: numero di video */
                    _n('%d video', '%d videos', $total, 'eg-social-timeline'),
                    $total
                ),
            );
        } else {
            $report['platforms']['peertube'] = array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['instance'], PHP_URL_HOST),
                'note'   => ('' !== $last['error']) ? $last['error'] : sprintf(
                    /* translators: 1: nome cercato, 2: codice di stato HTTP */
                    __('Neither an account nor a channel named "%1$s" (HTTP %2$d). On PeerTube videos usually live in a channel.', 'eg-social-timeline'),
                    $profile['username'],
                    $last['code']
                ),
            );
        }
    }

    // Pixelfed: feed Atom.
    $profile = $profiles['pixelfed'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $result = $get($profile['instance'] . '/users/' . rawurlencode($profile['username']) . '.atom');
        $entries = ('' !== $result['body']) ? substr_count($result['body'], '<entry>') : 0;

        $report['platforms']['pixelfed'] = (200 === $result['code'])
            ? array(
                'ok'     => true,
                'detail' => sprintf('%s · %s', wp_parse_url($profile['instance'], PHP_URL_HOST), $profile['username']),
                'note'   => sprintf(
                    /* translators: %d: numero di elementi nel feed */
                    _n('%d item in the Atom feed', '%d items in the Atom feed', $entries, 'eg-social-timeline'),
                    $entries
                ),
            )
            : array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['instance'], PHP_URL_HOST),
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: 1: nome utente, 2: codice di stato HTTP */
                    __('No Atom feed for "%1$s" (HTTP %2$d).', 'eg-social-timeline'),
                    $profile['username'],
                    $result['code']
                ),
            );
    }

    // GoToSocial: feed RSS, spento finche' l'utente non lo accende.
    $profile = $profiles['gotosocial'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $result = $get($profile['instance'] . '/@' . rawurlencode($profile['username']) . '/feed.rss');
        $items = ('' !== $result['body']) ? substr_count($result['body'], '<item>') : 0;

        if (200 === $result['code']) {
            $note = sprintf(
                /* translators: %d: numero di elementi nel feed */
                _n('%d item in the feed', '%d items in the feed', $items, 'eg-social-timeline'),
                $items
            );
        } elseif ('' !== $result['error']) {
            $note = $result['error'];
        } elseif (404 === $result['code']) {
            $note = sprintf(
                /* translators: %s: indirizzo delle impostazioni dell'account */
                __('The RSS feed of this account is turned off. Turn it on in the account settings (%s).', 'eg-social-timeline'),
                $profile['instance'] . '/settings'
            );
        } else {
            $note = sprintf(
                /* translators: 1: nome utente, 2: codice di stato HTTP */
                __('Account "%1$s" not found (HTTP %2$d).', 'eg-social-timeline'),
                $profile['username'],
                $result['code']
            );
        }

        $report['platforms']['gotosocial'] = array(
            'ok'     => (200 === $result['code']),
            'detail' => (200 === $result['code'])
                ? sprintf('%s · %s', wp_parse_url($profile['instance'], PHP_URL_HOST), $profile['username'])
                : wp_parse_url($profile['instance'], PHP_URL_HOST),
            'note'   => $note,
        );
    }

    // Friendica: feed Atom del profilo.
    $profile = $profiles['friendica'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $result = $get($profile['instance'] . '/feed/' . rawurlencode($profile['username']) . '/');
        $entries = ('' !== $result['body']) ? preg_match_all('~<entry[\s>]~', $result['body']) : 0;

        $report['platforms']['friendica'] = (200 === $result['code'])
            ? array(
                'ok'     => true,
                'detail' => sprintf('%s · %s', wp_parse_url($profile['instance'], PHP_URL_HOST), $profile['username']),
                'note'   => sprintf(
                    /* translators: %d: numero di elementi nel feed */
                    _n('%d item in the Atom feed', '%d items in the Atom feed', $entries, 'eg-social-timeline'),
                    $entries
                ),
            )
            : array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['instance'], PHP_URL_HOST),
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: 1: nome utente, 2: codice di stato HTTP */
                    __('Account "%1$s" not found (HTTP %2$d).', 'eg-social-timeline'),
                    $profile['username'],
                    $result['code']
                ),
            );
    }

    // ListenBrainz: ascolti pubblici.
    $profile = $profiles['listenbrainz'];

    if ('' !== $profile['username'] && '' !== $profile['instance']) {
        $result = $get($profile['instance'] . '/1/user/' . rawurlencode($profile['username']) . '/listens?count=1');
        $data = json_decode($result['body'], true);

        $report['platforms']['listenbrainz'] = (200 === $result['code'] && isset($data['payload']['listens']))
            ? array(
                'ok'     => true,
                'detail' => sprintf('%s · %s', wp_parse_url($profile['instance'], PHP_URL_HOST), $profile['username']),
                'note'   => '',
            )
            : array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['instance'], PHP_URL_HOST),
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: 1: nome utente, 2: codice di stato HTTP */
                    __('No listens returned for "%1$s" (HTTP %2$d).', 'eg-social-timeline'),
                    $profile['username'],
                    $result['code']
                ),
            );
    }

    // Feed esterno.
    $profile = $profiles['rss'];

    if ('' !== $profile['url']) {
        $result = $get($profile['url']);
        $items = ('' !== $result['body']) ? (substr_count($result['body'], '<item>') + substr_count($result['body'], '<entry>')) : 0;

        $report['platforms']['rss'] = (200 === $result['code'] && $items > 0)
            ? array(
                'ok'     => true,
                'detail' => sprintf('%s · %s', wp_parse_url($profile['url'], PHP_URL_HOST), ('' !== $profile['label']) ? $profile['label'] : __('label from the feed', 'eg-social-timeline')),
                'note'   => sprintf(
                    /* translators: %d: numero di elementi nel feed */
                    _n('%d item in the feed', '%d items in the feed', $items, 'eg-social-timeline'),
                    $items
                ),
            )
            : array(
                'ok'     => false,
                'detail' => wp_parse_url($profile['url'], PHP_URL_HOST),
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: %d: codice di stato HTTP */
                    __('No RSS or Atom items found at this address (HTTP %d).', 'eg-social-timeline'),
                    $result['code']
                ),
            );
    }

    // Bluesky: feed pubblico dell'autore.
    $profile = $profiles['bluesky'];

    if ('' !== $profile['username']) {
        $result = $get(EG_SOCIAL_TIMELINE_BLUESKY_SERVICE . '/xrpc/app.bsky.feed.getAuthorFeed?' . http_build_query(array(
            'actor' => $profile['username'],
            'limit' => 1,
        )));

        $report['platforms']['bluesky'] = (200 === $result['code'])
            ? array('ok' => true, 'detail' => $profile['username'], 'note' => '')
            : array(
                'ok'     => false,
                'detail' => $profile['username'],
                'note'   => ('' !== $result['error']) ? $result['error'] : sprintf(
                    /* translators: %d: codice di stato HTTP */
                    __('The public API did not return this handle (HTTP %d).', 'eg-social-timeline'),
                    $result['code']
                ),
            );
    }

    update_option('eg_social_timeline_diagnostics', $report, false);

    return $report;
}

/**
 * Piattaforme configurate, con il motivo quando il profilo e' a meta'.
 *
 * Una piattaforma senza nessun dato non compare. Il valore e' una stringa
 * vuota per un profilo completo, il motivo per uno compilato a meta'.
 *
 * @return array Coppie slug => motivo ('' se completo).
 */
function eg_social_timeline_profile_rows() {
    $profiles = eg_social_timeline_profiles();
    $rows = array();

    foreach ($profiles as $platform => $profile) {
        // Il feed esterno si configura con il solo indirizzo.
        if ('rss' === $platform) {
            if ('' !== $profile['url']) {
                $rows['rss'] = '';
            }

            continue;
        }

        $has_username = '' !== $profile['username'];

        // L'istanza di Forgejo ha un valore predefinito: da sola non significa
        // che la piattaforma sia stata configurata.
        $defaults = array(
            'forgejo'      => 'https://gitea.com',
            'listenbrainz' => EG_SOCIAL_TIMELINE_LISTENBRAINZ_API,
        );

        $has_instance = ('bluesky' === $platform)
            ? $has_username
            : ('' !== $profile['instance'] && !(isset($defaults[$platform]) && $defaults[$platform] === $profile['instance'] && !$has_username));

        if (!$has_username && !$has_instance) {
            continue;
        }

        if ($has_username && $has_instance) {
            $rows[$platform] = '';
            continue;
        }

        // Meta' compilato: finora veniva scartato senza dire niente.
        $rows[$platform] = $has_username
            ? __('Incomplete: the instance URL is missing, so this platform is skipped.', 'eg-social-timeline')
            : __('Incomplete: the username is missing, so this platform is skipped.', 'eg-social-timeline');
    }

    return $rows;
}

/**
 * Tabella in Impostazioni con l'esito delle verifiche e dell'ultimo recupero.
 */
function eg_social_timeline_render_diagnostics() {
    $profiles = eg_social_timeline_profiles();
    $diagnostics = get_option('eg_social_timeline_diagnostics');
    $status = get_option('eg_social_timeline_status');
    $rows = array();

    $rows = eg_social_timeline_profile_rows();

    if (empty($rows)) {
        return;
    }

    // Piattaforme che non stanno portando nulla: profilo a meta', oppure
    // ultimo recupero a zero. Un recupero mai avvenuto non e' un problema.
    $in_difficolta = array();

    foreach ($rows as $platform => $incomplete) {
        $fetch = isset($status['platforms'][$platform]) ? $status['platforms'][$platform] : null;

        if ('' !== $incomplete || (null !== $fetch && empty($fetch['count']))) {
            $in_difficolta[] = eg_social_timeline_get_platform_name($platform);
        }
    }

    $options = get_option('eg_social_timeline_options');

    // Con la diagnostica spenta la pagina resta corta, ma un problema non
    // deve restare muto: di quello si accorge solo chi guarda la timeline.
    if (empty($options['show_diagnostics'])) {
        if (!empty($in_difficolta)) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <?php
                    printf(
                        /* translators: %s: elenco delle piattaforme che non hanno portato contenuti */
                        esc_html__('%s returned nothing on the last refresh. Turn on "Show diagnostics" in the profiles section below to see why.', 'eg-social-timeline'),
                        '<strong>' . esc_html(implode(', ', $in_difficolta)) . '</strong>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- gia' escapato qui sopra
                    );
                    ?>
                </p>
            </div>
            <?php
        }

        return;
    }
    ?>
    <h2><?php esc_html_e('Configured profiles', 'eg-social-timeline'); ?></h2>
    <table class="widefat striped" style="max-width: 900px;">
        <thead>
            <tr>
                <th><?php esc_html_e('Platform', 'eg-social-timeline'); ?></th>
                <th><?php esc_html_e('What the plugin sees', 'eg-social-timeline'); ?></th>
                <th><?php esc_html_e('Last fetch', 'eg-social-timeline'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $platform => $incomplete): ?>
                <?php
                $check = isset($diagnostics['platforms'][$platform]) ? $diagnostics['platforms'][$platform] : null;
                $fetch = isset($status['platforms'][$platform]) ? $status['platforms'][$platform] : null;
                ?>
                <tr>
                    <td><strong><?php echo esc_html(eg_social_timeline_get_platform_name($platform)); ?></strong></td>
                    <td>
                        <?php if ('' !== $incomplete): ?>
                            <span style="color: #b32d2e;"><?php echo esc_html($incomplete); ?></span>
                        <?php elseif (null === $check): ?>
                            <em><?php esc_html_e('Not verified yet.', 'eg-social-timeline'); ?></em>
                        <?php else: ?>
                            <?php echo esc_html(($check['ok'] ? '✓ ' : '✗ ') . $check['detail']); ?>
                            <?php if (!empty($check['note'])): ?>
                                <br><span class="description"><?php echo esc_html($check['note']); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ('' !== $incomplete): ?>
                            —
                        <?php elseif (null === $fetch): ?>
                            <em><?php esc_html_e('No fetch since the cache was emptied: open the page with the timeline, or use "Flush Cache Now" below.', 'eg-social-timeline'); ?></em>
                        <?php elseif (!empty($fetch['count'])): ?>
                            <?php
                            printf(
                                /* translators: %d: numero di contenuti recuperati */
                                esc_html(_n('%d item', '%d items', $fetch['count'], 'eg-social-timeline')),
                                intval($fetch['count'])
                            );
                            ?>
                        <?php else: ?>
                            <span style="color: #b32d2e;">
                                <?php echo esc_html(!empty($fetch['issue']) ? $fetch['issue'] : __('No content retrieved.', 'eg-social-timeline')); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="description">
        <?php
        if (!empty($diagnostics['time'])) {
            printf(
                /* translators: %s: data e ora dell'ultima verifica */
                esc_html__('Profiles verified on %s.', 'eg-social-timeline'),
                esc_html(date_i18n(get_option('date_format') . ' H:i', $diagnostics['time']))
            );
        } else {
            esc_html_e('Save the settings or use "Verify profiles" to run the checks.', 'eg-social-timeline');
        }
        ?>
    </p>
    <form method="post" style="display: inline;">
        <?php wp_nonce_field('eg_social_timeline_verify', 'eg_social_timeline_verify_nonce'); ?>
        <input type="hidden" name="eg_social_timeline_verify" value="1">
        <button type="submit" class="button"><?php esc_html_e('Verify profiles', 'eg-social-timeline'); ?></button>
    </form>
    <?php
}

// Verifica su richiesta e dopo il salvataggio delle impostazioni
add_action('admin_init', 'eg_social_timeline_handle_verify');

function eg_social_timeline_handle_verify() {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    $screen_is_settings = isset($_GET['page']) && 'eg-social-timeline' === sanitize_key(wp_unslash($_GET['page'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sola lettura del parametro di pagina

    // Richiesta esplicita dal pulsante.
    if (isset($_POST['eg_social_timeline_verify'])) {
        if (
            !isset($_POST['eg_social_timeline_verify_nonce'])
            || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['eg_social_timeline_verify_nonce'])), 'eg_social_timeline_verify')
        ) {
            wp_die(esc_html__('Security check failed.', 'eg-social-timeline'));
        }

        eg_social_timeline_verify_profiles();

        return;
    }

    // Dopo un salvataggio riuscito, una volta.
    if ($screen_is_settings && isset($_GET['settings-updated'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- parametro aggiunto da options.php, nessuna azione distruttiva
        eg_social_timeline_verify_profiles();
    }
}

// Shortcode
add_shortcode('eg_social_timeline', 'eg_social_timeline_shortcode');

function eg_social_timeline_shortcode($atts) {
    $options = get_option('eg_social_timeline_options');
    
    if (!eg_social_timeline_has_profiles()) {
        if (current_user_can('manage_options')) {
            return '<div style="background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0;">
                <strong>' . esc_html__('EG Social Timeline — Configuration Required', 'eg-social-timeline') . '</strong><br>
                ' . esc_html__('No social profile configured.', 'eg-social-timeline') . ' 
                <a href="' . esc_url(admin_url('options-general.php?page=eg-social-timeline')) . '">' . esc_html__('Configure now', 'eg-social-timeline') . '</a>
            </div>';
        }
        return '';
    }
    
    $atts = shortcode_atts(array(
        'limit'  => $options['post_limit'],
        // Vuoto: vale l'impostazione del pannello. "list" o "grid" la sostituiscono per questa pagina.
        'layout' => '',
    ), $atts, 'eg_social_timeline');

    $layout = sanitize_key($atts['layout']);
    
    $limit = intval($atts['limit']);
    
    $posts = eg_social_timeline_fetch_all_feeds();
    
    if (empty($posts)) {
        return '<div class="' . esc_attr(eg_social_timeline_wrapper_classes($layout)) . '">' .
               '<div class="eg-social-timeline-empty">' .
               esc_html__('No posts available at the moment.', 'eg-social-timeline') .
               '</div></div>';
    }
    
    $posts = array_slice($posts, 0, $limit);
    
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr(eg_social_timeline_wrapper_classes($layout)); ?>">
        
        <?php
        // Count posts per platform for filters
        $platform_counts = array();
        foreach ($posts as $post) {
            $platform = $post['platform'];
            if (!isset($platform_counts[$platform])) {
                $platform_counts[$platform] = 0;
            }
            $platform_counts[$platform]++;
        }
        
        // Cosa mostrare e quanto testo: una scelta per piattaforma, calcolata
        // una volta sola e non a ogni scheda.
        $display = array();

        foreach ($platform_counts as $platform => $count) {
            $display[$platform] = array(
                'stats'    => eg_social_timeline_display_option($platform, 'stats'),
                'images'   => eg_social_timeline_display_option($platform, 'images'),
                'truncate' => eg_social_timeline_truncate_option($platform),
            );
        }

        // Etichette e software arrivano dai post: Lemmy mostra il nome della
        // sua istanza, la famiglia Mastodon quello del software rilevato.
        $platform_names = array();
        $platform_software = array();

        foreach ($posts as $post) {
            $platform = $post['platform'];

            if (!isset($platform_names[$platform])) {
                $platform_names[$platform] = !empty($post['platform_label'])
                    ? $post['platform_label']
                    : eg_social_timeline_get_platform_name($platform);
            }

            if (!isset($platform_software[$platform]) && !empty($post['software'])) {
                $platform_software[$platform] = $post['software'];
            }
        }
        ?>
        
        <!-- Checkbox FUORI dal container (siblings degli article) -->
        <?php foreach ($platform_counts as $platform => $count): ?>
            <input type="checkbox" 
                   id="filter-<?php echo esc_attr($platform); ?>" 
                   class="filter-checkbox-input"
                   checked>
        <?php endforeach; ?>
        
        <!-- CSS-only Filters Box (solo label, checkbox sopra) -->
        <div class="eg-timeline-filters">
            <div class="filters-header">
                <span class="filters-icon">🔍</span>
                <h3>
                    <?php
                    if ('compact' === eg_social_timeline_appearance_settings()['filters_style']) {
                        esc_html_e('Filter:', 'eg-social-timeline');
                    } else {
                        esc_html_e('Filter by platform:', 'eg-social-timeline');
                    }
                    ?>
                </h3>
            </div>
            
            <div class="filters-checkboxes">
                <?php foreach ($platform_counts as $platform => $count): ?>
                    <?php
                    $software = isset($platform_software[$platform]) ? $platform_software[$platform] : '';
                    $name = isset($platform_names[$platform]) ? $platform_names[$platform] : ucfirst($platform);

                    // Nello stile compatto il nome sparisce alla vista: resta nel
                    // title per il mouse e nel documento per gli screen reader.
                    $tooltip = sprintf(
                        /* translators: 1: nome della piattaforma, 2: numero di contenuti */
                        _x('%1$s (%2$d)', 'nome piattaforma e conteggio nel tooltip del filtro', 'eg-social-timeline'),
                        $name,
                        intval($count)
                    );
                    ?>
                    <label for="filter-<?php echo esc_attr($platform); ?>"
                           class="<?php echo esc_attr('filter-checkbox-label' . ($software ? ' egst-sw-' . $software : '')); ?>"
                           title="<?php echo esc_attr($tooltip); ?>">
                        <span class="platform-icon-small">
                            <?php echo eg_social_timeline_get_icon($platform, $software); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG sanitizzato internamente dalla funzione ?>
                        </span>
                        <span class="platform-label"><?php echo esc_html($name); ?></span>
                        <span class="post-count">(<?php echo intval($count); ?>)</span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php foreach ($posts as $post): ?>
            <?php
            $item_classes = 'timeline-item timeline-' . $post['platform'];

            if (!empty($post['software'])) {
                $item_classes .= ' egst-sw-' . $post['software'];
            }

            if ($post['is_boost']) {
                $item_classes .= ' is-boost';
            }

            $item_label = !empty($post['platform_label'])
                ? $post['platform_label']
                : eg_social_timeline_get_platform_name($post['platform']);

            $item_display = isset($display[$post['platform']])
                ? $display[$post['platform']]
                : array('stats' => true, 'images' => true, 'truncate' => 300);
            ?>
            <article class="<?php echo esc_attr($item_classes); ?>"
                     data-platform="<?php echo esc_attr($post['platform']); ?>">
                <header class="timeline-header">
                    <span class="platform-icon platform-<?php echo esc_attr($post['platform']); ?>">
                        <?php echo eg_social_timeline_get_icon($post['platform'], isset($post['software']) ? $post['software'] : ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG sanitizzato internamente dalla funzione ?>
                    </span>
                    <span class="platform-name"><?php echo esc_html($item_label); ?></span>
                    
                    <?php if ($post['is_boost']): ?>
                        <span class="boost-badge">🔁 Boost</span>
                    <?php endif; ?>
                    
                    <time datetime="<?php echo esc_attr(gmdate('c', $post['date'])); ?>" class="post-date">
                        <?php echo esc_html(eg_social_timeline_format_date($post['date'])); ?>
                    </time>
                </header>
                <div class="timeline-content">
                    <?php if (!empty($post['content'])): ?>
                    <div class="post-text">
                        <?php echo esc_html($item_display['truncate'] > 0 ? eg_social_timeline_truncate($post['content'], $item_display['truncate']) : wp_strip_all_tags($post['content'])); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($item_display['images'] && !empty($post['image_url'])): ?>
                    <div class="post-image">
                        <img src="<?php echo esc_url($post['image_url']); ?>"
                             alt="<?php echo esc_attr(!empty($post['image_alt']) ? $post['image_alt'] : __('Attached image', 'eg-social-timeline')); ?>"
                             loading="lazy">
                    </div>
                    <?php endif; ?>
                </div>
                <footer class="timeline-footer">
                    <?php if ($item_display['stats'] && ($post['favourites_count'] > 0 || $post['reblogs_count'] > 0 || $post['replies_count'] > 0)): ?>
                        <div class="post-stats">
                            <?php if ($post['favourites_count'] > 0): ?>
                                <span class="stat-item stat-favourites" title="<?php 
                                    echo esc_attr($post['platform'] === 'lemmy' ? __('Points', 'eg-social-timeline') : __('Favourites', 'eg-social-timeline')); 
                                ?>">
                                    <?php echo $post['platform'] === 'lemmy' ? '⭐' : '❤️'; ?> <?php echo esc_html($post['favourites_count']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($post['reblogs_count'] > 0): ?>
                                <span class="stat-item stat-boosts" title="<?php esc_attr_e('Boost', 'eg-social-timeline'); ?>">
                                    🔁 <?php echo esc_html($post['reblogs_count']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($post['replies_count'] > 0): ?>
                                <span class="stat-item stat-replies" title="<?php 
                                    echo esc_attr($post['platform'] === 'lemmy' ? __('Comments', 'eg-social-timeline') : __('Replies', 'eg-social-timeline')); 
                                ?>">
                                    💬 <?php echo esc_html($post['replies_count']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($post['link']); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="view-original">
                        <?php 
                        if ($post['platform'] === 'forgejo') {
                            esc_html_e('View commit', 'eg-social-timeline');
                        } elseif ($post['platform'] === 'peertube') {
                            esc_html_e('Watch video', 'eg-social-timeline');
                        } elseif ($post['platform'] === 'pixelfed') {
                            esc_html_e('View photo', 'eg-social-timeline');
                        } elseif ($post['platform'] === 'listenbrainz') {
                            esc_html_e('View the recording', 'eg-social-timeline');
                        } elseif ($post['platform'] === 'rss') {
                            esc_html_e('Read the post', 'eg-social-timeline');
                        } else {
                            esc_html_e('View original post', 'eg-social-timeline');
                        }
                        ?> →
                    </a>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

// Helper functions
function eg_social_timeline_get_platform_name($platform) {
    $names = array(
        'mastodon' => __('Mastodon', 'eg-social-timeline'),
        'lemmy' => __('Lemmy', 'eg-social-timeline'),
        'bluesky' => __('Bluesky', 'eg-social-timeline'),
        'forgejo' => __('Forgejo', 'eg-social-timeline'),
        'peertube' => __('PeerTube', 'eg-social-timeline'),
        'pixelfed' => __('Pixelfed', 'eg-social-timeline'),
        'gotosocial' => __('GoToSocial', 'eg-social-timeline'),
        'friendica' => __('Friendica', 'eg-social-timeline'),
        'listenbrainz' => __('ListenBrainz', 'eg-social-timeline'),
        'rss' => __('Feed', 'eg-social-timeline')
    );
    
    return isset($names[$platform]) ? $names[$platform] : $platform;
}

function eg_social_timeline_get_icon($platform, $software = '') {
    // Mappatura piattaforme -> file SVG
    $icon_files = array(
        'mastodon' => 'mastodon.svg',
        'lemmy' => 'lemmy.svg',
        'bluesky' => 'bluesky.svg',
        'forgejo' => 'forgejo.svg',
        'peertube' => 'peertube.svg',
        'pixelfed' => 'pixelfed.svg',
        'gotosocial' => 'gotosocial.svg',
        'friendica' => 'friendica.svg',
        'listenbrainz' => 'listenbrainz.svg',
        'rss' => 'rss.svg'
    );

    // Pleroma e Akkoma condividono l'API con Mastodon ma non il logo. Akkoma
    // non ha un'icona propria nel set usato dal plugin: prende quella del
    // progetto da cui nasce.
    $software_icons = array(
        'pleroma' => 'pleroma.svg',
        'akkoma'  => 'pleroma.svg',
    );

    if ('' !== $software && isset($software_icons[$software])) {
        $platform = 'software';
        $icon_files['software'] = $software_icons[$software];
    }
    
    // Percorso cartella icone
    $icons_dir = EG_SOCIAL_TIMELINE_DIR . 'social-icons/';
    
    // Tag e attributi SVG consentiti per wp_kses
    $svg_kses = array(
        'svg'    => array( 'width' => true, 'height' => true, 'viewbox' => true, 'xmlns' => true, 'fill' => true, 'role' => true, 'aria-hidden' => true, 'class' => true ),
        'title'  => array(),
        'path'   => array( 'd' => true, 'fill' => true, 'fill-rule' => true, 'clip-rule' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true ),
        'circle' => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'opacity' => true ),
        'rect'   => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true ),
        'g'      => array( 'fill' => true, 'transform' => true, 'clip-path' => true ),
        'defs'   => array(),
        'clippath' => array( 'id' => true ),
    );

    // Verifica file esiste
    if (isset($icon_files[$platform])) {
        $icon_path = $icons_dir . $icon_files[$platform];

        if (file_exists($icon_path)) {
            $svg = wp_kses(file_get_contents($icon_path), $svg_kses);

            // L'icona accompagna sempre il nome della piattaforma, scritto
            // accanto o nascosto alla sola vista: come elemento a se' verrebbe
            // letta due volte dagli screen reader per via del <title> interno.
            return str_replace('<svg ', '<svg aria-hidden="true" focusable="false" ', $svg);
        }
    }

    // Fallback: cerchia generica
    return '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.3"/></svg>';
}

function eg_social_timeline_format_date($timestamp) {
    $diff = time() - $timestamp;
    
    if ($diff < 3600) {
        $mins = round($diff / 60);
        /* translators: %s: numero di minuti */
        return sprintf(_n('%s minute ago', '%s minutes ago', $mins, 'eg-social-timeline'), $mins);
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        /* translators: %s: numero di ore */
        return sprintf(_n('%s hour ago', '%s hours ago', $hours, 'eg-social-timeline'), $hours);
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        /* translators: %s: numero di giorni */
        return sprintf(_n('%s day ago', '%s days ago', $days, 'eg-social-timeline'), $days);
    } else {
        return date_i18n(get_option('date_format'), $timestamp);
    }
}

function eg_social_timeline_truncate($text, $length = 200) {
    $text = wp_strip_all_tags($text);
    
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    $truncated = mb_substr($text, 0, $length);
    $lastSpace = mb_strrpos($truncated, ' ');
    
    if ($lastSpace !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }
    
    return $truncated . '…';
}

/**
 * Classi del contenitore della timeline: stile icone, sfondo e layout.
 *
 * @param string $layout 'list' o 'grid' dall'attributo dello shortcode;
 *                       vuoto usa l'impostazione del pannello.
 * @return string Elenco di classi CSS separate da spazio.
 */
function eg_social_timeline_wrapper_classes($layout = '') {
    $settings = eg_social_timeline_appearance_settings();
    $classes  = array('eg-social-timeline', 'egst-icons-' . $settings['icon_style']);

    if (!in_array($layout, array('list', 'grid'), true)) {
        $layout = $settings['layout'];
    }

    if ('grid' === $layout) {
        $classes[] = 'egst-layout-grid';
    }

    if ('compact' === $settings['filters_style']) {
        $classes[] = 'egst-filters-compact';
    }

    if ('none' !== $settings['canvas_bg']) {
        $classes[] = 'egst-has-canvas';
    }

    return implode(' ', $classes);
}

/**
 * Luminanza relativa di un colore esadecimale, secondo WCAG 2.1.
 *
 * @param string $hex Colore nel formato #rgb o #rrggbb.
 * @return float Valore fra 0 (nero) e 1 (bianco).
 */
function eg_social_timeline_relative_luminance($hex) {
    $hex = ltrim((string) $hex, '#');

    if (3 === strlen($hex)) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    if (6 !== strlen($hex)) {
        return 1.0;
    }

    $channels = array(
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
    );

    $weights = array(0.2126, 0.7152, 0.0722);
    $luminance = 0.0;

    foreach ($channels as $index => $value) {
        $channel = $value / 255;
        $channel = ($channel <= 0.03928) ? $channel / 12.92 : pow((($channel + 0.055) / 1.055), 2.4);
        $luminance += $weights[$index] * $channel;
    }

    return $luminance;
}

/**
 * Rapporto di contrasto fra due colori, secondo WCAG 2.1.
 *
 * @param string $hex_a Primo colore.
 * @param string $hex_b Secondo colore.
 * @return float Valore fra 1 e 21.
 */
function eg_social_timeline_contrast_ratio($hex_a, $hex_b) {
    $a = eg_social_timeline_relative_luminance($hex_a);
    $b = eg_social_timeline_relative_luminance($hex_b);

    $lighter = max($a, $b);
    $darker  = min($a, $b);

    return ($lighter + 0.05) / ($darker + 0.05);
}

/**
 * Palette di primo piano che sta meglio su una superficie.
 *
 * Nessuna soglia arbitraria: si confronta il contrasto del colore della
 * superficie con il testo della palette chiara e con quello della palette
 * scura, e vince quella che contrasta di piu'.
 *
 * @param string $hex Colore della superficie.
 * @return string 'light' oppure 'dark'.
 */
function eg_social_timeline_surface_polarity($hex) {
    $on_light = eg_social_timeline_contrast_ratio($hex, '#374151');
    $on_dark  = eg_social_timeline_contrast_ratio($hex, '#d1d5db');

    return ($on_dark > $on_light) ? 'dark' : 'light';
}

/**
 * Colore di una superficie in un dato contesto.
 *
 * @param array  $settings Impostazioni aspetto normalizzate.
 * @param string $surface  'canvas' oppure 'card'.
 * @param string $context  'light' oppure 'dark' (il contesto della @media).
 * @return string|null Colore, oppure null se la superficie e' trasparente.
 */
function eg_social_timeline_surface_color($settings, $surface, $context) {
    $mode = $settings[$surface . '_bg'];

    if ('none' === $mode) {
        return null;
    }

    if ('custom' === $mode) {
        return $settings[$surface . '_bg_color'];
    }

    $presets = array(
        'canvas' => array('light' => '#f3f4f6', 'dark' => '#111827'),
        'card'   => array('light' => '#ffffff', 'dark' => '#1f2937'),
    );

    // Il preset neutro e' un colore fisso chiaro; "auto" segue il contesto.
    $key = ('auto' === $mode) ? $context : 'light';

    return $presets[$surface][$key];
}

/**
 * Custom properties da sovrascrivere in un contesto.
 *
 * @param array  $settings Impostazioni aspetto normalizzate.
 * @param string $context  'light' oppure 'dark'.
 * @return array Coppie nome => valore.
 */
function eg_social_timeline_scope_declarations($settings, $context) {
    $canvas = eg_social_timeline_surface_color($settings, 'canvas', $context);
    $card   = eg_social_timeline_surface_color($settings, 'card', $context);

    // La scheda si misura sul proprio colore; se e' trasparente si guarda il
    // fondo della timeline, e se non c'e' nemmeno quello l'unico indizio su
    // cosa ci sia sotto e' il tema del browser, cioe' il contesto.
    $card_reference    = $card ? $card : $canvas;
    $filters_reference = $canvas ? $canvas : $card_reference;

    $card_polarity    = $card_reference ? eg_social_timeline_surface_polarity($card_reference) : $context;
    $filters_polarity = $filters_reference ? eg_social_timeline_surface_polarity($filters_reference) : $context;

    $icons = array('mono', 'mastodon', 'pleroma', 'gotosocial', 'friendica', 'lemmy', 'bluesky', 'forgejo', 'peertube', 'pixelfed', 'listenbrainz', 'rss');
    $declarations = array();

    // Primo piano della scheda.
    if ('dark' === $card_polarity) {
        $tokens = array(
            'text', 'heading', 'muted', 'stat', 'link', 'link-hover',
            'badge-bg', 'badge-text', 'card-border', 'card-border-hover',
            'card-shadow', 'card-shadow-hover', 'brand', 'empty-bg', 'empty-text',
        );

        foreach ($icons as $icon) {
            $tokens[] = 'icon-' . $icon;
        }

        foreach ($tokens as $token) {
            $declarations['--egst-' . $token] = 'var(--egst-dark-' . $token . ')';
        }
    }

    // Primo piano del box filtri e dei chip.
    if ('dark' === $filters_polarity) {
        $tokens = array(
            'filters-bg', 'filters-border', 'filters-title', 'filters-shadow',
            'chip-bg', 'chip-bg-hover', 'chip-bg-off', 'chip-border', 'chip-text', 'count',
        );

        foreach ($tokens as $token) {
            $declarations['--egst-' . $token] = 'var(--egst-dark-' . $token . ')';
        }

        $declarations['--egst-chip-brand'] = 'var(--egst-dark-brand)';

        foreach ($icons as $icon) {
            $declarations['--egst-chip-icon-' . $icon] = 'var(--egst-dark-icon-' . $icon . ')';
        }
    }

    // Superfici: vanno dopo i gruppi, cosi' una scheda trasparente resta senza
    // ombra anche quando il gruppo scuro ne avrebbe impostata una.
    if (null !== $canvas) {
        $declarations['--egst-canvas'] = $canvas;
    }

    if (null === $card) {
        $declarations['--egst-card-bg'] = 'transparent';
        $declarations['--egst-card-shadow'] = 'none';
        $declarations['--egst-card-shadow-hover'] = 'none';
    } elseif ('#ffffff' !== strtolower($card)) {
        $declarations['--egst-card-bg'] = $card;
    }

    return $declarations;
}

/**
 * Serializza un elenco di custom properties in dichiarazioni CSS.
 *
 * @param array $vars Coppie nome => valore.
 * @return string
 */
function eg_social_timeline_css_declarations($vars) {
    $declarations = '';

    foreach ($vars as $name => $value) {
        $declarations .= $name . ':' . $value . ';';
    }

    return $declarations;
}

/**
 * CSS inline che applica le scelte della sezione Aspetto.
 *
 * Il foglio di stile statico e' tutto sulla palette chiara: qui si spostano i
 * token attivi sulla palette scura dove serve, in base al contrasto del
 * colore scelto. Il blocco @media viene emesso solo se il contesto scuro
 * risulta diverso, cioe' quando almeno una superficie segue il browser o
 * quando sono entrambe trasparenti e non c'e' nessun colore da misurare.
 *
 * @return string CSS, stringa vuota se non c'e' nulla da sovrascrivere.
 */
function eg_social_timeline_appearance_css() {
    $settings = eg_social_timeline_appearance_settings();

    $light = eg_social_timeline_scope_declarations($settings, 'light');
    $dark  = eg_social_timeline_scope_declarations($settings, 'dark');

    $css = '';

    if (!empty($light)) {
        $css .= '.eg-social-timeline{' . eg_social_timeline_css_declarations($light) . '}';
    }

    if ($dark !== $light) {
        $css .= '@media (prefers-color-scheme: dark){.eg-social-timeline{' . eg_social_timeline_css_declarations($dark) . '}}';
    }

    return $css;
}

// Enqueue CSS
add_action('wp_enqueue_scripts', 'eg_social_timeline_enqueue_styles');

function eg_social_timeline_enqueue_styles() {
    if (is_singular() || is_page()) {
        wp_enqueue_style(
            'eg-social-timeline-style', 
            plugin_dir_url(__FILE__) . 'eg-social-timeline.css',
            array(),
            EG_SOCIAL_TIMELINE_VERSION
        );

        $appearance_css = eg_social_timeline_appearance_css();

        if ('' !== $appearance_css) {
            wp_add_inline_style('eg-social-timeline-style', $appearance_css);
        }
    }
}

// Plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'eg_social_timeline_action_links');

function eg_social_timeline_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=eg-social-timeline')) . '">' . __('Settings', 'eg-social-timeline') . '</a>';
    $docs_link = '<a href="https://git.emanuelegori.uno/emanuelegori/eg-social-timeline" target="_blank" rel="noopener noreferrer">' . __('Documentation', 'eg-social-timeline') . '</a>';
    
    array_unshift($links, $docs_link, $settings_link);
    
    return $links;
}

