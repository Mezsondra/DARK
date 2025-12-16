<?php
/**
 * Color Presets Class
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DMP_Color_Presets Class
 */
class DMP_Color_Presets {

    /**
     * Get all color presets
     */
    public static function get_presets() {
        return array(
            // Classic Dark Themes
            'midnight' => array(
                'name' => __('Midnight', 'dark-mode-pro'),
                'category' => 'classic',
                'colors' => array(
                    'background' => '#1a1a2e',
                    'surface' => '#16213e',
                    'primary' => '#0f3460',
                    'text' => '#eaeaea',
                    'text_secondary' => '#a0a0a0',
                    'accent' => '#e94560',
                    'link' => '#4da8da',
                    'border' => '#2a2a4a',
                ),
            ),
            'charcoal' => array(
                'name' => __('Charcoal', 'dark-mode-pro'),
                'category' => 'classic',
                'colors' => array(
                    'background' => '#2d2d2d',
                    'surface' => '#363636',
                    'primary' => '#404040',
                    'text' => '#f5f5f5',
                    'text_secondary' => '#b0b0b0',
                    'accent' => '#ff6b6b',
                    'link' => '#74b9ff',
                    'border' => '#4a4a4a',
                ),
            ),
            'obsidian' => array(
                'name' => __('Obsidian', 'dark-mode-pro'),
                'category' => 'classic',
                'colors' => array(
                    'background' => '#0d0d0d',
                    'surface' => '#171717',
                    'primary' => '#212121',
                    'text' => '#ffffff',
                    'text_secondary' => '#888888',
                    'accent' => '#00d9ff',
                    'link' => '#00b4d8',
                    'border' => '#2a2a2a',
                ),
            ),
            'slate' => array(
                'name' => __('Slate', 'dark-mode-pro'),
                'category' => 'classic',
                'colors' => array(
                    'background' => '#1e293b',
                    'surface' => '#334155',
                    'primary' => '#475569',
                    'text' => '#f1f5f9',
                    'text_secondary' => '#94a3b8',
                    'accent' => '#38bdf8',
                    'link' => '#60a5fa',
                    'border' => '#475569',
                ),
            ),
            'graphite' => array(
                'name' => __('Graphite', 'dark-mode-pro'),
                'category' => 'classic',
                'colors' => array(
                    'background' => '#1c1c1e',
                    'surface' => '#2c2c2e',
                    'primary' => '#3a3a3c',
                    'text' => '#f2f2f7',
                    'text_secondary' => '#8e8e93',
                    'accent' => '#ff9f0a',
                    'link' => '#64d2ff',
                    'border' => '#48484a',
                ),
            ),

            // Modern Themes
            'github_dark' => array(
                'name' => __('GitHub Dark', 'dark-mode-pro'),
                'category' => 'modern',
                'colors' => array(
                    'background' => '#0d1117',
                    'surface' => '#161b22',
                    'primary' => '#21262d',
                    'text' => '#c9d1d9',
                    'text_secondary' => '#8b949e',
                    'accent' => '#58a6ff',
                    'link' => '#58a6ff',
                    'border' => '#30363d',
                ),
            ),
            'discord' => array(
                'name' => __('Discord', 'dark-mode-pro'),
                'category' => 'modern',
                'colors' => array(
                    'background' => '#36393f',
                    'surface' => '#2f3136',
                    'primary' => '#202225',
                    'text' => '#dcddde',
                    'text_secondary' => '#72767d',
                    'accent' => '#5865f2',
                    'link' => '#00aff4',
                    'border' => '#42454a',
                ),
            ),
            'twitter_dim' => array(
                'name' => __('Twitter Dim', 'dark-mode-pro'),
                'category' => 'modern',
                'colors' => array(
                    'background' => '#15202b',
                    'surface' => '#192734',
                    'primary' => '#22303c',
                    'text' => '#ffffff',
                    'text_secondary' => '#8899a6',
                    'accent' => '#1da1f2',
                    'link' => '#1da1f2',
                    'border' => '#38444d',
                ),
            ),
            'twitter_lights_out' => array(
                'name' => __('Twitter Lights Out', 'dark-mode-pro'),
                'category' => 'modern',
                'colors' => array(
                    'background' => '#000000',
                    'surface' => '#15181c',
                    'primary' => '#1d1f23',
                    'text' => '#d9d9d9',
                    'text_secondary' => '#6e767d',
                    'accent' => '#1da1f2',
                    'link' => '#1da1f2',
                    'border' => '#2f3336',
                ),
            ),
            'spotify' => array(
                'name' => __('Spotify', 'dark-mode-pro'),
                'category' => 'modern',
                'colors' => array(
                    'background' => '#121212',
                    'surface' => '#181818',
                    'primary' => '#282828',
                    'text' => '#ffffff',
                    'text_secondary' => '#b3b3b3',
                    'accent' => '#1db954',
                    'link' => '#1db954',
                    'border' => '#333333',
                ),
            ),
            'youtube' => array(
                'name' => __('YouTube', 'dark-mode-pro'),
                'category' => 'modern',
                'colors' => array(
                    'background' => '#0f0f0f',
                    'surface' => '#212121',
                    'primary' => '#303030',
                    'text' => '#f1f1f1',
                    'text_secondary' => '#aaaaaa',
                    'accent' => '#ff0000',
                    'link' => '#3ea6ff',
                    'border' => '#3f3f3f',
                ),
            ),

            // AMOLED Themes (True Black)
            'amoled_pure' => array(
                'name' => __('AMOLED Pure', 'dark-mode-pro'),
                'category' => 'amoled',
                'colors' => array(
                    'background' => '#000000',
                    'surface' => '#0a0a0a',
                    'primary' => '#141414',
                    'text' => '#ffffff',
                    'text_secondary' => '#808080',
                    'accent' => '#00ff88',
                    'link' => '#00d4ff',
                    'border' => '#1a1a1a',
                ),
            ),
            'amoled_blue' => array(
                'name' => __('AMOLED Blue', 'dark-mode-pro'),
                'category' => 'amoled',
                'colors' => array(
                    'background' => '#000000',
                    'surface' => '#001122',
                    'primary' => '#002244',
                    'text' => '#e0f0ff',
                    'text_secondary' => '#6699cc',
                    'accent' => '#0088ff',
                    'link' => '#44aaff',
                    'border' => '#003366',
                ),
            ),
            'amoled_purple' => array(
                'name' => __('AMOLED Purple', 'dark-mode-pro'),
                'category' => 'amoled',
                'colors' => array(
                    'background' => '#000000',
                    'surface' => '#0d0015',
                    'primary' => '#1a002b',
                    'text' => '#f0e0ff',
                    'text_secondary' => '#9966cc',
                    'accent' => '#bb66ff',
                    'link' => '#aa88ff',
                    'border' => '#2a0044',
                ),
            ),

            // Warm Themes
            'mocha' => array(
                'name' => __('Mocha', 'dark-mode-pro'),
                'category' => 'warm',
                'colors' => array(
                    'background' => '#1e1a17',
                    'surface' => '#2a2420',
                    'primary' => '#3d342d',
                    'text' => '#f5ebe0',
                    'text_secondary' => '#a89984',
                    'accent' => '#d79921',
                    'link' => '#83a598',
                    'border' => '#504538',
                ),
            ),
            'dracula' => array(
                'name' => __('Dracula', 'dark-mode-pro'),
                'category' => 'warm',
                'colors' => array(
                    'background' => '#282a36',
                    'surface' => '#343746',
                    'primary' => '#44475a',
                    'text' => '#f8f8f2',
                    'text_secondary' => '#6272a4',
                    'accent' => '#ff79c6',
                    'link' => '#8be9fd',
                    'border' => '#44475a',
                ),
            ),
            'gruvbox' => array(
                'name' => __('Gruvbox', 'dark-mode-pro'),
                'category' => 'warm',
                'colors' => array(
                    'background' => '#282828',
                    'surface' => '#3c3836',
                    'primary' => '#504945',
                    'text' => '#ebdbb2',
                    'text_secondary' => '#a89984',
                    'accent' => '#fe8019',
                    'link' => '#83a598',
                    'border' => '#665c54',
                ),
            ),
            'nord' => array(
                'name' => __('Nord', 'dark-mode-pro'),
                'category' => 'warm',
                'colors' => array(
                    'background' => '#2e3440',
                    'surface' => '#3b4252',
                    'primary' => '#434c5e',
                    'text' => '#eceff4',
                    'text_secondary' => '#d8dee9',
                    'accent' => '#88c0d0',
                    'link' => '#81a1c1',
                    'border' => '#4c566a',
                ),
            ),
            'solarized_dark' => array(
                'name' => __('Solarized Dark', 'dark-mode-pro'),
                'category' => 'warm',
                'colors' => array(
                    'background' => '#002b36',
                    'surface' => '#073642',
                    'primary' => '#094552',
                    'text' => '#839496',
                    'text_secondary' => '#657b83',
                    'accent' => '#b58900',
                    'link' => '#268bd2',
                    'border' => '#094552',
                ),
            ),

            // Cool Themes
            'ocean' => array(
                'name' => __('Ocean', 'dark-mode-pro'),
                'category' => 'cool',
                'colors' => array(
                    'background' => '#0a1929',
                    'surface' => '#0d2137',
                    'primary' => '#132f4c',
                    'text' => '#b2bac2',
                    'text_secondary' => '#5b6b7c',
                    'accent' => '#66b2ff',
                    'link' => '#3399ff',
                    'border' => '#1e4976',
                ),
            ),
            'arctic' => array(
                'name' => __('Arctic', 'dark-mode-pro'),
                'category' => 'cool',
                'colors' => array(
                    'background' => '#1a2332',
                    'surface' => '#232d3f',
                    'primary' => '#2d3a4f',
                    'text' => '#e8eaed',
                    'text_secondary' => '#9ba3af',
                    'accent' => '#6ee7b7',
                    'link' => '#7dd3fc',
                    'border' => '#3d4a5c',
                ),
            ),
            'cyberpunk' => array(
                'name' => __('Cyberpunk', 'dark-mode-pro'),
                'category' => 'cool',
                'colors' => array(
                    'background' => '#0a0a0f',
                    'surface' => '#12121a',
                    'primary' => '#1a1a2e',
                    'text' => '#00ff9f',
                    'text_secondary' => '#008855',
                    'accent' => '#ff00ff',
                    'link' => '#00ffff',
                    'border' => '#2a2a4a',
                ),
            ),
            'synthwave' => array(
                'name' => __('Synthwave', 'dark-mode-pro'),
                'category' => 'cool',
                'colors' => array(
                    'background' => '#241734',
                    'surface' => '#2d1f42',
                    'primary' => '#382952',
                    'text' => '#f4eeff',
                    'text_secondary' => '#b88cce',
                    'accent' => '#ff6ad5',
                    'link' => '#c774e8',
                    'border' => '#463463',
                ),
            ),

            // Professional Themes
            'material_dark' => array(
                'name' => __('Material Dark', 'dark-mode-pro'),
                'category' => 'professional',
                'colors' => array(
                    'background' => '#121212',
                    'surface' => '#1e1e1e',
                    'primary' => '#2d2d2d',
                    'text' => '#e0e0e0',
                    'text_secondary' => '#9e9e9e',
                    'accent' => '#bb86fc',
                    'link' => '#03dac6',
                    'border' => '#333333',
                ),
            ),
            'vscode' => array(
                'name' => __('VS Code', 'dark-mode-pro'),
                'category' => 'professional',
                'colors' => array(
                    'background' => '#1e1e1e',
                    'surface' => '#252526',
                    'primary' => '#333333',
                    'text' => '#d4d4d4',
                    'text_secondary' => '#808080',
                    'accent' => '#0078d4',
                    'link' => '#4fc1ff',
                    'border' => '#3c3c3c',
                ),
            ),
            'atom_one_dark' => array(
                'name' => __('Atom One Dark', 'dark-mode-pro'),
                'category' => 'professional',
                'colors' => array(
                    'background' => '#282c34',
                    'surface' => '#21252b',
                    'primary' => '#2c323c',
                    'text' => '#abb2bf',
                    'text_secondary' => '#5c6370',
                    'accent' => '#61afef',
                    'link' => '#56b6c2',
                    'border' => '#3e4451',
                ),
            ),
            'one_monokai' => array(
                'name' => __('One Monokai', 'dark-mode-pro'),
                'category' => 'professional',
                'colors' => array(
                    'background' => '#272822',
                    'surface' => '#2d2e27',
                    'primary' => '#3e3d32',
                    'text' => '#f8f8f2',
                    'text_secondary' => '#75715e',
                    'accent' => '#f92672',
                    'link' => '#66d9ef',
                    'border' => '#49483e',
                ),
            ),

            // Accessibility Themes
            'high_contrast' => array(
                'name' => __('High Contrast', 'dark-mode-pro'),
                'category' => 'accessibility',
                'colors' => array(
                    'background' => '#000000',
                    'surface' => '#000000',
                    'primary' => '#1a1a1a',
                    'text' => '#ffffff',
                    'text_secondary' => '#ffff00',
                    'accent' => '#00ff00',
                    'link' => '#00ffff',
                    'border' => '#ffffff',
                ),
            ),
            'reduced_blue' => array(
                'name' => __('Reduced Blue Light', 'dark-mode-pro'),
                'category' => 'accessibility',
                'colors' => array(
                    'background' => '#1f1d1a',
                    'surface' => '#2a2723',
                    'primary' => '#36322c',
                    'text' => '#ffeedd',
                    'text_secondary' => '#ccbb99',
                    'accent' => '#ffaa44',
                    'link' => '#ffcc77',
                    'border' => '#443d33',
                ),
            ),

            // Seasonal Themes
            'autumn' => array(
                'name' => __('Autumn', 'dark-mode-pro'),
                'category' => 'seasonal',
                'colors' => array(
                    'background' => '#1a1512',
                    'surface' => '#261e18',
                    'primary' => '#33271f',
                    'text' => '#f5e6d3',
                    'text_secondary' => '#b8a082',
                    'accent' => '#e07b39',
                    'link' => '#d4a03c',
                    'border' => '#4a3728',
                ),
            ),
            'winter' => array(
                'name' => __('Winter', 'dark-mode-pro'),
                'category' => 'seasonal',
                'colors' => array(
                    'background' => '#141821',
                    'surface' => '#1c2230',
                    'primary' => '#252d40',
                    'text' => '#e8f0f8',
                    'text_secondary' => '#8fa4bc',
                    'accent' => '#88ccff',
                    'link' => '#99ddff',
                    'border' => '#2d3a52',
                ),
            ),
            'forest' => array(
                'name' => __('Forest', 'dark-mode-pro'),
                'category' => 'seasonal',
                'colors' => array(
                    'background' => '#0f1a14',
                    'surface' => '#15241c',
                    'primary' => '#1c3026',
                    'text' => '#d8efe0',
                    'text_secondary' => '#7fb88f',
                    'accent' => '#4ade80',
                    'link' => '#86efac',
                    'border' => '#264032',
                ),
            ),

            // Custom (placeholder for user customization)
            'custom' => array(
                'name' => __('Custom', 'dark-mode-pro'),
                'category' => 'custom',
                'colors' => array(
                    'background' => '#1a1a2e',
                    'surface' => '#16213e',
                    'primary' => '#0f3460',
                    'text' => '#e4e4e4',
                    'text_secondary' => '#a0a0a0',
                    'accent' => '#e94560',
                    'link' => '#4da8da',
                    'border' => '#2a2a4a',
                ),
            ),
        );
    }

