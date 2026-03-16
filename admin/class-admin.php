<?php
/**
 * Admin Class
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DMP_Admin Class
 */
class DMP_Admin {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Options
     */
    private $options;

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
        $this->options = get_option('dmp_options', array());
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_ajax_dmp_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_dmp_export_analytics', array($this, 'ajax_export_analytics'));
        add_action('wp_ajax_dmp_send_test_email', array($this, 'ajax_send_test_email'));
        add_action('wp_ajax_dmp_reset_settings', array($this, 'ajax_reset_settings'));
        add_filter('plugin_action_links_' . DMP_PLUGIN_BASENAME, array($this, 'add_plugin_links'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Dark Mode Pro', 'dark-mode-pro'),
            __('Dark Mode Pro', 'dark-mode-pro'),
            'manage_options',
            'dark-mode-pro',
            array($this, 'render_settings_page'),
            'dashicons-moon',
            80
        );

        add_submenu_page(
            'dark-mode-pro',
            __('Settings', 'dark-mode-pro'),
            __('Settings', 'dark-mode-pro'),
            'manage_options',
            'dark-mode-pro',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            'dark-mode-pro',
            __('Switch Styles', 'dark-mode-pro'),
            __('Switch Styles', 'dark-mode-pro'),
            'manage_options',
            'dark-mode-pro-switches',
            array($this, 'render_switches_page')
        );

        add_submenu_page(
            'dark-mode-pro',
            __('Color Presets', 'dark-mode-pro'),
            __('Color Presets', 'dark-mode-pro'),
            'manage_options',
            'dark-mode-pro-colors',
            array($this, 'render_colors_page')
        );

        add_submenu_page(
            'dark-mode-pro',
            __('Analytics', 'dark-mode-pro'),
            __('Analytics', 'dark-mode-pro'),
            'manage_options',
            'dark-mode-pro-analytics',
            array($this, 'render_analytics_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('dmp_options_group', 'dmp_options', array($this, 'sanitize_options'));
    }

    /**
     * Sanitize options
     */
    public function sanitize_options($input) {
        $sanitized = array();

        // Boolean fields
        $boolean_fields = array(
            'enabled', 'remember_choice', 'admin_dark_mode', 'show_on_mobile',
            'time_based_enabled', 'use_visitor_timezone', 'typography_enabled',
            'font_smoothing', 'analytics_enabled', 'track_toggle_events',
            'track_time_spent', 'email_reports_enabled', 'enable_keyboard_shortcut'
        );

        foreach ($boolean_fields as $field) {
            $sanitized[$field] = !empty($input[$field]);
        }

        // String fields
        $string_fields = array(
            'default_mode', 'color_engine', 'switch_style', 'switch_position', 'switch_size',
            'color_preset', 'time_based_mode', 'schedule_start', 'schedule_end',
            'email_report_frequency', 'keyboard_shortcut'
        );

        foreach ($string_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? sanitize_text_field($input[$field]) : '';
        }

        // Integer fields
        $int_fields = array(
            'transition_duration', 'dark_font_size_adjust'
        );

        foreach ($int_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? intval($input[$field]) : 0;
        }

        // Float fields
        $float_fields = array(
            'dark_line_height_adjust', 'dark_letter_spacing'
        );

        foreach ($float_fields as $field) {
            $sanitized[$field] = isset($input[$field]) ? floatval($input[$field]) : 0;
        }

        // Array fields
        $sanitized['hide_on_pages'] = isset($input['hide_on_pages']) ? array_map('intval', (array) $input['hide_on_pages']) : array();

        // Custom colors
        if (isset($input['custom_colors']) && is_array($input['custom_colors'])) {
            $sanitized['custom_colors'] = array();
            foreach ($input['custom_colors'] as $key => $color) {
                $sanitized['custom_colors'][sanitize_key($key)] = sanitize_hex_color($color);
            }
        }

        // Text area fields
        $sanitized['exclude_elements'] = isset($input['exclude_elements']) ? sanitize_textarea_field($input['exclude_elements']) : '';
        $sanitized['custom_css_light'] = isset($input['custom_css_light']) ? wp_strip_all_tags($input['custom_css_light']) : '';
        $sanitized['custom_css_dark'] = isset($input['custom_css_dark']) ? wp_strip_all_tags($input['custom_css_dark']) : '';
        $sanitized['email_report_recipients'] = isset($input['email_report_recipients']) ? sanitize_textarea_field($input['email_report_recipients']) : '';

        return $sanitized;
    }

    /**
     * Add plugin links
     */
    public function add_plugin_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=dark-mode-pro') . '">' . __('Settings', 'dark-mode-pro') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        include DMP_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    /**
     * Render switches page
     */
    public function render_switches_page() {
        include DMP_PLUGIN_DIR . 'admin/views/switches-page.php';
    }

    /**
     * Render colors page
     */
    public function render_colors_page() {
        include DMP_PLUGIN_DIR . 'admin/views/colors-page.php';
    }

    /**
     * Render analytics page
     */
    public function render_analytics_page() {
        include DMP_PLUGIN_DIR . 'admin/views/analytics-page.php';
    }

    /**
     * AJAX: Save settings
     */
    public function ajax_save_settings() {
        check_ajax_referer('dmp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'dark-mode-pro'));
        }

        $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : array();

        if (empty($settings)) {
            wp_send_json_error(__('Invalid settings', 'dark-mode-pro'));
        }

        // Merge incoming settings with existing options so partial updates
        // (e.g., selecting a theme or toggle style) don't wipe out other
        // saved preferences.
        $current_options = get_option('dmp_options', dark_mode_pro()->get_default_options());
        $merged_settings = wp_parse_args($settings, $current_options);

        $sanitized = $this->sanitize_options($merged_settings);
        update_option('dmp_options', $sanitized);

        wp_send_json_success(array(
            'message' => __('Settings saved successfully', 'dark-mode-pro'),
            'settings' => $sanitized,
        ));
    }

    /**
     * AJAX: Export analytics
     */
    public function ajax_export_analytics() {
        check_ajax_referer('dmp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'dark-mode-pro'));
        }

        $days = isset($_POST['days']) ? intval($_POST['days']) : 30;

        $analytics = new DMP_Analytics();
        $csv = $analytics->export_csv($days);

        wp_send_json_success(array(
            'csv' => $csv,
            'filename' => 'dark-mode-pro-analytics-' . date('Y-m-d') . '.csv',
        ));
    }

    /**
     * AJAX: Send test email
     */
    public function ajax_send_test_email() {
        check_ajax_referer('dmp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'dark-mode-pro'));
        }

        $analytics = new DMP_Analytics();
        $result = $analytics->send_email_report();

        if ($result) {
            wp_send_json_success(__('Test email sent successfully', 'dark-mode-pro'));
        } else {
            wp_send_json_error(__('Failed to send test email', 'dark-mode-pro'));
        }
    }

    /**
     * AJAX: Reset settings
     */
    public function ajax_reset_settings() {
        check_ajax_referer('dmp_admin_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'dark-mode-pro'));
        }

        $defaults = dark_mode_pro()->get_default_options();
        update_option('dmp_options', $defaults);

        wp_send_json_success(array(
            'message' => __('Settings reset to defaults', 'dark-mode-pro'),
            'settings' => $defaults,
        ));
    }
}

// Initialize
DMP_Admin::get_instance();
