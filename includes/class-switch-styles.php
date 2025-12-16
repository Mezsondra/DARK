<?php
/**
 * Switch Styles Class
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DMP_Switch_Styles Class
 */
class DMP_Switch_Styles {

    /**
     * Get all switch styles
     */
    public static function get_styles() {
        return array(
            // Classic Toggles
            'classic' => array(
                'name' => __('Classic Toggle', 'dark-mode-pro'),
                'category' => 'toggle',
                'preview' => 'toggle-classic.svg',
            ),
            'ios' => array(
                'name' => __('iOS Style', 'dark-mode-pro'),
                'category' => 'toggle',
                'preview' => 'toggle-ios.svg',
            ),
            'android' => array(
                'name' => __('Android Material', 'dark-mode-pro'),
                'category' => 'toggle',
                'preview' => 'toggle-android.svg',
            ),
            'flat' => array(
                'name' => __('Flat Toggle', 'dark-mode-pro'),
                'category' => 'toggle',
                'preview' => 'toggle-flat.svg',
            ),
            '3d' => array(
                'name' => __('3D Toggle', 'dark-mode-pro'),
                'category' => 'toggle',
                'preview' => 'toggle-3d.svg',
            ),
            'skeuomorphic' => array(
                'name' => __('Skeuomorphic', 'dark-mode-pro'),
                'category' => 'toggle',
                'preview' => 'toggle-skeuomorphic.svg',
            ),

            // Sun/Moon Themed
            'sun_moon' => array(
                'name' => __('Sun & Moon', 'dark-mode-pro'),
                'category' => 'themed',
                'preview' => 'toggle-sun-moon.svg',
            ),
            'sun_moon_animated' => array(
                'name' => __('Sun & Moon Animated', 'dark-mode-pro'),
                'category' => 'themed',
                'preview' => 'toggle-sun-moon-animated.svg',
            ),
            'eclipse' => array(
                'name' => __('Eclipse', 'dark-mode-pro'),
                'category' => 'themed',
                'preview' => 'toggle-eclipse.svg',
            ),
            'day_night' => array(
                'name' => __('Day & Night', 'dark-mode-pro'),
                'category' => 'themed',
                'preview' => 'toggle-day-night.svg',
            ),
            'sunrise_sunset' => array(
                'name' => __('Sunrise/Sunset', 'dark-mode-pro'),
                'category' => 'themed',
                'preview' => 'toggle-sunrise-sunset.svg',
            ),

            // Icon Buttons
            'icon_circle' => array(
                'name' => __('Circle Icon', 'dark-mode-pro'),
                'category' => 'button',
                'preview' => 'button-circle.svg',
            ),
            'icon_square' => array(
                'name' => __('Square Icon', 'dark-mode-pro'),
                'category' => 'button',
                'preview' => 'button-square.svg',
            ),
            'icon_rounded' => array(
                'name' => __('Rounded Icon', 'dark-mode-pro'),
                'category' => 'button',
                'preview' => 'button-rounded.svg',
            ),
            'icon_pill' => array(
                'name' => __('Pill Button', 'dark-mode-pro'),
                'category' => 'button',
                'preview' => 'button-pill.svg',
            ),
            'icon_fab' => array(
                'name' => __('FAB Style', 'dark-mode-pro'),
                'category' => 'button',
                'preview' => 'button-fab.svg',
            ),
            'icon_minimal' => array(
                'name' => __('Minimal Icon', 'dark-mode-pro'),
                'category' => 'button',
                'preview' => 'button-minimal.svg',
            ),

            // Text Buttons
            'text_label' => array(
                'name' => __('Text Label', 'dark-mode-pro'),
                'category' => 'text',
                'preview' => 'text-label.svg',
            ),
            'text_icon_label' => array(
                'name' => __('Icon + Label', 'dark-mode-pro'),
                'category' => 'text',
                'preview' => 'text-icon-label.svg',
            ),
            'text_toggle_label' => array(
                'name' => __('Toggle + Label', 'dark-mode-pro'),
                'category' => 'text',
                'preview' => 'text-toggle-label.svg',
            ),

            // Animated Styles
            'animated_morph' => array(
                'name' => __('Morphing', 'dark-mode-pro'),
                'category' => 'animated',
                'preview' => 'animated-morph.svg',
            ),
            'animated_flip' => array(
                'name' => __('Flip Card', 'dark-mode-pro'),
                'category' => 'animated',
                'preview' => 'animated-flip.svg',
            ),
            'animated_slide' => array(
                'name' => __('Slide Effect', 'dark-mode-pro'),
                'category' => 'animated',
                'preview' => 'animated-slide.svg',
            ),
            'animated_glow' => array(
                'name' => __('Glow Effect', 'dark-mode-pro'),
                'category' => 'animated',
                'preview' => 'animated-glow.svg',
            ),
            'animated_pulse' => array(
                'name' => __('Pulse Effect', 'dark-mode-pro'),
                'category' => 'animated',
                'preview' => 'animated-pulse.svg',
            ),
            'animated_bounce' => array(
                'name' => __('Bounce', 'dark-mode-pro'),
                'category' => 'animated',
                'preview' => 'animated-bounce.svg',
            ),
        );
    }

