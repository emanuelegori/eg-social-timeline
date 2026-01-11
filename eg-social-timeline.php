<?php
/**
 * Plugin Name: EG Social Timeline
 * Plugin URI: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
 * Description: Mostra una timeline cronologica unificata delle tue attività social da Mastodon, Diggita e Bluesky
 * Version: 1.1.2
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
define('EG_SOCIAL_TIMELINE_VERSION', '1.1.2');
define('EG_SOCIAL_TIMELINE_DIR', plugin_dir_path(__FILE__));
define('EG_SOCIAL_TIMELINE_URL', plugin_dir_url(__FILE__));
define('EG_SOCIAL_TIMELINE_DEBUG', false);

// Load text domain
add_action('plugins_loaded', 'eg_social_timeline_load_textdomain');

function eg_social_timeline_load_textdomain() {
    load_plugin_textdomain('eg-social-timeline', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

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
                'mastodon_url' => '',
                'diggita_username' => '',
                'post_limit' => 10,
                'cache_duration' => 3600,
                'show_boosts' => false,
                'show_stats' => true
            )
        )
    );
    
    add_settings_section(
        'eg_social_timeline_main_section',
        __('Configurazione Profili Social', 'eg-social-timeline'),
        'eg_social_timeline_main_section_callback',
        'eg-social-timeline'
    );
    
    add_settings_field(
        'eg_social_timeline_mastodon_url',
        __('URL Profilo Mastodon', 'eg-social-timeline'),
        'eg_social_timeline_mastodon_url_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_diggita_username',
        __('Username Diggita', 'eg-social-timeline'),
        'eg_social_timeline_diggita_username_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_post_limit',
        __('Numero Post da Mostrare', 'eg-social-timeline'),
        'eg_social_timeline_post_limit_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_cache_duration',
        __('Durata Cache', 'eg-social-timeline'),
        'eg_social_timeline_cache_duration_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_show_boosts',
        __('Includi Boost/Repost', 'eg-social-timeline'),
        'eg_social_timeline_show_boosts_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_show_stats',
        __('Mostra Statistiche', 'eg-social-timeline'),
        'eg_social_timeline_show_stats_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
}

// Settings callbacks
function eg_social_timeline_main_section_callback() {
    echo '<p>' . esc_html__('Configura i tuoi profili social per mostrare una timeline unificata. Almeno un profilo è obbligatorio.', 'eg-social-timeline') . '</p>';
}

function eg_social_timeline_mastodon_url_callback() {
    $options = get_option('eg_social_timeline_options');
    $url = isset($options['mastodon_url']) ? $options['mastodon_url'] : '';
    ?>
    <input type="url" 
           id="eg_social_timeline_mastodon_url" 
           name="eg_social_timeline_options[mastodon_url]" 
           value="<?php echo esc_attr($url); ?>" 
           placeholder="<?php echo esc_attr__('es: https://mastodon.uno/@emanuelegori', 'eg-social-timeline'); ?>"
           class="regular-text">
    <p class="description">
        <?php esc_html_e('URL completo del tuo profilo pubblico Mastodon (o altre istanze Fediverso).', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_diggita_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $username = isset($options['diggita_username']) ? $options['diggita_username'] : '';
    ?>
    <input type="text" 
           id="eg_social_timeline_diggita_username" 
           name="eg_social_timeline_options[diggita_username]" 
           value="<?php echo esc_attr($username); ?>" 
           placeholder="<?php echo esc_attr__('es: emanuelegori', 'eg-social-timeline'); ?>"
           class="regular-text">
    <p class="description">
        <?php esc_html_e('Username Diggita (senza @).', 'eg-social-timeline'); ?>
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
           max="50"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Numero massimo di post da mostrare nella timeline (1-50). Default: 10', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_cache_duration_callback() {
    $options = get_option('eg_social_timeline_options');
    $duration = isset($options['cache_duration']) ? $options['cache_duration'] : 3600;
    ?>
    <select id="eg_social_timeline_cache_duration" 
            name="eg_social_timeline_options[cache_duration]">
        <option value="1800" <?php selected($duration, 1800); ?>>30 <?php esc_html_e('minuti', 'eg-social-timeline'); ?></option>
        <option value="3600" <?php selected($duration, 3600); ?>>1 <?php esc_html_e('ora', 'eg-social-timeline'); ?></option>
        <option value="7200" <?php selected($duration, 7200); ?>>2 <?php esc_html_e('ore', 'eg-social-timeline'); ?></option>
        <option value="14400" <?php selected($duration, 14400); ?>>4 <?php esc_html_e('ore', 'eg-social-timeline'); ?></option>
        <option value="28800" <?php selected($duration, 28800); ?>>8 <?php esc_html_e('ore', 'eg-social-timeline'); ?></option>
        <option value="86400" <?php selected($duration, 86400); ?>>24 <?php esc_html_e('ore', 'eg-social-timeline'); ?></option>
    </select>
    <p class="description">
        <?php esc_html_e('Tempo di cache dei feed. Cache più lunga = meno richieste ai server. Default: 1 ora', 'eg-social-timeline'); ?>
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
        <?php esc_html_e('Includi boost e repost nella timeline', 'eg-social-timeline'); ?>
    </label>
    <p class="description">
        <?php esc_html_e('Se disabilitato, mostra solo post originali (nessun boost/reblog).', 'eg-social-timeline'); ?>
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
        <?php esc_html_e('Mostra conteggi like/boost/risposte', 'eg-social-timeline'); ?>
    </label>
    <p class="description">
        <?php esc_html_e('Visualizza le statistiche di interazione sotto ogni post.', 'eg-social-timeline'); ?>
    </p>
    <?php
}

// Sanitization
function eg_social_timeline_sanitize_options($input) {
    $output = array();
    
    $mastodon_url = isset($input['mastodon_url']) ? esc_url_raw($input['mastodon_url']) : '';
    $diggita_username = isset($input['diggita_username']) ? sanitize_text_field($input['diggita_username']) : '';
    
    if (empty($mastodon_url) && empty($diggita_username)) {
        add_settings_error(
            'eg_social_timeline_options',
            'no_profiles',
            '<strong>' . __('Errore:', 'eg-social-timeline') . '</strong> ' . 
            __('Devi configurare almeno un profilo social (Mastodon o Diggita).', 'eg-social-timeline'),
            'error'
        );
        
        $old_options = get_option('eg_social_timeline_options');
        return $old_options ? $old_options : array();
    }
    
    $output['mastodon_url'] = $mastodon_url;
    $output['diggita_username'] = $diggita_username;
    
    $limit = isset($input['post_limit']) ? intval($input['post_limit']) : 10;
    $output['post_limit'] = max(1, min(50, $limit));
    
    $duration = isset($input['cache_duration']) ? intval($input['cache_duration']) : 3600;
    $output['cache_duration'] = in_array($duration, array(1800, 3600, 7200, 14400, 28800, 86400)) ? $duration : 3600;
    
    $output['show_boosts'] = isset($input['show_boosts']) ? true : false;
    $output['show_stats'] = isset($input['show_stats']) ? true : false;
    
    delete_transient('eg_social_timeline_cache');
    
    add_settings_error(
        'eg_social_timeline_options',
        'settings_updated',
        __('Impostazioni salvate correttamente! Cache pulita.', 'eg-social-timeline'),
        'success'
    );
    
    return $output;
}

// Admin notices
add_action('admin_notices', 'eg_social_timeline_admin_notice');

function eg_social_timeline_admin_notice() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'settings_page_eg-social-timeline') {
        return;
    }
    
    $options = get_option('eg_social_timeline_options');
    
    if (empty($options['mastodon_url']) && empty($options['diggita_username'])) {
        ?>
        <div class="notice notice-error">
            <p>
                <strong><?php esc_html_e('EG Social Timeline richiede configurazione!', 'eg-social-timeline'); ?></strong><br>
                <?php esc_html_e('Il plugin è attivo ma nessun profilo social è configurato.', 'eg-social-timeline'); ?><br>
                <a href="<?php echo esc_url(admin_url('options-general.php?page=eg-social-timeline')); ?>" class="button button-primary" style="margin-top: 10px;">
                    <?php esc_html_e('Configura ora', 'eg-social-timeline'); ?>
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
        
        <form action="options.php" method="post">
            <?php
            settings_fields('eg_social_timeline_settings');
            do_settings_sections('eg-social-timeline');
            submit_button(__('Salva Impostazioni', 'eg-social-timeline'));
            ?>
        </form>
        
        <hr>
        
        <h2><?php esc_html_e('Utilizzo', 'eg-social-timeline'); ?></h2>
        <p><?php esc_html_e('Una volta configurato, puoi inserire la timeline nei tuoi articoli usando:', 'eg-social-timeline'); ?></p>
        
        <h3><?php esc_html_e('Shortcode', 'eg-social-timeline'); ?></h3>
        <p><?php esc_html_e('Inserisci nel contenuto dell\'articolo:', 'eg-social-timeline'); ?></p>
        <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #6364FF;"><code>[eg_social_timeline]</code></pre>
        
        <p><?php esc_html_e('Opzionale: limita il numero di post:', 'eg-social-timeline'); ?></p>
        <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #6364FF;"><code>[eg_social_timeline limit="20"]</code></pre>
        
        <h3><?php esc_html_e('Svuota Cache Manualmente', 'eg-social-timeline'); ?></h3>
        <p><?php esc_html_e('Per forzare l\'aggiornamento immediato dei feed:', 'eg-social-timeline'); ?></p>
        <form method="post" style="display: inline;">
            <?php wp_nonce_field('eg_social_timeline_clear_cache', 'eg_social_timeline_nonce'); ?>
            <input type="hidden" name="eg_social_timeline_clear_cache" value="1">
            <button type="submit" class="button"><?php esc_html_e('Svuota Cache Ora', 'eg-social-timeline'); ?></button>
        </form>
        
        <hr>
        
        <p style="color: #666;">
            <strong><?php printf(esc_html__('EG Social Timeline v%s', 'eg-social-timeline'), EG_SOCIAL_TIMELINE_VERSION); ?></strong><br>
            <?php 
            printf(
                esc_html__('Sviluppato da %s', 'eg-social-timeline'),
                '<a href="https://emanuelegori.uno" target="_blank">Emanuele Gori</a>'
            );
            ?> | 
            <a href="https://git.emanuelegori.uno/emanuelegori/eg-social-timeline" target="_blank"><?php esc_html_e('Repository', 'eg-social-timeline'); ?></a> | 
            <?php esc_html_e('Licenza GPL-2.0-or-later', 'eg-social-timeline'); ?>
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
    
    if (!isset($_POST['eg_social_timeline_nonce']) || !wp_verify_nonce($_POST['eg_social_timeline_nonce'], 'eg_social_timeline_clear_cache')) {
        return;
    }
    
    delete_transient('eg_social_timeline_cache');
    
    add_settings_error(
        'eg_social_timeline_options',
        'cache_cleared',
        __('Cache svuotata con successo!', 'eg-social-timeline'),
        'success'
    );
    
    set_transient('eg_social_timeline_admin_notice', true, 5);
}

// Get Mastodon Account ID from profile URL
function eg_social_timeline_get_mastodon_account_id($profile_url) {
    preg_match('#https?://([^/]+)/@([^/]+)#', $profile_url, $matches);
    
    if (count($matches) < 3) {
        return false;
    }
    
    $instance = $matches[1];
    $username = $matches[2];
    
    $cache_key = 'eg_mastodon_id_' . md5($profile_url);
    $cached_id = get_transient($cache_key);
    
    if ($cached_id !== false) {
        return $cached_id;
    }
    
    $api_url = "https://{$instance}/api/v1/accounts/lookup?acct={$username}";
    
    $response = wp_remote_get($api_url, array(
        'timeout' => 10,
        'sslverify' => true
    ));
    
    if (is_wp_error($response)) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline: Mastodon account lookup error - ' . $response->get_error_message());
        }
        return false;
    }
    
    $data = json_decode(wp_remote_retrieve_body($response), true);
    
    if (!isset($data['id'])) {
        return false;
    }
    
    set_transient($cache_key, $data['id'], 86400);
    
    return $data['id'];
}

// Fetch Mastodon posts via API
function eg_social_timeline_fetch_mastodon($profile_url) {
    if (empty($profile_url)) {
        return array();
    }
    
    $account_id = eg_social_timeline_get_mastodon_account_id($profile_url);
    
    if (!$account_id) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline: Could not get Mastodon account ID for ' . $profile_url);
        }
        return array();
    }
    
    preg_match('#https?://([^/]+)/#', $profile_url, $matches);
    $instance = $matches[1];
    
    $options = get_option('eg_social_timeline_options');
    $show_boosts = !empty($options['show_boosts']);
    
    $api_url = "https://{$instance}/api/v1/accounts/{$account_id}/statuses";
    
    $params = array(
        'limit' => 40,
        'exclude_replies' => 'true',
        'exclude_reblogs' => $show_boosts ? 'false' : 'true'
    );
    
    $api_url .= '?' . http_build_query($params);
    
    $response = wp_remote_get($api_url, array(
        'timeout' => 15,
        'sslverify' => true
    ));
    
    if (is_wp_error($response)) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Mastodon API Error: ' . $response->get_error_message());
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
        
        $content = strip_tags($content_data['content']);
        
        $title_parts = explode("\n", $content);
        $title = !empty($title_parts[0]) ? $title_parts[0] : '';
        
        $post_url = $is_boost ? $content_data["url"] : $status["url"];
        
        $posts[] = array(
            'platform' => 'mastodon',
            'date' => strtotime($status['created_at']),
            'title' => $title,
            'content' => $content,
            'link' => $post_url,
            'is_boost' => $is_boost,
            'favourites_count' => isset($content_data['favourites_count']) ? intval($content_data['favourites_count']) : 0,
            'reblogs_count' => isset($content_data['reblogs_count']) ? intval($content_data['reblogs_count']) : 0,
            'replies_count' => isset($content_data['replies_count']) ? intval($content_data['replies_count']) : 0
        );
    }
    
    return $posts;
}

// Fetch Diggita RSS
function eg_social_timeline_fetch_diggita($username) {
    if (empty($username)) {
        return array();
    }
    
    $rss_url = 'https://diggita.com/feeds/u/' . sanitize_text_field($username) . '.xml';
    
    $response = wp_remote_get($rss_url, array(
        'timeout' => 15,
        'sslverify' => true
    ));
    
    if (is_wp_error($response)) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Diggita Error: ' . $response->get_error_message());
        }
        return array();
    }
    
    $body = wp_remote_retrieve_body($response);
    
    if (empty($body)) {
        return array();
    }
    
    $xml = simplexml_load_string($body);
    
    if ($xml === false) {
        return array();
    }
    
    $posts = array();
    
    foreach ($xml->channel->item as $item) {
        $pubDate = (string) $item->pubDate;
        $timestamp = strtotime($pubDate);
        
        $posts[] = array(
            'platform' => 'diggita',
            'date' => $timestamp,
            'title' => (string) $item->title,
            'content' => (string) $item->description,
            'link' => (string) $item->link,
            'is_boost' => false,
            'favourites_count' => 0,
            'reblogs_count' => 0,
            'replies_count' => 0
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
    
    $options = get_option('eg_social_timeline_options');
    $all_posts = array();
    
    $mastodon_posts = eg_social_timeline_fetch_mastodon($options['mastodon_url']);
    $all_posts = array_merge($all_posts, $mastodon_posts);
    
    $diggita_posts = eg_social_timeline_fetch_diggita($options['diggita_username']);
    $all_posts = array_merge($all_posts, $diggita_posts);
    
    usort($all_posts, function($a, $b) {
        return $b['date'] - $a['date'];
    });
    
    $cache_duration = isset($options['cache_duration']) ? intval($options['cache_duration']) : 3600;
    set_transient('eg_social_timeline_cache', $all_posts, $cache_duration);
    
    return $all_posts;
}

// Shortcode
add_shortcode('eg_social_timeline', 'eg_social_timeline_shortcode');

function eg_social_timeline_shortcode($atts) {
    $options = get_option('eg_social_timeline_options');
    
    if (empty($options['mastodon_url']) && empty($options['diggita_username'])) {
        if (current_user_can('manage_options')) {
            return '<div style="background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0;">
                <strong>' . esc_html__('EG Social Timeline - Configurazione Richiesta', 'eg-social-timeline') . '</strong><br>
                ' . esc_html__('Nessun profilo social configurato.', 'eg-social-timeline') . ' 
                <a href="' . admin_url('options-general.php?page=eg-social-timeline') . '">' . esc_html__('Configura ora', 'eg-social-timeline') . '</a>
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
        return '<div class="eg-social-timeline-empty">' . 
               esc_html__('Nessun post disponibile al momento.', 'eg-social-timeline') . 
               '</div>';
    }
    
    $posts = array_slice($posts, 0, $limit);
    
    $show_stats = !empty($options['show_stats']);
    
    ob_start();
    ?>
    <div class="eg-social-timeline">
        
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
        
        // Platform display names
        $platform_names = array(
            'mastodon' => 'Mastodon',
            'diggita' => 'Diggita',
            'bluesky' => 'Bluesky',
            'forgejo' => 'Forgejo',
            'blog' => 'Blog'
        );
        ?>
        
        <!-- CSS-only Filters Box -->
        <div class="eg-timeline-filters">
            <div class="filters-header">
                <span class="filters-icon">🔍</span>
                <h3>Filtra per piattaforma:</h3>
            </div>
            
            <div class="filters-checkboxes">
                <?php foreach ($platform_counts as $platform => $count): ?>
                    <input type="checkbox" 
                           id="filter-<?php echo esc_attr($platform); ?>" 
                           class="filter-checkbox-input"
                           checked>
                    <label for="filter-<?php echo esc_attr($platform); ?>" 
                           class="filter-checkbox-label">
                        <span class="platform-icon-small">
                            <?php echo eg_social_timeline_get_icon($platform); ?>
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
            <article class="timeline-item timeline-<?php echo esc_attr($post['platform']); ?><?php echo $post['is_boost'] ? ' is-boost' : ''; ?>" 
                     data-platform="<?php echo esc_attr($post['platform']); ?>">
                <header class="timeline-header">
                    <span class="platform-icon platform-<?php echo esc_attr($post['platform']); ?>">
                        <?php echo eg_social_timeline_get_icon($post['platform']); ?>
                    </span>
                    <span class="platform-name"><?php echo esc_html(eg_social_timeline_get_platform_name($post['platform'])); ?></span>
                    
                    <?php if ($post['is_boost']): ?>
                        <span class="boost-badge">🔁 Boost</span>
                    <?php endif; ?>
                    
                    <time datetime="<?php echo esc_attr(date('c', $post['date'])); ?>" class="post-date">
                        <?php echo esc_html(eg_social_timeline_format_date($post['date'])); ?>
                    </time>
                </header>
                <div class="timeline-content">
                    <div class="post-text">
                        <?php echo wp_kses_post(eg_social_timeline_truncate($post['content'], 300)); ?>
                    </div>
                </div>
                <footer class="timeline-footer">
                    <?php if ($show_stats && ($post['favourites_count'] > 0 || $post['reblogs_count'] > 0 || $post['replies_count'] > 0)): ?>
                        <div class="post-stats">
                            <?php if ($post['favourites_count'] > 0): ?>
                                <span class="stat-item stat-favourites" title="<?php esc_attr_e('Preferiti', 'eg-social-timeline'); ?>">
                                    ❤️ <?php echo esc_html($post['favourites_count']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($post['reblogs_count'] > 0): ?>
                                <span class="stat-item stat-boosts" title="<?php esc_attr_e('Boost', 'eg-social-timeline'); ?>">
                                    🔁 <?php echo esc_html($post['reblogs_count']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($post['replies_count'] > 0): ?>
                                <span class="stat-item stat-replies" title="<?php esc_attr_e('Risposte', 'eg-social-timeline'); ?>">
                                    💬 <?php echo esc_html($post['replies_count']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($post['link']); ?>" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="view-original">
                        <?php esc_html_e('Vedi post originale', 'eg-social-timeline'); ?> →
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
        'diggita' => __('Diggita', 'eg-social-timeline'),
        'bluesky' => __('Bluesky', 'eg-social-timeline')
    );
    
    return isset($names[$platform]) ? $names[$platform] : $platform;
}

function eg_social_timeline_get_icon($platform) {
    // Mappatura piattaforme -> file SVG
    $icon_files = array(
        'mastodon' => 'mastodon.svg',
        'diggita' => 'diggita.svg',
        'bluesky' => 'bluesky.svg',
        'forgejo' => 'forgejo.svg',
        'blog' => 'blog.svg'
    );
    
    // Percorso cartella icone
    $icons_dir = EG_SOCIAL_TIMELINE_DIR . 'social-icons/';
    
    // Verifica file esiste
    if (isset($icon_files[$platform])) {
        $icon_path = $icons_dir . $icon_files[$platform];
        
        if (file_exists($icon_path)) {
            return file_get_contents($icon_path);
        }
    }
    
    // Fallback: cerchia generica
    return '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.3"/></svg>';
}

function eg_social_timeline_format_date($timestamp) {
    $diff = time() - $timestamp;
    
    if ($diff < 3600) {
        $mins = round($diff / 60);
        return sprintf(_n('%s minuto fa', '%s minuti fa', $mins, 'eg-social-timeline'), $mins);
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return sprintf(_n('%s ora fa', '%s ore fa', $hours, 'eg-social-timeline'), $hours);
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return sprintf(_n('%s giorno fa', '%s giorni fa', $days, 'eg-social-timeline'), $days);
    } else {
        return date_i18n(get_option('date_format'), $timestamp);
    }
}

function eg_social_timeline_truncate($text, $length = 200) {
    $text = strip_tags($text);
    
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
    }
}

// Plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'eg_social_timeline_action_links');

function eg_social_timeline_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=eg-social-timeline')) . '">' . __('Impostazioni', 'eg-social-timeline') . '</a>';
    $docs_link = '<a href="https://git.emanuelegori.uno/emanuelegori/eg-social-timeline" target="_blank">' . __('Documentazione', 'eg-social-timeline') . '</a>';
    
    array_unshift($links, $docs_link, $settings_link);
    
    return $links;
}
