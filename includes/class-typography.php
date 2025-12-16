<?php
/**
 * Typography Class
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DMP_Typography Class
 */
class DMP_Typography {

    /**
     * Get font size presets
     */
    public static function get_font_size_presets() {
        return array(
            '-2' => __('-2 (Smaller)', 'dark-mode-pro'),
            '-1' => __('-1 (Slightly Smaller)', 'dark-mode-pro'),
            '0' => __('0 (No Change)', 'dark-mode-pro'),
            '1' => __('+1 (Slightly Larger)', 'dark-mode-pro'),
            '2' => __('+2 (Larger)', 'dark-mode-pro'),
            '3' => __('+3 (Much Larger)', 'dark-mode-pro'),
        );
    }

    /**
     * Get line height presets
     */
    public static function get_line_height_presets() {
        return array(
            '-0.1' => __('-0.1 (Tighter)', 'dark-mode-pro'),
            '0' => __('0 (No Change)', 'dark-mode-pro'),
            '0.1' => __('+0.1 (Slightly Looser)', 'dark-mode-pro'),
            '0.2' => __('+0.2 (Looser)', 'dark-mode-pro'),
            '0.3' => __('+0.3 (Much Looser)', 'dark-mode-pro'),
        );
    }

    /**
     * Get letter spacing presets
     */
    public static function get_letter_spacing_presets() {
        return array(
            '-0.5' => __('-0.5px (Tighter)', 'dark-mode-pro'),
            '0' => __('0 (No Change)', 'dark-mode-pro'),
            '0.5' => __('+0.5px (Slightly Looser)', 'dark-mode-pro'),
            '1' => __('+1px (Looser)', 'dark-mode-pro'),
        );
    }

    /**
     * Generate typography CSS
     */
    public static function generate_css($options) {
        $css = '';

        $font_size_adjust = isset($options['dark_font_size_adjust']) ? intval($options['dark_font_size_adjust']) : 0;
        $line_height_adjust = isset($options['dark_line_height_adjust']) ? floatval($options['dark_line_height_adjust']) : 0;
        $letter_spacing = isset($options['dark_letter_spacing']) ? floatval($options['dark_letter_spacing']) : 0;
        $font_smoothing = isset($options['font_smoothing']) ? $options['font_smoothing'] : true;

        if ($font_size_adjust !== 0) {
            $css .= self::generate_font_size_css($font_size_adjust);
        }

        if ($line_height_adjust !== 0) {
            $css .= self::generate_line_height_css($line_height_adjust);
        }

        if ($letter_spacing !== 0) {
            $css .= self::generate_letter_spacing_css($letter_spacing);
        }

        if ($font_smoothing) {
            $css .= self::generate_font_smoothing_css();
        }

        return $css;
    }

    /**
     * Generate font size CSS
     */
    private static function generate_font_size_css($adjust) {
        $base_sizes = array(
            'html' => 16,
            'body' => 16,
            'p' => 16,
            'h1' => 32,
            'h2' => 28,
            'h3' => 24,
            'h4' => 20,
            'h5' => 18,
            'h6' => 16,
            'small' => 14,
            'code' => 14,
            'pre' => 14,
        );

        $css = "body.dmp-dark-mode {\n";
        $css .= "  --dmp-font-size-adjust: {$adjust}px;\n";
        $css .= "}\n\n";

        foreach ($base_sizes as $element => $size) {
            $new_size = $size + $adjust;
            $css .= "body.dmp-dark-mode {$element} {\n";
            $css .= "  font-size: {$new_size}px;\n";
            $css .= "}\n";
        }

        // Also adjust elements with specific classes
        $css .= "\nbody.dmp-dark-mode .entry-content,\n";
        $css .= "body.dmp-dark-mode .post-content,\n";
        $css .= "body.dmp-dark-mode article {\n";
        $css .= "  font-size: calc(1em + var(--dmp-font-size-adjust, 0px));\n";
        $css .= "}\n";

        return $css;
    }