    /**
     * Get switch categories
     */
    public static function get_categories() {
        return array(
            'toggle' => __('Toggle Switches', 'dark-mode-pro'),
            'themed' => __('Sun & Moon Themed', 'dark-mode-pro'),
            'button' => __('Icon Buttons', 'dark-mode-pro'),
            'text' => __('Text Buttons', 'dark-mode-pro'),
            'animated' => __('Animated', 'dark-mode-pro'),
        );
    }

    /**
     * Get styles by category
     */
    public static function get_styles_by_category($category) {
        $styles = self::get_styles();
        $filtered = array();

        foreach ($styles as $key => $style) {
            if ($style['category'] === $category) {
                $filtered[$key] = $style;
            }
        }

        return $filtered;
    }

    /**
     * Get switch positions
     */
    public static function get_positions() {
        return array(
            'bottom-right' => __('Bottom Right', 'dark-mode-pro'),
            'bottom-left' => __('Bottom Left', 'dark-mode-pro'),
            'top-right' => __('Top Right', 'dark-mode-pro'),
            'top-left' => __('Top Left', 'dark-mode-pro'),
            'middle-right' => __('Middle Right', 'dark-mode-pro'),
            'middle-left' => __('Middle Left', 'dark-mode-pro'),
        );
    }

    /**
     * Get switch sizes
     */
    public static function get_sizes() {
        return array(
            'small' => __('Small', 'dark-mode-pro'),
            'medium' => __('Medium', 'dark-mode-pro'),
            'large' => __('Large', 'dark-mode-pro'),
            'xlarge' => __('Extra Large', 'dark-mode-pro'),
        );
    }

    /**
     * Render switch HTML
     */
    public static function render_switch($style, $classes = array()) {
        $styles = self::get_styles();

        if (!isset($styles[$style])) {
            $style = 'classic';
        }

        $class_string = implode(' ', array_merge(array('dmp-switch', 'dmp-switch-' . $style), $classes));

        ob_start();

        switch ($style) {
            case 'sun_moon':
            case 'sun_moon_animated':
                self::render_sun_moon_switch($style, $class_string);
                break;

            case 'eclipse':
                self::render_eclipse_switch($class_string);
                break;

            case 'day_night':
                self::render_day_night_switch($class_string);
                break;

            case 'icon_circle':
            case 'icon_square':
            case 'icon_rounded':
            case 'icon_pill':
            case 'icon_fab':
            case 'icon_minimal':
                self::render_icon_button($style, $class_string);
                break;

            case 'text_label':
            case 'text_icon_label':
            case 'text_toggle_label':
                self::render_text_button($style, $class_string);
                break;

            case 'animated_morph':
            case 'animated_flip':
            case 'animated_slide':
            case 'animated_glow':
            case 'animated_pulse':
            case 'animated_bounce':
                self::render_animated_switch($style, $class_string);
                break;

            default:
                self::render_toggle_switch($style, $class_string);
                break;
        }

        return ob_get_clean();
    }

    /**
     * Render toggle switch
     */
    private static function render_toggle_switch($style, $class_string) {
        ?>
        <label class="<?php echo esc_attr($class_string); ?>" role="switch" aria-checked="false" tabindex="0">
            <input type="checkbox" class="dmp-toggle-input" aria-hidden="true">
            <span class="dmp-toggle-track">
                <span class="dmp-toggle-thumb"></span>
            </span>
            <span class="dmp-sr-only"><?php esc_html_e('Toggle dark mode', 'dark-mode-pro'); ?></span>
        </label>
        <?php
    }