    /**
     * Get preset categories
     */
    public static function get_categories() {
        return array(
            'classic' => __('Classic Dark', 'dark-mode-pro'),
            'modern' => __('Modern Apps', 'dark-mode-pro'),
            'amoled' => __('AMOLED (True Black)', 'dark-mode-pro'),
            'warm' => __('Warm & Cozy', 'dark-mode-pro'),
            'cool' => __('Cool & Futuristic', 'dark-mode-pro'),
            'professional' => __('Professional', 'dark-mode-pro'),
            'accessibility' => __('Accessibility', 'dark-mode-pro'),
            'seasonal' => __('Seasonal', 'dark-mode-pro'),
            'custom' => __('Custom', 'dark-mode-pro'),
        );
    }

    /**
     * Get presets by category
     */
    public static function get_presets_by_category($category) {
        $presets = self::get_presets();
        $filtered = array();

        foreach ($presets as $key => $preset) {
            if ($preset['category'] === $category) {
                $filtered[$key] = $preset;
            }
        }

        return $filtered;
    }

    /**
     * Validate color value
     */
    public static function validate_color($color) {
        // Check if it's a valid hex color
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            return $color;
        }
        return false;
    }

    /**
     * Generate CSS variables from preset
     */
    public static function generate_css_variables($preset_key) {
        $presets = self::get_presets();

        if (!isset($presets[$preset_key])) {
            $preset_key = 'midnight';
        }

        $colors = $presets[$preset_key]['colors'];
        $css = '';

        foreach ($colors as $name => $value) {
            $css_name = str_replace('_', '-', $name);
            $css .= "--dmp-{$css_name}: {$value};\n";
        }

        return $css;
    }
}
