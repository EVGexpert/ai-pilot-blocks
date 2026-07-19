<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$tone = in_array( $attributes['tone'] ?? 'plain', array( 'plain','acid','blue' ), true ) ? $attributes['tone'] : 'plain';
$class = 'ap-article-lead is-tone-' . $tone . ( ! empty( $attributes['showRule'] ) ? ' has-rule' : '' );
$wrapper = get_block_wrapper_attributes( array( 'class' => $class ) );
?>
<div <?php echo $wrapper; ?>><p><?php echo esc_html( $attributes['text'] ?? '' ); ?></p></div>
