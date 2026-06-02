<?php
/**
 * Plugin Name: EG Social Timeline
 * Plugin URI: https://git.emanuelegori.uno/emanuelegori/eg-social-timeline
 * Description: Unified chronological timeline of your public activity from Mastodon, Bluesky, Forgejo and Diggita. Zero JavaScript, zero tracking.
 * Version: 1.6.6
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
define('EG_SOCIAL_TIMELINE_VERSION', '1.6.6');
define('EG_SOCIAL_TIMELINE_DIR', plugin_dir_path(__FILE__));
define('EG_SOCIAL_TIMELINE_URL', plugin_dir_url(__FILE__));
define('EG_SOCIAL_TIMELINE_DEBUG', false);

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
                'forgejo_username' => '',
                'forgejo_instance' => 'https://gitea.com',
                'post_limit' => 10,
                'cache_duration' => 3600,
                'show_boosts' => false,
                'show_stats' => true,
                'mastodon_limit' => 20,
                'diggita_limit' => 10,
                'forgejo_limit' => 5,
                'bluesky_handle' => '',
                'bluesky_limit' => 10,
                'truncate_length' => 300,
                'show_images' => false
            )
        )
    );
    
    add_settings_section(
        'eg_social_timeline_main_section',
        __('Social Profiles Configuration', 'eg-social-timeline'),
        'eg_social_timeline_main_section_callback',
        'eg-social-timeline'
    );
    
    add_settings_field(
        'eg_social_timeline_mastodon_url',
        __('Mastodon Profile URL', 'eg-social-timeline'),
        'eg_social_timeline_mastodon_url_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_diggita_username',
        __('Diggita Username', 'eg-social-timeline'),
        'eg_social_timeline_diggita_username_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_forgejo_username',
        __('Forgejo/Gitea Username', 'eg-social-timeline'),
        'eg_social_timeline_forgejo_username_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );
    
    add_settings_field(
        'eg_social_timeline_forgejo_instance',
        __('Forgejo/Gitea Instance URL', 'eg-social-timeline'),
        'eg_social_timeline_forgejo_instance_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );

    add_settings_field(
        'eg_social_timeline_bluesky_handle',
        __('Bluesky Handle', 'eg-social-timeline'),
        'eg_social_timeline_bluesky_handle_callback',
        'eg-social-timeline',
        'eg_social_timeline_main_section'
    );

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
        'eg_social_timeline_diggita_limit',
        __('Max Diggita Posts', 'eg-social-timeline'),
        'eg_social_timeline_diggita_limit_callback',
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
}

// Settings callbacks
function eg_social_timeline_main_section_callback() {
    echo '<p>' . esc_html__('Configure your social profiles to display a unified timeline. At least one profile is required.', 'eg-social-timeline') . '</p>';
}

function eg_social_timeline_limits_section_callback() {
    echo '<p>' . esc_html__('Limit the maximum number of posts per platform. This prevents a very active platform (e.g. Forgejo) from filling all available slots. Set 0 for no limit.', 'eg-social-timeline') . '</p>';
}

function eg_social_timeline_mastodon_url_callback() {
    $options = get_option('eg_social_timeline_options');
    $url = isset($options['mastodon_url']) ? $options['mastodon_url'] : '';
    ?>
    <input type="url" 
           id="eg_social_timeline_mastodon_url" 
           name="eg_social_timeline_options[mastodon_url]" 
           value="<?php echo esc_attr($url); ?>" 
           placeholder="<?php echo esc_attr__('e.g. https://mastodon.social/@yourusername', 'eg-social-timeline'); ?>"
           class="regular-text">
    <p class="description">
        <?php esc_html_e('Full URL of your public Mastodon profile (or other Fediverse instances).', 'eg-social-timeline'); ?>
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
           placeholder="<?php echo esc_attr__('e.g. yourusername', 'eg-social-timeline'); ?>"
           class="regular-text">
    <p class="description">
        <?php esc_html_e('Diggita username (without @).', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_forgejo_username_callback() {
    $options = get_option('eg_social_timeline_options');
    $username = isset($options['forgejo_username']) ? $options['forgejo_username'] : '';
    ?>
    <input type="text" 
           id="eg_social_timeline_forgejo_username" 
           name="eg_social_timeline_options[forgejo_username]" 
           value="<?php echo esc_attr($username); ?>" 
           placeholder="<?php echo esc_attr__('e.g. yourusername', 'eg-social-timeline'); ?>"
           class="regular-text">
    <p class="description">
        <?php esc_html_e('Your Forgejo/Gitea account username (without @).', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_forgejo_instance_callback() {
    $options = get_option('eg_social_timeline_options');
    $instance = isset($options['forgejo_instance']) ? $options['forgejo_instance'] : 'https://gitea.com';
    ?>
    <input type="url" 
           id="eg_social_timeline_forgejo_instance" 
           name="eg_social_timeline_options[forgejo_instance]" 
           value="<?php echo esc_attr($instance); ?>" 
           placeholder="https://gitea.com"
           class="regular-text">
    <p class="description">
        <?php esc_html_e('Full URL of your Forgejo/Gitea instance. Default: https://gitea.com', 'eg-social-timeline'); ?>
    </p>
    <?php
}

function eg_social_timeline_bluesky_handle_callback() {
    $options = get_option('eg_social_timeline_options');
    $handle = isset($options['bluesky_handle']) ? $options['bluesky_handle'] : '';
    ?>
    <input type="text"
           id="eg_social_timeline_bluesky_handle"
           name="eg_social_timeline_options[bluesky_handle]"
           value="<?php echo esc_attr($handle); ?>"
           placeholder="<?php echo esc_attr__('e.g. emanuele.bsky.social', 'eg-social-timeline'); ?>"
           class="regular-text">
    <p class="description">
        <?php esc_html_e('Your Bluesky handle (without @). Example: emanuele.bsky.social', 'eg-social-timeline'); ?>
    </p>
    <?php
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

function eg_social_timeline_diggita_limit_callback() {
    $options = get_option('eg_social_timeline_options');
    $limit = isset($options['diggita_limit']) ? $options['diggita_limit'] : 10;
    ?>
    <input type="number" 
           id="eg_social_timeline_diggita_limit" 
           name="eg_social_timeline_options[diggita_limit]" 
           value="<?php echo esc_attr($limit); ?>" 
           min="0"
           max="100"
           class="small-text">
    <p class="description">
        <?php esc_html_e('Maximum number of Diggita posts to fetch (0 = unlimited). Default: 10', 'eg-social-timeline'); ?>
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

// Sanitization
function eg_social_timeline_sanitize_options($input) {
    $output = array();
    
    $mastodon_url = isset($input['mastodon_url']) ? esc_url_raw($input['mastodon_url']) : '';
    $diggita_username = isset($input['diggita_username']) ? sanitize_text_field($input['diggita_username']) : '';
    $forgejo_username = isset($input['forgejo_username']) ? sanitize_text_field($input['forgejo_username']) : '';
    $bluesky_handle = isset($input['bluesky_handle']) ? sanitize_text_field(ltrim($input['bluesky_handle'], '@')) : '';

    if (empty($mastodon_url) && empty($diggita_username) && empty($forgejo_username) && empty($bluesky_handle)) {
        add_settings_error(
            'eg_social_timeline_options',
            'no_profiles',
            '<strong>' . __('Error:', 'eg-social-timeline') . '</strong> ' .
            __('You must configure at least one social profile (Mastodon, Diggita, Forgejo or Bluesky).', 'eg-social-timeline'),
            'error'
        );

        $old_options = get_option('eg_social_timeline_options');
        return $old_options ? $old_options : array();
    }

    $output['mastodon_url'] = $mastodon_url;
    $output['diggita_username'] = $diggita_username;
    $output['forgejo_username'] = $forgejo_username;
    $output['bluesky_handle'] = $bluesky_handle;
    
    $forgejo_instance = isset($input['forgejo_instance']) ? esc_url_raw($input['forgejo_instance']) : 'https://gitea.com';
    // Accetta solo HTTPS — HTTP espone il token in chiaro e apre a SSRF su reti interne
    if ( ! empty($forgejo_instance) && strpos($forgejo_instance, 'https://') === 0 ) {
        $output['forgejo_instance'] = $forgejo_instance;
    } else {
        $output['forgejo_instance'] = 'https://gitea.com';
    }
    
    $limit = isset($input['post_limit']) ? intval($input['post_limit']) : 10;
    $output['post_limit'] = max(1, min(100, $limit));
    
    $mastodon_limit = isset($input['mastodon_limit']) ? intval($input['mastodon_limit']) : 20;
    $output['mastodon_limit'] = max(0, min(100, $mastodon_limit));
    
    $diggita_limit = isset($input['diggita_limit']) ? intval($input['diggita_limit']) : 10;
    $output['diggita_limit'] = max(0, min(100, $diggita_limit));
    
    $forgejo_limit = isset($input['forgejo_limit']) ? intval($input['forgejo_limit']) : 5;
    $output['forgejo_limit'] = max(0, min(50, $forgejo_limit));

    $bluesky_limit = isset($input['bluesky_limit']) ? intval($input['bluesky_limit']) : 10;
    $output['bluesky_limit'] = max(0, min(100, $bluesky_limit));

    $duration = isset($input['cache_duration']) ? intval($input['cache_duration']) : 3600;
    $output['cache_duration'] = in_array($duration, array(1800, 3600, 7200, 14400, 28800, 86400)) ? $duration : 3600;
    
    $output['show_boosts'] = isset($input['show_boosts']) ? true : false;
    $output['show_stats'] = isset($input['show_stats']) ? true : false;
    $output['show_images'] = isset($input['show_images']) ? true : false;

    $truncate = isset($input['truncate_length']) ? intval($input['truncate_length']) : 300;
    $output['truncate_length'] = ($truncate === 0) ? 0 : max(50, min(600, $truncate));
    
    delete_transient('eg_social_timeline_cache');
    
    add_settings_error(
        'eg_social_timeline_options',
        'settings_updated',
        __('Settings saved successfully! Cache cleared.', 'eg-social-timeline'),
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
    
    if (empty($options['mastodon_url']) && empty($options['diggita_username']) && empty($options['forgejo_username']) && empty($options['bluesky_handle'])) {
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
        <p><?php esc_html_e('Inserisci nel contenuto dell\'articolo:', 'eg-social-timeline'); ?></p>
        <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #6364FF;"><code>[eg_social_timeline]</code></pre>
        
        <p><?php esc_html_e('Opzionale: limita il numero di post:', 'eg-social-timeline'); ?></p>
        <pre style="background: #f5f5f5; padding: 10px; border-left: 4px solid #6364FF;"><code>[eg_social_timeline limit="20"]</code></pre>
        
        <h3><?php esc_html_e('Flush Cache Manually', 'eg-social-timeline'); ?></h3>
        <p><?php esc_html_e('Per forzare l\'aggiornamento immediato dei feed:', 'eg-social-timeline'); ?></p>
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
function eg_social_timeline_get_mastodon_account_id($profile_url) {
    if (!eg_social_timeline_is_public_url($profile_url)) {
        return false;
    }

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
            error_log('EG Social Timeline: Mastodon account lookup error - ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return false;
    }
    
    $data = json_decode(wp_remote_retrieve_body($response), true);
    
    if (!isset($data['id'])) {
        return false;
    }
    
    set_transient($cache_key, $data['id'], MONTH_IN_SECONDS);
    
    return $data['id'];
}

// Fetch Mastodon posts via API
function eg_social_timeline_fetch_mastodon($profile_url, $limit = 0) {
    if (empty($profile_url)) {
        return array();
    }
    
    $account_id = eg_social_timeline_get_mastodon_account_id($profile_url);
    
    if (!$account_id) {
        if (EG_SOCIAL_TIMELINE_DEBUG) {
            error_log('EG Social Timeline: Could not get Mastodon account ID for ' . $profile_url); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return array();
    }
    
    preg_match('#https?://([^/]+)/#', $profile_url, $matches);
    $instance = $matches[1];
    
    $options = get_option('eg_social_timeline_options');
    $show_boosts = !empty($options['show_boosts']);
    
    // Use configured limit or default to 40
    $api_limit = ($limit > 0) ? min($limit, 40) : 40;
    
    $api_url = "https://{$instance}/api/v1/accounts/{$account_id}/statuses";
    
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

// Fetch Diggita RSS with statistics parsing
function eg_social_timeline_fetch_diggita($username, $limit = 0) {
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
            error_log('EG Social Timeline Diggita Error: ' . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
        }
        return array();
    }
    
    $body = wp_remote_retrieve_body($response);
    
    if (empty($body)) {
        return array();
    }
    
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
            'platform' => 'diggita',
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

        $posts[] = array(
            'platform'        => 'bluesky',
            'date'            => $timestamp,
            'title'           => $title,
            'content'         => $content,
            'link'            => $post_url,
            'is_boost'        => $is_repost,
            'image_url'       => '',
            'image_alt'       => '',
            'favourites_count' => intval($post['likeCount'] ?? 0),
            'reblogs_count'   => intval($post['repostCount'] ?? 0),
            'replies_count'   => intval($post['replyCount'] ?? 0),
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
    
    // Get platform limits
    $mastodon_limit = isset($options['mastodon_limit']) ? intval($options['mastodon_limit']) : 20;
    $diggita_limit  = isset($options['diggita_limit'])  ? intval($options['diggita_limit'])  : 10;
    $forgejo_limit  = isset($options['forgejo_limit'])  ? intval($options['forgejo_limit'])  : 5;
    $bluesky_limit  = isset($options['bluesky_limit'])  ? intval($options['bluesky_limit'])  : 10;

    // Fetch with limits
    if (!empty($options['mastodon_url'])) {
        $mastodon_posts = eg_social_timeline_fetch_mastodon($options['mastodon_url'], $mastodon_limit);
        $all_posts = array_merge($all_posts, $mastodon_posts);
    }

    if (!empty($options['diggita_username'])) {
        $diggita_posts = eg_social_timeline_fetch_diggita($options['diggita_username'], $diggita_limit);
        $all_posts = array_merge($all_posts, $diggita_posts);
    }

    if (!empty($options['forgejo_username'])) {
        $forgejo_instance = !empty($options['forgejo_instance']) ? $options['forgejo_instance'] : 'https://gitea.com';
        $forgejo_posts = eg_social_timeline_fetch_forgejo($options['forgejo_username'], $forgejo_instance, $forgejo_limit);
        $all_posts = array_merge($all_posts, $forgejo_posts);
    }

    if (!empty($options['bluesky_handle'])) {
        $bluesky_posts = eg_social_timeline_fetch_bluesky($options['bluesky_handle'], $bluesky_limit);
        $all_posts = array_merge($all_posts, $bluesky_posts);
    }
    
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
    
    if (empty($options['mastodon_url']) && empty($options['diggita_username']) && empty($options['forgejo_username']) && empty($options['bluesky_handle'])) {
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
        return '<div class="eg-social-timeline-empty">' . 
               esc_html__('No posts available at the moment.', 'eg-social-timeline') . 
               '</div>';
    }
    
    $posts = array_slice($posts, 0, $limit);
    
    $show_stats = !empty($options['show_stats']);
    $show_images = !empty($options['show_images']);
    $truncate_length = isset($options['truncate_length']) ? intval($options['truncate_length']) : 300;
    
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
                    <label for="filter-<?php echo esc_attr($platform); ?>" 
                           class="filter-checkbox-label">
                        <span class="platform-icon-small">
                            <?php echo eg_social_timeline_get_icon($platform); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG sanitizzato internamente dalla funzione ?>
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
                        <?php echo eg_social_timeline_get_icon($post['platform']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG sanitizzato internamente dalla funzione ?>
                    </span>
                    <span class="platform-name"><?php echo esc_html(eg_social_timeline_get_platform_name($post['platform'])); ?></span>
                    
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
                                    echo esc_attr($post['platform'] === 'diggita' ? __('Points', 'eg-social-timeline') : __('Favourites', 'eg-social-timeline')); 
                                ?>">
                                    <?php echo $post['platform'] === 'diggita' ? '⭐' : '❤️'; ?> <?php echo esc_html($post['favourites_count']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($post['reblogs_count'] > 0): ?>
                                <span class="stat-item stat-boosts" title="<?php esc_attr_e('Boost', 'eg-social-timeline'); ?>">
                                    🔁 <?php echo esc_html($post['reblogs_count']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($post['replies_count'] > 0): ?>
                                <span class="stat-item stat-replies" title="<?php 
                                    echo esc_attr($post['platform'] === 'diggita' ? __('Comments', 'eg-social-timeline') : __('Replies', 'eg-social-timeline')); 
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
        'diggita' => __('Diggita', 'eg-social-timeline'),
        'bluesky' => __('Bluesky', 'eg-social-timeline'),
        'forgejo' => __('Forgejo', 'eg-social-timeline')
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
    
    // Tag e attributi SVG consentiti per wp_kses
    $svg_kses = array(
        'svg'    => array( 'width' => true, 'height' => true, 'viewbox' => true, 'xmlns' => true, 'fill' => true, 'role' => true, 'aria-hidden' => true, 'class' => true ),
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
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=eg-social-timeline')) . '">' . __('Settings', 'eg-social-timeline') . '</a>';
    $docs_link = '<a href="https://git.emanuelegori.uno/emanuelegori/eg-social-timeline" target="_blank" rel="noopener noreferrer">' . __('Documentation', 'eg-social-timeline') . '</a>';
    
    array_unshift($links, $docs_link, $settings_link);
    
    return $links;
}

