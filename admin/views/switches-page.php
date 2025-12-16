<?php
/**
 * Switch Styles Page View
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('dmp_options', dark_mode_pro()->get_default_options());
$categories = DMP_Switch_Styles::get_categories();
$current_style = $options['switch_style'];
?>

<div class="wrap dmp-admin-wrap">
    <div class="dmp-admin-header">
        <div class="dmp-header-content">
            <h1>
                <span class="dashicons dashicons-marker"></span>
                <?php esc_html_e('Switch Styles', 'dark-mode-pro'); ?>
            </h1>
            <p><?php esc_html_e('Choose from 24+ professionally designed toggle switch styles.', 'dark-mode-pro'); ?></p>
        </div>
        <div class="dmp-header-actions">
            <a href="<?php echo esc_url(admin_url('admin.php?page=dark-mode-pro')); ?>" class="button">
                <span class="dashicons dashicons-arrow-left-alt"></span>
                <?php esc_html_e('Back to Settings', 'dark-mode-pro'); ?>
            </a>
        </div>
    </div>

    <div class="dmp-admin-content">
        <div class="dmp-switches-grid">
            <?php foreach ($categories as $cat_key => $cat_name): ?>
                <div class="dmp-category-section">
                    <h2><?php echo esc_html($cat_name); ?></h2>
                    <div class="dmp-styles-row">
                        <?php foreach (DMP_Switch_Styles::get_styles_by_category($cat_key) as $style_key => $style): ?>
                            <div class="dmp-style-card <?php echo $current_style === $style_key ? 'active' : ''; ?>" data-style="<?php echo esc_attr($style_key); ?>">
                                <div class="dmp-style-preview">
                                    <div class="dmp-preview-light">
                                        <?php echo DMP_Switch_Styles::render_switch($style_key); ?>
                                    </div>
                                    <div class="dmp-preview-dark">
                                        <?php echo DMP_Switch_Styles::render_switch($style_key); ?>
                                    </div>
                                </div>
                                <div class="dmp-style-info">
                                    <h3><?php echo esc_html($style['name']); ?></h3>
                                    <?php if ($current_style === $style_key): ?>
                                        <span class="dmp-active-badge"><?php esc_html_e('Active', 'dark-mode-pro'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="button dmp-select-style-btn" data-style="<?php echo esc_attr($style_key); ?>">
                                    <?php echo $current_style === $style_key ? esc_html__('Selected', 'dark-mode-pro') : esc_html__('Select', 'dark-mode-pro'); ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.dmp-switches-grid {
    max-width: 1400px;
}

.dmp-category-section {
    margin-bottom: 40px;
}

.dmp-category-section h2 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e94560;
}

.dmp-styles-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.dmp-style-card {
    background: #fff;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.dmp-style-card:hover {
    border-color: #1a1a2e;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.dmp-style-card.active {
    border-color: #e94560;
    background: linear-gradient(135deg, #fff 0%, #fff5f7 100%);
}

.dmp-style-preview {
    display: flex;
    justify-content: center;
    gap: 15px;
    padding: 20px 0;
    min-height: 80px;
    align-items: center;
}

.dmp-preview-light {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.dmp-preview-dark {
    background: #1a1a2e;
    padding: 15px;
    border-radius: 8px;
}

.dmp-style-info {
    margin: 15px 0;
}

.dmp-style-info h3 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}

.dmp-active-badge {
    display: inline-block;
    background: #e94560;
    color: #fff;
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 10px;
    margin-top: 5px;
}

.dmp-select-style-btn {
    width: 100%;
}

.dmp-style-card.active .dmp-select-style-btn {
    background: #e94560;
    border-color: #e94560;
    color: #fff;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.dmp-select-style-btn').on('click', function() {
        var style = $(this).data('style');
        var $card = $(this).closest('.dmp-style-card');
        var adminApp = window.DmpAdminApp;

        if (!adminApp) {
            return;
        }

        adminApp.saveOptionsFragment({ switch_style: style }, {
            button: $(this),
            restoreButtonContent: false,
            successMessage: '<?php echo esc_js(__('Switch style saved.', 'dark-mode-pro')); ?>',
            onSuccess: function() {
                // Update UI
                $('.dmp-style-card').removeClass('active');
                $('.dmp-select-style-btn').text('<?php echo esc_js(__('Select', 'dark-mode-pro')); ?>');
                $('.dmp-active-badge').remove();

                $card.addClass('active');
                $card.find('.dmp-select-style-btn').text('<?php echo esc_js(__('Selected', 'dark-mode-pro')); ?>');
                $card.find('.dmp-style-info').append('<span class="dmp-active-badge"><?php echo esc_js(__('Active', 'dark-mode-pro')); ?></span>');
            }
        });
    });
});
</script>
