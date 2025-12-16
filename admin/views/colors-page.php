<?php
/**
 * Color Presets Page View
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('dmp_options', dark_mode_pro()->get_default_options());
$categories = DMP_Color_Presets::get_categories();
$current_preset = $options['color_preset'];
$custom_colors = $options['custom_colors'];
?>

<div class="wrap dmp-admin-wrap">
    <div class="dmp-admin-header">
        <div class="dmp-header-content">
            <h1>
                <span class="dashicons dashicons-art"></span>
                <?php esc_html_e('Color Presets', 'dark-mode-pro'); ?>
            </h1>
            <p><?php esc_html_e('Choose from professionally designed color themes or create your own.', 'dark-mode-pro'); ?></p>
        </div>
        <div class="dmp-header-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=dark-mode-pro')); ?>" class="button">
                <span class="dashicons dashicons-arrow-left-alt"></span>
                <?php esc_html_e('Back to Settings', 'dark-mode-pro'); ?>
            </a>
        </div>
    </div>

    <div class="dmp-admin-content">
        <div class="dmp-colors-layout">
            <div class="dmp-colors-main">
                <?php foreach ($categories as $cat_key => $cat_name): ?>
                    <?php if ($cat_key === 'custom') continue; ?>
                    <div class="dmp-category-section">
                        <h2><?php echo esc_html($cat_name); ?></h2>
                        <div class="dmp-presets-row">
                            <?php foreach (DMP_Color_Presets::get_presets_by_category($cat_key) as $preset_key => $preset): ?>
                                <div class="dmp-preset-card <?php echo $current_preset === $preset_key ? 'active' : ''; ?>" data-preset="<?php echo esc_attr($preset_key); ?>">
                                    <div class="dmp-preset-preview" style="background: <?php echo esc_attr($preset['colors']['background']); ?>;">
                                        <div class="dmp-preview-surface" style="background: <?php echo esc_attr($preset['colors']['surface']); ?>;">
                                            <div class="dmp-preview-text" style="color: <?php echo esc_attr($preset['colors']['text']); ?>;">
                                                <?php echo esc_html($preset['name']); ?>
                                            </div>
                                            <div class="dmp-preview-accent" style="background: <?php echo esc_attr($preset['colors']['accent']); ?>;"></div>
                                        </div>
                                        <div class="dmp-preview-colors">
                                            <?php foreach ($preset['colors'] as $color): ?>
                                                <span class="dmp-color-dot" style="background: <?php echo esc_attr($color); ?>;"></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="dmp-preset-info">
                                        <h3><?php echo esc_html($preset['name']); ?></h3>
                                        <?php if ($current_preset === $preset_key): ?>
                                            <span class="dmp-active-badge"><?php esc_html_e('Active', 'dark-mode-pro'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="button dmp-select-preset-btn" data-preset="<?php echo esc_attr($preset_key); ?>">
                                        <?php echo $current_preset === $preset_key ? esc_html__('Selected', 'dark-mode-pro') : esc_html__('Select', 'dark-mode-pro'); ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="dmp-colors-sidebar">
                <div class="dmp-card dmp-custom-colors-card <?php echo $current_preset === 'custom' ? 'active' : ''; ?>">
                    <h2><?php esc_html_e('Custom Colors', 'dark-mode-pro'); ?></h2>
                    <p><?php esc_html_e('Create your own custom color scheme.', 'dark-mode-pro'); ?></p>

                    <div class="dmp-custom-preview" id="dmp-custom-preview">
                        <!-- Live preview -->
                    </div>

                    <div class="dmp-color-pickers">
                        <?php
                        $color_labels = array(
                            'background' => __('Background', 'dark-mode-pro'),
                            'surface' => __('Surface', 'dark-mode-pro'),
                            'primary' => __('Primary', 'dark-mode-pro'),
                            'text' => __('Text', 'dark-mode-pro'),
                            'text_secondary' => __('Secondary Text', 'dark-mode-pro'),
                            'accent' => __('Accent', 'dark-mode-pro'),
                            'link' => __('Links', 'dark-mode-pro'),
                            'border' => __('Borders', 'dark-mode-pro'),
                        );

                        foreach ($color_labels as $key => $label):
                        ?>
                            <div class="dmp-color-picker-field">
                                <label for="color_<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                                <input type="text"
                                       name="custom_colors[<?php echo esc_attr($key); ?>]"
                                       id="color_<?php echo esc_attr($key); ?>"
                                       value="<?php echo esc_attr($custom_colors[$key] ?? ''); ?>"
                                       class="dmp-color-picker"
                                       data-color-key="<?php echo esc_attr($key); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="dmp-custom-actions">
                        <button type="button" class="button button-primary dmp-save-custom-btn">
                            <?php esc_html_e('Save & Activate Custom Colors', 'dark-mode-pro'); ?>
                        </button>
                    </div>
                </div>

                <div class="dmp-card">
                    <h3><?php esc_html_e('Color Tips', 'dark-mode-pro'); ?></h3>
                    <ul class="dmp-tips-list">
                        <li><?php esc_html_e('Use sufficient contrast between text and background (WCAG recommends 4.5:1)', 'dark-mode-pro'); ?></li>
                        <li><?php esc_html_e('Avoid pure black (#000000) for backgrounds - use dark grays instead', 'dark-mode-pro'); ?></li>
                        <li><?php esc_html_e('Keep accent colors vibrant but not overly bright', 'dark-mode-pro'); ?></li>
                        <li><?php esc_html_e('Test your colors on different screens and devices', 'dark-mode-pro'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dmp-colors-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
    max-width: 1600px;
}

.dmp-presets-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 15px;
}

.dmp-preset-card {
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.dmp-preset-card:hover {
    border-color: #1a1a2e;
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.dmp-preset-card.active {
    border-color: #e94560;
}

.dmp-preset-preview {
    padding: 15px;
    min-height: 100px;
}

.dmp-preview-surface {
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 10px;
}

.dmp-preview-text {
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 8px;
}

.dmp-preview-accent {
    height: 4px;
    border-radius: 2px;
    width: 60%;
}

.dmp-preview-colors {
    display: flex;
    gap: 4px;
}

.dmp-color-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.2);
}

.dmp-preset-info {
    padding: 10px 15px;
    background: #f8f9fa;
    text-align: center;
}

.dmp-preset-info h3 {
    font-size: 13px;
    margin: 0;
}

.dmp-select-preset-btn {
    width: 100%;
    border-radius: 0;
    border-top: 1px solid #e0e0e0;
}

.dmp-preset-card.active .dmp-select-preset-btn {
    background: #e94560;
    border-color: #e94560;
    color: #fff;
}

.dmp-colors-sidebar {
    position: sticky;
    top: 32px;
}

.dmp-custom-colors-card {
    border: 2px solid #e0e0e0;
}

.dmp-custom-colors-card.active {
    border-color: #e94560;
}

.dmp-custom-preview {
    background: var(--preview-bg, #1a1a2e);
    padding: 20px;
    border-radius: 8px;
    margin: 15px 0;
    min-height: 80px;
}

.dmp-color-pickers {
    display: grid;
    gap: 10px;
}

.dmp-color-picker-field {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dmp-color-picker-field label {
    font-size: 13px;
}

.dmp-custom-actions {
    margin-top: 20px;
}

.dmp-save-custom-btn {
    width: 100%;
}

.dmp-tips-list {
    margin: 0;
    padding-left: 20px;
}

.dmp-tips-list li {
    font-size: 13px;
    color: #666;
    margin-bottom: 8px;
}

@media (max-width: 1200px) {
    .dmp-colors-layout {
        grid-template-columns: 1fr;
    }

    .dmp-colors-sidebar {
        position: static;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    var adminApp = window.DmpAdminApp;

    if (!adminApp) {
        return;
    }

    $('.dmp-select-preset-btn').on('click', function() {
        var preset = $(this).data('preset');
        var $card = $(this).closest('.dmp-preset-card');

        adminApp.saveOptionsFragment({ color_preset: preset }, {
            button: $(this),
            restoreButtonContent: false,
            successMessage: '<?php echo esc_js(__('Color preset saved.', 'dark-mode-pro')); ?>',
            onSuccess: function() {
                $('.dmp-preset-card, .dmp-custom-colors-card').removeClass('active');
                $('.dmp-select-preset-btn').text('<?php echo esc_js(__('Select', 'dark-mode-pro')); ?>');
                $('.dmp-active-badge').remove();

                $card.addClass('active');
                $card.find('.dmp-select-preset-btn').text('<?php echo esc_js(__('Selected', 'dark-mode-pro')); ?>');
                $card.find('.dmp-preset-info').append('<span class="dmp-active-badge"><?php echo esc_js(__('Active', 'dark-mode-pro')); ?></span>');
            }
        });
    });

    // Save custom colors
    $('.dmp-save-custom-btn').on('click', function() {
        var customColors = {};
        $('.dmp-color-picker').each(function() {
            var key = $(this).data('color-key');
            customColors[key] = $(this).val();
        });

        adminApp.saveOptionsFragment({
            color_preset: 'custom',
            custom_colors: customColors
        }, {
            button: $(this),
            successMessage: '<?php echo esc_js(__('Custom colors saved and activated!', 'dark-mode-pro')); ?>',
            onSuccess: function() {
                $('.dmp-preset-card').removeClass('active');
                $('.dmp-select-preset-btn').text('<?php echo esc_js(__('Select', 'dark-mode-pro')); ?>');
                $('.dmp-active-badge').remove();
                $('.dmp-custom-colors-card').addClass('active');
            }
        });
    });

    function updateCustomPreview() {
        if (typeof adminApp.updateCustomPreview === 'function') {
            adminApp.updateCustomPreview();
        }
    }

    // Initial preview
    updateCustomPreview();
});
</script>
