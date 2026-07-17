<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'paper', array( 'paper','white','ink','accent','muted' ), true ) ? $attributes['tone'] : 'paper';
$spacing = in_array( $attributes['spacing'] ?? 'spacious', array( 'compact','normal','spacious','hero' ), true ) ? $attributes['spacing'] : 'spacious';
$width = in_array( $attributes['width'] ?? 'wide', array( 'content','wide','full' ), true ) ? $attributes['width'] : 'wide';
$class = 'ap-section is-tone-' . $tone . ' is-spacing-' . $spacing . ' is-width-' . $width . ( ! empty( $attributes['divider'] ) ? ' has-divider' : '' );
$wrapper = get_block_wrapper_attributes( array( 'class' => $class ) );
?>
<section <?php echo $wrapper; ?>><div class="ap-section__inner"><?php echo $content; ?></div></section>
