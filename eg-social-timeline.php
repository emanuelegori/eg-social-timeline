<?php
/**
 * Plugin Name: EG Social Timeline
 * Plugin URI: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
 * Description: Unified chronological timeline of your public activity from Mastodon, Bluesky, PeerTube, Forgejo and Lemmy. Zero JavaScript, zero tracking.
 * Version: 1.9.1
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
define('EG_SOCIAL_TIMELINE_VERSION', '1.9.1');
define('EG_SOCIAL_TIMELINE_DIR', plugin_dir_path(__FILE__));
define('EG_SOCIAL_TIMELINE_URL', plugin_dir_url(__FILE__));
define('EG_SOCIAL_TIMELINE_DEBUG', false);
define('EG_SOCIAL_TIMELINE_BLUESKY_SERVICE', 'https://public.api.bsky.app');

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
                'post_limit' => 10,
                'cache_duration' => 3600,
                'show_boosts' => false,
                'show_stats' => true,
                'mastodon_limit' => 20,
                'lemmy_limit' => 10,
                'forgejo_limit' => 5,
                'bluesky_handle' => '',
                'bluesky_limit' => 10,
                'peertube_username' => '',
                'peertube_instance' => '',
                'peertube_limit' => 5,
                'truncate_length' => 300,
                'show_images' => false,
                'icon_style' => 'brand',
                'canvas_bg' => 'none',
                'canvas_bg_color' => '#f3f4f6',
                'card_bg' => 'neutral',
                'card_bg_color' => '#ffffff'
            )
        )
    );
    
    add_settings_section(
        'eg_social_timeline_main_section',
        __('Social Profiles Configuration', 'eg-social-timeline'),
        'eg_social_timeline_main_section_callback',
        'eg-social-timeline'
    );
    
    $profile_fields = array(
        'mastodon_instance' => __('Mastodon / Pleroma / Akkoma — Instance URL', 'eg-social-timeline'),
        'mastodon_username' => __('Mastodon / Pleroma / Akkoma — Username', 'eg-social-timeline'),
        'lemmy_instance'    => __('Lemmy — Instance URL', 'eg-social-timeline'),
        'lemmy_username'    => __('Lemmy — Username', 'eg-social-timeline'),
        'forgejo_instance'  => __('Forgejo / Gitea — Instance URL', 'eg-social-timeline'),
        'forgejo_username'  => __('Forgejo / Gitea — Username', 'eg-social-timeline'),
        'peertube_instance' => __('PeerTube — Instance URL', 'eg-social-timeline'),
        'peertube_username' => __('PeerTube — Account', 'eg-social-timeline'),
        'bluesky_instance'  => __('Bluesky — Service', 'eg-social-timeline'),
        'bluesky_handle'    => __('Bluesky — Handle', 'eg-social-timeline'),
    );

    foreach ($profile_fields as $field => $label) {
        add_settings_field(
            'eg_social_timeline_' . $field,
            $label,
            'eg_social_timeline_' . $field . '_callback',
            'eg-social-timeline',
            'eg_social_timeline_main_section'
        );
    }

    add_settings_section(
        'eg_social_timeline_limits_section',
        __('Per-Platform Post Limits', 'eg-social-timeline'),
        'eg_social_timeline_limits_section_callback',
        'eg-social-timeline'
    );
    
    add_settings_field(
        'eg_social_timeline_mastodon_limit',
        __('Max Mastodon Posts', 'eg-social-timeline'),
        'eg_social_timeline_mastodon_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_limits_section'
    );
    
    add_settings_field(
        'eg_social_timeline_lemmy_limit',
        __('Max Lemmy Posts', 'eg-social-timeline'),
        'eg_social_timeline_lemmy_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_limits_section'
    );
    
    add_settings_field(
        'eg_social_timeline_forgejo_limit',
        __('Max Forgejo Commits', 'eg-social-timeline'),
        'eg_social_timeline_forgejo_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_limits_section'
    );

    add_settings_field(
        'eg_social_timeline_bluesky_limit',
        __('Max Bluesky Posts', 'eg-social-timeline'),
        'eg_social_timeline_bluesky_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_limits_section'
    );

    add_settings_field(
        'eg_social_timeline_peertube_limit',
        __('Max PeerTube Videos', 'eg-social-timeline'),
        'eg_social_timeline_peertube_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_limits_section'
    );

    add_settings_field(
        'eg_social_timeline_post_limit',
        __('Number of Posts to Show', 'eg-social-timeline'),
        'eg_social_timeline_post_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_cache_duration',
        __('Cache Duration', 'eg-social-timeline'),
        'eg_social_timeline_cache_duration_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_show_boosts',
        __('Include Boosts/Reposts', 'eg-social-timeline'),
        'eg_social_timeline_show_boosts_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_show_stats',
        __('Show Statistics', 'eg-social-timeline'),
        'eg_social_timeline_show_stats_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );

    add_settings_field(
        'eg_social_timeline_truncate_length',
        __('Post Text Length', 'eg-social-timeline'),
        'eg_social_timeline_truncate_length_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );

    add_settings_field(
        'eg_social_timeline_show_images',
        __('Show Image Previews', 'eg-social-timeline'),
        'eg_social_timeline_show_images_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );

    add_settings_section(
        'eg_social_timeline_appearance_section',
        __('Appearance', 'eg-social-timeline'),
        'eg_social_timeline_appearance_section_callback',
        'eg-social-timeline'
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
        'eg_social_timeline_icon_style',
        __('Platform Icon Style', 'eg-social-timeline'),
        'eg_social_timeline_icon_style_callback',
        'eg-social-timeline',
        'eg_social_timeline_appearance_section'
    );
}

// Settings callbacks
function eg_social_timeline_main_section_callback() {
    echo '<p>' . esc_html__('Configure your social profiles to display a unified timeline. At least one profile is required.', 'eg-social-timeline') . '</p>';
}

function eg_social_timeline_limits_section_callback() {
    echo '<p>' . esc_html__('Limit the maximum number of posts per platform. This prevents a very active platform (e.g. Forgejo) from filling all available slots. Set 0 for no limit.', 'eg-social-timeline') . '</p>';
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
            'limit'    => $limit('mastodon_limit', 20),
        ),
        'lemmy' => array(
            'instance' => $lemmy_instance,
            'username' => ltrim($lemmy_username, '@'),
            'limit'    => $limit('lemmy_limit', 10, 'diggita_limit'),
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
        ),
        'bluesky' => array(
            'instance' => EG_SOCIAL_TIMELINE_BLUESKY_SERVICE,
            'username' => ltrim(sanitize_text_field($get('bluesky_handle')), '@'),
            'limit'    => $limit('bluesky_limit', 10),
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
        if ('' === $profile['username']) {
            continue;
        }

        // Le piattaforme federate servono a poco senza l'istanza.
        if (in_array($slug, array('mastodon', 'lemmy', 'forgejo', 'peertube'), true) && '' === $profile['instance']) {
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

    $cache_key = 'eg_st_software_' . md5($instance);
    $cached = get_transient($cache_key);

    if (false !== $cached) {
        return $cached;
    }

    $software = '';
    $response = wp_remote_get($instance . '/.well-known/nodeinfo', array('timeout' => 10, 'sslverify' => true));

    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($data['links']) && is_array($data['links'])) {
            $document = end($data['links']);
            $href = isset($document['href']) ? $document['href'] : '';
            $same_host = $href && wp_parse_url($href, PHP_URL_HOST) === wp_parse_url($instance, PHP_URL_HOST);

            if ($same_host && eg_social_timeline_is_public_url($href)) {
                $document_response = wp_remote_get($href, array('timeout' => 10, 'sslverify' => true));

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
        'gotosocial' => __('GoToSocial requires authentication for the accounts API, so a public timeline cannot be read.', 'eg-social-timeline'),
        'friendica'  => __('Friendica requires a login for the Mastodon-compatible API.', 'eg-social-timeline'),
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
        'mastodon' => 'Mastodon',
        'pleroma'  => 'Pleroma',
        'akkoma'   => 'Akkoma',
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
        __('The server where your account lives, HTTPS only. Mastodon, Pleroma and Akkoma expose the public API this plugin reads; GoToSocial and Friendica require a login, Misskey and Sharkey use a different API.', 'eg-social-timeline')
    );
}

function eg_social_timeline_mastodon_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['mastodon_username']) ? $options['mastodon_username'] : eg_social_timeline_profiles()['mastodon']['username'];

    eg_social_timeline_profile_field(
        'mastodon_username',
        $value,
        'emanuelegori',
        __('Username alone, without the leading @ and without the domain.', 'eg-social-timeline')
    );
}

function eg_social_timeline_lemmy_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['lemmy_instance']) ? $options['lemmy_instance'] : eg_social_timeline_profiles()['lemmy']['instance'];

    eg_social_timeline_profile_field(
        'lemmy_instance',
        $value,
        'https://diggita.com',
        __('Your Lemmy instance, HTTPS only. The user feed works only on the instance where the account is registered, not on another one that federates with it.', 'eg-social-timeline')
    );
}

function eg_social_timeline_lemmy_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['lemmy_username']) ? $options['lemmy_username'] : eg_social_timeline_profiles()['lemmy']['username'];

    eg_social_timeline_profile_field(
        'lemmy_username',
        $value,
        'emanuelegori',
        __('Username alone, without the leading @. The platform name shown on the cards comes from the instance domain.', 'eg-social-timeline')
    );
}

function eg_social_timeline_forgejo_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['forgejo_instance']) ? $options['forgejo_instance'] : 'https://gitea.com';

    eg_social_timeline_profile_field(
        'forgejo_instance',
        $value,
        'https://gitea.com',
        __('Instance hosting your repositories, HTTPS only. Default: https://gitea.com', 'eg-social-timeline')
    );
}

function eg_social_timeline_forgejo_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['forgejo_username']) ? $options['forgejo_username'] : '';

    eg_social_timeline_profile_field(
        'forgejo_username',
        $value,
        'emanuelegori',
        __('Commits are read from the public repositories of this account.', 'eg-social-timeline')
    );
}

function eg_social_timeline_peertube_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['peertube_instance']) ? $options['peertube_instance'] : '';

    eg_social_timeline_profile_field(
        'peertube_instance',
        $value,
        'https://peertube.uno',
        __('The PeerTube instance hosting your videos, HTTPS only.', 'eg-social-timeline')
    );
}

function eg_social_timeline_peertube_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $value = isset($options['peertube_username']) ? $options['peertube_username'] : eg_social_timeline_profiles()['peertube']['username'];

    eg_social_timeline_profile_field(
        'peertube_username',
        $value,
        'emanuelegori',
        __('Account name, the part before the @ of your PeerTube address.', 'eg-social-timeline')
    );
}

function eg_social_timeline_bluesky_instance_callback() {
    eg_social_timeline_profile_field(
        'bluesky_instance',
        EG_SOCIAL_TIMELINE_BLUESKY_SERVICE,
        '',
        __('Bluesky is not split across instances the way the fediverse is: the public API endpoint is the same for everyone, so there is nothing to choose here.', 'eg-social-timeline'),
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
        __('Full handle, without the leading @. It already contains its own domain.', 'eg-social-timeline')
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
        <?php esc_html_e('Maximum number of Bluesky posts to fetch (0 = unlimited). Default: 10', 'eg-social-timeline'); ?>
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
        <?php esc_html_e('Maximum number of Mastodon posts to fetch (0 = unlimited). Default: 20', 'eg-social-timeline'); ?>
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
        <?php esc_html_e('Maximum number of Lemmy posts included in the timeline. 0 = no limit. Default: 10', 'eg-social-timeline'); ?>
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

function eg_social_timeline_post_limit_callback() {
    $options = get_option('eg_social_timeline_options');
    $limit = isset($options['post_limit']) ? $options['post_limit'] : 10;
    ?>
    <input type="number" 
           id="eg_social_timeline_post_limit" 
           name="eg_social_timeline_options[post_limit]" 
           value="<?php echo esc_attr($limit); ?>" 
           min="1"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of posts to show in the timeline (1–100). Default: 10', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_cache_duration_callback() {
    $options = get_option('eg_social_timeline_options');
    $duration = isset($options['cache_duration']) ? $options['cache_duration'] : 3600;
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
        <?php esc_html_e('Feed cache duration. Longer cache = fewer requests to servers. Default: 1 hour', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_show_boosts_callback() {
    $options = get_option('eg_social_timeline_options');
    $show = isset($options['show_boosts']) ? $options['show_boosts'] : false;
    ?>
    <label>
        <input type="checkbox" 
               id="eg_social_timeline_show_boosts" 
               name="eg_social_timeline_options[show_boosts]" 
               value="1"
               <?php checked($show, 1); ?>>
        <?php esc_html_e('Include boosts and reposts in the timeline', 'eg-social-timeline'); ?>
    </label>
    <p class="description">
        <?php esc_html_e('If disabled, shows only original posts (no boosts/reblogs).', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_show_stats_callback() {
    $options = get_option('eg_social_timeline_options');
    $show = isset($options['show_stats']) ? $options['show_stats'] : true;
    ?>
    <label>
        <input type="checkbox" 
               id="eg_social_timeline_show_stats" 
               name="eg_social_timeline_options[show_stats]" 
               value="1"
               <?php checked($show, 1); ?>>
        <?php esc_html_e('Show like/boost/reply counts', 'eg-social-timeline'); ?>
    </label>
    <p class="description">
        <?php esc_html_e('Display interaction statistics below each post.', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_show_images_callback() {
    $options = get_option('eg_social_timeline_options');
    $show = !empty($options['show_images']);
    ?>
    <label>
        <input type="checkbox"
               id="eg_social_timeline_show_images"
               name="eg_social_timeline_options[show_images]"
               value="1"
               <?php checked($show, 1); ?>>
        <?php esc_html_e('Show the first image attached to posts (when available)', 'eg-social-timeline'); ?>
    </label>
    <p class="description">
        <?php esc_html_e('Supported by Mastodon and Pixelfed. Disabled by default: most useful if you use Pixelfed.', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_truncate_length_callback() {
    $options = get_option('eg_social_timeline_options');
    $length = isset($options['truncate_length']) ? $options['truncate_length'] : 300;
    ?>
    <input type="number"
           id="eg_social_timeline_truncate_length"
           name="eg_social_timeline_options[truncate_length]"
           value="<?php echo esc_attr($length); ?>"
           min="0"
           max="600"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of characters shown per post (50–600). Set 0 to show full text with no limit (not recommended: may break mobile layout). Default: 300', 'eg-social-timeline'); ?>
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

    return array(
        'canvas_bg'       => $canvas,
        'canvas_bg_color' => $canvas_color ? $canvas_color : '#f3f4f6',
        'card_bg'         => $card,
        'card_bg_color'   => $card_color ? $card_color : '#ffffff',
        'icon_style'      => $icon_style,
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
    
    // Profili: istanza + utente per ogni piattaforma federata.
    $instances = array(
        'mastodon_instance' => '',
        'lemmy_instance'    => '',
        'forgejo_instance'  => 'https://gitea.com',
        'peertube_instance' => '',
    );

    foreach ($instances as $key => $fallback) {
        $instance = isset($input[$key]) ? eg_social_timeline_normalize_instance($input[$key]) : '';

        if ('' === $instance && isset($input[$key]) && '' !== trim((string) $input[$key])) {
            add_settings_error(
                'eg_social_timeline_options',
                'invalid_instance_' . $key,
                '<strong>' . __('Error:', 'eg-social-timeline') . '</strong> ' .
                sprintf(
                    /* translators: %s: valore inserito per l'istanza */
                    __('"%s" is not a usable instance address. Use an HTTPS address of a public server.', 'eg-social-timeline'),
                    esc_html(trim((string) $input[$key]))
                ),
                'error'
            );
        }

        $output[$key] = ('' !== $instance) ? $instance : $fallback;
    }

    $usernames = array('mastodon_username', 'lemmy_username', 'forgejo_username', 'peertube_username', 'bluesky_handle');

    foreach ($usernames as $key) {
        $output[$key] = isset($input[$key]) ? sanitize_text_field(ltrim(trim($input[$key]), '@')) : '';
    }

    $configured = false;

    foreach (array('mastodon', 'lemmy', 'forgejo', 'peertube') as $platform) {
        if ('' !== $output[$platform . '_username'] && '' !== $output[$platform . '_instance']) {
            $configured = true;
        }
    }

    if ('' !== $output['bluesky_handle']) {
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
                    esc_html(ucfirst($software)),
                    esc_html($unsupported[$software])
                ),
                'warning'
            );
        }
    }

    $limit = isset($input['post_limit']) ? intval($input['post_limit']) : 10;
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

    $duration = isset($input['cache_duration']) ? intval($input['cache_duration']) : 3600;
    $output['cache_duration'] = in_array($duration, array(1800, 3600, 7200, 14400, 28800, 86400)) ? $duration : 3600;
    
    $output['show_boosts'] = isset($input['show_boosts']) ? true : false;
    $output['show_stats'] = isset($input['show_stats']) ? true : false;
    $output['show_images'] = isset($input['show_images']) ? true : false;

    $truncate = isset($input['truncate_length']) ? intval($input['truncate_length']) : 300;
    $output['truncate_length'] = ($truncate === 0) ? 0 : max(50, min(600, $truncate));

    // Aspetto
    $canvas_bg = isset($input['canvas_bg']) ? sanitize_key($input['canvas_bg']) : 'none';
    $output['canvas_bg'] = in_array($canvas_bg, array('none', 'neutral', 'auto', 'custom'), true) ? $canvas_bg : 'none';

    $card_bg = isset($input['card_bg']) ? sanitize_key($input['card_bg']) : 'neutral';
    $output['card_bg'] = in_array($card_bg, array('neutral', 'none', 'auto', 'custom'), true) ? $card_bg : 'neutral';

    $icon_style = isset($input['icon_style']) ? sanitize_key($input['icon_style']) : 'brand';
    $output['icon_style'] = in_array($icon_style, array('brand', 'mono'), true) ? $icon_style : 'brand';

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

    delete_transient('eg_social_timeline_cache');
    delete_option('eg_social_timeline_status');
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
function eg_social_timeline_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php eg_social_timeline_render_status_notice(); ?>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('eg_social_timeline_settings');
            do_settings_sections('eg-social-timeline');
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
        
        <hr>
        
        <p style="color: #666;">
            <strong><?php
                /* translators: %s: numero versione plugin */
                printf( esc_html__( 'EG Social Timeline v%s', 'eg-social-timeline' ), esc_html( EG_SOCIAL_TIMELINE_VERSION ) );
            ?></strong><br>
            <?php
            printf(
                /* translators: %s: link HTML al sito dello sviluppatore */
                esc_html__('Developed by %s', 'eg-social-timeline'),
                '<a href="https://emanuelegori.uno" target="_blank" rel="noopener noreferrer">Emanuele Gori</a>'
            );
            ?> | 
            <a href="https://git.emanuelegori.uno/emanuelegori/eg-social-timeline" target="_blank"><?php esc_html_e('Repository', 'eg-social-timeline'); ?></a> | 
            <?php esc_html_e('License GPL-2.0-or-later', 'eg-social-timeline'); ?>
        </p>
    </div>
    <?php
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
    
    add_settings_error(
        'eg_social_timeline_options',
        'cache_cleared',
        __('Cache flushed successfully!', 'eg-social-timeline'),
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

    $cache_key = 'eg_mastodon_id_' . md5($instance . '/' . $username);
    $cached_id = get_transient($cache_key);

    if ($cached_id !== false) {
        return $cached_id;
    }

    $api_url = $instance . '/api/v1/accounts/lookup?acct=' . rawurlencode($username);

    $response = wp_remote_get($api_url, array(
        'timeout' => 10,
        'sslverify' => true
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

    $options = get_option('eg_social_timeline_options');
    $show_boosts = !empty($options['show_boosts']);
    
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
        'sslverify' => true
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
        'sslverify' => true
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
    
    $xml = simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NONET);

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
        'sslverify' => true
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
            'sslverify' => true
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
    $show_boosts = !empty($options['show_boosts']);

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
function eg_social_timeline_fetch_peertube($handle, $instance_url, $limit = 0) {
    if (empty($handle) || empty($instance_url)) {
        return array();
    }

    // HTTPS only + anti-SSRF (public API, no token, must not hit internal hosts)
    if (strpos($instance_url, 'https://') !== 0 || !eg_social_timeline_is_public_url($instance_url)) {
        return array();
    }

    $handle = ltrim($handle, '@');
    $api_limit = ($limit > 0) ? min($limit, 100) : 100;

    $api_url = rtrim($instance_url, '/') . '/api/v1/accounts/' . rawurlencode($handle) . '/videos?' . http_build_query(array(
        'count' => $api_limit,
        'sort'  => '-publishedAt',
    ));

    $response = wp_remote_get($api_url, array(
        'timeout'  => 15,
        'sslverify' => true,
    ));

    if (is_wp_error($response)) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline PeerTube Error: ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return array();
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($data['data']) || !is_array($data['data'])) {
        return array();
    }

    $base = rtrim($instance_url, '/');
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
    );

    foreach ($fetchers as $platform => $fetcher) {
        $profile = $profiles[$platform];

        if ('' === $profile['username'] || '' === $profile['instance']) {
            continue;
        }

        $posts = call_user_func($fetcher, $profile['username'], $profile['instance'], $profile['limit']);
        $all_posts = array_merge($all_posts, $posts);
        $fetched[$platform] = count($posts);
    }

    // Bluesky non e' federato: un solo endpoint pubblico per tutti.
    if ('' !== $profiles['bluesky']['username']) {
        $posts = eg_social_timeline_fetch_bluesky($profiles['bluesky']['username'], $profiles['bluesky']['limit']);
        $all_posts = array_merge($all_posts, $posts);
        $fetched['bluesky'] = count($posts);
    }

    usort($all_posts, function($a, $b) {
        return $b['date'] - $a['date'];
    });

    $cache_duration = isset($options['cache_duration']) ? intval($options['cache_duration']) : 3600;
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
 * Avvisi in Impostazioni sulle piattaforme che non hanno portato contenuti.
 */
function eg_social_timeline_render_status_notice() {
    $report = get_option('eg_social_timeline_status');

    if (empty($report['platforms']) || !is_array($report['platforms'])) {
        return;
    }

    foreach ($report['platforms'] as $platform => $result) {
        if (!empty($result['count'])) {
            continue;
        }

        $label = eg_social_timeline_get_platform_name($platform);
        $reason = !empty($result['issue'])
            ? $result['issue']
            : __('No content retrieved. Check the username and the instance URL.', 'eg-social-timeline');
        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php echo esc_html($label); ?>:</strong>
                <?php echo esc_html($reason); ?>
            </p>
        </div>
        <?php
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
        'limit' => $options['post_limit']
    ), $atts, 'eg_social_timeline');
    
    $limit = intval($atts['limit']);
    
    $posts = eg_social_timeline_fetch_all_feeds();
    
    if (empty($posts)) {
        return '<div class="' . esc_attr(eg_social_timeline_wrapper_classes()) . '">' .
               '<div class="eg-social-timeline-empty">' .
               esc_html__('No posts available at the moment.', 'eg-social-timeline') .
               '</div></div>';
    }
    
    $posts = array_slice($posts, 0, $limit);
    
    $show_stats = !empty($options['show_stats']);
    $show_images = !empty($options['show_images']);
    $truncate_length = isset($options['truncate_length']) ? intval($options['truncate_length']) : 300;
    
    ob_start();
    ?>
    <div class="<?php echo esc_attr(eg_social_timeline_wrapper_classes()); ?>">
        
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
                <h3><?php esc_html_e( 'Filter by platform:', 'eg-social-timeline' ); ?></h3>
            </div>
            
            <div class="filters-checkboxes">
                <?php foreach ($platform_counts as $platform => $count): ?>
                    <?php $software = isset($platform_software[$platform]) ? $platform_software[$platform] : ''; ?>
                    <label for="filter-<?php echo esc_attr($platform); ?>"
                           class="<?php echo esc_attr('filter-checkbox-label' . ($software ? ' egst-sw-' . $software : '')); ?>">
                        <span class="platform-icon-small">
                            <?php echo eg_social_timeline_get_icon($platform, $software); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG sanitizzato internamente dalla funzione ?>
                        </span>
                        <?php 
                        $name = isset($platform_names[$platform]) ? $platform_names[$platform] : ucfirst($platform);
                        echo esc_html($name); 
                        ?>
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
                        <?php echo esc_html($truncate_length > 0 ? eg_social_timeline_truncate($post['content'], $truncate_length) : wp_strip_all_tags($post['content'])); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($show_images && !empty($post['image_url'])): ?>
                    <div class="post-image">
                        <img src="<?php echo esc_url($post['image_url']); ?>"
                             alt="<?php echo esc_attr(!empty($post['image_alt']) ? $post['image_alt'] : __('Attached image', 'eg-social-timeline')); ?>"
                             loading="lazy">
                    </div>
                    <?php endif; ?>
                </div>
                <footer class="timeline-footer">
                    <?php if ($show_stats && ($post['favourites_count'] > 0 || $post['reblogs_count'] > 0 || $post['replies_count'] > 0)): ?>
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
        'peertube' => __('PeerTube', 'eg-social-timeline')
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
        'blog' => 'blog.svg'
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
            return wp_kses(file_get_contents($icon_path), $svg_kses);
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
 * Classi del contenitore della timeline: stile icone e sfondo.
 *
 * @return string Elenco di classi CSS separate da spazio.
 */
function eg_social_timeline_wrapper_classes() {
    $settings = eg_social_timeline_appearance_settings();
    $classes  = array('eg-social-timeline', 'egst-icons-' . $settings['icon_style']);

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

    $icons = array('mono', 'mastodon', 'pleroma', 'lemmy', 'bluesky', 'forgejo', 'peertube', 'blog');
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