    /**
     * Render sun/moon switch
     */
    private static function render_sun_moon_switch($style, $class_string) {
        ?>
        <label class="<?php echo esc_attr($class_string); ?>" role="switch" aria-checked="false" tabindex="0">
            <input type="checkbox" class="dmp-toggle-input" aria-hidden="true">
            <span class="dmp-toggle-track">
                <span class="dmp-toggle-thumb">
                    <svg class="dmp-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    <svg class="dmp-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </span>
            </span>
            <span class="dmp-sr-only"><?php esc_html_e('Toggle dark mode', 'dark-mode-pro'); ?></span>
        </label>
        <?php
    }

    /**
     * Render eclipse switch
     */
    private static function render_eclipse_switch($class_string) {
        ?>
        <label class="<?php echo esc_attr($class_string); ?>" role="switch" aria-checked="false" tabindex="0">
            <input type="checkbox" class="dmp-toggle-input" aria-hidden="true">
            <span class="dmp-eclipse-container">
                <span class="dmp-eclipse-sun"></span>
                <span class="dmp-eclipse-moon"></span>
            </span>
            <span class="dmp-sr-only"><?php esc_html_e('Toggle dark mode', 'dark-mode-pro'); ?></span>
        </label>
        <?php
    }

    /**
     * Render day/night switch
     */
    private static function render_day_night_switch($class_string) {
        ?>
        <label class="<?php echo esc_attr($class_string); ?>" role="switch" aria-checked="false" tabindex="0">
            <input type="checkbox" class="dmp-toggle-input" aria-hidden="true">
            <span class="dmp-day-night-container">
                <span class="dmp-sky">
                    <span class="dmp-clouds"></span>
                    <span class="dmp-stars"></span>
                </span>
                <span class="dmp-celestial">
                    <span class="dmp-sun"></span>
                    <span class="dmp-moon"></span>
                </span>
            </span>
            <span class="dmp-sr-only"><?php esc_html_e('Toggle dark mode', 'dark-mode-pro'); ?></span>
        </label>
        <?php
    }

    /**
     * Render icon button
     */
    private static function render_icon_button($style, $class_string) {
        ?>
        <button class="<?php echo esc_attr($class_string); ?>" type="button" aria-pressed="false">
            <svg class="dmp-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/>
                <line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/>
                <line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
            <svg class="dmp-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
            <span class="dmp-sr-only"><?php esc_html_e('Toggle dark mode', 'dark-mode-pro'); ?></span>
        </button>
        <?php
    }

    /**
     * Render text button
     */
    private static function render_text_button($style, $class_string) {
        ?>
        <button class="<?php echo esc_attr($class_string); ?>" type="button" aria-pressed="false">
            <?php if ($style === 'text_icon_label' || $style === 'text_toggle_label'): ?>
                <span class="dmp-button-icon">
                    <svg class="dmp-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    <svg class="dmp-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </span>
            <?php endif; ?>
            <span class="dmp-button-label">
                <span class="dmp-label-light"><?php esc_html_e('Light', 'dark-mode-pro'); ?></span>
                <span class="dmp-label-dark"><?php esc_html_e('Dark', 'dark-mode-pro'); ?></span>
            </span>
        </button>
        <?php
    }

    /**
     * Render animated switch
     */
    private static function render_animated_switch($style, $class_string) {
        ?>
        <label class="<?php echo esc_attr($class_string); ?>" role="switch" aria-checked="false" tabindex="0">
            <input type="checkbox" class="dmp-toggle-input" aria-hidden="true">
            <span class="dmp-animated-container">
                <span class="dmp-animated-track">
                    <span class="dmp-animated-thumb">
                        <svg class="dmp-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="5"/>
                            <line x1="12" y1="1" x2="12" y2="3"/>
                            <line x1="12" y1="21" x2="12" y2="23"/>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                            <line x1="1" y1="12" x2="3" y2="12"/>
                            <line x1="21" y1="12" x2="23" y2="12"/>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                        </svg>
                        <svg class="dmp-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </span>
                </span>
            </span>
            <span class="dmp-sr-only"><?php esc_html_e('Toggle dark mode', 'dark-mode-pro'); ?></span>
        </label>
        <?php
    }
}
