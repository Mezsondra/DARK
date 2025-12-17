<?php
/**
 * Plugin Name: Dark Mode Pro
 * Plugin URI: https://darkmodepro.com
 * Description: Professional dark mode plugin with 24+ toggle styles, color presets, time-based activation, analytics, and typography controls.
 * Version: 1.0.0
 * Author: Dark Mode Pro Team
 * Author URI: https://darkmodepro.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dark-mode-pro
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DMP_VERSION', '1.0.0');
define('DMP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DMP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DMP_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Dark Mode Pro Class
 */
final class Dark_Mode_Pro {

    /**
     * Single instance
     */
    private static $instance = null;

    /**
     * Plugin options
     */
    private $options = array();

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_options();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Load plugin options
     */
    private function load_options() {
        $defaults = $this->get_default_options();
        $this->options = wp_parse_args(get_option('dmp_options', array()), $defaults);
    }

    /**
     * Get default options
     */
    public function get_default_options() {
        return array(
            // General Settings
            'enabled' => true,
            'default_mode' => 'light',
            'color_engine' => 'css_variables',
            'remember_choice' => true,
            'admin_dark_mode' => false,

            // Toggle Switch
            'switch_style' => 'classic',
            'switch_position' => 'bottom-right',
            'switch_size' => 'medium',
            'show_on_mobile' => true,
            'hide_on_pages' => array(),

            // Color Preset
            'color_preset' => 'midnight',
            'custom_colors' => array(
                'background' => '#1a1a2e',
                'surface' => '#16213e',
                'primary' => '#0f3460',
                'text' => '#e4e4e4',
                'text_secondary' => '#a0a0a0',
                'accent' => '#e94560',
                'link' => '#4da8da',
                'border' => '#2a2a4a',
            ),

            // Time-based Settings
            'time_based_enabled' => false,
            'time_based_mode' => 'sunset',
            'schedule_start' => '19:00',
            'schedule_end' => '07:00',
            'use_visitor_timezone' => true,

            // Typography
            'typography_enabled' => false,
            'dark_font_size_adjust' => 0,
            'dark_line_height_adjust' => 0,
            'dark_letter_spacing' => 0,
            'font_smoothing' => true,

            // Analytics
            'analytics_enabled' => false,
            'track_toggle_events' => true,
            'track_time_spent' => true,
            'email_reports_enabled' => false,
            'email_report_frequency' => 'weekly',
            'email_report_recipients' => '',

            // Advanced
            'transition_duration' => 300,
            'exclude_elements' => '',
            'custom_css_light' => '',
            'custom_css_dark' => '',
            'keyboard_shortcut' => 'd',
            'enable_keyboard_shortcut' => true,
        );
    }

    /**
     * Include required files
     */
    private function includes() {
        require_once DMP_PLUGIN_DIR . 'includes/class-color-presets.php';
        require_once DMP_PLUGIN_DIR . 'includes/class-switch-styles.php';
        require_once DMP_PLUGIN_DIR . 'includes/class-time-based.php';
        require_once DMP_PLUGIN_DIR . 'includes/class-analytics.php';
        require_once DMP_PLUGIN_DIR . 'includes/class-typography.php';

        if (is_admin()) {
            require_once DMP_PLUGIN_DIR . 'admin/class-admin.php';
            require_once DMP_PLUGIN_DIR . 'admin/class-dashboard-widget.php';
        }
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Frontend
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('wp_footer', array($this, 'render_toggle_switch'));
        add_action('wp_head', array($this, 'output_custom_styles'));

        // Admin
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        }

        // REST API
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // AJAX handlers
        add_action('wp_ajax_dmp_track_event', array($this, 'ajax_track_event'));
        add_action('wp_ajax_nopriv_dmp_track_event', array($this, 'ajax_track_event'));

        // Cron for email reports
        add_action('dmp_send_email_report', array($this, 'send_email_report'));

        // Shortcode
        add_shortcode('dark_mode_toggle', array($this, 'shortcode_toggle'));
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables for analytics
        $this->create_analytics_tables();

        // Set default options
        if (!get_option('dmp_options')) {
            update_option('dmp_options', $this->get_default_options());
        }

        // Schedule email reports cron
        if (!wp_next_scheduled('dmp_send_email_report')) {
            wp_schedule_event(time(), 'weekly', 'dmp_send_email_report');
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        wp_clear_scheduled_hook('dmp_send_email_report');
    }

