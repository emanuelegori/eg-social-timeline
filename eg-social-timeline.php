<?php
/**
 * Plugin Name: EG Social Timeline
 * Plugin URI: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
 * Description: Mostra una timeline cronologica unificata delle tue attività social da Mastodon, Diggita e Bluesky
 * Version: 1.0.0
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
define('EG_SOCIAL_TIMELINE_VERSION', '1.0.0');
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
                'show_boosts' => false
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
        __('Durata Cache (secondi)', 'eg-social-timeline'),
        'eg_social_timeline_cache_duration_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_show_boosts',
        __('Mostra Boost/Repost', 'eg-social-timeline'),
        'eg_social_timeline_show_boosts_callback',
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
        <?php esc_html_e('Tempo di cache dei feed RSS. Cache più lunga = meno richieste ai server. Default: 1 ora', 'eg-social-timeline'); ?>
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
        <?php esc_html_e('Se disabilitato, mostra solo post originali.', 'eg-social-timeline'); ?>
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

// Fetch Mastodon RSS
function eg_social_timeline_fetch_mastodon($profile_url) {
    if (empty($profile_url)) {
        return array();
    }
    
    $rss_url = rtrim($profile_url, '/') . '.rss';
    
    $response = wp_remote_get($rss_url, array(
        'timeout' => 15,
        'sslverify' => true
    ));
    
    if (is_wp_error($response)) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline Mastodon Error: ' . $response->get_error_message());
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
    $namespaces = $xml->getNamespaces(true);
    
    foreach ($xml->channel->item as $item) {
        $pubDate = (string) $item->pubDate;
        $timestamp = strtotime($pubDate);
        
        $title = (string) $item->title;
        if (strpos($title, 'RT @') === 0 || strpos($title, 'Boost: ') === 0) {
            $options = get_option('eg_social_timeline_options');
            if (empty($options['show_boosts'])) {
                continue;
            }
        }
        
        $posts[] = array(
            'platform' => 'mastodon',
            'date' => $timestamp,
            'title' => $title,
            'content' => (string) $item->description,
            'link' => (string) $item->link
        );
    }
    
    return $posts;
}

// Fetch Diggita RSS (Lemmy)
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
            'link' => (string) $item->link
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
    
    ob_start();
    ?>
    <div class="eg-social-timeline">
        <?php foreach ($posts as $post): ?>
            <article class="timeline-item timeline-<?php echo esc_attr($post['platform']); ?>">
                <header class="timeline-header">
                    <span class="platform-icon platform-<?php echo esc_attr($post['platform']); ?>">
                        <?php echo eg_social_timeline_get_icon($post['platform']); ?>
                    </span>
                    <span class="platform-name"><?php echo esc_html(eg_social_timeline_get_platform_name($post['platform'])); ?></span>
                    <time datetime="<?php echo esc_attr(date('c', $post['date'])); ?>" class="post-date">
                        <?php echo esc_html(eg_social_timeline_format_date($post['date'])); ?>
                    </time>
                </header>
                <div class="timeline-content">
                    <?php if (!empty($post['title'])): ?>
                        <h3 class="post-title"><?php echo esc_html($post['title']); ?></h3>
                    <?php endif; ?>
                    <div class="post-excerpt">
                        <?php echo wp_kses_post(eg_social_timeline_truncate($post['content'], 200)); ?>
                    </div>
                </div>
                <footer class="timeline-footer">
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
    $icons = array(
        'mastodon' => '<svg width="20" height="20" viewBox="0 0 61 65" xmlns="http://www.w3.org/2000/svg"><path d="M60.7 14.2c-.8-4.2-3.5-8-7.6-10.3C48.9 1.5 44 .3 38.5.3h-.3c-5.5 0-10.3 1.2-14.6 3.6-4.1 2.3-6.8 6.1-7.6 10.3-.9 4.5-1.1 9.1-.7 13.7.2 2.7.5 5.3 1 7.9.9 5 2.5 9.8 5.3 14 3.8 5.7 9.4 9.6 16 10.9 6.9 1.4 14-.5 19.8-5.3.3-.3.6-.5.8-.8l-3.8-3.2c-.2.2-.4.4-.6.6-4.5 3.8-10.4 5.2-15.9 4-5.3-1.1-9.8-4.3-12.8-9-2.3-3.6-3.6-7.7-4.3-12-.5-2.4-.7-4.9-.9-7.4-.3-4.2-.2-8.3.6-12.4.6-3.1 2.7-5.8 5.6-7.5 3.4-2 7.2-3 11.3-3h.3c4.1 0 7.9 1 11.3 3 2.9 1.7 5 4.4 5.6 7.5.8 4.1.9 8.2.6 12.4-.1 2.5-.4 5-.9 7.4-.7 4.3-2 8.4-4.3 12-2.3 3.6-5.6 6.2-9.7 7.5-1.7.5-3.5.8-5.3.9v5c2.2-.1 4.4-.4 6.5-1 5.2-1.7 9.5-5 12.4-9.5 2.8-4.3 4.5-9.2 5.4-14.4.5-2.6.8-5.2 1-7.9.4-4.6.2-9.2-.7-13.7z"/></svg>',
        'diggita' => '<svg width="20" height="20" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="45" fill="currentColor"/><text x="50" y="65" font-size="50" font-weight="bold" text-anchor="middle" fill="white">D</text></svg>',
        'bluesky' => '<svg width="20" height="20" viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M180 265.4C150 223 84 129.4 56.3 93.8c-47.3-60.6-36-46.8-36-8.5 0 18.2 13 95.7 22.3 143 11.8 59.7 51.7 108.7 111.5 108.7 24.7 0 58-19.3 77-30.7m0 0c-19 11.4-52.3 30.7-77 30.7 59.8 0 99.7-49 111.5-108.7 9.3-47.3 22.3-124.8 22.3-143 0-38.3 11.3-52.1-36-8.5-27.7 35.6-93.7 129.2-123.7 171.6"/></svg>'
    );
    
    return isset($icons[$platform]) ? $icons[$platform] : '';
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