    /**
     * Generate line height CSS
     */
    private static function generate_line_height_css($adjust) {
        $css = "body.dmp-dark-mode {\n";
        $css .= "  --dmp-line-height-adjust: {$adjust};\n";
        $css .= "}\n\n";

        $css .= "body.dmp-dark-mode p,\n";
        $css .= "body.dmp-dark-mode li,\n";
        $css .= "body.dmp-dark-mode td,\n";
        $css .= "body.dmp-dark-mode th,\n";
        $css .= "body.dmp-dark-mode .entry-content,\n";
        $css .= "body.dmp-dark-mode article {\n";
        $css .= "  line-height: calc(1.6 + var(--dmp-line-height-adjust, 0));\n";
        $css .= "}\n";

        return $css;
    }

    /**
     * Generate letter spacing CSS
     */
    private static function generate_letter_spacing_css($spacing) {
        $css = "body.dmp-dark-mode {\n";
        $css .= "  --dmp-letter-spacing: {$spacing}px;\n";
        $css .= "}\n\n";

        $css .= "body.dmp-dark-mode p,\n";
        $css .= "body.dmp-dark-mode li,\n";
        $css .= "body.dmp-dark-mode span,\n";
        $css .= "body.dmp-dark-mode .entry-content {\n";
        $css .= "  letter-spacing: var(--dmp-letter-spacing, 0);\n";
        $css .= "}\n";

        return $css;
    }

    /**
     * Generate font smoothing CSS
     */
    private static function generate_font_smoothing_css() {
        $css = "body.dmp-dark-mode {\n";
        $css .= "  -webkit-font-smoothing: antialiased;\n";
        $css .= "  -moz-osx-font-smoothing: grayscale;\n";
        $css .= "  text-rendering: optimizeLegibility;\n";
        $css .= "}\n\n";

        // Additional smoothing for headings
        $css .= "body.dmp-dark-mode h1,\n";
        $css .= "body.dmp-dark-mode h2,\n";
        $css .= "body.dmp-dark-mode h3,\n";
        $css .= "body.dmp-dark-mode h4,\n";
        $css .= "body.dmp-dark-mode h5,\n";
        $css .= "body.dmp-dark-mode h6 {\n";
        $css .= "  -webkit-font-smoothing: antialiased;\n";
        $css .= "  -moz-osx-font-smoothing: grayscale;\n";
        $css .= "}\n";

        return $css;
    }

    /**
     * Get typography JavaScript
     */
    public static function get_client_script($options) {
        $font_size_adjust = isset($options['dark_font_size_adjust']) ? intval($options['dark_font_size_adjust']) : 0;
        $line_height_adjust = isset($options['dark_line_height_adjust']) ? floatval($options['dark_line_height_adjust']) : 0;
        $letter_spacing = isset($options['dark_letter_spacing']) ? floatval($options['dark_letter_spacing']) : 0;

        ob_start();
        ?>
        (function() {
            const dmpTypography = {
                fontSizeAdjust: <?php echo $font_size_adjust; ?>,
                lineHeightAdjust: <?php echo $line_height_adjust; ?>,
                letterSpacing: <?php echo $letter_spacing; ?>,

                apply: function() {
                    document.documentElement.style.setProperty('--dmp-font-size-adjust', this.fontSizeAdjust + 'px');
                    document.documentElement.style.setProperty('--dmp-line-height-adjust', this.lineHeightAdjust);
                    document.documentElement.style.setProperty('--dmp-letter-spacing', this.letterSpacing + 'px');
                },

                reset: function() {
                    document.documentElement.style.removeProperty('--dmp-font-size-adjust');
                    document.documentElement.style.removeProperty('--dmp-line-height-adjust');
                    document.documentElement.style.removeProperty('--dmp-letter-spacing');
                }
            };

            window.dmpTypography = dmpTypography;
        })();
        <?php
        return ob_get_clean();
    }

    /**
     * Get font family suggestions for dark mode
     */
    public static function get_font_suggestions() {
        return array(
            'sans_serif' => array(
                'Inter' => 'Inter, system-ui, sans-serif',
                'Roboto' => 'Roboto, Arial, sans-serif',
                'Open Sans' => '"Open Sans", Arial, sans-serif',
                'Lato' => 'Lato, Arial, sans-serif',
                'Source Sans Pro' => '"Source Sans Pro", Arial, sans-serif',
            ),
            'monospace' => array(
                'JetBrains Mono' => '"JetBrains Mono", monospace',
                'Fira Code' => '"Fira Code", monospace',
                'Source Code Pro' => '"Source Code Pro", monospace',
                'Monaco' => 'Monaco, Consolas, monospace',
            ),
        );
    }
}
