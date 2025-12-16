<?php
/**
 * Inline Toggle Switch Template (for shortcode)
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

$style = isset($atts['style']) ? $atts['style'] : 'classic';
$size = isset($atts['size']) ? $atts['size'] : 'medium';
$extra_class = isset($atts['class']) ? $atts['class'] : '';

$classes = array(
    'dmp-inline-toggle',
    'dmp-size-' . $size,
);

if (!empty($extra_class)) {
    $classes[] = $extra_class;
}

$class_string = implode(' ', $classes);
?>

<span class="<?php echo esc_attr($class_string); ?>">
    <?php echo DMP_Switch_Styles::render_switch($style); ?>
</span>
