<?php
/**
 * Settings Page View
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('dmp_options', dark_mode_pro()->get_default_options());
$switch_styles = DMP_Switch_Styles::get_styles();
$switch_positions = DMP_Switch_Styles::get_positions();
$switch_sizes = DMP_Switch_Styles::get_sizes();
$time_modes = DMP_Time_Based::get_modes();
?>

<div class="wrap dmp-admin-wrap">
    <div class="dmp-admin-header">
        <div class="dmp-header-content">
            <h1>
                <span class="dashicons dashicons-moon"></span>
                <?php esc_html_e('Dark Mode Pro', 'dark-mode-pro'); ?>
            </h1>
            <p class="dmp-version"><?php printf(esc_html__('Version %s', 'dark-mode-pro'), DMP_VERSION); ?></p>
        </div>
        <div class="dmp-header-actions">
            <button type="button" class="button dmp-reset-btn">
                <span class="dashicons dashicons-image-rotate"></span>
                <?php esc_html_e('Reset to Defaults', 'dark-mode-pro'); ?>
            </button>
            <button type="button" class="button button-primary dmp-save-btn">
                <span class="dashicons dashicons-saved"></span>
                <?php esc_html_e('Save Settings', 'dark-mode-pro'); ?>
            </button>
        </div>
    </div>

    <div class="dmp-admin-content">
        <div class="dmp-tabs">
            <button class="dmp-tab active" data-tab="general">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php esc_html_e('General', 'dark-mode-pro'); ?>
            </button>
            <button class="dmp-tab" data-tab="toggle">
                <span class="dashicons dashicons-marker"></span>
                <?php esc_html_e('Toggle Switch', 'dark-mode-pro'); ?>
            </button>
            <button class="dmp-tab" data-tab="timing">
                <span class="dashicons dashicons-clock"></span>
                <?php esc_html_e('Time-Based', 'dark-mode-pro'); ?>
            </button>
            <button class="dmp-tab" data-tab="typography">
                <span class="dashicons dashicons-editor-textcolor"></span>
                <?php esc_html_e('Typography', 'dark-mode-pro'); ?>
            </button>
            <button class="dmp-tab" data-tab="analytics">
                <span class="dashicons dashicons-chart-area"></span>
                <?php esc_html_e('Analytics', 'dark-mode-pro'); ?>
            </button>
            <button class="dmp-tab" data-tab="advanced">
                <span class="dashicons dashicons-admin-tools"></span>
                <?php esc_html_e('Advanced', 'dark-mode-pro'); ?>
            </button>
        </div>

        <form id="dmp-settings-form" class="dmp-settings-form">
            <!-- General Tab -->
            <div class="dmp-tab-content active" data-tab="general">
                <div class="dmp-card">
                    <h2><?php esc_html_e('General Settings', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="enabled" <?php checked($options['enabled']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Enable Dark Mode', 'dark-mode-pro'); ?></span>
                        </label>
                        <p class="dmp-field-desc"><?php esc_html_e('Enable or disable dark mode functionality on your site.', 'dark-mode-pro'); ?></p>
                    </div>

                    <div class="dmp-field">
                        <label for="default_mode"><?php esc_html_e('Default Mode', 'dark-mode-pro'); ?></label>
                        <select name="default_mode" id="default_mode">
                            <option value="light" <?php selected($options['default_mode'], 'light'); ?>><?php esc_html_e('Light Mode', 'dark-mode-pro'); ?></option>
                            <option value="dark" <?php selected($options['default_mode'], 'dark'); ?>><?php esc_html_e('Dark Mode', 'dark-mode-pro'); ?></option>
                            <option value="system" <?php selected($options['default_mode'], 'system'); ?>><?php esc_html_e('System Preference', 'dark-mode-pro'); ?></option>
                        </select>
                        <p class="dmp-field-desc"><?php esc_html_e('The default mode for first-time visitors.', 'dark-mode-pro'); ?></p>
                    </div>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="remember_choice" <?php checked($options['remember_choice']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Remember User Choice', 'dark-mode-pro'); ?></span>
                        </label>
                        <p class="dmp-field-desc"><?php esc_html_e('Save user preference in browser storage.', 'dark-mode-pro'); ?></p>
                    </div>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="admin_dark_mode" <?php checked($options['admin_dark_mode']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Enable Admin Dark Mode', 'dark-mode-pro'); ?></span>
                        </label>
                        <p class="dmp-field-desc"><?php esc_html_e('Apply dark mode to WordPress admin area.', 'dark-mode-pro'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Toggle Switch Tab -->
            <div class="dmp-tab-content" data-tab="toggle">
                <div class="dmp-card">
                    <h2><?php esc_html_e('Toggle Switch Settings', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label for="switch_style"><?php esc_html_e('Switch Style', 'dark-mode-pro'); ?></label>
                        <select name="switch_style" id="switch_style">
                            <?php foreach (DMP_Switch_Styles::get_categories() as $cat_key => $cat_name): ?>
                                <optgroup label="<?php echo esc_attr($cat_name); ?>">
                                    <?php foreach (DMP_Switch_Styles::get_styles_by_category($cat_key) as $style_key => $style): ?>
                                        <option value="<?php echo esc_attr($style_key); ?>" <?php selected($options['switch_style'], $style_key); ?>>
                                            <?php echo esc_html($style['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                        <p class="dmp-field-desc"><?php esc_html_e('Choose from 24+ toggle switch designs.', 'dark-mode-pro'); ?></p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=dark-mode-pro-switches')); ?>" class="button button-secondary">
                            <?php esc_html_e('Preview All Styles', 'dark-mode-pro'); ?>
                        </a>
                    </div>

                    <div class="dmp-field">
                        <label for="switch_position"><?php esc_html_e('Position', 'dark-mode-pro'); ?></label>
                        <select name="switch_position" id="switch_position">
                            <?php foreach ($switch_positions as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($options['switch_position'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="dmp-field">
                        <label for="switch_size"><?php esc_html_e('Size', 'dark-mode-pro'); ?></label>
                        <select name="switch_size" id="switch_size">
                            <?php foreach ($switch_sizes as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($options['switch_size'], $key); ?>>
                                    <?php echo esc_html($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="show_on_mobile" <?php checked($options['show_on_mobile']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Show on Mobile', 'dark-mode-pro'); ?></span>
                        </label>
                    </div>

                    <div class="dmp-field">
                        <label for="hide_on_pages"><?php esc_html_e('Hide on Pages', 'dark-mode-pro'); ?></label>
                        <select name="hide_on_pages[]" id="hide_on_pages" multiple>
                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page):
                            ?>
                                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected(in_array($page->ID, (array) $options['hide_on_pages'])); ?>>
                                    <?php echo esc_html($page->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="dmp-field-desc"><?php esc_html_e('Hide the toggle switch on specific pages.', 'dark-mode-pro'); ?></p>
                    </div>
                </div>

                <div class="dmp-card">
                    <h2><?php esc_html_e('Switch Preview', 'dark-mode-pro'); ?></h2>
                    <div class="dmp-switch-preview">
                        <div id="dmp-preview-container" class="dmp-preview-light">
                            <!-- Preview will be rendered here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time-Based Tab -->
            <div class="dmp-tab-content" data-tab="timing">
                <div class="dmp-card">
                    <h2><?php esc_html_e('Time-Based Activation', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="time_based_enabled" <?php checked($options['time_based_enabled']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Enable Time-Based Mode', 'dark-mode-pro'); ?></span>
                        </label>
                        <p class="dmp-field-desc"><?php esc_html_e('Automatically switch modes based on time.', 'dark-mode-pro'); ?></p>
                    </div>

                    <div class="dmp-field dmp-time-based-options">
                        <label><?php esc_html_e('Activation Mode', 'dark-mode-pro'); ?></label>
                        <div class="dmp-radio-group">
                            <?php foreach ($time_modes as $mode_key => $mode): ?>
                                <label class="dmp-radio-option">
                                    <input type="radio" name="time_based_mode" value="<?php echo esc_attr($mode_key); ?>" <?php checked($options['time_based_mode'], $mode_key); ?>>
                                    <span class="dmp-radio-label">
                                        <strong><?php echo esc_html($mode['name']); ?></strong>
                                        <span><?php echo esc_html($mode['description']); ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="dmp-field dmp-schedule-options">
                        <label><?php esc_html_e('Schedule', 'dark-mode-pro'); ?></label>
                        <div class="dmp-time-range">
                            <div class="dmp-time-input">
                                <label for="schedule_start"><?php esc_html_e('Dark Mode Starts', 'dark-mode-pro'); ?></label>
                                <input type="time" name="schedule_start" id="schedule_start" value="<?php echo esc_attr($options['schedule_start']); ?>">
                            </div>
                            <span class="dmp-time-separator"><?php esc_html_e('to', 'dark-mode-pro'); ?></span>
                            <div class="dmp-time-input">
                                <label for="schedule_end"><?php esc_html_e('Dark Mode Ends', 'dark-mode-pro'); ?></label>
                                <input type="time" name="schedule_end" id="schedule_end" value="<?php echo esc_attr($options['schedule_end']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="use_visitor_timezone" <?php checked($options['use_visitor_timezone']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Use Visitor Timezone', 'dark-mode-pro'); ?></span>
                        </label>
                        <p class="dmp-field-desc"><?php esc_html_e('Use visitor\'s local timezone instead of site timezone.', 'dark-mode-pro'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Typography Tab -->
            <div class="dmp-tab-content" data-tab="typography">
                <div class="dmp-card">
                    <h2><?php esc_html_e('Typography Settings', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="typography_enabled" <?php checked($options['typography_enabled']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Enable Typography Adjustments', 'dark-mode-pro'); ?></span>
                        </label>
                        <p class="dmp-field-desc"><?php esc_html_e('Adjust font settings when dark mode is active.', 'dark-mode-pro'); ?></p>
                    </div>

                    <div class="dmp-typography-options">
                        <div class="dmp-field">
                            <label for="dark_font_size_adjust"><?php esc_html_e('Font Size Adjustment', 'dark-mode-pro'); ?></label>
                            <div class="dmp-range-slider">
                                <input type="range" name="dark_font_size_adjust" id="dark_font_size_adjust"
                                       min="-2" max="3" step="1" value="<?php echo esc_attr($options['dark_font_size_adjust']); ?>">
                                <span class="dmp-range-value"><?php echo intval($options['dark_font_size_adjust']); ?>px</span>
                            </div>
                            <p class="dmp-field-desc"><?php esc_html_e('Increase or decrease font size in dark mode.', 'dark-mode-pro'); ?></p>
                        </div>

                        <div class="dmp-field">
                            <label for="dark_line_height_adjust"><?php esc_html_e('Line Height Adjustment', 'dark-mode-pro'); ?></label>
                            <div class="dmp-range-slider">
                                <input type="range" name="dark_line_height_adjust" id="dark_line_height_adjust"
                                       min="-0.1" max="0.3" step="0.1" value="<?php echo esc_attr($options['dark_line_height_adjust']); ?>">
                                <span class="dmp-range-value"><?php echo floatval($options['dark_line_height_adjust']); ?></span>
                            </div>
                            <p class="dmp-field-desc"><?php esc_html_e('Adjust line spacing for better readability.', 'dark-mode-pro'); ?></p>
                        </div>

                        <div class="dmp-field">
                            <label for="dark_letter_spacing"><?php esc_html_e('Letter Spacing', 'dark-mode-pro'); ?></label>
                            <div class="dmp-range-slider">
                                <input type="range" name="dark_letter_spacing" id="dark_letter_spacing"
                                       min="-0.5" max="1" step="0.5" value="<?php echo esc_attr($options['dark_letter_spacing']); ?>">
                                <span class="dmp-range-value"><?php echo floatval($options['dark_letter_spacing']); ?>px</span>
                            </div>
                        </div>

                        <div class="dmp-field">
                            <label class="dmp-toggle-field">
                                <input type="checkbox" name="font_smoothing" <?php checked($options['font_smoothing']); ?>>
                                <span class="dmp-toggle-switch"></span>
                                <span class="dmp-field-label"><?php esc_html_e('Enable Font Smoothing', 'dark-mode-pro'); ?></span>
                            </label>
                            <p class="dmp-field-desc"><?php esc_html_e('Apply antialiasing for sharper text on dark backgrounds.', 'dark-mode-pro'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div class="dmp-tab-content" data-tab="analytics">
                <div class="dmp-card">
                    <h2><?php esc_html_e('Analytics Settings', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="analytics_enabled" <?php checked($options['analytics_enabled']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Enable Analytics', 'dark-mode-pro'); ?></span>
                        </label>
                        <p class="dmp-field-desc"><?php esc_html_e('Track dark mode usage statistics.', 'dark-mode-pro'); ?></p>
                    </div>

                    <div class="dmp-analytics-options">
                        <div class="dmp-field">
                            <label class="dmp-toggle-field">
                                <input type="checkbox" name="track_toggle_events" <?php checked($options['track_toggle_events']); ?>>
                                <span class="dmp-toggle-switch"></span>
                                <span class="dmp-field-label"><?php esc_html_e('Track Toggle Events', 'dark-mode-pro'); ?></span>
                            </label>
                        </div>

                        <div class="dmp-field">
                            <label class="dmp-toggle-field">
                                <input type="checkbox" name="track_time_spent" <?php checked($options['track_time_spent']); ?>>
                                <span class="dmp-toggle-switch"></span>
                                <span class="dmp-field-label"><?php esc_html_e('Track Time in Each Mode', 'dark-mode-pro'); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="dmp-card">
                    <h2><?php esc_html_e('Email Reports', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="email_reports_enabled" <?php checked($options['email_reports_enabled']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Enable Email Reports', 'dark-mode-pro'); ?></span>
                        </label>
                    </div>

                    <div class="dmp-email-options">
                        <div class="dmp-field">
                            <label for="email_report_frequency"><?php esc_html_e('Report Frequency', 'dark-mode-pro'); ?></label>
                            <select name="email_report_frequency" id="email_report_frequency">
                                <option value="daily" <?php selected($options['email_report_frequency'], 'daily'); ?>><?php esc_html_e('Daily', 'dark-mode-pro'); ?></option>
                                <option value="weekly" <?php selected($options['email_report_frequency'], 'weekly'); ?>><?php esc_html_e('Weekly', 'dark-mode-pro'); ?></option>
                                <option value="monthly" <?php selected($options['email_report_frequency'], 'monthly'); ?>><?php esc_html_e('Monthly', 'dark-mode-pro'); ?></option>
                            </select>
                        </div>

                        <div class="dmp-field">
                            <label for="email_report_recipients"><?php esc_html_e('Recipients', 'dark-mode-pro'); ?></label>
                            <textarea name="email_report_recipients" id="email_report_recipients" rows="3" placeholder="<?php esc_attr_e('One email per line', 'dark-mode-pro'); ?>"><?php echo esc_textarea($options['email_report_recipients']); ?></textarea>
                            <p class="dmp-field-desc"><?php esc_html_e('Leave empty to send to admin email.', 'dark-mode-pro'); ?></p>
                        </div>

                        <button type="button" class="button dmp-test-email-btn">
                            <?php esc_html_e('Send Test Email', 'dark-mode-pro'); ?>
                        </button>
                    </div>
                </div>

                <p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=dark-mode-pro-analytics')); ?>" class="button button-primary">
                        <?php esc_html_e('View Analytics Dashboard', 'dark-mode-pro'); ?>
                    </a>
                </p>
            </div>

            <!-- Advanced Tab -->
            <div class="dmp-tab-content" data-tab="advanced">
                <div class="dmp-card">
                    <h2><?php esc_html_e('Transition Settings', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label for="transition_duration"><?php esc_html_e('Transition Duration', 'dark-mode-pro'); ?></label>
                        <div class="dmp-range-slider">
                            <input type="range" name="transition_duration" id="transition_duration"
                                   min="0" max="1000" step="50" value="<?php echo esc_attr($options['transition_duration']); ?>">
                            <span class="dmp-range-value"><?php echo intval($options['transition_duration']); ?>ms</span>
                        </div>
                        <p class="dmp-field-desc"><?php esc_html_e('Duration of the mode transition animation.', 'dark-mode-pro'); ?></p>
                    </div>
                </div>

                <div class="dmp-card">
                    <h2><?php esc_html_e('Keyboard Shortcut', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label class="dmp-toggle-field">
                            <input type="checkbox" name="enable_keyboard_shortcut" <?php checked($options['enable_keyboard_shortcut']); ?>>
                            <span class="dmp-toggle-switch"></span>
                            <span class="dmp-field-label"><?php esc_html_e('Enable Keyboard Shortcut', 'dark-mode-pro'); ?></span>
                        </label>
                    </div>

                    <div class="dmp-field">
                        <label for="keyboard_shortcut"><?php esc_html_e('Shortcut Key', 'dark-mode-pro'); ?></label>
                        <div class="dmp-keyboard-input">
                            <span class="dmp-key">Ctrl/Cmd</span>
                            <span class="dmp-key-plus">+</span>
                            <span class="dmp-key">Shift</span>
                            <span class="dmp-key-plus">+</span>
                            <input type="text" name="keyboard_shortcut" id="keyboard_shortcut" value="<?php echo esc_attr($options['keyboard_shortcut']); ?>" maxlength="1" style="width: 40px; text-align: center;">
                        </div>
                    </div>
                </div>

                <div class="dmp-card">
                    <h2><?php esc_html_e('Exclude Elements', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label for="exclude_elements"><?php esc_html_e('CSS Selectors', 'dark-mode-pro'); ?></label>
                        <textarea name="exclude_elements" id="exclude_elements" rows="4" placeholder=".my-element, #my-id, [data-no-dark]"><?php echo esc_textarea($options['exclude_elements']); ?></textarea>
                        <p class="dmp-field-desc"><?php esc_html_e('Elements that should not be affected by dark mode (comma-separated CSS selectors).', 'dark-mode-pro'); ?></p>
                    </div>
                </div>

                <div class="dmp-card">
                    <h2><?php esc_html_e('Custom CSS', 'dark-mode-pro'); ?></h2>

                    <div class="dmp-field">
                        <label for="custom_css_light"><?php esc_html_e('Light Mode CSS', 'dark-mode-pro'); ?></label>
                        <textarea name="custom_css_light" id="custom_css_light" rows="6" class="code"><?php echo esc_textarea($options['custom_css_light']); ?></textarea>
                        <p class="dmp-field-desc"><?php esc_html_e('Additional CSS for light mode.', 'dark-mode-pro'); ?></p>
                    </div>

                    <div class="dmp-field">
                        <label for="custom_css_dark"><?php esc_html_e('Dark Mode CSS', 'dark-mode-pro'); ?></label>
                        <textarea name="custom_css_dark" id="custom_css_dark" rows="6" class="code"><?php echo esc_textarea($options['custom_css_dark']); ?></textarea>
                        <p class="dmp-field-desc"><?php esc_html_e('Additional CSS for dark mode.', 'dark-mode-pro'); ?></p>
                    </div>
                </div>

                <div class="dmp-card">
                    <h2><?php esc_html_e('Shortcode', 'dark-mode-pro'); ?></h2>
                    <p><?php esc_html_e('Use this shortcode to place a toggle switch anywhere:', 'dark-mode-pro'); ?></p>
                    <code>[dark_mode_toggle style="sun_moon" size="medium"]</code>
                </div>
            </div>
        </form>
    </div>
</div>
