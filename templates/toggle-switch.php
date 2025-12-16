<?php
/**
 * Toggle Switch Template
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

$class_string = implode(' ', $classes);
?>

<div class="<?php echo esc_attr($class_string); ?>" id="dmp-floating-toggle">
    <?php echo DMP_Switch_Styles::render_switch($style); ?>
</div>