    /**
     * Create analytics tables
     */
    private function create_analytics_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}dmp_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data longtext,
            user_id bigint(20) DEFAULT 0,
            session_id varchar(100),
            page_url varchar(500),
            user_agent varchar(500),
            ip_hash varchar(64),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (!$this->options['enabled']) {
            return;
        }

        // Check if current page is excluded
        if ($this->is_page_excluded()) {
            return;
        }

        // Main CSS
        wp_enqueue_style(
            'dmp-main',
            DMP_PLUGIN_URL . 'assets/css/dark-mode.css',
            array(),
            DMP_VERSION
        );

        // Switch styles CSS
        wp_enqueue_style(
            'dmp-switches',
            DMP_PLUGIN_URL . 'assets/css/switch-styles.css',
            array(),
            DMP_VERSION
        );

        // Main JS
        wp_enqueue_script(
            'dmp-main',
            DMP_PLUGIN_URL . 'assets/js/dark-mode.js',
            array('jquery'),
            DMP_VERSION,
            true
        );

        // Localize script
        wp_localize_script('dmp-main', 'dmpConfig', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dmp_nonce'),
            'options' => array(
                'defaultMode' => $this->options['default_mode'],
                'colorEngine' => $this->options['color_engine'],
                'rememberChoice' => $this->options['remember_choice'],
                'switchStyle' => $this->options['switch_style'],
                'switchPosition' => $this->options['switch_position'],
                'switchSize' => $this->options['switch_size'],
                'transitionDuration' => $this->options['transition_duration'],
                'keyboardShortcut' => $this->options['keyboard_shortcut'],
                'enableKeyboardShortcut' => $this->options['enable_keyboard_shortcut'],
                'timeBasedEnabled' => $this->options['time_based_enabled'],
                'timeBasedMode' => $this->options['time_based_mode'],
                'scheduleStart' => $this->options['schedule_start'],
                'scheduleEnd' => $this->options['schedule_end'],
                'useVisitorTimezone' => $this->options['use_visitor_timezone'],
                'analyticsEnabled' => $this->options['analytics_enabled'],
                'trackToggleEvents' => $this->options['track_toggle_events'],
                'trackTimeSpent' => $this->options['track_time_spent'],
                'typographyEnabled' => $this->options['typography_enabled'],
                'fontSizeAdjust' => $this->options['dark_font_size_adjust'],
                'lineHeightAdjust' => $this->options['dark_line_height_adjust'],
                'letterSpacing' => $this->options['dark_letter_spacing'],
                'fontSmoothing' => $this->options['font_smoothing'],
            ),
            'colorPreset' => $this->get_active_color_preset(),
            'sunTimes' => $this->get_sun_times(),
        ));
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'dark-mode-pro') === false) {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'dmp-admin',
            DMP_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            DMP_VERSION
        );

        // Color picker
        wp_enqueue_style('wp-color-picker');

        // Chart.js for analytics
        wp_enqueue_script(
            'chart-js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
            array(),
            '4.4.1',
            true
        );

        // Admin JS
        wp_enqueue_script(
            'dmp-admin',
            DMP_PLUGIN_URL . 'admin/js/admin.js',
            array('jquery', 'wp-color-picker', 'chart-js'),
            DMP_VERSION,
            true
        );

        wp_localize_script('dmp-admin', 'dmpAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dmp_admin_nonce'),
            'options' => $this->options,
        ));
    }

    /**
     * Check if current page is excluded
     */
    private function is_page_excluded() {
        $excluded = $this->options['hide_on_pages'];
        if (empty($excluded)) {
            return false;
        }

        $current_page_id = get_the_ID();
        return in_array($current_page_id, (array) $excluded);
    }

    /**
     * Get active color preset
     */
    private function get_active_color_preset() {
        $preset_name = $this->options['color_preset'];

        if ($preset_name === 'custom') {
            return $this->options['custom_colors'];
        }

        $presets = DMP_Color_Presets::get_presets();
        return isset($presets[$preset_name]) ? $presets[$preset_name]['colors'] : $presets['midnight']['colors'];
    }

    /**
     * Get sun times for location
     */
    private function get_sun_times() {
        // Default to a general sunset/sunrise time
        // In production, this would use geolocation API
        return array(
            'sunrise' => '06:30',
            'sunset' => '18:30',
        );
    }

    /**
     * Output custom styles
     */
    public function output_custom_styles() {
        if (!$this->options['enabled']) {
            return;
        }

        $colors = $this->get_active_color_preset();
        ?>
        <style id="dmp-custom-styles">
            :root {
                --dmp-transition-duration: <?php echo intval($this->options['transition_duration']); ?>ms;
            }

            body.dmp-dark-mode {
                --dmp-bg: <?php echo esc_attr($colors['background']); ?>;
                --dmp-surface: <?php echo esc_attr($colors['surface']); ?>;
                --dmp-primary: <?php echo esc_attr($colors['primary']); ?>;
                --dmp-text: <?php echo esc_attr($colors['text']); ?>;
                --dmp-text-secondary: <?php echo esc_attr($colors['text_secondary']); ?>;
                --dmp-accent: <?php echo esc_attr($colors['accent']); ?>;
                --dmp-link: <?php echo esc_attr($colors['link']); ?>;
                --dmp-border: <?php echo esc_attr($colors['border']); ?>;
            }

            <?php if ($this->options['typography_enabled'] && $this->options['font_smoothing']): ?>
            body.dmp-dark-mode {
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            <?php endif; ?>

            <?php if (!empty($this->options['custom_css_light'])): ?>
            body:not(.dmp-dark-mode) {
                <?php echo wp_strip_all_tags($this->options['custom_css_light']); ?>
            }
            <?php endif; ?>

            <?php if (!empty($this->options['custom_css_dark'])): ?>
            body.dmp-dark-mode {
                <?php echo wp_strip_all_tags($this->options['custom_css_dark']); ?>
            }
            <?php endif; ?>
        </style>
        <?php
    }

    /**
     * Render toggle switch
     */
    public function render_toggle_switch() {
        if (!$this->options['enabled']) {
            return;
        }

        if ($this->is_page_excluded()) {
            return;
        }

        $style = $this->options['switch_style'];
        $position = $this->options['switch_position'];
        $size = $this->options['switch_size'];

        $classes = array(
            'dmp-toggle-wrapper',
            'dmp-position-' . $position,
            'dmp-size-' . $size,
            'dmp-style-' . $style,
        );

        if (!$this->options['show_on_mobile']) {
            $classes[] = 'dmp-hide-mobile';
        }

        include DMP_PLUGIN_DIR . 'templates/toggle-switch.php';
    }

    /**
     * Shortcode for toggle switch
     */
    public function shortcode_toggle($atts) {
        $atts = shortcode_atts(array(
            'style' => $this->options['switch_style'],
            'size' => $this->options['switch_size'],
            'class' => '',
        ), $atts);

        ob_start();
        include DMP_PLUGIN_DIR . 'templates/toggle-inline.php';
        return ob_get_clean();
    }

    /**
     * Register REST routes
     */
    public function register_rest_routes() {
        register_rest_route('dark-mode-pro/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_settings'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ));

        register_rest_route('dark-mode-pro/v1', '/analytics', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_get_analytics'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            },
        ));
    }

    /**
     * REST: Get settings
     */
    public function rest_get_settings() {
        return rest_ensure_response($this->options);
    }

    /**
     * REST: Get analytics
     */
    public function rest_get_analytics() {
        $analytics = new DMP_Analytics();
        return rest_ensure_response($analytics->get_dashboard_data());
    }

    /**
     * AJAX: Track event
     */
    public function ajax_track_event() {
        check_ajax_referer('dmp_nonce', 'nonce');

        if (!$this->options['analytics_enabled']) {
            wp_send_json_error('Analytics disabled');
        }

        $event_type = sanitize_text_field($_POST['event_type'] ?? '');
        $event_data = isset($_POST['event_data']) ? json_decode(stripslashes($_POST['event_data']), true) : array();

        $analytics = new DMP_Analytics();
        $result = $analytics->track_event($event_type, $event_data);

        wp_send_json_success($result);
    }

    /**
     * Send email report
     */
    public function send_email_report() {
        if (!$this->options['email_reports_enabled']) {
            return;
        }

        $analytics = new DMP_Analytics();
        $analytics->send_email_report();
    }

    /**
     * Get option
     */
    public function get_option($key, $default = null) {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * Update option
     */
    public function update_option($key, $value) {
        $this->options[$key] = $value;
        update_option('dmp_options', $this->options);
    }
}

/**
 * Initialize plugin
 */
function dark_mode_pro() {
    return Dark_Mode_Pro::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'dark_mode_pro');
